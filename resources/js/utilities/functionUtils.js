export function call(callable, ...args) {
    if (typeof callable !== "function") return;
    return callable(...args);
}
