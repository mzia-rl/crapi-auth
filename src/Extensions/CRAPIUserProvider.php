<?php

namespace Canzell\Extensions;

use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Contracts\Auth\Authenticatable;
use Canzell\Auth\User;

use Firebase\JWT\JWT;

use Canzell\Facades\CRAPI;

class CRAPIUserProvider implements UserProvider
{

    private $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function retrieveByCredentials(array $credentials) {
        $token = $credentials['access_token'];
        $payload = explode('.', $token)[1];
        $payload = strtr($payload, '-_', '+/');
        $payload = base64_decode($payload);
        $payload = json_decode($payload);
        $id = $payload->sub ?? $payload->id;
        return $this->fetchResource($id);
    }

    public function validateCredentials(Authenticatable $user, Array $credentials) {
        $token = $credentials['access_token'];
        $parts = explode('.', $token);
        $header = $parts[0];
        $header = strtr($header, '-_', '+/');
        $header = base64_decode($header);
        $header = json_decode($header);
        $key = file_get_contents(config('crapi-auth.public_key_url'));

        try {
            JWT::decode($token, $key, ['RS256']);
            return true;
        } catch (\Exception $e) {
            return false;
        }

    }

    public function retrieveByToken($id, $token) { }

    public function updateRememberToken(Authenticatable $user, $token) { }

    public function retrieveById($id)
    {
        return $this->fetchResource($id);
    }

    protected function fetchResource($id)
    {
        $type = substr($id, 0, 3);
        switch ($type) {
            case 'usr':
                $fields = $this->config['fields'] ?? [];
                $query = ['$select' => implode(',', $fields)];
                $user = CRAPI::get("users/$id", compact('query'))->data;
                break;
            case 'cli':
                if (env('APP_NAME') == 'crapi-auth-service') $user = \App\Models\Client::find($id)->toArray();
                else $user = CRAPI::get("auth/clients/$id");
                break;
            default:
                throw new \Exception("Resource type ($type) can't be used for authentication!");
        }
        return new User((array) $user);
    }

}