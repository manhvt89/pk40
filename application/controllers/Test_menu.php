<?php
class Test_menu extends CI_Controller {
    public function index() {
        echo "<pre>";
        echo "=== CHECKING OSPOS_MODULES ===\n";
        $q = $this->db->query("SELECT * FROM ospos_modules WHERE module_key = 'customer_care'");
        print_r($q->result_array());
        
        echo "\n=== CHECKING OSPOS_PERMISSIONS ===\n";
        $q = $this->db->query("SELECT * FROM ospos_permissions WHERE module_id = 'customer_care'");
        print_r($q->result_array());

        echo "\n=== CHECKING OSPOS_GRANTS ===\n";
        $q = $this->db->query("SELECT * FROM ospos_grants WHERE permission_id IN (SELECT id FROM ospos_permissions WHERE module_id = 'customer_care')");
        print_r($q->result_array());

        echo "\n=== CHECKING ROLES FOR CURRENT LOGGED IN USER ===\n";
        $user_id = $this->session->userdata('person_id');
        if ($user_id) {
            echo "Logged in user ID: " . $user_id . "\n";
            $q = $this->db->query("SELECT * FROM ospos_user_roles WHERE user_id = ?", [$user_id]);
            print_r($q->result_array());
        } else {
            echo "No user logged in currently.\n";
        }
        
        echo "\n=== CHECKING IF MODULE IS ALLOWED FOR USER ===\n";
        if ($user_id) {
            $q = $this->Module->get_allowed_modules($user_id);
            $found = false;
            foreach ($q->result() as $mod) {
                if ($mod->module_key == 'customer_care') {
                    $found = true;
                    echo "YES! customer_care is in allowed_modules query!\n";
                    print_r($mod);
                }
            }
            if (!$found) {
                echo "NO! customer_care is NOT in allowed_modules query!\n";
            }
        }
        echo "</pre>";
    }
}
