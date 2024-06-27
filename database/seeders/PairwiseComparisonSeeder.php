<?php

namespace Database\Seeders;

use App\Models\Criterion;
use App\Models\Goal;
use App\Models\PairwiseComparison;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PairwiseComparisonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $goal = Goal::first();
        $criteria = Criterion::where('goal_id', $goal->id)->orderBy('index', 'ASC')->get();

        $pwcsUpperDiagonalValues = [1, 1, 1, 1, 3, 3, 3, 2, 2, 3];
        $pwcsUpperDiagonalValuesIndex = 0;
        $pairwiseComparisons = collect();

        foreach ($criteria as $criterion) {
            // Main diagonal
            $pairwiseComparisons->add([
                'id' => Str::ulid(),
                'primary_criterion_id' => $criterion->id,
                'secondary_criterion_id' => $criterion->id,
                'value' => 1,
                'created_at' => now()->toDateTimeString(),
            ]);

            $primaryCriterion = $criterion;
            $secondaryCriteria = $criteria->filter(fn ($c) => $c->index > $primaryCriterion->index);
            foreach ($secondaryCriteria as $secondaryCriterion) {
                $value = $pwcsUpperDiagonalValues[$pwcsUpperDiagonalValuesIndex];
                $pwcsUpperDiagonalValuesIndex++;

                // Upper diagonal
                $pairwiseComparisons->add([
                    'id' => Str::ulid(),
                    'primary_criterion_id' => $primaryCriterion->id,
                    'secondary_criterion_id' => $secondaryCriterion->id,
                    'value' => $value,
                    'created_at' => now()->toDateTimeString(),
                ]);
                // Lower diagonal
                $pairwiseComparisons->add([
                    'id' => Str::ulid(),
                    'primary_criterion_id' => $secondaryCriterion->id,
                    'secondary_criterion_id' => $primaryCriterion->id,
                    'value' => 1 / $value,
                    'created_at' => now()->toDateTimeString(),
                ]);
            }
        }

        // Chunk data insertion to optimize performance
        foreach ($pairwiseComparisons->chunk(100)->toArray() as $chunk) {
            PairwiseComparison::insert($chunk);
        }
    }
}
