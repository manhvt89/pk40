<?php
class Test_db extends CI_Controller {
    public function index() {
        // Disable profiler just in case
        $this->output->enable_profiler(FALSE);
        
        $dbprefix = $this->db->dbprefix;
        $sql = "
            SELECT 
                iq.item_id, 
                i.name,
                iq.location_id, 
                iq.quantity AS current_quantity, 
                COALESCE(SUM(inv.trans_inventory), 0) AS calculated_quantity
            FROM 
                {$dbprefix}item_quantities iq
            LEFT JOIN 
                {$dbprefix}items i ON iq.item_id = i.item_id
            LEFT JOIN 
                {$dbprefix}inventory inv ON iq.item_id = inv.trans_items AND iq.location_id = inv.trans_location
            GROUP BY 
                iq.item_id, iq.location_id, i.name, iq.quantity
            HAVING 
                current_quantity != calculated_quantity
        ";
        
        $query = $this->db->query($sql);
        
        header('Content-Type: application/json');
        echo json_encode($query->result_array());
    }
}
