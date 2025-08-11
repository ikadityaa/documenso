<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureInstalled
{
    public function handle(Request $request, Closure $next): Response
    {
        $isInstalled = $this->isInstalled();
        $isInstallRoute = str_starts_with(trim($request->path(), '/'), 'install');

        if (!$isInstalled && !$isInstallRoute) {
            return redirect()->to('/install');
        }

        if ($isInstalled && $isInstallRoute) {
            return redirect()->route('dashboard');
        }

        return $next($request);
    }

    private function isInstalled(): bool
    {
        if (env('APP_INSTALLED') === true || env('APP_INSTALLED') === 'true') {
            return true;
        }

        $flagPath = storage_path('app/installed');
        return file_exists($flagPath);
    }
}