<?php
class Check_db_all extends CI_Controller {
    public function index() {
        $this->output->enable_profiler(FALSE);
        header('Content-Type: text/plain; charset=utf-8');

        $sql_content = file_get_contents('/app/kmvh.sql');
        
        // Extract all CREATE TABLE statements
        preg_match_all('/CREATE TABLE `(.*?)` \((.*?)\) ENGINE=/s', $sql_content, $matches);
        
        $tables_ref = [];
        for ($i=0; $i < count($matches[1]); $i++) {
            $table_name = $matches[1][$i];
            $body = $matches[2][$i];
            
            $tables_ref[$table_name] = [];
            
            // Extract columns
            $lines = explode("\n", $body);
            foreach ($lines as $line) {
                $line = trim($line);
                if (preg_match('/^`(.*?)`\s+(.*?)(,|$)/', $line, $col_match)) {
                    $tables_ref[$table_name][$col_match[1]] = $col_match[2];
                }
            }
        }
        
        echo "=== BẮT ĐẦU KIỂM TRA TOÀN BỘ DATABASE ===\n\n";
        
        $missing_columns = [];
        
        foreach ($tables_ref as $table_name => $ref_cols) {
            // Check if table exists
            if (!$this->db->table_exists($table_name)) {
                echo "MISSING TABLE: {$table_name}\n";
                continue;
            }
            
            $live_cols = $this->db->list_fields($table_name);
            
            foreach ($ref_cols as $col_name => $col_def) {
                if (!in_array($col_name, $live_cols)) {
                    echo "MISSING COLUMN in {$table_name}: {$col_name} ({$col_def})\n";
                    $missing_columns[] = "ALTER TABLE `{$table_name}` ADD COLUMN `{$col_name}` {$col_def};";
                }
            }
        }
        
        echo "\n=== TỔNG HỢP CÁC TRƯỜNG BỊ THIẾU ===\n";
        if (count($missing_columns) == 0) {
            echo "Tuyệt vời! Không còn trường nào bị thiếu so với kmvh.sql.\n";
        } else {
            echo implode("\n", $missing_columns) . "\n";
            
            // Thực thi luôn
            echo "\nĐang tự động bổ sung...\n";
            foreach($missing_columns as $sql) {
                if ($this->db->query($sql)) {
                    echo "-> OK: {$sql}\n";
                } else {
                    echo "-> LỖI: {$sql} - " . $this->db->error()['message'] . "\n";
                }
            }
        }
    }
}
