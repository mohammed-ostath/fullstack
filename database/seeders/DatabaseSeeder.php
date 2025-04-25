<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // إنشاء المستخدمين
        $AdminUser = User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
        ]);

        $EditorUser = User::create([
            'name' => 'Editor',
            'email' => 'editor@example.com',
            'password' => Hash::make('password'),
        ]);

        $adminRole = Role::create(['name' => 'admin']);
        $editorRole = Role::create(['name' => 'editor']);

        $editPermission = Permission::create(['name' => 'edit posts']);
        $publishPermission = Permission::create(['name' => 'publish posts']);

        $adminRole->permissions()->attach([$editPermission->id, $publishPermission->id]);
        $editorRole->permissions()->attach($editPermission->id);

        $AdminUser->roles()->attach($adminRole->id);
        $EditorUser->roles()->attach($editorRole->id);
    }
}
