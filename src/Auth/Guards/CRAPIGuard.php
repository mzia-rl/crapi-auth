<?php

namespace Canzell\Auth\Guards;

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\UserProvider;

class CRAPIGuard implements Guard
{

    private $user = null;
    protected $provider;

    public function __construct($provider, $token)
    {
        $this->provider = $provider;
        $this->validate([
            'access_token' => $token
        ]);
    }

    public function check()
    {
        return $this->user !== null;
    }

    public function guest()
    {
        return false;
    }

    public function user()
    {
        return $this->user;
    }

    public function id()
    {
        if ($this->user) return $this->user->id;
        else return null;
    }

    public function validate($credentials = [])
    {
        $user = $this->provider->retrieveByCredentials($credentials);
        $valid = $this->provider->validateCredentials($user, $credentials);
        if ($user && $valid) {
            $this->setUser($user);
            return true;
        } else return false;
    }

    public function setUser($user)
    {
        $this->user = $user;
    }

}