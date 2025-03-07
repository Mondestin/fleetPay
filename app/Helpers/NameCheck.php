<?php

namespace App\Helpers;

use Illuminate\Support\Str;

class NameCheck
{
    /**
     * Check if the input name matches the given full name.
     *
     * The logic splits the input name into parts (by space), converts everything to lowercase,
     * and then checks if at least two parts of the input name are present in the full name.
     *
     * @param string $inputName
     * @param string $fullName
     * @return bool
     */
    public static function matchName(string $inputName, string $fullName): bool
    {
        // Convert names to lowercase
        $inputNameParts = explode(' ', Str::lower($inputName));
        $fullNameLower = Str::lower($fullName);

        // Count how many parts of the input name exist in the full name
        $matchCount = 0;
        foreach ($inputNameParts as $part) {
            if (strpos($fullNameLower, $part) !== false) {
                $matchCount++;
            }
        }

        return $matchCount >= 2;
    }
}
