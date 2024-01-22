<?php

namespace Canzell\Aws\Sns\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Aws\Sns\Message;

class NotificationRecieved
{
    use Dispatchable, SerializesModels, InteractsWithSockets;

    public $message;
    public $payload;
    public $event;
    public $details;

    public function __construct(Message $message)
    {
        $this->message = $message;
        $this->payload = json_decode($message->toArray()['Message']);
        $this->event = $this->payload->event ?? null;
        $this->details = $this->payload->details ?? null;
    }

}