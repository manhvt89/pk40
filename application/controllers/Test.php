<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once("Secure_Controller.php");

class Test extends Secure_Controller
{
	public function __construct()
	{
		parent::__construct('test');
		$this->load->library('sale_lib');
		$this->load->library('test_lib');
		$this->load->library('barcode_lib');
		$this->load->library('email_lib');
		$this->load->library('barcode_lib');
		$this->logedUser_type = $this->session->userdata('type');
		$this->logedUser_id = $this->session->userdata('person_id');
		if($this->Employee->has_grant('test_step_one'))
		{
			//echo 'Tư vấn'; die();
		}
	}

	public function detail_test($sAccount_Number=0){
        $person_id = $this->session->userdata('person_id');
        $this->test_lib->remove_customer();
        $this->test_lib->empty_cart();
        $this->test_lib->clear_test_id();

		if($this->logedUser_type != 2)
		{
			if($sAccount_Number) {

					if ($this->Customer->account_number_exists($sAccount_Number)) {

						$customer_id = $this->Customer->get_info_by_account_number($sAccount_Number)->person_id;
						$this->test_lib->set_customer($customer_id);
					}
					$this->_reload();
				}else{
					redirect('/test/index');
			}
			
		} else {
			if($sAccount_Number) {

				if ($this->Customer->account_number_exists($sAccount_Number)) {

					$customer_id = $this->Customer->get_info_by_account_number($sAccount_Number)->person_id;
					//echo $customer_id; die();
					$this->test_lib->set_customer($customer_id);
				}
				$this->_reload();
			}else{
				redirect('/test/index');
			}
		}
    }
	public function index()
	{
		//test_create
        $person_id = $this->session->userdata('person_id');
       // echo str_replace('S','', strtoupper('s-0.00'));
		if($this->logedUser_type != 2)
		{
			$this->test_lib->remove_customer();
			$this->test_lib->empty_cart();
			$this->test_lib->clear_test_id();
			$this->_reload();
			
		} else {
			//Nếu là bác sĩ 
			$this->test_lib->remove_customer();
			$this->test_lib->empty_cart();
			$this->test_lib->clear_test_id();
			$this->_reload();
		}
	}

	public function manage()
	{
		//$person_id = $this->session->userdata('person_id');
		if($this->logedUser_type != 2)
		{
			$data['is_create'] = $this->Employee->has_grant('test_index');
			if($this->Employee->has_grant('sales_index')) {
				$data['table_headers'] = get_test_manage_table_headers(1);
			}else{
				$data['table_headers'] = get_test_manage_table_headers();
			}
				// filters that will be loaded in the multiselect dropdown
			$data['filters'] = null;
			$this->load->view('test/manage', $data);
			
		} else {
			/*
			** Chức năng này chỉ dành cho
			*/
			$data['is_create'] = 1;
			$data['table_headers'] = get_test_manage_table_headers(); //không cho phép có link (không hiển thị link)
			$data['filters'] = null;
			$this->load->view('test/manage', $data);
		}
	}

	public function get_row($row_id)
	{
		$sale_info = $this->Sale->get_info($row_id)->row();
		$data_row = $this->xss_clean(get_sale_data_row($sale_info));
		echo json_encode($data_row);
	}

	public function search()
	{
		$search = $this->input->get('search');
		$limit  = $this->input->get('limit');
		$offset = $this->input->get('offset');
		$sort   = $this->input->get('sort');
		$order  = $this->input->get('order');

		$filters = array('type' => 'all',
			'location_id' => 'all',
			'start_date' => $this->input->get('start_date'),
			'end_date' => $this->input->get('end_date'));

		// check if any filter is set in the multiselect dropdown
		$filledup = array_fill_keys($this->input->get('filters'), TRUE);
		$filters = array_merge($filters, $filledup);
		//$sales = $this->Testex->search($search, $filters, $limit, $offset, $sort, $order);
		$sales = $this->Testex->search($search, $filters, $limit, $offset, $sort, $order,$this->logedUser_type, $this->logedUser_id);
		$total_rows = $this->Testex->get_found_rows($search, $filters,$this->logedUser_type, $this->logedUser_id);
		//$payments = $this->Testex->get_payments_summary($search, $filters);
		//$payment_summary = $this->xss_clean(get_sales_manage_payments_summary($payments, $sales, $this));
        $user = $this->Employee->get_logged_in_employee_info();
        $permission = $this->Employee->has_grant('sales_create',$user->person_id);

		$data_rows = array();
        if($permission)
        {
            foreach ($sales->result() as $sale) {
                $data_rows[] = $this->xss_clean(get_test_data_row($sale, $this, 1));
            }
        }else {
            foreach ($sales->result() as $sale) {
                $data_rows[] = $this->xss_clean(get_test_data_row($sale, $this));
            }
        }

		echo json_encode(array('total' => $total_rows, 'rows' => $data_rows));
	}

