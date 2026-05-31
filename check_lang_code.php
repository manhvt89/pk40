<?php
$conn = new mysqli('mysql', 'admin', 'pointofsale', 'ospos', 3306);
$result = $conn->query("SELECT value FROM ospos_app_config WHERE `key`='language_code'");
$row = $result->fetch_assoc();
echo "LANGUAGE_CODE=" . ($row ? $row['value'] : 'NOT_FOUND') . "\n";
