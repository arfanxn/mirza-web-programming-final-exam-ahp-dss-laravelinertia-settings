export function urlSearchParams(key, paramsStr = null) {
    const searchParams = new URLSearchParams(
        paramsStr == null ? window.location.search : paramsStr,
    );
    if (searchParams.has(key) == false) return null;
    return searchParams.get(key);
}
