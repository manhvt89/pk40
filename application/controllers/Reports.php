<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once('Secure_Controller.php');

class Reports extends Secure_Controller
{
	public function __construct()
	{
		parent::__construct('reports');

		$method_name = $this->uri->segment(2);
		$exploder = explode('_', $method_name);

		if(sizeof($exploder) > 1)
		{
			preg_match('/(?:inventory)|([^_.]*)(?:_graph|_row)?$/', $method_name, $matches);
			preg_match('/^(.*?)([sy])?$/', array_pop($matches), $matches);
			$submodule_id = $matches[1] . ((count($matches) > 2) ? $matches[2] : 's');

			$this->track_page('reports/' . $submodule_id, 'reports_' . $submodule_id);
            //echo $submodule_id; die();
			// check access to report submodule
			if(!$this->Employee->has_grant('reports_' . $submodule_id, $this->Employee->get_logged_in_employee_info()->person_id))
			{
				redirect('no_access/reports/reports_' . $submodule_id);
			}
		}

		$this->load->helper('report');
	}

	//Initial report listing screen
	public function index()
	{
		$data['grants'] = $this->Employee->get_employee_grants();		
		$this->load->view('reports/listing', $data);
	}

    

	//Summary sales report
	public function summary_sales($start_date, $end_date, $sale_type, $location_id = 'all')
	{
		$inputs = array('start_date' => $start_date, 'end_date' => $end_date, 'sale_type' => $sale_type, 'location_id' => $location_id);

		$this->load->model('reports/Summary_sales');
		$model = $this->Summary_sales;

		$report_data = $model->getData($inputs);
		$summary = $this->xss_clean($model->getSummaryData($inputs));

		$tabular_data = array();
		foreach($report_data as $row)
		{
			$tabular_data[] = $this->xss_clean(array(
				'sale_date' => $row['sale_date'],
				'quantity' => to_quantity_decimals($row['quantity_purchased']),
				'subtotal' => to_currency($row['subtotal']),
				'tax' => to_currency($row['tax']),
				'total' => to_currency($row['total']),
				'cost' => to_currency($row['cost']),
				'profit' => to_currency($row['profit'])
			));
		}

		$data = array(
			'title' => $this->lang->line('reports_sales_summary_report'),
			'subtitle' => date($this->config->item('dateformat'), strtotime($start_date)) . '-' . date($this->config->item('dateformat'), strtotime($end_date)),
			'headers' => $this->xss_clean($model->getDataColumns()),
			'data' => $tabular_data,
			'summary_data' => $summary
		);

		$this->load->view('reports/tabular', $data);
	}

	//Summary categories report
	public function summary_categories($start_date, $end_date, $sale_type, $location_id = 'all')
	{
		$inputs = array('start_date' => $start_date, 'end_date' => $end_date, 'sale_type' => $sale_type, 'location_id' => $location_id);

		$this->load->model('reports/Summary_categories');
		$model = $this->Summary_categories;

		$report_data = $model->getData($inputs);
		$summary = $this->xss_clean($model->getSummaryData($inputs));

		$tabular_data = array();
		foreach($report_data as $row)
		{
			$tabular_data[] = $this->xss_clean(array(
				'category' => $row['category'],
				'quantity' => to_quantity_decimals($row['quantity_purchased']),
				'subtotal' => to_currency($row['subtotal']),
				'tax' => to_currency($row['tax']),
				'total' => to_currency($row['total']),
				'cost' => to_currency($row['cost']),
				'profit' => to_currency($row['profit'])
			));
		}

		$data = array(
			'title' => $this->lang->line('reports_categories_summary_report'),
			'subtitle' => date($this->config->item('dateformat'), strtotime($start_date)) . '-' . date($this->config->item('dateformat'), strtotime($end_date)),
			'headers' => $this->xss_clean($model->getDataColumns()),
			'data' => $tabular_data,
			'summary_data' => $summary
		);

		$this->load->view('reports/tabular', $data);
	}

	//Summary customers report
	public function summary_customers($start_date, $end_date, $sale_type, $location_id = 'all')
	{
		$inputs = array('start_date' => $start_date, 'end_date' => $end_date, 'sale_type' => $sale_type, 'location_id' => $location_id);

		$this->load->model('reports/Summary_customers');
		$model = $this->Summary_customers;

		$report_data = $model->getData($inputs);
		$summary = $this->xss_clean($model->getSummaryData($inputs));

		$tabular_data = array();
		foreach($report_data as $row)
		{
			$tabular_data[] = $this->xss_clean(array(
				'customer_name' => $row['customer'],
				'quantity' => to_quantity_decimals($row['quantity_purchased']),
				'subtotal' => to_currency($row['subtotal']),
				'tax' => to_currency($row['tax']),
				'total' => to_currency($row['total']),
				'cost' => to_currency($row['cost']),
				'profit' => to_currency($row['profit'])
			));
		}

		$data = array(
			'title' => $this->lang->line('reports_customers_summary_report'),
			'subtitle' => date($this->config->item('dateformat'), strtotime($start_date)) . '-' . date($this->config->item('dateformat'), strtotime($end_date)),
			'headers' => $this->xss_clean($model->getDataColumns()),
			'data' => $tabular_data,
			'summary_data' => $summary
		);

		$this->load->view('reports/tabular', $data);
	}

	//Summary suppliers report
	public function summary_suppliers($start_date, $end_date, $sale_type, $location_id = 'all')
	{
		$inputs = array('start_date' => $start_date, 'end_date' => $end_date, 'sale_type' => $sale_type, 'location_id' => $location_id);

		$this->load->model('reports/Summary_suppliers');
		$model = $this->Summary_suppliers;

		$report_data = $model->getData($inputs);
		$summary = $this->xss_clean($model->getSummaryData($inputs));

		$tabular_data = array();
		foreach($report_data as $row)
		{
			$tabular_data[] = $this->xss_clean(array(
				'supplier_name' => $row['supplier'],
				'quantity' => to_quantity_decimals($row['quantity_purchased']),
				'subtotal' => to_currency($row['subtotal']),
				'tax' => to_currency($row['tax']),
				'total' => to_currency($row['total']),
				'cost' => to_currency($row['cost']),
				'profit' => to_currency($row['profit'])
			));
		}

		$data = array(
			'title' => $this->lang->line('reports_suppliers_summary_report'),
			'subtitle' => date($this->config->item('dateformat'), strtotime($start_date)) . '-' . date($this->config->item('dateformat'), strtotime($end_date)),
			'headers' => $this->xss_clean($model->getDataColumns()),
			'data' => $tabular_data,
			'summary_data' => $summary
		);

		$this->load->view('reports/tabular', $data);
	}

	//Summary items report
	public function summary_items($start_date, $end_date, $sale_type, $location_id = 'all')
	{
		$inputs = array('start_date' => $start_date, 'end_date' => $end_date, 'sale_type' => $sale_type, 'location_id' => $location_id);

		$this->load->model('reports/Summary_items');
		$model = $this->Summary_items;

		$report_data = $model->getData($inputs);
		$summary = $this->xss_clean($model->getSummaryData($inputs));

		$tabular_data = array();
		foreach($report_data as $row)
		{
			$tabular_data[] = $this->xss_clean(array(
				'item_name' => $row['name'],
				'quantity' => to_quantity_decimals($row['quantity_purchased']),
				'subtotal' => to_currency($row['subtotal']),
				'tax'  => to_currency($row['tax']),
				'total' => to_currency($row['total']),
				'cost' => to_currency($row['cost']),
				'profit' => to_currency($row['profit'])
			));
		}

		$data = array(
			'title' => $this->lang->line('reports_items_summary_report'),
			'subtitle' => date($this->config->item('dateformat'), strtotime($start_date)) . '-' . date($this->config->item('dateformat'), strtotime($end_date)),
			'headers' => $this->xss_clean($model->getDataColumns()),
			'data' => $tabular_data,
			'summary_data' => $summary
		);

		$this->load->view('reports/tabular', $data);
	}

	//Summary employees report
	public function summary_employees($start_date, $end_date, $sale_type, $location_id = 'all')
	{
		$inputs = array('start_date' => $start_date, 'end_date' => $end_date, 'sale_type' => $sale_type, 'location_id' => $location_id);

		$this->load->model('reports/Summary_employees');
		$model = $this->Summary_employees;

		$report_data = $model->getData($inputs);
		$summary = $this->xss_clean($model->getSummaryData($inputs));

		$tabular_data = array();
		foreach($report_data as $row)
		{
			$tabular_data[] = $this->xss_clean(array(
				'employee_name' => $row['employee'],
				'quantity' => to_quantity_decimals($row['quantity_purchased']),
				'subtotal' => to_currency($row['subtotal']),
				'tax' => to_currency($row['tax']),
				'total' => to_currency($row['total']),
				'cost' => to_currency($row['cost']),
				'profit' => to_currency($row['profit'])
			));
		}

		$data = array(
			'title' => $this->lang->line('reports_employees_summary_report'),
			'subtitle' => date($this->config->item('dateformat'), strtotime($start_date)) . '-' . date($this->config->item('dateformat'), strtotime($end_date)),
			'headers' => $this->xss_clean($model->getDataColumns()),
			'data' => $tabular_data,
			'summary_data' => $summary
		);

		$this->load->view('reports/tabular', $data);
	}

	//Summary taxes report
	public function summary_taxes($start_date, $end_date, $sale_type, $location_id = 'all')
	{
		$inputs = array('start_date' => $start_date, 'end_date' => $end_date, 'sale_type' => $sale_type, 'location_id' => $location_id);

		$this->load->model('reports/Summary_taxes');
		$model = $this->Summary_taxes;

		$report_data = $model->getData($inputs);
		$summary = $this->xss_clean($model->getSummaryData($inputs));

		$tabular_data = array();
		foreach($report_data as $row)
		{
			$tabular_data[] = $this->xss_clean(array(
				'tax_percent' => $row['percent'],
				'report_count' => $row['count'],
				'subtotal' => to_currency($row['subtotal']),
				'tax' => to_currency($row['tax']),
				'total' => to_currency($row['total'])
			));
		}

		$data = array(
			'title' => $this->lang->line('reports_taxes_summary_report'),
			'subtitle' => date($this->config->item('dateformat'), strtotime($start_date)) . '-' . date($this->config->item('dateformat'), strtotime($end_date)),
			'headers' => $this->xss_clean($model->getDataColumns()),
			'data' => $tabular_data,
			'summary_data' => $summary
		);

		$this->load->view('reports/tabular', $data);
	}

	//Summary discounts report
	public function summary_discounts($start_date, $end_date, $sale_type, $location_id = 'all')
	{
		$inputs = array('start_date' => $start_date, 'end_date' => $end_date, 'sale_type' => $sale_type, 'location_id' => $location_id);

		$this->load->model('reports/Summary_discounts');
		$model = $this->Summary_discounts;

		$report_data = $model->getData($inputs);
		$summary = $this->xss_clean($model->getSummaryData($inputs));

		$tabular_data = array();
		foreach($report_data as $row)
		{
			$tabular_data[] = $this->xss_clean(array(
				'discount' => $row['discount_percent'],
				'count' => $row['count']
			));
		}

		$data = array(
			'title' => $this->lang->line('reports_discounts_summary_report'),
			'subtitle' => date($this->config->item('dateformat'), strtotime($start_date)) . '-' . date($this->config->item('dateformat'), strtotime($end_date)),
			'headers' => $this->xss_clean($model->getDataColumns()),
			'data' => $tabular_data,
			'summary_data' => $summary
		);

		$this->load->view('reports/tabular', $data);
	}

	//Summary payments report
	public function summary_payments($start_date, $end_date, $sale_type, $location_id = 'all')
	{
		$inputs = array('start_date' => $start_date, 'end_date' => $end_date, 'sale_type' => $sale_type, 'location_id' => $location_id);

		$this->load->model('reports/Summary_payments');
		$model = $this->Summary_payments;

		$report_data = $model->getData($inputs);
		$summary = $this->xss_clean($model->getSummaryData($inputs));

		$tabular_data = array();
		foreach($report_data as $row)
		{
			$tabular_data[] = $this->xss_clean(array(
				'payment_type' => $row['payment_type'],
				'report_count' => $row['count'],
				'amount_tendered' => to_currency($row['payment_amount'])
			));
		}

		$data = array(
			'title' => $this->lang->line('reports_payments_summary_report'),
			'subtitle' => date($this->config->item('dateformat'), strtotime($start_date)) . '-' . date($this->config->item('dateformat'), strtotime($end_date)),
			'headers' => $this->xss_clean($model->getDataColumns()),
			'data' => $tabular_data,
			'summary_data' => $summary
		);

		$this->load->view('reports/tabular', $data);
	}

	//Input for reports that require only a date range. (see routes.php to see that all graphical summary reports route here)
	public function date_input()
	{
		$data = array();
		$stock_locations = $data = $this->xss_clean($this->Stock_location->get_allowed_locations('sales'));
		$stock_locations['all'] = $this->lang->line('reports_all');
		$data['stock_locations'] = array_reverse($stock_locations, TRUE);
		$data['mode'] = 'sale';

		$this->load->view('reports/date_input', $data);
	}

	//Input for reports that require only a date range. (see routes.php to see that all graphical summary reports route here)
	public function date_input_sales()
	{
		$data = array();
		$stock_locations = $data = $this->xss_clean($this->Stock_location->get_allowed_locations('sales'));
		$stock_locations['all'] =  $this->lang->line('reports_all');
		$data['stock_locations'] = array_reverse($stock_locations, TRUE);
        $data['mode'] = 'sale';

		$this->load->view('reports/date_input', $data);
	}

    public function date_input_recv()
    {
        $data = array();
		$stock_locations = $data = $this->xss_clean($this->Stock_location->get_allowed_locations('receivings'));
		$stock_locations['all'] =  $this->lang->line('reports_all');
		$data['stock_locations'] = array_reverse($stock_locations, TRUE);
 		$data['mode'] = 'receiving';

        $this->load->view('reports/date_input', $data);
    }


    public function date_input_import_lens()
    {
        $data = array();
        $this->load->model('reports/Detailed_receivings');
        $model = $this->Detailed_receivings;
        $stock_locations = $data = $this->xss_clean($this->Stock_location->get_allowed_locations('receivings'));
        $stock_locations['all'] =  $this->lang->line('reports_all');
        $data['stock_locations'] = array_reverse($stock_locations, TRUE);
        $data['category'] = $model->getCategoryDropdownArray();
        $data['mode'] = 'import_lens';

        $this->load->view('reports/date_input', $data);
    }

    //Graphical summary sales report
	public function graphical_summary_sales($start_date, $end_date, $sale_type, $location_id = 'all')
	{
		$inputs = array('start_date' => $start_date, 'end_date' => $end_date, 'sale_type' => $sale_type, 'location_id' => $location_id);

		$this->load->model('reports/Summary_sales');
		$model = $this->Summary_sales;

		$report_data = $model->getData($inputs);
		$summary = $this->xss_clean($model->getSummaryData($inputs));

		$labels = array();
		$series = array();
		foreach($report_data as $row)
		{
			$row = $this->xss_clean($row);

			$date = date($this->config->item('dateformat'), strtotime($row['sale_date']));
			$labels[] = $date;
			$series[] = array('meta' => $date, 'value' => $row['total']);
		}

		$data = array(
			'title' => $this->lang->line('reports_sales_summary_report'),
			'subtitle' => date($this->config->item('dateformat'), strtotime($start_date)) . '-' . date($this->config->item('dateformat'), strtotime($end_date)),
			'chart_type' => 'reports/graphs/line',
			'labels_1' => $labels,
			'series_data_1' => $series,
			'summary_data_1' => $summary,
			'yaxis_title' => $this->lang->line('reports_revenue'),
			'xaxis_title' => $this->lang->line('reports_date'),
			'show_currency' => TRUE
		);

		$this->load->view('reports/graphical', $data);
	}

	//Graphical summary items report
	public function graphical_summary_items($start_date, $end_date, $sale_type, $location_id = 'all')
	{
		$inputs = array('start_date' => $start_date, 'end_date' => $end_date, 'sale_type' => $sale_type, 'location_id' => $location_id);

		$this->load->model('reports/Summary_items');
		$model = $this->Summary_items;

		$report_data = $model->getData($inputs);
		$summary = $this->xss_clean($model->getSummaryData($inputs));

		$labels = array();
		$series = array();
		foreach($report_data as $row)
		{
			$row = $this->xss_clean($row);

			$labels[] = $row['name'];
			$series[] = $row['total'];
		}

		$data = array(
			'title' => $this->lang->line('reports_items_summary_report'),
			'subtitle' => date($this->config->item('dateformat'), strtotime($start_date)) . '-' . date($this->config->item('dateformat'), strtotime($end_date)),
			'chart_type' => 'reports/graphs/hbar',
			'labels_1' => $labels,
			'series_data_1' => $series,
			'summary_data_1' => $summary,
			'yaxis_title' => $this->lang->line('reports_items'),
			'xaxis_title' => $this->lang->line('reports_revenue'),
			'show_currency' => TRUE
		);

		$this->load->view('reports/graphical', $data);
	}

	//Graphical summary customers report
	public function graphical_summary_categories($start_date, $end_date, $sale_type, $location_id = 'all')
	{
		$inputs = array('start_date' => $start_date, 'end_date' => $end_date, 'sale_type' => $sale_type, 'location_id' => $location_id);

		$this->load->model('reports/Summary_categories');
		$model = $this->Summary_categories;

		$report_data = $model->getData($inputs);
		$summary = $this->xss_clean($model->getSummaryData($inputs));

		$labels = array();
		$series = array();
		foreach($report_data as $row)
		{
			$row = $this->xss_clean($row);

			$labels[] = $row['category'];
			$series[] = array('meta' => $row['category'] . ' ' . round($row['total'] / $summary['total'] * 100, 2) . '%', 'value' => $row['total']);
		}

		$data = array(
			'title' => $this->lang->line('reports_categories_summary_report'),
			'subtitle' => date($this->config->item('dateformat'), strtotime($start_date)) . '-' . date($this->config->item('dateformat'), strtotime($end_date)),
			'chart_type' => 'reports/graphs/pie',
			'labels_1' => $labels,
			'series_data_1' => $series,
			'summary_data_1' => $summary,
			'show_currency' => TRUE
		);

		$this->load->view('reports/graphical', $data);
	}

