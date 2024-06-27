<?php

namespace Database\Seeders;

use App\Models\Alternative;
use App\Models\Criterion;
use App\Models\PerformanceScore;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PerformanceScoreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $alternativeNames = [
            'Sony A9 III',
            'Leica SL3',
            'Fujifilm GFX100 II',
            'Lumix G9 II',
            'Canon EOS R3',
        ];
        $alternatives = Alternative::query()->whereIn('name', $alternativeNames)->get();

        $criterionNames = [
            'Price',
            'Sensor size',
            'Frame rates',
            'Screen size inches',
            'Screen brightness nits',
        ];
        $criteria = Criterion::query()->whereIn('name', $criterionNames)->get();

        $assocOfPerformanceScoreValues = [
            $alternativeNames[0] => [101000000, 36, 120, 3, 1500],
            $alternativeNames[1] => [129000000, 36, 240, 5, 1500],
            $alternativeNames[2] => [155000000, 44, 360, 5, 1500],
            $alternativeNames[3] => [29000000, 17, 120, 3, 1500],
            $alternativeNames[4] => [90000000, 36, 90, 3, 1500],
        ];
        $arrayOfPerformanceScores = [];


        foreach ($alternativeNames as $alternativeName) {
            $alternative = $alternatives->firstWhere('name', $alternativeName);
            $values = $assocOfPerformanceScoreValues[$alternativeName];

            for ($i = 0; $i < count($criterionNames); $i++) {
                $criterion = $criteria->firstWhere('name', $criterionNames[$i]);
                array_push($arrayOfPerformanceScores, [
                    'id' => Str::ulid(),
                    'alternative_id' => $alternative->id,
                    'criterion_id' => $criterion->id,
                    'value' => $values[$i],
                    'created_at' => now()->toDateTimeString(),
                ]);
            }
        }

        // Chunk data insertion to optimize performance
        foreach (array_chunk($arrayOfPerformanceScores, 100) as $chunk) {
            PerformanceScore::insert($chunk);
        }
    }
}
