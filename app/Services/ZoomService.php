<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class ZoomService
{
    public function createMeeting($title, $startTime, $durationMinutes = 120)
    {
        // 1. Authenticate with Zoom to get a temporary access token
        $tokenResponse = Http::withBasicAuth(env('ZOOM_CLIENT_ID'), env('ZOOM_CLIENT_SECRET'))
            ->asForm()
            ->post('https://zoom.us/oauth/token', [
                'grant_type' => 'account_credentials',
                'account_id' => env('ZOOM_ACCOUNT_ID'),
            ]);

        if ($tokenResponse->failed()) {
            throw new \Exception('Failed to connect to Zoom API. Please check your credentials.');
        }

        $token = $tokenResponse->json('access_token');

        // 2. Instruct Zoom to create the scheduled meeting
        $meetingResponse = Http::withToken($token)
            ->post('https://api.zoom.us/v2/users/me/meetings', [
                'topic'      => $title,
                'type'       => 2, // 2 = Scheduled Meeting
                'start_time' => Carbon::parse($startTime)->toIso8601ZuluString(),
                'duration'   => $durationMinutes,
                'timezone'   => 'Asia/Colombo', // Locks time to Sri Lanka
                'settings'   => [
                    'waiting_room'     => true,
                    'mute_upon_entry'  => true,
                    'join_before_host' => false,
                ]
            ]);

        return $meetingResponse->json();
    }
}