	//Graphical summary suppliers report
	public function graphical_summary_suppliers($start_date, $end_date, $sale_type, $location_id = 'all')
	{
		$inputs = array('start_date' => $start_date, 'end_date' => $end_date, 'sale_type' => $sale_type, 'location_id' => $location_id);

		$this->load->model('reports/Summary_suppliers');
		$model = $this->Summary_suppliers;

		$report_data = $model->getData($inputs);
		$summary = $this->xss_clean($model->getSummaryData($inputs));

		$labels = array();
		$series = array();
		foreach($report_data as $row)
		{
			$row = $this->xss_clean($row);

			$labels[] = $row['supplier'];
			$series[] = array('meta' => $row['supplier'] . ' ' . round($row['total'] / $summary['total'] * 100, 2) . '%', 'value' => $row['total']);
		}

		$data = array(
			'title' => $this->lang->line('reports_suppliers_summary_report'),
			'subtitle' => date($this->config->item('dateformat'), strtotime($start_date)) . '-' . date($this->config->item('dateformat'), strtotime($end_date)),
			'chart_type' => 'reports/graphs/pie',
			'labels_1' => $labels,
			'series_data_1' => $series,
			'summary_data_1' => $summary,
			'show_currency' => TRUE
		);

		$this->load->view('reports/graphical', $data);
	}

	//Graphical summary employees report
	public function graphical_summary_employees($start_date, $end_date, $sale_type, $location_id = 'all')
	{
		$inputs = array('start_date' => $start_date, 'end_date' => $end_date, 'sale_type' => $sale_type, 'location_id' => $location_id);

		$this->load->model('reports/Summary_employees');
		$model = $this->Summary_employees;
		
		$report_data = $model->getData($inputs);
		$summary = $this->xss_clean($model->getSummaryData($inputs));

		$labels = array();
		$series = array();
		foreach($report_data as $row)
		{
			$row = $this->xss_clean($row);

			$labels[] = $row['employee'];
			$series[] = array('meta' => $row['employee'] . ' ' . round($row['total'] / $summary['total'] * 100, 2) . '%', 'value' => $row['total']);
		}

		$data = array(
			'title' => $this->lang->line('reports_employees_summary_report'),
			'subtitle' => date($this->config->item('dateformat'), strtotime($start_date)) . '-' . date($this->config->item('dateformat'), strtotime($end_date)),
			'chart_type' => 'reports/graphs/pie',
			'labels_1' => $labels,
			'series_data_1' => $series,
			'summary_data_1' => $summary,
			'show_currency' => TRUE
		);

		$this->load->view('reports/graphical', $data);
	}

	//Graphical summary taxes report
	public function graphical_summary_taxes($start_date, $end_date, $sale_type, $location_id = 'all')
	{
		$inputs = array('start_date' => $start_date, 'end_date' => $end_date, 'sale_type' => $sale_type, 'location_id' => $location_id);

		$this->load->model('reports/Summary_taxes');
		$model = $this->Summary_taxes;
		
		$report_data = $model->getData($inputs);
		$summary = $this->xss_clean($model->getSummaryData($inputs));

		$labels = array();
		$series = array();
		foreach($report_data as $row)
		{
			$row = $this->xss_clean($row);

			$labels[] = $row['percent'];
			$series[] = array('meta' => $row['percent'] . ' ' . round($row['total'] / $summary['total'] * 100, 2) . '%', 'value' => $row['total']);
		}

		$data = array(
			'title' => $this->lang->line('reports_taxes_summary_report'),
			'subtitle' => date($this->config->item('dateformat'), strtotime($start_date)) . '-' . date($this->config->item('dateformat'), strtotime($end_date)),
			'chart_type' => 'reports/graphs/pie',
			'labels_1' => $labels,
			'series_data_1' => $series,
			'summary_data_1' => $summary,
			'show_currency' => TRUE
		);

		$this->load->view('reports/graphical', $data);
	}

	//Graphical summary customers report
	public function graphical_summary_customers($start_date, $end_date, $sale_type, $location_id = 'all')
	{
		$inputs = array('start_date' => $start_date, 'end_date' => $end_date, 'sale_type' => $sale_type, 'location_id' => $location_id);
		
		$this->load->model('reports/Summary_customers');
		$model = $this->Summary_customers;
		
		$report_data = $model->getData($inputs);
		$summary = $this->xss_clean($model->getSummaryData($inputs));

		$labels = array();
		$series = array();
		foreach($report_data as $row)
		{
			$row = $this->xss_clean($row);

			$labels[] = $row['customer'];
			$series[] = $row['total'];
		}

		$data = array(
			'title' => $this->lang->line('reports_customers_summary_report'),
			'subtitle' => date($this->config->item('dateformat'), strtotime($start_date)) . '-' . date($this->config->item('dateformat'), strtotime($end_date)),
			'chart_type' => 'reports/graphs/hbar',
			'labels_1' => $labels,
			'series_data_1' => $series,
			'summary_data_1' => $summary,
			'yaxis_title' => $this->lang->line('reports_customers'),
			'xaxis_title' => $this->lang->line('reports_revenue'),
			'show_currency' => TRUE
		);

		$this->load->view('reports/graphical', $data);
	}

	//Graphical summary discounts report
	public function graphical_summary_discounts($start_date, $end_date, $sale_type, $location_id = 'all')
	{
		$inputs = array('start_date' => $start_date, 'end_date' => $end_date, 'sale_type' => $sale_type, 'location_id' => $location_id);

		$this->load->model('reports/Summary_discounts');
		$model = $this->Summary_discounts;
		
		$report_data = $model->getData($inputs);
		$summary = $this->xss_clean($model->getSummaryData($inputs));

		$labels = array();
		$series = array();
		foreach($report_data as $row)
		{
			$row = $this->xss_clean($row);

			$labels[] = $row['discount_percent'];
			$series[] = $row['count'];
		}

		$data = array(
			'title' => $this->lang->line('reports_discounts_summary_report'),
			'subtitle' => date($this->config->item('dateformat'), strtotime($start_date)) . '-' . date($this->config->item('dateformat'), strtotime($end_date)),
			'chart_type' => 'reports/graphs/bar',
			'labels_1' => $labels,
			'series_data_1' => $series,
			'summary_data_1' => $summary,
			'yaxis_title' => $this->lang->line('reports_count'),
			'xaxis_title' => $this->lang->line('reports_discount_percent'),
			'show_currency' => FALSE
		);

		$this->load->view('reports/graphical', $data);
	}

	//Graphical summary payments report
	public function graphical_summary_payments($start_date, $end_date, $sale_type, $location_id = 'all')
	{
		$inputs = array('start_date' => $start_date, 'end_date' => $end_date, 'sale_type' => $sale_type, 'location_id' => $location_id);
		
		$this->load->model('reports/Summary_payments');
		$model = $this->Summary_payments;
		
		$report_data = $model->getData($inputs);
		$summary = $this->xss_clean($model->getSummaryData($inputs));

		$labels = array();
		$series = array();
		foreach($report_data as $row)
		{
			$row = $this->xss_clean($row);

			$labels[] = $row['payment_type'];
			$series[] = array('meta' => $row['payment_type'] . ' ' . round($row['payment_amount'] / $summary['total'] * 100, 2) . '%', 'value' => $row['payment_amount']);
		}

		$data = array(
			'title' => $this->lang->line('reports_payments_summary_report'),
			'subtitle' => date($this->config->item('dateformat'), strtotime($start_date)) . '-' . date($this->config->item('dateformat'), strtotime($end_date)),
			'chart_type' => 'reports/graphs/pie',
			'labels_1' => $labels,
			'series_data_1' => $series,
			'summary_data_1' => $summary,
			'show_currency' => TRUE
		);

		$this->load->view('reports/graphical', $data);
	}

	public function specific_customer_input()
	{
		$data = array();
		$data['specific_input_name'] = $this->lang->line('reports_customer');

		$customers = array();
		foreach($this->Customer->get_all()->result() as $customer)
		{		
			$customers[$customer->person_id] = $this->xss_clean($customer->first_name . ' ' . $customer->last_name);
		}
		$data['specific_input_data'] = $customers;

		$this->load->view('reports/specific_input', $data);
	}

	public function specific_customer($start_date, $end_date, $customer_id, $sale_type)
	{
		$inputs = array('start_date' => $start_date, 'end_date' => $end_date, 'customer_id' => $customer_id, 'sale_type' => $sale_type);
		
		$this->load->model('reports/Specific_customer');
		$model = $this->Specific_customer;

		$model->create($inputs);

		$headers = $this->xss_clean($model->getDataColumns());
		$report_data = $model->getData($inputs);

		$summary_data = array();
		$details_data = array();

		foreach($report_data['summary'] as $key => $row)
		{
			$summary_data[] = $this->xss_clean(array(
				'id' => anchor('sales/receipt/'.$row['sale_id'], 'POS '.$row['sale_id'], array('target'=>'_blank')),
				'sale_date' => $row['sale_date'],
				'quantity' => to_quantity_decimals($row['items_purchased']),
				'employee_name' => $row['employee_name'],
				'subtotal' => to_currency($row['subtotal']),
				'tax' => to_currency($row['tax']),
				'total' => to_currency($row['total']),
				'cost' => to_currency($row['cost']),
				'profit' => to_currency($row['profit']),
				'payment_type' => $row['payment_type'],
				'comment' => $row['comment']));

			foreach($report_data['details'][$key] as $drow)
			{
				$details_data[$row['sale_id']][] = $this->xss_clean(array($drow['name'], $drow['category'], $drow['serialnumber'], $drow['description'], to_quantity_decimals($drow['quantity_purchased']), to_currency($drow['subtotal']), to_currency($drow['tax']), to_currency($drow['total']), to_currency($drow['cost']), to_currency($drow['profit']), $drow['discount_percent'].'%'));
			}
		}
        $person_id = $this->session->userdata('person_id');
        $reports_accounting = $this->Employee->has_grant('reports_sales-accounting', $person_id);

		$customer_info = $this->Customer->get_info($customer_id);
		$data = array(
			'title' => $this->xss_clean($customer_info->first_name . ' ' . $customer_info->last_name . ' ' . $this->lang->line('reports_report')),
			'subtitle' => date($this->config->item('dateformat'), strtotime($start_date)) . '-' . date($this->config->item('dateformat'), strtotime($end_date)),
			'headers' => $headers,
			'summary_data' => $summary_data,
			'details_data' => $details_data,
            'reports_accounting' => $reports_accounting,
			'overall_summary_data' => $this->xss_clean($model->getSummaryData($inputs))
		);

		$this->load->view('reports/tabular_details', $data);
	}

	public function specific_ctvs_input()
    {
        $data = array();
        $data['specific_input_name'] = $this->lang->line('reports_ctv');

        $employees = array();
        foreach($this->Ctv->get_all()->result() as $employee)
        {
            $employees[$employee->person_id] = $this->xss_clean($employee->first_name . ' ' . $employee->last_name);
        }
        $data['specific_input_data'] = $employees;

        $this->load->view('reports/specific_input', $data);
    }

    public function specific_ctvs($start_date, $end_date, $employee_id, $sale_type)
    {
        $inputs = array('start_date' => $start_date, 'end_date' => $end_date, 'ctv_id' => $employee_id, 'sale_type' => $sale_type);

        $this->load->model('reports/Specific_ctv');
        $model = $this->Specific_ctv;

        $model->create($inputs);

        $headers = $this->xss_clean($model->getDataColumns());
        $report_data = $model->getData($inputs);

        $summary_data = array();
        $details_data = array();

        foreach($report_data['summary'] as $key => $row)
        {
            $summary_data[] = $this->xss_clean(array(
                'id' => anchor('sales/receipt/'.$row['sale_id'], 'POS '.$row['sale_id'], array('target'=>'_blank')),
                'sale_date' => $row['sale_date'],
                'quantity' => to_quantity_decimals($row['items_purchased']),
                'customer_name' => $row['customer_name'],
                'subtotal' => to_currency($row['subtotal']),
                'tax' => to_currency($row['tax']),
                'total' => to_currency($row['total']),
                'cost' => to_currency($row['cost']),
                'profit' => to_currency($row['profit']),
                'payment_type' => $row['payment_type'],
                'comment' => $row['comment']));

            foreach($report_data['details'][$key] as $drow)
            {
                $details_data[$row['sale_id']][] = $this->xss_clean(array($drow['name'], $drow['category'], $drow['serialnumber'], $drow['description'], to_quantity_decimals($drow['quantity_purchased']), to_currency($drow['subtotal']), to_currency($drow['tax']), to_currency($drow['total']), to_currency($drow['cost']), to_currency($drow['profit']), $drow['discount_percent'].'%'));
            }
        }

        $employee_info = $this->Employee->get_info($employee_id);
        $data = array(
            'title' => $this->xss_clean($employee_info->first_name . ' ' . $employee_info->last_name . ' ' . $this->lang->line('reports_report')),
            'subtitle' => date($this->config->item('dateformat'), strtotime($start_date)) . '-' . date($this->config->item('dateformat'), strtotime($end_date)),
            'headers' => $headers,
            'summary_data' => $summary_data,
            'details_data' => $details_data,
            'overall_summary_data' => $this->xss_clean($model->getSummaryData($inputs))
        );

        $this->load->view('reports/tabular_details', $data);
    }

    public function specific_employee_input()
	{
		$data = array();
		$data['specific_input_name'] = $this->lang->line('reports_employee');

		$employees = array();
		foreach($this->Employee->get_all()->result() as $employee)
		{
			$employees[$employee->person_id] = $this->xss_clean($employee->first_name . ' ' . $employee->last_name);
		}
		$data['specific_input_data'] = $employees;

		$this->load->view('reports/specific_input', $data);
	}

	public function specific_employee($start_date, $end_date, $employee_id, $sale_type)
	{
		$inputs = array('start_date' => $start_date, 'end_date' => $end_date, 'employee_id' => $employee_id, 'sale_type' => $sale_type);

		$this->load->model('reports/Specific_employee');
		$model = $this->Specific_employee;

		$model->create($inputs);

		$headers = $this->xss_clean($model->getDataColumns());
		$report_data = $model->getData($inputs);

		$summary_data = array();
		$details_data = array();

		foreach($report_data['summary'] as $key => $row)
		{
			$summary_data[] = $this->xss_clean(array(
				'id' => anchor('sales/receipt/'.$row['sale_id'], 'POS '.$row['sale_id'], array('target'=>'_blank')),
				'sale_date' => $row['sale_date'],
				'quantity' => to_quantity_decimals($row['items_purchased']),
				'customer_name' => $row['customer_name'],
				'subtotal' => to_currency($row['subtotal']),
				'tax' => to_currency($row['tax']),
				'total' => to_currency($row['total']),
				'cost' => to_currency($row['cost']),
				'profit' => to_currency($row['profit']),
				'payment_type' => $row['payment_type'],
				'comment' => $row['comment']));

			foreach($report_data['details'][$key] as $drow)
			{
				$details_data[$row['sale_id']][] = $this->xss_clean(array($drow['name'], $drow['category'], $drow['serialnumber'], $drow['description'], to_quantity_decimals($drow['quantity_purchased']), to_currency($drow['subtotal']), to_currency($drow['tax']), to_currency($drow['total']), to_currency($drow['cost']), to_currency($drow['profit']), $drow['discount_percent'].'%'));
			}
		}

        $person_id = $this->session->userdata('person_id');
        $reports_accounting = $this->Employee->has_grant('reports_sales-accounting', $person_id);

		$employee_info = $this->Employee->get_info($employee_id);
		$data = array(
			'title' => $this->xss_clean($employee_info->first_name . ' ' . $employee_info->last_name . ' ' . $this->lang->line('reports_report')),
			'subtitle' => date($this->config->item('dateformat'), strtotime($start_date)) . '-' . date($this->config->item('dateformat'), strtotime($end_date)),
			'headers' => $headers,
			'summary_data' => $summary_data,
            'reports_accounting'=>$reports_accounting,
			'details_data' => $details_data,
			'overall_summary_data' => $this->xss_clean($model->getSummaryData($inputs))
		);

		$this->load->view('reports/tabular_details', $data);
	}

	public function specific_discount_input()
	{
		$data = array();
		$data['specific_input_name'] = $this->lang->line('reports_discount');

		$discounts = array();
		for ($i = 0; $i <= 100; $i += 10)
		{
			$discounts[$i] = $i . '%';
		}
		$data['specific_input_data'] = $discounts;
		
		$data = $this->xss_clean($data);

		$this->load->view('reports/specific_input', $data);
	}

	public function specific_discount($start_date, $end_date, $discount, $sale_type)
	{
		$inputs = array('start_date' => $start_date, 'end_date' => $end_date, 'discount' => $discount, 'sale_type' => $sale_type);
		
		$this->load->model('reports/Specific_discount');
		$model = $this->Specific_discount;

		$model->create($inputs);

		$headers = $this->xss_clean($model->getDataColumns());
		$report_data = $model->getData($inputs);

		$summary_data = array();
		$details_data = array();

		foreach($report_data['summary'] as $key => $row)
		{
			$summary_data[] = $this->xss_clean(array(
				'id' => anchor('sales/receipt/'.$row['sale_id'], 'POS '.$row['sale_id'], array('target'=>'_blank')),
				'sale_date' => $row['sale_date'],
				'quantity' => to_quantity_decimals($row['items_purchased']),
				'customer_name' => $row['customer_name'],
				'subtotal' => to_currency($row['subtotal']),
				'tax' => to_currency($row['tax']),
				'total' => to_currency($row['total']),
				'profit' => to_currency($row['profit']),
				'payment_type' => $row['payment_type'],
				'comment' => $row['comment']
			));

			foreach($report_data['details'][$key] as $drow)
			{
				$details_data[$row['sale_id']][] = $this->xss_clean(array($drow['name'], $drow['category'], $drow['serialnumber'], $drow['description'], to_quantity_decimals($drow['quantity_purchased']), to_currency($drow['subtotal']), to_currency($drow['tax']), to_currency($drow['total']), to_currency($drow['profit']), $drow['discount_percent'].'%'));
			}
		}

		$data = array(
			'title' => $discount . '% ' . $this->lang->line('reports_discount') . ' ' . $this->lang->line('reports_report'),
			'subtitle' => date($this->config->item('dateformat'), strtotime($start_date)) . '-' . date($this->config->item('dateformat'), strtotime($end_date)),
			'headers' => $headers,
			'summary_data' => $summary_data,
			'details_data' => $details_data,
			'overall_summary_data' => $this->xss_clean($model->getSummaryData($inputs))
		);

		$this->load->view('reports/tabular_details', $data);
	}

