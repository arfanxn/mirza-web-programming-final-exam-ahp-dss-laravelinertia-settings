<?php

namespace App\Repositories;

use App\Models\Goal;;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class GoalRepository
{
    public function __construct()
    {
        //
    }

    public function paginate($options)
    {
        $options = collect($options);
        $userId = $options['user_id'] ?? null;
        return Goal::query()
            ->when($userId, fn (EloquentBuilder $q) => $q->whereUserId($userId))
            ->when($options->has('keyword'), function ($q) use ($options) {
                $keyword = $options->get('keyword') . '%';
                return $q->where('title', 'LIKE', $keyword)
                    ->orWhere('description', 'LIKE', $keyword);
            })
            ->orderBy('created_at', 'DESC')
            ->paginate();
    }

    /**
     * create
     *
     * @param array $item
     * @return Goal
     * @throws QueryException
     */
    public function save($item)
    {
        $model = (new Goal());
        if ($id = $item['id'] ?? null) {
            $model = $model->query()->where('id', $id)->first();
        } else {
            $model->id = Str::ulid();
        }
        $model->user_id = $item['user_id'];
        $model->title = $item['title'];
        $model->slug = Str::slug($model->title);
        $model->description = $item['description'] ?? null;

        try {
            $model->save();
        } catch (QueryException $e) {
            throw $e;
        }

        return $model;
    }

    /**
     * whereUserId
     * @param int $userId
     * @return EloquentBuilder
     */
    public function whereUserId($userId)
    {
        return Goal::query()->where('user_id', $userId);
    }

    /**
     * loadRelations loads all relationships of the given model
     *
     * @param Goal $model
     */
    public function loadRelations($model)
    {
        return $model->load([
            'criteria' => fn ($q) => $q->orderBy('index', 'asc'),
            'alternatives' => fn ($q) => $q->orderBy('index', 'ASC'),
            'performanceScores' => fn ($q) => $q->with(['criterion', 'alternative']),
            'pairwiseComparisons' => fn ($q) => $q
                ->with([
                    'primaryCriterion' => fn ($q) => $q->orderBy('index', 'asc'),
                    'secondaryCriterion' => fn ($q) => $q->orderBy('index', 'asc'),
                ])
        ]);
    }
}
