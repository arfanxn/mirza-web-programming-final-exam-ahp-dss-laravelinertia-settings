<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreGoalRequest;
use App\Http\Requests\UpdateGoalRequest;
use App\Models\Goal;
use App\Services\GoalService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class GoalController extends Controller
{
    private GoalService $service;

    public function __construct(GoalService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $request->merge(['user_id' => $request->user()->id]);
        $goals = $this->service->getRepository()->paginate($request->all());
        return response()->json([
            'message' => 'Goals retrieved successfully.',
            'goals' => $goals
        ]);
    }

    public function show(Request $request, Goal $goal)
    {
        $goal = $this->service->getRepository()->loadRelations($goal);
        return response()->json([
            'message' => 'Goal retrieved successfully.',
            'goal' => $goal
        ]);
    }

    /**
     * store
     *
     * @param StoreGoalRequest $request
     */
    public function store(StoreGoalRequest $request)
    {
        $data = $request->validated();
        $this->service->store($data);
        return response()->json([
            'message' => 'Goal created successfully.',
        ]);
    }


    /**
     * update
     *
     * @param UpdateGoalRequest $request
     */
    public function update(UpdateGoalRequest $request)
    {
        $data = $request->validated();
        $goalId = $request->get('id');
        $goalTitle = $request->get('title');
        $this->service->update($data);
        return response()->json([
            'message' => 'Goal "' . $goalTitle . '" and its related updated successfully.'
        ]);
    }

    /**
     * delete
     *
     * @param Goal $goal
     */
    public function destroy(Goal $goal)
    {
        $goalTitle = $goal->title; // retrieve the title of goal before deletion
        $goal->delete();
        return response()->json([
            'message' => 'Goal "' . $goalTitle . '" deleted successfully.'
        ]);
    }
}
