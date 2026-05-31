<?php
class Migrate_care extends CI_Controller {
    public function index() {
        $this->output->enable_profiler(FALSE);
        header('Content-Type: text/plain; charset=utf-8');

        // 1. Create table
        $sql1 = "CREATE TABLE IF NOT EXISTS `ospos_customer_care` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `customer_id` int(11) NOT NULL,
          `employee_id` int(11) NOT NULL,
          `contact_time` int(11) NOT NULL,
          `notes` text,
          PRIMARY KEY (`id`),
          KEY `customer_id` (`customer_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

        if ($this->db->query($sql1)) {
            echo "Table ospos_customer_care created/exists.\n";
        } else {
            echo "Error creating table: " . $this->db->error()['message'] . "\n";
        }

        // 2. Insert Module
        $module_data = [
            'name_lang_key' => 'module_customer_care',
            'desc_lang_key' => 'module_customer_care_desc',
            'sort' => 110,
            'module_key' => 'customer_care',
            'name' => 'Chăm sóc KH'
        ];
        if ($this->db->where('module_key', 'customer_care')->count_all_results('modules') == 0) {
            $max = $this->db->select_max('id')->get('modules')->row()->id;
            $module_data['id'] = $max + 1;
            $this->db->insert('modules', $module_data);
            echo "Inserted module.\n";
        }

        // 3. Insert Permission
        $perm_data = [
            'permission_key' => 'customer_care_index',
            'module_id' => 'customer_care',
            'name' => 'Chăm sóc KH'
        ];
        if ($this->db->where('permission_key', 'customer_care_index')->count_all_results('permissions') == 0) {
            $this->db->insert('permissions', $perm_data);
            echo "Inserted permission.\n";
        }

        // 4. Insert Grant
        $grant_data = [
            'permission_id' => 'customer_care_index',
            'role_id' => 1
        ];
        if ($this->db->where('permission_id', 'customer_care_index')->where('role_id', 1)->count_all_results('grants') == 0) {
            $this->db->insert('grants', $grant_data);
            echo "Inserted grant.\n";
        }
        
        echo "Migration completed. Vui lòng ĐĂNG XUẤT và ĐĂNG NHẬP LẠI để menu xuất hiện.\n";
    }
}
