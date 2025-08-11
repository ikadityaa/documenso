<x-guest-layout>
    <h1 class="text-2xl font-bold mb-4">Create Super Admin</h1>

    @if ($errors->any())
        <div class="bg-red-100 text-red-800 p-3 rounded mb-4">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('install.admin.save') }}" class="space-y-4">
        @csrf
        <div>
            <label class="block text-sm font-medium">Name</label>
            <input name="name" value="{{ old('name') }}" class="mt-1 w-full border rounded p-2" required />
        </div>
        <div>
            <label class="block text-sm font-medium">Email</label>
            <input name="email" type="email" value="{{ old('email') }}" class="mt-1 w-full border rounded p-2" required />
        </div>
        <div>
            <label class="block text-sm font-medium">Password</label>
            <input name="password" type="password" class="mt-1 w-full border rounded p-2" required />
        </div>
        <div>
            <label class="block text-sm font-medium">Confirm Password</label>
            <input name="password_confirmation" type="password" class="mt-1 w-full border rounded p-2" required />
        </div>
        <div class="pt-2">
            <button class="px-4 py-2 bg-indigo-600 text-white rounded">Create and Finish</button>
        </div>
    </form>
</x-guest-layout>