	private function item_search()
	{
		$suggestions = array();
		$receipt = $search = $this->input->get('term') != '' ? $this->input->get('term') : NULL;

		if($this->sale_lib->get_mode() == 'return' && $this->Sale->is_valid_receipt($receipt))
		{
			// if a valid receipt or invoice was found the search term will be replaced with a receipt number (POS #)
			$suggestions[] = $receipt;
		}
		$suggestions = array_merge($suggestions, $this->Item->get_search_suggestions($search, array('search_custom' => FALSE, 'is_deleted' => FALSE), TRUE));
		$suggestions = array_merge($suggestions, $this->Item_kit->get_search_suggestions($search));

		$suggestions = $this->xss_clean($suggestions);

		echo json_encode($suggestions);
	}

	function suggest_search()
	{
		exit();
		$search = $this->input->post('term') != '' ? $this->input->post('term') : NULL;

		$suggestions = $this->xss_clean($this->Sale->get_search_suggestions($search));

		echo json_encode($suggestions);
	}

	public function select_customer()
	{
		$customer_id = $this->input->post('customer');

		$_oCustomerInfor = $this->Customer->get_info_by_account_number($customer_id);
		if(!empty($_oCustomerInfor))
		{
			$this->test_lib->set_customer($_oCustomerInfor->person_id);
		}
		elseif($this->Customer->exists($customer_id))
		{
			$this->test_lib->set_customer($customer_id);
		}
		$this->_reload();
	}

	public function remove_customer()
	{

		$this->test_lib->remove_customer();
        $this->test_lib->empty_cart();
        $this->test_lib->clear_test_id();

		$this->_reload();
	}

