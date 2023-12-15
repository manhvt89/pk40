<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once("Secure_Controller.php");

class Sales extends Secure_Controller
{
	public function __construct()
	{
		parent::__construct('sales');

		$this->load->library('sale_lib');
		$this->load->library('barcode_lib');
		$this->load->library('email_lib');
		$this->load->library('ciqrcode'); // Load QR Code library
		$this->config->load('qrcode'); // Load QR code config file;
		$this->logedUser_type = $this->session->userdata('type');
		$this->logedUser_id = $this->session->userdata('person_id');
	}

	public function index()
	{
		if($this->logedUser_type != 2)
		{
			$this->_reload();
		} else {
			redirect('/sales/manage');
		}
	}
	
	public function manage()
	{
		
		$data['table_headers'] = get_sales_manage_table_headers();

		// filters that will be loaded in the multiselect dropdown
		if($this->config->item('invoice_enable') == TRUE)
		{
			$data['filters'] = array('only_cash' => $this->lang->line('sales_cash_filter'),
									'only_invoices' => $this->lang->line('sales_invoice_filter'));
		}
		else
		{
			$data['filters'] = array('only_cash' => $this->lang->line('sales_cash_filter'));
		}

		if ($this->Employee->has_grant('sales_index')) {
			$data['is_created'] = 1;
		} else {
			$data['is_created'] = 0;
		}
		
		$this->load->view('sales/manage', $data);
		
	}
	
	public function get_row($row_id=0)
	{
		if($row_id == 0)
		{
			echo 'Invalid Data';
			exit();
		}
		$sale_info = $this->Sale->get_info($row_id)->row();
		if($sale_info == null)
		{
			echo 'Not Found a Record';
			exit();
		}
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

		$filters = array('sale_type' => 'all',
						'location_id' => 'all',
						'start_date' => $this->input->get('start_date'),
						'end_date' => $this->input->get('end_date'),
						'only_cash' => FALSE,
						'pending'=>FALSE, //added 03.02.2023 - manhvt
						'only_invoices' => $this->config->item('invoice_enable') && $this->input->get('only_invoices'),
						'is_valid_receipt' => $this->Sale->is_valid_receipt($search));

		// check if any filter is set in the multiselect dropdown
		if($this->input->get('filters') == null)
		{
			echo 'Invalid Data';
			exit();
		}
		//var_dump($this->input->get('filters'));
		$filledup = array_fill_keys($this->input->get('filters'), TRUE);
		//var_dump($filledup);
		//die();
		$filters = array_merge($filters, $filledup);

		$sales = $this->Sale->search($search, $filters, $limit, $offset, $sort, $order, $this->logedUser_type, $this->logedUser_id);
		$total_rows = $this->Sale->get_found_rows($search, $filters, $this->logedUser_type, $this->logedUser_id);
		//$payments = $this->Sale->get_payments_summary($search, $filters, $this->logedUser_type, $this->logedUser_id);
		//$payment_summary = $this->xss_clean(get_sales_manage_payments_summary($payments, $sales, $this));
		$payment_summary = '';
		$data_rows = array();
		foreach($sales->result() as $sale)
		{
			$data_rows[] = $this->xss_clean(get_sale_data_row($sale));
		}

		if($total_rows > 0)
		{
			//$data_rows[] = $this->xss_clean(get_sale_data_last_row($sales, $this));
		}

		echo json_encode(array('total' => $total_rows, 'rows' => $data_rows, 'payment_summary' => $payment_summary));
	}

	public function item_search()
	{
		$suggestions = array();
		$receipt = $search = $this->input->get('term') != '' ? $this->input->get('term') : NULL;

		if($this->sale_lib->get_mode() == 'return' && $this->Sale->is_valid_receipt($receipt))
		{
			// if a valid receipt or invoice was found the search term will be replaced with a receipt number (POS #)
			$suggestions[] = $receipt;
		}
		if($this->sale_lib->get_mode() == 'payment' && $this->Sale->is_valid_receipt($receipt))
		{
			// if a valid receipt or invoice was found the search term will be replaced with a receipt number (POS #)
			$suggestions[] = $receipt;
		}
		$suggestions = array_merge($suggestions, $this->Item->get_search_suggestions($search, array('search_custom' => FALSE, 'is_deleted' => FALSE), TRUE));
		$suggestions = array_merge($suggestions, $this->Item_kit->get_search_suggestions($search));
		
		$suggestions = $this->xss_clean($suggestions);

		echo json_encode($suggestions);
	}

	public function suggest_search()
	{
		$search = $this->input->post('term') != '' ? $this->input->post('term') : NULL;
		
		$suggestions = $this->xss_clean($this->Sale->get_search_suggestions($search));
		
		echo json_encode($suggestions);
	}

	public function select_customer()
	{
		$customer_id = $this->input->post('customer');
		$_oCustomerInfor = $this->Customer->get_info_by_account_number($customer_id);
		//echo $customer_id;
		//if($this->Customer->account_number_exists($customer_id))
		if(!empty($_oCustomerInfor))
		{
			//var_dump((array)$_oCustomerInfor); die();
			$this->sale_lib->set_customer($_oCustomerInfor->person_id);
			$this->sale_lib->set_points($_oCustomerInfor->points);
			//$this->sale_lib->set_customer_name($_oCustomerInfor->last_name . ' ' . $_oCustomerInfor->first_name);
			//$this->sale_lib->set_customer_cellphone($_oCustomerInfor->phone_number);
			$this->sale_lib->set_obj_customer($_oCustomerInfor);

			$discount_percent = $_oCustomerInfor->discount_percent;

			// apply customer default discount to items that have 0 discount
			if($discount_percent != '')
			{	
				$this->sale_lib->apply_customer_discount($discount_percent);
			}
			$cust_totals = $this->Customer->get_totals($customer_id);
			if (!empty($cust_totals)) {		

				$this->sale_lib->set_customer_total($cust_totals->total);
			} else{
				$this->sale_lib->set_customer_total(0);
			}
		}
		elseif($this->Customer->exists($customer_id))
		{
			$_oCustomerInfor = $this->Customer->get_info($customer_id);
			//var_dump((array)$_oCustomerInfor); die();
			$this->sale_lib->set_customer($customer_id);
			$this->sale_lib->set_points($_oCustomerInfor->points);
			//$this->sale_lib->set_customer_name($_oCustomerInfor->last_name . ' ' . $_oCustomerInfor->first_name);
			//$this->sale_lib->set_customer_cellphone($_oCustomerInfor->phone_number);
			$this->sale_lib->set_obj_customer($_oCustomerInfor);

			$discount_percent = $_oCustomerInfor->discount_percent;

			// apply customer default discount to items that have 0 discount
			if($discount_percent != '')
			{	
				$this->sale_lib->apply_customer_discount($discount_percent);
			}
			$cust_totals = $this->Customer->get_totals($customer_id);
			if (!empty($cust_totals)) {		

				$this->sale_lib->set_customer_total($cust_totals->total);
			} else{
				$this->sale_lib->set_customer_total(0);
			}
		}
		$this->_reload();
	}

	public function change_mode()
	{
		$stock_location = $this->input->post('stock_location');
		if (!$stock_location || $stock_location == $this->sale_lib->get_sale_location())
		{
			$mode = $this->input->post('mode');
			$this->sale_lib->set_mode($mode);
		} 
		elseif($this->Stock_location->is_allowed_location($stock_location, 'sales'))
		{
			$this->sale_lib->set_sale_location($stock_location);
		}

		$this->_reload();
	}
	
	public function set_comment() 
	{
		$this->sale_lib->set_comment($this->input->post('comment'));
	}
	
	public function set_invoice_number()
	{
		$this->sale_lib->set_invoice_number($this->input->post('sales_invoice_number'));
	}
	
	public function set_invoice_number_enabled()
	{
		$this->sale_lib->set_invoice_number_enabled($this->input->post('sales_invoice_number_enabled'));
	}
	
	public function set_print_after_sale()
	{
		$this->sale_lib->set_print_after_sale($this->input->post('sales_print_after_sale'));
	}
	
	public function set_email_receipt()
	{
 		$this->sale_lib->set_email_receipt($this->input->post('email_receipt'));
	}

