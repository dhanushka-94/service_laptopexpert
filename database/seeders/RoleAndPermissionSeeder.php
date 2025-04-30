<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            'view customers',
            'create customers',
            'edit customers',
            'delete customers',
            'view jobs',
            'create jobs',
            'edit jobs',
            'delete jobs',
            'assign jobs',
            'change job status',
            'add job notes',
            'print job notes',
            'view reports',
            'manage users',
        ];

        foreach ($permissions as $permission) {
            // Check if permission exists before creating
            if (!Permission::where('name', $permission)->exists()) {
                Permission::create(['name' => $permission]);
            }
        }

        // Create roles and assign permissions
        // Check if admin role exists before creating
        if (!Role::where('name', 'admin')->exists()) {
            $adminRole = Role::create(['name' => 'admin']);
            $adminRole->givePermissionTo(Permission::all());
        } else {
            $adminRole = Role::where('name', 'admin')->first();
        }

        // Check if technician role exists before creating
        if (!Role::where('name', 'technician')->exists()) {
            $technicianRole = Role::create(['name' => 'technician']);
            $technicianRole->givePermissionTo([
                'view customers',
                'view jobs',
                'edit jobs',
                'change job status',
                'add job notes',
                'print job notes',
            ]);
        }

        // Create admin user if it doesn't exist
        if (!User::where('email', 'admin@example.com')->exists()) {
            $admin = User::create([
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                'password' => Hash::make('password'),
            ]);
            $admin->assignRole('admin');
        }

        // Create technician user if it doesn't exist
        if (!User::where('email', 'tech@example.com')->exists()) {
            $technician = User::create([
                'name' => 'Technician User',
                'email' => 'tech@example.com',
                'password' => Hash::make('password'),
            ]);
            $technician->assignRole('technician');
        }
    }
}
