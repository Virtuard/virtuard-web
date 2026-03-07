<?php
// debug_profile_v3.php
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\User;
use Illuminate\Support\Facades\DB;

try {
    $username = 'virtuardtour';
    $output = "";

    $output .= "--- DATABASE DEBUG V3 ---\n";

    // Check connection
    try {
        DB::connection()->getPdo();
        $output .= "Database Connection: SUCCESS\n";
    } catch (\Exception $e) {
        $output .= "Database Connection: FAILED - " . $e->getMessage() . "\n";
    }

    if ($user = User::where('user_name', $username)->first()) {
        $output .= "User Found: YES\n";
        $output .= "ID: " . $user->id . "\n";
        $output .= "Email: " . $user->email . "\n";
        $output .= "Role ID: " . $user->role_id . "\n";
        $output .= "Status: " . $user->status . "\n";
        $output .= "Has Permission 'dashboard_vendor_access': " . ($user->hasPermission('dashboard_vendor_access') ? 'YES' : 'NO') . "\n";
    } else {
        $output .= "User Found: NO (Checked user_name '$username')\n";

        // Check if user exists by ID as fallback to see if it's a numeric slug issue
        $user_by_id = User::find($username);
        if ($user_by_id) {
            $output .= "User found by ID fallback: " . $user_by_id->id . "\n";
        }
    }

    $tables = DB::select('SHOW TABLES');
    $tableList = array_map(function ($t) {
        return array_values((array) $t)[0]; }, $tables);

    if (in_array('follow_member', $tableList)) {
        $output .= "Table 'follow_member': EXISTS\n";
        if (isset($user)) {
            try {
                $f1 = DB::table('follow_member')->where('follower_id', $user->id)->count();
                $f2 = DB::table('follow_member')->where('user_id', $user->id)->count();
                $output .= "Follower count: $f1\n";
                $output .= "Following count: $f2\n";
            } catch (\Exception $e) {
                $output .= "Error counting follows: " . $e->getMessage() . "\n";
            }
        }
    } else {
        $output .= "Table 'follow_member': MISSING\n";
    }

    file_put_contents('profile_debug_output.txt', $output);
    echo "Debug complete. Check profile_debug_output.txt\n";

} catch (\Exception $e) {
    file_put_contents('profile_debug_output.txt', "CRITICAL ERROR: " . $e->getMessage() . "\n" . $e->getTraceAsString());
    echo "Critical Error. Check profile_debug_output.txt\n";
}
