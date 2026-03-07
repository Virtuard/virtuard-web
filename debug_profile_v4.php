<?php
// debug_profile_v4.php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\User;
use Illuminate\Support\Facades\DB;

$username = 'virtuardtour';
$output = "--- DATABASE DEBUG V4 (SQLite) ---\n";

try {
    // Check user
    $user = User::where('user_name', $username)->first();
    if ($user) {
        $output .= "User '$username' FOUND!\n";
        $output .= "ID: {$user->id}, Role: {$user->role_id}, Status: {$user->status}\n";
    } else {
        $output .= "User '$username' NOT FOUND.\n";
    }

    // Check table existence in SQLite
    $res = DB::select("SELECT name FROM sqlite_master WHERE type='table' AND name='follow_member'");
    if (empty($res)) {
        $output .= "Table 'follow_member' is MISSING in SQLite!\n";
    } else {
        $output .= "Table 'follow_member' EXISTS.\n";
        if ($user) {
            try {
                $f1 = DB::table('follow_member')->where('follower_id', $user->id)->count();
                $output .= "Count where follower_id={$user->id}: $f1\n";
            } catch (\Exception $e) {
                $output .= "Error querying follow_member: " . $e->getMessage() . "\n";
            }
        }
    }

} catch (\Exception $e) {
    $output .= "CRITICAL ERROR: " . $e->getMessage() . "\n";
}

file_put_contents('profile_debug_output.txt', $output);
echo "Debug complete.\n";
