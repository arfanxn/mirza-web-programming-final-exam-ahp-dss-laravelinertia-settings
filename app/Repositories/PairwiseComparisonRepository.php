<?php

namespace App\Repositories;

use App\Models\PairwiseComparison;
use Illuminate\Database\QueryException;
use Illuminate\Support\Arr;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Support\Str;

class PairwiseComparisonRepository
{
    public function __construct()
    {
        //
    }

    /**
     * insert
     *
     * @param array $items
     * @return bool
     * @throws QueryException
     */
    public function insert($items)
    {
        $createdAt = now();
        $items = array_map(
            fn ($item) =>
            array_merge(
                Arr::only($item, ['primary_criterion_id', 'secondary_criterion_id', 'value']),
                ['id' => $item['id'] ?? Str::ulid()],
                ['created_at' => $createdAt],
                ['updated_at' => $item['updated_at'] ?? null]
            ),
            $items
        );

        try {
            foreach (array_chunk($items, 100) as $chunk) {
                PairwiseComparison::insert($chunk);
            }
            return true;
        } catch (QueryException $e) {
            throw $e;
        }
    }

    /**
     * insertDefaultFrom
     *
     * @param array $criterionIds
     * @return bool
     * @throws QueryException
     */
    public function insertDefaultFrom($criterionIds)
    {
        $items = [];
        foreach ($criterionIds as $primaryCriterionId) {
            foreach ($criterionIds as $secondaryCriterionId) {
                array_push($items, [
                    'id' => Str::ulid(),
                    'primary_criterion_id' => $primaryCriterionId,
                    'secondary_criterion_id' => $secondaryCriterionId,
                    'value' => 1
                ]);
            }
        }
        return $this->insert($items);
    }

    /**
     * whereGoalId
     *
     * @param int $goalId
     * @return \lluminate\Database\Eloquent\Builder
     */
    function whereGoalId($goalId)
    {
        $query = PairwiseComparison::query()->where(
            fn (EloquentBuilder $q) => $q->whereHas(
                'primaryCriterion',
                fn (EloquentBuilder $q) => $q->whereHas(
                    'goal',
                    fn (EloquentBuilder $q) => $q->where('id', $goalId)
                )
            )
        );
        return $query;
    }


    /**
     * replaceWhereGoalId replaces existing Pairwise Comparisons by the given goal id with new Pairwise Comparisons
     *
     * @param Goal
     * @param array $items
     * @return bool
     */
    public function replaceWhereGoalId($goalId, $items)
    {
        try {
            $this->whereGoalId($goalId)->delete();
            return $this->insert($items);
        } catch (QueryException $e) {
            throw $e;
        }
    }
}
