<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\TutoringSession;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AttendanceController extends Controller
{
    public function index(TutoringSession $session): View
    {
        $attendances = Attendance::with('user')
            ->where('session_id', $session->id)
            ->latest('checked_in_at')
            ->paginate(25);

        return view('attendance.index', compact('session', 'attendances'));
    }

    public function exportCsv(TutoringSession $session): StreamedResponse
    {
        $filename = 'attendance_session_'.$session->id.'.csv';

        $callback = function () use ($session) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Student Name', 'Email', 'Checked In At', 'Checked Out At']);

            Attendance::with('user')
                ->where('session_id', $session->id)
                ->orderBy('checked_in_at')
                ->chunk(200, function ($chunk) use ($handle) {
                    foreach ($chunk as $attendance) {
                        fputcsv($handle, [
                            optional($attendance->user)->name,
                            optional($attendance->user)->email,
                            optional($attendance->checked_in_at)?->toDateTimeString(),
                            optional($attendance->checked_out_at)?->toDateTimeString(),
                        ]);
                    }
                });

            fclose($handle);
        };

        return response()->streamDownload($callback, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }
}