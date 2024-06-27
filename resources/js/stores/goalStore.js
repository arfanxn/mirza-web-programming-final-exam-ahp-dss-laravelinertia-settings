/**
 * @file Stores for the goal related data.
 * @module stores/goalStore
 */
import { derived, readable, writable } from "svelte/store";

import Alternative from "../models/Alternative.js";
import Criterion from "../models/Criterion.js";
import PairwiseComparison from "../models/PairwiseComparison.js";
import PerformanceScore from "../models/PerformanceScore.js";
import {
    createPssMatrixFrom,
    createPwcsMatrixFrom,
    riskIntervalOf,
} from "../utilities/ahpUtils";
import { sum } from "../utilities/arrayUtils.js";
import {
    createMatrix,
    dimensionOf,
    normalizeColumns,
    sumColumns,
    sumRows,
} from "../utilities/matrixUtils";

export const goal = writable({
    id: null,
    user_id: null,
    title: null,
    slug: null,
    description: null,
    alternatives: [],
    criteria: [],
    pairwise_comparisons: [],
    performance_scores: [],
});

/**
 * Constants for the minimum and maximum allowed lengths of the criteria and
 * alternatives arrays in the goal object.
 *
 * @constant
 * @type {Array<number>}
 * @default
 * @readonly
 * @property {number} minCriteriaLength - The minimum allowed number of criteria.
 * @property {number} maxCriteriaLength - The maximum allowed number of criteria.
 * @property {number} minAlternativesLength - The minimum allowed number of alternatives.
 * @property {number} maxAlternativesLength - The maximum allowed number of alternatives.
 */
const [
    minCriteriaLength,
    maxCriteriaLength,
    minAlternativesLength,
    maxAlternativesLength,
] = [
    3, // The minimum allowed number of criteria.
    7, // The maximum allowed number of criteria.
    3, // The minimum allowed number of alternatives.
    7, // The maximum allowed number of alternatives.
];

/**
 * Update the Performance Scores (PS) of the goal.
 *
 * @param {Function} updater - A function that takes the current Performance Scores as input
 *                             and returns the updated Performance Scores.
 */
export function updateGoalPss(updater) {
    goal.update(($goal) => ({
        ...$goal,
        performance_scores: updater($goal.performance_scores),
    }));
}

/**
 * Update the pairwise comparisons of the goal.
 *
 * @param {Function} updater - A function that takes the current pairwise comparisons as input
 *                             and returns the updated pairwise comparisons.
 */
export function updateGoalPwcs(updater) {
    goal.update(($goal) => ({
        ...$goal,
        pairwise_comparisons: updater($goal.pairwise_comparisons),
    }));
}

/**
 * addCriterion adds a new criterion to the goal and updates the pairwise comparisons
 * and performance scores accordingly.
 *
 * @returns {Object} The updated goal object.
 */
export function addCriterion() {
    // Update the goal object by adding a new criterion and updating the pairwise
    // comparisons and performance scores.
    goal.update(($goal) => {
        let criteria = $goal.criteria; // Get the current criteria
        let criteriaLength = criteria.length; // Get the length of the criteria
        let highestCriterionIndex = criteriaLength - 1; // Get the index of the last criterion

        // If the number of criteria is already at the maximum allowed, skip the addition
        if (criteriaLength >= maxCriteriaLength) return;

        // Create a new criterion with the next index and default values
        let criterion = new Criterion();
        criterion.name = `Criterion ${criteriaLength + 1}`;
        criterion.index = highestCriterionIndex + 1;
        criterion.impact_type = 0;
        criteria.push(criterion); // Add the new criterion to the criteria

        let pwcs = $goal.pairwise_comparisons; // Get the current pairwise comparisons

        // Create pairwise comparisons for each existing criterion with the new criterion
        criteria.forEach((c) => {
            if (c.index == criterion.index) return; // Skip the new criterion itself

            // Create a new pairwise comparison within the new created criterion
            let pwc = new PairwiseComparison();
            pwc.primary_criterion = criterion;
            pwc.secondary_criterion = c;
            pwc.value = 1;

            // Create a mirrored pairwise comparison
            let mirroredPwc = new PairwiseComparison();
            mirroredPwc.primary_criterion = c;
            mirroredPwc.secondary_criterion = criterion;
            mirroredPwc.value = 1;

            pwcs.push(pwc, mirroredPwc); // Add the pairwise comparisons to the list
        });

        // Create a main diagonal of pairwise comparison
        let mainDiagPwc = new PairwiseComparison();
        mainDiagPwc.primary_criterion = criterion;
        mainDiagPwc.secondary_criterion = criterion;
        mainDiagPwc.value = 1;
        pwcs.push(mainDiagPwc); // Add the main diagonal to the list

        // Append the newly added criterion to the alternatives
        let pss = $goal.performance_scores; // Get the current performance scores
        $goal.alternatives.forEach((alternative) => {
            let ps = new PerformanceScore();
            ps.alternative = alternative;
            ps.criterion = criterion;
            ps.value = 1;
            pss.push(ps); // Add the new performance score to the list
        });

        // Refresh with new values
        $goal.criteria = criteria;
        $goal.pairwise_comparisons = pwcs;
        $goal.performance_scores = pss;
        return $goal;
    });
}

