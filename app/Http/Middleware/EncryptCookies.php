<?php

namespace App\Http\Middleware;

use Illuminate\Cookie\Middleware\EncryptCookies as Middleware;

class EncryptCookies extends Middleware
{
    protected $except = [
        // Ajoutez ici les cookies que vous ne souhaitez pas chiffrer.
    ];
}

