<?php
$menu = DB::table('core_menus')->where('name', 'Main Menu')->first();
if ($menu) {
    echo "ID: " . $menu->id . "\n";
    echo "Items: " . $menu->items . "\n";
} else {
    echo "Menu 'Main Menu' not found.\n";
}
