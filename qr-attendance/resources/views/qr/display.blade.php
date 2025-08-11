<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">QR Check-in</h2>
            <form method="POST" action="{{ route('sessions.qr.refresh', $session) }}">
                @csrf
                <button class="px-3 py-2 bg-indigo-600 text-white rounded">Refresh</button>
            </form>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="text-center space-y-4">
                        <div class="text-lg">Scan to check in</div>
                        <div class="flex justify-center">{!! $qrSvg !!}</div>
                        <div class="text-sm text-gray-600 break-words">{{ $scanUrl }}</div>
                        <div id="expiry" class="text-sm text-gray-800"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const expiresAt = {{ json_encode(optional($expiresAt)->toIso8601String()) }};
        if (expiresAt) {
            const expiryEl = document.getElementById('expiry');
            const end = new Date(expiresAt);
            const tick = () => {
                const now = new Date();
                const s = Math.max(0, Math.floor((end - now) / 1000));
                const m = Math.floor(s/60);
                const r = s%60;
                expiryEl.textContent = `Expires in ${m}:${r.toString().padStart(2,'0')}`;
            };
            tick();
            setInterval(tick, 1000);
        }
    </script>
</x-app-layout>