	// Multiple Payments
	public function add_payment()
	{
		$data = array();
		$this->form_validation->set_rules('amount_tendered', 'lang:sales_amount_tendered', 'trim|required|callback_numeric');

		$payment_type = $this->input->post('payment_type');
		$ctv_id = $this->input->post('ctvs');
		$this->sale_lib->set_partner_id($ctv_id);
		if($this->form_validation->run() == FALSE)
		{
			if($payment_type == $this->lang->line('sales_giftcard'))
			{
				$data['error'] = $this->lang->line('sales_must_enter_numeric_giftcard');
			}
			else
			{
				$data['error'] = $this->lang->line('sales_must_enter_numeric');
			}
		}
		else
		{
			if($payment_type == $this->lang->line('sales_giftcard'))
			{
				// in case of giftcard payment the register input amount_tendered becomes the giftcard number
				$giftcard_num = $this->input->post('amount_tendered');

				$payments = $this->sale_lib->get_payments();
				$payment_type = $payment_type . ':' . $giftcard_num;
				$current_payments_with_giftcard = isset($payments[$payment_type]) ? $payments[$payment_type]['payment_amount'] : 0;
				$cur_giftcard_value = $this->Giftcard->get_giftcard_value($giftcard_num);
				
				if(($cur_giftcard_value - $current_payments_with_giftcard) <= 0)
				{
					$data['error'] = $this->lang->line('giftcards_remaining_balance', $giftcard_num, to_currency($cur_giftcard_value));
				}
				else
				{
					$new_giftcard_value = $this->Giftcard->get_giftcard_value($giftcard_num) - $this->sale_lib->get_amount_due();
					$new_giftcard_value = $new_giftcard_value >= 0 ? $new_giftcard_value : 0;
					$this->sale_lib->set_giftcard_remainder($new_giftcard_value);
					$new_giftcard_value = str_replace('$', '\$', to_currency($new_giftcard_value));
					$data['warning'] = $this->lang->line('giftcards_remaining_balance', $giftcard_num, $new_giftcard_value);
					$amount_tendered = min( $this->sale_lib->get_amount_due(), $this->Giftcard->get_giftcard_value($giftcard_num) );

					$this->sale_lib->add_payment($payment_type, $amount_tendered);
				}
			}
			else
			{
				$amount_tendered = $this->input->post('amount_tendered');
				$this->sale_lib->add_payment($payment_type, $amount_tendered,'',0);
				if($payment_type == $this->lang->line('sales_point')) // Nếu sử dụng points
				{
					$this->sale_lib->set_paid_points($amount_tendered);
				}
			}
		}

		$this->_reload($data);
	}

	// Multiple Payments
	public function delete_payment($payment_id)
	{
		$this->sale_lib->delete_payment($payment_id);

		$this->_reload();
	}

	public function add()
	{
		$data = array();
		
		$discount = 0;

		// check if any discount is assigned to the selected customer
		$customer_id = $this->sale_lib->get_customer();
		if($customer_id != -1)
		{
			// load the customer discount if any
			$discount_percent = $this->Customer->get_info($customer_id)->discount_percent;
			if($discount_percent != '')
			{
				$discount = $discount_percent;
			}
		}
		//$ctv_id = $this->input->post('add_hidden_ctv');
		//$this->sale_lib->set_partner_id($ctv_id);
		// if the customer discount is 0 or no customer is selected apply the default sales discount
		if($discount == 0)
		{
			$discount = $this->config->item('default_sales_discount');
		}

		$mode = $this->sale_lib->get_mode();
		//var_dump($this->sale_lib->get_ctv());die();
		if($this->sale_lib->get_ctv() == null) { // Chỉ khi chưa có trong session thì mới đọc trong csdl;
			$ctvs = $this->Ctv->get_list();
			$this->sale_lib->set_ctv($ctvs);
		}
		$quantity = ($mode == 'return') ? -1 : 1;
		if($mode == 'payment'){
			$this->sale_lib->set_edit(1);
		}
		$item_location = $this->sale_lib->get_sale_location();
		$item_id_or_number_or_item_kit_or_receipt = $this->input->post('item');

		if(($mode == 'return' || $mode == 'payment')&& $this->Sale->is_valid_receipt($item_id_or_number_or_item_kit_or_receipt))
		{
			$this->sale_lib->return_entire_sale($item_id_or_number_or_item_kit_or_receipt);
		}
		elseif($this->Item_kit->is_valid_item_kit($item_id_or_number_or_item_kit_or_receipt))
		{
			if(!$this->sale_lib->add_item_kit($item_id_or_number_or_item_kit_or_receipt, $item_location, $discount))
			{
				$data['error'] = $this->lang->line('sales_unable_to_add_item');
			}
		}
		else
		{

			if(!$this->sale_lib->add_item($item_id_or_number_or_item_kit_or_receipt, $quantity, $item_location, $discount))
			{
				$data['error'] = $this->lang->line('sales_unable_to_add_item');
			}
			else
			{
				$data['warning'] = $this->sale_lib->out_of_stock($item_id_or_number_or_item_kit_or_receipt, $item_location);
			}
		}

		$this->_reload($data);
	}

	public function edit_item($item_id=0)
	{
		if($item_id == 0)
		{
			echo 'Invalid Data';
			exit();
		}
		$data = array();
		
		$this->form_validation->set_rules('price', 'lang:items_price', 'required|callback_numeric');
		$this->form_validation->set_rules('quantity', 'lang:items_quantity', 'required|callback_numeric');
		$this->form_validation->set_rules('discount', 'lang:items_discount', 'required|callback_numeric');

		$description = $this->input->post('description');
		$serialnumber = $this->input->post('serialnumber');
		
		$price = parse_decimals($this->input->post('price'));
		$quantity = parse_decimals($this->input->post('quantity'));
		$discount = parse_decimals($this->input->post('discount'));
		//$discount = $this->input->post('discount');
		//var_dump($discount);
		//var_dump($price);
		//var_dump($quantity);
		//var_dump($discount);
		//die();
		$item_location = $this->input->post('location');
		//$ctv_id = $this->input->post('ctvs');
		//$this->sale_lib->set_partner_id($ctv_id);
		if($quantity == null)
		{
			echo 'Invalid Data';
			exit();
		}
		if($this->form_validation->run() != FALSE)
		{
			$this->sale_lib->edit_item($item_id, $description, $serialnumber, $quantity, $discount, $price);
		}
		else
		{
			$data['error'] = $this->lang->line('sales_error_editing_item');
		}

		$data['warning'] = $this->sale_lib->out_of_stock($this->sale_lib->get_item_id($item_id), $item_location);

		$this->_reload($data);
	}

	public function delete_item($item_number=0)
	{
		$this->sale_lib->delete_item($item_number);

		$this->_reload();
	}

	public function remove_customer()
	{
		$this->sale_lib->clear_giftcard_remainder();
		$this->sale_lib->clear_invoice_number();
		$this->sale_lib->remove_customer();
		$this->sale_lib->clear_test_id();
		$this->sale_lib->clear_partner_id();
		$this->sale_lib->clear_suspend_id();
		$this->sale_lib->empty_payments();
		$this->_reload();
	}

