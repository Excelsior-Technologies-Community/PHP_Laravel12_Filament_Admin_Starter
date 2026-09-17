<?php

namespace App\Filament\Resources\LoginActivities;

use App\Filament\Resources\LoginActivities\Pages;
use App\Models\LoginActivity;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Response;

class LoginActivityResource extends Resource
{
    protected static ?string $model = LoginActivity::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-finger-print';

    protected static ?string $navigationLabel = 'Login Audit Logs';

    protected static ?string $modelLabel = 'Login Activity';

    protected static ?string $pluralModelLabel = 'Login Audit Logs';

    protected static string|\UnitEnum|null $navigationGroup = 'Security & Audit';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->label('User')
                    ->relationship('user', 'name')
                    ->disabled(),

                TextInput::make('ip_address')
                    ->label('IP Address')
                    ->disabled(),

                TextInput::make('city')
                    ->label('City')
                    ->disabled(),

                TextInput::make('country')
                    ->label('Country')
                    ->disabled(),

                TextInput::make('status')
                    ->label('Status')
                    ->disabled(),

                DateTimePicker::make('login_at')
                    ->label('Login Timestamp')
                    ->disabled(),

                Textarea::make('user_agent')
                    ->label('User Agent (Browser & Device)')
                    ->rows(3)
                    ->columnSpanFull()
                    ->disabled(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('login_at', 'desc')
            ->columns([
                TextColumn::make('id')
                    ->label('#')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('user.name')
                    ->label('User')
                    ->description(fn (LoginActivity $record) => $record->user?->email ?? 'N/A')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('ip_address')
                    ->label('IP Address')
                    ->copyable()
                    ->copyMessage('IP copied to clipboard')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('browser')
                    ->label('Browser / OS')
                    ->description(fn (LoginActivity $record) => $record->platform)
                    ->badge()
                    ->color('info')
                    ->sortable(false),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->colors([
                        'success' => 'success',
                        'danger' => 'failed',
                        'warning' => 'blocked',
                    ])
                    ->formatStateUsing(fn ($state) => ucfirst($state ?? 'Success'))
                    ->sortable(),

                TextColumn::make('city')
                    ->label('Location')
                    ->formatStateUsing(fn (LoginActivity $record) => $record->city ? "{$record->city}, {$record->country}" : 'Local / Unknown')
                    ->toggleable(),

                TextColumn::make('login_at')
                    ->label('Login At')
                    ->dateTime('d M Y, h:i:s A')
                    ->since()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Login Status')
                    ->options([
                        'success' => 'Success',
                        'failed' => 'Failed',
                        'blocked' => 'Blocked',
                    ]),

                SelectFilter::make('user_id')
                    ->label('Filter by User')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload(),

                Filter::make('today')
                    ->label('Today Only')
                    ->query(fn (Builder $query) => $query->whereDate('login_at', today())),
            ])
            ->recordActions([
                Action::make('view_details')
                    ->label('Details')
                    ->icon('heroicon-o-information-circle')
                    ->modalHeading('Login Session Details')
                    ->modalDescription(fn (LoginActivity $record) => "Audit details for {$record->user?->name} at {$record->login_at?->format('d M Y, h:i:s A')}")
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Close')
                    ->infolist([
                        // Modal view info
                    ])
                    ->action(fn () => null),

                DeleteAction::make()
                    ->requiresConfirmation(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->requiresConfirmation(),

                    BulkAction::make('export_audit_csv')
                        ->label('Export Audit Logs (CSV)')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->requiresConfirmation()
                        ->action(function ($records) {
                            $fileName = 'login_audit_' . now()->format('Y_m_d_H_i_s') . '.csv';

                            $headers = [
                                'Content-Type' => 'text/csv',
                                'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
                            ];

                            $callback = function () use ($records) {
                                $file = fopen('php://output', 'w');
                                fputcsv($file, ['ID', 'User Name', 'User Email', 'IP Address', 'Browser', 'Platform', 'Status', 'Location', 'Timestamp', 'User Agent']);

                                foreach ($records as $log) {
                                    fputcsv($file, [
                                        $log->id,
                                        $log->user?->name ?? 'Deleted User',
                                        $log->user?->email ?? '',
                                        $log->ip_address,
                                        $log->browser,
                                        $log->platform,
                                        $log->status ?? 'success',
                                        ($log->city ? "{$log->city}, {$log->country}" : 'Local'),
                                        $log->login_at?->format('Y-m-d H:i:s') ?? '',
                                        $log->user_agent,
                                    ]);
                                }

                                fclose($file);
                            };

                            return Response::stream($callback, 200, $headers);
                        }),
                ]),
            ])
            ->emptyStateHeading('No login activity logs found')
            ->emptyStateDescription('User authentication attempts will appear here.');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLoginActivities::route('/'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with('user');
    }
}
