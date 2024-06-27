/**
 * accessor accesses an object with keys separated by dots.
 */
export function accessor(obj, key) {
    key.split(".").forEach((key) => {
        obj = obj[key];
    });
    return obj;
}

/**
 *  firstWhereMax
 */
export function firstWhereMax(arr, key) {
    const values = arr.map((obj) => {
        return accessor(obj, key);
    });
    const maxValue = Math.max(...values);
    const maxValueIndex = values.indexOf(maxValue);
    return arr[maxValueIndex];
}

/**
 *  where
 */
export function where(arr, key, comparator, operator = "==") {
    return arr.filter((obj) => {
        obj = accessor(obj, key);
        switch (comparator) {
            case "==":
                return obj == comparator;
                break;
            default:
                return false;
                break;
        }
    });
}

export function firstWhere(arr, key, comparator, operator = "==") {
    arr = where(arr, key, comparator, operator);
    return arr.length != 0 ? arr[0] : null;
}

export function sum(arr) {
    return arr.reduce((acc, val) => val + acc);
}

/**
 * rankOf returns the rank of the given number in the given array
 * eg: the given number is 5 and the given array is [1,3,5,7] it will return 2, if the given number is 7 it will return 1.
 */
export function rankOf(num, arr, key = null) {
    let rank = 1;
    let isNumInArray = false;
    arr.forEach((value, i) => {
        if (key != null) {
            value = accessor(obj, key);
        }
        if (!isNumInArray && num == value) {
            isNumInArray = true;
        }
        if (value > num) {
            rank++;
        }
    });
    return isNumInArray ? rank : -1; // return -1 if num is not in array
}
