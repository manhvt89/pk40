<?php
class Check_config extends CI_Controller {
    public function index() {
        $this->output->enable_profiler(FALSE);
        header('Content-Type: text/plain; charset=utf-8');

        echo "--- CYLS ---\n";
        var_dump($this->config->item('cyls'));
        
        echo "\n--- MYSPHS ---\n";
        var_dump($this->config->item('mysphs'));
        
        echo "\n--- HYSPHS ---\n";
        var_dump($this->config->item('hysphs'));
        
        echo "\n--- LENS ---\n";
        var_dump($this->config->item('iKindOfLens'));
        
        echo "\n--- DONE ---\n";
    }
}
