<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Support\EnvWriter;
use Database\Seeders\RoleSeeder;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class InstallationController extends Controller
{
    public function welcome(): View|RedirectResponse
    {
        if ($this->isInstalled()) {
            return redirect()->route('dashboard');
        }
        return view('install.welcome');
    }

    public function requirements(): View|RedirectResponse
    {
        if ($this->isInstalled()) {
            return redirect()->route('dashboard');
        }

        $checks = $this->getRequirementsChecks();
        $allPass = collect($checks['extensions'])->every(fn ($v) => $v['ok'])
            && collect($checks['permissions'])->every(fn ($v) => $v['ok'])
            && $checks['php']['ok'];

        return view('install.requirements', compact('checks', 'allPass'));
    }

    public function environment(): View|RedirectResponse
    {
        if ($this->isInstalled()) {
            return redirect()->route('dashboard');
        }
        return view('install.environment');
    }

    public function saveEnvironment(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'app_name' => ['required','string','max:255'],
            'app_url' => ['required','url'],
            'db_connection' => ['required','in:sqlite,mysql,pgsql'],
            'db_host' => ['nullable','string'],
            'db_port' => ['nullable','string'],
            'db_database' => ['nullable','string'],
            'db_username' => ['nullable','string'],
            'db_password' => ['nullable','string'],
        ]);

        $env = [
            'APP_NAME' => $data['app_name'],
            'APP_URL' => $data['app_url'],
            'APP_ENV' => 'production',
            'APP_DEBUG' => 'false',
            'LOG_CHANNEL' => 'stack',
            'DB_CONNECTION' => $data['db_connection'],
        ];

        if ($data['db_connection'] === 'sqlite') {
            $env['DB_DATABASE'] = database_path('database.sqlite');
            // Ensure sqlite file exists
            if (!file_exists($env['DB_DATABASE'])) {
                @touch($env['DB_DATABASE']);
            }
        } else {
            $env = array_merge($env, [
                'DB_HOST' => $data['db_host'] ?? '127.0.0.1',
                'DB_PORT' => $data['db_port'] ?? ($data['db_connection'] === 'pgsql' ? '5432' : '3306'),
                'DB_DATABASE' => $data['db_database'] ?? '',
                'DB_USERNAME' => $data['db_username'] ?? '',
                'DB_PASSWORD' => $data['db_password'] ?? '',
            ]);
        }

        // Ensure APP_KEY
        $currentKey = config('app.key');
        if (!$currentKey) {
            $random = random_bytes(32);
            $env['APP_KEY'] = 'base64:'.base64_encode($random);
        }

        EnvWriter::setValues($env);

        // Try DB connection
        try {
            DB::purge();
            DB::reconnect();
            DB::connection()->getPdo();
        } catch (\Throwable $e) {
            return back()->withErrors(['db' => 'Database connection failed: '.$e->getMessage()])->withInput();
        }

        // Publish vendor assets (Spatie permission)
        try {
            Artisan::call('vendor:publish', [
                '--provider' => 'Spatie\\Permission\\PermissionServiceProvider',
                '--tag' => 'migrations',
                '--force' => true,
            ]);
            Artisan::call('vendor:publish', [
                '--provider' => 'Spatie\\Permission\\PermissionServiceProvider',
                '--tag' => 'config',
                '--force' => true,
            ]);
        } catch (\Throwable $e) {
            // continue; publishing is optional if already present
        }

        // Run migrations and seed roles
        try {
            Artisan::call('migrate', ['--force' => true]);
            Artisan::call('db:seed', ['--class' => RoleSeeder::class, '--force' => true]);
        } catch (\Throwable $e) {
            return back()->withErrors(['migrate' => 'Migration/Seeding failed: '.$e->getMessage()])->withInput();
        }

        return redirect()->route('install.admin');
    }

    public function admin(): View|RedirectResponse
    {
        if ($this->isInstalled()) {
            return redirect()->route('dashboard');
        }
        return view('install.admin');
    }

    public function saveAdmin(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required','string','max:255'],
            'email' => ['required','email','max:255','unique:users,email'],
            'password' => ['required','string','min:8','confirmed'],
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        if (method_exists($user, 'assignRole')) {
            $user->assignRole('super-admin');
        }

        // Mark installed
        try {
            Storage::disk('local')->put('installed', now()->toDateTimeString());
            EnvWriter::setValues(['APP_INSTALLED' => 'true']);
        } catch (\Throwable $e) {
            // ignore and continue
        }

        try {
            Artisan::call('config:cache');
            Artisan::call('route:cache');
            Artisan::call('view:cache');
            Artisan::call('event:cache');
        } catch (\Throwable $e) {}

        return redirect()->route('install.finish');
    }

    public function finish(): View|RedirectResponse
    {
        if (!$this->isInstalled()) {
            return redirect()->route('install.welcome');
        }
        return view('install.finish');
    }

    private function isInstalled(): bool
    {
        return (env('APP_INSTALLED') === true || env('APP_INSTALLED') === 'true') || file_exists(storage_path('app/installed'));
    }

    private function getRequirementsChecks(): array
    {
        $phpOk = version_compare(PHP_VERSION, '8.2.0', '>=');
        $extensions = [
            'BCMath' => extension_loaded('bcmath'),
            'Ctype' => extension_loaded('ctype'),
            'JSON' => extension_loaded('json'),
            'Mbstring' => extension_loaded('mbstring'),
            'OpenSSL' => extension_loaded('openssl'),
            'PDO' => extension_loaded('pdo'),
            'Tokenizer' => extension_loaded('tokenizer'),
            'XML' => extension_loaded('xml'),
            'Fileinfo' => extension_loaded('fileinfo'),
        ];

        $permissions = [
            'storage writable' => is_writable(storage_path()),
            'bootstrap/cache writable' => is_writable(base_path('bootstrap/cache')),
        ];

        return [
            'php' => ['version' => PHP_VERSION, 'ok' => $phpOk],
            'extensions' => collect($extensions)->map(fn($ok,$name) => ['name' => $name, 'ok' => $ok])->all(),
            'permissions' => collect($permissions)->map(fn($ok,$name) => ['name' => $name, 'ok' => $ok])->all(),
        ];
    }
}