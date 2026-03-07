<?php
try {
    $db = new PDO('sqlite:database/database.sqlite');
    $res = $db->query("SELECT sql FROM sqlite_master WHERE type='table' AND name='user_post_status'");
    $row = $res->fetch();
    if ($row) {
        echo "Schema for user_post_status:\n";
        echo $row['sql'] . "\n";
    } else {
        echo "Table user_post_status not found in sqlite.\n";

        $res = $db->query("SELECT name FROM sqlite_master WHERE type='table'");
        echo "Available tables:\n";
        while ($r = $res->fetch()) {
            echo $r['name'] . "\n";
        }
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