/**
 * subtractCriterion removes the last criterion from the goal and updates the
 * pairwise comparisons and performance scores accordingly.
 *
 * @returns {Object} The updated goal object.
 */
export function subtractCriterion() {
    // Update the goal object by removing the last criterion and updating the
    // pairwise comparisons and performance scores.
    goal.update(($goal) => {
        let criteria = $goal.criteria;
        let criteriaLength = criteria.length;
        let highestCriterionIndex = criteriaLength - 1;

        // If the number of criteria is less than or equal to the minimum allowed,
        // skip the subtraction.
        if (criteriaLength <= minCriteriaLength) return;

        // Remove pairwise comparisons related to the criterion that will be removed/subtracted
        let pwcs = $goal.pairwise_comparisons.filter(
            (pwc) =>
                // Exclude pairwise comparisons with the highest criterion index
                ![
                    pwc.primary_criterion.index,
                    pwc.secondary_criterion.index,
                ].includes(highestCriterionIndex),
        );
        // Remove the criterion
        criteria = criteria.filter(
            (criterion) => criterion.index != highestCriterionIndex,
        );
        // Remove the subtracted criterion from the alternatives
        let pss = $goal.performance_scores.filter(
            (ps) => ps.criterion.index != highestCriterionIndex,
        );

        // Refresh with new values
        $goal.criteria = criteria;
        $goal.pairwise_comparisons = pwcs;
        $goal.performance_scores = pss;
        return $goal;
    });
}

/**
 * addAlternative adds a new alternative to the goal and updates the performance
 * scores accordingly.
 *
 * @returns {Object} The updated goal object.
 */
export function addAlternative() {
    // Update the goal object by adding a new alternative and updating the
    // performance scores.
    return goal.update(($goal) => {
        let alternatives = $goal.alternatives;
        let alternativesLength = alternatives.length;
        let highestAlternativeIndex = alternativesLength - 1;

        // If the number of alternatives is greater than or equal to the maximum
        // allowed, skip the addition.
        if (alternativesLength >= maxAlternativesLength) return;

        // Create a new alternative with a unique name and index.
        let alternative = new Alternative();
        alternative.name = `Alternative ${alternativesLength + 1}`;
        alternative.index = highestAlternativeIndex + 1;
        alternatives.push(alternative);

        let pss = $goal.performance_scores;

        // Iterate over each criterion and create a new pairwise comparison within
        // the new created alternative.
        $goal.criteria.forEach((criterion) => {
            let ps = new PerformanceScore();
            ps.alternative = alternative;
            ps.criterion = criterion;
            ps.value = 1;
            pss.push(ps);
        });

        // Refresh the goal object with the new values.
        $goal.alternatives = alternatives;
        $goal.performance_scores = pss;
        return $goal;
    });
}

/**
 * subtractAlternative removes the last alternative from the goal and updates the
 * performance scores accordingly.
 *
 * @returns {Object} The updated goal object.
 */
export function subtractAlternative() {
    // Update the goal object by removing the last alternative and updating the
    // performance scores.
    return goal.update(($goal) => {
        let alternatives = $goal.alternatives;
        let alternativesLength = alternatives.length;
        let highestAlternativeIndex = alternativesLength - 1;

        // If the number of alternatives is less than or equal to the minimum
        // allowed, skip the subtraction.
        if (alternativesLength <= minAlternativesLength) return;

        // Remove the alternative with the highest index from the list of alternatives.
        alternatives = alternatives.filter(
            (alternative) => alternative.index != highestAlternativeIndex,
        );
        // Remove the performance scores associated with the subtracted alternative.
        let pss = $goal.performance_scores.filter(
            (ps) => ps.alternative.index != highestAlternativeIndex,
        );

        // Refresh the goal object with the new values.
        $goal.alternatives = alternatives;
        $goal.performance_scores = pss;
        return $goal;
    });
}

