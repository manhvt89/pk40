<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once("Secure_Controller.php");

class Customer_info extends Secure_Controller
{
	public function __construct()
	{
		parent::__construct('customer_info');

		$this->load->library('barcode_lib');

	}

	public function index($textinput = null)
	{
		//echo $textinput;die();
		$data = null;
		$data['message'] = '';
		$data['report_data'] = null;
		if($textinput == null) {
			if ($this->input->post('hddinput')) {
				if ($this->input->post('textinput')) {
					$inputs['term'] = $this->input->post('textinput');

					$this->Customer_infoex->create($inputs);
					$headers = $this->xss_clean($this->Customer_infoex->getDataColumns());
					$report_data = $this->Customer_infoex->getData($inputs);
					$summary_data = array();
					$details_data = array();

					foreach ($report_data['summary'] as $key => $row) {
						$summary_data[] = $this->xss_clean(array(
							'id' => anchor('sales/receipt/' . $row['sale_id'], 'POS ' . $row['sale_id'], array('target' => '_blank')),
							'sale_date' => date('d/m/Y', strtotime($row['sale_date'])),
							'phone_number' => $row['phone_number'],
							'customer_name' => $row['customer_name'],
							'quantity' => to_quantity_decimals($row['items_purchased']),
							'employee_name' => $row['employee_name'],
							'subtotal' => to_currency($row['subtotal']),
							'tax' => to_currency($row['tax']),
							'total' => to_currency($row['total']),
							'cost' => to_currency($row['cost']),
							'profit' => to_currency($row['profit']),
							'payment_type' => $row['payment_type'],
							'comment' => $row['comment']));

						foreach ($report_data['details'][$key] as $drow) {
							$details_data[$row['sale_id']][] = $this->xss_clean(array($drow['name'], $drow['category'], to_quantity_decimals($drow['quantity_purchased']), to_currency($drow['subtotal']), to_currency($drow['tax']), to_currency($drow['total']), $drow['discount_percent'] . '%'));
						}
					}
					//var_dump($headers);die();
					$data = array(
						'headers' => $headers,
						'summary_data' => $summary_data,
						'details_data' => $details_data,
						'report_data' => $report_data,
						'overall_summary_data' => $this->xss_clean($this->Customer_infoex->getSummaryData($inputs))
					);
				} else {
					$data['message'] = 'Chưa nhập thông tin';
					$data['report_data'] = null;
				}
			}
		}else{
			$inputs['term'] = $textinput;

			$this->Customer_infoex->create($inputs);
			$headers = $this->xss_clean($this->Customer_infoex->getDataColumns());
			$report_data = $this->Customer_infoex->getData($inputs);
			$summary_data = array();
			$details_data = array();

			foreach ($report_data['summary'] as $key => $row) {
				$summary_data[] = $this->xss_clean(array(
					'id' => anchor('sales/receipt/' . $row['sale_id'], 'POS ' . $row['sale_id'], array('target' => '_blank')),
					'sale_date' => date('d/m/Y', strtotime($row['sale_date'])),
					'phone_number' => $row['phone_number'],
					'customer_name' => $row['customer_name'],
					'quantity' => to_quantity_decimals($row['items_purchased']),
					'employee_name' => $row['employee_name'],
					'subtotal' => to_currency($row['subtotal']),
					'tax' => to_currency($row['tax']),
					'total' => to_currency($row['total']),
					'cost' => to_currency($row['cost']),
					'profit' => to_currency($row['profit']),
					'payment_type' => $row['payment_type'],
					'comment' => $row['comment']));

				foreach ($report_data['details'][$key] as $drow) {
					$details_data[$row['sale_id']][] = $this->xss_clean(array($drow['name'], $drow['category'], to_quantity_decimals($drow['quantity_purchased']), to_currency($drow['subtotal']), to_currency($drow['tax']), to_currency($drow['total']), $drow['discount_percent'] . '%'));
				}
			}
			//var_dump($headers);die();
			$data = array(
				'headers' => $headers,
				'summary_data' => $summary_data,
				'details_data' => $details_data,
				'report_data' => $report_data,
				'overall_summary_data' => $this->xss_clean($this->Customer_infoex->getSummaryData($inputs))
			);
		}
		$this->load->view('customer_info/index', $data);
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
						'only_invoices' => $this->config->item('invoice_enable') && $this->input->get('only_invoices'),
						'is_valid_receipt' => $this->Sale->is_valid_receipt($search));

