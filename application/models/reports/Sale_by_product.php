<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once("Report.php");

class Sale_by_product extends Report
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
                ['product_name' => 'Tên sản phẩm','footer-formatter'=>'iformatter'],
				['item_number' => 'Mã sản phẩm','footer-formatter'=>'iformatter','visible'=>'false'],
				['category' => 'Danh mục','footer-formatter'=>'iformatter','visible'=>'true'],
                ['quantity' => 'Số lượng xuất','align'=>'right','formatter'=>'currencyFormatter','footer-formatter'=>'totalformatter','sortable'=>true],
                ['total_cost_amount' => 'Thành tiền giá vốn','align'=>'right', 'formatter'=>'currencyFormatter','sortable'=>true],
                ['total_revenue_amount' => 'Doanh thu','align'=>'right','footer-formatter'=>'totalformatter',
                    'formatter'=>'currencyFormatter', 'sortable'=>true,
                'visible'=>'true'],
				['total_ln' => 'Lợi nhuận','align'=>'right','footer-formatter'=>'totalformatter',
				'formatter'=>'currencyFormatter', 'sortable'=>true,
				'visible'=>'true'],
            ]
        );
    
	}
	
	public function getData(array $inputs)
	{
        
        $this->db->select('items.name AS product_name,  SUM(sales_items.quantity_purchased) AS quantity, items.item_number AS item_number,items.category AS category,
                      SUM(sales_items.item_cost_price * sales_items.quantity_purchased) AS total_cost_amount, 
                      SUM(((100 - sales_items.discount_percent)/100)*sales_items.item_unit_price * sales_items.quantity_purchased) AS total_revenue_amount, 
					  SUM(((100 - sales_items.discount_percent)/100)*sales_items.item_unit_price * sales_items.quantity_purchased) - SUM(sales_items.item_cost_price * sales_items.quantity_purchased) AS total_ln');
		$this->db->from('sales_items AS sales_items');
		$this->db->join('items AS items', 'sales_items.item_id = items.item_id');
		$this->db->join('sales AS sales', 'sales_items.sale_id = sales.sale_id');
		$this->db->where('DATE(sales.sale_time) BETWEEN '. $this->db->escape($inputs['start_date']) .' AND '. $this->db->escape($inputs['end_date']));
		$this->db->group_by('items.item_id');
		$this->db->order_by('category desc');

		$data = [];
		$data['summary'] = $this->db->get()->result_array();
		$data['details'] = [];
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