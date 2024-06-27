<?php

namespace App\Repositories;

use App\Models\PerformanceScore;
use Illuminate\Database\QueryException;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;

class PerformanceScoreRepository
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
                Arr::only($item, ['alternative_id', 'criterion_id', 'value']),
                ['id' => $item['id'] ?? Str::ulid()],
                ['created_at' => $item['created_at'] ?? $createdAt],
                ['updated_at' => $item['updated_at'] ?? null]
            ),
            $items
        );

        try {
            foreach (array_chunk($items, 100) as $chunk) {
                PerformanceScore::insert($chunk);
            }
            return true;
        } catch (QueryException $e) {
            throw $e;
        }
    }

    /**
     * insertDefaultFrom
     *
     * @param array $alternativeIds
     * @param array $criterionIds
     * @return bool
     * @throws QueryException
     */
    public function insertDefaultFrom($alternativeIds, $criterionIds)
    {
        $items = [];
        foreach ($alternativeIds as $alternativeId) {
            foreach ($criterionIds as $criterionId) {
                array_push($items, [
                    'id' =>  Str::ulid(),
                    'alternative_id' => $alternativeId,
                    'criterion_id' => $criterionId,
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
        $query = PerformanceScore::query()->where(
            fn (EloquentBuilder $q) => $q->whereHas(
                'criterion',
                fn (EloquentBuilder $q) => $q->whereHas(
                    'goal',
                    fn (EloquentBuilder $q) => $q->where('id', $goalId)
                )
            )
                ->orWhereHas(
                    'alternative',
                    fn (EloquentBuilder $q) => $q->whereHas(
                        'goal',
                        fn (EloquentBuilder $q) => $q->where('id', $goalId)
                    )
                )
        );
        return $query;
    }


    /**
     * replaceWhereGoalId replaces existing Performance Scores by the given goal id with new Performance Scores
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
