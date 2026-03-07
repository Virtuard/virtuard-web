<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

$columns = Schema::getColumnListing('user_post_status');
echo "Columns in user_post_status:\n";
print_r($columns);

$sample = DB::table('user_post_status')->limit(1)->first();
echo "\nSample record:\n";
print_r($sample);