/**
 * updatePwcValue updates the value of a pairwise comparison (pwc) and its associated value.
 *
 * @param {Object} pwc - The pairwise comparison object to be updated.
 * @param {String|Number} value - The new value for the pwc.
 */
export function updatePwcValue(pwc, value) {
    goal.update(($goal) => {
        value = parseFloat(value); // Convert value to a number

        // Update the pairwise comparisons array
        $goal.pairwise_comparisons = $goal.pairwise_comparisons.map(
            (mapPwc) => {
                // Check if the pairwise comparison is the main pwc
                if (
                    mapPwc.primary_criterion.index ==
                        pwc.primary_criterion.index &&
                    mapPwc.secondary_criterion.index ==
                        pwc.secondary_criterion.index
                ) {
                    mapPwc.value = value; // Update the main pwc
                } else if (
                    // Check if the pairwise comparison is the mirrored pwc
                    mapPwc.primary_criterion.index ==
                        pwc.secondary_criterion.index &&
                    mapPwc.secondary_criterion.index ==
                        pwc.primary_criterion.index
                ) {
                    mapPwc.value = 1 / value; // Update the mirrored pwc
                }
                return mapPwc;
            },
        );

        return $goal;
    });
}

export const criteriaLength = derived(goal, (goal) => goal.criteria.length);
export const alternativesLength = derived(
    goal,
    (goal) => goal.alternatives.length,
);

/**
 * Derived store that holds the pairwise comparisons matrix.
 *
 * @returns {Array} A 2-dimensional array where each row represents a vector
 * of the CAAS (Comparative Analysis of System) vectors. The elements of the
 * array are the values of the pairwise comparisons.
 */
export const pwcsMatrix = derived(goal, (goal) => {
    // Create the pairwise comparisons matrix
    return createPwcsMatrixFrom(goal);
});

/**
 * Derived store that calculates the sum of all rows in the pwcsMatrix.
 * Each row represents a vector of the CAAS (Comparative Analysis of System) vectors.
 * The sum of all vectors represents the sum of all alternatives in the criterion.
 *
 * @returns {Array} An array of length number of criteria, where each element
 * represents the sum of all alternatives in the corresponding criterion.
 */
export const pwcsColumnsTotals = derived(pwcsMatrix, (matrix) => {
    // Calculate the sum of all rows in the pwcsMatrix.
    // Each row represents a vector of the CAAS vectors.
    return sumColumns(matrix);
});

/**
 * Derived store that calculates the normalized PWCS matrix.
 * The normalization process divides each element of the matrix by the sum of the row it belongs to.
 * This normalization ensures that the pairwise comparisons represented by the matrix are relative to each other,
 * and not related to the absolute value of each element.
 *
 * @returns {Array} A normalized PWCS matrix.
 */
export const normalizedPwcsMatrix = derived(pwcsMatrix, (matrix) => {
    // Normalize the matrix by dividing each element by the sum of the row it belongs to
    return normalizeColumns(matrix);
});

/**
 * Derived store that calculates the sum of all rows in the normalized PWCS matrix.
 * Each row represents a vector of the CAAS (Comparative Analysis of System) vectors.
 * The sum of all vectors represents the sum of all alternatives in the criterion.
 *
 * @returns {Array} An array of length number of criteria, where each element
 * represents the sum of all alternatives in the corresponding criterion.
 */
export const normalizedPwcsColumnsTotals = derived(
    normalizedPwcsMatrix,
    /**
     * Calculates the sum of all rows in the normalized PWCS matrix.
     *
     * @param {Array} matrix - The normalized PWCS matrix.
     * @returns {Array} An array of length number of criteria, where each element
     * represents the sum of all alternatives in the corresponding criterion.
     */
    (matrix) => sumColumns(matrix),
);

/**
 * Derived store that calculates the sum of all rows in the normalized PWCS matrix.
 * Each row represents a vector of the CAAS (Comparative Analysis of System) vectors.
 * The sum of all vectors represents the sum of all alternatives in the criterion.
 *
 * @returns {Array} An array of length number of criteria, where each element
 * represents the sum of all alternatives in the corresponding criterion.
 */
