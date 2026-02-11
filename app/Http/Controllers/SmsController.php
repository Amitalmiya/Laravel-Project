<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\TwilioService;

class SmsController extends Controller
{
    protected $twilioService;

    public function __construct(TwilioService $twilioService)
    {
        $this->twilioService = $twilioService;
    }

    public function send(Request $request)
    {
        try {
            $this->twilioService->sendSMS('+917500895450', 'Hello from Laravel Twilio.');

            return response()->json([
                'message' => 'SMS Sent Successfully.'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to send SMS.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
} 