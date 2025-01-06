<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once("Secure_Controller.php");

class Home extends Secure_Controller 
{
	public function __construct()
	{
		parent::__construct();	
	}

	public function index()
	{
		
		/*
		$inputs = [
            'start_date' => $start_date, 
            'end_date' => $end_date, 
            'sale_type' => $sale_type, 
            'location_id' => $location_id];
		*/

		$input_today = get_date_range('today');
		$input_today['location_id'] = 1;
		$input_today['sale_type'] = 'sales';

		$input_this_week = get_date_range('this_week');
		$input_this_week['location_id'] = 1;
		$input_this_week['sale_type'] = 'sales';

		$input_this_month = get_date_range('this_month');
		$input_this_month['location_id'] = 1;
		$input_this_month['sale_type'] = 'sales';
		
		//$this->load->model('reports/Reports_detailed_sales');
		//$model = $this->Reports_detailed_sales;

        $this->load->model('reports/Detailed_sales');
        $model = $this->Detailed_sales;
        $model->create($input_today);
		
		$today = 0;
		$thisWeek = 0;
		$thisMonth = 0;

		$today_total = $model->getSummaryData($input_today);
		//var_dump($today_total);
		if(!empty($today_total))
		{
			if($today_total['total'] != null)
			{
				$today = $today_total['total'];
			}
		}

		$this_week_total = $model->getSummaryData($input_this_week);
		if(!empty($this_week_total))
		{
			if($this_week_total['total'] != null)
			{
				$thisWeek = $this_week_total['total'];
			}
		}

		$this_month_total = $model->getSummaryData($input_this_month);
		if(!empty($this_month_total))
		{
			if($this_month_total['total'] != null)
			{
				$thisMonth = $this_month_total['total'];
			}
		}
		
		$data['today'] = number_format($today,0,'.',',');
		$data['thisWeek'] = number_format($thisWeek,0,'.',',');
		$data['thisMonth'] = number_format($thisMonth,0,'.',',');
		//var_dump($data);die();

		// Lấy số ngày của tháng hiện tại
		$year = date('Y'); // Năm hiện tại
		$month = date('m'); // Tháng hiện tại
		$days_in_month = (int) (new DateTime("$year-$month-01"))->format('t');//cal_days_in_month(CAL_GREGORIAN, $month, $year);

		// Dữ liệu doanh thu ngẫu nhiên cho ví dụ
        $labels = range(1, $days_in_month); // Ngày trong tháng
		$revenues = [];

		$this->load->model('reports/Summary_sales');
		$Summary_sales = $this->Summary_sales;

		$report_data = $Summary_sales->getData($input_this_month);
		foreach($report_data as $row)
		{
			$revenues[] = $row['total'];
		}
		$data['labels'] = json_encode($labels);
		$data['revenues'] = json_encode($revenues);

		
		$report_data = $model->getData($input_today);

		$summary_data = [];
        //$person_id = $this->session->userdata('person_id');
        $reports_accounting = 1;//$this->Employee->has_grant('reports_sales-accounting', $person_id);
        //var_dump($report_data['details']);
        foreach($report_data['summary'] as $key => $row)
		{
			$summary_data[] = $this->xss_clean(array(
				'id' => $row['sale_id'],
				'sale_date' => $row['sale_date'],
				'quantity' => to_quantity_decimals($row['items_purchased']),
				'employee_name' => $row['employee_name'],
				'customer_name' => $row['customer_name'],
				'subtotal' => to_currency($row['subtotal']),
				'tax' => to_currency($row['tax']),
				'total' => to_currency($row['total']),
				'cost' => to_currency($row['cost']),
				'profit' => to_currency($row['profit']),
				'payment_type' => $row['payment_type'],
				'comment' => $row['comment'],
				'edit' => anchor('sales/edit/'.$row['sale_uuid'], '<span class="glyphicon glyphicon-pencil"></span>',
					//array('class' => 'modal-dlg print_hide', 'data-btn-delete' => $this->lang->line('common_delete'), 'data-btn-submit' => $this->lang->line('common_submit'), 'title' => $this->lang->line('sales_update'))
                    array('class' => 'modal-dlg print_hide', 'data-btn-submit' => $this->lang->line('common_submit'), 'title' => $this->lang->line('sales_update'))
				)
			));
		}

		$data['summary_data'] = $summary_data;
		
		$data['reports_accounting'] = $reports_accounting;
		
		$this->load->view('home',$data);
	}

	public function logout()
	{
		$this->track_page('logout', 'logout');

		$this->Employee->logout();
	}
}
?>