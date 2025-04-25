<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\Permission;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $fillable = [
        'name',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class,'role_user');
    }
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'permission_role');
    }
    public function hasPermission(String $permissionName)
    {
        return $this->permissions()->contains('name', $permissionName);
    }
}
