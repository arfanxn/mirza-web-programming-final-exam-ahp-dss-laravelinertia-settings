<?php

if (!function_exists('accessor')) {
    /**
     * accessor accesses an object with keys separated by dots.
     *
     * @param array $obj
     * @param string $key
     * @param mixed $defaultValue
     * @return mixed
     */
    function accessor($obj, $key, $defaultValue = null)
    {
        $keys = explode('.', $key);
        foreach ($keys as $key) {
            if (!isset($obj[$key])) {
                return $defaultValue; // or throw an exception, depending on your use case
            }
            $obj = $obj[$key];
        }
        return $obj;
    }
}

if (!function_exists('isset_and_not_null')) {
    /**
     * isset_and_not_null
     *
     * @param array $obj
     * @param string $key
     * @param mixed $defaultValue
     * @return mixed
     */
    function isset_and_not_null($obj, $key)
    {
        try {
            $value = accessor($obj, $key);
            return $value != null ? true : false;
        } catch (\Throwable $th) {
            return false;
        }
    }
}
