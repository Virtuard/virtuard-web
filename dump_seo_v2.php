<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$results = [];

// Get home page ID
$homePageId = DB::table('core_settings')->where('name', 'home_page_id')->first();
$results['home_page_id'] = $homePageId ? $homePageId->val : 'Not Found';

// Get global SEO settings
$seoSettings = DB::table('core_settings')
    ->where('name', 'like', '%site_title%')
    ->orWhere('name', 'like', '%site_desc%')
    ->orWhere('name', 'like', '%site_keyword%')
    ->orWhere('name', 'like', '%home_seo%')
    ->get();

$results['global_seo'] = $seoSettings;

// If we have a home page ID, get its SEO from bravo_seo if possible
// Page typically has a polymorphic relation or stores SEO object ID
if ($homePageId && is_numeric($homePageId->val)) {
    $page = DB::table('core_pages')->where('id', $homePageId->val)->first();
    if ($page) {
        $results['home_page_title'] = $page->title;
        // Search bravo_seo for this page
        $pageSeo = DB::table('bravo_seo')
            ->where('object_id', $homePageId->val)
            ->where('object_model', 'page')
            ->first();
        $results['home_page_seo'] = $pageSeo;
    }
}

echo json_encode($results, JSON_PRETTY_PRINT);
