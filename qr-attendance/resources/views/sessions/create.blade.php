<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">New Session</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('sessions.store') }}" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-sm font-medium">Title</label>
                            <input name="title" value="{{ old('title') }}" class="mt-1 w-full border rounded p-2" required />
                            @error('title')<div class="text-sm text-red-600">{{ $message }}</div>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium">Description</label>
                            <textarea name="description" class="mt-1 w-full border rounded p-2">{{ old('description') }}</textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium">Tutor</label>
                            <select name="tutor_id" class="mt-1 w-full border rounded p-2" required>
                                @foreach($tutors as $t)
                                    <option value="{{ $t->id }}" @selected(old('tutor_id')==$t->id)>{{ $t->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium">Location</label>
                            <input name="location" value="{{ old('location') }}" class="mt-1 w-full border rounded p-2" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium">Scheduled At</label>
                            <input type="datetime-local" name="scheduled_at" value="{{ old('scheduled_at') }}" class="mt-1 w-full border rounded p-2" required />
                        </div>
                        <div>
                            <label class="block text-sm font-medium">Duration (minutes)</label>
                            <input type="number" name="duration_minutes" value="{{ old('duration_minutes', 60) }}" class="mt-1 w-full border rounded p-2" required />
                        </div>
                        <div class="pt-2">
                            <button class="px-3 py-2 bg-indigo-600 text-white rounded">Create</button>
                            <a href="{{ route('sessions.index') }}" class="ml-2 text-gray-700">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>