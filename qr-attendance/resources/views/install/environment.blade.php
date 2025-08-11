<x-guest-layout>
    <h1 class="text-2xl font-bold mb-4">Environment Configuration</h1>

    @if ($errors->any())
        <div class="bg-red-100 text-red-800 p-3 rounded mb-4">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('install.environment.save') }}" class="space-y-4">
        @csrf
        <div>
            <label class="block text-sm font-medium">App Name</label>
            <input name="app_name" value="{{ old('app_name', config('app.name')) }}" class="mt-1 w-full border rounded p-2" required />
        </div>
        <div>
            <label class="block text-sm font-medium">App URL</label>
            <input name="app_url" value="{{ old('app_url', config('app.url') ?? 'http://localhost') }}" class="mt-1 w-full border rounded p-2" required />
        </div>
        <div>
            <label class="block text-sm font-medium">Database Driver</label>
            <select name="db_connection" class="mt-1 w-full border rounded p-2">
                <option value="sqlite" @selected(old('db_connection')==='sqlite')>SQLite</option>
                <option value="mysql" @selected(old('db_connection','mysql')==='mysql')>MySQL</option>
                <option value="pgsql" @selected(old('db_connection')==='pgsql')>PostgreSQL</option>
            </select>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium">DB Host</label>
                <input name="db_host" value="{{ old('db_host','127.0.0.1') }}" class="mt-1 w-full border rounded p-2" />
            </div>
            <div>
                <label class="block text-sm font-medium">DB Port</label>
                <input name="db_port" value="{{ old('db_port','3306') }}" class="mt-1 w-full border rounded p-2" />
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium">DB Database</label>
                <input name="db_database" value="{{ old('db_database') }}" class="mt-1 w-full border rounded p-2" />
            </div>
            <div>
                <label class="block text-sm font-medium">DB Username</label>
                <input name="db_username" value="{{ old('db_username') }}" class="mt-1 w-full border rounded p-2" />
            </div>
        </div>
        <div>
            <label class="block text-sm font-medium">DB Password</label>
            <input type="password" name="db_password" value="{{ old('db_password') }}" class="mt-1 w-full border rounded p-2" />
        </div>
        <div class="pt-2">
            <button class="px-4 py-2 bg-indigo-600 text-white rounded">Save and Continue</button>
        </div>
    </form>
</x-guest-layout>