	/*
	** Chức năng: dùng để xuất hàng (tạo phiếu xuất, và tạo thanh toán nếu có)
	*/
	public function before_complete() // Xuất đơn hàng;
	{
		$data = array();
		$this->form_validation->set_rules('amount_tendered', 'lang:sales_amount_tendered', 'trim|required|callback_numeric_zero');

		if ($this->form_validation->run() == false) {
			$data['error'] = $this->lang->line('sales_must_enter_numeric');
			$this->_reload($data);
		} else {
			$amount_tendered = $this->input->post('amount_tendered');
			$payment_type = $this->input->post('payment_type');
			$payment_kind = $this->lang->line('sales_reserve_money');
			$this->sale_lib->add_payment($payment_type, $amount_tendered,$payment_kind); // Thêm vào trả trước với loại thanh toán $type
		
			$data['cart'] = $this->sale_lib->get_cart(); // Lấy danh sách các sản phẩm từ cart
			//var_dump($this->sale_lib->get_sale_id());
			$_iSaleID = $this->sale_lib->get_sale_id();
			$status = 1; //Trạng thái Đơn hàng: xuất hàng
			$data['status'] = $status; 
			$data['subtotal'] = $this->sale_lib->get_subtotal();
			$data['discounted_subtotal'] = $this->sale_lib->get_subtotal(true);
			$data['tax_exclusive_subtotal'] = $this->sale_lib->get_subtotal(true, true);
			$data['taxes'] = $this->sale_lib->get_taxes();
			$data['total'] = $this->sale_lib->get_total();
			$data['discount'] = $this->sale_lib->get_discount();
			$data['receipt_title'] = $this->lang->line('sales_receipt');
			$data['transaction_time'] = date($this->config->item('dateformat') . ' ' . $this->config->item('timeformat'));
			$data['transaction_date'] = date($this->config->item('dateformat'));
			$data['show_stock_locations'] = $this->Stock_location->show_locations('sales');
			$data['comments'] = $this->sale_lib->get_comment();
			$data['payments'] = $this->sale_lib->get_payments();
			$data['amount_change'] = $this->sale_lib->get_amount_due() * -1;
			$amount_change = $this->sale_lib->get_amount_due() * -1;
			$data['amount_due'] = $this->sale_lib->get_amount_due();
			$suspended_sale_id = $this->sale_lib->get_suspend_id();
			$employee_id = $this->Employee->get_logged_in_employee_info()->person_id;
			$employee_info = $this->Employee->get_info($employee_id);
			//$data['employee'] = $employee_info->first_name  . ' ' . $employee_info->last_name[0];
			$data['employee'] = get_fullname($employee_info->first_name, $employee_info->last_name);
			$data['company_info'] = implode("\n", array(
				$this->config->item('address'),
				$this->config->item('phone'),
				$this->config->item('account_number')
			));
			$payments = array();
			$payment['payment_type'] = $payment_type;
			$payment['payment_amount'] = 0;
			$payment['payment_kind'] = $this->lang->line('sales_reserve_money'); //"Đặt trước:"
			//var_dump($data['payments']);
			//die();

			if (count($data['payments']) == 0) {
				$data['error'] = 'Chưa thêm thanh toán, kiểm tra lại thông tin';
				$this->_reload($data);
			} else {
				if (isset($data['payments'][$this->lang->line('sales_reserve_money')])) {
					$old_payments = $data['payments'][$this->lang->line('sales_reserve_money')];
				} else {
					$old_payments = array();
				}
				//var_dump($old_payments);die();
				if(!empty($old_payments))
				{
					foreach ($old_payments as $item) {
						$payment['payment_amount'] = $payment['payment_amount'] + $item['payment_amount'];
					}
				}
				/* if (isset($data['payments'][$this->lang->line('sales_paid_money')])) {
					$new_payments = $data['payments'][$this->lang->line('sales_paid_money')];
				} else {
					$new_payments = null;
				} 
				foreach ($new_payments as $item) {
					$payment['payment_amount'] = $payment['payment_amount'] + $item['payment_amount'];
				}
				*/
				$payments[] = $payment;

				$customer_id = $this->sale_lib->get_customer();
				$test_id = $this->sale_lib->get_test_id();
				$kxv_id = 0;
				$doctor_id = 0;
				if ($test_id == 0) {
					
				} else {
					//echo $test_id;die();
					$test_info = $this->Testex->get_info($test_id);
					//var_dump($test_info); die();
					$kxv_id = $test_info['employeer_id'];
					$doctor_id = 0;
				}
				$customer_info = $this->_load_customer_data($customer_id, $data);
				$invoice_number = $this->_substitute_invoice_number($customer_info);

				if ($this->sale_lib->is_invoice_number_enabled() && $this->Sale->check_invoice_number_exists($invoice_number)) {
					$data['error'] = $this->lang->line('sales_invoice_number_duplicate');
					$this->_reload($data);
				} else {
					$data1 = array();
					$data1['status'] = $status; //1 Trạng thái xuất đơn hàng
					// Lưu thông tin vào bản ghi Sale
					$ctv_id = $this->input->post('ctvs');
					$invoice_number = $this->sale_lib->is_invoice_number_enabled() ? $invoice_number : null;
					$data['invoice_number'] = $invoice_number;
					//var_dump($payments);die();
					if ($_iSaleID == 0) { // Tạo mới
						$data['sale_id_num'] = $this->Sale->save(
							$data['cart'],
							$customer_id,
							$employee_id,
							$data['comments'],
							$invoice_number,
							$payments,
							$amount_change,
							$suspended_sale_id,
							$ctv_id,
							$status,
							$test_id,
							$kxv_id,
							$doctor_id
						);
					} else { // Chỉnh sửa tại đây
						$result = $this->Sale->edit(
							$_iSaleID,
							$data['cart'],
							$customer_id,
							$employee_id,
							$data['comments'],
							$invoice_number,
							$payments,
							$amount_change,
							$suspended_sale_id,
							$ctv_id,
							$status,
							$test_id,
							$kxv_id,
							$doctor_id
						);
						$data['sale_id_num'] = $_iSaleID;
					}
					//$data['sale_id'] = 'POS ' . $data['sale_id_num'];
					$data['sale_id'] = 'Bán ' . $data['sale_id_num'];
					$sale_info = $this->Sale->get_info($data['sale_id_num'])->row_array();
					$data1 = $this->_load_sale_data($data['sale_id_num']);
					$data['code'] = $sale_info['code'];
					$data1['cur_giftcard_value'] = $this->sale_lib->get_giftcard_remainder();
					$data1['print_after_sale'] = $this->sale_lib->is_print_after_sale();
					$data1['email_receipt'] = $this->sale_lib->get_email_receipt();
					$_is_invoice_number_enabled = $this->sale_lib->is_invoice_number_enabled();
					
					
					if ($data['sale_id_num'] == -1) {
						$data['error_message'] = $this->lang->line('sales_transaction_failed');
					} else {
						$this->sale_lib->clear_all();
						//$data['barcode'] = $this->barcode_lib->generate_receipt_barcode($data['sale_id']);
						$data['barcode'] = $this->barcode_lib->generate_receipt_barcode($data['code']); // Barcode của mã hóa đơn
					}

					
					$data1 = $this->xss_clean($data1);
					if ($_is_invoice_number_enabled) {
						$this->load->view('sales/invoice', $data1);
					} else {
						/*
						** Block QRcode
						** Thêm barcode vào đơn hàng
						** QRCode vào đơn hàng
						*/
						$_bIsBarcode = true;
						$_bIsQRcode = false; // Phieeuj tam ung ko co qrcode

						if ($_bIsQRcode == true) {
							$qr_url_data = base_url('/verify/confirm/').$sale_info['sale_uuid'];
							$hex_data   = bin2hex($qr_url_data);
							$save_name  = $hex_data.'.png';

							/* QR Code File Directory Initialize */
							$dir = 'assets/media/qrcode/';
							if (!file_exists($dir)) {
								mkdir($dir, 0775, true);
							}

							/* QR Configuration  */
							$config['cacheable']    = true;
							$config['imagedir']     = $dir;
							$config['quality']      = true;
							$config['size']         = '1024';
							$config['black']        = array(255,255,255);
							$config['white']        = array(255,255,255);
							$this->ciqrcode->initialize($config);

							/* QR Data  */
							$params['data']     = $qr_url_data;
							$params['level']    = 'L';
							$params['size']     = 10;
							$params['savename'] = FCPATH.$config['imagedir']. $save_name;

							//$this->ciqrcode->generate($params);

							$data1['qrcode_string'] = $this->ciqrcode->generate($params);
							$data1['url_string'] = $qr_url_data;
							$data1['footer_string'] = 'Quét mã QR để nhận quà';
							$data1['sale_uuid'] = $sale_info['sale_uuid'];
						} else {
							$data1['footer_string'] = '';
							$data1['qrcode_string'] = '';
							$data1['url_string'] = '';
							$data1['sale_uuid'] = $sale_info['sale_uuid'];
						}
						$this->load->view('sales/receipt', $data1);
					}

					
				}
			}
		}

	}

