<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once("Report.php");

class Inventory_frame extends Report
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
        $filter = $this->config->item('filter'); //define in app.php
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
				array('cat' => 'Loại mắt'),
				array('begin_quantity' => 'Đầu kỳ'),
				array('receive_quantity'=>'Nhập'),
				array('sale_quantity'=>'Bán'),
				array('end_quantity' => 'Cuối kỳ'),
				
			),
            'details' => [
                'name'=>$this->lang->line('reports_item_name'),
                'item_number'=>$this->lang->line('reports_item_number'),
                'quantity'=>$this->lang->line('reports_quantity'),
                'reoder_level'=>$this->lang->line('reports_reorder_level'),
                'location'=>$this->lang->line('reports_stock_location'),
                'cost_price'=>$this->lang->line('reports_cost_price'), //Giá vốn
            	'unit_price'=>$this->lang->line('reports_unit_price'),
                'sub_total'=>$this->lang->line('reports_sub_total_value')
		            ]
				];
	}

	public function _getData(array $inputs)
	{	
        $filter = $this->config->item('filter'); //define in app.php
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
        $tmp = $this->db->get()->result_array();

		$sales = $this->_getSalesToday($inputs);
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

		if (empty($data['summary']))
			$data['summary'] = array();

		$receives = $this->_getReceive($inputs);
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

	public function _getSalesToday($inputs)
	{
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
	}

	public function _getReceive($inputs)
	{
		$filter = $this->config->item('filter'); //define in app.php
		$this->db->select('r.receiving_time, SUM(ri.quantity_purchased) AS quantity, i.category as item_category');
        $this->db->from('receivings_items AS ri');
        $this->db->join('receivings AS r', 'ri.receiving_id = r.receiving_id');
		$this->db->join('items AS i', 'ri.item_id = i.item_id');
		$this->db->where_in('i.category', $filter);
		$this->db->where('DATE(r.receiving_time) BETWEEN '. $this->db->escape($inputs['fromDate']).' AND '.$this->db->escape($inputs['toDate']));
        $this->db->group_by('i.category');
        $this->db->order_by('i.category');
        $data = array();
        $data = $this->db->get()->result_array();
        return $data;
	}
}
?>