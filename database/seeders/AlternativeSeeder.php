<?php

namespace Database\Seeders;

use App\Models\Alternative;
use App\Models\Goal;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AlternativeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $goal = Goal::query()->first();
        $createdAtStr = now()->toDateTimeString();
        $alternativeNames = ['Sony A9 III', 'Leica SL3', 'Fujifilm GFX100 II', 'Lumix G9 II', 'Canon EOS R3'];
        $arrOfAlternatives = [];

        foreach ($alternativeNames as $index => $alternativeName) {
            array_push(
                $arrOfAlternatives,
                [
                    'id' => Str::ulid(),
                    'goal_id' => $goal->id,
                    'name' => $alternativeName,
                    'index' => $index,
                    'created_at' => $createdAtStr
                ],
            );
        }

        Alternative::insert($arrOfAlternatives);
    }
}
