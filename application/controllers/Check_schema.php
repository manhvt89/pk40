<?php
class Check_schema extends CI_Controller {
    public function index() {
        $this->output->enable_profiler(FALSE);
        header('Content-Type: text/plain; charset=utf-8');
        
        $res = $this->db->query("SHOW CREATE TABLE ospos_customers")->row_array();
        echo $res['Create Table'] . "\n\n";
        
        $res = $this->db->query("SHOW CREATE TABLE ospos_people")->row_array();
        echo $res['Create Table'] . "\n\n";
    }
}