	public function complete()
	{

		$data = array();

		$data['cart'] = $this->sale_lib->get_cart();
		$data['subtotal'] = $this->sale_lib->get_subtotal();
		$data['discounted_subtotal'] = $this->sale_lib->get_subtotal(TRUE);
		$data['tax_exclusive_subtotal'] = $this->sale_lib->get_subtotal(TRUE, TRUE);
		$data['taxes'] = $this->sale_lib->get_taxes();
		$data['total'] = $this->sale_lib->get_total();
		$data['discount'] = $this->sale_lib->get_discount();
		$data['receipt_title'] = $this->lang->line('sales_receipt');
		$data['transaction_time'] = date($this->config->item('dateformat') . ' ' . $this->config->item('timeformat'));
		$data['transaction_date'] = date($this->config->item('dateformat'));
		$data['show_stock_locations'] = $this->Stock_location->show_locations('sales');
		$data['comments'] = $this->sale_lib->get_comment();
		$data['payments'] = $this->sale_lib->get_payments();
		$data['amount_change'] = $this->sale_lib->get_amount_due() * -1;
		$amount_change = $this->sale_lib->get_amount_due() * -1;
		$data['amount_due'] = $this->sale_lib->get_amount_due();
		$suspended_sale_id = $this->sale_lib->get_suspend_id();
		$employee_id = $this->Employee->get_logged_in_employee_info()->person_id;
		$employee_info = $this->Employee->get_info($employee_id);
		$data['employee'] = get_fullname($employee_info->first_name,$employee_info->last_name);
		$data['company_info'] = implode("\n", array(
			$this->config->item('address'),
			$this->config->item('phone'),
			$this->config->item('account_number')
		));
		$customer_id = $this->sale_lib->get_customer();
		$customer_info = $this->_load_customer_data($customer_id, $data);
		$invoice_number = $this->_substitute_invoice_number($customer_info);
		$sale_id = $this->sale_lib->get_sale_id();

		$data1['cur_giftcard_value'] = $this->sale_lib->get_giftcard_remainder();
		$data1['print_after_sale'] = $this->sale_lib->is_print_after_sale();
		$data1['email_receipt'] = $this->sale_lib->get_email_receipt();
		if(empty($data['payments']))
		{
			$data['payments'][$this->lang->line('sales_paid_money')] = array();
		}
		$data['status'] = 0;
		if($this->input->post('hidden_form')) {
			$ctv_id = $this->input->post('hidden_ctv');
			if ($this->sale_lib->is_invoice_number_enabled() && $this->Sale->check_invoice_number_exists($invoice_number)) {
				$data['error'] = $this->lang->line('sales_invoice_number_duplicate');

				$this->_reload($data);
			} else {
				$invoice_number = $this->sale_lib->is_invoice_number_enabled() ? $invoice_number : NULL;
				$data['invoice_number'] = $invoice_number;

				if ($sale_id > 0) {
					//update - payment, and sale status from 1 to 0
					$data['sale_id_num'] = $sale_id;
					$data['sale_id'] = 'Bán ' . $data['sale_id_num'];
					$sale_info = $this->Sale->get_info($data['sale_id_num'])->row_array();
					$sale_data = array(
						'customer_id' => $sale_info['customer_id'], //update lại,
						'employee_id' => $sale_info['employee_id'], //update lại,
						'status' => 0,
						'ctv_id' =>$ctv_id,
						//'sale_time'	=> date('Y-m-d H:i:s'), //don't update time
					);
					if(empty($data['payments'][$this->lang->line('sales_paid_money')])){ //added 07/07/2023
						$_tt["Giảm thêm"] = array( "payment_type"=> "Giảm thêm",
														"payment_amount"=> 0,
														"payment_kind"=>"Thanh toán",
														"payment_id"=> 0);
						$data['payments'][$this->lang->line('sales_paid_money')] = 	$_tt;
					}
					//var_dump($data['payments']); echo $this->lang->line('sales_paid_money'); die();
					if($data['payments'][$this->lang->line('sales_paid_money')]) {
						$success = $this->Sale->update($sale_id, $sale_data, $data['payments'][$this->lang->line('sales_paid_money')], $employee_id, $customer_id, $amount_change);
					}else{
						//$success = $this->Sale->update($sale_id, $sale_data, $data['payments'][$this->lang->line('sales_reserve_money')], $employee_id, $customer_id, $amount_change);
					}
					if (!$success) {
						$data['sale_id_num'] = -1;
					}
				} else {

					$data['sale_id_num'] = $this->Sale->save($data['cart'],
																$customer_id,
																$employee_id,
																$data['comments'],
																$invoice_number,
																$data['payments'][$this->lang->line('sales_paid_money')],
																$amount_change,
																$suspended_sale_id,
																$ctv_id,
																0,
																0,
																0,
																0,
																$this->sale_lib->get_paid_points());
					$data['sale_id'] = 'Bán ' . $data['sale_id_num'];
					$sale_info = $this->Sale->get_info($data['sale_id_num'])->row_array();
				}
				$data['code'] = $sale_info['code'];
				
				
				if ($data['sale_id_num'] == -1) {
					$data1['error_message'] = $this->lang->line('sales_transaction_failed');
				} else {
					$this->sale_lib->clear_all(); //Thành công thì CLEAR ALL DATA CART in SESSION. update 29/10/2023
					$data1['barcode'] = $this->barcode_lib->generate_receipt_barcode($data['code']);
					//$sale_info['amount_due']
					//ManhVT added 29/10/2023 - Thêm tính năng update history_ctv
					if($sale_info['sync'] == 0) {
						$_time = time();
						$ctv_info = $this->Employee->get_info($ctv_id);
						//var_dump($ctv_info);
						//die();
						$_ctv_name = get_fullname($ctv_info->first_name, $ctv_info->last_name);
						$_ctv_code = $ctv_info->code;
						$_ctv_phone = $ctv_info->phone_number;
						$_comission_rate = $ctv_info->comission_rate;
						$_employee_name = get_fullname($employee_info->first_name, $employee_info->last_name);
						$_customer_name = get_fullname($customer_info->first_name, $customer_info->last_name);
						$_comission_amount = ($_comission_rate * $sale_info['amount_due']) / 100;
						$history_data = [
							'ctv_id' => $ctv_id,
							'sale_id' => $sale_info['sale_id'],
							'employee_id' => $sale_info['employee_id'],
							'customer_id' => $sale_info['customer_id'],
							'ctv_name' => $_ctv_name,
							'ctv_code' => $_ctv_code,
							'ctv_phone' => $_ctv_phone,
							'sale_code' =>  $sale_info['code'],
							'employee_name' => $_employee_name,
							'customer_name' => $_customer_name,
							'created_time' => $_time,
							'payment_time' => $_time,
							'payment_amount' => $sale_info['amount_due'],
							'comission_amount' => $_comission_amount,
							'comission_rate' => $_comission_rate,
							'status' => 0
						];
						$this->History_ctv->save($history_data);
					}
				}
				$data1 = $this->_load_sale_data($data['sale_id_num']);

				if ($this->sale_lib->is_invoice_number_enabled()) {
					$this->load->view('sales/invoice', $data1);
				} else {

					/*
					** Thêm barcode vào đơn hàng
					** QRCode vào đơn hàng					
					*/
					$_bIsBarcode = TRUE;
					$_bIsQRcode = TRUE;

					if ($this->config->item('qrcode') == 0) {
						$_bIsQRcode = false;
					} else {
						
					}
					if ($this->config->item('barcode') == 0) {
						$_bIsBarcode = false;
					} else {
						
					}
					if($_bIsQRcode == TRUE)
					{
						$qr_url_data = base_url('/verify/confirm/').$sale_info['sale_uuid'];
						$hex_data   = bin2hex($qr_url_data);
						$save_name  = $hex_data.'.png';

						/* QR Code File Directory Initialize */
						$dir = 'assets/media/qrcode/';
						if (!file_exists($dir)) {
							mkdir($dir, 0775, true);
						}

						/* QR Configuration  */
						$config['cacheable']    = true;
						$config['imagedir']     = $dir;
						$config['quality']      = true;
						$config['size']         = '1024';
						$config['black']        = array(255,255,255);
						$config['white']        = array(255,255,255);
						$this->ciqrcode->initialize($config);
				
						/* QR Data  */
						$params['data']     = $qr_url_data;
						$params['level']    = 'L';
						$params['size']     = 10;
						$params['savename'] = FCPATH.$config['imagedir']. $save_name;
						
						//$this->ciqrcode->generate($params);
					
						$data1['qrcode_string'] = $this->ciqrcode->generate($params);
						$data1['url_string'] = $qr_url_data;
						$data1['footer_string'] = 'Quét mã QR để nhận quà';
						$data1['sale_uuid'] = $sale_info['sale_uuid'];
					} else {
						$data1['footer_string'] = '';
						$data1['qrcode_string'] = '';
						$data1['url_string'] = '';
						$data1['sale_uuid'] = $sale_info['sale_uuid'];
					}

					$data1 = $this->xss_clean($data1);

					$this->load->view('sales/receipt', $data1);
				}

				//$this->sale_lib->clear_all(); //CLEAR ALL DATA CART in SESSION
			}
		}else{
			$data['error'] = 'Bạn không được Refresh lại web hoặc nhấn F5';

			$this->_reload($data);
		}
	}

