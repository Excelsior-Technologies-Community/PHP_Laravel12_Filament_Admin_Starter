<?php

namespace App\Filament\Pages;

use App\Models\SystemSetting;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class ManageSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationLabel = 'System Settings';

    protected static ?string $title = 'System Settings & Branding';

    protected static string|\UnitEnum|null $navigationGroup = 'Settings & System';

    protected static ?int $navigationSort = 10;

    protected string $view = 'filament.pages.manage-settings';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            // General
            'app_name' => SystemSetting::get('app_name', 'Laravel 12 Starter'),
            'app_tagline' => SystemSetting::get('app_tagline', 'Modern Admin Dashboard Kit'),
            'support_email' => SystemSetting::get('support_email', 'admin@example.com'),
            'currency' => SystemSetting::get('currency', 'USD ($)'),
            'footer_text' => SystemSetting::get('footer_text', '© 2026 Laravel 12 Filament Starter. All rights reserved.'),

            // Branding & Theme
            'app_logo' => SystemSetting::get('app_logo', ''),
            'app_favicon' => SystemSetting::get('app_favicon', ''),
            'theme_color' => SystemSetting::get('theme_color', 'amber'),
            'dark_mode_default' => (bool) SystemSetting::get('dark_mode_default', true),

            // Security & Maintenance
            'maintenance_mode' => (bool) SystemSetting::get('maintenance_mode', false),
            'allow_registration' => (bool) SystemSetting::get('allow_registration', true),
            'session_lifetime' => SystemSetting::get('session_lifetime', '120'),
            'max_login_attempts' => SystemSetting::get('max_login_attempts', '5'),
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                Tabs::make('Settings Tabs')
                    ->tabs([
                        Tab::make('General')
                            ->icon('heroicon-o-globe-alt')
                            ->schema([
                                Grid::make(2)->schema([
                                    TextInput::make('app_name')
                                        ->label('Application / Site Name')
                                        ->required(),

                                    TextInput::make('app_tagline')
                                        ->label('Tagline / Slogan'),

                                    TextInput::make('support_email')
                                        ->label('Support / Admin Email')
                                        ->email()
                                        ->required(),

                                    Select::make('currency')
                                        ->label('Primary Currency')
                                        ->options([
                                            'USD ($)' => 'USD ($)',
                                            'EUR (€)' => 'EUR (€)',
                                            'GBP (£)' => 'GBP (£)',
                                            'INR (₹)' => 'INR (₹)',
                                            'CAD ($)' => 'CAD ($)',
                                        ]),

                                    Textarea::make('footer_text')
                                        ->label('Custom Footer Text')
                                        ->rows(2)
                                        ->columnSpanFull(),
                                ]),
                            ]),

                        Tab::make('Branding & Theme')
                            ->icon('heroicon-o-paint-brush')
                            ->schema([
                                Grid::make(2)->schema([
                                    TextInput::make('app_logo')
                                        ->label('Logo Image URL')
                                        ->placeholder('https://example.com/logo.png'),

                                    TextInput::make('app_favicon')
                                        ->label('Favicon URL')
                                        ->placeholder('https://example.com/favicon.ico'),

                                    Select::make('theme_color')
                                        ->label('Primary Accent Color')
                                        ->options([
                                            'amber' => '🟡 Amber (Default)',
                                            'emerald' => '🟢 Emerald Green',
                                            'indigo' => '🔵 Indigo Blue',
                                            'sky' => '🔷 Sky Blue',
                                            'rose' => '🔴 Rose Red',
                                            'purple' => '🟣 Purple Violet',
                                        ]),

                                    Toggle::make('dark_mode_default')
                                        ->label('Enable Dark Mode by Default')
                                        ->inline(false),
                                ]),
                            ]),

                        Tab::make('Security & Maintenance')
                            ->icon('heroicon-o-shield-check')
                            ->schema([
                                Grid::make(2)->schema([
                                    Toggle::make('maintenance_mode')
                                        ->label('Maintenance Mode')
                                        ->helperText('When enabled, the public front-end will display a maintenance notice.')
                                        ->inline(false),

                                    Toggle::make('allow_registration')
                                        ->label('Allow User Registration')
                                        ->helperText('Allow new users to sign up from the front-end.')
                                        ->inline(false),

                                    TextInput::make('session_lifetime')
                                        ->label('Session Lifetime (Minutes)')
                                        ->numeric(),

                                    TextInput::make('max_login_attempts')
                                        ->label('Max Failed Login Attempts (Rate Limit)')
                                        ->numeric(),
                                ]),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public function save(): void
    {
        $state = $this->form->getState();

        foreach ($state as $key => $value) {
            $group = match (true) {
                in_array($key, ['app_logo', 'app_favicon', 'theme_color', 'dark_mode_default']) => 'branding',
                in_array($key, ['maintenance_mode', 'allow_registration', 'session_lifetime', 'max_login_attempts']) => 'security',
                default => 'general',
            };

            SystemSetting::set($key, $value, $group);
        }

        Notification::make()
            ->title('Settings Saved Successfully')
            ->body('System configuration and branding preferences have been updated.')
            ->success()
            ->send();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('save')
                ->label('Save All Settings')
                ->icon('heroicon-o-check')
                ->color('primary')
                ->action('save'),
        ];
    }
}
