<?php

namespace App\Services;

use App\Enums\Criterion\ImpactType;
use App\Models\Goal;
use App\Models\PairwiseComparison;
use App\Models\PerformanceScore;
use App\Repositories\AlternativeRepository;
use App\Repositories\CriterionRepository;
use App\Repositories\GoalRepository;
use App\Repositories\PairwiseComparisonRepository;
use App\Repositories\PerformanceScoreRepository;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use stdClass;

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
                range(0, 4)
            );
            $this->criterionRepository->insert($criteriaData);
            $criterionIds = $this->criterionRepository->whereGoalId($goalId)->select('id')->pluck('id');

            $alternativesData = array_map(
                fn ($index) => [
                    'goal_id' => $goalId,
                    'name' => 'Alternative ' . strval($index + 1),
                    'index' => $index
                ],
                range(0, 4)
            );
            $this->alternativeRepository->insert($alternativesData);
            $alternativeIds = $this->alternativeRepository->whereGoalId($goalId)->select('id')->pluck('id');

            $this->psRepository->insertDefaultFrom($alternativeIds, $criterionIds);
            $this->pwcRepository->insertDefaultFrom($criterionIds);
        });
    }

    /**
     * rankingization
     *
     * @param Goal $goal
     * @return \Illuminate\Support\Collection
     */
    public function rankingization(Goal $goal)
    {
        $goal = $this->repository->loadRelations($goal);
        $alternatives = collect($goal->alternatives);
        $criteria = collect($goal->criteria);
        $pss = collect($goal->performanceScores);
        $pssGroupedByCriterion = $pss->groupBy('criterion.index');
        $pwcs = collect($goal->pairwiseComparisons);
        $pwcsGroupedBySecondaryCriterion = $pwcs->groupBy('secondaryCriterion.index');
        $pwcsGroupedBySecondaryCriterionSum =
            $pwcsGroupedBySecondaryCriterion->map(fn ($pwcs) => $pwcs->sum('value'));
        $normPwcs = $pwcs->map(function (PairwiseComparison $pwc) use ($pwcsGroupedBySecondaryCriterionSum) {
            $pwc = clone $pwc;
            $pwc->value = $pwc->value / $pwcsGroupedBySecondaryCriterionSum[$pwc->secondaryCriterion->index];
            return $pwc;
        });
        $normPwcsGroupedBySecondaryCriterion = $normPwcs->groupBy('secondaryCriterion.index');
        $normPwcsGroupedBySecondaryCriterionSum =
            $normPwcsGroupedBySecondaryCriterion->map(fn ($pwcs) => $pwcs->sum('value'));
        $pwcsVectorValues = $normPwcs->groupBy('primaryCriterion.index')->map(fn ($pwcs) => $pwcs->sum('value'));
        $pwcsWeights = $pwcsVectorValues->map(fn ($x) => $x / $criteria->count());
        $pwcsEigenValues = $pwcs
            ->map(function (PairwiseComparison $pwc) use ($pwcsWeights) {
                $pwc = clone $pwc;
                $pwc->value = $pwc->value * $pwcsWeights[$pwc->secondaryCriterion->index];
                return $pwc;
            })
            ->groupBy('primaryCriterion.index')
            ->map(fn ($pwcs) => $pwcs->sum('value'));
        $pwcsEigenValuesSum = $pwcsEigenValues->sum();
        $tStatistic = $pwcsEigenValues->map(fn ($x, $i) => $x / $pwcsWeights[$i])->sum() / $criteria->count();
        $confidenceInterval = ($tStatistic - $criteria->count()) / ($criteria->count() - 1);
        $ratioInterval = match ($criteria->count()) {
            3 => 0.58,
            4 => 0.9,
            5 => 1.12,
            6 => 1.24,
            7 => 1.32,
            default => -1
        };
        $consistentInterval = $confidenceInterval / $ratioInterval;
        $isConsistentInterval = $consistentInterval <= 0.1;
        $pssCaasVectors = $pss->map(function (PerformanceScore $ps) use ($pssGroupedByCriterion) {
            $criterionImpactType = $ps->criterion->impact_type->toArray();
            $ps = clone $ps;
            $ps->value = $criterionImpactType == ImpactType::Cost ?
                $pssGroupedByCriterion[$ps->criterion->index]->min('value') / $ps->value
                : $ps->value / $pssGroupedByCriterion[$ps->criterion->index]->max('value');
            return $ps;
        });
        $pssCaasVectorsGroupedByCriterion = $pssCaasVectors->groupBy('criterion.index');
        $pssCaasWeights = $pssCaasVectors
            ->map(function (PerformanceScore $ps)  use ($pssCaasVectorsGroupedByCriterion) {
                $ps = clone $ps;
                $ps->value = $ps->value / $pssCaasVectorsGroupedByCriterion[$ps->criterion->index]->sum('value');
                return $ps;
            });
        $pssCaasWeightsMultipliedByPwcsWeights =  $pssCaasWeights
            ->map(function (PerformanceScore $ps) use ($pwcsWeights) {
                $ps = clone $ps;
                $ps->value = $ps->value * $pwcsWeights[$ps->criterion->index];
                return $ps;
            });
        $pssCaasWmbpwAlternativesSum = $pssCaasWeightsMultipliedByPwcsWeights
            ->groupBy('alternative.index')
            ->map(fn ($pss) => $pss->sum('value'));
        $rankings = $pssCaasWmbpwAlternativesSum
            ->map(function ($x, $i) use ($alternatives) {
                $obj = new stdClass();
                $obj->alternative_id = $alternatives[$i]->id;
                $obj->alternative_name = $alternatives[$i]->name;
                $obj->alternative_index = $alternatives[$i]->index;
                $obj->alternative_score = $x;
                return $obj;
            })
            ->sortByDesc('alternative_score')
            ->values()
            ->map(function ($obj, $i) {
                $rank =  $i + 1;
                $obj->alternative_rank = $rank;
                return $obj;
            });

        return $rankings;
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
