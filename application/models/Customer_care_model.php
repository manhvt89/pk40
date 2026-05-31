<?php
class Customer_care_model extends CI_Model {
    
    public function search_customers($status = 'new', $limit = 10, $offset = 0, $sort = 'total_amount', $order = 'desc', $search = '') {
        $ninety_days_ago = time() - (90 * 24 * 60 * 60);
        
        $this->db->select('c.person_id, p.first_name, p.last_name, p.phone_number, p.address_1, 
            COALESCE((SELECT SUM(sp.payment_amount) FROM '.$this->db->dbprefix('sales').' s JOIN '.$this->db->dbprefix('sales_payments').' sp ON s.sale_id = sp.sale_id WHERE s.customer_id = c.person_id), 0) AS total_amount, 
            (SELECT MAX(contact_time) FROM '.$this->db->dbprefix('customer_care').' WHERE customer_id = c.person_id) AS last_contact_time');
        $this->db->from('customers AS c');
        $this->db->join('people AS p', 'c.person_id = p.person_id');
        $this->db->where('c.deleted', 0);
        
        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('p.first_name', $search);
            $this->db->or_like('p.last_name', $search);
            $this->db->or_like('p.phone_number', $search);
            $this->db->or_like('CONCAT(p.last_name, " ", p.first_name)', $search);
            $this->db->group_end();
        }
        
        if ($status == 'new') {
            $this->db->having('(last_contact_time IS NULL OR last_contact_time < '.$ninety_days_ago.')');
        } else {
            $this->db->having('last_contact_time >= '.$ninety_days_ago);
        }
        
        // Xử lý logic sắp xếp
        if ($sort == 'name') {
            $this->db->order_by('p.last_name', $order);
            $this->db->order_by('p.first_name', $order);
        } else {
            $this->db->order_by($sort, $order);
        }
        
        if ($limit > 0) {
            $this->db->limit($limit, $offset);
        }
        
        return $this->db->get();
    }
    
    public function count_customers($status = 'new', $search = '') {
        $ninety_days_ago = time() - (90 * 24 * 60 * 60);
        
        $this->db->select('c.person_id,
            (SELECT MAX(contact_time) FROM '.$this->db->dbprefix('customer_care').' WHERE customer_id = c.person_id) AS last_contact_time');
        $this->db->from('customers AS c');
        $this->db->join('people AS p', 'c.person_id = p.person_id');
        $this->db->where('c.deleted', 0);
        
        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('p.first_name', $search);
            $this->db->or_like('p.last_name', $search);
            $this->db->or_like('p.phone_number', $search);
            $this->db->or_like('CONCAT(p.last_name, " ", p.first_name)', $search);
            $this->db->group_end();
        }
        
        if ($status == 'new') {
            $this->db->having('(last_contact_time IS NULL OR last_contact_time < '.$ninety_days_ago.')');
        } else {
            $this->db->having('last_contact_time >= '.$ninety_days_ago);
        }
        
        // Trả về số dòng bằng cách gói lại thành subquery hoặc dùng count_all_results không chuẩn với having
        // Ở đây dùng đếm result_array
        return $this->db->get()->num_rows();
    }
    
    public function log_contact($customer_id, $employee_id, $notes = '') {
        $data = [
            'customer_id' => $customer_id,
            'employee_id' => $employee_id,
            'contact_time' => time(),
            'notes' => $notes
        ];
        return $this->db->insert('customer_care', $data);
    }
}
