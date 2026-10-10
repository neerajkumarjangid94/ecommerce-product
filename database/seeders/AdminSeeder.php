<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Role banao agar exist nahi karta
        $adminRole = Role::firstOrCreate(['name' => 'admin']);

        // Admin user banao ya update karo
        $admin = User::updateOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name'      => 'Admin',
                'password'  => Hash::make('Admin@12345'),
            ]
        );

        // Admin user ko 'admin' role assign karo
        $admin->assignRole($adminRole);

        $this->command->info('✅ Admin user created/updated:');
    }
}
