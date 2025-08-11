<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\TutoringSession;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __invoke(): View|RedirectResponse
    {
        $user = Auth::user();

        if ($user->hasRole(['super-admin', 'admin'])) {
            $upcoming = TutoringSession::with('tutor')
                ->where('scheduled_at', '>=', now()->subDay())
                ->orderBy('scheduled_at')
                ->take(10)
                ->get();

            return view('dashboard', [
                'upcomingSessions' => $upcoming,
            ]);
        }

        $myUpcoming = TutoringSession::where('scheduled_at', '>=', now()->subDay())
            ->orderBy('scheduled_at')
            ->take(10)
            ->get();

        $myAttendance = Attendance::with('session')
            ->where('user_id', $user->id)
            ->latest('checked_in_at')
            ->take(10)
            ->get();

        return view('dashboard', [
            'upcomingSessions' => $myUpcoming,
            'myAttendance' => $myAttendance,
        ]);
    }
}