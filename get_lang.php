<?php
$conn = new mysqli('mysql', 'admin', 'pointofsale', 'ospos', 3306);
$conn->query("UPDATE ospos_app_config SET value='english' WHERE `key`='language'");
echo "Updated language to english\n";