	public function send_invoice($sale_id)
	{
		$sale_data = $this->_load_sale_data($sale_id);

		$result = FALSE;
		$message = $this->lang->line('sales_invoice_no_email');

		if(!empty($sale_data['customer_email']))
		{
			$to = $sale_data['customer_email'];
			$subject = $this->lang->line('sales_invoice') . ' ' . $sale_data['invoice_number'];

			$text = $this->config->item('invoice_email_message');
			$text = str_replace('$INV', $sale_data['invoice_number'], $text);
			$text = str_replace('$CO', 'POS ' . $sale_data['sale_id'], $text);
			$text = $this->_substitute_customer($text, (object) $sale_data);

			// generate email attachment: invoice in pdf format
			$html = $this->load->view('sales/invoice_email', $sale_data, TRUE);
			// load pdf helper
			$this->load->helper(array('dompdf', 'file'));
			$filename = sys_get_temp_dir() . '/' . $this->lang->line('sales_invoice') . '-' . str_replace('/', '-' , $sale_data['invoice_number']) . '.pdf';
			if(file_put_contents($filename, pdf_create($html)) !== FALSE)
			{
				$result = $this->email_lib->sendEmail($to, $subject, $text, $filename);	
			}

			$message = $this->lang->line($result ? 'sales_invoice_sent' : 'sales_invoice_unsent') . ' ' . $to;
		}

		echo json_encode(array('success' => $result, 'message' => $message, 'id' => $sale_id));

		$this->sale_lib->clear_all();

		return $result;
	}

	public function send_receipt($sale_id)
	{
		$sale_data = $this->_load_sale_data($sale_id);

		$result = FALSE;
		$message = $this->lang->line('sales_receipt_no_email');

		if(!empty($sale_data['customer_email']))
		{
			$sale_data['barcode'] = $this->barcode_lib->generate_receipt_barcode($sale_data['sale_id']);

			$to = $sale_data['customer_email'];
			$subject = $this->lang->line('sales_receipt');

			$text = $this->load->view('sales/receipt_email', $sale_data, TRUE);
			
			$result = $this->email_lib->sendEmail($to, $subject, $text);

			$message = $this->lang->line($result ? 'sales_receipt_sent' : 'sales_receipt_unsent') . ' ' . $to;
		}

		echo json_encode(array('success' => $result, 'message' => $message, 'id' => $sale_id));

		$this->sale_lib->clear_all();

		return $result;
	}

	private function _substitute_variable($text, $variable, $object, $function)
	{
		// don't query if this variable isn't used
		if(strstr($text, $variable))
		{
			$value = call_user_func(array($object, $function));
			$text = str_replace($variable, $value, $text);
		}

		return $text;
	}

	private function _substitute_customer($text, $customer_info)
	{
		// substitute customer info
		$customer_id = $this->sale_lib->get_customer();
		if($customer_id != -1 && $customer_info != '')
		{
			$text = str_replace('$CU', $customer_info->first_name . ' ' . $customer_info->last_name, $text);
			$words = preg_split("/\s+/", trim($customer_info->first_name . ' ' . $customer_info->last_name));
			$acronym = '';
			foreach($words as $w)
			{
				$acronym .= $w[0];
			}
			$text = str_replace('$CI', $acronym, $text);
		}

		return $text;
	}

	private function _is_custom_invoice_number($customer_info)
	{
		$invoice_number = $this->config->config['sales_invoice_format'];
		$invoice_number = $this->_substitute_variables($invoice_number, $customer_info);

		return $this->sale_lib->get_invoice_number() != $invoice_number;
	}

	private function _substitute_variables($text, $customer_info)
	{
		$text = $this->_substitute_variable($text, '$YCO', $this->Sale, 'get_invoice_number_for_year');
		$text = $this->_substitute_variable($text, '$CO', $this->Sale , 'get_invoice_count');
		$text = $this->_substitute_variable($text, '$SCO', $this->Sale_suspended, 'get_invoice_count');
		$text = strftime($text);
		$text = $this->_substitute_customer($text, $customer_info);

		return $text;
	}

	private function _substitute_invoice_number($customer_info)
	{
		$invoice_number = $this->config->config['sales_invoice_format'];
		$invoice_number = $this->_substitute_variables($invoice_number, $customer_info);
		$this->sale_lib->set_invoice_number($invoice_number, TRUE);

		return $this->sale_lib->get_invoice_number();
	}

	private function _load_customer_data($customer_id, &$data, $totals = FALSE)
	{	
		$customer_info = array();
		
		if($customer_id != -1)
		{
			//$customer_info = $this->Customer->get_info($customer_id);
			$customer_info = $this->sale_lib->get_obj_customer();
			if(empty($customer_info))
			{
				$customer_info = $this->Customer->get_info($customer_id);
			}
			if (!empty($customer_info)) {
				$data['customer'] = $customer_info->last_name . ' ' . $customer_info->first_name;
				$data['account_number'] = $customer_info->account_number;
				$data['first_name'] = $customer_info->first_name;
				$data['last_name'] = $customer_info->last_name;
				$data['customer_email'] = $customer_info->email;
				$data['customer_address'] = $customer_info->address_1;
				$data['phone_number'] = $customer_info->phone_number;
				//$data['points'] = $customer_info->points; get from session
				$data['points'] = $this->sale_lib->get_points();
				if (!empty($customer_info->zip) or !empty($customer_info->city)) {
					$data['customer_location'] = $customer_info->zip . ' ' . $customer_info->city;
				} else {
					$data['customer_location'] = '';
				}
				$data['customer_account_number'] = $customer_info->account_number;
				$data['customer_discount_percent'] = $customer_info->discount_percent;
			} else {
				$data['customer'] = '';
				$data['account_number'] = '';
				$data['first_name'] = '';
				$data['last_name'] = '';
				$data['customer_email'] = '';
				$data['customer_address'] = '';
				$data['phone_number'] = '';
				//$data['points'] = $customer_info->points; get from session
				$data['points'] = $this->sale_lib->get_points();
				
				$data['customer_location'] = '';
				
				$data['customer_account_number'] = '';
				$data['customer_discount_percent'] = '';
			}
			if($totals)
			{
				$data['customer_total'] = $this->sale_lib->get_customer_total();
			}
			$data['customer_info'] = implode("\n", array(
				$data['customer'],
				$data['customer_address'],
				$data['customer_location'],
				$data['customer_account_number']
			));
		}

		return $customer_info;
	}

