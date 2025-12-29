<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
use App\Models\User;
use App\Models\Donatur;

$leader = User::where('email','hellyarmiandri2401@gmail.com')->first();
$sub = User::where('email','abdulhamidsumarjo@gmail.com')->first();

echo "leader id: {$leader->id}\n";
echo "sub id: {$sub->id}\n";

$c1 = Donatur::where('pic', (string)$sub->id)->count();
$c2 = Donatur::whereIn('pic', [$leader->id, $sub->id])->count();
$c3 = Donatur::whereIn('pic', [$leader->id])->count();

echo "donaturs with pic=sub: $c1\n";
echo "donaturs with pic in [leader,sub]: $c2\n";
echo "donaturs with pic=leader: $c3\n";

// test leader visibility query
$subIds = $leader->subordinates()->pluck('id')->toArray();
$allowed = array_merge([$leader->id], $subIds);
$query = Donatur::query();
$query->where(function($q) use ($allowed) {
    $q->whereIn('donaturs.created_by', $allowed)
      ->orWhereIn('donaturs.pic', $allowed);
});

echo "leader query count: " . $query->count() . "\n";
