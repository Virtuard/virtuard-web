<?php
use Modules\Page\Models\Page;

$page = Page::where('slug', 'landscape')->first();
if ($page) {
    echo "ID: " . $page->id . "\n";
    echo "Title: " . $page->title . "\n";
    echo "Content Length: " . strlen($page->content) . "\n";
    echo "Content Snippet: " . substr($page->content, 0, 500) . "...\n";
    echo "Template ID: " . $page->template_id . "\n";
} else {
    echo "Page 'landscape' not found.\n";
}
