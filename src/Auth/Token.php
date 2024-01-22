<?php

namespace Canzell\Auth;

use Firebase\JWT\JWT;
use Canzell\Facades\CRAPI;

class Token
{

    private array $header;
    private array $payload;
    private string $key;

    public function __construct()
    {
        $this->header = [];
        $this->payload = [];
    }

    public function setKey(string $rsaKey): void
    {
        $this->key = $rsaKey;
    }

    public function setPayload(array $payload): void
    {
        $this->payload = $payload;
    }

    public function setHeader(array $header): void
    {
        $this->header = $header;
    }

    public function setHeaderClaim(string $name, mixed $value): void
    {
        $this->header[$name] = $value;
    }

    public function setClaim(string $name, mixed $value): void
    {
        $this->payload[$name] = $value;
    }

    public function __toString(): string
    {
        return $this->toString();
    }

    public function toJson(): string
    {
        return json_encode($this->payload);
    }

    public function toArray(): array
    {
        return $this->payload;
    }

    static public function decode(string $jwt, int|string|null $key = null): self
    {
        $token = self::blank();
        preg_match('/(.*)\.(.*)\.(.*)/', $jwt, $matches);
        $header = $token->base64UrlDecode($matches[1]);

        if (is_int($key)) { // If int, then assume its a CRAPI key ID
            $kid = $key;
            $key = $token->fetchKey($kid);
        } else if (is_null($key)) {
            $kid = empty($header->kid) ? null : $header->kid;
            $key = $token->fetchKey($kid);
        }

        $token->setKey($key);
        $token->setPayload(JWT::decode($jwt, $key, ['RS256']));
        $token->setHeader($header);
        return $token;
    }

    static public function blank(): self
    {
        return new self;
    }

    protected function encode(): string
    {
        return JWT::encode($this->payload, $this->key, 'RS256', null, $this->header);
    }

    public function toString(): string
    {
        return $this->encode();
    }

    protected function fetchKey(int $kid): string
    {
        if (env('APP_NAME') == 'crapi-auth-service') return (string) resolve(\App\Auth\KeySet::class)->get($kid)->public;
        else return (string) CRAPI::get("auth/keys/$kid");
    }

    private function base64UrlDecode(string $encoded)
    {
        $decoded = strtr($encoded, '_-', '/+');
        $decoded = base64_decode($decoded);
        $decoded = json_decode($decoded);
        return $decoded;
    }

}