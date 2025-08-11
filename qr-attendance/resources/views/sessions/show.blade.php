<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Session Details</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-4">
                        <div class="text-2xl font-semibold">{{ $session->title }}</div>
                        <div class="text-gray-600">By {{ $session->tutor->name ?? 'N/A' }}</div>
                        <div class="text-gray-600">{{ $session->scheduled_at->format('M d, Y H:i') }} ({{ $session->duration_minutes }}m) @ {{ $session->location }}</div>
                    </div>
                    <div class="prose max-w-none">{!! nl2br(e($session->description)) !!}</div>

                    <div class="mt-6 flex gap-3">
                        <a class="px-3 py-2 bg-indigo-600 text-white rounded" href="{{ route('sessions.qr', $session) }}">Show QR</a>
                        <a class="px-3 py-2 bg-gray-200 rounded" href="{{ route('attendance.index', $session) }}">View Attendance</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>