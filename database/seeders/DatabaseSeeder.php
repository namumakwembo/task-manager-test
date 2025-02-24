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
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

            // Create 10 users and assign 5 tasks to each
    \App\Models\User::factory(10)->create()->each(function ($user) {
        \App\Models\Task::factory()->count(5)->create(['user_id' => $user->id]);
    });
    }
}
