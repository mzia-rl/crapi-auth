<?php

namespace Canzell\Aws\Sns\Middleware;

use Aws\Sns\Exception\InvalidSnsMessageException;
use Aws\Sns\Message;
use Aws\Sns\MessageValidator;
use Illuminate\Support\Facades\Log;
use Closure;

class ValidateNotification
{

    public function handle($request, Closure $next)
    {
        try {
            // Validate signature of message
            $message = Message::fromRawPostData();
            $validator = new MessageValidator();
            $validator->validate($message);

            // Verify this app allows notifications from this ARN
            $subscriptions = config('aws.sns.subscriptions');
            $topicArn = $message->toArray()['TopicArn'];
            $allowedSubscription = in_array($topicArn, $subscriptions);
            if (!$allowedSubscription) throw new InvalidSnsMessageException('Topic ARN not listed in config.aws.sns.subscriptions');
        } catch (\Exception|\Error $e) {
            Log::debug("Exception thrown validating an SNS notification! \n$e");
            abort(401, 'Not a valid SNS notification');
        }

        return $next($request);
    }
}