export const pwcsVectors = derived(normalizedPwcsMatrix, (matrix) => {
    // Calculate the sum of all rows in the normalized PWCS matrix.
    // Each row represents a vector of the CAAS vectors.
    return sumRows(matrix);
});

/**
 * Derived store that calculates the total of all vectors in the pwcsVectors.
 * The pwcsVectors represent the CAAS vectors, and the total of all vectors
 * represents the sum of all alternatives in the criterion.
 *
 * @returns {number} The total of all vectors in the pwcsVectors.
 */
export const pwcsVectorsTotal = derived(pwcsVectors, (vectors) => {
    // Calculate the sum of all vectors in the pwcsVectors
    return sum(vectors);
});

/**
 * Derived store that calculates the weights of the criteria.
 * The weights represent the importance of each criterion in the pairwise comparisons.
 * The weights are calculated by dividing each vector of the pwcsVectors by the
 * number of criteria.
 *
 * @returns {Array} An array of weights.
 */
export const pwcsWeights = derived(
    [criteriaLength, pwcsVectors],
    /**
     * Calculates the weights of the criteria based on the vectors of the pairwise
     * comparisons and the number of criteria.
     *
     * @param {Array} criteriaLength - The length of the criteria array.
     * @param {Array} vectors - The vectors of the pairwise comparisons.
     * @returns {Array} An array of weights.
     */
    ([criteriaLength, vectors]) =>
        vectors.map((vector) => vector / criteriaLength),
);

/**
 * Derived store that calculates the sum of the weights of the criteria.
 * The weights represent the importance of each criterion in the pairwise comparisons.
 *
 * @returns {number} The sum of the weights of the criteria.
 */
export const pwcsWeightsTotal = derived(pwcsWeights, (weights) => {
    // Calculate the sum of the weights of the criteria.
    return sum(weights);
});

/**
 * Derived store that calculates the eigen values of the pairwise comparisons.
 * The eigen values represent the importance of each criterion in the pairwise comparisons.
 *
 * @returns {Array} An array of length criteriaLength, where each element
 * represents the eigen value for a given criterion.
 */
export const pwcsEigenValues = derived(
    [criteriaLength, pwcsMatrix, pwcsWeights],
    ([criteriaLength, matrix, weights]) => {
        // Initialize an array to hold the eigen values
        let eigenValues = new Array(criteriaLength).fill(0);

        // Calculate the eigen value for each criterion
        for (let row = 0; row < criteriaLength; row++) {
            let eigenValue = 0;

            // Sum up the matrix values for each criterion times the corresponding weight
            for (let column = 0; column < criteriaLength; column++) {
                eigenValue = matrix[row][column] * weights[column] + eigenValue;
            }

            // Store the calculated eigen value in the array
            eigenValues[row] = eigenValue;
        }

        // Return the array of eigen values
        return eigenValues;
    },
);

/**
 * Derived store that calculates the sum of the eigen values of the pairwise comparisons.
 * The eigen values represent the importance of each criterion in the pairwise comparisons.
 *
 * @returns {number} The sum of the eigen values of the pairwise comparisons.
 */
export const pwcsEigenValuesTotal = derived(pwcsEigenValues, (eigenValues) => {
    // Calculate the sum of the eigen values of the pairwise comparisons.
    // The eigen values represent the importance of each criterion in the pairwise comparisons.
    return sum(eigenValues);
});

/**
 * Derived store that calculates the t-statistic of the pairwise comparisons.
 * The t-statistic is calculated based on the weights and eigen values of the pairwise comparisons.
 *
 * @returns {number} The t-statistic of the pairwise comparisons.
 */
export const pwcsTStatistic = derived(
    [criteriaLength, pwcsWeights, pwcsEigenValues],
    /**
     * Calculates the t-statistic of the pairwise comparisons.
     *
     * @param {Array<number>} criteriaLength - The number of criteria.
     * @param {Array<number>} weights - The weights of the pairwise comparisons.
     * @param {Array<number>} eigenValues - The eigen values of the pairwise comparisons.
     * @returns {number} The t-statistic of the pairwise comparisons.
     */
    ([criteriaLength, weights, eigenValues]) => {
        let tStatistic = 0;
        // Calculate the t-statistic by summing the ratio of eigen value to weight
        for (let i = 0; i < criteriaLength; i++) {
            tStatistic = eigenValues[i] / weights[i] + tStatistic;
        }
        // Divide the sum by the number of criteria to get the t-statistic
        tStatistic = tStatistic / criteriaLength;
        return tStatistic;
    },
);

