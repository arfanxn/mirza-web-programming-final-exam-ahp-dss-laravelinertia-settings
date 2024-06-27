import { createMatrix } from "./../utilities/matrixUtils.js";

// createPwcsMatrixFrom creates a matrix from the given pairwise comparisons in goal object
export function createPwcsMatrixFrom(goal) {
    let criteriaLength = goal.criteria.length;
    let pwcs = goal.pairwise_comparisons;
    let matrix = createMatrix([criteriaLength, criteriaLength]);
    pwcs.forEach((pwc) => {
        let cells = matrix[pwc.primary_criterion.index];
        cells[pwc.secondary_criterion.index] = pwc.value;
        matrix[pwc.primary_criterion.index] = cells;
    });
    return matrix;
}

// createPssMatrixFrom creates a matrix from the given performance scores in goal object
export function createPssMatrixFrom(goal) {
    let alternativesLength = goal.alternatives.length;
    let criteriaLength = goal.criteria.length;
    let pss = goal.performance_scores;
    let matrix = createMatrix([alternativesLength, criteriaLength]);
    pss.forEach((pss) => {
        const row = pss.alternative.index;
        const column = pss.criterion.index;
        let cells = matrix[row];
        cells[column] = pss.value;
        matrix[row] = cells;
    });
    return matrix;
}

// riskIntervalOf returns the risk interval of the given number
export function riskIntervalOf(num) {
    switch (num) {
        case 3:
            return 0.58;
        case 4:
            return 0.9;
        case 5:
            return 1.12;
        case 6:
            return 1.24;
        case 7:
            return 1.32;
    }
}
