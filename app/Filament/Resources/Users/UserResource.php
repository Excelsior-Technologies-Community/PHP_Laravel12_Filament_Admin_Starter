<?php

namespace App\Filament\Resources\Users;

use App\Filament\Resources\Users\Pages\CreateUser;
use App\Filament\Resources\Users\Pages\EditUser;
use App\Filament\Resources\Users\Pages\ListUsers;
use App\Filament\Resources\Users\Schemas\UserForm;
use App\Filament\Resources\Users\Tables\UsersTable;
use App\Models\User;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-users';

    protected static string|\UnitEnum|null $navigationGroup = 'User Management';

    protected static ?string $navigationLabel = 'Users';

    protected static ?int $navigationSort = 1;

    /**
     * Configure the User form.
     */
    public static function form(Schema $schema): Schema
    {
        return UserForm::configure($schema);
    }

    /**
     * Configure the User table.
     */
    public static function table(Table $table): Table
    {
        return UsersTable::configure($table);
    }

    /**
     * Configure resource pages.
     */
    public static function getPages(): array
    {
        return [
            'index' => ListUsers::route('/'),
            'create' => CreateUser::route('/create'),
            'edit' => EditUser::route('/{record}/edit'),
        ];
    }

    /**
     * Only Super Admins can create users.
     */
    public static function canCreate(): bool
    {
        return auth()->user()?->hasRole('super_admin') ?? false;
    }

    /**
     * Admins and Super Admins can edit users.
     */
    public static function canEdit($record): bool
    {
        return auth()->user()?->hasAnyRole([
            'super_admin',
            'admin',
        ]) ?? false;
    }

    /**
     * Only Super Admins can delete users.
     */
    public static function canDelete($record): bool
    {
        return auth()->user()?->hasRole('super_admin') ?? false;
    }

    /**
     * Admins and Super Admins can access User Management.
     */
    public static function canViewAny(): bool
    {
        return auth()->user()?->hasAnyRole([
            'super_admin',
            'admin',
        ]) ?? false;
    }
}