/**
 * Derived store that calculates the confidence interval of the pairwise comparisons.
 * The confidence interval is calculated based on the number of criteria and the t-statistic.
 *
 * @returns {number} The confidence interval of the pairwise comparisons.
 */
export const pwcsConfidenceInterval = derived(
    [criteriaLength, pwcsTStatistic],
    /**
     * Calculates the confidence interval of the pairwise comparisons.
     *
     * @param {number} criteriaLength - The number of criteria.
     * @param {number} tStatistic - The t-statistic.
     * @returns {number} The confidence interval.
     */
    ([criteriaLength, tStatistic]) => {
        // Calculate the confidence interval based on the number of criteria and the t-statistic
        let confidenceInterval =
            (tStatistic - criteriaLength) / (criteriaLength - 1);
        return confidenceInterval;
    },
);

/**
 * Derived store that calculates the risk interval of the pairwise comparisons.
 * The risk interval is calculated based on the number of criteria.
 *
 * @returns {number} The risk interval of the pairwise comparisons.
 */
export const pwcsRiskInterval = derived(criteriaLength, (cL) => {
    // Calculate the risk interval based on the number of criteria
    return riskIntervalOf(cL);
});

/**
 * Derived store that calculates the consistency ratio of the pairwise comparisons.
 * The consistency ratio is calculated as the ratio of the confidence interval to the risk interval.
 *
 * @returns {number} The consistency ratio of the pairwise comparisons.
 */
export const pwcsConsistencyRatio = derived(
    [pwcsConfidenceInterval, pwcsRiskInterval],
    /**
     * Calculates the consistency ratio of the pairwise comparisons.
     *
     * @param {Array} args - The arguments passed to the derived store.
     * @param {number} args[0] - The confidence interval.
     * @param {number} args[1] - The risk interval.
     * @returns {number} The consistency ratio of the pairwise comparisons.
     */
    ([ci, ri]) => ci / ri,
);

/**
 * Derived store that calculates the performance score matrix (PSS) from
 * the performance scores in the goal object.
 *
 * @returns {Array} A matrix of size [alternativesLength, criteriaLength]
 * where each cell represents the PSS value for a given alternative-criterion pair.
 */
export const pssMatrix = derived(goal, (goal) => {
    // Extract relevant data from the goal object
    // and create the PSS matrix using the createPssMatrixFrom function
    return createPssMatrixFrom(goal);
});

/**
 * Derived store that calculates the criteria-alternatives vectors (CAAS)
 * from the performance scores.
 *
 * @returns {Array} A matrix of size [criteriaLength, alternativesLength]
 * where each cell represents the CAAS value for a given criterion-alternative pair.
 */
export const pssCriteriaAlternativesVectors = derived(goal, (goal) => {
    // Extract relevant data from the goal object
    const pss = goal.performance_scores; // Performance scores
    const criteria = goal.criteria; // Criteria
    const alternatives = goal.alternatives; // Alternatives
    const criteriaLength = criteria.length; // Number of criteria
    const alternativesLength = alternatives.length; // Number of alternatives

    // Initialize the CAAS matrix
    let caasVectors = createMatrix([criteriaLength, alternativesLength]);

    // Iterate over each criterion
    criteria.forEach((criterion) => {
        // Filter the relevant performance scores
        let relatedPss = pss.filter(
            (ps) => ps.criterion.index == criterion.index,
        );

        // Calculate the min and max values for the performance scores
        let pssValues = relatedPss.map((ps) => ps.value);
        let lowestPsValue = Math.min(...pssValues);
        let highestPsValue = Math.max(...pssValues);

        // Calculate the CAAS value for each alternative and store it in the matrix
        relatedPss.forEach((ps) => {
            let vectors = caasVectors[ps.criterion.index];
            vectors[ps.alternative.index] = criterion.impact_type
                ? ps.value / highestPsValue
                : lowestPsValue / ps.value;
            caasVectors[ps.criterion.index] = vectors;
        });
    });

    return caasVectors;
});

/**
 * Calculates the totals of the CAAS vectors for each criterion.
 *
 * @returns {Array} An array of length criteriaLength, where each element
 * represents the total CAAS value for a given criterion.
 */
