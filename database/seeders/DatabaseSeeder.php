<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\RolesAndPermissionsSeeder;
use Database\Seeders\SettingsSeeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::query()->updateOrCreate(
            ['email' => 'test@example.com'],
            [
                'first_name' => 'Test',
                'last_name' => 'User',
                'name' => 'Test User',
                'password' => Hash::make('password'),
            ]
        );

        // Seed roles and assign admin to user with ID 1
        $this->call(RolesAndPermissionsSeeder::class);

        // Create a dedicated Super Admin user
        $superAdmin = User::query()->updateOrCreate(
            ['email' => 'admin@insureverify.ai'],
            [
                'first_name' => 'Super',
                'last_name' => 'Admin',
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
            ]
        );
        // Ensure role exists and assign
        $superAdminRole = Role::firstOrCreate(['name' => 'super-admin', 'guard_name' => 'web']);
        if (! $superAdmin->hasRole($superAdminRole)) {
            $superAdmin->assignRole($superAdminRole);
        }

        // Seed subscription plans
        $this->call(PlanSeeder::class);

        // Seed blog categories and sample blog posts
        $this->call(BlogCategorySeeder::class);
        $this->call(BlogSeeder::class);

        // Seed a comprehensive demo subscriber with subscription, usage, invoices, items, payments, and events
        $this->call(SubscriberDemoSeeder::class);

        // Seed application settings
        $this->call(SettingsSeeder::class);
    }
}
