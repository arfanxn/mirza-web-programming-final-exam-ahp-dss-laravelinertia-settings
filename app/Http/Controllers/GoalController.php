<?php

namespace App\Http\Controllers;

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
        return Inertia::render('Goals/Index', compact('goals'));
    }

    public function edit(Request $request, Goal $goal)
    {
        $goal = $this->service->getRepository()->loadRelations($goal);
        return Inertia::render('Goals/Edit', compact('goal'));
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
        return redirect('/goals')->with('message', 'Goal created successfully.');;
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
        return redirect('/goals/' . $goalId)
            ->with('message', 'Goal "' . $goalTitle . '" and its related updated successfully.');;
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
        return redirect('/goals')->with('message', 'Goal "' . $goalTitle . '" deleted successfully.');;
    }
}
