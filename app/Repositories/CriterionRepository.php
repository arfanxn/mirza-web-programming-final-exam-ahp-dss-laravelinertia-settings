<?php

namespace App\Repositories;

use App\Models\Criterion;
use App\Models\Goal;
use Illuminate\Database\QueryException;
use Illuminate\Support\Arr;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Support\Str;

class CriterionRepository
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
                Arr::only($item, ['goal_id', 'name', 'impact_type', 'index', 'weight']),
                ['id' => $item['id'] ?? Str::ulid()],
                ['created_at' => $item['created_at'] ?? $createdAt],
                ['updated_at' => $item['updated_at'] ?? null]
            ),
            $items
        );

        try {
            foreach (array_chunk($items, 100) as $chunk) {
                Criterion::insert($chunk);
            }
            return true;
        } catch (QueryException $e) {
            throw $e;
        }
    }

    /**
     * whereGoalId
     *
     * @param int $goalId
     * @return \lluminate\Database\Eloquent\Builder
     */
    function whereGoalId($goalId)
    {
        $query = Criterion::query()->where(
            fn (EloquentBuilder $q) => $q->where('goal_id', $goalId)
        );
        return $query;
    }

    /**
     * saveManyFrom
     *
     * @param Goal $goal
     * @param array $items
     * @return Criterion[]
     * @throws QueryException
     */
    public function saveManyFrom($goal, $items)
    {
        $models = array_map(function ($item) {
            $model = new Criterion();
            $model->name = $item['name'];
            $model->impact_type = floatval($item['impact_type']);
            $model->index = floatval($item['index']);
            $model->weight = isset($item['weight']) ? floatval($item['weight']) : null;
            return $model;
        }, $items);

        try {
            return $goal->criteria()->saveMany($models);
        } catch (QueryException $e) {
            throw $e;
        }
    }

    /**
     * replaceWhereGoalId
     *
     * @param int $goalId
     * @param array $items
     * @return bool
     * @throws QueryException
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