	private function _load_sale_data($sale_id)
	{
		$this->sale_lib->clear_all(); //empty carts
		$sale_info = $this->Sale->get_info($sale_id)->row_array();
		if ($sale_info != null) {
			$this->sale_lib->copy_entire_sale($sale_id);
			$data = array();
			$data['cart'] = $this->sale_lib->get_cart();
			$data['payments'] = $this->sale_lib->get_payments();
			$data['subtotal'] = $this->sale_lib->get_subtotal();
			$data['discounted_subtotal'] = $this->sale_lib->get_subtotal(true);
			$data['tax_exclusive_subtotal'] = $this->sale_lib->get_subtotal(true, true);
			$data['taxes'] = $this->sale_lib->get_taxes();
			$data['total'] = $this->sale_lib->get_total();
			$data['discount'] = $this->sale_lib->get_discount();
			$data['receipt_title'] = $this->lang->line('sales_receipt');
			$data['transaction_time'] = date($this->config->item('dateformat') . ' ' . $this->config->item('timeformat'), strtotime($sale_info['sale_time']));
			$data['transaction_date'] = date($this->config->item('dateformat'), strtotime($sale_info['sale_time']));
			$data['show_stock_locations'] = $this->Stock_location->show_locations('sales');
			$data['amount_change'] = $this->sale_lib->get_amount_due() * -1;
			$data['amount_due'] = $this->sale_lib->get_amount_due();
			$employee_info = $this->Employee->get_info($this->sale_lib->get_employee());
			$data['employee'] = $employee_info->last_name . ' ' . $employee_info->first_name;
			$this->_load_customer_data($this->sale_lib->get_customer(), $data);

			$data['sale_id_num'] = $sale_id;
			$data['code'] = $sale_info['code'];
			$data['sale_id'] = 'POS ' . $sale_id;
			$data['comments'] = $sale_info['comment'];
			$data['status'] = $sale_info['status'];
			$data['invoice_number'] = $sale_info['invoice_number'];
			$data['sale_uuid'] = $sale_info['sale_uuid'];
			$data['company_info'] = implode("\n", array(
				$this->config->item('address'),
				$this->config->item('phone'),
				$this->config->item('account_number')
			));
			$data['barcode'] = $this->barcode_lib->generate_receipt_barcode($data['code']);
			$data['print_after_sale'] = false;
		} else {
			//$this->sale_lib->copy_entire_sale($sale_id);
			$data = array();
			$data['cart'] = '';
			$data['payments'] = '';
			$data['subtotal'] = '';
			$data['discounted_subtotal'] = '';
			$data['tax_exclusive_subtotal'] = '';
			$data['taxes'] = '';
			$data['total'] = '';
			$data['discount'] = '';
			$data['receipt_title'] = '';
			$data['status'] = 1;
			$data['transaction_time'] = '';
			$data['transaction_date'] = '';
			$data['show_stock_locations'] = $this->Stock_location->show_locations('sales');
			$data['amount_change'] = '';
			$data['amount_due'] = '';
			$employee_info = $this->Employee->get_info($this->sale_lib->get_employee());
			$data['employee'] = $employee_info->last_name . ' ' . $employee_info->first_name;
			$this->_load_customer_data($this->sale_lib->get_customer(), $data);

			$data['sale_id_num'] = $sale_id;
			$data['code'] = '';
			$data['sale_id'] = 'POS ' . $sale_id;
			$data['comments'] = '';
			$data['invoice_number'] = '';
			$data['sale_uuid'] = '';
			$data['company_info'] = implode("\n", array(
				$this->config->item('address'),
				$this->config->item('phone'),
				$this->config->item('account_number')
			));
			$data['barcode'] = $this->barcode_lib->generate_receipt_barcode($data['code']);
			$data['print_after_sale'] = false;

		}
		$this->sale_lib->clear_all(); //empty carts
		return $this->xss_clean($data);
	}

	private function _reload($data = array())
	{		
		if($this->sale_lib->get_ctv() == null) { // Chỉ khi chưa có trong session thì mới đọc trong csdl;
			$ctvs = $this->Ctv->get_list();
			$this->sale_lib->set_ctv($ctvs);
		}
		$data['sale_id'] = $this->sale_lib->get_sale_id();
		$data['cart'] = $this->sale_lib->get_cart();
		$data['quantity'] = $this->sale_lib->get_quantity();
		$data['points'] = $this->sale_lib->get_points();
		$data['modes'] = array('sale' => $this->lang->line('sales_sale'), 'return' => $this->lang->line('sales_return'),'payment'=>'Thanh toán');
		$data['mode'] = $this->sale_lib->get_mode();
		$data['stock_locations'] = $this->Stock_location->get_allowed_locations('sales');
		$data['stock_location'] = $this->sale_lib->get_sale_location();
		$data['subtotal'] = $this->sale_lib->get_subtotal(TRUE);
		$data['tax_exclusive_subtotal'] = $this->sale_lib->get_subtotal(TRUE, TRUE);
		$data['taxes'] = $this->sale_lib->get_taxes();
		$data['discount'] = $this->sale_lib->get_discount();
		$data['total'] = $this->sale_lib->get_total();
		$data['comment'] = $this->sale_lib->get_comment();
		$data['email_receipt'] = $this->sale_lib->get_email_receipt();
		$data['payments_total'] = $this->sale_lib->get_payments_total();
		$data['amount_due'] = $this->sale_lib->get_amount_due();
		$data['payments'] = $this->sale_lib->get_payments();
		$data['payment_options'] = $this->Sale->get_payment_options();
		$data['partner_id'] = $this->sale_lib->get_partner_id();
		$data['test_id'] = $this->sale_lib->get_test_id();

		$data['items_module_allowed'] = $this->Employee->has_grant('sales_price_edit');
		$customer_info = $this->_load_customer_data($this->sale_lib->get_customer(), $data, TRUE);
		$data['invoice_number'] = $this->_substitute_invoice_number($customer_info);
		$data['invoice_number_enabled'] = $this->sale_lib->is_invoice_number_enabled();
		$data['print_after_sale'] = $this->sale_lib->is_print_after_sale();
		$data['payments_cover_total'] = $this->sale_lib->get_amount_due() <= 0;
		$data['ctvs'] = $this->sale_lib->get_ctv();
		$data['tests'] = array(); //edit by manhvt04.02.2023
		$data['detail_tests'] = array(); //edit by manhvt04.02.2023
		$data['edit'] = $this->sale_lib->get_edit();
		//var_dump($this->sale_lib->get_customer());
		if($this->sale_lib->get_customer() > 0)
		{
			$data['detail_tests'] = $this->Testex->get_tests_by_customer($this->sale_lib->get_customer(),1);
		}else {
			$search = '';
			$sort = 'test_time';
			$order = 'desc';
			$offset = 0;
			$limit = '500';
			$filters = array('type' => 'all',
				'location_id' => 'all',
				'start_date' => date('Y-m-d'),
				'end_date' => date('Y-m-d'));
			// Display danh sách bệnh nhân trong ngày;
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
		}
		//var_dump($data['tests']); //die();
		//var_dump($data['detail_tests']);
		
		$data = $this->xss_clean($data);

		$this->load->view("sales/register", $data);
	}

	public function receipt($uuid)
	{
		$sale_info = $this->Sale->get_info($uuid)->row();
		//var_dump($sale_info);
		$sale_id = 0;
		if(!empty($sale_info))
		{
			$sale_id = $sale_info->sale_id;
		}
		$data = $this->_load_sale_data($sale_id);
	
		$qr_url_data = base_url('/verify/confirm/').$data['sale_uuid'];
		$hex_data   = bin2hex($qr_url_data);
        $save_name  = $hex_data.'.png';

        /* QR Code File Directory Initialize */
        $dir = 'assets/media/qrcode/';
        if (!file_exists($dir)) {
            mkdir($dir, 0775, true);
        }

        /* QR Configuration  */
        $config['cacheable']    = true;
        $config['imagedir']     = $dir;
        $config['quality']      = true;
        $config['size']         = '1024';
        $config['black']        = array(255,255,255);
        $config['white']        = array(255,255,255);
        $this->ciqrcode->initialize($config);
  
        /* QR Data  */
        $params['data']     = $qr_url_data;
        $params['level']    = 'L';
        $params['size']     = 10;
        $params['savename'] = FCPATH.$config['imagedir']. $save_name;
        
        //$this->ciqrcode->generate($params);
       
		$data['qrcode_string'] = $this->ciqrcode->generate($params);
		$data['url_string'] = $qr_url_data;
		$data['footer_string'] = 'Quét mã QR để nhận quà';
		//$this->ciqrcode->generate();
		//die();
		//$data['qrcode'] = $dir. $save_name;
		$this->load->view('sales/receipt', $data);

		$this->sale_lib->clear_all();
	}

