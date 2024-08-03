<?php

namespace App\Http\Helpers;


use Illuminate\Support\Str;

class ProductHelper
{
    /**
     * Generete product code
     * @return string
     */
    public static function generateProductCode()
    {
        return Str::upper(Str::random(10));

    }


}
