<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once("Report.php");

class Reports_detailed_sales extends Report
{
	function __construct()
	{
		parent::__construct();
	}

	public function create(array $inputs)
	{
		//Create our temp tables to work with the data in our report

	}

	public function is_exist($code)
    {
        $this->db->from('reports_detail_sales');
        $this->db->where('code', $code);
        return ($this->db->get()->num_rows() == 1);
    }
	public function insert($report_detail_sale)
    {
        if($this->is_exist($report_detail_sale['code']))
        {
            return 1;
        }else{
            if($this->db->insert('reports_detail_sales', $report_detail_sale))
            {
                return 2;
            }else{
                return 0;
            }
        }
    }

    public function getMax_code()
    {
        return $this->db->select('code')->order_by('code','desc')->limit(1)->get('reports_detail_sales')->row('code');
    }

	public function getDataColumns()
	{
        $CI =& get_instance();
        $person_id = $CI->session->userdata('person_id');
        if($CI->Employee->has_grant('reports_sales-accounting', $person_id)) {
            return array(
                'summary' => array(
                    array('id' => $this->lang->line('reports_sale_id')),
                    array('sale_date' => $this->lang->line('reports_date')),
                    array('quantity' => $this->lang->line('reports_quantity')),
                    array('employee_name' => $this->lang->line('reports_sold_by')),
                    array('customer_name' => $this->lang->line('reports_sold_to')),
                    array('subtotal' => $this->lang->line('reports_subtotal'), 'sorter' => 'number_sorter'),
                    array('total' => $this->lang->line('reports_total'), 'sorter' => 'number_sorter'),
                    array('tax' => $this->lang->line('reports_tax'), 'sorter' => 'number_sorter'),
                    array('cost' => $this->lang->line('reports_cost'), 'sorter' => 'number_sorter'),
                    array('profit' => $this->lang->line('reports_profit'), 'sorter' => 'number_sorter'),
                    array('payment_type' => $this->lang->line('sales_amount_tendered')),
                    array('comment' => $this->lang->line('reports_comments'))),
                'details' => array(
                    $this->lang->line('reports_name'),
                    $this->lang->line('reports_category'),
                    $this->lang->line('reports_serial_number'),
                    $this->lang->line('reports_description'),
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
                    array('total' => $this->lang->line('reports_total'), 'sorter' => 'number_sorter'),
                    array('payment_type' => $this->lang->line('sales_amount_tendered')),
                    array('comment' => $this->lang->line('reports_comments'))),
                'details' => array(
                    $this->lang->line('reports_name'),
                    $this->lang->line('reports_category'),
                    $this->lang->line('reports_serial_number'),
                    $this->lang->line('reports_description'),
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
		$this->db->select('code as sale_id, 
		                    sale_time as sale_date, 
		                    amount as items_purchased, 
		                    saler as employee_name, 
		                    buyer as customer_name,
		                    subtotal,tax, total,cost,profit,
		                    paid_customer as payment_type,
		                    comment,items');
		$this->db->from('reports_detail_sales');

		/*if($inputs['location_id'] != 'all')
		{
			$this->db->where('item_location', $inputs['location_id']);
		}*/

		if($inputs['sale_type'] == 'sales')
        {
            $this->db->where('amount > 0');
        }
        elseif($inputs['sale_type'] == 'returns')
        {
            $this->db->where('amount < 0');
        }
        $this->db->where("DATE_FORMAT(sale_time,'%Y-%m-%d') >=" ,$inputs['start_date']);
        $this->db->where("DATE_FORMAT(sale_time,'%Y-%m-%d') <=" ,$inputs['end_date']);
        //$where = 'WHERE DATE(sales.sale_time) BETWEEN ' . $this->db->escape($inputs['start_date']) . ' AND ' . $this->db->escape($inputs['end_date']);


        $this->db->order_by('sale_time');

		$data = array();
		$data['summary'] = $this->db->get()->result_array();
		$data['details'] = array();
        //var_dump($data['summary']);die();

		foreach($data['summary'] as $key=>$value)
		{

		    $data['details'][$key] = json_decode($value['items'],true);
            //var_dump(json_decode($value['items'],true));die();

		}
		//var_dump($data);
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
		$this->db->from('reports_detail_sales');

		if($inputs['location_id'] != 'all')
		{
		 	$this->db->where('item_location', $inputs['location_id']);
		}

		if($inputs['sale_type'] == 'sales')
        {
            $this->db->where('amount > 0');
        }
        elseif($inputs['sale_type'] == 'returns')
        {
            $this->db->where('amount < 0');
        }
        $this->db->where("DATE_FORMAT(sale_time,'%Y-%m-%d') >" ,$inputs['start_date']);
        $this->db->where("DATE_FORMAT(sale_time,'%Y-%m-%d') <" ,$inputs['end_date']);

		return $this->db->get()->row_array();
	}
}
?>