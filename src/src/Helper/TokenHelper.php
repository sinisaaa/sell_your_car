<?php

declare(strict_types=1);

namespace App\Helper;

final class TokenHelper
{

    /**
     * @return string
     * @throws \Exception
     */
    public static function generateToken(): string
    {
        $length = 64;
        $generatedString = '';
        $characters = array_merge(range('A', 'Z'), range('a', 'z'), range('0', '9'));
        $max = count($characters) - 1;
        for ($i = 0; $i < $length; $i++) {
            $rand = random_int(0, $max);
            $generatedString .= $characters[$rand];
        }

        return $generatedString;
    }

}