		// check if any filter is set in the multiselect dropdown
		$filledup = array_fill_keys($this->input->get('filters'), TRUE);
		$filters = array_merge($filters, $filledup);

		$sales = $this->Sale->search($search, $filters, $limit, $offset, $sort, $order);
		$total_rows = $this->Sale->get_found_rows($search, $filters);
		$payments = $this->Sale->get_payments_summary($search, $filters);
		$payment_summary = $this->xss_clean(get_sales_manage_payments_summary($payments, $sales, $this));

		$data_rows = array();
		foreach($sales->result() as $sale)
		{
			$data_rows[] = $this->xss_clean(get_sale_data_row($sale));
		}

		if($total_rows > 0)
		{
			$data_rows[] = $this->xss_clean(get_sale_data_last_row($sales, $this));
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
		if($this->Customer->exists($customer_id))
		{
			$this->sale_lib->set_customer($customer_id);

			$discount_percent = $this->Customer->get_info($customer_id)->discount_percent;

			// apply customer default discount to items that have 0 discount
			if($discount_percent != '')
			{	
				$this->sale_lib->apply_customer_discount($discount_percent);
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

		// if the customer discount is 0 or no customer is selected apply the default sales discount
		if($discount == 0)
		{
			$discount = $this->config->item('default_sales_discount');
		}

		$mode = $this->sale_lib->get_mode();
		$quantity = ($mode == 'return') ? -1 : 1;
		$item_location = $this->sale_lib->get_sale_location();
		$item_id_or_number_or_item_kit_or_receipt = $this->input->post('item');

		if($mode == 'return' && $this->Sale->is_valid_receipt($item_id_or_number_or_item_kit_or_receipt))
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

	public function edit_item($item_id)
	{
		$data = array();

		$this->form_validation->set_rules('price', 'lang:items_price', 'required|callback_numeric');
		$this->form_validation->set_rules('quantity', 'lang:items_quantity', 'required|callback_numeric');
		$this->form_validation->set_rules('discount', 'lang:items_discount', 'required|callback_numeric');

		$description = $this->input->post('description');
		$serialnumber = $this->input->post('serialnumber');
		$price = parse_decimals($this->input->post('price'));
		$quantity = parse_decimals($this->input->post('quantity'));
		$discount = parse_decimals($this->input->post('discount'));
		$item_location = $this->input->post('location');

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

	public function delete_item($item_number)
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
		$this->sale_lib->clear_suspend_id();
		$this->_reload();
	}


	public function before_complete()
	{
		$data = array();
		$this->form_validation->set_rules('amount_tendered', 'lang:sales_amount_tendered', 'trim|required|callback_numeric');

		if($this->form_validation->run() == FALSE)
		{
			$data['error'] = $this->lang->line('sales_must_enter_numeric');
		}else{
			$amount_tendered = $this->input->post('amount_tendered');
			$this->sale_lib->add_payment($this->lang->line('sales_check'), $amount_tendered);
		}

		$data['cart'] = $this->sale_lib->get_cart();
		$status = 1;
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
		$data['employee'] = $employee_info->first_name  . ' ' . $employee_info->last_name[0];
		$data['company_info'] = implode("\n", array(
			$this->config->item('address'),
			$this->config->item('phone'),
			$this->config->item('account_number')
		));
		$payments = array();
		$payment['payment_type'] = $this->lang->line('sales_cash');
		$payment['payment_amount'] = 0;
		$payment['payment_kind'] = $this->lang->line('sales_reserve_money');
		if(count($data['payments']) == 0)
		{
			$data['error'] = 'Chưa thêm thanh toán, kiểm tra lại thông tin';

			$this->_reload($data);
		}else{

			if(isset($data['payments'][$this->lang->line('sales_reserve_money')]))
			{
				$old_payments = $data['payments'][$this->lang->line('sales_reserve_money')];
			}else{
				$old_payments = null;
			}
			if(isset($data['payments'][$this->lang->line('sales_paid_money')]))
			{
				$new_payments = $data['payments'][$this->lang->line('sales_paid_money')];
			}else{
				$new_payments = null;
			}
			foreach($new_payments as $item)
			{
				$payment['payment_amount'] = $payment['payment_amount'] + $item['payment_amount'];
			}

			$payments[] = $payment;

			$customer_id = $this->sale_lib->get_customer();
			$test_id = $this->sale_lib->get_test_id();
			if(!isset($test_id)){
				$test_id = 0;
			}
			$customer_info = $this->_load_customer_data($customer_id, $data);
			$invoice_number = $this->_substitute_invoice_number($customer_info);

			if($this->sale_lib->is_invoice_number_enabled() && $this->Sale->check_invoice_number_exists($invoice_number))
			{
				$data['error'] = $this->lang->line('sales_invoice_number_duplicate');
				$this->_reload($data);
			}
			else
			{
				$invoice_number = $this->sale_lib->is_invoice_number_enabled() ? $invoice_number : NULL;
				$data['invoice_number'] = $invoice_number;
				$data['sale_id_num'] = $this->Sale->save($data['cart'], $customer_id, $employee_id, $data['comments'], $invoice_number, $payments,$amount_change,$suspended_sale_id,$status,$test_id);
				$data['sale_id'] = 'POS ' . $data['sale_id_num'];
				$sale_info = $this->Sale->get_info($data['sale_id_num'])->row_array();
				$data['code'] = $sale_info['code'];
				$data = $this->xss_clean($data);

				if($data['sale_id_num'] == -1)
				{
					$data['error_message'] = $this->lang->line('sales_transaction_failed');
				}
				else
				{
					$data['barcode'] = $this->barcode_lib->generate_receipt_barcode($data['sale_id']);
				}

				$data['cur_giftcard_value'] = $this->sale_lib->get_giftcard_remainder();
				$data['print_after_sale'] = $this->sale_lib->is_print_after_sale();
				$data['email_receipt'] = $this->sale_lib->get_email_receipt();

				if($this->sale_lib->is_invoice_number_enabled())
				{
					$this->load->view('sales/invoice', $data);
				}
				else
				{
					$this->load->view('sales/receipt', $data);
				}

				$this->sale_lib->clear_all();
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
			$data['employee'] = $employee_info->first_name . ' ' . $employee_info->last_name[0];
			$data['company_info'] = implode("\n", array(
				$this->config->item('address'),
				$this->config->item('phone'),
				$this->config->item('account_number')
			));
			$customer_id = $this->sale_lib->get_customer();
			$customer_info = $this->_load_customer_data($customer_id, $data);
			$invoice_number = $this->_substitute_invoice_number($customer_info);
			$sale_id = $this->sale_lib->get_sale_id();
			if(empty($data['payments']))
			{
				$data['payments'][$this->lang->line('sales_paid_money')] = array();
			}

		if($this->input->post('hidden_form')) {
			if ($this->sale_lib->is_invoice_number_enabled() && $this->Sale->check_invoice_number_exists($invoice_number)) {
				$data['error'] = $this->lang->line('sales_invoice_number_duplicate');

				$this->_reload($data);
			} else {
				$invoice_number = $this->sale_lib->is_invoice_number_enabled() ? $invoice_number : NULL;
				$data['invoice_number'] = $invoice_number;

				if ($sale_id > 0) {
					//update - payment, and sale status from 1 to 0
					$data['sale_id_num'] = $sale_id;
					$data['sale_id'] = 'POS ' . $data['sale_id_num'];
					$sale_info = $this->Sale->get_info($data['sale_id_num'])->row_array();
					$sale_data = array(
						'customer_id' => $sale_info['customer_id'],
						'employee_id' => $sale_info['employee_id'],
						'status' => 0
					);
					$success = $this->Sale->update($sale_id, $sale_data, $data['payments'][$this->lang->line('sales_paid_money')],$employee_id,$customer_id,$amount_change);
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
																$amount_change, $suspended_sale_id);
					$data['sale_id'] = 'POS ' . $data['sale_id_num'];
					$sale_info = $this->Sale->get_info($data['sale_id_num'])->row_array();
				}
				$data['code'] = $sale_info['code'];
				$data = $this->xss_clean($data);

				if ($data['sale_id_num'] == -1) {
					$data['error_message'] = $this->lang->line('sales_transaction_failed');
				} else {
					$data['barcode'] = $this->barcode_lib->generate_receipt_barcode($data['sale_id']);
				}

				$data['cur_giftcard_value'] = $this->sale_lib->get_giftcard_remainder();
				$data['print_after_sale'] = $this->sale_lib->is_print_after_sale();
				$data['email_receipt'] = $this->sale_lib->get_email_receipt();

				if ($this->sale_lib->is_invoice_number_enabled()) {
					$this->load->view('sales/invoice', $data);
				} else {
					$this->load->view('sales/receipt', $data);
				}

				$this->sale_lib->clear_all();
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
		$customer_info = '';

		if($customer_id != -1)
		{
			$customer_info = $this->Customer->get_info($customer_id);
			$data['customer'] = $customer_info->last_name . ' ' . $customer_info->first_name;
			$data['account_number'] = $customer_info->account_number;
			$data['first_name'] = $customer_info->first_name;
			$data['last_name'] = $customer_info->last_name;
			$data['customer_email'] = $customer_info->email;
			$data['customer_address'] = $customer_info->address_1;
			if(!empty($customer_info->zip) or !empty($customer_info->city))
			{
				$data['customer_location'] = $customer_info->zip . ' ' . $customer_info->city;				
			}
			else
			{
				$data['customer_location'] = '';
			}
			$data['customer_account_number'] = $customer_info->account_number;
			$data['customer_discount_percent'] = $customer_info->discount_percent;
			if($totals)
			{
				$cust_totals = $this->Customer->get_totals($customer_id);

				$data['customer_total'] = $cust_totals->total;
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
		$this->sale_lib->clear_all();
		$sale_info = $this->Sale->get_info($sale_id)->row_array();
		$this->sale_lib->copy_entire_sale($sale_id);
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
		$employee_info = $this->Employee->get_info($this->sale_lib->get_employee());
		$data['employee'] = $employee_info->first_name . ' ' . $employee_info->last_name;
		$this->_load_customer_data($this->sale_lib->get_customer(), $data);

		$data['sale_id_num'] = $sale_id;
		$data['code'] = $sale_info['code'];
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

	private function _reload($data = array())
	{		
		$data['sale_id'] = $this->sale_lib->get_sale_id();
		$data['cart'] = $this->sale_lib->get_cart();
		$data['modes'] = array('sale' => $this->lang->line('sales_sale'), 'return' => $this->lang->line('sales_return'));
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

		$data['items_module_allowed'] = $this->Employee->has_grant('items', $this->Employee->get_logged_in_employee_info()->person_id);

		$customer_info = $this->_load_customer_data($this->sale_lib->get_customer(), $data, TRUE);
		$data['invoice_number'] = $this->_substitute_invoice_number($customer_info);
		$data['invoice_number_enabled'] = $this->sale_lib->is_invoice_number_enabled();
		$data['print_after_sale'] = $this->sale_lib->is_print_after_sale();
		$data['payments_cover_total'] = $this->sale_lib->get_amount_due() <= 0;
		
		$data = $this->xss_clean($data);

		$this->load->view("sales/register", $data);
	}

	public function receipt($sale_id)
	{
		$data = $this->_load_sale_data($sale_id);

		$this->load->view('sales/receipt', $data);

		$this->sale_lib->clear_all();
	}

	public function invoice($sale_id)
	{
		$data = $this->_load_sale_data($sale_id);

		$this->load->view('sales/invoice', $data);

		$this->sale_lib->clear_all();
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

	public function editsale($sale_id)
	{
		$this->sale_lib->clear_all();
		$this->sale_lib->copy_entire_sale($sale_id);
		$this->_reload();
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

	public function test($customer_id,$principle_id){

		$this->sale_lib->clear_all($customer_id);
		if($this->Customer->exists($customer_id))
		{
			$this->sale_lib->set_customer($customer_id);
			$this->sale_lib->set_test_id($principle_id);

			$discount_percent = $this->Customer->get_info($customer_id)->discount_percent;

			// apply customer default discount to items that have 0 discount
			if($discount_percent != '')
			{
				$this->sale_lib->apply_customer_discount($discount_percent);
			}
		}

		$this->_reload();
	}
}
?>
