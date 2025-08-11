<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Attendance</h2>
            <a href="{{ route('attendance.export', $session) }}" class="px-3 py-2 bg-indigo-600 text-white rounded">Export CSV</a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="divide-y">
                        @foreach($attendances as $a)
                            <div class="py-3 flex items-center justify-between">
                                <div>
                                    <div class="font-medium">{{ $a->user->name ?? 'Unknown' }}</div>
                                    <div class="text-sm text-gray-600">{{ $a->user->email ?? '' }}</div>
                                </div>
                                <div class="text-sm text-gray-700">Checked in: {{ optional($a->checked_in_at)->format('M d, Y H:i') }}</div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-4">{{ $attendances->links() }}</div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>