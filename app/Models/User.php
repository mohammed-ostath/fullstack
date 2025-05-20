<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Role;
use App\Models\Order;
use App\Models\Profile;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable,HasApiTokens,HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'type',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function profile()
    {
        return $this->hasOne(Profile::class);
    }

    public function hasRole(String $roleName)
    {
        return $this->roles()->contains('name', $roleName);
    }
    public function hasPermission(string $permissionName)
    {
        return $this->roles->contains(fn($role) => $role->hasPermission($permissionName));
    }
    //is Admin
    public function isAdmin()
    {
        return $this->type === 'admin';
    }
    //is Customer
    public function isCustomer()
    {
        return $this->type === 'customer';
    }
    //is delivery
    public function isDelivery()
    {
        return $this->type === 'delivery';
    }
    // orders
    public function orders(){
        return $this->hasMany(Order::class);
    }
}