<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class GoogleCalendarController extends Controller
{
    public function connect()
    {
        $client = $this->buildClient();
        $client->setAccessType('offline');
        $client->setPrompt('consent');
        $client->setScopes([\Google\Service\Calendar::CALENDAR_EVENTS]);

        return redirect()->away($client->createAuthUrl());
    }

    public function callback(Request $request)
    {
        if ($request->has('error')) {
            return redirect()->route('budget.settings')
                ->with('error', 'Koneksi Google Calendar dibatalkan.');
        }

        $code = $request->get('code');
        if (!$code) {
            return redirect()->route('budget.settings')
                ->with('error', 'Kode otorisasi Google tidak ditemukan.');
        }

        try {
            $client = $this->buildClient();
            $token = $client->fetchAccessTokenWithAuthCode($code);

            if (isset($token['error'])) {
                throw new \RuntimeException($token['error_description'] ?? $token['error']);
            }

            $user = Auth::user();
            $user->google_calendar_token = json_encode($token);
            $user->save();

            return redirect()->route('budget.settings')
                ->with('success', 'Google Calendar berhasil disambungkan!');
        } catch (\Throwable $e) {
            Log::error('Google Calendar callback gagal: ' . $e->getMessage());
            return redirect()->route('budget.settings')
                ->with('error', 'Gagal menyambungkan Google Calendar. Coba lagi.');
        }
    }

    public function disconnect()
    {
        $user = Auth::user();
        $user->google_calendar_token = null;
        $user->save();

        return redirect()->route('budget.settings')
            ->with('success', 'Koneksi Google Calendar diputuskan.');
    }

    private function buildClient(): \Google\Client
    {
        $client = new \Google\Client();
        $client->setClientId(config('services.google.client_id'));
        $client->setClientSecret(config('services.google.client_secret'));
        $client->setRedirectUri(config('services.google.redirect'));

        return $client;
    }
}
