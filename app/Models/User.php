<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{

    use HasFactory;

    protected $primaryKey = 'user_id';

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'user_id', 'user_id');
    }

    public function inventories(): HasMany
    {
        return $this->hasMany(Inventory::class, 'user_id', 'user_id');
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'role_id', 'role_id');
    }

    protected $fillable = [
        'user_name',
        'email',
        'phone_num',
        'password_hash',
        'role_id',
    ];

    protected $hidden = [
        'password_hash',
        'remember_token',
    ];

    /**
     * Comprueba un permiso mediante la relación rol-permisos.
     * Si el usuario no tiene rol asignado, el acceso se deniega.
     */
    public function hasPermission(string $permissionCode): bool
    {
        return $this->role()
            ->whereHas('permissions', fn ($query) => $query->where('code', $permissionCode))
            ->exists();
    }

    /** The database stores password hashes in password_hash rather than password. */
    public function getAuthPasswordName(): string
    {
        return 'password_hash';
    }

    public function getAuthPassword(): string
    {
        return (string) $this->password_hash;
    }

}
