<?php

namespace Canzell\Aws\Sns\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NotificationPublished
{
    use Dispatchable, SerializesModels, InteractsWithSockets;

    public $payload;
    public $event;
    public $details;

    public function __construct(public string $messageId, public array $messageBody)
    {
        $this->payload = json_decode($messageBody['Message']);
        $this->event = $this->payload->event ?? null;
        $this->details = $this->payload->details ?? null;
    }

}