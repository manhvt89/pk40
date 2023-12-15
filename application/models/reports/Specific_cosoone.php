<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once("Report.php");

class Specific_cosoone extends Report
{
	function __construct()
	{
		parent::__construct();
	}
	
	public function create(array $inputs)
	{
		//Create our temp tables to work with the data in our report
		$this->Sale->create_temp_table($inputs);
	}
	
	public function getDataColumns()
	{
        return array(
            'summary' => [
                ['id' => '#','align'=>'center'],
                ['sale_date' => 'Ngày tháng'],
                ['product_name' => 'Tên sản phẩm','footer-formatter'=>'iformatter'],
                ['quantity' => 'Số lượng xuất','align'=>'right','formatter'=>'currencyFormatter','footer-formatter'=>'totalformatter'],
                ['item_cost_price' => 'Giá','align'=>'right', 'formatter'=>'currencyFormatter'],
                ['total_amount' => 'Thành tiền', 'sorter' => 'number_sorter','align'=>'right','footer-formatter'=>'totalformatter',
                    'formatter'=>'currencyFormatter',
                'visible'=>'true'],
            ]
        );
    
	}
	
	public function getData(array $inputs)
	{
        $account_number = $this->config->item('config_partner');
        $_oCustomer = $this->Customer->get_info_by_account_number($account_number);
        //var_dump($_oCustomer);
		$this->db->select('items.name AS product_name, 
                            sales_items.quantity_purchased AS quantity,
                            sales_items.item_cost_price,
                            ROUND(sales_items.item_cost_price * sales_items.quantity_purchased, 0) AS total_amount, 
                            sales.sale_time AS sale_date');
        $this->db->from('sales_items AS sales_items');
        $this->db->join('sales AS sales', 'sales_items.sale_id = sales.sale_id');
        $this->db->join('items AS items', 'sales_items.item_id = items.item_id');
        $this->db->where('sales.customer_id', $_oCustomer->person_id);
        $this->db->where('DATE(sales.sale_time) BETWEEN ' . $this->db->escape($inputs['start_date']) . ' AND ' . $this->db->escape($inputs['end_date']));
		
       // $this->db->where('sales.sale_time >=', $start_date);
       // $this->db->where('sales.sale_time <=', $end_date);
        $this->db->order_by('sales.sale_time desc');

		$this->db->order_by('sale_date');

		$data = array();
		$data['summary'] = $this->db->get()->result_array();
        $data['info'] = $_oCustomer;
		$data['details'] = array();

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
		$this->db->where('customer_id', $inputs['customer_id']);

		if ($inputs['sale_type'] == 'sales')
        {
            $this->db->where('quantity_purchased > 0');
        }
        elseif ($inputs['sale_type'] == 'returns')
        {
            $this->db->where('quantity_purchased < 0');
        }

		return $this->db->get()->row_array();
	}
}
?>