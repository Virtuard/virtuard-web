<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$settings = DB::table('core_settings')->select('id', 'name', 'group')->get();

foreach ($settings as $setting) {
    echo "[$setting->group] $setting->name\n";
}