	public function complete()
	{
		$data = array();
        $obj = array();

        if ($this->input->post('hidden_test')) {

            $employee_id = $this->Employee->get_logged_in_employee_info()->person_id;
            $employee_info = $this->Employee->get_info($employee_id);
            $data['employee'] = $employee_info->last_name[0] . ' ' . $employee_info->first_name;
            $data['company_info'] = implode("\n", array(
                $this->config->item('address'),
                $this->config->item('phone'),
                $this->config->item('account_number')
            ));
            $customer_id = $this->test_lib->get_customer();
            $test_id = $this->test_lib->get_test_id();
            $data['test_id'] = $test_id;

            $reArray = array(); // right eye information
            $leArray = array(); // left eye information

            $leArray['ADD'] = $this->input->post('l_add') ? $this->input->post('l_add') : '';
            $leArray['AX'] = $this->input->post('l_ax') ? $this->input->post('l_ax') : '';
            $leArray['CYL'] = $this->input->post('l_cyl') ? $this->input->post('l_cyl') : '';
            $leArray['PD'] = $this->input->post('l_pd') ? $this->input->post('l_pd') : '';
            //$leArray['ADD'] = $this->input->post('l_add') ? $this->input->post('l_add');
            $leArray['SPH'] = $this->input->post('l_sph') ? $this->input->post('l_sph') : '';
            $leArray['VA'] = $this->input->post('l_va') ? $this->input->post('l_va') : '';

            $reArray['ADD'] = $this->input->post('r_add') ? $this->input->post('r_add') : '';
            $reArray['AX'] = $this->input->post('r_ax') ? $this->input->post('r_ax') : '';
            $reArray['CYL'] = $this->input->post('r_cyl') ? $this->input->post('r_cyl') : '';
            $reArray['PD'] = $this->input->post('r_pd') ? $this->input->post('r_pd') : '';
            //$reArray['ADD'] = $this->input->post('r_add');
            $reArray['SPH'] = $this->input->post('r_sph') ? $this->input->post('r_sph') : '';
            $reArray['VA'] = $this->input->post('r_va') ? $this->input->post('r_va') : '';


			$leOldArray['ADD'] = $this->input->post('l_add_old') ? $this->input->post('l_add_old') : '';
            $leOldArray['AX'] = $this->input->post('l_ax_old') ? $this->input->post('l_ax_old') : '';
            $leOldArray['CYL'] = $this->input->post('l_cyl_old') ? $this->input->post('l_cyl_old') : '';
            $leOldArray['PD'] = $this->input->post('l_pd_old') ? $this->input->post('l_pd_old') : '';
            //$leArray['ADD'] = $this->input->post('l_add') ? $this->input->post('l_add');
            $leOldArray['SPH'] = $this->input->post('l_sph_old') ? $this->input->post('l_sph_old') : '';
            $leOldArray['VA'] = $this->input->post('l_va_old') ? $this->input->post('l_va_old') : '';

            $reOldArray['ADD'] = $this->input->post('r_add_old') ? $this->input->post('r_add_old') : '';
            $reOldArray['AX'] = $this->input->post('r_ax_old') ? $this->input->post('r_ax_old') : '';
            $reOldArray['CYL'] = $this->input->post('r_cyl_old') ? $this->input->post('r_cyl_old') : '';
            $reOldArray['PD'] = $this->input->post('r_pd_old') ? $this->input->post('r_pd_old') : '';
            //$reArray['ADD'] = $this->input->post('r_add');
            $reOldArray['SPH'] = $this->input->post('r_sph_old') ? $this->input->post('r_sph_old') : '';
            $reOldArray['VA'] = $this->input->post('r_va_old') ? $this->input->post('r_va_old') : '';

			
			
			$presArray = array();
			if(!empty($this->input->post('pres_name')))
			{
				$pres_count = count($this->input->post('pres_name'));
				for($i =0; $i < $pres_count; $i++)
				{
					$pres_item['stt'] = $i;
					$pres_item['name'] = $this->input->post('pres_name')[$i];
					$pres_item['dvt'] = $this->input->post('pres_dvt')[$i];
					$pres_item['sl'] = $this->input->post('pres_amount')[$i];
					$pres_item['hdsd'] = $this->input->post('pres_hdsd')[$i];
					$presArray[$i] = $pres_item;
				}
			}

			$obj['old_toltal'] = $this->input->post('old_distance') . ';' . $this->input->post('old_reading');
			$obj['r_va_o'] = $this->input->post('r_va_o') ? $this->input->post('r_va_o') : '';
			$obj['l_va_o'] = $this->input->post('l_va_o') ? $this->input->post('l_va_o') : '';
			$obj['r_va_lo'] = $this->input->post('r_va_o') ? $this->input->post('r_va_lo') : '';
			$obj['l_va_lo'] = $this->input->post('l_va_o') ? $this->input->post('l_va_lo') : '';

			$obj['right_e_old'] = json_encode($reOldArray);
            $obj['left_e_old'] = json_encode($leOldArray);
			$obj['prescription'] = json_encode($presArray);

            $obj['note'] = $this->input->post('note') ? $this->input->post('note') : '';
			$obj['duration_dvt'] = $this->input->post('duration_dvt') ? $this->input->post('duration_dvt') : '';
            $obj['right_e'] = json_encode($reArray);
            $obj['left_e'] = json_encode($leArray);
            $obj['toltal'] = $this->input->post('distance') . ';' . $this->input->post('reading');
            $obj['lens_type'] = $this->input->post('single') . ';' .
                $this->input->post('bifocal') . ';' .
                $this->input->post('progressive') . ';' .
                $this->input->post('rx');
            $obj['type'] = $this->input->post('type') ? $this->input->post('type') : 0;
            $obj['reminder'] = $this->input->post('reminder') ? $this->input->post('reminder') : 0;
            $obj['duration'] = $this->input->post('duration') ? $this->input->post('duration') : 6;
            $obj['employeer_id'] = $employee_id;
            $obj['customer_id'] = $customer_id;
            $obj['contact_lens_type'] = '';
			if($this->input->post('reason') == null)
			{
				$obj['reason'] = '';
			} else {
				$obj['reason'] = $this->input->post('reason');
			}
			if($this->Employee->has_grant('test_step_one'))
			{
				$obj['step'] = 3;
			}
            $new = 0;
            if ($this->input->post('hidden_test_id') == 0) {
                $new = 1;
            }

            if ($new == 0) { //update

                $data['test_id_num'] = $this->Testex->update($test_id, $obj);
            } else {
                $obj['code'] = 'TD' . time(); // just only create new
                $data['test_id_num'] = $this->Testex->save($obj);
                $this->test_lib->set_cart($data['test_id_num']);
                $this->test_lib->set_test_id($data['test_id_num']);
            }
            $customer_info = $this->_load_customer_data($customer_id, $data);

            $data = $this->xss_clean($data);

            if ($data['test_id_num'] == -1) {
                $data['error_message'] = $this->lang->line('sales_transaction_failed');
            }
            $this->_reload();
        }
        else
        {
            $customer_id = $this->test_lib->get_customer();
            $customer_info = $this->_load_customer_data($customer_id, $data);
            $this->_reload();
        }

	}
	private function _load_customer_data($customer_id, &$data, $totals = FALSE)
	{
		$customer_info = '';

		if($customer_id != -1)
		{
			$customer_info = $this->Customer->get_info($customer_id);
            //var_dump($customer_info);die();

			$data['customer'] = $customer_info->last_name . ' ' . $customer_info->first_name;

			$data['first_name'] = $customer_info->first_name;
            $data['age'] = $customer_info->age;
			$data['last_name'] = $customer_info->last_name;
			$data['customer_email'] = $customer_info->email;
			$data['customer_address'] = $customer_info->address_1;
			$data['customer_phone'] = $customer_info->phone_number;
			$data['customer_old_data'] = $this->test_lib->old_data_test_by_customer($customer_id);

            $data['customer_account_number'] = $customer_info->account_number;
			$data['customer_discount_percent'] = $customer_info->discount_percent;

			$data['customer_info'] = implode("\n", array(
				$data['customer'],
				$data['customer_address'],
				$data['customer_account_number']
			));
            if($this->test_lib->get_test_id() > 0) {
                $data['test'] = $this->Testex->get_info($this->test_lib->get_test_id());
            }else{
                $data['test'] = null;
            }
		}

		return $customer_info;
	}

