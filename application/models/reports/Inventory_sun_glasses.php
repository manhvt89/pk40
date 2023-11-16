<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once("Report.php");

class Inventory_sun_glasses extends Report
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
				array('cat' => 'Kính thời trang'),
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
}
?>