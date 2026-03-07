<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$settings = DB::table('core_settings')
    ->where('name', 'like', '%seo%')
    ->orWhere('name', 'like', '%site%')
    ->orWhere('name', 'like', '%home%')
    ->get();

echo "SEO and Site Settings:\n";
foreach ($settings as $setting) {
    echo "[$setting->group] $setting->name: $setting->val\n";
}

$seoEntries = DB::table('bravo_seo')->limit(5)->get();
echo "\nRecent bravo_seo entries:\n";
foreach ($seoEntries as $entry) {
    echo "ID: $entry->id | Title: $entry->seo_title | Desc: $entry->seo_desc\n";
}
