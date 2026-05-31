<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

abstract class Report extends CI_Model 
{
	function __construct()
	{
		parent::__construct();

		//Make sure the report is not cached by the browser
		$this->output->set_header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
		$this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate');
		$this->output->set_header('Cache-Control: post-check=0, pre-check=0', FALSE);
		$this->output->set_header('Pragma: no-cache');
	}

	public abstract function getDataColumns();
	public abstract function getData(array $inputs);
	public abstract function getSummaryData(array $inputs);

	// Get true historical inventory delta from ospos_inventory
	public function _getInventoryDelta($inputs, $filter, $isBegin = false)
	{
		$this->db->select('i.category as item_category, SUM(inv.trans_inventory) AS delta');
		$this->db->from('inventory AS inv');
		$this->db->join('items AS i', 'inv.trans_items = i.item_id');
		$this->db->where_in('i.category', $filter);
		
		if($isBegin) {
			$this->db->where('DATE(inv.trans_date) <', $inputs['fromDate']);
		} else {
			$this->db->where('DATE(inv.trans_date) <=', $inputs['toDate']);
		}

		if(isset($inputs['location_id']) && $inputs['location_id'] != 'all') {
			$this->db->where('inv.trans_location', $inputs['location_id']);
		}
		$this->db->group_by('i.category');
		
		$data = array();
		foreach($this->db->get()->result_array() as $row) {
			$data[to_upper($row['item_category'])] = $row['delta'];
		}
		return $data;
	}

	public function _getData(array $inputs,$filter)
	{	
		debug_log('--> Begin '.__FUNCTION__);
		
		$data = array();
		$data['summary'] = [];
		
		// 1. Current Stock (End Quantity from item_quantities, just to get categories and current state)
	    $this->db->select('items.category, SUM(item_quantities.quantity) AS end_quantity_current, stock_locations.location_id');
        $this->db->from('items AS items');
        $this->db->join('item_quantities AS item_quantities', 'items.item_id = item_quantities.item_id');
        $this->db->join('stock_locations AS stock_locations', 'item_quantities.location_id = stock_locations.location_id');
        $this->db->where('items.deleted', 0);
        $this->db->where('stock_locations.deleted', 0);
        $this->db->where_in('items.category', $filter);

		if(isset($inputs['location_id']) && $inputs['location_id'] != 'all') {
			$this->db->where('stock_locations.location_id', $inputs['location_id']);
		}
        $this->db->group_by('items.category');
        $this->db->order_by('items.category');
        
        $tmp = $this->db->get()->result_array();

		// 2. Deltas for exact historical stock
		$delta_begin = $this->_getInventoryDelta($inputs, $filter, true); // delta < fromDate
		$delta_end = $this->_getInventoryDelta($inputs, $filter, false); // delta <= toDate
		
		// 3. Sales and Receives in Period
		$sales = $this->_getSalesToday($inputs, $filter);
		$receives = $this->_getReceive($inputs, $filter);

		$_sales = array();
		foreach($sales as $s) {
            $_sales[to_upper($s['item_category'])] = $s['quantity'];
        }

		$_receives = array();
		foreach($receives as $r) {
            $_receives[to_upper($r['item_category'])] = $r['quantity'];
        }

		foreach($tmp as $k=>$v)
		{
			$cat = to_upper($v['category']);
			
			$v['begin_quantity'] = isset($delta_begin[$cat]) ? $delta_begin[$cat] : 0;
			$v['end_quantity'] = isset($delta_end[$cat]) ? $delta_end[$cat] : 0;
			$v['sale_quantity'] = isset($_sales[$cat]) ? $_sales[$cat] : 0;
			$v['receive_quantity'] = isset($_receives[$cat]) ? $_receives[$cat] : 0;
			
			// Tính toán số lượng điều chỉnh: Tồn cuối = Đầu + Nhập - Xuất + Điều Chỉnh => Điều Chỉnh = Cuối - Đầu - Nhập + Xuất
			$v['adjustment_quantity'] = $v['end_quantity'] - ($v['begin_quantity'] + $v['receive_quantity'] - $v['sale_quantity']);
			
			$data['summary'][$k] = $v;
		}
		
        $data['details'] = array();
		
        foreach($data['summary'] as $key=>$value)
        {			
			$sql = "SELECT items.name, items.item_number, COALESCE(receivings_items.total_received, 0) AS total_received, COALESCE(sales_items.total_sold, 0) AS total_sold, item_quantities.quantity, items.reorder_level, stock_locations.location_name, items.cost_price, items.unit_price, (items.unit_price * item_quantities.quantity) AS sub_total_value
                FROM ospos_items AS items
                JOIN ospos_item_quantities AS item_quantities ON items.item_id = item_quantities.item_id
                JOIN ospos_stock_locations AS stock_locations ON item_quantities.location_id = stock_locations.location_id
                LEFT JOIN (
                    SELECT item_id, COALESCE(SUM(quantity_purchased), 0) AS total_sold
                    FROM ospos_sales_items
                    WHERE sale_id IN (SELECT sale_id FROM ospos_sales WHERE DATE(sale_time) BETWEEN ? AND ?) ";
            
            if(isset($inputs['location_id']) && $inputs['location_id'] != 'all') {
                $sql .= " AND item_location = " . $this->db->escape($inputs['location_id']);
            }
            $sql .= " GROUP BY item_id
                ) AS sales_items ON sales_items.item_id = items.item_id
				LEFT JOIN (
                    SELECT item_id, COALESCE(SUM(quantity_purchased), 0) AS total_received
                    FROM ospos_receivings_items
                    WHERE receiving_id IN (SELECT receiving_id FROM ospos_receivings WHERE DATE(receiving_time) BETWEEN ? AND ?) ";
            if(isset($inputs['location_id']) && $inputs['location_id'] != 'all') {
                $sql .= " AND item_location = " . $this->db->escape($inputs['location_id']);
            }
            $sql .= " GROUP BY item_id
                ) AS receivings_items ON receivings_items.item_id = items.item_id
                WHERE items.deleted = 0
                    AND stock_locations.deleted = 0
                    AND items.category = ? ";
            if(isset($inputs['location_id']) && $inputs['location_id'] != 'all') {
                $sql .= " AND stock_locations.location_id = " . $this->db->escape($inputs['location_id']);
            }
            $sql .= " GROUP BY items.item_id
                ORDER BY total_sold DESC";

        	$query = $this->db->query($sql, [$inputs['fromDate'], $inputs['toDate'], $inputs['fromDate'], $inputs['toDate'], $value['category']]);
            $data['details'][$key] = $query->result_array();
        }
		
		debug_log('End '.__FUNCTION__);
        return $data;

	}