	public function invoice($sale_id)
	{
		$data = $this->_load_sale_data($sale_id);

		$this->load->view('sales/invoice', $data);

		$this->sale_lib->clear_all();
	}

	public function edit($uuid)
	{
		$_oSaleInfo = $this->Sale->get_info_by_uuid($uuid)->row();
		$_bCanCTVEdit = 0; // Không thể Edit; 1: Có thể edit
		$_bCanEmployeeEdit = 0; // Không thể Edit; 1: Có thể edit
		//var_dump($sale_info);
		$sale_id = 0;
		if(!empty($_oSaleInfo))
		{
			$sale_id = $_oSaleInfo->sale_id;
		}
		$ctvs = $this->Ctv->get_list();
		$data = array();

		$data['employees'] = array();
		$data['ctvs'] = $ctvs;
		//var_dump($ctvs);
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

		if($sale_info['status'] == 1) // Đơn bán hàng chưa hoàn thành, có thể chỉnh sửa
		{
			$_bCanCTVEdit = 1;
		} else {
			$_iTimestamp = $sale_info['completed_at']; // Nếu đơn hành đã hoàn thành kiểm tra thời gian hoàn thành
			if (date('Y-m-d', $_iTimestamp) == date('Y-m-d'))
			{
				$_bCanCTVEdit = 1;
			}
		}

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
		$data['bCanCTVEdit'] = $_bCanCTVEdit;
		$data['bCanEmployeeEdit'] = $_bCanEmployeeEdit;
		$this->load->view('sales/_form', $data);
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
		$newdate = $this->input->post('date');

		$date_formatter = date_create_from_format($this->config->item('dateformat') . ' ' . $this->config->item('timeformat'), $newdate);

		$sale_data = array(
			'sale_time' => $date_formatter->format('Y-m-d H:i:s'),
			'customer_id' => $this->input->post('customer_id') != '' ? $this->input->post('customer_id') : NULL,
			'employee_id' => $this->input->post('employee_id'),
			'comment' => $this->input->post('comment'),
			'invoice_number' => $this->input->post('invoice_number') != '' ? $this->input->post('invoice_number') : NULL
		);

		// go through all the payment type input from the form, make sure the form matches the name and iterator number
		$payments = array();
		$number_of_payments = $this->input->post('number_of_payments');
		for ($i = 0; $i < $number_of_payments; ++$i)
		{
			$payment_amount = $this->input->post('payment_amount_' . $i);
			$payment_type = $this->input->post('payment_type_' . $i);
			// remove any 0 payment if by mistake any was introduced at sale time
			if($payment_amount != 0)
			{
				// search for any payment of the same type that was already added, if that's the case add up the new payment amount
				$key = FALSE;
				if(!empty($payments))
				{
					// search in the multi array the key of the entry containing the current payment_type
					// NOTE: in PHP5.5 the array_map could be replaced by an array_column
					$key = array_search($payment_type, array_map(function($v){return $v['payment_type'];}, $payments));
				}

				// if no previous payment is found add a new one
				if($key === FALSE)
				{
					$payments[] = array('payment_type' => $payment_type, 'payment_amount' => $payment_amount);
				}
				else
				{
					// add up the new payment amount to an existing payment type
					$payments[$key]['payment_amount'] += $payment_amount;
				}
			}
		}

		if($this->Sale->update($sale_id, $sale_data, $payments))
		{
			echo json_encode(array('success' => TRUE, 'message' => $this->lang->line('sales_successfully_updated'), 'id' => $sale_id));
		}
		else
		{
			echo json_encode(array('success' => FALSE, 'message' => $this->lang->line('sales_unsuccessfully_updated'), 'id' => $sale_id));
		}
	}
	public function cancel()
	{
		$this->sale_lib->clear_all();

		$this->_reload();
	}

	public function suspend()
	{	
		$cart = $this->sale_lib->get_cart();
		$payments = $this->sale_lib->get_payments();
		$employee_id = $this->Employee->get_logged_in_employee_info()->person_id;
		$customer_id = $this->sale_lib->get_customer();
		$customer_info = $this->Customer->get_info($customer_id);
		$invoice_number = $this->_is_custom_invoice_number($customer_info) ? $this->sale_lib->get_invoice_number() : NULL;
		$comment = $this->sale_lib->get_comment();

		//SAVE sale to database
		$data = array();
		$sale_id = $this->sale_lib->get_suspend_id();

		//if(isset($sale_id))
		//{
			//update

		//}else{

			// thêm mới
			if($this->Sale_suspended->save($cart, $customer_id, $employee_id, $comment, $invoice_number, $payments) == '-1')
			{
				$data['error'] = $this->lang->line('sales_unsuccessfully_suspended_sale');
			}
			else
			{
				$data['success'] = $this->lang->line('sales_successfully_suspended_sale');
			}
		//}

		$this->sale_lib->clear_all();

		$this->_reload($data);
	}
	
	public function suspended()
	{	
		$data = array();
		$data['suspended_sales'] = $this->xss_clean($this->Sale_suspended->get_all()->result_array());

		$this->load->view('sales/suspended', $data);
	}
	
	public function unsuspend()
	{
		$suspended_sale_id = $this->input->post('suspended_sale_id');
		$this->sale_lib->clear_all();
		$this->sale_lib->copy_entire_suspended_sale($suspended_sale_id);
		$this->sale_lib->set_suspend_id($suspended_sale_id);
		$this->Sale_suspended->unsuspended($suspended_sale_id); //update status 1: it is using
		$this->_reload();
	}

	public function editsale($uuid='')
	{
		$sale_info = $this->Sale->get_info_by_uuid($uuid)->row();
		//var_dump($sale_info);
		$data = array();
		if (!empty($sale_info)) {
			$sale_id = $sale_info->sale_id;
			$this->sale_lib->clear_all();
			$this->sale_lib->copy_entire_sale($sale_id);
			//var_dump($this->sale_lib->get_payments());
			$this->sale_lib->set_edit(2);
			$data['edit'] = 2; // Thực hiện thanh toán; form này cho phep sửa.
		}
		$this->_reload($data);
	}
	
	public function check_invoice_number()
	{
		$sale_id = $this->input->post('sale_id');
		$invoice_number = $this->input->post('invoice_number');
		$exists = !empty($invoice_number) && $this->Sale->check_invoice_number_exists($invoice_number, $sale_id);

		echo !$exists ? 'true' : 'false';
	}

	private function _load_order_data($sale_id)
	{
		$this->sale_lib->clear_all();
		$sale_info = $this->Sale_suspended->get_info($sale_id)->row_array();
		$this->sale_lib->copy_entire_suspended_sale($sale_id);
		$data = array();
		$data['cart'] = $this->sale_lib->get_cart();
		$data['payments'] = $this->sale_lib->get_payments();
		$data['subtotal'] = $this->sale_lib->get_subtotal();
		$data['discounted_subtotal'] = $this->sale_lib->get_subtotal(TRUE);
		$data['tax_exclusive_subtotal'] = $this->sale_lib->get_subtotal(TRUE, TRUE);
		$data['taxes'] = $this->sale_lib->get_taxes();
		$data['total'] = $this->sale_lib->get_total();
		$data['discount'] = $this->sale_lib->get_discount();
		$data['receipt_title'] = $this->lang->line('sales_receipt');
		$data['transaction_time'] = date($this->config->item('dateformat') . ' ' . $this->config->item('timeformat'), strtotime($sale_info['sale_time']));
		$data['transaction_date'] = date($this->config->item('dateformat'), strtotime($sale_info['sale_time']));
		$data['show_stock_locations'] = $this->Stock_location->show_locations('sales');
		$data['amount_change'] = $this->sale_lib->get_amount_due() * -1;
		$data['amount_due'] = $this->sale_lib->get_amount_due();
		$employee_info = $this->Employee->get_logged_in_employee_info();
		$data['employee'] = $employee_info->first_name . ' ' . $employee_info->last_name;
		$this->_load_customer_data($this->sale_lib->get_customer(), $data);

		$data['sale_id_num'] = $sale_id;
		$data['sale_id'] = 'POS ' . $sale_id;
		$data['comments'] = $sale_info['comment'];
		$data['invoice_number'] = $sale_info['invoice_number'];
		$data['company_info'] = implode("\n", array(
			$this->config->item('address'),
			$this->config->item('phone'),
			$this->config->item('account_number')
		));
		$data['barcode'] = $this->barcode_lib->generate_receipt_barcode($data['sale_id']);
		$data['print_after_sale'] = FALSE;

		return $this->xss_clean($data);
	}


