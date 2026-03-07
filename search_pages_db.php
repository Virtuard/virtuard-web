<?php
$results = DB::select("SELECT id, title, slug, template_id FROM core_pages WHERE slug LIKE '%landscape%' OR title LIKE '%landscape%'");
if ($results) {
    foreach ($results as $row) {
        print_r($row);
    }
} else {
    echo "No pages found with 'landscape' in slug or title.\n";
}