	private function _reload($data = array())
	{

        $data['test_id'] = $this->test_lib->get_test_id();
	    $data['cart'] = $this->test_lib->get_cart();
		//$data['items_module_allowed'] = $this->Employee->has_grant('items', $this->Employee->get_logged_in_employee_info()->person_id);

		$customer_info = $this->_load_customer_data($this->test_lib->get_customer(), $data, TRUE);
		$data['duration_dvts'] = $this->config->item('duration_dvts');
        $pres_names_q = $this->Prescription->get_all();
		
		$pres_names[""] = array();
		$pres_a = array();
		foreach($pres_names_q as $pres)
		{
			$pres_names[$pres['name']] = $pres['name'];
			$pres_a[$pres['name']] = $pres;
		}
		$data['tests'] = array(); //edit by manhvt27.05.2023
		$data['pres_names'] = $pres_names;
		$data['pres_a'] = $pres_a;
		$data['json_pres_a'] = json_encode($pres_names_q);

		$data['json_prescription_list'] = json_encode(array());
		$data['duration_dvt'] = 'Tháng';
		if(isset($data['test_id'])) // view detail test
        {
            $test = $this->Testex->get_info($data['test_id']);
            if(isset($test)) {
                //var_dump($test);
                $data['toltal'] = explode(';', $test['toltal']);
                $data['duration'] = $test['duration'];
				$data['duration_dvt'] = $test['duration_dvt'];
                $data['code'] = $test['code'];
                $data['lens_type'] = explode(';', $test['lens_type']);
                $data['type'] = $test['type'];
                $data['note'] = $test['note'];
                $data['test_time'] = date('d/m/Y H:m:s', $test['test_time']);
                $data['contact_lens_type'] = explode(';',$test['contact_lens_type']);

                $data['right_e'] = json_decode($test['right_e'],true);
                $data['left_e'] = json_decode($test['left_e'],true);
                $data['test_time'] = $test['test_time'];
                $data['reminder'] = $test['reminder'];

				//Added by ManhVT support BS
				$data['prescription_list'] = $test['prescription'] != "" ? json_decode($test['prescription'],true) : array();
				
				$data['json_prescription_list'] = $test['prescription'] != "" ? $test['prescription'] : json_encode(array());
				$data['r_va_o'] = $test['r_va_o'] != "" ? $test['r_va_o'] : $data['right_e']['VA'];
				$data['l_va_o'] = $test['l_va_o'] != "" ? $test['l_va_o'] : $data['left_e']['VA'];
				$data['r_va_lo'] = $test['r_va_lo'] != "" ? $test['r_va_lo'] : $data['right_e']['VA'];
				$data['l_va_lo'] = $test['l_va_lo'] != "" ? $test['l_va_lo'] : $data['left_e']['VA'];
				$data['right_e_old'] = $test['right_e_old'] != "" ? json_decode($test['right_e_old'],true) : $data['right_e'];
            	$data['left_e_old'] = $test['left_e_old'] != "" ? json_decode($test['left_e_old'],true) : $data['left_e'];
				$data['old_toltal'] = $test['old_toltal'] != "" ? explode(';', $test['old_toltal']) : $data['toltal'];
				$data['reason'] = $test['reason'];
            }

			

        }else{
			if($this->Employee->has_grant('test_step_one'))
			{

			} else { // Nếu không phải là tư vấn
				$search = '';
				$sort = 'test_time';
				$order = 'desc';
				$offset = 0;
				$limit = '500';
				$filters = array('type' => 'all',
					'location_id' => 'all',
					'start_date' => date('Y-m-d'),
					'end_date' => date('Y-m-d'));
				$tests = $this->Testex->search($search, $filters, $limit, $offset, $sort, $order)->result_array();

				$_tests = array();
				$seenIds = array();
				foreach ($tests as $_test) {
					$_iCustomerId = $_test['customer_id'];
					
					// Kiểm tra xem customer_id đã xuất hiện trước đó hay chưa
					if (!in_array($_iCustomerId, $seenIds)) {
						// Nếu chưa xuất hiện, thêm khách hàng vào mảng kết quả và đánh dấu là đã xuất hiện
						$_tests[] = $_test;
						$seenIds[] = $_iCustomerId;
					}
				}
				$data['tests'] = $_tests;
				$data['duration_dvt'] = 'Tháng';
			}
        }
        $data = $this->xss_clean($data);
		$this->load->view("test/register", $data);
	}

