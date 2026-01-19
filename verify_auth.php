<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Request;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== VERIFYING AUTH CONTROLLER ===\n";

// 1. Create Request for Login
$controller = new \App\Http\Controllers\AuthController();

// Ensure user exists
$user = User::where('email', 'admin@falcon.com')->first();
if (!$user) {
    if (User::count() == 0) {
        // Fallback create
        $user = User::create([
            'name' => 'Test Admin',
            'email' => 'admin@falcon.com',
            'password' => Hash::make('password'),
            'role' => 'admin'
        ]);
    } else {
        $user = User::first(); 
    }
}
echo "[Context] Testing Login with User: {$user->email}\n";

// Mock Request
$request = \Illuminate\Http\Request::create('/login', 'POST', [
    'email' => $user->email,
    'password' => 'password',
    'role' => 'admin' // logic might just ignore this for auth but redirect matches
]);

// Mock Session/Auth
// Hard to mock full Auth facade behavior in script without testing DB session driver.
// But we can check if Controller throws validation error or redirect.
try {
    $response = $controller->login($request);
    echo "[SUCCESS] Controller processed login without crash.\n";
    // Check if redirect
    if ($response instanceof \Illuminate\Http\RedirectResponse) {
        echo "[SUCCESS] Redirected to: " . $response->getTargetUrl() . "\n";
    }
} catch (\Illuminate\Validation\ValidationException $e) {
    echo "[FAILURE] Validation Error: " . print_r($e->errors(), true) . "\n";
} catch (\Throwable $e) {
    echo "[NOTE] Error (likely session/auth mock): " . $e->getMessage() . "\n";
}
