<?php

// SNS Subscription Route Registration

use Aws\Sns\Message;
use Canzell\Aws\Sns\Events\NotificationRecieved;
use Illuminate\Support\Facades\Route;

$subscriptions = config('aws.sns.subscriptions');
if (empty($subscriptions)) return;

Route::middleware('subscriptions')->group(function () use ($subscriptions) {

    foreach ($subscriptions as $subscription) Route::post(
        '__subscriptions__/'.env('APP_NAME').'/'.$subscription,
        function () {
            $message = Message::fromRawPostData();
            NotificationRecieved::dispatch($message);
        }
    );

});