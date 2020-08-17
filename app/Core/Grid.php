<?php

namespace App\Core;

use Illuminate\Contracts\Support\Renderable;

class Grid implements Renderable
{
    public function __construct()
    {
    
    }


    public static function make(...$params)
    {
        return new static(...$params);
    }



    public function render()
    {

    }

}
