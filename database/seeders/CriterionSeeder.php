<?php

namespace Database\Seeders;

use App\Enums\Criterion\ImpactType;
use App\Models\Criterion;
use App\Models\Goal;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CriterionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $goal = Goal::first();

        $createdAtStr = now()->toDateTimeString();
        Criterion::insert([
            [
                'id' => Str::ulid(),
                'goal_id' => $goal->id,
                'name' => 'Price',
                'impact_type' => ImpactType::Cost,
                'index' => 0,
                'weight' => 2,
                'weight_percentage' => 0.2,
                'created_at' => $createdAtStr
            ],
            [
                'id' => Str::ulid(),
                'goal_id' => $goal->id,
                'name' => 'Sensor size',
                'impact_type' => ImpactType::Benefit,
                'index' => 1,
                'weight' => 5,
                'weight_percentage' => 0.3,
                'created_at' => $createdAtStr
            ],
            [
                'id' => Str::ulid(),
                'goal_id' => $goal->id,
                'name' => 'Frame rates',
                'impact_type' => ImpactType::Benefit,
                'index' => 2,
                'weight' => 1,
                'weight_percentage' => 0.1,
                'created_at' => $createdAtStr
            ],
            [
                'id' => Str::ulid(),
                'goal_id' => $goal->id,
                'name' => 'Screen size inches',
                'impact_type' => ImpactType::Benefit,
                'index' => 3,
                'weight' => 4,
                'weight_percentage' => 0.2,
                'created_at' => $createdAtStr
            ],
            [
                'id' => Str::ulid(),
                'goal_id' => $goal->id,
                'name' => 'Screen brightness nits',
                'impact_type' => ImpactType::Benefit,
                'index' => 4,
                'weight' => 3,
                'weight_percentage' => 0.2,
                'created_at' => $createdAtStr
            ],
        ]);
    }
}
