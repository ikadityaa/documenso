<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Sessions</h2>
            <a href="{{ route('sessions.create') }}" class="px-3 py-2 bg-indigo-600 text-white rounded">New Session</a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="divide-y">
                        @foreach($sessions as $s)
                            <div class="py-3 flex items-center justify-between">
                                <div>
                                    <div class="font-medium">{{ $s->title }}</div>
                                    <div class="text-sm text-gray-600">By {{ $s->tutor->name ?? 'N/A' }} | {{ $s->scheduled_at->format('M d, Y H:i') }} | {{ $s->location }}</div>
                                </div>
                                <div class="flex gap-3">
                                    <a class="text-blue-600" href="{{ route('sessions.show', $s) }}">View</a>
                                    <a class="text-green-600" href="{{ route('sessions.edit', $s) }}">Edit</a>
                                    <form method="POST" action="{{ route('sessions.destroy', $s) }}" onsubmit="return confirm('Delete this session?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="text-red-600">Delete</button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-4">{{ $sessions->links() }}</div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>