export const pssCriteriaAlternativesVectorsTotals = derived(
    pssCriteriaAlternativesVectors, // The CAAS vectors
    (caasVectors) => {
        // Calculate the total CAAS value for each criterion
        const totals = caasVectors.map((vectors) => {
            // Sum up the CAAS values for each alternative
            return vectors.reduce((acc, vector) => acc + vector, 0);
        });

        // Return the totals for each criterion
        return totals;
    },
);

/**
 * Calculates the weights of the CAAS vectors for each criterion.
 *
 * @returns {Array} An array of length criteriaLength, where each element
 * represents an array of weights for each alternative of a given criterion.
 */
export const pssCriteriaAlternativesWeights = derived(
    [pssCriteriaAlternativesVectors, pssCriteriaAlternativesVectorsTotals],
    ([caasVectors, caasVectorsTotals]) => {
        // Calculate the weight for each alternative for each criterion
        let caasWeights = caasVectors.map((vectors, criterionIndex) => {
            // Divide each CAAS vector by the total CAAS vector for the criterion
            return vectors.map(
                (vector) => vector / caasVectorsTotals[criterionIndex],
            );
        });

        // Return the weights for each criterion
        return caasWeights;
    },
);

/**
 * Calculates the totals of the weights of the CAAS vectors for each criterion.
 *
 * @returns {Array} An array of length criteriaLength, where each element
 * represents the total weight value for a given criterion.
 */
export const pssCriteriaAlternativesWeightsTotals = derived(
    pssCriteriaAlternativesWeights, // The weights of the CAAS vectors
    (caasWeights) => {
        // Calculate the total weight value for each criterion
        const totals = caasWeights.map((vectors) => {
            // Sum up the weights for each alternative
            return vectors.reduce((acc, vector) => acc + vector, 0);
        });

        // Return the totals for each criterion
        return totals;
    },
);

/**
 * Calculates the Rankingization matrix.
 *
 * This matrix contains the weighted sum of the CAAS weights and the PWC weights
 * for each criterion and alternative.
 *
 * @returns {Array} A matrix of shape [criteriaLength, alternativesLength], where
 * each element represents the weighted sum of the CAAS weights and the PWC weights
 * for a given criterion and alternative.
 */
export const rankingizationMatrix = derived(
    [
        pwcsWeights, // The PWC weights for each criterion
        pssCriteriaAlternativesWeights, // The CAAS weights for each criterion and alternative
        alternativesLength, // The number of alternatives
        criteriaLength, // The number of criteria
    ],
    ([caWeights, caasWeights, alternativesLength, criteriaLength]) => {
        // Initialize the matrix with the dimensions [criteriaLength, alternativesLength]
        let matrix = createMatrix(dimensionOf(caasWeights));

        // Iterate over each criterion
        for (
            let criterionIndex = 0;
            criterionIndex < criteriaLength;
            criterionIndex++
        ) {
            // Iterate over each alternative
            for (
                let alternativeIndex = 0;
                alternativeIndex < alternativesLength;
                alternativeIndex++
            ) {
                // Calculate the weighted sum of the CAAS weights and the PWC weights
                let cells = matrix[criterionIndex]; // Get the current row of the matrix
                let value =
                    caasWeights[criterionIndex][alternativeIndex] * // CAAS weight for the current criterion and alternative
                    caWeights[criterionIndex]; // PWC weight for the current criterion
                cells[alternativeIndex] = value; // Update the matrix with the weighted sum
                matrix[criterionIndex] = cells; // Update the matrix with the updated row
            }
        }

        return matrix;
    },
);

/**
 * Calculates the rankingization scores for each alternative.
 *
 * The rankingization scores are calculated by summing the weighted sum of the CAAS weights and PWC weights
 * for each criterion and alternative.
 *
 * @returns {Array} An array of length alternativesLength, where each element represents the rankingization score
 * for a given alternative.
 */
export const rankingizationScores = derived(
    [alternativesLength, rankingizationMatrix], // Dependencies: the number of alternatives and the rankingization matrix
    ([alternativesLength, matrix]) => {
        // Initialize an array to store the rankingization scores for each alternative
        let scores = new Array(alternativesLength).fill(0);

        // Iterate over each row of the rankingization matrix
        matrix.forEach((cells, criterionIndex) => {
            // Iterate over each cell in the current row
            cells.forEach((value, alternativeIndex) => {
                // Update the rankingization score for the current alternative by adding the weighted sum for the current criterion and alternative
                scores[alternativeIndex] += value;
            });
        });

        return scores;
    },
);
