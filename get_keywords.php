<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$keywords = DB::table('core_settings')->where('name', 'site_keywords')->first();
$desc = DB::table('core_settings')->where('name', 'site_desc')->first();
$title = DB::table('core_settings')->where('name', 'site_title')->first();

echo "Site Title: " . ($title ? $title->val : "NULL") . "\n";
echo "Site Description: " . ($desc ? $desc->val : "NULL") . "\n";
echo "Site Keywords: " . ($keywords ? $keywords->val : "NULL") . "\n";

$allSettings = DB::table('core_settings')->limit(50)->get();
echo "\nFirst 50 settings:\n";
foreach ($allSettings as $s) {
    echo "[$s->group] $s->name: $s->val\n";
}
