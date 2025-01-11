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
		$input_today['location_id'] = 'all';
		$input_today['sale_type'] = 'sales';

		$input_this_week = get_date_range('this_week');
		$input_this_week['location_id'] = 1;
		$input_this_week['sale_type'] = 'sales';

		$input_this_month = get_date_range('this_month');
		$input_this_month['location_id'] = 1;
		$input_this_month['sale_type'] = 'sales';
		$input_this_month['end_date'] = $input_today['end_date'];
		
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

		$report_sales = $model->getSales($input_today);
		//var_dump($report_sales);die();
		$model->delete_temp_table();
		$model->create($input_this_week);
		$this_week_total = $model->getSummaryData($input_this_week);
		//var_dump($input_this_week);die();
		if(!empty($this_week_total))
		{
			if($this_week_total['total'] != null)
			{
				$thisWeek = $this_week_total['total'];
			}
		}
		$model->delete_temp_table();
		$model->create($input_this_month);
		$this_month_total = $model->getSummaryData($input_this_month);
		if(!empty($this_month_total))
		{
			if($this_month_total['total'] != null)
			{
				$thisMonth = $this_month_total['total'];
			}
		}
		//var_dump($input_this_month);
		$data['today'] = number_format($today,0,'.',',');
		$data['thisWeek'] = number_format($thisWeek,0,'.',',');
		$data['thisMonth'] = number_format($thisMonth,0,'.',',');
		//var_dump($data);die();

		// Lấy số ngày của tháng hiện tại - Dữ  liệu để vẽ biểu đồ
		$year = date('Y'); // Năm hiện tại
		$month = date('m'); // Tháng hiện tại
		$days_in_month = (int) (new DateTime("$year-$month-01"))->format('t');//cal_days_in_month(CAL_GREGORIAN, $month, $year);

		// Dữ liệu doanh thu ngẫu nhiên cho ví dụ
        $labels = range(1, $days_in_month); // Ngày trong tháng
		$revenues = [];

		$this->load->model('reports/Summary_sales');
		$Summary_sales = $this->Summary_sales;

		$report_data = $Summary_sales->getData($input_this_month);
		$_aReportData = [];
		//var_dump($report_data);die();
		foreach($report_data as $row)
		{
			//	$revenues[] = $row['total'];
			$_strDate = $row['sale_date'];
			$dateObject = new DateTime($_strDate); // Tạo đối tượng DateTime
			$formattedDate = $dateObject->format('d-m-Y'); // Chuyển đổi sang định dạng ngày-tháng-năm
			$_strDay = $dateObject->format('d'); // Lấy ngày
			$_aReportData[(int)$_strDay] = $row['total'];
		}
		

		
		
		// Dữ liệu để hiển thị bảng danh sách đơn hàng hôm nay
		$summary_data = [];
        //$person_id = $this->session->userdata('person_id');
        $reports_accounting = 1;//$this->Employee->has_grant('reports_sales-accounting', $person_id);
        //var_dump($report_data['details']);
        foreach($report_sales['sales'] as $key => $row)
		{
			$_strDate = $row['sale_time'];
			//var_dump($_strDate);die();
			$dateObject = new DateTime($_strDate); // Tạo đối tượng DateTime
			$formattedDate = $dateObject->format('d-m-Y H:i'); // Chuyển đổi sang định dạng ngày-tháng-năm
			$summary_data[] = $this->xss_clean(array(
				'id' => $row['sale_id'],
				'sale_date' => $formattedDate,
				'quantity' => to_quantity_decimals($row['items_purchased']),
				'employee_name' => $row['employee_name'],
				'customer_name' => $row['customer_name'],
				'subtotal' => to_currency($row['subtotal']),
				'tax' => to_currency($row['tax']),
				'total' => to_currency($row['total']),
				'dtotal' => $row['total'],
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
		$day = date('d'); // Gets the day of the month (01 to 31)
		for($i=1;$i <=$day; $i++) // Set từ ngày đầu tháng đến ngày hiện tại về 0
		{
			$revenues[(int)$i] = 0;
		}

		foreach($_aReportData as $key=>$value)
		{
			$revenues[(int)$key] = $value;
		}

		$data['labels'] = json_encode($labels);
		$data['revenues'] = json_encode(array_values($revenues));
		$data['summary_sales'] = $summary_data;
		
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