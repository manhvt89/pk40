<?php
$file = 'E:/projects/pk401/pk40/application/controllers/Reports.php';
$content = file_get_contents($file);

$pattern = '/\$begin_quantity\s*=\s*\$row\[\'end_quantity\'\]\s*\+\s*\$row\[\'sale_quantity\'\]\s*-\s*\$row\[\'receive_quantity\'\];\s*\$_end_quantity\s*=\s*\$row\[\'end_quantity\'\]\s*\+\s*\$row\[\'b_sale_quantity\'\]\s*-\s*\$row\[\'b_receive_quantity\'\];\s*\$_sale_quantity\s*=\s*\$row\[\'sale_quantity\'\]\s*-\s*\$row\[\'b_sale_quantity\'\];\s*\$_receive_quantity\s*=\s*\$row\[\'receive_quantity\'\]\s*-\s*\$row\[\'b_receive_quantity\'\];\s*\$summary_data\[\]\s*=\s*\$this->xss_clean\(array\(\s*\'id\'\s*=>\s*\$i,\s*\'cat\'\s*=>\s*\$row\[\'category\'\],\s*\'begin_quantity\'\s*=>\s*number_format\(\$begin_quantity\),\s*\'end_quantity\'\s*=>\s*number_format\(\$_end_quantity\),\s*\'sale_quantity\'\s*=>\s*number_format\(\$_sale_quantity\)==0\?\'-\':number_format\(\$_sale_quantity\),\s*\'receive_quantity\'\s*=>\s*number_format\(\$_receive_quantity\)==0\?\'-\':number_format\(\$_receive_quantity\),\s*\)\);/s';

$replacement = '$summary_data[] = $this->xss_clean(array(
                    \'id\' => $i,
                    \'cat\' => $row[\'category\'],
                    \'begin_quantity\' => number_format($row[\'begin_quantity\']),
                    \'receive_quantity\' => number_format($row[\'receive_quantity\'])==0?\'-\':number_format($row[\'receive_quantity\']),
                    \'sale_quantity\' => number_format($row[\'sale_quantity\'])==0?\'-\':number_format($row[\'sale_quantity\']),
                    \'adjustment_quantity\' => number_format($row[\'adjustment_quantity\'])==0?\'-\':number_format($row[\'adjustment_quantity\']),
                    \'end_quantity\' => number_format($row[\'end_quantity\']),
                ));';

$new_content = preg_replace($pattern, $replacement, $content);
file_put_contents($file, $new_content);

echo "Replaced " . preg_match_all($pattern, $content) . " occurrences.\n";
