<?php

namespace Canzell\Aws\Sns\Middleware;

use Aws\Sns\Message;
use Canzell\Aws\Sns\DomainMessage;
use Illuminate\Support\Facades\Log;
use Closure;
use Error;
use Exception;
use Illuminate\Support\Facades\Http;
use Throwable;

class ConfirmSubscription
{

    public function handle($request, Closure $next)
    {
        try {
            $message = Message::fromRawPostData()->toArray();
            
            // Check if applicable
            $isSubscriptionConfirmation = $message['Type'] != 'SubscriptionConfirmation';
            if ($isSubscriptionConfirmation) return $next($request);

            // Check if allowable
            $isWhitelisted = in_array($message['TopicArn'], config('aws.sns.subscriptions'));
            abort_unless($isWhitelisted, 403, 'This Topic ARN is not whitelisted for subscription.');

            // Auto confirm subscription
            Http::get($message['SubscribeURL']);
            Log::info('A new subscription confirmation has come in and automatically confirmed!', $message);
            return response()->json(null, 200);
        } catch (Throwable|Error|Exception $e) {
            Log::debug("Error notifying of potential SNS confirmation! \n$e");
            abort(400, 'Not a valid SNS notification');
        }
    }
}