	public function view_test()
    {
        $test_id = $this->input->post('test_id');
        $this->test_lib->set_test_id($test_id);
        $this->test_lib->set_cart($test_id);
		/* remove by ManhVT 12.01.2023 
        $employee_id = $this->Employee->get_logged_in_employee_info()->person_id;
        $employee_info = $this->Employee->get_info($employee_id);
		$data['employee'] = $employee_info->last_name . ' ' . $employee_info->first_name;
        $data['company_info'] = implode("\n", array(
            $this->config->item('address'),
            $this->config->item('phone'),
            $this->config->item('account_number')
        ));
		*/
		$test = $this->Testex->get_info($test_id);
		$customer_id = 0;
		//var_dump($test['customer_id']);
		if(!empty($test))
		{
        	$customer_id = $test['customer_id'];
		} 
		$this->test_lib->set_customer($customer_id);
        $data['test_id'] = $this->test_lib->get_test_id();

       // $customer_info = $this->_load_customer_data($customer_id, $data);

        $data = $this->xss_clean($data);

        $this->_reload($data);
    }
	public function edit($sale_id)
	{
		$data = array();

		$data['employees'] = array();
		foreach($this->Employee->get_all()->result() as $employee)
		{
			foreach(get_object_vars($employee) as $property => $value)
			{
				$employee->$property = $this->xss_clean($value);
			}

			$data['employees'][$employee->person_id] = $employee->first_name . ' ' . $employee->last_name;
		}

		$sale_info = $this->xss_clean($this->Sale->get_info($sale_id)->row_array());
		$data['selected_customer_name'] = $sale_info['customer_name'];
		$data['selected_customer_id'] = $sale_info['customer_id'];
		$data['sale_info'] = $sale_info;

		$data['payments'] = array();
		foreach($this->Sale->get_sale_payments($sale_id)->result() as $payment)
		{
			foreach(get_object_vars($payment) as $property => $value)
			{
				$payment->$property = $this->xss_clean($value);
			}

			$data['payments'][] = $payment;
		}

		// don't allow gift card to be a payment option in a sale transaction edit because it's a complex change
		$data['payment_options'] = $this->xss_clean($this->Sale->get_payment_options(FALSE));

		$this->load->view('sales/form', $data);
	}

	public function delete($sale_id = -1, $update_inventory = TRUE)
	{
		$employee_id = $this->Employee->get_logged_in_employee_info()->person_id;
		$sale_ids = $sale_id == -1 ? $this->input->post('ids') : array($sale_id);

		if($this->Sale->delete_list($sale_ids, $employee_id, $update_inventory))
		{
			echo json_encode(array('success' => TRUE, 'message' => $this->lang->line('sales_successfully_deleted') . ' ' .
				count($sale_ids) . ' ' . $this->lang->line('sales_one_or_multiple'), 'ids' => $sale_ids));
		}
		else
		{
			echo json_encode(array('success' => FALSE, 'message' => $this->lang->line('sales_unsuccessfully_deleted')));
		}
	}

	public function save($sale_id = -1)
	{
		exit();
	}

	/*
	** Hàm này dùng để kiểm tra xem nếu được câp quyền này, tài khoản có chức năng tạo phiếu khám; Bước đầu tiên của quy trình khám bệnh;
	*/
	public function step_one()
	{
		return 0;
	}
}
?>