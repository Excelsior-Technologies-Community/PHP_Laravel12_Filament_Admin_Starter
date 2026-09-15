<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Full Name')
                    ->required()
                    ->maxLength(255)
                    ->autofocus(),

                TextInput::make('email')
                    ->label('Email Address')
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
                    ->rule(Password::defaults())
                    ->dehydrateStateUsing(
                        fn (?string $state): ?string =>
                            filled($state) ? Hash::make($state) : null
                    )
                    ->dehydrated(
                        fn (?string $state): bool => filled($state)
                    )
                    ->required(fn (string $operation): bool => $operation === 'create')
                    ->helperText(
                        fn (string $operation): string =>
                            $operation === 'create'
                                ? 'Enter a secure password for the new user.'
                                : 'Leave blank to keep the current password.'
                    ),

                Select::make('roles')
                    ->label('Roles')
                    ->relationship(
                        name: 'roles',
                        titleAttribute: 'name'
                    )
                    ->multiple()
                    ->searchable()
                    ->preload()
                    ->required()
                    ->helperText(
                        'Assign one or more roles to this user.'
                    ),

                Toggle::make('is_active')
                    ->label('Active Account')
                    ->default(true)
                    ->helperText(
                        'Inactive users cannot access the Filament admin panel.'
                    ),
            ]);
    }
}