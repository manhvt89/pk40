<?php
$mysqli = new mysqli("localhost", "root", "", "kmvh");
if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: " . $mysqli->connect_error;
    exit();
}
$res = $mysqli->query("SELECT trans_items, trans_date, trans_comment, trans_inventory FROM ospos_inventory LIMIT 10");
while ($row = $res->fetch_assoc()) {
    print_r($row);
}
