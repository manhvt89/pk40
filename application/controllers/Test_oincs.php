<?php
class Test_oincs extends CI_Controller {
    public function index() {
        $this->output->enable_profiler(FALSE);
        header('Content-Type: text/plain; charset=utf-8');
        
        $sql = "ALTER TABLE ospos_inc1 ADD COLUMN type tinyint(1) DEFAULT 0";
        if ($this->db->query($sql)) {
            echo "SUCCESS: Added 'type' column to ospos_inc1.\n";
        } else {
            echo "ERROR: " . $this->db->error()['message'] . "\n";
        }
        
        $fields = $this->db->list_fields('inc1');
        echo "Current columns: \n";
        print_r($fields);
    }
}
