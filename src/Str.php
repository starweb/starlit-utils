<?php
/**
 * Utils.
 *
 * @copyright Copyright (c) 2016 Starweb AB
 * @license   BSD 3-Clause
 */

namespace Starlit\Utils;

/**
 * String utilities.
 *
 * @author Andreas Nilsson <http://github.com/jandreasn>
 */
class Str
{
    /**
     * CamelCases words or a dash name  (eg. "hey-you" or "hey you" becomes "heyYou").
     * NB! This method is only for property formatted separated none
     * multibyte (UTF-8) ASCII strings. It doesn't handle malformed strings with
     * a lot of other weird characters.
     *
     * @param string $separatorString
     * @param string $separator
     * @param bool   $upperCamelCase If first character should be capitalized (eg. "HeyYou" instead of "heyYou")
     * @return string
     */
    public static function separatorToCamel($separatorString, $separator = '_', $upperCamelCase = false)
    {
        // Using PHP's built in functions seem to be a bit 30% faster than a regular expression replace
        $wordStr = str_replace($separator, ' ', strtolower($separatorString));
        $wordsCapitalized = ucwords($wordStr);
        $camelCased = str_replace(' ', '', $wordsCapitalized);

        return $upperCamelCase ? $camelCased : lcfirst($camelCased);
    }

    /**
     * Converts CamelCase text to lowercase separator separated text (eg. "heyYou" becomes "hey-you").
     * NB! This method is only for none multibyte (UTF-8) ASCII strings.
     *
     * @param string $camelCaseString
     * @param string $separator
     * @return string
     */
    public static function camelToSeparator($camelCaseString, $separator = '_')
    {
        $separatedString = preg_replace('/([a-zA-Z])([A-Z])/', '$1' . $separator . '$2', $camelCaseString);
        $separatedString = preg_replace('/([A-Z0-9])([A-Z])/', '$1' . $separator . '$2', $separatedString);

        return strtolower($separatedString);
    }

    /**
     * Get a random string generated from provided values.
     *
     * @param int    $charCount
     * @param string $characters
     * @return string
     */
    public static function random($charCount, $characters = 'abcdefghijklmnopqrstuvqxyz0123456789')
    {
        $randomString = '';
        for ($i = 0; $i < $charCount; $i++) {
            $pos = mt_rand(0, strlen($characters) - 1);
            $randomString .= $characters[$pos];
        }

        return $randomString;
    }

    /**
     * Get shortened string.
     *
     * @param string $string
     * @param int    $maxLength
     * @param string $indicator
     * @return string
     */
    public static function truncate($string, $maxLength, $indicator = '...')
    {
        if (mb_strlen($string) > $maxLength) {
            $string = mb_substr($string, 0, $maxLength - mb_strlen($indicator)) . $indicator;
        }

        return $string;
    }

    /**
     * Check if a string start with another substring.
     *
     * @param string $string
     * @param string $search
     * @return bool
     */
    public static function startsWith($string, $search)
    {
        return strpos($string, $search) === 0;
    }

    /**
     * Check if a string ends with another substring.
     *
     * @param string $string
     * @param string $search
     * @return bool
     */
    public static function endsWith($string, $search)
    {
        return (substr($string, -strlen($search)) === $search);
    }

    /**
     * Remove left part of string and return the new string.
     *
     * @param string $string
     * @param string $strip
     * @return string
     */
    public static function stripLeft($string, $strip)
    {
        if (self::startsWith($string, $strip)) {
            return substr($string, strlen($strip));
        }

        return $string;
    }

    /**
     * Remove right part of string and return the new string.
     *
     * @param string $string
     * @param string $strip
     * @return string
     */
    public static function stripRight($string, $strip)
    {
        if (self::endsWith($string, $strip)) {
            return substr($string, 0, -strlen($strip));
        }

        return $string;
    }

    /**
     * Increment a string/separated string.
     * Example: "a-name" becomes "a-name-2", "a-name-3" and so on.
     *
     * @param string $string
     * @param string $separator
     * @return string
     */
    public static function incrementSeparated($string, $separator = '-')
    {
        if (preg_match('/' . $separator . '(\d+)$/', $string, $matches)) {
            $string = self::stripRight($string, $separator . $matches[1]);
            $duplicateNo = ((int) $matches[1]) + 1;
        } else {
            $duplicateNo = 2;
        }

        $newString = $string . $separator . $duplicateNo;

        return $newString;
    }

    /**
     * @param string $string
     * @return string
     */
    public static function uppercaseFirst($string)
    {
        $length = mb_strlen($string);
        $firstChar = mb_substr($string, 0, 1);
        $otherChars = mb_substr($string, 1, $length - 1);

        return mb_strtoupper($firstChar) . $otherChars;
    }

    /**
     * Filter out all non numeric characters and return a clean numeric string.
     *
     * @param string $string
     * @param bool   $allowDecimal
     * @param bool   $allowNegative
     * @return string
     */
    public static function toNumber($string, $allowDecimal = false, $allowNegative = false)
    {
        $string = trim($string);

        $decimalPart = '';
        if (($firstPos = strpos($string, '.')) !== false) {
            $integerPart = substr($string, 0, $firstPos);
            if ($allowDecimal) {
                $rawDecimalPart = substr($string, $firstPos);
                $filteredDecimalPart = rtrim(preg_replace('/[^0-9]/', '', $rawDecimalPart), '0');
                if (!empty($filteredDecimalPart)) {
                    $decimalPart = '.' . $filteredDecimalPart;
                }
            }
        } else {
            $integerPart = $string;
        }

        $integerPart = ltrim(preg_replace('/[^0-9]/', '', $integerPart), '0');
        $integerPart = $integerPart ?: '0';

        $minusSign = '';
        if (strpos($string, '-') === 0) {
            if (!$allowNegative) {
                return '0';
            } elseif (!($integerPart === '0' && empty($decimalPart))) {
                $minusSign = '-';
            }
        }

        $number = $minusSign . $integerPart . $decimalPart;

        return $number;
    }

    /**
     * @param string $search
     * @param string $replace
     * @param string $subject
     * @return string
     */
    public static function replaceFirst($search, $replace, $subject)
    {
        if (($pos = strpos($subject, $search)) !== false) {
            return substr_replace($subject, $replace, $pos, strlen($search));
        }

        return $subject;
    }
}
