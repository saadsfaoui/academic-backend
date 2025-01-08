<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    protected $except = [
        // Ajoutez ici les URLs que vous souhaitez exclure de la vérification CSRF.
    ];
}

