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

	// Returns the column names used for the report
	public abstract function getDataColumns();
	
	// Returns all the data to be populated into the report
	public abstract function getData(array $inputs);
	
	// Returns key=>value pairing of summary data for the report
	public abstract function getSummaryData(array $inputs);

	public function _getData(array $inputs,$filter)
	{	
	    $this->db->select('items.category, SUM(item_quantities.quantity) AS end_quantity, stock_locations.location_id');
        $this->db->from('items AS items');
        $this->db->join('item_quantities AS item_quantities', 'items.item_id = item_quantities.item_id');
        $this->db->join('stock_locations AS stock_locations', 'item_quantities.location_id = stock_locations.location_id');
        $this->db->where('items.deleted', 0);
        $this->db->where('stock_locations.deleted', 0);
        $this->db->where_in('items.category', $filter);

		// should be corresponding to values Inventory_summary::getItemCountDropdownArray() returns...

		if($inputs['location_id'] != 'all')
		{
			$this->db->where('stock_locations.location_id', $inputs['location_id']);
		}
        $this->db->group_by('items.category');
        $this->db->order_by('items.category');

        $data = array();
		$data['summary'] = [];
        $tmp = $this->db->get()->result_array();
		//Tính toán đầu kỳ A (fromDate)
		$_aA = $inputs;
		$_aA['toDate'] = date('Y/m/d');
		//$this->_getAction($_aA,$filter); chưa dùng
		$sales = $this->_getSalesToday($_aA,$filter); // Lây total sale từ ngày bắt đầu đến hiện tại;
		//var_dump($sales);
		debug_log($sales);
		if(empty($sales))
		{

			foreach($tmp as $k=>$v)
			{
				$v['sale_quantity'] = 0;
				$data['summary'][$k] = $v;
			}
			
		} else {
			$_sales = array();
			foreach($sales as $_k=>$_v)
			{
				$_sales[$_v['item_category']] = $_v['quantity'];
			}
			foreach($tmp as $k=>$v)
			{
				if(isset($_sales[$v['category']]))
				{
					$v['sale_quantity'] = $_sales[$v['category']];
				} else{
					$v['sale_quantity'] = 0;
				}
				$data['summary'][$k] = $v;
			}
		}

		$receives = $this->_getReceive($_aA,$filter); //Lây total Receive từ fromDate to Today.
		if(empty($receives))
		{
			foreach($data['summary'] as $k=>$v)
			{
				$v['receive_quantity'] = 0;
				$data['summary'][$k] = $v;
			}
		} else{
			$_receives = array();
			foreach($receives as $k=>$v)
			{
				$_receives[$v['item_category']] = $v['quantity'];
			}

			foreach($data['summary'] as $k=>$v)
			{
				if(isset($_receives[$v['category']]))
				{
					$v['receive_quantity'] = $_receives[$v['category']];
				} else{
					$v['receive_quantity'] = 0;
				}
				$data['summary'][$k] = $v;
			}

		}

		//Tính toán cuối kỳ B - tại thời điểm toDate
		$_aB = $inputs;
		// Chuyển đổi chuỗi ngày thành đối tượng DateTime
		$_dateTimeB = DateTime::createFromFormat('Y/m/d', $inputs['toDate']);

		// Thêm 1 ngày
		$_nextDay = $_dateTimeB->modify('+1 day');

		// Lấy ngày tiếp theo dưới dạng chuỗi
		$nextDayString = $_nextDay->format('Y/m/d');
		$_aB['fromDate'] = $nextDayString;
		$_aB['toDate'] = date('Y/m/d');
		$sales = $this->_getSalesToday($_aB,$filter); // Lây total sale từ ngày toDate bắt đầu đến hiện tại;
		//var_dump($sales);
		if(empty($sales))
		{

			foreach($data['summary'] as $k=>$v)
			{
				$v['b_sale_quantity'] = 0;
				$data['summary'][$k] = $v;
			}
			
		} else {
			$_sales = array();
			foreach($sales as $k=>$v)
			{
				$_sales[$v['item_category']] = $v['quantity'];
			}
			foreach($data['summary'] as $k=>$v)
			{
				if(isset($_sales[$v['category']]))
				{
					$v['b_sale_quantity'] = $_sales[$v['category']];
				} else{
					$v['b_sale_quantity'] = 0;
				}
				$data['summary'][$k] = $v;
			}
		}

		$receives = $this->_getReceive($_aB,$filter); //Lây total Receive từ fromDate to Today.
		if(empty($receives))
		{
			foreach($data['summary'] as $k=>$v)
			{
				$v['b_receive_quantity'] = 0;
				$data['summary'][$k] = $v;
			}
		} else{
			$_receives = array();
			foreach($receives as $k=>$v)
			{
				$_receives[$v['item_category']] = $v['quantity'];
			}

			foreach($data['summary'] as $k=>$v)
			{
				if(isset($_receives[$v['category']]))
				{
					$v['b_receive_quantity'] = $_receives[$v['category']];
				} else{
					$v['b_receive_quantity'] = 0;
				}
				$data['summary'][$k] = $v;
			}

		}

		//var_dump($data['summary']);
        $data['details'] = array();
		
        foreach($data['summary'] as $key=>$value)
        {			
			$sql = 'SELECT items.name, items.item_number, COALESCE(receivings_items.total_received, 0) AS total_received, COALESCE(sales_items.total_sold, 0) AS total_sold, item_quantities.quantity, items.reorder_level, stock_locations.location_name, items.cost_price, items.unit_price, (items.unit_price * item_quantities.quantity) AS sub_total_value
                FROM ospos_items AS items
                JOIN ospos_item_quantities AS item_quantities ON items.item_id = item_quantities.item_id
                JOIN ospos_stock_locations AS stock_locations ON item_quantities.location_id = stock_locations.location_id
                LEFT JOIN (
                    SELECT item_id, COALESCE(SUM(quantity_purchased), 0) AS total_sold
                    FROM ospos_sales_items
                    WHERE sale_id IN (SELECT sale_id FROM ospos_sales WHERE DATE(sale_time) BETWEEN ? AND ?)
                    GROUP BY item_id
                ) AS sales_items ON sales_items.item_id = items.item_id
				LEFT JOIN (
                    SELECT item_id, COALESCE(SUM(quantity_purchased), 0) AS total_received
                    FROM ospos_receivings_items
                    WHERE receiving_id IN (SELECT receiving_id FROM ospos_receivings WHERE DATE(receiving_time) BETWEEN ? AND ?)
                    GROUP BY item_id
                ) AS receivings_items ON receivings_items.item_id = items.item_id
                WHERE items.deleted = 0
                    AND stock_locations.deleted = 0
                    AND items.category = ?
                    AND stock_locations.location_id = 1
                    
                GROUP BY items.item_id
                ORDER BY total_sold DESC';

        		$query = $this->db->query($sql, [$inputs['fromDate'], $inputs['toDate'],$inputs['fromDate'], $inputs['toDate'],$value['category']]);

            	$data['details'][$key] = $query->result_array();
        }
		
		var_dump($data['summary']);
        return $data;

	}
	/*
	** Sử dụng cho mắt kính
	**/
	public function __getData(array $inputs,$filter)
	{	
	    $this->db->select('items.category, SUM(item_quantities.quantity) AS end_quantity, stock_locations.location_id');
        $this->db->from('items AS items');
        $this->db->join('item_quantities AS item_quantities', 'items.item_id = item_quantities.item_id');
        $this->db->join('stock_locations AS stock_locations', 'item_quantities.location_id = stock_locations.location_id');
        $this->db->where('items.deleted', 0);
        $this->db->where('stock_locations.deleted', 0);
        $this->db->where_in('items.category', $filter);

		// should be corresponding to values Inventory_summary::getItemCountDropdownArray() returns...

		if($inputs['location_id'] != 'all')
		{
			$this->db->where('stock_locations.location_id', $inputs['location_id']);
		}
        $this->db->group_by('items.category');
        $this->db->order_by('items.category');

        $data = array();
		$data['summary'] = [];
        $tmp = $this->db->get()->result_array();
		//Tính toán đầu kỳ A (fromDate)
		$_aA = $inputs;
		$_aA['toDate'] = date('Y/m/d');
		//$this->_getAction($_aA,$filter); chưa dùng
		$sales = $this->_getSalesToday($_aA,$filter); // Lây total sale từ ngày bắt đầu đến hiện tại;
		//var_dump($sales);
		if(empty($sales))
		{

			foreach($tmp as $k=>$v)
			{
				$v['sale_quantity'] = 0;
				$data['summary'][$k] = $v;
			}
			
		} else {
			$_sales = array();
			foreach($sales as $k=>$v)
			{
				$_sales[$v['item_category']] = $v['quantity'];
			}
			foreach($tmp as $k=>$v)
			{
				if(isset($_sales[$v['category']]))
				{
					$v['sale_quantity'] = $_sales[$v['category']];
				} else{
					$v['sale_quantity'] = 0;
				}
				$data['summary'][$k] = $v;
			}
		}

		$receives = $this->_getReceive($_aA,$filter); //Lây total Receive từ fromDate to Today.
		if(empty($receives))
		{
			foreach($data['summary'] as $k=>$v)
			{
				$v['receive_quantity'] = 0;
				$data['summary'][$k] = $v;
			}
		} else{
			$_receives = array();
			foreach($receives as $k=>$v)
			{
				$_receives[$v['item_category']] = $v['quantity'];
			}

			foreach($data['summary'] as $k=>$v)
			{
				if(isset($_receives[$v['category']]))
				{
					$v['receive_quantity'] = $_receives[$v['category']];
				} else{
					$v['receive_quantity'] = 0;
				}
				$data['summary'][$k] = $v;
			}

		}

		//Tính toán cuối kỳ B - tại thời điểm toDate, đầu kỳ của ngày tiếp theo toDate là toDate + 1;
		$_aB = $inputs;
		// Chuyển đổi chuỗi ngày thành đối tượng DateTime
		$_dateTimeB = DateTime::createFromFormat('Y/m/d', $inputs['toDate']);

		// Thêm 1 ngày
		$_nextDay = $_dateTimeB->modify('+1 day');

		// Lấy ngày tiếp theo dưới dạng chuỗi
		$nextDayString = $_nextDay->format('Y/m/d');
		$_aB['fromDate'] = $nextDayString;
		$_aB['toDate'] = date('Y/m/d');
		$sales = $this->_getSalesToday($_aB,$filter); // Lây total sale từ ngày toDate bắt đầu đến hiện tại;
		//var_dump($sales);
		if(empty($sales))
		{

			foreach($data['summary'] as $k=>$v)
			{
				$v['b_sale_quantity'] = 0;
				$data['summary'][$k] = $v;
			}
			
		} else {
			$_sales = array();
			foreach($sales as $k=>$v)
			{
				$_sales[$v['item_category']] = $v['quantity'];
			}
			foreach($data['summary'] as $k=>$v)
			{
				if(isset($_sales[$v['category']]))
				{
					$v['b_sale_quantity'] = $_sales[$v['category']];
				} else{
					$v['b_sale_quantity'] = 0;
				}
				$data['summary'][$k] = $v;
			}
		}

		$receives = $this->_getReceive($_aB,$filter); //Lây total Receive từ fromDate to Today.
		if(empty($receives))
		{
			foreach($data['summary'] as $k=>$v)
			{
				$v['b_receive_quantity'] = 0;
				$data['summary'][$k] = $v;
			}
		} else{
			$_receives = array();
			foreach($receives as $k=>$v)
			{
				$_receives[$v['item_category']] = $v['quantity'];
			}

			foreach($data['summary'] as $k=>$v)
			{
				if(isset($_receives[$v['category']]))
				{
					$v['b_receive_quantity'] = $_receives[$v['category']];
				} else{
					$v['b_receive_quantity'] = 0;
				}
				$data['summary'][$k] = $v;
			}

		}

		
        $data['details'] = array();
		/*
        foreach($data['summary'] as $key=>$value)
        {			
			$sql = 'SELECT items.name, items.item_number, COALESCE(receivings_items.total_received, 0) AS total_received, COALESCE(sales_items.total_sold, 0) AS total_sold, item_quantities.quantity, items.reorder_level, stock_locations.location_name, items.cost_price, items.unit_price, (items.unit_price * item_quantities.quantity) AS sub_total_value
                FROM ospos_items AS items
                JOIN ospos_item_quantities AS item_quantities ON items.item_id = item_quantities.item_id
                JOIN ospos_stock_locations AS stock_locations ON item_quantities.location_id = stock_locations.location_id
                LEFT JOIN (
                    SELECT item_id, COALESCE(SUM(quantity_purchased), 0) AS total_sold
                    FROM ospos_sales_items
                    WHERE sale_id IN (SELECT sale_id FROM ospos_sales WHERE DATE(sale_time) BETWEEN ? AND ?)
                    GROUP BY item_id
                ) AS sales_items ON sales_items.item_id = items.item_id
				LEFT JOIN (
                    SELECT item_id, COALESCE(SUM(quantity_purchased), 0) AS total_received
                    FROM ospos_receivings_items
                    WHERE receiving_id IN (SELECT receiving_id FROM ospos_receivings WHERE DATE(receiving_time) BETWEEN ? AND ?)
                    GROUP BY item_id
                ) AS receivings_items ON receivings_items.item_id = items.item_id
                WHERE items.deleted = 0
                    AND stock_locations.deleted = 0
                    AND items.category = ?
                    AND stock_locations.location_id = 1
                    
                GROUP BY items.item_id
                ORDER BY total_sold DESC';

        		$query = $this->db->query($sql, [$inputs['fromDate'], $inputs['toDate'],$inputs['fromDate'], $inputs['toDate'],$value['category']]);

            	$data['details'][$key] = $query->result_array();
        }
		*/
		//var_dump($data['summary']);
        return $data;

	}
	public function _getDetailData(array $inputs,$category='')
	{	
	    
        $data['details'] = array();
		
		$sql = 'SELECT items.name, items.item_number, COALESCE(receivings_items.total_received, 0) AS total_received, COALESCE(sales_items.total_sold, 0) AS total_sold, item_quantities.quantity, items.reorder_level, stock_locations.location_name, items.cost_price, items.unit_price, (items.unit_price * item_quantities.quantity) AS sub_total_value
			FROM ospos_items AS items
			JOIN ospos_item_quantities AS item_quantities ON items.item_id = item_quantities.item_id
			JOIN ospos_stock_locations AS stock_locations ON item_quantities.location_id = stock_locations.location_id
			LEFT JOIN (
				SELECT item_id, COALESCE(SUM(quantity_purchased), 0) AS total_sold
				FROM ospos_sales_items
				WHERE sale_id IN (SELECT sale_id FROM ospos_sales WHERE DATE(sale_time) BETWEEN ? AND ?)
				GROUP BY item_id
			) AS sales_items ON sales_items.item_id = items.item_id
			LEFT JOIN (
				SELECT item_id, COALESCE(SUM(quantity_purchased), 0) AS total_received
				FROM ospos_receivings_items
				WHERE receiving_id IN (SELECT receiving_id FROM ospos_receivings WHERE DATE(receiving_time) BETWEEN ? AND ?)
				GROUP BY item_id
			) AS receivings_items ON receivings_items.item_id = items.item_id
			WHERE items.deleted = 0
				AND stock_locations.deleted = 0
				AND items.category = ?
				AND stock_locations.location_id = 1
				
			GROUP BY items.item_id
			ORDER BY total_sold DESC';

			$query = $this->db->query($sql, [$inputs['fromDate'], $inputs['toDate'],$inputs['fromDate'], $inputs['toDate'],$category]);

			$data['details'] = $query->result_array();
	
		
		//var_dump($data['summary']);
        return $data;

	}

	public function _getSalesToday($inputs,$filter)
	{
		/*
		$filter = $this->config->item('filter'); //define in app.php//

		$this->db->select('s.sale_time, SUM(si.quantity_purchased) AS quantity, i.category as item_category');
        $this->db->from('sales_items AS si');
        $this->db->join('sales AS s', 'si.sale_id = s.sale_id');
		$this->db->join('items AS i', 'si.item_id = i.item_id');
        $this->db->where_in('i.category', $filter);
		$this->db->where('DATE(s.sale_time) BETWEEN '. $this->db->escape($inputs['fromDate']).' AND '.$this->db->escape($inputs['toDate']));
        $this->db->group_by('i.category');
        $this->db->order_by('i.category');
        $data = array();
        $data = $this->db->get()->result_array();
        return $data;
		*/
		
		$this->db->select('s.sale_time, SUM(si.quantity_purchased) AS quantity, i.category as item_category');
        $this->db->from('sales_items AS si');
        $this->db->join('sales AS s', 'si.sale_id = s.sale_id');
		$this->db->join('items AS i', 'si.item_id = i.item_id');
        $this->db->where_in('i.category', $filter);
		$this->db->where('DATE(s.sale_time) BETWEEN '. $this->db->escape($inputs['fromDate']).' AND '.$this->db->escape($inputs['toDate']));
        $this->db->group_by('i.category');
        $data = array();
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
		$this->db->where('DATE(r.receiving_time) BETWEEN '. $this->db->escape($inputs['fromDate']).' AND '.$this->db->escape($inputs['toDate']));
        $this->db->group_by('i.category');
        $data = array();
        $data = $this->db->get()->result_array();
        return $data;
	}
}
?>