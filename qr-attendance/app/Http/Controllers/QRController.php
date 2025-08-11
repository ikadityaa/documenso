<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\TutoringSession;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QRController extends Controller
{
    private const TOKEN_TTL_MINUTES = 5;

    public function show(TutoringSession $session): View
    {
        $this->authorizeForUser(Auth::user(), 'view', $session);

        if (!$session->isQrActive()) {
            $this->regenerateToken($session);
        }

        $scanUrl = route('qr.scan', [
            'session' => $session->id,
            'token' => $this->getPlainTokenForDisplay($session),
        ]);

        $qrSvg = QrCode::format('svg')->size(260)->generate($scanUrl);

        return view('qr.display', [
            'session' => $session,
            'scanUrl' => $scanUrl,
            'qrSvg' => $qrSvg,
            'expiresAt' => $session->active_qr_expires_at,
        ]);
    }

    public function regenerate(TutoringSession $session): RedirectResponse
    {
        $this->authorizeForUser(Auth::user(), 'view', $session);
        $this->regenerateToken($session);
        return back()->with('status', 'QR refreshed');
    }

    public function scan(Request $request): RedirectResponse
    {
        $request->validate([
            'session' => ['required','exists:tutoring_sessions,id'],
            'token' => ['required','string'],
        ]);

        if (!Auth::check()) {
            return redirect()->guest(route('login'))->with('status', 'Please log in to check in.');
        }

        $user = Auth::user();
        $session = TutoringSession::findOrFail($request->integer('session'));

        if (!$session->isQrActive()) {
            return redirect()->route('dashboard')->with('error', 'QR expired.');
        }

        $providedToken = $request->string('token');
        $hashed = $this->hashToken($providedToken);

        if (!hash_equals((string) $session->active_qr_token_hash, $hashed)) {
            return redirect()->route('dashboard')->with('error', 'Invalid QR token.');
        }

        // Record attendance (idempotent per session/user)
        $attendance = Attendance::firstOrCreate(
            [
                'session_id' => $session->id,
                'user_id' => $user->id,
            ],
            [
                'checked_in_at' => now(),
            ]
        );

        if (!$attendance->checked_in_at) {
            $attendance->checked_in_at = now();
            $attendance->save();
        }

        return redirect()->route('dashboard')->with('status', 'Checked in successfully.');
    }

    private function regenerateToken(TutoringSession $session): void
    {
        $plain = Str::random(40);
        $session->active_qr_token_hash = $this->hashToken($plain);
        $session->active_qr_expires_at = now()->addMinutes(self::TOKEN_TTL_MINUTES);
        // Temporarily stash the plain token for the request so we can show it in QR without storing it
        $session->setAttribute('_plain_qr_token', $plain);
        $session->save();
    }

    private function getPlainTokenForDisplay(TutoringSession $session): string
    {
        $plain = $session->getAttribute('_plain_qr_token');
        if (!$plain) {
            // generate a throwaway token that matches current hash? Not possible; regenerate to keep flow simple
            $this->regenerateToken($session);
            $plain = $session->getAttribute('_plain_qr_token');
        }
        return $plain;
    }

    private function hashToken(string $plain): string
    {
        $key = config('app.key', 'base64:');
        return hash('sha256', $plain.'|'.$key);
    }
}