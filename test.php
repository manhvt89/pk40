<?php
require 'application/config/database.php';
$db = new mysqli($db['default']['hostname'], $db['default']['username'], $db['default']['password'], $db['default']['database']);
$res = $db->query("SELECT * FROM ospos_app_config WHERE `key` IN ('cyls', 'mysphs', 'hysphs')");
while($row = $res->fetch_assoc()) {
    print_r($row);
}
