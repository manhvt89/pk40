<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once("Report.php");

class Inventory_thuoc extends Report
{
	function __construct()
	{
		parent::__construct();
	}

	public function getDataColumns()
	{
		return array(

            'summary' => array(
                array('id' => $this->lang->line('reports_sale_id')),
                array('cat' => $this->lang->line('reports_item_cat')),
                array('quantity' => $this->lang->line('reports_quantity')),
            ),
            'details' => array(
                $this->lang->line('reports_item_name'),
                $this->lang->line('reports_item_number'),
                $this->lang->line('reports_quantity'),
                $this->lang->line('reports_reorder_level'),
                $this->lang->line('reports_stock_location'),
                $this->lang->line('reports_cost_price'),
                $this->lang->line('reports_unit_price'),
                $this->lang->line('reports_sub_total_value'))
		            );
	}

	public function getData(array $inputs)
	{	
        $filter = $this->config->item('other_filter'); //define in app.php
	    $this->db->select('items.category, SUM(item_quantities.quantity) AS quantity, stock_locations.location_id');
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
        $data['summary'] = $this->db->get()->result_array();
        $data['details'] = array();
        foreach($data['summary'] as $key=>$value)
        {
            $this->db->select('items.name, items.item_number, item_quantities.quantity, items.reorder_level, stock_locations.location_name, items.cost_price, items.unit_price, (items.cost_price * item_quantities.quantity) AS sub_total_value');
            $this->db->from('items AS items');
            $this->db->join('item_quantities AS item_quantities', 'items.item_id = item_quantities.item_id');
            $this->db->join('stock_locations AS stock_locations', 'item_quantities.location_id = stock_locations.location_id');
            $this->db->where('items.deleted', 0);
            $this->db->where('stock_locations.deleted', 0);
            $this->db->where('items.category', $value['category']);
            $this->db->where('stock_locations.location_id', $value['location_id']);
            $this->db->order_by('items.name');
            $data['details'][$key] = $this->db->get()->result_array();
        }
        return $data;

	}

	/**
	 * calculates the total value of the given inventory summary by summing all sub_total_values (see Inventory_summary::getData())
	 * 
	 * @param array $inputs expects the reports-data-array which Inventory_summary::getData() returns
	 * @return array
	 */
	public function getSummaryData(array $inputs)
	{
		$return = array('total_inventory_value' => 0);

		foreach($inputs as $input)
		{
			$return['total_inventory_value'] += $input['sub_total_value'];
		}

		return $return;
	}

	/**
	 * returns the array for the dropdown-element item-count in the form for the inventory summary-report
	 * 
	 * @return array
	 */
	public function getItemCountDropdownArray()
	{
		return array('all' => $this->lang->line('reports_all'),
					'zero_and_less' => $this->lang->line('reports_zero_and_less'),
					'more_than_zero' => $this->lang->line('reports_more_than_zero'));
	}


	public function _getDataColumns()
	{
		return [

            'summary' => array(
				array('id' => $this->lang->line('reports_sale_id')),
				array('cat' => 'Tên thuốc'),
				array('begin_quantity' => 'Đầu kỳ','align'=>'right'),
				array('receive_quantity'=>'Nhập','align'=>'right'),
				array('sale_quantity'=>'Xuất','align'=>'right'),
				array('end_quantity' => 'Cuối kỳ','align'=>'right'),
				
			),
            'details' => [
				['item_number'=>$this->lang->line('reports_item_number')],
                ['name'=>$this->lang->line('reports_item_name')],
				['total_received'=>'Số lượng nhập','align'=>'right'],
                ['total_sold'=>'Số lượng xuất','align'=>'right'],
                ['quantity'=>'Tồn kho','align'=>'right'],
                ['cost_price'=>$this->lang->line('reports_cost_price'),'align'=>'right'], //Giá vốn
            	['unit_price'=>$this->lang->line('reports_unit_price'),'align'=>'right'],
                ['sub_total'=>$this->lang->line('reports_sub_total_value'),'align'=>'right']
		            ]
				];
	}

	public function _getData(array $inputs)
	{	
        $filter = $this->config->item('filter_other'); //define in app.php
		//var_dump($filter);die();
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
		if(empty($sales))
		{

			foreach($tmp as $k=>$v)
			{
				$v['sale_quantity'] = 0;
				$data['summary'][] = $v;
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
				$data['summary'][] = $v;
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
        foreach($data['summary'] as $key=>$value)
        {
			/*
            $this->db->select('items.name, items.item_number, COALESCE(SUM(sales_items.quantity_purchased), 0) AS total_sold, item_quantities.quantity, items.reorder_level, stock_locations.location_name, items.cost_price, items.unit_price, (items.unit_price * item_quantities.quantity) AS sub_total_value');
            $this->db->from('items AS items');
            $this->db->join('item_quantities AS item_quantities', 'items.item_id = item_quantities.item_id');
            $this->db->join('stock_locations AS stock_locations', 'item_quantities.location_id = stock_locations.location_id');
			$this->db->join('(SELECT item_id, COALESCE(SUM(quantity_purchased), 0) AS total_sold FROM ospos_sales_items WHERE sale_id IN (SELECT sale_id FROM ospos_sales WHERE sale_time >= ? AND sale_time < ?) GROUP BY item_id) AS sales_items', 'sales_items.item_id = items.item_id', 'left');
        
			//$this->db->join('sales_items AS sales_items', 'sales_items.item_id = items.item_id','left');
			//$this->db->join('(SELECT * FROM ospos_sales AS s WHERE DATE(s.sale_time) BETWEEN "'.$this->db->escape($inputs['fromDate']).'" AND "'.$this->db->escape($inputs['toDate']).'") AS sales', 'sales.sale_id = sales_items.sale_id', 'left');
            $this->db->where('items.deleted', 0);
            $this->db->where('stock_locations.deleted', 0);
            $this->db->where('items.category', $value['category']);
            $this->db->where('stock_locations.location_id', $value['location_id']);
			//$this->db->where('DATE(sales.sale_time) BETWEEN '. $this->db->escape($inputs['fromDate']).' AND '.$this->db->escape($inputs['toDate']));
			$this->db->group_by('items.item_id');
            $this->db->order_by('items.name1');
			*/
			
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
		//var_dump($data);
        return $data;

	}
	// Get total sale from fromDate to Today 
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
	//Lây phát sinh tăng (nhập hàng); phát sinh giảm bán hàng (xuất hàng)
	public function _getAction($inputs,$filter)
	{
		$sql = 'SELECT items.item_id, COALESCE(SUM(receivings_items.total_received),0) AS total_received, COALESCE(SUM(sales_items.total_sold),0) AS total_sold, items.category as item_category
				FROM ospos_items AS items
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
				WHERE items.category IN ? 
				GROUP BY  items.category
		';
       $query = $this->db->query($sql, [$inputs['fromDate'], $inputs['toDate'],$inputs['fromDate'], $inputs['toDate'],$filter]);
	   $data = $query->result_array();
	   return $data;
	}
}
?>