<?php

namespace Database\Seeders;

use App\Models\User;
use Database\Factories\GoalFactory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;


class GoalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the first user
        $user = User::first();

        // Hardcoded goal ID for testing purposes
        $goalId = '01j17kxyy7e89jn6q3t9zgk523';

        // Define the attributes for the goal
        $goalData = [
            'id' => $goalId,
            'user_id' => $user->id,
            'title' => 'Pemilihan kamera untuk fotografi',
            'slug' => Str::slug(Str::lower('Pemilihan kamera untukografi')),
        ];

        // Create a new goal using the factory and the defined attributes
        GoalFactory::new()->create($goalData);
    }
}
