<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

if (!isset($argv[1]) || empty($argv[1])) {
    echo "Usage: php call_api_as_user.php user@example.com\n";
    exit(1);
}

$email = $argv[1];
$user = User::where('email', $email)->first();
if (! $user) {
    echo json_encode(['error' => 'user_not_found', 'email' => $email], JSON_PRETTY_PRINT) . "\n";
    exit(0);
}

// Log in as the user in the current runtime
Auth::loginUsingId($user->id);

$request = Request::create('/admin/api/donatur', 'GET', ['per_page' => 100]);
// attach session/cookie headers if needed
$response = $app->handle($request);

// Output response content
$status = $response->getStatusCode();
$content = $response->getContent();

echo "HTTP STATUS: $status\n";
echo $content . "\n";

// logout
Auth::logout();

