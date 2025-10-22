<?php

namespace App\Filament\Resources\Users;

use App\Filament\Resources\Users\Pages\CreateUser;
use App\Filament\Resources\Users\Pages\EditUser;
use App\Filament\Resources\Users\Pages\ListUsers;
use App\Filament\Resources\Users\Schemas\UserForm;
use App\Filament\Resources\Users\Tables\UsersTable;
use App\Models\User;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-users';

    protected static string|UnitEnum|null $navigationGroup = 'Managemen Akun';

    /**
     * The navigation label for the resource.
     */
    protected static ?string $navigationLabel = 'Pengguna';

    /**
     * The permissions required to access this resource.
     */
    protected static ?string $permissionPrefixes = 'user';

    /**
     * Get the title for the resource page.
     */
    protected static ?string $title = 'Pengguna';

    /**
     * Get the plural model label for the resource.
     */
    protected static ?string $pluralModelLabel = 'Pengguna';

    /**
     * Get the model label for the resource.
     */
    protected static ?string $modelLabel = 'Pengguna';

    public static function form(Schema $schema): Schema
    {
        return UserForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return UsersTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListUsers::route('/'),
            'create' => CreateUser::route('/create'),
            'edit' => EditUser::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ])
            ->with(['userDetail', 'roles']); // Eager load relationships
    }

    /**
     * Handle the creation of a new user with roles
     */
    public static function create(array $data): User
    {
        $roles = $data['roles'] ?? [];
        unset($data['roles']);

        $user = new User($data);
        $user->save();

        if (! empty($roles)) {
            $user->syncRoles($roles);
        }

        return $user;
    }

    /**
     * Handle the update of an existing user with roles
     */
    public static function update(User $record, array $data): User
    {
        $roles = $data['roles'] ?? [];
        unset($data['roles']);

        $record->fill($data);
        $record->save();

        if (! empty($roles)) {
            $record->syncRoles($roles);
        } else {
            $record->syncRoles([]);
        }

        return $record;
    }

    /**
     * Get the redirect URL after creating a new user
     */
    public static function getRedirectUrlAfterCreate(): string
    {
        return static::getUrl('index');
    }

    /**
     * Get the redirect URL after editing a user
     */
    public static function getRedirectUrlAfterEdit(): string
    {
        return static::getUrl('index');
    }
}
