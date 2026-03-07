<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\User;
use Illuminate\Support\Facades\DB;

$username = 'virtuardtour';

try {
    $user = User::where('user_name', $username)->first();
    if (!$user) {
        echo "User '$username' NOT FOUND by user_name.\n";
        $user = User::find($username);
        if ($user) {
            echo "User found by ID: " . $user->id . "\n";
        } else {
            echo "User NOT FOUND by ID either.\n";
        }
    } else {
        echo "User '$username' FOUND!\n";
        echo "ID: " . $user->id . "\n";
        echo "Role ID: " . $user->role_id . "\n";
        echo "Status: " . $user->status . "\n";
        echo "Has Permission 'dashboard_vendor_access': " . ($user->hasPermission('dashboard_vendor_access') ? 'YES' : 'NO') . "\n";
    }

    // Check if table exists
    $tables = DB::select('SHOW TABLES LIKE "follow_member"');
    if (empty($tables)) {
        echo "Table 'follow_member' DOES NOT EXIST!\n";
    } else {
        echo "Table 'follow_member' exists.\n";
    }

} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