 	public function get_detailed_sales_row($sale_id)
	{
		$inputs = array('sale_id' => $sale_id);
		
		$this->load->model('reports/Detailed_sales');
		$model = $this->Detailed_sales;

		$model->create($inputs);

		$report_data = $model->getDataBySaleId($sale_id);

		$summary_data = $this->xss_clean(array(
			'sale_id' => $report_data['sale_id'],
			'sale_date' => $report_data['sale_date'],
			'quantity' => to_quantity_decimals($report_data['items_purchased']),
			'employee_name' => $report_data['employee_name'],
			'customer_name' => $report_data['customer_name'],
			'subtotal' => to_currency($report_data['subtotal']),
			'tax' => to_currency($report_data['tax']),
			'total' => to_currency($report_data['total']),
			'cost' => to_currency($report_data['cost']),
			'profit' => to_currency($report_data['profit']),
			'payment_type' => $report_data['payment_type'],
			'comment' => $report_data['comment'],
			'edit' => anchor('sales/edit/'. $report_data['sale_id'], '<span class="glyphicon glyphicon-edit"></span>',
				array('class'=>'modal-dlg print_hide', 'data-btn-delete' => $this->lang->line('common_delete'), 'data-btn-submit' => $this->lang->line('common_submit'), 'title' => $this->lang->line('sales_update'))
			)
		));

		echo json_encode(array($sale_id => $summary_data));
	}

