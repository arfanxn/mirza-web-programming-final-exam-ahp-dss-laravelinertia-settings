export function createMatrix(dimension, defaultValue = 0) {
    let matrix = new Array(dimension[0]).fill(defaultValue);
    for (let i = 0; i < matrix.length; i++) {
        matrix[i] = new Array(dimension[1]).fill(defaultValue);
    }
    return matrix;
}

export function dimensionOf(matrix) {
    let row = matrix.length;
    let col = 0;
    for (let i = 0; i < row; i++) {
        col = Math.max(col, matrix[i].length);
    }
    return [row, col];
}

export function sumRows(matrix) {
    let rowsLength = matrix.length;
    let totals = new Array(rowsLength).fill(0);
    for (let row = 0; row < matrix.length; row++) {
        for (let column = 0; column < rowsLength; column++) {
            totals[row] += matrix[row][column];
        }
    }
    return totals;
}

export function sumColumns(matrix) {
    let columnsLength = matrix[0].length;
    let totals = new Array(columnsLength).fill(0);
    for (let row = 0; row < matrix.length; row++) {
        for (let column = 0; column < columnsLength; column++) {
            totals[column] += matrix[row][column];
        }
    }
    return totals;
}

export function normalizeRows(matrix) {
    return matrix.map((row) => {
        const sum = row.reduce((a, b) => a + b, 0);
        return row.map((x) => x / sum);
    });
}

export function normalizeColumns(matrix) {
    let totals = sumColumns(matrix);
    return matrix.map((row) => row.map((x, i) => x / totals[i]));
}

export function normalizeMatrix(matrix) {
    const sum = matrix.reduce(
        (sum, row) => sum + row.reduce((a, b) => a + b, 0),
        0,
    );
    return matrix.map((row) => row.map((x) => x / sum));
}
