<?php
$m = new mysqli('localhost', 'root', '', 'kmvh');
$r = $m->query("SELECT DISTINCT SUBSTRING_INDEX(trans_comment, ' ', 1) as comment_prefix FROM ospos_inventory");
while($row = $r->fetch_assoc()) echo $row['comment_prefix']."\n";
