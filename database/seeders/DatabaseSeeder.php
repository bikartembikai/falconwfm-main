<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => bcrypt('password'), // Add password if missing in factory default
                'role' => 'user'
            ]
        );

        User::updateOrCreate(
            ['email' => 'm.amir.afham@gmail.com'],
            [
                'name' => 'Amir Afham',
                'password' => bcrypt('12345678'),
                'role' => 'operation_manager',
            ]
        );

        User::updateOrCreate(
            ['email' => 'm.amir.afham55@gmail.com'],
            [
                'name' => 'Amir Afham (Marketing)',
                'password' => bcrypt('12345678'),
                'role' => 'marketing_manager',
            ]
        );

        $this->call([
            RuleSeeder::class,
            RealFacilitatorSeeder::class,
        ]);
    }
}
