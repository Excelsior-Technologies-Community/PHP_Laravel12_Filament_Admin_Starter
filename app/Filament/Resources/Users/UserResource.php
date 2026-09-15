<?php

namespace App\Filament\Resources\Users;

use App\Filament\Resources\Users\Pages;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Response;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationLabel = 'Users';

    protected static ?string $modelLabel = 'User';

    protected static ?string $pluralModelLabel = 'Users';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Name')
                    ->required()
                    ->maxLength(255),

                TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->required()
                    ->unique(
                        table: 'users',
                        column: 'email',
                        ignoreRecord: true
                    )
                    ->maxLength(255),

                TextInput::make('password')
                    ->label('Password')
                    ->password()
                    ->revealable()
                    ->required(fn ($record) => $record === null)
                    ->dehydrated(fn ($state) => filled($state))
                    ->minLength(8),

                Select::make('roles')
                    ->label('Role')
                    ->relationship(
                        name: 'roles',
                        titleAttribute: 'name'
                    )
                    ->multiple()
                    ->preload()
                    ->searchable(),

                Toggle::make('is_active')
                    ->label('Active Account')
                    ->default(true)
                    ->inline(false),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'asc')

            ->columns([
                TextColumn::make('id')
                    ->label('#')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('roles.name')
                    ->label('Role')
                    ->badge()
                    ->separator(',')
                    ->sortable(),

                IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean()
                    ->sortable(),

                TextColumn::make('last_login_at')
                    ->label('Last Login')
                    ->dateTime('d M Y, h:i A')
                    ->placeholder('Never')
                    ->sortable(),

                TextColumn::make('last_login_ip')
                    ->label('Last Login IP')
                    ->placeholder('Not recorded')
                    ->toggleable(),

                TextColumn::make('created_at')
                    ->label('Registered')
                    ->dateTime('d M Y, h:i A')
                    ->sortable()
                    ->toggleable(),
            ])

            ->filters([
                TernaryFilter::make('is_active')
                    ->label('Account Status')
                    ->placeholder('All Users')
                    ->trueLabel('Active Users')
                    ->falseLabel('Inactive Users'),

                SelectFilter::make('roles')
                    ->label('Role')
                    ->relationship(
                        name: 'roles',
                        titleAttribute: 'name'
                    )
                    ->multiple()
                    ->preload()
                    ->searchable(),
            ])

            ->recordActions([
                EditAction::make(),

                Action::make('toggle_status')
                    ->label(
                        fn (User $record): string =>
                            $record->is_active
                                ? 'Deactivate'
                                : 'Activate'
                    )
                    ->icon(
                        fn (User $record): string =>
                            $record->is_active
                                ? 'heroicon-o-x-circle'
                                : 'heroicon-o-check-circle'
                    )
                    ->color(
                        fn (User $record): string =>
                            $record->is_active
                                ? 'danger'
                                : 'success'
                    )
                    ->requiresConfirmation()
                    ->modalHeading(
                        fn (User $record): string =>
                            $record->is_active
                                ? 'Deactivate User?'
                                : 'Activate User?'
                    )
                    ->modalDescription(
                        fn (User $record): string =>
                            $record->is_active
                                ? 'This user will no longer be able to access protected areas.'
                                : 'This user will be allowed to access protected areas again.'
                    )
                    ->modalSubmitActionLabel(
                        fn (User $record): string =>
                            $record->is_active
                                ? 'Deactivate'
                                : 'Activate'
                    )
                    ->action(function (User $record): void {
                        $record->update([
                            'is_active' => ! $record->is_active,
                        ]);
                    }),

                DeleteAction::make()
                    ->requiresConfirmation(),
            ])

            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->requiresConfirmation(),

                    BulkAction::make('activate')
                        ->label('Activate Selected')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(function ($records): void {
                            foreach ($records as $record) {
                                $record->update([
                                    'is_active' => true,
                                ]);
                            }
                        }),

                    BulkAction::make('deactivate')
                        ->label('Deactivate Selected')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->action(function ($records): void {
                            foreach ($records as $record) {
                                $record->update([
                                    'is_active' => false,
                                ]);
                            }
                        }),

                    BulkAction::make('export_selected')
                        ->label('Export Selected CSV')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->requiresConfirmation()
                        ->action(function ($records) {
                            $fileName =
                                'selected_users_' .
                                now()->format('Y_m_d_H_i_s') .
                                '.csv';

                            $headers = [
                                'Content-Type' => 'text/csv',
                                'Content-Disposition' =>
                                    'attachment; filename="' .
                                    $fileName .
                                    '"',
                            ];

                            $callback = function () use ($records) {
                                $file = fopen(
                                    'php://output',
                                    'w'
                                );

                                fputcsv($file, [
                                    'ID',
                                    'Name',
                                    'Email',
                                    'Role',
                                    'Status',
                                    'Last Login',
                                    'Last Login IP',
                                    'Registered',
                                ]);

                                foreach ($records as $user) {
                                    fputcsv($file, [
                                        $user->id,
                                        $user->name,
                                        $user->email,
                                        $user->roles
                                            ->pluck('name')
                                            ->implode(', '),
                                        $user->is_active
                                            ? 'Active'
                                            : 'Inactive',
                                        $user->last_login_at
                                            ? $user->last_login_at->format(
                                                'Y-m-d H:i:s'
                                            )
                                            : 'Never',
                                        $user->last_login_ip ?? '',
                                        $user->created_at
                                            ? $user->created_at->format(
                                                'Y-m-d H:i:s'
                                            )
                                            : '',
                                    ]);
                                }

                                fclose($file);
                            };

                            return Response::stream(
                                $callback,
                                200,
                                $headers
                            );
                        }),
                ]),
            ])

            ->searchPlaceholder(
                'Search users by name or email...'
            )

            ->emptyStateHeading('No users found')

            ->emptyStateDescription(
                'Try changing your search or filters.'
            )

            ->paginationPageOptions([
                10,
                25,
                50,
                100,
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with('roles');
    }
}