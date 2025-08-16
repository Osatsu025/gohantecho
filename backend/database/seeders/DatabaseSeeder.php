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

        User::factory()->create([
                'name' => '馬井ごはん',
                'email' => 'gohan@example.com',
        ]);
        User::factory()->create([
                'name' => '白井たまご',
                'email' => 'tamago@example.com'
        ]);

        $this->call([
            TimeSeeder::class,
            TagSeeder::class,
            MenuSeeder::class,
            MenuTagSeeder::class,
        ]);
    }
}
