@php($isAdmin = auth()->user()->hasRole(['admin','super-admin']))

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Dashboard
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            @if (session('status'))
                <div class="bg-green-100 text-green-800 p-3 rounded">{{ session('status') }}</div>
            @endif
            @if (session('error'))
                <div class="bg-red-100 text-red-800 p-3 rounded">{{ session('error') }}</div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="font-semibold text-lg mb-4">Upcoming Sessions</h3>
                    <div class="divide-y">
                        @forelse(($upcomingSessions ?? []) as $s)
                            <div class="py-3 flex items-center justify-between">
                                <div>
                                    <div class="font-medium">{{ $s->title }}</div>
                                    <div class="text-sm text-gray-600">{{ $s->scheduled_at->format('M d, Y H:i') }} ({{ $s->duration_minutes }}m) @ {{ $s->location }}</div>
                                </div>
                                @if ($isAdmin)
                                    <div class="flex gap-2">
                                        <a class="text-blue-600" href="{{ route('sessions.show', $s) }}">View</a>
                                        <a class="text-indigo-600" href="{{ route('sessions.qr', $s) }}">Show QR</a>
                                    </div>
                                @endif
                            </div>
                        @empty
                            <div class="py-4 text-gray-600">No sessions scheduled.</div>
                        @endforelse
                    </div>
                </div>
            </div>

            @unless($isAdmin)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <h3 class="font-semibold text-lg mb-4">My Recent Attendance</h3>
                        <div class="divide-y">
                            @forelse(($myAttendance ?? []) as $a)
                                <div class="py-3">
                                    <div class="font-medium">{{ $a->session->title ?? 'Session' }}</div>
                                    <div class="text-sm text-gray-600">Checked in: {{ optional($a->checked_in_at)->format('M d, Y H:i') }}</div>
                                </div>
                            @empty
                                <div class="py-4 text-gray-600">No attendance yet.</div>
                            @endforelse
                        </div>
                    </div>
                </div>
            @endunless
        </div>
    </div>
</x-app-layout>