<?php
use Modules\Page\Models\Page;

$pages = Page::select('slug', 'title')->get();
foreach ($pages as $page) {
    echo $page->slug . " | " . $page->title . "\n";
}
