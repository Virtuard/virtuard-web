<?php
$conn = mysqli_connect('localhost', 'root', '', 'newvirtuard');
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$res = mysqli_query($conn, "DESCRIBE user_post_status");
while ($row = mysqli_fetch_assoc($res)) {
    echo $row['Field'] . " (" . $row['Type'] . ")\n";
}

$res = mysqli_query($conn, "SELECT * FROM user_post_status LIMIT 1");
$row = mysqli_fetch_assoc($res);
print_r($row);
mysqli_close($conn);
