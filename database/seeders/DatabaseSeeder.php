<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Database\Seeders\ProductSeeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(ProductSeeder::class);

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

// إنشاء الأدوار مع تحديد الـ guard_name
    $adminRole = Role::create(['name' => 'admin', 'guard_name' => 'web']);
    $editorRole = Role::create(['name' => 'editor', 'guard_name' => 'web']);

// إنشاء الصلاحيات مع تحديد الـ guard_name
    $editPermission = Permission::create(['name' => 'edit posts', 'guard_name' => 'web']);
    $publishPermission = Permission::create(['name' => 'publish posts', 'guard_name' => 'web']);

// ربط الصلاحيات بالأدوار (بالطريقة الرسمية في Spatie)
    $adminRole->givePermissionTo([$editPermission, $publishPermission]);
    $editorRole->givePermissionTo($editPermission);

// ربط الأدوار بالمستخدمين
    $AdminUser->assignRole($adminRole);
    $EditorUser->assignRole($editorRole);
    }
}
