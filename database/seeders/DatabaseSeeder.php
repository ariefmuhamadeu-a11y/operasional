<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seeder default bawaan Laravel
        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        // Tambahkan seeder master data di bawah ini
        $this->call([
            ItemClassSeeder::class,
            ProductCategorySeeder::class,
            ItemSeeder::class,
            EmployeeSeeder::class,
            SupplierSeeder::class,
        ]);
    }
}
