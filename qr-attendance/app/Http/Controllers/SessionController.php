<?php

namespace App\Http\Controllers;

use App\Models\TutoringSession;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SessionController extends Controller
{
    public function index(): View
    {
        $sessions = TutoringSession::with('tutor')->latest('scheduled_at')->paginate(15);
        return view('sessions.index', compact('sessions'));
    }

    public function create(): View
    {
        $tutors = \App\Models\User::role(['admin','super-admin'])->get();
        return view('sessions.create', compact('tutors'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required','string','max:255'],
            'description' => ['nullable','string'],
            'tutor_id' => ['required','exists:users,id'],
            'location' => ['nullable','string','max:255'],
            'scheduled_at' => ['required','date'],
            'duration_minutes' => ['required','integer','min:5','max:600'],
        ]);

        TutoringSession::create($data);

        return redirect()->route('sessions.index')->with('status', 'Session created');
    }

    public function show(TutoringSession $session): View
    {
        $session->load('tutor');
        return view('sessions.show', compact('session'));
    }

    public function edit(TutoringSession $session): View
    {
        $tutors = \App\Models\User::role(['admin','super-admin'])->get();
        return view('sessions.edit', compact('session','tutors'));
    }

    public function update(Request $request, TutoringSession $session): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required','string','max:255'],
            'description' => ['nullable','string'],
            'tutor_id' => ['required','exists:users,id'],
            'location' => ['nullable','string','max:255'],
            'scheduled_at' => ['required','date'],
            'duration_minutes' => ['required','integer','min:5','max:600'],
        ]);

        $session->update($data);

        return redirect()->route('sessions.index')->with('status', 'Session updated');
    }

    public function destroy(TutoringSession $session): RedirectResponse
    {
        $session->delete();
        return redirect()->route('sessions.index')->with('status', 'Session deleted');
    }
}