	public function view_order($sale_id)
	{
		$data = $this->_load_order_data($sale_id);

		$this->load->view('sales/orderdetail', $data);

		$this->sale_lib->clear_all();
	}

	public function test($customer_id=0,$principle_id=0,$partner_id=0){

		$this->sale_lib->clear_all($customer_id);
		if($this->Customer->exists($customer_id))
		{
			$this->sale_lib->set_customer($customer_id);
			$this->sale_lib->set_test_id($principle_id);
			$this->sale_lib->set_partner_id($partner_id);

			$discount_percent = $this->Customer->get_info($customer_id)->discount_percent;

			// apply customer default discount to items that have 0 discount
			if($discount_percent != '')
			{
				$this->sale_lib->apply_customer_discount($discount_percent);
			}
		}

		$this->_reload();
	}
	public function price_edit()
	{
		exit();
	}

	public function pending()
	{
		
		$data['table_headers'] = get_sales_manage_table_headers();

		// filters that will be loaded in the multiselect dropdown
		if($this->config->item('invoice_enable') == TRUE)
		{
			$data['filters'] = array('only_cash' => $this->lang->line('sales_cash_filter'),
									'only_invoices' => $this->lang->line('sales_invoice_filter'));
		}
		else
		{
			$data['filters'] = array('only_cash' => $this->lang->line('sales_cash_filter'),'tt'=>'tt');
		}

		if ($this->Employee->has_grant('sales_index')) {
			$data['is_created'] = 1;
		} else {
			$data['is_created'] = 0;
		}
		
		$this->load->view('sales/pending', $data);
		
	}
	/**
	 * Summary of payment
	 * Hiển thị form thanh toán để thực hiện thanh toán cho đơn hàng
	 * @param mixed $uuid
	 * @return void
	 */
	public function payment($uuid='')
	{
		$sale_info = $this->Sale->get_info_by_uuid($uuid)->row();
		//var_dump($sale_info);
		$data = array();
		if (!empty($sale_info)) {
			$sale_id = $sale_info->sale_id;
			$this->sale_lib->clear_all();
			$this->sale_lib->copy_entire_sale($sale_id);
			$this->sale_lib->set_edit(1);
			$data['edit'] = 1; // Thực hiện thanh toán; form này không cho sửa.
		}
		$this->_reload($data);
	}

	public function change_ctv()
	{
 		$this->sale_lib->set_partner_id($this->input->post('ctv_id'));
	}

	public function ajax_save_info($uuid = '-1')
	{
		$_oSaleInfo = $this->Sale->get_info_by_uuid($uuid)->row();
		$_iOldCtvID = $this->input->post('old_ctv_id');
		$_iCtvID = $this->input->post('ctv_id');
		$_bCanCTVEdit = $this->input->post('bCanCTVEdit');
		$_bCanEmployeeEdit = $this->input->post('bCanEmployeeEdit');
		$_iEmployeeID = $this->input->post('employee_id');
		$_sComment = $this->input->post('comment');
		$_option = [
			'bCanCTVEdit'=> $_bCanCTVEdit,
			'iOldCtvID'=>$_iOldCtvID,
			'iCtvID' =>$_iCtvID,
			'bCanEmployeeEdit' => $_bCanEmployeeEdit,
			'iEmployeeID' > $_iEmployeeID
		];
		if($_bCanCTVEdit == 1)
		{
			$employee_id = $_oSaleInfo->employee_id;
			$employee_info = $this->Employee->get_info($employee_id);
			$data['employee'] = get_fullname($employee_info->first_name,$employee_info->last_name);
			
			$customer_id = $_oSaleInfo->customer_id;
			$customer_info = $this->_load_customer_data($customer_id, $data);

			$ctv_info = $this->Employee->get_info($_iCtvID);

			$_ctv_name = get_fullname($ctv_info->first_name, $ctv_info->last_name);
			$_ctv_code = $ctv_info->code;
			$_ctv_phone = $ctv_info->phone_number;
			$_comission_rate = $ctv_info->comission_rate;
			$_employee_name = get_fullname($employee_info->first_name, $employee_info->last_name);
			$_customer_name = get_fullname($customer_info->first_name, $customer_info->last_name);
			
			$_comission_amount = ((float)$_comission_rate * (float)$_oSaleInfo->amount_due) / 100;
			
			$_comission_rate = $ctv_info->comission_rate;
			$_option['comission_rate'] = $_comission_rate;
			$_option['employee_id'] = $_oSaleInfo->employee_id;
			$_option['customer_id'] = $_oSaleInfo->customer_id;
			$_option['ctv_name'] = $_ctv_name;
			$_option['ctv_code'] = $_ctv_code;
			$_option['ctv_phone'] = $_ctv_phone;
			$_option['sale_code'] =  $_oSaleInfo->code;
			$_option['employee_name'] = $_employee_name;
			$_option['customer_name'] = $_customer_name;
			$_option['comission_amount'] = $_comission_amount;
			$_option['payment_amount'] = $_oSaleInfo->amount_due;
		} else {
			$_option['comission_rate'] = 0;
		}

			//var_dump($sale_info);
		$sale_id = 0;
		if(!empty($_oSaleInfo))
		{
			$sale_id = $_oSaleInfo->sale_id;
		}
		//$newdate = $this->input->post('date');

		//$date_formatter = date_create_from_format($this->config->item('dateformat') . ' ' . $this->config->item('timeformat'), $newdate);

		$sale_data = array(
			//'sale_time' => $date_formatter->format('Y-m-d H:i:s'),
			//'customer_id' => $this->input->post('customer_id') != '' ? $this->input->post('customer_id') : NULL,
			//'employee_id' => $this->input->post('employee_id'),
			//'ctv_id'=>$this->input->post('ctv_id'),
			'comment' => $_sComment,
			//'invoice_number' => $this->input->post('invoice_number') != '' ? $this->input->post('invoice_number') : NULL
		);

		// go through all the payment type input from the form, make sure the form matches the name and iterator number
		/*
		$payments = array();
		$number_of_payments = $this->input->post('number_of_payments');
		for ($i = 0; $i < $number_of_payments; ++$i)
		{
			$payment_amount = $this->input->post('payment_amount_' . $i);
			$payment_type = $this->input->post('payment_type_' . $i);
			// remove any 0 payment if by mistake any was introduced at sale time
			if($payment_amount != 0)
			{
				// search for any payment of the same type that was already added, if that's the case add up the new payment amount
				$key = FALSE;
				if(!empty($payments))
				{
					// search in the multi array the key of the entry containing the current payment_type
					// NOTE: in PHP5.5 the array_map could be replaced by an array_column
					$key = array_search($payment_type, array_map(function($v){return $v['payment_type'];}, $payments));
				}

				// if no previous payment is found add a new one
				if($key === FALSE)
				{
					$payments[] = array('payment_type' => $payment_type, 'payment_amount' => $payment_amount);
				}
				else
				{
					// add up the new payment amount to an existing payment type
					$payments[$key]['payment_amount'] += $payment_amount;
				}
			}
		}
		*/

		if($this->Sale->ajax_save_info($sale_id, $sale_data,$_option))
		{
			echo json_encode(array('success' => TRUE, 'message' => $this->lang->line('sales_successfully_updated'), 'id' => $sale_id));
		}
		else
		{
			echo json_encode(array('success' => FALSE, 'message' => $this->lang->line('sales_unsuccessfully_updated'), 'id' => $sale_id));
		}
	}
	
}
?>
