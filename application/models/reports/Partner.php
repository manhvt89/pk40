<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once("Report.php");

class Partner extends Report
{
	function __construct()
	{
		parent::__construct();
	}

	public function getDataColumns()
	{
		return [];
	}

	public function getData(array $inputs)
	{	
        return [];
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

            'summary' => [
				array('id' => $this->lang->line('reports_sale_id')),
				array('ctv_code' => 'Mã CTV'),
				array('ctv_name' => 'Tên'),
				array('total' => 'Số đơn hàng','halign'=>'center', 'align'=>'right'),
				array('total_DT'=>'Tổng doanh thu','halign'=>'center', 'align'=>'right'),
				array('total_HH'=>'Hoa hồng','halign'=>'center', 'align'=>'right'),
			],
			'details' =>[
				array('id' => $this->lang->line('reports_sale_id')),
				array('datetime' => 'Ngày'),
				array('sale_name' => 'Tên khách hàng'),
				array('sale_code' => 'Mã đơn hàng'),
				array('DT'=>'Tổng doanh thu','halign'=>'center', 'align'=>'right'),
				array('HH'=>'Hoa hồng','halign'=>'center', 'align'=>'right'),
			]
		];
	}

	public function _getData(array $inputs)
	{	
       
	    $this->db->select('history_ctv.*,COUNT(history_ctv_id) as tt,SUM(payment_amount) as pm, SUM(comission_amount) as cm');
        $this->db->from('history_ctv');
		// should be corresponding to values Inventory_summary::getItemCountDropdownArray() returns...
		$this->db->where('DATE(FROM_UNIXTIME(created_time)) BETWEEN '. $this->db->escape($inputs['fromDate']).' AND '.$this->db->escape($inputs['toDate']));
        $this->db->group_by('history_ctv.ctv_id');
        $this->db->order_by('pm');

        $data = array();
        $data['summary'] = $this->db->get()->result_array();

        $data['details'] = array();
		
        foreach($data['summary'] as $key=>$value)
        {
            $this->db->select('history_ctv.*');
            $this->db->from('history_ctv as history_ctv');
			$this->db->where('ctv_id', $value['ctv_id']);
			$this->db->where('DATE(FROM_UNIXTIME(created_time)) BETWEEN '. $this->db->escape($inputs['fromDate']).' AND '.$this->db->escape($inputs['toDate']));
            $this->db->order_by('history_ctv.created_time');
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