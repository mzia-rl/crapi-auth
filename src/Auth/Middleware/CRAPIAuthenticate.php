<?php

namespace Canzell\Auth\Middleware;

use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Auth\AuthenticationException;

class CRAPIAuthenticate extends Middleware
{

    protected function unauthenticated($request, array $guards)
    {
        throw new HttpException(401, 'Unauthenticated');
    }
    
}
