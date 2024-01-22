<?php

namespace Canzell\Aws\Sns;

use Aws\Sns\SnsClient as AwsSnsClient;
use Canzell\Aws\Sns\Events\NotificationPublished;

class Client
{
    public $config;
    public  $client;

    public function __construct($config)
    {
        $this->config = $config;
        $this->connect();
    }

    private function connect()
    {
        $this->client = new AwsSnsClient([
            'credentials' => [
                'key'    => $this->config['access_key_id'],
                'secret' => $this->config['secret_access_key'],
            ],
            'region' => 'us-east-1',
            'version' => 'latest'
        ]);
    }

    function publish($topic, $event, $details)
    {
        $message = json_encode(compact('event', 'details'));
        return $this->send($topic, $message);
    }

    function send($topic, $message)
    {
        $data = [
            'TopicArn' => $topic,
            'Message' => $message,
        ];

        $result = $this->client->publish($data);
        $messageId = $result->get('MessageId');
        NotificationPublished::dispatch($messageId, $data);
        
        return $result;
    }

    function text($number, $message)
    {
        $result = $this->client->publish([
            'PhoneNumber' => $number,
            'Message' => $message,
        ]);
        return $result;
    }
}
