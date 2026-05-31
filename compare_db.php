<?php
$db = new mysqli('mysql', 'admin', 'pointofsale');
if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

// 1. Create temporary reference database
$db->query("DROP DATABASE IF EXISTS ospos_ref");
if (!$db->query("CREATE DATABASE ospos_ref")) {
    die("Cannot create ospos_ref database: " . $db->error);
}

echo "Created ospos_ref database.\n";