	public function detailed_sales($start_date, $end_date, $sale_type, $location_id = 'all')
	{
		$inputs = array('start_date' => $start_date, 'end_date' => $end_date, 'sale_type' => $sale_type, 'location_id' => $location_id);
		
		//$this->load->model('reports/Reports_detailed_sales');
		//$model = $this->Reports_detailed_sales;

        $this->load->model('reports/Detailed_sales');
        $model = $this->Detailed_sales;
        $model->create($inputs);

		$headers = $this->xss_clean($model->getDataColumns());
		$report_data = $model->getData($inputs);

		$summary_data = array();
		$details_data = array();

		$show_locations = $this->xss_clean($this->Stock_location->multiple_locations());

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
				'edit' => anchor('sales/edit/'.$row['sale_id'], '<span class="glyphicon glyphicon-edit"></span>',
					//array('class' => 'modal-dlg print_hide', 'data-btn-delete' => $this->lang->line('common_delete'), 'data-btn-submit' => $this->lang->line('common_submit'), 'title' => $this->lang->line('sales_update'))
                    array('class' => 'modal-dlg print_hide', 'data-btn-submit' => $this->lang->line('common_submit'), 'title' => $this->lang->line('sales_update'))
				)
			));
            if($report_data['details'][$key] == null)
            {
                echo $key . ' M'; echo $row['sale_id'] .' A';
            }
			foreach($report_data['details'][$key] as $k => $drow)
			{
				$quantity_purchased = to_quantity_decimals($drow['quantity_purchased']);
				if(!$show_locations)
				{
					$quantity_purchased .= ' [' . $this->Stock_location->get_location_name($drow['item_location']) . ']';
				}
				$details_data[$row['sale_id']][] = $this->xss_clean(array($drow['item_number'],$drow['name'], $drow['category'], $quantity_purchased, to_currency($drow['subtotal']), $drow['discount_percent'].'%', to_currency($drow['total']), to_currency($drow['cost']), to_currency($drow['profit']), $drow['discount_percent'].'%'));
			}
		}

		$data = array(
			'title' => $this->lang->line('reports_detailed_sales_report'),
			'subtitle' => date($this->config->item('dateformat'), strtotime($start_date)) . '-' . date($this->config->item('dateformat'), strtotime($end_date)),
			'headers' => $headers,
			'editable' => 'sales',
			'summary_data' => $summary_data,
			'details_data' => $details_data,
            'reports_accounting' => $reports_accounting,
			'overall_summary_data' => $this->xss_clean($model->getSummaryData($inputs))
		);

		$this->load->view('reports/tabular_details', $data);
	}

    public function sales_accounting() // use to permission
    {
        return true;
    }

	public function get_detailed_receivings_row($receiving_id)
	{
		$inputs = array('receiving_id' => $receiving_id);

		$this->load->model('reports/Detailed_receivings');
		$model = $this->Detailed_receivings;
		
		$model->create($inputs);

		$report_data = $model->getDataByReceivingId($receiving_id);

		$summary_data = $this->xss_clean(array(
			'receiving_id' => $report_data['receiving_id'],
			'receiving_date' => $report_data['receiving_date'],
			'quantity' => to_quantity_decimals($report_data['items_purchased']),
			'employee_name' => $report_data['employee_name'],
			'supplier_name' => $report_data['supplier_name'],
			'total' => to_currency($report_data['total']),
			'payment_type' => $report_data['payment_type'],
			'reference' => $report_data['reference'],
			'comment' => $report_data['comment'],
			'edit' => anchor('receivings/edit/'. $report_data['receiving_id'], '<span class="glyphicon glyphicon-edit"></span>',
				array('class'=>'modal-dlg print_hide', 'data-btn-submit' => $this->lang->line('common_submit'), 'data-btn-delete' => $this->lang->line('common_delete'), 'title' => $this->lang->line('receivings_update'))
			)
		));

		echo json_encode(array($receiving_id => $summary_data));
	}

	public function detail_import_lens($start_date, $end_date, $receiving_type, $location_id = 'all', $category = 'all')
    {
        $inputs = array('start_date' => $start_date, 'end_date' => $end_date, 'receiving_type' => $receiving_type, 'location_id' => $location_id);
        $inputs['category'] = $category;
        $this->load->model('reports/Detailed_receivings');
        $model = $this->Detailed_receivings;
        $model->create($inputs);
        $model->create($inputs);

        $headers = $this->xss_clean($model->getDataColumns());
        $report_data = $model->getData($inputs);

        $summary_data = array();
        $details_data = array();

        $show_locations = $this->xss_clean($this->Stock_location->multiple_locations());
        $show_category = $model->getCategoryDropdownArray();
        foreach($report_data['summary'] as $key => $row)
        {
            $summary_data[] = $this->xss_clean(array(
                'id' => $row['receiving_id'],
                'receiving_date' => $row['receiving_date'],
                'quantity' => to_quantity_decimals($row['items_purchased']),
                'employee_name' => $row['employee_name'],
                'supplier_name' => $row['supplier_name'],
                'total' => to_currency($row['total']),
                'payment_type' => $row['payment_type'],
                'reference' => $row['reference'],
                'comment' => $row['comment']

            ));

            foreach($report_data['details'][$key] as $drow)
            {
                $quantity_purchased = $drow['receiving_quantity'] > 1 ? to_quantity_decimals($drow['quantity_purchased']) . ' x ' . to_quantity_decimals($drow['receiving_quantity']) : to_quantity_decimals($drow['quantity_purchased']);
                if ($show_locations)
                {
                    $quantity_purchased .= ' [' . $this->Stock_location->get_location_name($drow['item_location']) . ']';
                }
                $details_data[$row['receiving_id']][] = $this->xss_clean(array($drow['item_number'], $drow['name'], $drow['category'], $quantity_purchased, to_currency($drow['total']), $drow['discount_percent'].'%'));
            }
        }

        $data = array(
            'title' => $this->lang->line('reports_detailed_receivings_report'),
            'subtitle' => date($this->config->item('dateformat'), strtotime($start_date)) . '-' . date($this->config->item('dateformat'), strtotime($end_date)),
            'headers' => $headers,
            //'editable' => 'receivings',
            'summary_data' => $summary_data,
            'editable' => '',
            'details_data' => $details_data,
            'overall_summary_data' => $this->xss_clean($model->getSummaryData($inputs)),
            'show_category'=>$show_category
        );

        $this->load->view('reports/import_lens_details', $data);

    }

	public function detailed_receivings($start_date, $end_date, $receiving_type, $location_id = 'all', $category = 'all')
	{
	    $inputs = array('start_date' => $start_date, 'end_date' => $end_date, 'receiving_type' => $receiving_type, 'location_id' => $location_id);
        $inputs['category'] = $category;
		$this->load->model('reports/Detailed_receivings');
		$model = $this->Detailed_receivings;
		
		$model->create($inputs);

		$headers = $this->xss_clean($model->getDataColumns());
		$report_data = $model->getData($inputs);

		$summary_data = array();
		$details_data = array();

		$show_locations = $this->xss_clean($this->Stock_location->multiple_locations());
        //$person_id = $this->session->userdata('person_id');
        $reports_accounting = $this->Employee->has_grant('reports_receiving-accounting');
        $now_time = time() - 36000;

		foreach($report_data['summary'] as $key => $row)
		{
            $receiving_time = strtotime($row['receiving_time']);
            
            if ($now_time < $receiving_time ) {
                $summary_data[] = $this->xss_clean(
                    array(
                        'id' => $row['receiving_id'],
                        'receiving_date' => $row['receiving_date'],
                        'quantity' => to_quantity_decimals($row['items_purchased']),
                        'employee_name' => $row['employee_name'],
                        'supplier_name' => $row['supplier_name'],
                        'total' => to_currency($row['total']),
                        'payment_type' => $row['payment_type'],
                        'reference' => $row['reference'],
                        'comment' => $row['comment'],
                        'edit' => anchor(
                            'receivings/edit/' . $row['receiving_id'],
                            '<span class="glyphicon glyphicon-edit"></span>',
                            array('class' => 'modal-dlg print_hide', 'data-btn-delete' => $this->lang->line('common_delete'), 'data-btn-submit' => $this->lang->line('common_submit'), 'title' => $this->lang->line('receivings_update'))
                        ) . ' | '.anchor(
                            'receivings/receipt/' . $row['receiving_id'],
                            '<span class="glyphicon glyphicon-file"></span>',
                            array('class' => 'print_hide', 'data-btn-submit' => 'Xem chi tiết', 'title' => 'Xem chi tiết phiếu')
                        )
                    )
                );
            } else{
                $summary_data[] = $this->xss_clean(
                    array(
                        'id' => $row['receiving_id'],
                        'receiving_date' => $row['receiving_date'],
                        'quantity' => to_quantity_decimals($row['items_purchased']),
                        'employee_name' => $row['employee_name'],
                        'supplier_name' => $row['supplier_name'],
                        'total' => to_currency($row['total']),
                        'payment_type' => $row['payment_type'],
                        'reference' => $row['reference'],
                        'comment' => $row['comment'],
                        'edit' => anchor(
                            'receivings/edit/' . $row['receiving_id'],
                            '<span class="glyphicon glyphicon-edit"></span>',
                            array('class' => 'modal-dlg print_hide', 'data-btn-submit' => $this->lang->line('common_submit'), 'title' => $this->lang->line('receivings_update'))
                        ). ' | '. anchor(
                            'receivings/receipt/' . $row['receiving_id'],
                            '<span class="glyphicon glyphicon-file"></span>',
                            array('class' => 'print_hide', 'data-btn-submit' => 'Xem chi tiết', 'title' => 'Xem chi tiết phiếu')
                        )
                    )
                );
            }

			foreach($report_data['details'][$key] as $drow)
			{
				$quantity_purchased = $drow['receiving_quantity'] > 1 ? to_quantity_decimals($drow['quantity_purchased']) . ' x ' . to_quantity_decimals($drow['receiving_quantity']) : to_quantity_decimals($drow['quantity_purchased']);
				if ($show_locations)
				{
					$quantity_purchased .= ' [' . $this->Stock_location->get_location_name($drow['item_location']) . ']';
				}
				$details_data[$row['receiving_id']][] = $this->xss_clean(array($drow['item_number'], $drow['name'], $drow['category'], $quantity_purchased, to_currency($drow['item_unit_price']), $drow['discount_percent'].'%'));
			}
		}

		$data = array(
			'title' => $this->lang->line('reports_detailed_receivings_report'),
			'subtitle' => date($this->config->item('dateformat'), strtotime($start_date)) . '-' . date($this->config->item('dateformat'), strtotime($end_date)),
			'headers' => $headers,
			'editable' => 'receivings',
            'reports_accounting' => $reports_accounting,
			'summary_data' => $summary_data,
			'details_data' => $details_data,
			'overall_summary_data' => $this->xss_clean($model->getSummaryData($inputs))
		);

		$this->load->view('reports/tabular_details', $data);
	}

	public function inventory_low()
	{
		$inputs = array();

		$this->load->model('reports/Inventory_low');
		$model = $this->Inventory_low;

		$report_data = $model->getData($inputs);
		
		$tabular_data = array();
		foreach($report_data as $row)
		{
			$tabular_data[] = $this->xss_clean(array(
				'item_name' => $row['name'],
				'item_number' => $row['item_number'],
				'quantity' => to_quantity_decimals($row['quantity']),
				'reorder_level' => to_quantity_decimals($row['reorder_level']),
				'location_name' => $row['location_name']
			));
		}

		$data = array(
			'title' => $this->lang->line('reports_inventory_low_report'),
			'subtitle' => '',
			'headers' => $this->xss_clean($model->getDataColumns()),
			'data' => $tabular_data,
			'summary_data' => $this->xss_clean($model->getSummaryData($inputs))
		);

		$this->load->view('reports/tabular', $data);
	}

	public function inventory_summary_input()
	{
		$this->load->model('reports/Inventory_summary');
		$model = $this->Inventory_summary;
		
		$data = array();
		$data['item_count'] = $model->getItemCountDropdownArray();

		$stock_locations = $this->xss_clean($this->Stock_location->get_allowed_locations());
		$stock_locations['all'] = $this->lang->line('reports_all');
		$data['stock_locations'] = array_reverse($stock_locations, TRUE);

		$this->load->view('reports/inventory_summary_input', $data);
	}

	public function inventory_summary($location_id = 'all', $item_count = 'all')
	{

	    $inputs = array('location_id' => $location_id, 'item_count' => $item_count);
		
		$this->load->model('reports/Inventory_summary');
		$model = $this->Inventory_summary;

		$report_data = $model->getData($inputs);

		$tabular_data = array();
		foreach($report_data as $row)
		{
			$tabular_data[] = $this->xss_clean(array(
				'item_name' => $row['name'],
				'item_number' => $row['item_number'],
				'quantity' => to_quantity_decimals($row['quantity']),
				'reorder_level' => to_quantity_decimals($row['reorder_level']),
				'location_name' => $row['location_name'],
				'cost_price' => to_currency($row['cost_price']),
				'unit_price' => to_currency($row['unit_price']),
				'subtotal' => to_currency($row['sub_total_value'])
			));
		}

		$data = array(
			'title' => $this->lang->line('reports_inventory_summary_report'),
			'subtitle' => '',
			'headers' => $this->xss_clean($model->getDataColumns()),
			'data' => $tabular_data,
			'summary_data' => $this->xss_clean($model->getSummaryData($report_data))
		);

		$this->load->view('reports/tabular', $data);
	}

    public function inventory_detail_lens()
    {
        $this->load->model('reports/Inventory_lens');
        $model = $this->Inventory_lens;
        $data = array();
        $data['item_count'] = $model->getCategoryDropdownArray();
        $stock_locations = $this->xss_clean($this->Stock_location->get_allowed_locations());
        $stock_locations['all'] = $this->lang->line('reports_all');
        $data['stock_locations'] = array_reverse($stock_locations, TRUE);

        $this->load->view('reports/inventory_detail_lens_input', $data);
    }

    /*
    Call report inventory_lens()
    */
    public function ajax_inventory_summary()
    {
        $this->load->model('reports/Inventory_lens');
        $model = $this->Inventory_lens;
        $location_id = $this->input->post('location_id');
        $category = $this->input->post('category');
        $icate = $this->config->item('iKindOfLens');
        $result = 1;

        $inputs = array('location_id'=>$location_id,'category'=>$category);

        $report_data = $model->getData($inputs);
        //var_dump($report_data);

        if(count($report_data) != 1087)
        {
            //$json = array('result'=>0,'data'=>$report_data);
            //echo json_encode($json);
            //exit();
        }

        $data['header'] = array(
                                'title'=>'Báo cáo tồn kho',
                                'company_name'=>'',
                                'description'=>$icate[$category],
                                'brand'=>'',
                                'customer'=>'',
                                'ordered_date'=>date('d/m/Y'),
                                'code'=>'MS đơn đặt hàng',
                                'total'=>'Tổng số lượng (miếng)'
                            );
        $map = array(
                                '-'=>0,
                                '0.00'=>1,
                                '0.25'=>2,
                                '0.50'=>3,
                                '0.75'=>4,
                                '1.00'=>5,
                                '1.25'=>6,
                                '1.50'=>7,
                                '1.75'=>8,
                                '2.00'=>9,
                                '2.25'=>10,
                                '2.50'=>11,
                                '2.75'=>12,
                                '3.00'=>13,
                                '3.25'=>14,
                                '3.50'=>15,
                                '3.75'=>16,
                                '4.00'=>17,
                                '4.25'=>18,
                                '4.50'=>19,
                                '4.75'=>20,
                                '5.00'=>21,
                                '5.25'=>22,
                                '5.50'=>23,
                                '5.75'=>24,
                                '6.00'=>25,
                                '6.25'=>26,
                                '6.50'=>27,
                                '6.75'=>28,
                                '7.00'=>29,
                                '7.25'=>30,
                                '7.50'=>31,
                                '7.75'=>32,
                                '8.00'=>33,
                                '8.25'=>34,
                                '8.50'=>35,
                                '8.75'=>36,
                                '9.00'=>37,
                                '9.25'=>38,
                                '9.50'=>39,
                                '9.75'=>40,
                                '10.00'=>41,
                                '10.25'=>42,
                                '10.50'=>43,
                                '10.75'=>44,
                                '11.00'=>45,
                                '11.25'=>46,
                                '11.50'=>47,
                                '11.75'=>48,
                                '12.00'=>49,
                                '12.25'=>50,
                                '12.50'=>51,
                                '12.75'=>52,
                                '13.00'=>53,
                                '13.25'=>54,
                                '13.50'=>55,
                                '13.75'=>56,
                                '14.00'=>57,
                                '14.25'=>58,
                                '14.50'=>59,
                                '14.75'=>60,
                                '15.00'=>61,
                            ); 

        $re_map = array(
            '-',
            '0.00',
            '0.25',
            '0.50',
            '0.75',
            '1.00',
            '1.25',
            '1.50',
            '1.75',
            '2.00',
            '2.25',
            '2.50',
            '2.75',
            '3.00',
            '3.25',
            '3.50',
            '3.75',
            '4.00',
            '4.25',
            '4.50',
            '4.75',
            '5.00',
            '5.25',
            '5.50',
            '5.75',
            '6.00',
            '6.25',
            '6.50',
            '6.75',
            '7.00',
            '7.25',
            '7.50',
            '7.75',
            '8.00',
            '8.25',
            '8.50',
            '8.75',
            '9.00',
            '9.25',
            '9.50',
            '9.75',
            '10.00',
            '10.25',
            '10.50',
            '10.75',
            '11.00',
            '11.25',
            '11.50',
            '11.75',
            '12.00',
            '12.25',
            '12.50',
            '12.75',
            '13.00',
            '13.25',
            '13.50',
            '13.75',
            '14.00',
            '14.25',
            '14.50',
            '14.75',
            '15.00',  
        );

        $grid_data = array();
        $myopia = array(); //cận
        $hyperopia = array(); //viễn
        foreach ($report_data as $item)
        {
            $name = $item['name'];
            //if($name == '1.67 KODAK S-0.75 C-0.00') var_dump($item);
            $arr_name = explode(' ',$name);
            //if($name == '1.67 KODAK S-0.75 C-0.00') var_dump($arr_name);
            if(count($arr_name) > 2) {
                $ct = strtoupper($arr_name[count($arr_name)-1]);
                $ct = str_replace('C','',$ct);

                $st1 = strtoupper($arr_name[count($arr_name)-2]);
                $st = str_replace('S','',$st1);

                $sph = $st;
                $cyl = $ct;
                //if($name == '1.67 KODAK S-0.75 C-0.00') {echo  $sph . '|CYL: '. $cyl .'|QT: '.$item['quantity'] ;}
                $cyl = str_replace('-','',$cyl);
                if(strpos($sph,'-')===0) //Độ cận
                {
                    //if($name == '1.67 KODAK S-0.75 C-0.00') {echo  $sph . '|CYL: '. $cyl .'|QT: '.$item['quantity'] ;}
                    $sph = str_replace('-','',$sph);
                    //if($name == '1.67 KODAK S-0.75 C-0.00') {echo  $sph . '|CYL: '. $cyl .'|QT: '.$item['quantity'] ;}
                    if(isset($map[$sph]) && isset($map[$cyl])) {
                        $s = $map[$sph];
                        $c = $map[$cyl];
                        $myopia[$s][$c] = number_format($item['quantity']);

                    }else{
                        echo $sph . '|'.$cyl.'-> -' . $item['item_number'];
                    }
                    //if($name == '1.67 KODAK S-0.75 C-0.00') {echo  $s . '|CYL: '. $c .'|QT: '.$item['quantity'] ;}
                    //echo $name .'|'; echo  'myopia['.$s.']['.$c.']= '.$myopia[$s][$c].'</br>';
                    //$myopia[] = $map[$sph];

                }else{
                    $sph = str_replace('+','',$sph);
                    if(isset($map[$sph]) && isset($map[$cyl])) {
                    $s = $map[$sph];
                    $c = $map[$cyl];
                    $hyperopia[$s][$c] = number_format($item['quantity']);

                    }else{
                        echo $sph . '|'.$cyl.'-> +' . $item['item_number'];
                    }
                }
                
            } 
        }
        //echo $myopia[4][1];
        $sub_myopia = array();
        $sub_hyperopia = array();
        $sub_group = array();
        $total = 0;
        $cols = 18;
        $rows = 62;

        if($category == '1.56 TC')
        {
            for ($i = 0; $i < 62; $i++) {
                for ($j = 0; $j < 26; $j++) {
                    if (!isset($myopia[$i][$j])) {
                        $myopia[$i][$j] = '';
                    } else {

                    }
                    if (!isset($hyperopia[$i][$j])) {
                        $hyperopia[$i][$j] = '';
                    }
                }
            }
            for($i =1;$i<26;$i++)
            {
                $sub_myopia[$i] = 0;
                for($j =1;$j<62;$j++)
                {

                    if($myopia[$j][$i] !='') {
                        $sub_myopia[$i] = $sub_myopia[$i] + $myopia[$j][$i];
                    }
                }
            }
            //var_dump($myopia);
            $sub_group[0] =0;
            for($i = 1;$i<26;$i++)
            {
                for($j=1;$j<10;$j++)
                {
                    if($myopia[$i][$j] !='') {
                        $sub_group[0] = $sub_group[0] + $myopia[$i][$j];
                    }
                }
            }

            $sub_group[1] =0;
            for($i = 26;$i<34;$i++)
            {
                for($j=1;$j<10;$j++)
                {
                    if($myopia[$i][$j] !='') {
                        $sub_group[1] = $sub_group[1] + $myopia[$i][$j];
                    }
                }
            }
            $sub_group[2] =0;
            for($i = 34;$i<56;$i++)
            {
                for($j=1;$j<10;$j++)
                {
                    if($myopia[$i][$j] !='') {
                        $sub_group[2] = $sub_group[2] + $myopia[$i][$j];
                    }
                }
            }

            $sub_group[3] =0;
            for($i = 1;$i<26;$i++)
            {
                for($j=1;$j<10;$j++)
                {
                    if($hyperopia[$i][$j] !='') {
                        $sub_group[3] = $sub_group[3] + $hyperopia[$i][$j];
                    }
                }
            }

            $sub_group[4] =0;
            for($i = 1;$i<42;$i++)
            {
                for($j=10;$j<14;$j++)
                {
                    if($myopia[$i][$j] !='') {
                        $sub_group[4] = $sub_group[4] + $myopia[$i][$j];
                    }
                }
            }
            $sub_group[5] =0;
            for($i = 1;$i<42;$i++)
            {
                for($j=14;$j<18;$j++)
                {
                    if($myopia[$i][$j] !='') {
                        $sub_group[5] = $sub_group[5] + $myopia[$i][$j];
                    }
                }
            }

            $sub_group[6] =0; //do nothing

            $sub_group[7] =0;
            for($i = 2;$i<9;$i++)
            {
                for($j=10;$j<18;$j++)
                {
                    if($hyperopia[$i][$j] !='') {
                        $sub_group[7] = $sub_group[7] + $hyperopia[$i][$j];
                    }
                }
            }

            for($i =1;$i<26;$i++)
            {
                $sub_hyperopia[$i] = 0;
                for($j =1;$j<26;$j++)
                {

                    if($hyperopia[$j][$i] !='') {
                        $sub_hyperopia[$i] = $sub_hyperopia[$i] + $hyperopia[$j][$i];
                    }
                }
            }

            for($i =10;$i<18;$i++)
            {
                $sub_hyperopia[$i] = 0;
                for($j =1;$j<9;$j++)
                {

                    if($hyperopia[$j][$i] !='') {
                        $sub_hyperopia[$i] = $sub_hyperopia[$i] + $hyperopia[$j][$i];
                    }
                }
            }

            $total = array_sum($sub_hyperopia) + array_sum($sub_myopia);
        }else {
            for ($i = 0; $i < $rows; $i++) {
                for ($j = 0; $j < $cols; $j++) {
                    if (!isset($myopia[$i][$j])) {
                        $myopia[$i][$j] = '';
                    } else {
                        if($myopia[$i][$j] == 0)
                        {
                            $myopia[$i][$j] = '-';
                        }

                    }
                    if (!isset($hyperopia[$i][$j])) {
                        $hyperopia[$i][$j] = '';
                    } else {
                        if($hyperopia[$i][$j] == 0)
                        {
                            $hyperopia[$i][$j] = '-';
                        }
                    }
                }
            }

            for($i =1;$i<$cols;$i++)
            {
                $sub_myopia[$i] = 0;
                for($j =1;$j<$rows;$j++)
                {

                    if($myopia[$j][$i] !='') {
                        $sub_myopia[$i] = (int) $sub_myopia[$i] + (int) $myopia[$j][$i];
                    }
                }
            }
            //var_dump($myopia);
            $sub_group[0] =0;
            for($i = 1;$i<26;$i++)
            {
                for($j=1;$j<10;$j++)
                {
                    if($myopia[$i][$j] !='') {
                        $sub_group[0] = (int) $sub_group[0] + (int) $myopia[$i][$j];
                    }
                }
            }

            $sub_group[1] =0;
            for($i = 26;$i<34;$i++)
            {
                for($j=1;$j<10;$j++)
                {
                    if($myopia[$i][$j] !='') {
                        $sub_group[1] = (int) $sub_group[1] + (int) $myopia[$i][$j];
                    }
                }
            }
            $sub_group[2] =0;
            for($i = 34;$i<56;$i++)
            {
                for($j=1;$j<10;$j++)
                {
                    if($myopia[$i][$j] !='') {
                        $sub_group[2] = (int) $sub_group[2] + (int) $myopia[$i][$j];
                    }
                }
            }

            $sub_group[3] =0;
            for($i = 1;$i<26;$i++)
            {
                for($j=1;$j<10;$j++)
                {
                    if($hyperopia[$i][$j] !='') {
                        $sub_group[3] = (int) $sub_group[3] + (int) $hyperopia[$i][$j];
                    }
                }
            }

            $sub_group[4] =0;
            for($i = 1;$i<42;$i++)
            {
                for($j=10;$j<14;$j++)
                {
                    if($myopia[$i][$j] !='') {
                        $sub_group[4] = (int) $sub_group[4] + (int) $myopia[$i][$j];
                    }
                }
            }
            $sub_group[5] =0;
            for($i = 1;$i<42;$i++)
            {
                for($j=14;$j<18;$j++)
                {
                    if($myopia[$i][$j] !='') {
                        $sub_group[5] = (int) $sub_group[5] + (int) $myopia[$i][$j];
                    }
                }
            }

            $sub_group[6] =0; //do nothing

            $sub_group[7] =0;
            for($i = 2;$i<9;$i++)
            {
                for($j=10;$j<18;$j++)
                {
                    if($hyperopia[$i][$j] !='') {
                        $sub_group[7] = $sub_group[7] + (int) $hyperopia[$i][$j];
                    }
                }
            }

            for($i =1;$i<$cols;$i++)
            {
                $sub_hyperopia[$i] = 0;
                for($j =1;$j<26;$j++)
                {
                    if($hyperopia[$j][$i] !='') {
                        //if ($hyperopia[$j][$i] != '-') {
                            $sub_hyperopia[$i] = $sub_hyperopia[$i] + (int) $hyperopia[$j][$i];
                        //}
                    }
                }
            }

            /* for($i =10;$i<18;$i++)
            {
                $sub_hyperopia[$i] = 0;
                for($j =1;$j<9;$j++)
                {

                    if($hyperopia[$j][$i] !='') {
                        $sub_hyperopia[$i] = $sub_hyperopia[$i] + $hyperopia[$j][$i];
                    }
                }
            } */

            $total = array_sum($sub_myopia) + array_sum($sub_hyperopia);
        }


        //var_dump($myopia);

        $data['total'] = $total;
        $data['map'] = $map;
        $data['re_map'] = $re_map;
        $data['myopia'] = $myopia;
        $data['hyperopia'] = $hyperopia;
        $data['sub_myopia'] = $sub_myopia;
        $data['sub_hyperopia'] = $sub_hyperopia;
        $data['sub_group'] = $sub_group;
        $json = array('result'=>$result,'data'=>$data);
        echo json_encode($json);
    }

    public function inventory_import_lens()
    {
        $this->load->model('reports/Inventory_lens');
        $model = $this->Inventory_lens;
        $data = array();
        $data['item_count'] = $model->getCategoryDropdownArray();
        $stock_locations = $this->xss_clean($this->Stock_location->get_allowed_locations());
        $stock_locations['all'] = $this->lang->line('reports_all');
        $data['stock_locations'] = array_reverse($stock_locations, TRUE);

        $this->load->view('reports/inventory_import_lens_input', $data);
    }

    public function ajax_import_inventory_summary()
    {
        $this->load->model('reports/Inventory_lens');
        $model = $this->Inventory_lens;
        $location_id = $this->input->post('location_id');
        $category = $this->input->post('category');
        $result = 1;

        $inputs = array('location_id'=>$location_id,'category'=>$category);

        $report_data = $model->getData($inputs);

        $data['header'] = array(
            'title'=>'Đơn đặt hàng',
            'company_name'=>'',
            'description'=>$category,
            'brand'=>'',
            'customer'=>'',
            'ordered_date'=>date('d/m/Y'),
            'code'=>'MS đơn đặt hàng',
            'total'=>'Tổng số lượng (miếng)'
        );
        $map = array(
            '-'=>0,
            '0.00'=>1,
            '0.25'=>2,
            '0.50'=>3,
            '0.75'=>4,
            '1.00'=>5,
            '1.25'=>6,
            '1.50'=>7,
            '1.75'=>8,
            '2.00'=>9,
            '2.25'=>10,
            '2.50'=>11,
            '2.75'=>12,
            '3.00'=>13,
            '3.25'=>14,
            '3.50'=>15,
            '3.75'=>16,
            '4.00'=>17,
            '4.25'=>18,
            '4.50'=>19,
            '4.75'=>20,
            '5.00'=>21,
            '5.25'=>22,
            '5.50'=>23,
            '5.75'=>24,
            '6.00'=>25,
            '6.25'=>26,
            '6.50'=>27,
            '6.75'=>28,
            '7.00'=>29,
            '7.25'=>30,
            '7.50'=>31,
            '7.75'=>32,
            '8.00'=>33,
            '8.25'=>34,
            '8.50'=>35,
            '8.75'=>36,
            '9.00'=>37,
            '9.25'=>38,
            '9.50'=>39,
            '9.75'=>40,
            '10.00'=>41,
            '10.25'=>42,
            '10.50'=>43,
            '10.75'=>44,
            '11.00'=>45,
            '11.25'=>46,
            '11.50'=>47,
            '11.75'=>48,
            '12.00'=>49,
            '12.50'=>50,
            '13.00'=>51,
            '13.50'=>52,
            '14.00'=>53,
            '14.50'=>54,
            '15.00'=>55,
        );

        $re_map = array(
            '-',
            '0.00',
            '0.25',
            '0.50',
            '0.75',
            '1.00',
            '1.25',
            '1.50',
            '1.75',
            '2.00',
            '2.25',
            '2.50',
            '2.75',
            '3.00',
            '3.25',
            '3.50',
            '3.75',
            '4.00',
            '4.25',
            '4.50',
            '4.75',
            '5.00',
            '5.25',
            '5.50',
            '5.75',
            '6.00',
            '6.25',
            '6.50',
            '6.75',
            '7.00',
            '7.25',
            '7.50',
            '7.75',
            '8.00',
            '8.25',
            '8.50',
            '8.75',
            '9.00',
            '9.25',
            '9.50',
            '9.75',
            '10.00',
            '10.25',
            '10.50',
            '10.75',
            '11.00',
            '11.25',
            '11.50',
            '11.75',
            '12.00',

            '12.50',

            '13.00',

            '13.50',

            '14.00',

            '14.50',

            '15.00',
        );

        $grid_data = array();
        $myopia = array(); //can
        $hyperopia = array(); //vien
        foreach ($report_data as $item)
        {
            $name = $item['name'];
            $arr_name = explode(' ',$name);

            if(count($arr_name) > 2) {
                $ct = strtoupper($arr_name[count($arr_name)-1]);
                $ct = str_replace('C','',$ct);

                $st = strtoupper($arr_name[count($arr_name)-2]);
                $st = str_replace('S','',$st);
                $sph = $st;
                $cyl = $ct;
                $cyl = str_replace('-','',$cyl);
                if(strpos($sph,'-')===0) //Độ cận
                {
                    $sph = str_replace('-','',$sph);
                    if(isset($map[$sph]) && isset($map[$cyl])) {
                        $s = $map[$sph];
                        $c = $map[$cyl];
                        $myopia[$s][$c] = number_format($item['standard_amount'] - $item['quantity']);
                        if ($myopia[$s][$c] <= 0) {
                            $myopia[$s][$c] = '';
                        }
                    }else{
                        echo $sph . '|'.$cyl .'-> - ' . $item['item_number'];
                    }
                    //$myopia[] = $map[$sph];

                }else{
                    $sph = str_replace('+','',$sph);
                    if(isset($map[$sph]) && isset($map[$cyl])) {
                        $s = $map[$sph];
                        $c = $map[$cyl];

                        $hyperopia[$s][$c] = number_format($item['standard_amount'] - $item['quantity']);
                        if ($hyperopia[$s][$c] <= 0) {
                            $hyperopia[$s][$c] = '';
                        }
                    }else{
                        echo $sph . '|'.$cyl.'-> +' . $item['item_number'];
                    }
                }
            }

        }

        for($i =0;$i < 62;$i++)
        {
            for($j =0;$j<26;$j++)
            {
                if(!isset($myopia[$i][$j]))
                {
                    $myopia[$i][$j] = '';
                }else{

                }
                if(!isset($hyperopia[$i][$j]))
                {
                    $hyperopia[$i][$j]='';
                }
            }
        }
        $sub_myopia = array();
        $sub_hyperopia = array();
        $sub_group = array();
        $total = 0;
        for($i =1;$i<10;$i++)
        {
            $sub_myopia[$i] = 0;
            for($j =1;$j<62;$j++)
            {

                if($myopia[$j][$i] !='') {
                    $sub_myopia[$i] = $sub_myopia[$i] + $myopia[$j][$i];
                }
            }
        }
        //var_dump($myopia);
        $sub_group[0] =0;
        for($i = 1;$i<26;$i++)
        {
            for($j=1;$j<10;$j++)
            {
                if($myopia[$i][$j] !='') {
                    $sub_group[0] = $sub_group[0] + $myopia[$i][$j];
                }
            }
        }

        $sub_group[1] =0;
        for($i = 26;$i<34;$i++)
        {
            for($j=1;$j<10;$j++)
            {
                if($myopia[$i][$j] !='') {
                    $sub_group[1] = $sub_group[1] + $myopia[$i][$j];
                }
            }
        }
        $sub_group[2] =0;
        for($i = 34;$i<56;$i++)
        {
            for($j=1;$j<10;$j++)
            {
                if($myopia[$i][$j] !='') {
                    $sub_group[2] = $sub_group[2] + $myopia[$i][$j];
                }
            }
        }

        $sub_group[3] =0;
        for($i = 1;$i<26;$i++)
        {
            for($j=1;$j<10;$j++)
            {
                if($hyperopia[$i][$j] !='') {
                    $sub_group[3] = $sub_group[3] + $hyperopia[$i][$j];
                }
            }
        }

        $sub_group[4] =0;
        for($i = 1;$i<42;$i++)
        {
            for($j=10;$j<14;$j++)
            {
                if($myopia[$i][$j] !='') {
                    $sub_group[4] = $sub_group[4] + $myopia[$i][$j];
                }
            }
        }
        $sub_group[5] =0;
        for($i = 1;$i<42;$i++)
        {
            for($j=14;$j<18;$j++)
            {
                if($myopia[$i][$j] !='') {
                    $sub_group[5] = $sub_group[5] + $myopia[$i][$j];
                }
            }
        }

        $sub_group[6] =0; //do nothing

        $sub_group[7] =0;
        for($i = 2;$i<9;$i++)
        {
            for($j=10;$j<18;$j++)
            {
                if($hyperopia[$i][$j] !='') {
                    $sub_group[7] = $sub_group[7] + $hyperopia[$i][$j];
                }
            }
        }

        for($i =1;$i<10;$i++)
        {
            $sub_hyperopia[$i] = 0;
            for($j =1;$j<26;$j++)
            {

                if($hyperopia[$j][$i] !='') {
                    $sub_hyperopia[$i] = $sub_hyperopia[$i] + $hyperopia[$j][$i];
                }
            }
        }

        for($i =10;$i<18;$i++)
        {
            $sub_hyperopia[$i] = 0;
            for($j =1;$j<9;$j++)
            {

                if($hyperopia[$j][$i] !='') {
                    $sub_hyperopia[$i] = $sub_hyperopia[$i] + $hyperopia[$j][$i];
                }
            }
        }

        $total = array_sum($sub_group);
        $data['total'] = $total;
        $data['map'] = $map;
        $data['re_map'] = $re_map;
        $data['myopia'] = $myopia;
        $data['hyperopia'] = $hyperopia;
        $data['sub_myopia'] = $sub_myopia;
        $data['sub_hyperopia'] = $sub_hyperopia;
        $data['sub_group'] = $sub_group;
        $json = array('result'=>$result,'data'=>$data);
        echo json_encode($json);
    }

    public function inventory_detail_contact_lens()
    {
        $this->load->model('reports/Inventory_contact_lens');
        $model = $this->Inventory_contact_lens;
        $data = array();
        $data['item_count'] = $model->getCategoryDropdownArray();
        $stock_locations = $this->xss_clean($this->Stock_location->get_allowed_locations());
        $stock_locations['all'] = $this->lang->line('reports_all');
        $data['stock_locations'] = array_reverse($stock_locations, TRUE);

        $this->load->view('reports/inventory_detail_contact_lens_input', $data);
    }
    public function ajax_inventory_contact_lens()
    {
        $this->load->model('reports/Inventory_lens');
        $model = $this->Inventory_lens;
        $location_id = $this->input->post('location_id');
        $category = $this->input->post('category');
        $result = 1;

        $inputs = array('location_id'=>$location_id,'category'=>$category);

        $report_data = $model->getData($inputs);
        $data['header'] = array(
            'title'=>'TỒN KHO ÁP TRÒNG',
            'company_name'=>$category,
            'description'=>$category,
            'brand'=>'',
            'customer'=>'',
            'ordered_date'=>'Ngày lập báo cáo: ' . date('d/m/Y'),
            'code'=>'MS',
            'total'=>'Tổng số lượng (miếng)',
            'address'=>''
        );
        $map = array(
            '-'=>0,
            '0.00'=>1,
            '0.25'=>2,
            '0.50'=>3,
            '0.75'=>4,
            '1.00'=>5,
            '1.25'=>6,
            '1.50'=>7,
            '1.75'=>8,
            '2.00'=>9,
            '2.25'=>10,
            '2.50'=>11,
            '2.75'=>12,
            '3.00'=>13,
            '3.25'=>14,
            '3.50'=>15,
            '3.75'=>16,
            '4.00'=>17,
            '4.25'=>18,
            '4.50'=>19,
            '4.75'=>20,
            '5.00'=>21,
            '5.25'=>22,
            '5.50'=>23,
            '5.75'=>24,
            '6.00'=>25,
            '6.50'=>26,
            '7.00'=>27,
            '7.50'=>28,
            '8.00'=>29,
            '8.50'=>30,
            '9.00'=>31,
            '9.50'=>32,
            '10.00'=>33,
            '6.25'=>34,
            '6.75'=>35,
            '7.25'=>36,
            '7.75'=>37
        );

        $re_map = array(
            '-',
            '0.00',
            '0.25',
            '0.50',
            '0.75',
            '1.00',
            '1.25',
            '1.50',
            '1.75',
            '2.00',
            '2.25',
            '2.50',
            '2.75',
            '3.00',
            '3.25',
            '3.50',
            '3.75',
            '4.00',
            '4.25',
            '4.50',
            '4.75',
            '5.00',
            '5.25',
            '5.50',
            '5.75',
            '6.00',
            '6.50',
            '7.00',
            '7.50',
            '8.00',
            '8.50',
            '9.00',
            '9.50',
            '10.00',
            '6.25',
            '6.75',
            '7.25',
            '7.75'
        );

        $myopia = array(); //can
        $mywater = array();//
        if($category == 'Bách Quang')
        {
            $key_arr = array(
                'Biomedics 55' => 'Biomedics 55',
                'BioMedics 1day' => 'BioMedics 1day',
                'MAXIM 1 Day Honey' => 'MAXIM 1 Day Honey',
                'MAXIM 1 Day Black' => 'MAXIM 1 Day Black',
                'MAXIM 1 Day Gray' => 'MAXIM 1 Day Gray',
                'MAXIM 1 Day Brownie' => 'MAXIM 1 Day Brownie',
                'MAXIM 1 Day Blue' => 'MAXIM 1 Day Blue',
                'Optima FW' => 'Optima FW',
                'Soflens 59 Monthly' => 'Soflens 59 Monthly',
                'Freshkon AE 1Day Magnetic Grey' => 'Freshkon AE 1Day Magnetic Grey',
                'Freshkon AE 1Day Mystical Black' => 'Freshkon AE 1Day Mystical Black',
                'Freshkon AE 1Day Winsome Brown' => 'Freshkon AE 1Day Winsome Brown',
                'Freshkon AE 3Month Magnetic Grey' => 'Freshkon AE 3Month Magnetic Grey',
                'Freshkon AE 3Month Mystical Black' => 'Freshkon AE 3Month Mystical Black',
                'Freshkon AE 3Month Winsome Brown' => 'Freshkon AE 3Month Winsome Brown',
                "Nước" => 'Nước'
            );
            foreach ($report_data as $item) {
                $name = $item['name'];
                $arr_name = explode(' ', $name);


                if (count($arr_name) > 1) {

                    $st1 = strtoupper($arr_name[count($arr_name) - 1]);
                    //update by ManhVT to add item without Diop such as water to drop, clean lens
                    if (strpos($st1, 'D-') == 0) {
                        $st = str_replace('D-', '', $st1);
                        unset($arr_name[count($arr_name) - 1]);
                        $fname = implode(' ', $arr_name);
                        $fname = trim($fname);
                        //echo $st;

                        $sph = $st;

                        if (isset($map[$sph]) && isset($key_arr[$fname])) {
                            $s = $map[$sph];
                            $myopia[$key_arr[$fname]][$s] = number_format($item['quantity']);
                            if ($myopia[$key_arr[$fname]][$s] < 0) {
                                $myopia[$key_arr[$fname]][$s] = 0;
                            }
                        } else {
                            echo $sph . '|->' . $fname . '->' . $item['item_number'];
                        }
                        //$myopia[] = $map[$sph];

                    } else {
                        $mywater[$name] = number_format($item['quantity']);
                    }
                }

            }

            for ($i = 0; $i < 34; $i++) {
                foreach ($key_arr as $key => $value) {
                    if (!isset($myopia[$value][$i])) {
                        $myopia[$value][$i] = '';
                    } else {

                    }
                }

            }
            $maxim1day = array();

            foreach ($myopia['MAXIM 1 Day Honey'] as $key => $value) {
                $maxim1day['Honey'][$key] = $value;
            }
            foreach ($myopia['MAXIM 1 Day Black'] as $key => $value) {
                $maxim1day['Black'][$key] = $value;
            }
            foreach ($myopia['MAXIM 1 Day Gray'] as $key => $value) {
                $maxim1day['Gray'][$key] = $value;
            }
            foreach ($myopia['MAXIM 1 Day Brownie'] as $key => $value) {
                $maxim1day['Brownie'][$key] = $value;
            }

            foreach ($myopia['MAXIM 1 Day Blue'] as $key => $value) {
                $maxim1day['Blue'][$key] = $value;
            }


            $biomedics55 = $myopia['Biomedics 55'];
            $biomedic1day = $myopia['BioMedics 1day'];

            $biomedics55['sum'] = array_sum($myopia['Biomedics 55']);
            $biomedic1day['sum'] = array_sum($myopia['BioMedics 1day']);

            $maxim1day['Honey']['sum'] = array_sum($maxim1day['Honey']);
            $maxim1day['Black']['sum'] = array_sum($maxim1day['Black']);
            $maxim1day['Gray']['sum'] = array_sum($maxim1day['Gray']);
            $maxim1day['Brownie']['sum'] = array_sum($maxim1day['Brownie']);
            $maxim1day['Blue']['sum'] = array_sum($maxim1day['Blue']);


            $sub_myopia = array();
            $sub_group = array();
            $total = 0;

            //var_dump($myopia);
            $total = array_sum($sub_group);
            $data['total'] = $total;
            $data['map'] = $map;
            $data['re_map'] = $re_map;
            $data['biomedics55'] = $biomedics55;
            $data['maxim1day'] = $maxim1day;
            $data['biomedic1day'] = $biomedic1day;
            $data['sub_myopia'] = $sub_myopia;
            $data['sub_group'] = $sub_group;
            $data['category'] = $category;
        }
        elseif ($category == 'Seed')
        {
            $key_arr = array(
                'Biomedics 55' => 'Biomedics 55',
                'BioMedics 1day' => 'BioMedics 1day',
                'MAXIM 1 Day Honey' => 'MAXIM 1 Day Honey',
                'MAXIM 1 Day Black' => 'MAXIM 1 Day Black',
                'MAXIM 1 Day Gray' => 'MAXIM 1 Day Gray',
                'MAXIM 1 Day Brownie' => 'MAXIM 1 Day Brownie',
                'MAXIM 1 Day Blue' => 'MAXIM 1 Day Blue',
                'Optima FW' => 'Optima FW',
                'Soflens 59 Monthly' => 'Soflens 59 Monthly',
                'Freshkon AE 1Day Magnetic Grey' => 'Freshkon AE 1Day Magnetic Grey',
                'Freshkon AE 1Day Mystical Black' => 'Freshkon AE 1Day Mystical Black',
                'Freshkon AE 1Day Winsome Brown' => 'Freshkon AE 1Day Winsome Brown',
                'Freshkon AE 3Month Magnetic Grey' => 'Freshkon AE 3Month Magnetic Grey',
                'Freshkon AE 3Month Mystical Black' => 'Freshkon AE 3Month Mystical Black',
                'Freshkon AE 3Month Winsome Brown' => 'Freshkon AE 3Month Winsome Brown',
                'Nước' => 'Nước',
                'SEED 1 day Pure' => 'SEED 1 day Pure',
                'SEED 1 day Base'=>'SEED 1 day Base',
                'SEED 1 day Natural'=>'SEED 1 day Natural',
                'SEED 1 day Rich'=>'SEED 1 day Rich',
                'SEED 1 day Grace'=>'SEED 1 day Grace',
                'SEED 1monthly'=>'SEED 1monthly',
                'SEED 1monthly Cocoa' => 'SEED 1monthly Cocoa',
                'SEED 1monthly Gray'=>'SEED 1monthly Gray',
                'SEED 1monthly Gold Brown'=>'SEED 1monthly Gold Brown',
                'SEED 1monthly Pink'=>'SEED 1monthly Pink'
            );
            foreach ($report_data as $item) {
                $name = $item['name'];
                $arr_name = explode(' ', $name);
                if (count($arr_name) > 1) {
                    $st1 = strtoupper($arr_name[count($arr_name) - 1]);
                    //update by ManhVT to add item without Diop such as water to drop, clean lens
                    if (strpos($st1, 'D-') === 0) {
                        $st = str_replace('D-', '', $st1);
                        unset($arr_name[count($arr_name) - 1]);
                        $fname = implode(' ', $arr_name);
                        $fname = trim($fname);
                        //echo $st;
                        $sph = $st;
                        if (isset($map[$sph]) && isset($key_arr[$fname])) {
                            $s = $map[$sph];
                            $myopia[$key_arr[$fname]][$s] = number_format($item['quantity']);
                            if ($myopia[$key_arr[$fname]][$s] < 0) {
                                $myopia[$key_arr[$fname]][$s] = 0;
                            }
                        } else {
                            echo $sph . '|->' . $fname . '->' . $item['item_number'];
                        }
                        //$myopia[] = $map[$sph];

                    } else {
                        $mywater[$name] = number_format($item['quantity']);
                    }
                }

            }
            //var_dump($mywater);

            for ($i = 0; $i < 34; $i++) {
                foreach ($key_arr as $key => $value) {
                    if (!isset($myopia[$value][$i])) {
                        $myopia[$value][$i] = '';
                    } else {

                    }
                }

            }
            $fr1day = array();
            $fr1month = array();

            foreach ($myopia['SEED 1 day Pure'] as $key => $value) {
                $fr1day['pure'][$key] = $value;
            }
            foreach ($myopia['SEED 1 day Base'] as $key => $value) {
                $fr1day['base'][$key] = $value;
            }
            foreach ($myopia['SEED 1 day Natural'] as $key => $value) {
                $fr1day['natural'][$key] = $value;
            }
            foreach ($myopia['SEED 1 day Rich'] as $key => $value) {
                $fr1day['rich'][$key] = $value;
            }
            foreach ($myopia['SEED 1 day Grace'] as $key => $value) {
                $fr1day['grace'][$key] = $value;
            }

            foreach ($myopia['SEED 1monthly'] as $key => $value) {
                $fr1month['SEED1monthly'][$key] = $value;
            }
            foreach ($myopia['SEED 1monthly Cocoa'] as $key => $value) {
                $fr1month['Cocoa'][$key] = $value;
            }
            foreach($myopia['SEED 1monthly Gray'] as $key => $value)
            {
                $fr1month['Gray'][$key] = $value;
            }
            foreach($myopia['SEED 1monthly Gold Brown'] as $key => $value)
            {
                $fr1month['Gold Brown'][$key] = $value;
            }

            foreach($myopia['SEED 1monthly Pink'] as $key => $value)
            {
                $fr1month['Pink'][$key] = $value;
            }
            
            
            $fr1month['SEED1monthly']['sum'] = array_sum($fr1month['SEED1monthly']);
            $fr1month['Cocoa']['sum'] = array_sum($fr1month['Cocoa']);
            $fr1month['Gray']['sum'] = array_sum($fr1month['Gray']);
            $fr1month['Gold Brown']['sum'] = array_sum($fr1month['Gold Brown']);
            $fr1month['Pink']['sum'] = array_sum($fr1month['Pink']);
            //---------------- MONTHLY --------------------------
            /*foreach ($myopia['Freshkon AE 3Month Magnetic Grey'] as $key => $value) {
                $fr1month['Magnetic Grey'][$key] = $value;
            }

            foreach ($myopia['Freshkon AE 3Month Mystical Black'] as $key => $value) {
                $fr1month['Mystical Black'][$key] = $value;
            }
            foreach ($myopia['Freshkon AE 3Month Winsome Brown'] as $key => $value) {
                $fr1month['Winsome Brown'][$key] = $value;
            }*/


            /*$soflens59monthly = $myopia['Soflens 59 Monthly'];
            $optimafw = $myopia['Optima FW'];


            $soflens59monthly['sum'] = array_sum($myopia['Soflens 59 Monthly']);
            $optimafw['sum'] = array_sum($myopia['Optima FW']);*/

            $fr1day['pure']['sum'] = array_sum($fr1day['pure']);
            $fr1day['base']['sum'] = array_sum($fr1day['base']);
            $fr1day['natural']['sum'] = array_sum($fr1day['natural']);
            $fr1day['rich']['sum'] = array_sum($fr1day['rich']);
            $fr1day['grace']['sum'] = array_sum($fr1day['grace']);

/*            $fr1month['Magnetic Grey']['sum'] = array_sum($fr1month['Magnetic Grey']);
            $fr1month['Mystical Black']['sum'] = array_sum($fr1month['Mystical Black']);
            $fr1month['Winsome Brown']['sum'] = array_sum($fr1month['Winsome Brown']);*/


            $sub_myopia = array();
            $sub_group = array();
            $total = 0;

            //var_dump($myopia);
            $total = array_sum($sub_group);
            $data['total'] = $total;
            $data['map'] = $map;
            $data['re_map'] = $re_map;

            //$data['soflens59monthly'] = $soflens59monthly;
            //$data['optimafw'] = $optimafw;

            $data['fr1day'] = $fr1day;
            $data['fr1month'] = $fr1month;

            $data['sub_myopia'] = $sub_myopia;
            $data['sub_group'] = $sub_group;
            $data['mywater'] = $mywater;
            $data['category'] = $category;
        }
        elseif ($category == 'Ann 365 len')
        {
            $key_arr = array(
                'Biomedics 55' => 'Biomedics 55',
                'BioMedics 1day' => 'BioMedics 1day',
                'MAXIM 1 Day Honey' => 'MAXIM 1 Day Honey',
                'MAXIM 1 Day Black' => 'MAXIM 1 Day Black',
                'MAXIM 1 Day Gray' => 'MAXIM 1 Day Gray',
                'MAXIM 1 Day Brownie' => 'MAXIM 1 Day Brownie',
                'MAXIM 1 Day Blue' => 'MAXIM 1 Day Blue',
                'Optima FW' => 'Optima FW',
                'Soflens 59 Monthly' => 'Soflens 59 Monthly',
                'Freshkon AE 1Day Magnetic Grey' => 'Freshkon AE 1Day Magnetic Grey',
                'Freshkon AE 1Day Mystical Black' => 'Freshkon AE 1Day Mystical Black',
                'Freshkon AE 1Day Winsome Brown' => 'Freshkon AE 1Day Winsome Brown',
                'Freshkon AE 3Month Magnetic Grey' => 'Freshkon AE 3Month Magnetic Grey',
                'Freshkon AE 3Month Mystical Black' => 'Freshkon AE 3Month Mystical Black',
                'Freshkon AE 3Month Winsome Brown' => 'Freshkon AE 3Month Winsome Brown',
                'Nước' => 'Nước',
                'Ann 1 day Brown'=>'Ann 1 day Brown',
                'Ann 1 day Choco'=>'Ann 1 day Choco',
                'Ann 1 day Grey'=>'Ann 1 day Grey'
            );
            foreach ($report_data as $item) {
                $name = $item['name'];
                $arr_name = explode(' ', $name);
                if (count($arr_name) > 1) {
                    $st1 = strtoupper($arr_name[count($arr_name) - 1]);
                    //update by ManhVT to add item without Diop such as water to drop, clean lens
                    if (strpos($st1, 'D-') === 0) {
                        $st = str_replace('D-', '', $st1);
                        unset($arr_name[count($arr_name) - 1]);
                        $fname = implode(' ', $arr_name);
                        $fname = trim($fname);
                        //echo $st;
                        $sph = $st;
                        if (isset($map[$sph]) && isset($key_arr[$fname])) {
                            $s = $map[$sph];
                            $myopia[$key_arr[$fname]][$s] = number_format($item['quantity']);
                            if ($myopia[$key_arr[$fname]][$s] < 0) {
                                $myopia[$key_arr[$fname]][$s] = 0;
                            }
                        } else {
                            echo $sph . '|->' . $fname . '->' . $item['item_number'];
                        }
                        //$myopia[] = $map[$sph];

                    } else {
                        $mywater[$name] = number_format($item['quantity']);
                    }
                }

            }
            //var_dump($mywater);
            for ($i = 0; $i < 34; $i++) {
                foreach ($key_arr as $key => $value) {
                    if (!isset($myopia[$value][$i])) {
                        $myopia[$value][$i] = '';
                    } else {

                    }
                }

            }
            $fr1day = array();
            //$fr1month = array();
            foreach ($myopia['Ann 1 day Choco'] as $key => $value) {
                $fr1day['choco'][$key] = $value;
            }
            foreach ($myopia['Ann 1 day Grey'] as $key => $value) {
                $fr1day['grey'][$key] = $value;
            }
            foreach ($myopia['Ann 1 day Brown'] as $key => $value) {
                $fr1day['brown'][$key] = $value;
            }

            //$fr1month['SEED1monthly']['sum'] = array_sum($fr1month['SEED1monthly']);
            //---------------- MONTHLY --------------------------
            /*foreach ($myopia['Freshkon AE 3Month Magnetic Grey'] as $key => $value) {
                $fr1month['Magnetic Grey'][$key] = $value;
            }

            foreach ($myopia['Freshkon AE 3Month Mystical Black'] as $key => $value) {
                $fr1month['Mystical Black'][$key] = $value;
            }
            foreach ($myopia['Freshkon AE 3Month Winsome Brown'] as $key => $value) {
                $fr1month['Winsome Brown'][$key] = $value;
            }*/

            /*$soflens59monthly = $myopia['Soflens 59 Monthly'];
            $optimafw = $myopia['Optima FW'];

            $soflens59monthly['sum'] = array_sum($myopia['Soflens 59 Monthly']);
            $optimafw['sum'] = array_sum($myopia['Optima FW']);*/

            $fr1day['choco']['sum'] = array_sum($fr1day['choco']);
            $fr1day['brown']['sum'] = array_sum($fr1day['brown']);
            $fr1day['grey']['sum'] = array_sum($fr1day['grey']);


            /*            $fr1month['Magnetic Grey']['sum'] = array_sum($fr1month['Magnetic Grey']);
                        $fr1month['Mystical Black']['sum'] = array_sum($fr1month['Mystical Black']);
                        $fr1month['Winsome Brown']['sum'] = array_sum($fr1month['Winsome Brown']);*/


            $sub_myopia = array();
            $sub_group = array();
            $total = 0;

            //var_dump($myopia);
            $total = array_sum($sub_group);
            $data['total'] = $total;
            $data['map'] = $map;
            $data['re_map'] = $re_map;

            //$data['soflens59monthly'] = $soflens59monthly;
            //$data['optimafw'] = $optimafw;

            $data['fr1day'] = $fr1day;
            //$data['fr1month'] = $fr1month;

            $data['sub_myopia'] = $sub_myopia;
            $data['sub_group'] = $sub_group;
            $data['mywater'] = $mywater;
            $data['category'] = $category;
        }
        elseif($category == 'CLEARLAB-USA')
        {
            //var_dump($report_data);
            $key_arr = array(
                'KÍNH ÁP TRÒNG DÙNG 1 NGÀY CLEAR 1 DAY'=>'KÍNH ÁP TRÒNG DÙNG 1 NGÀY CLEAR 1 DAY',
                'KÍNH ÁP TRÒNG DÙNG 1 THÁNG CLEAR ALL DAY' => 'KÍNH ÁP TRÒNG DÙNG 1 THÁNG CLEAR ALL DAY',
                'KÍNH ÁP TRÒNG DÙNG 1 NGÀY MÀU GRAY CL241' =>'KÍNH ÁP TRÒNG DÙNG 1 NGÀY MÀU GRAY CL241',
                'KÍNH ÁP TRÒNG DÙNG 1 NGÀY MÀU TAN BROWN CL243' =>'KÍNH ÁP TRÒNG DÙNG 1 NGÀY MÀU TAN BROWN CL243',
                'KÍNH ÁP TRÒNG DÙNG 1 NGÀY MÀU BLACK CL244' => 'KÍNH ÁP TRÒNG DÙNG 1 NGÀY MÀU BLACK CL244',
                'KÍNH ÁP TRÒNG DÙNG 1 NGÀY MÀU GRAY FL334' =>'KÍNH ÁP TRÒNG DÙNG 1 NGÀY MÀU GRAY FL334',
                'KÍNH ÁP TRÒNG DÙNG 1 NGÀY MÀU GREEN FL334'=>'KÍNH ÁP TRÒNG DÙNG 1 NGÀY MÀU GREEN FL334',
                'KÍNH ÁP TRÒNG MÀU 1 THÁNG  BROWN CL261N' =>'KÍNH ÁP TRÒNG MÀU 1 THÁNG  BROWN CL261N',
                'KÍNH ÁP TRÒNG MÀU 1 THÁNG  GRAY CL263N' => 'KÍNH ÁP TRÒNG MÀU 1 THÁNG  GRAY CL263N',
                'KÍNH ÁP TRÒNG MÀU 3 THÁNG BLACK B11N' => 'KÍNH ÁP TRÒNG MÀU 3 THÁNG BLACK B11N',
                'KÍNH ÁP TRÒNG MÀU 3 THÁNG BROWN A32N' =>'KÍNH ÁP TRÒNG MÀU 3 THÁNG BROWN A32N',
                'KÍNH ÁP TRÒNG MÀU 3 THÁNG BROWN CV24N' =>'KÍNH ÁP TRÒNG MÀU 3 THÁNG BROWN CV24N',
                'KÍNH ÁP TRÒNG MÀU 3 THÁNG GALAXY GRAY CL357N' =>'KÍNH ÁP TRÒNG MÀU 3 THÁNG GALAXY GRAY CL357N',
                'KÍNH ÁP TRÒNG MÀU 3 THÁNG GREEN CL305N'=>'KÍNH ÁP TRÒNG MÀU 3 THÁNG GREEN CL305N',
                'KÍNH ÁP TRÒNG MÀU 3 THÁNG OLIVE CL370N' =>'KÍNH ÁP TRÒNG MÀU 3 THÁNG OLIVE CL370N',
                'KÍNH ÁP TRÒNG MÀU 3 THÁNG SILVER GRAY CL369N' =>'KÍNH ÁP TRÒNG MÀU 3 THÁNG SILVER GRAY CL369N',
                'KÍNH ÁP TRÒNG MÀU 3 THÁNG SLATE GRAY CV26 N' => 'KÍNH ÁP TRÒNG MÀU 3 THÁNG SLATE GRAY CV26 N',
                'NƯỚC NGÂM ÁP TRÒNG' => 'NƯỚC NGÂM ÁP TRÒNG',
            );
            foreach ($report_data as $item) {
                $name = $item['name'];
                $arr_name = explode(' ', $name);
                if (count($arr_name) > 1) {
                    $st1 = strtoupper($arr_name[count($arr_name) - 1]);
                    //update by ManhVT to add item without Diop such as water to drop, clean lens
                    if (strpos($st1, 'S-') === 0) {
                        $st = str_replace('S-', '', $st1);
                        unset($arr_name[count($arr_name) - 1]);
                        $fname = implode(' ', $arr_name);
                        $fname = trim($fname);
                        //echo $st;
                        $sph = $st;
                        if (isset($map[$sph]) && isset($key_arr[$fname])) {
                            $s = $map[$sph];
                            $myopia[$key_arr[$fname]][$s] = number_format($item['quantity']);
                            if ($myopia[$key_arr[$fname]][$s] < 0) {
                                $myopia[$key_arr[$fname]][$s] = 0;
                            }
                        } else {
                            echo $sph . '|->' . $fname . '->' . $item['item_number'];
                        }
                        //$myopia[] = $map[$sph];

                    } else {
                        $mywater[$name] = number_format($item['quantity']);
                    }
                }

            }
            //var_dump($mywater);
            for ($i = 0; $i < 34; $i++) {
                foreach ($key_arr as $key => $value) {
                    if (!isset($myopia[$value][$i])) {
                        $myopia[$value][$i] = '';
                    } else {

                    }
                }

            }
            $fr1day = array();
            //$fr1month = array();
            foreach ($myopia['KÍNH ÁP TRÒNG DÙNG 1 NGÀY CLEAR 1 DAY'] as $key => $value) {
                $fr1day['CLEAR 1 DAY'][$key] = $value;
            }
            foreach ($myopia['KÍNH ÁP TRÒNG DÙNG 1 NGÀY MÀU GRAY CL241'] as $key => $value) {
                $fr1day['GRAY CL241'][$key] = $value;
            }
            foreach ($myopia['KÍNH ÁP TRÒNG DÙNG 1 NGÀY MÀU TAN BROWN CL243'] as $key => $value) {
                $fr1day['BROWN CL243'][$key] = $value;
            }

            foreach ($myopia['KÍNH ÁP TRÒNG DÙNG 1 NGÀY MÀU BLACK CL244'] as $key => $value) {
                $fr1day['BLACK CL244'][$key] = $value;
            }
            foreach ($myopia['KÍNH ÁP TRÒNG DÙNG 1 NGÀY MÀU GRAY FL334'] as $key => $value) {
                $fr1day['GRAY FL334'][$key] = $value;
            }
            foreach ($myopia['KÍNH ÁP TRÒNG DÙNG 1 NGÀY MÀU GREEN FL334'] as $key => $value) {
                $fr1day['GREEN FL334'][$key] = $value;
            }


            $fr1day['CLEAR 1 DAY']['sum'] = array_sum($fr1day['CLEAR 1 DAY']);
            $fr1day['GRAY CL241']['sum'] = array_sum($fr1day['GRAY CL241']);
            $fr1day['BROWN CL243']['sum'] = array_sum($fr1day['BROWN CL243']);

            $fr1day['BLACK CL244']['sum'] = array_sum($fr1day['BLACK CL244']);
            $fr1day['GRAY FL334']['sum'] = array_sum($fr1day['GRAY FL334']);
            $fr1day['GREEN FL334']['sum'] = array_sum($fr1day['GREEN FL334']);


            $fr1month = array();
            
            foreach ($myopia['KÍNH ÁP TRÒNG DÙNG 1 THÁNG CLEAR ALL DAY'] as $key => $value) {
                $fr1month['CLEAR ALL DAY'][$key] = $value;
            }
            foreach ($myopia['KÍNH ÁP TRÒNG MÀU 1 THÁNG  BROWN CL261N'] as $key => $value) {
                $fr1month['BROWN CL261N'][$key] = $value;
            }
            foreach($myopia['KÍNH ÁP TRÒNG MÀU 1 THÁNG  GRAY CL263N'] as $key => $value)
            {
                $fr1month['GRAY CL263N'][$key] = $value;
            }

            $fr1month['CLEAR ALL DAY']['sum'] = array_sum($fr1month['CLEAR ALL DAY']);
            $fr1month['BROWN CL261N']['sum'] = array_sum($fr1month['BROWN CL261N']);
            $fr1month['GRAY CL263N']['sum'] = array_sum($fr1month['GRAY CL263N']);


            $fr3month = array();
            
            foreach ($myopia['KÍNH ÁP TRÒNG MÀU 3 THÁNG BLACK B11N'] as $key => $value) {
                $fr3month['BLACK B11N'][$key] = $value;
            }
            foreach ($myopia['KÍNH ÁP TRÒNG MÀU 3 THÁNG BROWN A32N'] as $key => $value) {
                $fr3month['BROWN A32N'][$key] = $value;
            }
            foreach ($myopia['KÍNH ÁP TRÒNG MÀU 3 THÁNG BROWN CV24N'] as $key => $value) {
                $fr3month['BROWN CV24N'][$key] = $value;
            }
            foreach ($myopia['KÍNH ÁP TRÒNG MÀU 3 THÁNG GALAXY GRAY CL357N'] as $key => $value) {
                $fr3month['GRAY CL357N'][$key] = $value;
            }
            foreach ($myopia['KÍNH ÁP TRÒNG MÀU 3 THÁNG OLIVE CL370N'] as $key => $value) {
                $fr3month['OLIVE CL370N'][$key] = $value;
            }
            foreach ($myopia['KÍNH ÁP TRÒNG MÀU 3 THÁNG SILVER GRAY CL369N'] as $key => $value) {
                $fr3month['GRAY CL369N'][$key] = $value;
            }
            foreach ($myopia['KÍNH ÁP TRÒNG MÀU 3 THÁNG SLATE GRAY CV26 N'] as $key => $value) {
                $fr3month['SLATE GRAY CV26 N'][$key] = $value;
            }

            $fr3month['BLACK B11N']['sum'] = array_sum($fr3month['BLACK B11N']);
            $fr3month['BROWN A32N']['sum'] = array_sum($fr3month['BROWN A32N']);
            $fr3month['BROWN CV24N']['sum'] = array_sum($fr3month['BROWN CV24N']);
            $fr3month['GRAY CL357N']['sum'] = array_sum($fr3month['GRAY CL357N']);
            $fr3month['OLIVE CL370N']['sum'] = array_sum($fr3month['OLIVE CL370N']);
            $fr3month['GRAY CL369N']['sum'] = array_sum($fr3month['GRAY CL369N']);
            $fr3month['SLATE GRAY CV26 N']['sum'] = array_sum($fr3month['SLATE GRAY CV26 N']);
            
            //$fr1month['SEED1monthly']['sum'] = array_sum($fr1month['SEED1monthly']);
            //---------------- MONTHLY --------------------------
            /*foreach ($myopia['Freshkon AE 3Month Magnetic Grey'] as $key => $value) {
                $fr1month['Magnetic Grey'][$key] = $value;
            }

            foreach ($myopia['Freshkon AE 3Month Mystical Black'] as $key => $value) {
                $fr1month['Mystical Black'][$key] = $value;
            }
            foreach ($myopia['Freshkon AE 3Month Winsome Brown'] as $key => $value) {
                $fr1month['Winsome Brown'][$key] = $value;
            }*/

            /*$soflens59monthly = $myopia['Soflens 59 Monthly'];
            $optimafw = $myopia['Optima FW'];

            $soflens59monthly['sum'] = array_sum($myopia['Soflens 59 Monthly']);
            $optimafw['sum'] = array_sum($myopia['Optima FW']);*/

            


            /*            $fr1month['Magnetic Grey']['sum'] = array_sum($fr1month['Magnetic Grey']);
                        $fr1month['Mystical Black']['sum'] = array_sum($fr1month['Mystical Black']);
                        $fr1month['Winsome Brown']['sum'] = array_sum($fr1month['Winsome Brown']);*/

            $sub_myopia = array();
            $sub_group = array();
            $total = 0;

            //var_dump($myopia);
            $total = array_sum($sub_group);
            $data['total'] = $total;
            $data['map'] = $map;
            $data['re_map'] = $re_map;

            //$data['soflens59monthly'] = $soflens59monthly;
            //$data['optimafw'] = $optimafw;

            $data['fr1day'] = $fr1day;
            $data['fr1month'] = $fr1month;
            $data['fr3month'] = $fr3month;

            $data['sub_myopia'] = $sub_myopia;
            $data['sub_group'] = $sub_group;
            $data['mywater'] = $mywater;
            $data['category'] = $category;

        }
        else{
            $key_arr = array(
                'Biomedics 55' => 'Biomedics 55',
                'BioMedics 1day' => 'BioMedics 1day',
                'MAXIM 1 Day Honey' => 'MAXIM 1 Day Honey',
                'MAXIM 1 Day Black' => 'MAXIM 1 Day Black',
                'MAXIM 1 Day Gray' => 'MAXIM 1 Day Gray',
                'MAXIM 1 Day Brownie' => 'MAXIM 1 Day Brownie',
                'MAXIM 1 Day Blue' => 'MAXIM 1 Day Blue',
                'Optima FW' => 'Optima FW',
                'Soflens 59 Monthly' => 'Soflens 59 Monthly',
                'Freshkon AE 1Day Magnetic Grey' => 'Freshkon AE 1Day Magnetic Grey',
                'Freshkon AE 1Day Mystical Black' => 'Freshkon AE 1Day Mystical Black',
                'Freshkon AE 1Day Winsome Brown' => 'Freshkon AE 1Day Winsome Brown',
                'Freshkon AE 3Month Magnetic Grey' => 'Freshkon AE 3Month Magnetic Grey',
                'Freshkon AE 3Month Mystical Black' => 'Freshkon AE 3Month Mystical Black',
                'Freshkon AE 3Month Winsome Brown' => 'Freshkon AE 3Month Winsome Brown',
                'Nước' => 'Nước',
                'SEED 1 day Pure' => 'SEED 1 day Pure',
                'SEED 1 day Base'=>'SEED 1 day Base',
                'SEED 1 day Natural'=>'SEED 1 day Natural',
                'SEED 1 day Rich'=>'SEED 1 day Rich'
            );
            foreach ($report_data as $item) {
                $name = $item['name'];
                $arr_name = explode(' ', $name);
                if (count($arr_name) > 1) {
                    $st1 = strtoupper($arr_name[count($arr_name) - 1]);
                    //update by ManhVT to add item without Diop such as water to drop, clean lens
                    if (strpos($st1, 'D-') === 0) {
                        $st = str_replace('D-', '', $st1);
                        unset($arr_name[count($arr_name) - 1]);
                        $fname = implode(' ', $arr_name);
                        $fname = trim($fname);
                        //echo $st;
                        $sph = $st;
                        if (isset($map[$sph]) && isset($key_arr[$fname])) {
                            $s = $map[$sph];
                            $myopia[$key_arr[$fname]][$s] = number_format($item['quantity']);
                            if ($myopia[$key_arr[$fname]][$s] < 0) {
                                $myopia[$key_arr[$fname]][$s] = 0;
                            }
                        } else {
                            echo $sph . '|->' . $fname . '->' . $item['item_number'];
                        }
                        //$myopia[] = $map[$sph];

                    } else {
                        $mywater[$name] = number_format($item['quantity']);
                    }
                }

            }
            //var_dump($mywater);

            for ($i = 0; $i < 34; $i++) {
                foreach ($key_arr as $key => $value) {
                    if (!isset($myopia[$value][$i])) {
                        $myopia[$value][$i] = '';
                    } else {

                    }
                }
            }
            $fr1day = array();
            $fr1month = array();

            foreach ($myopia['Freshkon AE 1Day Magnetic Grey'] as $key => $value) {
                $fr1day['Magnetic Grey'][$key] = $value;
            }
            foreach ($myopia['Freshkon AE 1Day Mystical Black'] as $key => $value) {
                $fr1day['Mystical Black'][$key] = $value;
            }
            foreach ($myopia['Freshkon AE 1Day Winsome Brown'] as $key => $value) {
                $fr1day['Winsome Brown'][$key] = $value;
            }
            //---------------- MONTHLY --------------------------
            foreach ($myopia['Freshkon AE 3Month Magnetic Grey'] as $key => $value) {
                $fr1month['Magnetic Grey'][$key] = $value;
            }

            foreach ($myopia['Freshkon AE 3Month Mystical Black'] as $key => $value) {
                $fr1month['Mystical Black'][$key] = $value;
            }
            foreach ($myopia['Freshkon AE 3Month Winsome Brown'] as $key => $value) {
                $fr1month['Winsome Brown'][$key] = $value;
            }


            $soflens59monthly = $myopia['Soflens 59 Monthly'];
            $optimafw = $myopia['Optima FW'];


            $soflens59monthly['sum'] = array_sum($myopia['Soflens 59 Monthly']);
            $optimafw['sum'] = array_sum($myopia['Optima FW']);

            $fr1day['Magnetic Grey']['sum'] = array_sum($fr1day['Magnetic Grey']);
            $fr1day['Mystical Black']['sum'] = array_sum($fr1day['Mystical Black']);
            $fr1day['Winsome Brown']['sum'] = array_sum($fr1day['Winsome Brown']);

            $fr1month['Magnetic Grey']['sum'] = array_sum($fr1month['Magnetic Grey']);
            $fr1month['Mystical Black']['sum'] = array_sum($fr1month['Mystical Black']);
            $fr1month['Winsome Brown']['sum'] = array_sum($fr1month['Winsome Brown']);


            $sub_myopia = array();
            $sub_group = array();
            $total = 0;

            //var_dump($myopia);
            $total = array_sum($sub_group);
            $data['total'] = $total;
            $data['map'] = $map;
            $data['re_map'] = $re_map;

            $data['soflens59monthly'] = $soflens59monthly;
            $data['optimafw'] = $optimafw;

            $data['fr1day'] = $fr1day;
            $data['fr1month'] = $fr1month;

            $data['sub_myopia'] = $sub_myopia;
            $data['sub_group'] = $sub_group;
            $data['mywater'] = $mywater;
            $data['category'] = $category;
        }
        $json = array('result'=>$result,'data'=>$data);
        echo json_encode($json);
    }

    public function inventory_import_contact_lens()
    {
        $this->load->model('reports/Inventory_contact_lens');
        $model = $this->Inventory_contact_lens;
        $data = array();
        $data['item_count'] = $model->getCategoryDropdownArray();
        $stock_locations = $this->xss_clean($this->Stock_location->get_allowed_locations());
        $stock_locations['all'] = $this->lang->line('reports_all');
        $data['stock_locations'] = array_reverse($stock_locations, TRUE);

        $this->load->view('reports/inventory_import_contact_lens_input', $data);
    }

    public function ajax_inventory_import_contact_lens()
    {
        $this->load->model('reports/Inventory_lens');
        $model = $this->Inventory_lens;
        $location_id = $this->input->post('location_id');
        $category = $this->input->post('category');
        $result = 1;

        $inputs = array('location_id'=>$location_id,'category'=>$category);

        $report_data = $model->getData($inputs);
        $data['header'] = array(
            'title'=>'Phiếu đặt hàng',
            'company_name'=>$category,
            'description'=>$category,
            'brand'=>'',
            'customer'=>'KÍNH MẮT VIỆT HÀN',
            'ordered_date'=>'Ngày đặt hàng: ' . date('d/m/Y'),
            'code'=>'MS đơn đặt hàng',
            'total'=>'Tổng số lượng (miếng)',
            'address'=>'91 Trương Định, HBT, HN'
        );
        $map = array(
            '-'=>0,
            '0.00'=>1,
            '0.25'=>2,
            '0.50'=>3,
            '0.75'=>4,
            '1.00'=>5,
            '1.25'=>6,
            '1.50'=>7,
            '1.75'=>8,
            '2.00'=>9,
            '2.25'=>10,
            '2.50'=>11,
            '2.75'=>12,
            '3.00'=>13,
            '3.25'=>14,
            '3.50'=>15,
            '3.75'=>16,
            '4.00'=>17,
            '4.25'=>18,
            '4.50'=>19,
            '4.75'=>20,
            '5.00'=>21,
            '5.25'=>22,
            '5.50'=>23,
            '5.75'=>24,
            '6.00'=>25,
            '6.50'=>26,
            '7.00'=>27,
            '7.50'=>28,
            '8.00'=>29,
            '8.50'=>30,
            '9.00'=>31,
            '9.50'=>32,
            '10.00'=>33,
        );

        $re_map = array(
            '-',
            '0.00',
            '0.25',
            '0.50',
            '0.75',
            '1.00',
            '1.25',
            '1.50',
            '1.75',
            '2.00',
            '2.25',
            '2.50',
            '2.75',
            '3.00',
            '3.25',
            '3.50',
            '3.75',
            '4.00',
            '4.25',
            '4.50',
            '4.75',
            '5.00',
            '5.25',
            '5.50',
            '5.75',
            '6.00',
            '6.50',
            '7.00',
            '7.50',
            '8.00',
            '8.50',
            '9.00',
            '9.50',
            '10.00',
        );

        $grid_data = array();
        $myopia = array(); //can
        $hyperopia = array(); //vien
        $return_arr = array();
        $mywater = array();
        if($category == 'Bách Quang') {
            $key_arr = array(
                'Biomedics 55' => 'Biomedics 55',
                'BioMedics 1day' => 'BioMedics 1day',
                'MAXIM 1 Day Honey' => 'MAXIM 1 Day Honey',
                'MAXIM 1 Day Black' => 'MAXIM 1 Day Black',
                'MAXIM 1 Day Gray' => 'MAXIM 1 Day Gray',
                'MAXIM 1 Day Brownie' => 'MAXIM 1 Day Brownie',
                'MAXIM 1 Day Blue' => 'MAXIM 1 Day Blue'
            );
            foreach ($report_data as $item) {
                $name = $item['name'];
                $arr_name = explode(' ', $name);


                if (count($arr_name) > 1) {

                    $st1 = strtoupper($arr_name[count($arr_name) - 1]);
                    $st = str_replace('D-', '', $st1);
                    unset($arr_name[count($arr_name) - 1]);
                    $fname = implode(' ', $arr_name);
                    $fname = trim($fname);
                    //echo $st;

                    $sph = $st;

                    if (isset($map[$sph]) && isset($key_arr[$fname])) {
                        $s = $map[$sph];
                        $myopia[$key_arr[$fname]][$s] = number_format($item['standard_amount'] - $item['quantity']);
                        if ($myopia[$key_arr[$fname]][$s] < 0) {
                            $myopia[$key_arr[$fname]][$s] = 0;
                        }
                    } else {
                        echo $sph . '|->' . $fname . '->' . $item['item_number'];
                    }
                    //$myopia[] = $map[$sph];


                }

            }

            for ($i = 0; $i < 34; $i++) {
                foreach ($key_arr as $key => $value) {
                    if (!isset($myopia[$value][$i])) {
                        $myopia[$value][$i] = '';
                    } else {

                    }
                }

            }
            $maxim1day = array();

            foreach ($myopia['MAXIM 1 Day Honey'] as $key => $value) {
                $maxim1day['Honey'][$key] = $value;
            }
            foreach ($myopia['MAXIM 1 Day Black'] as $key => $value) {
                $maxim1day['Black'][$key] = $value;
            }
            foreach ($myopia['MAXIM 1 Day Gray'] as $key => $value) {
                $maxim1day['Gray'][$key] = $value;
            }
            foreach ($myopia['MAXIM 1 Day Brownie'] as $key => $value) {
                $maxim1day['Brownie'][$key] = $value;
            }

            foreach ($myopia['MAXIM 1 Day Blue'] as $key => $value) {
                $maxim1day['Blue'][$key] = $value;
            }

            $biomedics55 = $myopia['Biomedics 55'];
            $biomedic1day = $myopia['BioMedics 1day'];

            $biomedics55['sum'] = array_sum($myopia['Biomedics 55']);
            $biomedic1day['sum'] = array_sum($myopia['BioMedics 1day']);

            $maxim1day['Honey']['sum'] = array_sum($maxim1day['Honey']);
            $maxim1day['Black']['sum'] = array_sum($maxim1day['Black']);
            $maxim1day['Gray']['sum'] = array_sum($maxim1day['Gray']);
            $maxim1day['Brownie']['sum'] = array_sum($maxim1day['Brownie']);
            $maxim1day['Blue']['sum'] = array_sum($maxim1day['Blue']);


            $sub_myopia = array();
            $sub_group = array();
            $total = 0;

            //var_dump($myopia);
            $total = array_sum($sub_group);
            $data['total'] = $total;
            $data['map'] = $map;
            $data['re_map'] = $re_map;
            $data['biomedics55'] = $biomedics55;
            $data['maxim1day'] = $maxim1day;
            $data['biomedic1day'] = $biomedic1day;
            $data['sub_myopia'] = $sub_myopia;
            $data['sub_group'] = $sub_group;
            $data['category'] = $category;
        }
        elseif($category == 'Seed')
        {
            $key_arr = array(
                'Biomedics 55' => 'Biomedics 55',
                'BioMedics 1day' => 'BioMedics 1day',
                'MAXIM 1 Day Honey' => 'MAXIM 1 Day Honey',
                'MAXIM 1 Day Black' => 'MAXIM 1 Day Black',
                'MAXIM 1 Day Gray' => 'MAXIM 1 Day Gray',
                'MAXIM 1 Day Brownie' => 'MAXIM 1 Day Brownie',
                'MAXIM 1 Day Blue' => 'MAXIM 1 Day Blue',
                'Optima FW' => 'Optima FW',
                'Soflens 59 Monthly' => 'Soflens 59 Monthly',
                'Freshkon AE 1Day Magnetic Grey' => 'Freshkon AE 1Day Magnetic Grey',
                'Freshkon AE 1Day Mystical Black' => 'Freshkon AE 1Day Mystical Black',
                'Freshkon AE 1Day Winsome Brown' => 'Freshkon AE 1Day Winsome Brown',
                'Freshkon AE 3Month Magnetic Grey' => 'Freshkon AE 3Month Magnetic Grey',
                'Freshkon AE 3Month Mystical Black' => 'Freshkon AE 3Month Mystical Black',
                'Freshkon AE 3Month Winsome Brown' => 'Freshkon AE 3Month Winsome Brown',
                'Nước' => 'Nước',
                'SEED 1 day Pure' => 'SEED 1 day Pure',
                'SEED 1 day Base'=>'SEED 1 day Base',
                'SEED 1 day Natural'=>'SEED 1 day Natural',
                'SEED 1 day Rich'=>'SEED 1 day Rich',
                'SEED 1 day Grace'=>'SEED 1 day Grace',
                'SEED 1monthly'=>'SEED 1monthly',
                'SEED 1monthly Cocoa' => 'SEED 1monthly Cocoa',
                'SEED 1monthly Gray'=>'SEED 1monthly Gray',
                'SEED 1monthly Gold Brown'=>'SEED 1monthly Gold Brown',
                'SEED 1monthly Pink'=>'SEED 1monthly Pink'

            );
            foreach ($report_data as $item) {
                $name = $item['name'];
                $arr_name = explode(' ', $name);


                if (count($arr_name) > 1) {

                    $st1 = strtoupper($arr_name[count($arr_name) - 1]);
                    //update by ManhVT to add item without Diop such as water to drop, clean lens
                    if (strpos($st1, 'D-') === 0) {
                        $st = str_replace('D-', '', $st1);
                        unset($arr_name[count($arr_name) - 1]);
                        $fname = implode(' ', $arr_name);
                        $fname = trim($fname);
                        //echo $st;

                        $sph = $st;

                        if (isset($map[$sph]) && isset($key_arr[$fname])) {
                            $s = $map[$sph];
                            $myopia[$key_arr[$fname]][$s] = number_format($item['standard_amount'] - $item['quantity']);
                            if ($myopia[$key_arr[$fname]][$s] < 0) {
                                $myopia[$key_arr[$fname]][$s] = 0;
                            }
                        } else {
                            echo $sph . '|->' . $fname . '->' . $item['item_number'];
                        }
                        //$myopia[] = $map[$sph];

                    } else {
                        $mywater[$name] = number_format($item['standard_amount'] - $item['quantity']);
                    }
                }

            }
            //var_dump($mywater);

            for ($i = 0; $i < 34; $i++) {
                foreach ($key_arr as $key => $value) {
                    if (!isset($myopia[$value][$i])) {
                        $myopia[$value][$i] = '';
                    } else {

                    }
                }

            }
            $fr1day = array();
            $fr1month = array();

            foreach ($myopia['SEED 1 day Pure'] as $key => $value) {
                $fr1day['pure'][$key] = $value;
            }
            foreach ($myopia['SEED 1 day Base'] as $key => $value) {
                $fr1day['base'][$key] = $value;
            }
            foreach ($myopia['SEED 1 day Natural'] as $key => $value) {
                $fr1day['natural'][$key] = $value;
            }
            foreach ($myopia['SEED 1 day Rich'] as $key => $value) {
                $fr1day['rich'][$key] = $value;
            }
            foreach ($myopia['SEED 1 day Grace'] as $key => $value) {
                $fr1day['grace'][$key] = $value;
            }

          
            $fr1day['pure']['sum'] = array_sum($fr1day['pure']);
            $fr1day['base']['sum'] = array_sum($fr1day['base']);
            $fr1day['natural']['sum'] = array_sum($fr1day['natural']);
            $fr1day['rich']['sum'] = array_sum($fr1day['rich']);
            $fr1day['grace']['sum'] = array_sum($fr1day['grace']);

            foreach ($myopia['SEED 1monthly'] as $key => $value) {
                $fr1month['SEED1monthly'][$key] = $value;
            }
            foreach ($myopia['SEED 1monthly Cocoa'] as $key => $value) {
                $fr1month['Cocoa'][$key] = $value;
            }
            foreach($myopia['SEED 1monthly Gray'] as $key => $value)
            {
                $fr1month['Gray'][$key] = $value;
            }
            foreach($myopia['SEED 1monthly Gold Brown'] as $key => $value)
            {
                $fr1month['Gold Brown'][$key] = $value;
            }

            foreach($myopia['SEED 1monthly Pink'] as $key => $value)
            {
                $fr1month['Pink'][$key] = $value;
            }
            
            
            $fr1month['SEED1monthly']['sum'] = array_sum($fr1month['SEED1monthly']);
            $fr1month['Cocoa']['sum'] = array_sum($fr1month['Cocoa']);
            $fr1month['Gray']['sum'] = array_sum($fr1month['Gray']);
            $fr1month['Gold Brown']['sum'] = array_sum($fr1month['Gold Brown']);
            $fr1month['Pink']['sum'] = array_sum($fr1month['Pink']);


            $sub_myopia = array();
            $sub_group = array();
            $total = 0;

            //var_dump($myopia);
            $total = array_sum($sub_group);
            $data['total'] = $total;
            $data['map'] = $map;
            $data['re_map'] = $re_map;

            $data['fr1day'] = $fr1day;

            $data['sub_myopia'] = $sub_myopia;
            $data['sub_group'] = $sub_group;
            $data['mywater'] = $mywater;
            $data['category'] = 'Seed';

            $sub_myopia = array();
            $sub_group = array();
            $total = 0;

            //var_dump($myopia);
            $total = array_sum($sub_group);
            $data['total'] = $total;
            $data['map'] = $map;
            $data['re_map'] = $re_map;

            $data['fr1day'] = $fr1day;
            $data['fr1month'] = $fr1month;

            $data['sub_myopia'] = $sub_myopia;
            $data['sub_group'] = $sub_group;
            $data['mywater'] = $mywater;
            $data['category'] = $category;
        }
        else{
            $key_arr = array(
                'Biomedics 55' => 'Biomedics 55',
                'BioMedics 1day' => 'BioMedics 1day',
                'MAXIM 1 Day Honey' => 'MAXIM 1 Day Honey',
                'MAXIM 1 Day Black' => 'MAXIM 1 Day Black',
                'MAXIM 1 Day Gray' => 'MAXIM 1 Day Gray',
                'MAXIM 1 Day Brownie' => 'MAXIM 1 Day Brownie',
                'MAXIM 1 Day Blue' => 'MAXIM 1 Day Blue',
                'Optima FW' => 'Optima FW',
                'Soflens 59 Monthly' => 'Soflens 59 Monthly',
                'Freshkon AE 1Day Magnetic Grey' => 'Freshkon AE 1Day Magnetic Grey',
                'Freshkon AE 1Day Mystical Black' => 'Freshkon AE 1Day Mystical Black',
                'Freshkon AE 1Day Winsome Brown' => 'Freshkon AE 1Day Winsome Brown',
                'Freshkon AE 3Month Magnetic Grey' => 'Freshkon AE 3Month Magnetic Grey',
                'Freshkon AE 3Month Mystical Black' => 'Freshkon AE 3Month Mystical Black',
                'Freshkon AE 3Month Winsome Brown' => 'Freshkon AE 3Month Winsome Brown',
                "Nước" => 'Nước'
            );
            foreach ($report_data as $item) {
                $name = $item['name'];
                $arr_name = explode(' ', $name);


                if (count($arr_name) > 1) {

                    $st1 = strtoupper($arr_name[count($arr_name) - 1]);
                    //update by ManhVT to add item without Diop such as water to drop, clean lens
                    if (strpos($st1, 'D-') === 0) {
                        $st = str_replace('D-', '', $st1);
                        unset($arr_name[count($arr_name) - 1]);
                        $fname = implode(' ', $arr_name);
                        $fname = trim($fname);
                        //echo $st;

                        $sph = $st;

                        if (isset($map[$sph]) && isset($key_arr[$fname])) {
                            $s = $map[$sph];
                            $myopia[$key_arr[$fname]][$s] = number_format($item['standard_amount'] - $item['quantity']);
                            if ($myopia[$key_arr[$fname]][$s] < 0) {
                                $myopia[$key_arr[$fname]][$s] = 0;
                            }
                        } else {
                            echo $sph . '|->' . $fname . '->' . $item['item_number'];
                        }
                        //$myopia[] = $map[$sph];

                    } else {
                        $mywater[$name] = number_format($item['standard_amount'] - $item['quantity']);
                    }
                }

            }
            //var_dump($mywater);

            for ($i = 0; $i < 34; $i++) {
                foreach ($key_arr as $key => $value) {
                    if (!isset($myopia[$value][$i])) {
                        $myopia[$value][$i] = '';
                    } else {

                    }
                }

            }
            $fr1day = array();
            $fr1month = array();

            foreach ($myopia['Freshkon AE 1Day Magnetic Grey'] as $key => $value) {
                $fr1day['Magnetic Grey'][$key] = $value;
            }
            foreach ($myopia['Freshkon AE 1Day Mystical Black'] as $key => $value) {
                $fr1day['Mystical Black'][$key] = $value;
            }
            foreach ($myopia['Freshkon AE 1Day Winsome Brown'] as $key => $value) {
                $fr1day['Winsome Brown'][$key] = $value;
            }
            //---------------- MONTHLY --------------------------
            foreach ($myopia['Freshkon AE 3Month Magnetic Grey'] as $key => $value) {
                $fr1month['Magnetic Grey'][$key] = $value;
            }

            foreach ($myopia['Freshkon AE 3Month Mystical Black'] as $key => $value) {
                $fr1month['Mystical Black'][$key] = $value;
            }
            foreach ($myopia['Freshkon AE 3Month Winsome Brown'] as $key => $value) {
                $fr1month['Winsome Brown'][$key] = $value;
            }


            $soflens59monthly = $myopia['Soflens 59 Monthly'];
            $optimafw = $myopia['Optima FW'];


            $soflens59monthly['sum'] = array_sum($myopia['Soflens 59 Monthly']);
            $optimafw['sum'] = array_sum($myopia['Optima FW']);

            $fr1day['Magnetic Grey']['sum'] = array_sum($fr1day['Magnetic Grey']);
            $fr1day['Mystical Black']['sum'] = array_sum($fr1day['Mystical Black']);
            $fr1day['Winsome Brown']['sum'] = array_sum($fr1day['Winsome Brown']);

            $fr1month['Magnetic Grey']['sum'] = array_sum($fr1month['Magnetic Grey']);
            $fr1month['Mystical Black']['sum'] = array_sum($fr1month['Mystical Black']);
            $fr1month['Winsome Brown']['sum'] = array_sum($fr1month['Winsome Brown']);


            $sub_myopia = array();
            $sub_group = array();
            $total = 0;

            //var_dump($myopia);
            $total = array_sum($sub_group);
            $data['total'] = $total;
            $data['map'] = $map;
            $data['re_map'] = $re_map;

            $data['soflens59monthly'] = $soflens59monthly;
            $data['optimafw'] = $optimafw;

            $data['fr1day'] = $fr1day;
            $data['fr1month'] = $fr1month;

            $data['sub_myopia'] = $sub_myopia;
            $data['sub_group'] = $sub_group;
            $data['mywater'] = $mywater;
            $data['category'] = $category;
        }
        $json = array('result'=>$result,'data'=>$data);
        echo json_encode($json);
    }

   /* public function inventory_frame($location_id = 'all', $item_count = 'all')
    {

    }*/
    public function inventory_frame()
    {
        $this->load->model('reports/Inventory_frame');
        $model = $this->Inventory_frame;
        $data = array();
        $data['item_count'] = $model->getItemCountDropdownArray();

        $stock_locations = $this->xss_clean($this->Stock_location->get_allowed_locations());
        $stock_locations['all'] = $this->lang->line('reports_all');
        $data['stock_locations'] = array_reverse($stock_locations, TRUE);

        $this->load->view('reports/inventory_frame_input', $data);
    }

    public function ajax_inventory_frame()
    {
        $this->load->model('reports/Inventory_frame');
        $model = $this->Inventory_frame;
        $location_id = $this->input->post('location_id');

        $_sFromDate = $this->input->post('fromDate');
        $_sToDate = $this->input->post('toDate');

        $_aFromDate = explode('/', $_sFromDate);
        $_aToDate = explode('/', $_sToDate);
        $_sFromDate = $_aFromDate[2] . '/' . $_aFromDate[1] . '/' . $_aFromDate[0];
        $_sToDate = $_aToDate[2] . '/' . $_aToDate[1] . '/' . $_aToDate[0];
        $location_id = $this->input->post('location_id');
        $result = 1;

        $inputs = array('location_id'=>$location_id, 'fromDate'=>$_sFromDate,'toDate'=>$_sToDate);
        $headers = $this->xss_clean($model->_getDataColumns());
        if($this->Employee->has_grant('items_unitprice_hide'))
        {
            //unset();
            unset($headers['details']['cost_price']); //cost_price
            //unset($headers['details']['sub_total']); //cost_price
        }
        //var_dump($headers);
        $report_data = $model->_getData($inputs);
        $data = null;
        if(!$report_data)
        {
            $result = 0;
        }else{
            $summary_data = array();
            $details_data = array();
            $i = 1;
            foreach($report_data['summary'] as $key => $row)
            {

                $begin_quantity = $row['end_quantity'] + $row['sale_quantity'] - $row['receive_quantity'];
                $summary_data[] = $this->xss_clean(array(
                    'id' => $i,
                    'cat' => $row['category'],
                    'begin_quantity' => number_format($begin_quantity),
                    'end_quantity' => number_format($row['end_quantity']),
                    'sale_quantity' => number_format($row['sale_quantity'])==0?'-':number_format($row['sale_quantity']),
                    'receive_quantity' => number_format($row['receive_quantity'])==0?'-':number_format($row['receive_quantity']),
                ));

                foreach($report_data['details'][$key] as $drow)
                {
                    //var_dump(to_currency($drow['unit_price']));die();
                    if($this->Employee->has_grant('items_unitprice_hide'))
                    {
                        $details_data[$i][] = $this->xss_clean(
                            [
                                'name'=>$drow['name'],
                                'item_number'=>$drow['item_number'],
                                'quantity'=>number_format($drow['quantity']),
                                'reorder_level'=>number_format($drow['reorder_level']), 
                                'location'=>$drow['location_name'],
                                //'cost_price'=>to_currency($drow['cost_price']),
                                'unit_price'=>to_currency($drow['unit_price']), 
                                'sub_total'=>to_currency($drow['sub_total_value'])
                            ]);
                    
                    } else {
                       // $details_data[$i][] = $this->xss_clean(array($drow['name'], $drow['item_number'], number_format($drow['quantity']), number_format($drow['reorder_level']), $drow['location_name'], to_currency($drow['cost_price']), to_currency($drow['unit_price']), to_currency($drow['sub_total_value'])));
                       $details_data[$i][] = $this->xss_clean(
                        [
                            'name'=>$drow['name'],
                            'item_number'=>$drow['item_number'], 
                            'quantity'=>number_format($drow['quantity']), 
                            'reorder_level'=>number_format($drow['reorder_level']), 
                            'location'=>$drow['location_name'],
                            'cost_price'=>to_currency($drow['cost_price']),
                            'unit_price'=>to_currency($drow['unit_price']), 
                            'sub_total'=>to_currency($drow['sub_total_value'])
                        ]);
                    }
                }
                $i++;
            }

            $data = array(
                'headers_summary' => transform_headers_raw($headers['summary'],TRUE),
                'headers_details' => transform_headers_readonly_raw($headers['details']),
                'summary_data' => $summary_data,
                'details_data' => $details_data,
                'report_data' =>$report_data
            );

        }


        $json = array('result'=>$result,'data'=>$data);
        echo json_encode($json);
    }

    //Added by ManhVT 26.12.2022
    public function inventory_lens()
    {
        $this->load->model('reports/Inventory_lens');
        $model = $this->Inventory_lens;
        $data = array();
        $data['item_count'] = $model->getCategoryDropdownArray();
        $stock_locations = $this->xss_clean($this->Stock_location->get_allowed_locations());
        $stock_locations['all'] = $this->lang->line('reports_all');
        $data['stock_locations'] = array_reverse($stock_locations, TRUE);

        $this->load->view('reports/inventory_lens_input', $data);
    }

    public function ajax_inventory_lens()
    {
        $this->load->model('reports/Inventory_lens');
        $model = $this->Inventory_lens;
        $location_id = $this->input->post('location_id');

        $_sFromDate = $this->input->post('fromDate');
        $_sToDate = $this->input->post('toDate');

        $_aFromDate = explode('/', $_sFromDate);
        $_aToDate = explode('/', $_sToDate);
        $_sFromDate = $_aFromDate[2] . '/' . $_aFromDate[1] . '/' . $_aFromDate[0];
        $_sToDate = $_aToDate[2] . '/' . $_aToDate[1] . '/' . $_aToDate[0];
        $location_id = $this->input->post('location_id');
        $result = 1;

        $inputs = array('location_id'=>$location_id, 'fromDate'=>$_sFromDate,'toDate'=>$_sToDate);
        $headers = $this->xss_clean($model->_getDataColumns());
        //var_dump($headers);
        $report_data = $model->_getData($inputs);
        $data = null;
        if(!$report_data)
        {
            $result = 0;
        }else{
            $summary_data = array();
            $details_data = array();
            $i = 1;
            foreach($report_data['summary'] as $key => $row)
            {
                $begin_quantity = $row['end_quantity'] + $row['sale_quantity'] - $row['receive_quantity'];
                $summary_data[] = $this->xss_clean(array(
                    'id' => $i,
                    'cat' => $row['category'],
                    'begin_quantity' => number_format($begin_quantity),
                    'end_quantity' => number_format($row['end_quantity']),
                    'sale_quantity' => number_format($row['sale_quantity'])==0?'-':number_format($row['sale_quantity']),
                    'receive_quantity' => number_format($row['receive_quantity'])==0?'-':number_format($row['receive_quantity']),
                ));
                $i++;
            }

            $data = array(
                'headers_summary' => transform_headers_raw($headers['summary'],TRUE),
                'summary_data' => $summary_data,
                'report_data' =>$report_data
            );

        }


        $json = array('result'=>$result,'data'=>$data);
        echo json_encode($json);
    }

    public function inventory_contact_lens()
    {
        $this->load->model('reports/Inventory_lens');
        $model = $this->Inventory_lens;
        $data = array();
        //$data['item_count'] = $model->getCategoryDropdownArray();
        $stock_locations = $this->xss_clean($this->Stock_location->get_allowed_locations());
        $stock_locations['all'] = $this->lang->line('reports_all');
        $data['stock_locations'] = array_reverse($stock_locations, TRUE);

        $this->load->view('reports/inventory_contact_lens_input', $data);
    }

    public function ajax_inventory_total_contact_lens()
    {
        $this->load->model('reports/Inventory_contact_lens');
        $model = $this->Inventory_contact_lens;
        $location_id = $this->input->post('location_id');

        $_sFromDate = $this->input->post('fromDate');
        $_sToDate = $this->input->post('toDate');

        $_aFromDate = explode('/', $_sFromDate);
        $_aToDate = explode('/', $_sToDate);
        $_sFromDate = $_aFromDate[2] . '/' . $_aFromDate[1] . '/' . $_aFromDate[0];
        $_sToDate = $_aToDate[2] . '/' . $_aToDate[1] . '/' . $_aToDate[0];
        $location_id = $this->input->post('location_id');
        $result = 1;

        $inputs = array('location_id'=>$location_id, 'fromDate'=>$_sFromDate,'toDate'=>$_sToDate);

        $headers = $this->xss_clean($model->_getDataColumns());
        //var_dump($headers);
        if($this->Employee->has_grant('items_unitprice_hide'))
        {
            unset($headers['details'][5]); //remove giá vốn
        }
        $report_data = $model->_getData($inputs);
        $data = null;
        if(!$report_data)
        {
            $result = 0;
        }else{
            $summary_data = array();
            $details_data = array();
            $i = 1;
            foreach($report_data['summary'] as $key => $row)
            {
                $begin_quantity = $row['end_quantity'] + $row['sale_quantity'] - $row['receive_quantity'];
                $summary_data[] = $this->xss_clean(array(
                    'id' => $i,
                    'cat' => $row['category'],
                    'begin_quantity' => number_format($begin_quantity),
                    'end_quantity' => number_format($row['end_quantity']),
                    'sale_quantity' => number_format($row['sale_quantity'])==0?'-':number_format($row['sale_quantity']),
                    'receive_quantity' => number_format($row['receive_quantity'])==0?'-':number_format($row['receive_quantity']),
                ));
                foreach($report_data['details'][$key] as $drow)
                {
                    if($this->Employee->has_grant('items_unitprice_hide'))
                    {
                        $details_data[$i][] = $this->xss_clean(array($drow['name'], $drow['item_number'], number_format($drow['quantity']), number_format($drow['reorder_level']), $drow['location_name'], to_currency($drow['unit_price']), to_currency($drow['sub_total_value'])));
                    } else {
                        $details_data[$i][] = $this->xss_clean(array($drow['name'], $drow['item_number'], number_format($drow['quantity']), number_format($drow['reorder_level']), $drow['location_name'], to_currency($drow['cost_price']), to_currency($drow['unit_price']), to_currency($drow['sub_total_value'])));
                    }
                }
                $i++;
            }

            $data = array(
                'headers_summary' => transform_headers_raw($headers['summary'],TRUE),
                'headers_details' => transform_headers_readonly_raw($headers['details']),
                'summary_data' => $summary_data,
                'details_data' => $details_data,
                'report_data' =>$report_data
            );

        }
        $json = array('result'=>$result,'data'=>$data);
        echo json_encode($json);
    }
    //added by ManhVT 04.01.2023
    public function inventory_sun_glasses()
    {
        $this->load->model('reports/Inventory_sun_glasses');
        $model = $this->Inventory_sun_glasses;
        $data = array();
        $data['item_count'] = $model->getItemCountDropdownArray();

        $stock_locations = $this->xss_clean($this->Stock_location->get_allowed_locations());
        $stock_locations['all'] = $this->lang->line('reports_all');
        $data['stock_locations'] = array_reverse($stock_locations, TRUE);

        $this->load->view('reports/inventory_sun_glasses_input', $data);
    }

    public function ajax_inventory_sun_glasses()
    {
        $this->load->model('reports/Inventory_sun_glasses');
        $model = $this->Inventory_sun_glasses;
        $location_id = $this->input->post('location_id');

        $_sFromDate = $this->input->post('fromDate');
        $_sToDate = $this->input->post('toDate');

        $_aFromDate = explode('/', $_sFromDate);
        $_aToDate = explode('/', $_sToDate);
        $_sFromDate = $_aFromDate[2] . '/' . $_aFromDate[1] . '/' . $_aFromDate[0];
        $_sToDate = $_aToDate[2] . '/' . $_aToDate[1] . '/' . $_aToDate[0];
        $location_id = $this->input->post('location_id');
        $result = 1;

        $inputs = array('location_id'=>$location_id, 'fromDate'=>$_sFromDate,'toDate'=>$_sToDate);
        $headers = $this->xss_clean($model->_getDataColumns());
        //var_dump($headers);
        if($this->Employee->has_grant('items_unitprice_hide'))
        {
            unset($headers['details'][5]); //remove giá vốn
        }
        $report_data = $model->_getData($inputs);
        $data = null;
        if(!$report_data)
        {
            $result = 0;
        }else{
            $summary_data = array();
            $details_data = array();
            $i = 1;
            foreach($report_data['summary'] as $key => $row)
            {

                $begin_quantity = $row['end_quantity'] + $row['sale_quantity'] - $row['receive_quantity'];
                $summary_data[] = $this->xss_clean(array(
                    'id' => $i,
                    'cat' => $row['category'],
                    'begin_quantity' => number_format($begin_quantity),
                    'end_quantity' => number_format($row['end_quantity']),
                    'sale_quantity' => number_format($row['sale_quantity'])==0?'-':number_format($row['sale_quantity']),
                    'receive_quantity' => number_format($row['receive_quantity'])==0?'-':number_format($row['receive_quantity']),
                ));

                foreach($report_data['details'][$key] as $drow)
                {
                    if($this->Employee->has_grant('items_unitprice_hide'))
                    {
                        $details_data[$i][] = $this->xss_clean(array($drow['name'], $drow['item_number'], number_format($drow['quantity']), number_format($drow['reorder_level']), $drow['location_name'], to_currency($drow['unit_price']), to_currency($drow['sub_total_value'])));
                    } else {
                        $details_data[$i][] = $this->xss_clean(array($drow['name'], $drow['item_number'], number_format($drow['quantity']), number_format($drow['reorder_level']), $drow['location_name'], to_currency($drow['cost_price']), to_currency($drow['unit_price']), to_currency($drow['sub_total_value'])));
                    }
                }
                $i++;
            }

            $data = array(
                'headers_summary' => transform_headers_raw($headers['summary'],TRUE),
                'headers_details' => transform_headers_readonly_raw($headers['details']),
                'summary_data' => $summary_data,
                'details_data' => $details_data,
                'report_data' =>$report_data
            );

        }


        $json = array('result'=>$result,'data'=>$data);
        echo json_encode($json);
    }

    
}
?>