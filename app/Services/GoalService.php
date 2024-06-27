<?php

namespace App\Services;

use App\Enums\Criterion\ImpactType;
use App\Repositories\AlternativeRepository;
use App\Repositories\CriterionRepository;
use App\Repositories\GoalRepository;
use App\Repositories\PairwiseComparisonRepository;
use App\Repositories\PerformanceScoreRepository;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class GoalService
{
    private GoalRepository $repository;
    private CriterionRepository $criterionRepository;
    private AlternativeRepository $alternativeRepository;
    private PerformanceScoreRepository $psRepository;
    private PairwiseComparisonRepository $pwcRepository;

    public function __construct(
        GoalRepository $repository,
        CriterionRepository $criterionRepository,
        AlternativeRepository $alternativeRepository,
        PerformanceScoreRepository $psRepository,
        PairwiseComparisonRepository $pwcRepository,
    ) {
        $this->repository = $repository;
        $this->criterionRepository = $criterionRepository;
        $this->alternativeRepository = $alternativeRepository;
        $this->psRepository = $psRepository;
        $this->pwcRepository = $pwcRepository;
    }

    /**
     * getRepository returns the service's repository
     *
     * @return GoalRepository
     */
    public function getRepository()
    {
        return $this->repository;
    }

    /**
     * store
     *
     * @param array $data
     * @return boolean
     */
    public function store($data)
    {
        return DB::transaction(function () use ($data) {
            $goalData = Arr::only($data, ['user_id', 'title', 'description']);
            if (!isset_and_not_null($goalData, 'title')) {
                $userId = $data['user_id'];
                $goalsCount = $this->repository->whereUserId($userId)->count();
                $goalData['title'] = 'Goal ' . strval($goalsCount + 1);
            }
            $goal = $this->repository->save($goalData);
            $goalId = $goal->id;

            $criteriaData =  array_map(
                fn ($index) => [
                    'goal_id' => $goalId,
                    'name' => 'Criterion ' . strval($index + 1),
                    'impact_type' => ImpactType::Cost,
                    'index' => $index,
                ],
                range(0, 2)
            );
            $this->criterionRepository->insert($criteriaData);
            $criterionIds = $this->criterionRepository->whereGoalId($goalId)->select('id')->pluck('id');

            $alternativesData = array_map(
                fn ($index) => [
                    'goal_id' => $goalId,
                    'name' => 'Alternative ' . strval($index + 1),
                    'index' => $index
                ],
                range(0, 2)
            );
            $this->alternativeRepository->insert($alternativesData);
            $alternativeIds = $this->alternativeRepository->whereGoalId($goalId)->select('id')->pluck('id');

            $this->psRepository->insertDefaultFrom($alternativeIds, $criterionIds);
            $this->pwcRepository->insertDefaultFrom($criterionIds);
        });
    }

    /**
     * update
     *
     * @param array $data
     * @return boolean
     */
    public function update($data)
    {
        return DB::transaction(function () use ($data) {
            $goalData = Arr::only($data, ['id', 'user_id', 'title', 'description']);
            $goal = $this->repository->save($goalData);
            $goalId = $goal->id;

            $criteriaData = array_map(function ($criterionData) use ($goalId) {
                $criterionData['goal_id'] = $goalId;
                return $criterionData;
            }, $data['criteria']);
            $this->criterionRepository->replaceWhereGoalId($goalId, $criteriaData);
            $sortedCriteria = $this->criterionRepository->whereGoalId($goalId)
                ->orderBy('index', 'ASC')->get();

            $alternativesData = array_map(function ($alternativeData) use ($goalId) {
                $alternativeData['goal_id'] = $goalId;
                return $alternativeData;
            }, $data['alternatives']);
            $this->alternativeRepository->replaceWhereGoalId($goalId, $alternativesData);
            $sortedAlternatives = $this->alternativeRepository->whereGoalId($goalId)
                ->orderBy('index', 'ASC')->get();

            $pssData = array_map(function ($psData) use ($sortedCriteria, $sortedAlternatives) {
                $criterionIndex = $psData['criterion']['index'];
                $alternativeIndex = $psData['alternative']['index'];
                $psData['criterion_id'] = $sortedCriteria[$criterionIndex]->id;
                $psData['alternative_id'] = $sortedAlternatives[$alternativeIndex]->id;
                return $psData;
            }, $data['performance_scores']);
            $this->psRepository->replaceWhereGoalId($goalId, $pssData);

            $pwcsData = array_map(function ($pwcData) use ($sortedCriteria) {
                $primaryCriterionIndex = $pwcData['primary_criterion']['index'];
                $secondaryCriterionIndex = $pwcData['secondary_criterion']['index'];
                $pwcData['primary_criterion_id'] = $sortedCriteria[$primaryCriterionIndex]->id;
                $pwcData['secondary_criterion_id'] = $sortedCriteria[$secondaryCriterionIndex]->id;
                return $pwcData;
            }, $data['pairwise_comparisons']);
            $this->pwcRepository->replaceWhereGoalId($goalId, $pwcsData);
        });
    }
}
