<?php
/**
 * Utils.
 *
 * @copyright Copyright (c) 2016 Starweb / Ehandelslogik i Lund AB
 * @license   BSD 3-Clause
 */

namespace Starlit\Utils;

/**
 * Array utilities.
 *
 * @author Andreas Nilsson <http://github.com/jandreasn>
 */
class Arr
{
    /**
     * If any of the provided values is in an array.
     * This is a convenient function for constructs like in_array('val1', $a) || in_array('val2, $a) etc.
     *
     * @param array|string $anyValue Array of needles (will try any for a match)
     * @param array        $array    Haystack array
     * @return bool
     */
    public static function anyIn($anyValue, $array)
    {
        if (is_array($anyValue)) {
            return (bool) array_intersect($anyValue, $array);
        }

        return in_array($anyValue, $array);
    }

    /**
     * If all of the provided values is in an array.
     * This is a convenient function for constructs like in_array('val1', $a) && in_array('val2, $a) etc.
     *
     * @param array|mixed $allValues Array of needles (will try all for a match)
     * @param array       $array     Haystack array
     * @return bool
     */
    public static function allIn($allValues, $array)
    {
        if (is_array($allValues)) {
            if (empty($allValues)) {
                return false;
            }

            foreach ($allValues as $value) {
                if (!in_array($value, $array)) {
                    return false;
                }
            }

            // A match was found for all values if we got here
            return true;
        }

        return in_array($allValues, $array);
    }

    /**
     * Get a new array with prefix applied to all values.
     *
     * @param array $array
     * @param bool  $prefix
     * @return array
     */
    public static function valuesWithPrefix(array $array, $prefix)
    {
        $newArray = [];
        foreach ($array as $oldValue) {
            $newArray[] = $prefix . $oldValue;
        }


        return $newArray;
    }

    /**
     * Checks if all values in an array is empty (recursively).
     *
     * Doesn't consider other arrays with empty values non-empty as the normal
     * empty() function does.
     *
     * @param array|mixed $input
     * @return bool
     */
    public static function allEmpty($input)
    {
        if (is_array($input)) {
            foreach ($input as $value) {
                if (!self::allEmpty($value)) {
                    return false;
                }
            }

            return true;
        } else {
            return empty($input);
        }
    }

    /**
     * Filter associative array on keys, by provided keys, a callable or null (like  array_filter, but for keys).
     *
     * @param array               $inputArray
     * @param array|callable|null $keysOrCallable
     * @return array
     */
    public static function filterKeys(array $inputArray, $keysOrCallable = null)
    {
        // Get keys
        if (is_array($keysOrCallable)) {
            $filteredKeys = $keysOrCallable;
        } elseif (is_callable($keysOrCallable)) {
            $filteredKeys = array_filter(array_keys($inputArray), $keysOrCallable);
        } else {
            $filteredKeys = array_filter(array_keys($inputArray));
        }

        // Return associative array with only the the filtered keys
        return array_intersect_key($inputArray, array_flip($filteredKeys));
    }

    /**
     * collect values from method calls from an array of objects.
     *
     * Ee.g. get all product names from an array of products.
     *
     * @param array  $objectArray
     * @param string $methodName
     * @return array
     */
    public static function objectsMethodValues(array $objectArray, $methodName)
    {
        $methodValues = [];

        foreach ($objectArray as $object) {
            $methodValues[] = $object->$methodName();
        }

        return $methodValues;
    }

    /**
     * Get a new array with all values cast to type.
     *
     * @param array  $inputArray
     * @param string $type
     * @return array
     */
    public static function valuesWithType(array $inputArray, $type)
    {
        $newArray = [];
        foreach ($inputArray as $key => $value) {
            $newValue = $value;
            settype($newValue, $type);
            $newArray[$key] = $newValue;
        }

        return $newArray;
    }

    /**
     * Replaces values in array1 with values from array2 comparing keys and
     * discarding keys that doesn't exist in array1 .
     *
     * @param array $array1
     * @param array $array2
     * @param bool  $recursive
     * @return array
     */
    public static function replaceExisting(array $array1, array $array2, $recursive = false)
    {
        foreach ($array1 as $key => $value) {
            if (array_key_exists($key, $array2)) {
                $value2 = $array2[$key];
                if ($recursive && is_array($value)) {
                    $value2 = self::replaceExisting($value, $value2, $recursive);
                }
                $array1[$key] = $value2;
            }
        }

        return $array1;
    }

    /**
     * Sort by array.
     *
     * @param array $sortArray
     * @param array $mapArray
     * @return array
     */
    public static function sortByArray(array $sortArray, array $mapArray)
    {
        $sortedArray = [];
        foreach ((array) $mapArray as $id) {
            if (array_key_exists($id, $sortArray)) {
                $sortedArray[] = $sortArray[$id];
            }
        }

        return $sortedArray;
    }

    /**
     * @param array  $array
     * @param string $key
     * @param null   $default
     * @return array
     */
    public static function getValue(array $array, $key, $default = null)
    {
        return isset($array[$key]) ? $array[$key] : $default;
    }
}
