<?php
$db = new mysqli('mysql', 'admin', 'pointofsale', 'ospos');
$res = $db->query('SHOW COLUMNS FROM ospos_inc1');
while($row = $res->fetch_assoc()) {
    echo $row['Field'] . "\n";
}
