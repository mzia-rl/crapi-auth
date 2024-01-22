<?php

return [

    'fetch_user' => env('CRAPI_AUTH_FETCH_USER', false),
    'verify_token' => env('CRAPI_AUTH_VERIFY_TOKEN', false),
    'public_key_url' => env('CRAPI_AUTH_PUBLIC_KEY_URL', 'https://cr-identities.s3.amazonaws.com/canzell-auth-service.pub')

];