	public function __getData(array $inputs,$filter)
	{	
		return $this->_getData($inputs, $filter);
	}

	public function _getDetailData(array $inputs,$category='')
	{	
        $data['details'] = array();
		
		$sql = "SELECT items.name, items.item_number, COALESCE(receivings_items.total_received, 0) AS total_received, COALESCE(sales_items.total_sold, 0) AS total_sold, item_quantities.quantity, items.reorder_level, stock_locations.location_name, items.cost_price, items.unit_price, (items.unit_price * item_quantities.quantity) AS sub_total_value
			FROM ospos_items AS items
			JOIN ospos_item_quantities AS item_quantities ON items.item_id = item_quantities.item_id
			JOIN ospos_stock_locations AS stock_locations ON item_quantities.location_id = stock_locations.location_id
			LEFT JOIN (
				SELECT item_id, COALESCE(SUM(quantity_purchased), 0) AS total_sold
				FROM ospos_sales_items
				WHERE sale_id IN (SELECT sale_id FROM ospos_sales WHERE DATE(sale_time) BETWEEN ? AND ?) ";
        if(isset($inputs['location_id']) && $inputs['location_id'] != 'all') {
            $sql .= " AND item_location = " . $this->db->escape($inputs['location_id']);
        }
        $sql .= " GROUP BY item_id
			) AS sales_items ON sales_items.item_id = items.item_id
			LEFT JOIN (
				SELECT item_id, COALESCE(SUM(quantity_purchased), 0) AS total_received
				FROM ospos_receivings_items
				WHERE receiving_id IN (SELECT receiving_id FROM ospos_receivings WHERE DATE(receiving_time) BETWEEN ? AND ?) ";
        if(isset($inputs['location_id']) && $inputs['location_id'] != 'all') {
            $sql .= " AND item_location = " . $this->db->escape($inputs['location_id']);
        }
        $sql .= " GROUP BY item_id
			) AS receivings_items ON receivings_items.item_id = items.item_id
			WHERE items.deleted = 0
				AND stock_locations.deleted = 0
				AND items.category = ? ";
        if(isset($inputs['location_id']) && $inputs['location_id'] != 'all') {
            $sql .= " AND stock_locations.location_id = " . $this->db->escape($inputs['location_id']);
        }
		$sql .= " GROUP BY items.item_id
			ORDER BY total_sold DESC";

		$query = $this->db->query($sql, [$inputs['fromDate'], $inputs['toDate'],$inputs['fromDate'], $inputs['toDate'],$category]);

		$data['details'] = $query->result_array();
        return $data;
	}

	public function _getSalesToday($inputs,$filter)
	{
		$this->db->select('s.sale_time, SUM(si.quantity_purchased) AS quantity, i.category as item_category');
        $this->db->from('sales_items AS si');
        $this->db->join('sales AS s', 'si.sale_id = s.sale_id');
		$this->db->join('items AS i', 'si.item_id = i.item_id');
        $this->db->where_in('i.category', $filter);
		$this->db->where('DATE(s.sale_time) >=', $inputs['fromDate']);
        $this->db->where('DATE(s.sale_time) <=', $inputs['toDate']);
        
        if(isset($inputs['location_id']) && $inputs['location_id'] != 'all') {
			$this->db->where('si.item_location', $inputs['location_id']);
		}
        $this->db->group_by('i.category');
        $data = $this->db->get()->result_array();
        return $data;
	}

	public function _getReceive($inputs,$filter)
	{
		$this->db->select('r.receiving_time, SUM(ri.quantity_purchased) AS quantity, i.category as item_category');
        $this->db->from('receivings_items AS ri');
        $this->db->join('receivings AS r', 'ri.receiving_id = r.receiving_id');
		$this->db->join('items AS i', 'ri.item_id = i.item_id');
		$this->db->where_in('i.category', $filter);
		$this->db->where('DATE(r.receiving_time) >=', $inputs['fromDate']);
        $this->db->where('DATE(r.receiving_time) <=', $inputs['toDate']);
        
        if(isset($inputs['location_id']) && $inputs['location_id'] != 'all') {
			$this->db->where('ri.item_location', $inputs['location_id']);
		}
        $this->db->group_by('i.category');
        $data = $this->db->get()->result_array();
        return $data;
	}
}
?>