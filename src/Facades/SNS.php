<?php

namespace Canzell\Aws\Sns\Facades;

use Illuminate\Support\Facades\Facade;
use Canzell\Aws\Sns\Client;

class SNS extends Facade
{

    static public function getFacadeAccessor()
    {
        return Client::class;
    }

}