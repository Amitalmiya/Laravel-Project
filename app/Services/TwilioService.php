<?php

namespace App\Services;

use Twilio\Rest\Client;
use Twilio\Exceptions\TwilioException;
use Illuminate\Support\Facades\Log;

class TwilioService
{
    protected $client;
    protected $from;

    public function __construct()
    {
        $this->client = new Client(
            config('services.twilio.sid'),
            config('services.twilio.auth_token')
        );
        $this->from = config('services.twilio.phone');
    }

    public function sendSMS($to, $message)
    {
        try {
            return $this->client->messages->create($to, [
                'from' => $this->from,
                'body' => $message
            ]);
        } catch (TwilioException $e) {
            Log::error('Twilio SMS Error: ' . $e->getMessage());
            throw $e;
        }
    }
}