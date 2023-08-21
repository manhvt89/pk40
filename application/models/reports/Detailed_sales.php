<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once("Report.php");

class Detailed_sales extends Report
{
	function __construct()
	{
        parent::__construct();
        $CI =& get_instance();
        $this->iLoggedIn_Id = $CI->session->userdata('person_id');
        $this->bLoggedIn_type = $CI->session->userdata('type');
	}

	public function create(array $inputs)
	{
		//Create our temp tables to work with the data in our report
		$this->Sale->create_temp_table($inputs);
	}

	public function getDataColumns()
	{
        $CI =& get_instance();
        //$person_id = $this->iLoggedIn_Id;
        if($CI->Employee->has_grant('reports_sales_accounting')) //Phân quyền cho kế toán
        {
            return array(
                'summary' => array(
                    array('id' => $this->lang->line('reports_sale_id')),
                    array('sale_date' => $this->lang->line('reports_date')),
                    array('quantity' => $this->lang->line('reports_quantity')),
                    array('employee_name' => $this->lang->line('reports_sold_by')),
                    array('customer_name' => $this->lang->line('reports_sold_to')),
                    array('subtotal' => $this->lang->line('reports_subtotal'), 'sorter' => 'number_sorter'),
                    array('total' => 'Tổng cộng tiền hàng', 'sorter' => 'number_sorter'),
                    array('tax' => $this->lang->line('reports_tax'), 'sorter' => 'number_sorter'),
                    array('cost' => $this->lang->line('reports_cost'), 'sorter' => 'number_sorter'),
                    array('profit' => $this->lang->line('reports_profit'), 'sorter' => 'number_sorter'),
                    array('payment_type' => $this->lang->line('sales_amount_tendered')),
                    array('comment' => $this->lang->line('reports_comments'))),
                'details' => array(
                    $this->lang->line('reports_item_number'),
                    $this->lang->line('reports_name'),
                    $this->lang->line('reports_category'),
                    $this->lang->line('reports_quantity'),
                    $this->lang->line('reports_subtotal'),
                    $this->lang->line('reports_tax'),
                    $this->lang->line('reports_total'),
                    $this->lang->line('reports_cost'),
                    $this->lang->line('reports_profit'),
                    $this->lang->line('reports_discount'))
            );
        }else{
            return array(
                'summary' => array(
                    array('id' => $this->lang->line('reports_sale_id')),
                    array('sale_date' => $this->lang->line('reports_date')),
                    array('quantity' => $this->lang->line('reports_quantity')),
                    array('employee_name' => $this->lang->line('reports_sold_by')),
                    array('customer_name' => $this->lang->line('reports_sold_to')),
                    array('total' => 'Tổng cộng tiền hàng', 'sorter' => 'number_sorter'),
                    array('payment_type' => 'Loại thanh toán'),
                    array('comment' => $this->lang->line('reports_comments'))),
                'details' => array(
                    $this->lang->line('reports_item_number'),
                    $this->lang->line('reports_name'),
                    $this->lang->line('reports_category'),
                    $this->lang->line('reports_quantity'),
                    $this->lang->line('reports_total'),
                    $this->lang->line('reports_discount'))
            );
        }
	}

	public function getDataBySaleId($sale_id)
	{
		$this->db->select('sale_id, sale_date, SUM(quantity_purchased) AS items_purchased, employee_name, customer_name, SUM(subtotal) AS subtotal, SUM(tax) AS tax, SUM(total) AS total, SUM(cost) AS cost, SUM(profit) AS profit, payment_type, comment');
		$this->db->from('sales_items_temp');
		$this->db->where('sale_id', $sale_id);

		return $this->db->get()->row_array();
	}

	public function getData(array $inputs)
	{
		$this->db->select('sale_id, sale_uuid ,kind, sale_time, sale_date, SUM(quantity_purchased) AS items_purchased, employee_name, customer_name, SUM(subtotal) AS subtotal, SUM(tax) AS tax, SUM(total) AS total, SUM(cost) AS cost, SUM(profit) AS profit, payment_type, comment');
		$this->db->from('sales_items_temp');

		if($inputs['location_id'] != 'all')
		{
			$this->db->where('item_location', $inputs['location_id']);
		}

		if($inputs['sale_type'] == 'sales')
        {
            $this->db->where('quantity_purchased > 0');
        }
        elseif($inputs['sale_type'] == 'returns')
        {
            $this->db->where('quantity_purchased < 0');
        }

        if(!empty($inputs['code']))
        {
            $this->db->where('sale_id >=', $inputs['code']);
        }

        if($this->bLoggedIn_type == 2)
        {
            $this->db->where('ctv_id=', $this->iLoggedIn_Id);
        }

		$this->db->group_by('sale_id');
		$this->db->order_by('sale_date');

		$data = array();
		$data['summary'] = $this->db->get()->result_array();
		$data['details'] = array();

		foreach($data['summary'] as $key=>$value)
		{
			$this->db->select('name, category, item_number, quantity_purchased, item_location, serialnumber, description, subtotal, tax, total, cost, profit, discount_percent');
			$this->db->from('sales_items_temp');
			$this->db->where('sale_id', $value['sale_id']);
			$data['details'][$key] = $this->db->get()->result_array();
		}

		return $data;
	}

	public function getSummaryData(array $inputs)
	{
        $CI =& get_instance();
        $person_id = $CI->session->userdata('person_id');
        if($CI->Employee->has_grant('reports_sales-accounting', $person_id)) {
            $this->db->select('SUM(subtotal) AS subtotal, SUM(tax) AS tax, SUM(total) AS total, SUM(cost) AS cost, SUM(profit) AS profit');
        }else{
            $this->db->select('SUM(total) AS total');
        }
		$this->db->from('sales_items_temp');

		if($inputs['location_id'] != 'all')
		{
		 	$this->db->where('item_location', $inputs['location_id']);
		}

		if($inputs['sale_type'] == 'sales')
        {
            $this->db->where('quantity_purchased > 0');
        }
        elseif($inputs['sale_type'] == 'returns')
        {
            $this->db->where('quantity_purchased < 0');
        }
        if($this->bLoggedIn_type == 2)
        {
            $this->db->where('ctv_id=', $this->iLoggedIn_Id);
        }

		return $this->db->get()->row_array();
	}
}
?>