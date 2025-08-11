<x-guest-layout>
    <h1 class="text-2xl font-bold mb-4">Requirements Check</h1>

    <div class="space-y-4">
        <div>
            <div class="font-semibold">PHP Version (>= 8.2)</div>
            <div class="mt-1">
                @if($checks['php']['ok'])
                    <span class="text-green-700">OK ({{ $checks['php']['version'] }})</span>
                @else
                    <span class="text-red-700">Current: {{ $checks['php']['version'] }}</span>
                @endif
            </div>
        </div>

        <div>
            <div class="font-semibold">Extensions</div>
            <ul class="mt-1 list-disc pl-6">
                @foreach($checks['extensions'] as $ext)
                    <li class="{{ $ext['ok'] ? 'text-green-700' : 'text-red-700' }}">{{ $ext['name'] }}: {{ $ext['ok'] ? 'OK' : 'Missing' }}</li>
                @endforeach
            </ul>
        </div>

        <div>
            <div class="font-semibold">Permissions</div>
            <ul class="mt-1 list-disc pl-6">
                @foreach($checks['permissions'] as $perm)
                    <li class="{{ $perm['ok'] ? 'text-green-700' : 'text-red-700' }}">{{ $perm['name'] }}: {{ $perm['ok'] ? 'OK' : 'Not writable' }}</li>
                @endforeach
            </ul>
        </div>
    </div>

    <div class="mt-6">
        @if($allPass)
            <a href="{{ route('install.environment') }}" class="px-4 py-2 bg-indigo-600 text-white rounded">Continue</a>
        @else
            <div class="text-red-700">Please fix the items above and reload.</div>
        @endif
    </div>
</x-guest-layout>