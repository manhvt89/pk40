<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once("Secure_Controller.php");

class Verify extends CI_Controller
{
	public function __construct()
	{
		parent::__construct('');
		$this->load->library('sale_lib');
		$this->load->library('test_lib');
		$this->load->library('barcode_lib');
		$this->load->library('email_lib');
        $this->load->library('item_lib');
	}

	public function check()
	{
		$_sSUuID = $this->input->post('sale_uuid');
		$_nQ1 = $this->input->post('q1');
		$_nQ2 = $this->input->post('q2');
		$_nQ3 = $this->input->post('q3');
		$_nConfirm = $this->input->post('confirm');
		$_sCUuID = $this->input->post('c_uuid');
		
		$sale_info = $this->Sale->get_info_by_uuid($_sSUuID)->row_array();
		if (!empty($sale_info)) {
			$iCustomer_ID = $sale_info['customer_id'];
			//$customer_info = $this->Customer->get_info($iCustomer_ID);
			//var_dump($sale_info);die();
			$this->Sale->update_confirm($_nConfirm, $_nQ1, $_nQ2, $_nQ3, $sale_info);
			//$customer_info = $this->Customer->get_info($iCustomer_ID);
			//$sale_info = $this->Sale->get_info($sale_id)->row_array();

			redirect('verify/info/'.$_sCUuID);
		}
	}

	public function info($sCUUID=0)
	{
		$data = array();
		$data['customer'] = $this->Customer->get_info_by_uuic($sCUUID);
		//var_dump($data['customer']);
		$data['receipt_title'] = "Thông tin khách hàng";
		$this->load->view('verify/info', $data);
	}

	public function confirm($uuid='')
	{
		
		if($uuid != '')
		{
			$data['sale_uuid'] = $uuid;
			//echo $data['sale_uuid'];
			//$this->load->view('login');
			$sale_info = $this->Sale->get_info_by_uuid($uuid)->row_array();
			if(empty($sale_info))
			{
				//khonglam gi
			} else {
				//var_dump($sale_info);die();
				if ($sale_info['confirm'] > 0) {
					redirect('verify/info/'.$sale_info['c_uuid']);
				} else {
					$_sToken = '';
					$sSubString = substr($sale_info['phone_number'], -6);
					$_sToken = md5($sSubString); //Mã hóa token md5
					$data['token'] = $_sToken;
					//var_dump($sale_info);
					$this->load->view('verify/confirm', $data);
				}
			}
		} else {
			
				$this->form_validation->set_rules('sale_uuid', 'Token', 'callback_uuid_check');
				$this->form_validation->set_error_delimiters('<div class="error">', '</div>');
				
				if($this->form_validation->run() == FALSE)
				{
					$_sUuID = $this->input->post('sale_uuid');
					$sale_info = $this->Sale->get_info_by_uuid($_sUuID)->row_array();
					if (!empty($sale_info)) {
						$_sToken = '';
						$sSubString = substr($sale_info['phone_number'], -6);
						$_sToken = md5($sSubString); //Mã hóa token md5
					} else {
						$_sToken = md5(''); //Mã hóa token md5
					}
					$data['token'] = $_sToken;
					$data['sale_uuid'] = $_sUuID;
					$this->load->view('verify/confirm',$data);
				}
				else
				{
					$_sUuID = $this->input->post('sale_uuid');
					$sale_info = $this->Sale->get_info_by_uuid($_sUuID)->row_array();
					$data['sale_uuid'] = $_sUuID;
					//var_dump($sale_info);
					$data['c_uuid'] = $sale_info['c_uuid'];
					//var_dump($data);die();
					
					if($sale_info['confirm'] > 0)
					{
						redirect('verify/info/'.$data['c_uuid']);
					}
					else
					{
						$data = $this->_load_sale_data($sale_info['sale_id']);
						$data['c_uuid'] = $sale_info['c_uuid'];
						$data['sale_uuid'] = $_sUuID;
						$this->sale_lib->clear_all();
						//var_dump($data);die();	
						$this->load->view('verify/survey_form', $data); //form survey
					}
				}
			
		}
	}

	public function uuid_check($username)
	{
		$sale_uuid = $this->input->post('sale_uuid');
		$sale_token = $this->input->post('sale_token');
		$token = $this->input->post('token');

		if(md5($token) != $sale_token)
		{
			$this->form_validation->set_message('uuid_check', 'Token bạn nhập chưa đúng, nếu chắc chắn đúng 6 số cuối số điện thoại vui lòng liên hệ HOTLINE');
			return FALSE;
		}

		return TRUE;		
	}

    public function view($item_id = -1)
    {
        $data['item_tax_info'] = $this->xss_clean($this->Item_taxes->get_info($item_id));
        $data['default_tax_1_rate'] = '';
        $data['default_tax_2_rate'] = '';
        $data['employee_id'] = $this->Employee->get_logged_in_employee_info()->person_id;

        if($item_id == -1)
        {
            $data['default_tax_1_rate'] = $this->config->item('default_tax_1_rate');
            $data['default_tax_2_rate'] = $this->config->item('default_tax_2_rate');
        }

        $this->load->view('accounting/form', $data);
    }

    public function viewi($item_id = -1)
    {
        $data['item_tax_info'] = $this->xss_clean($this->Item_taxes->get_info($item_id));
        $data['default_tax_1_rate'] = '';
        $data['default_tax_2_rate'] = '';
        $data['employee_id'] = $this->Employee->get_logged_in_employee_info()->person_id;

        if($item_id == -1)
        {
            $data['default_tax_1_rate'] = $this->config->item('default_tax_1_rate');
            $data['default_tax_2_rate'] = $this->config->item('default_tax_2_rate');
        }

        $this->load->view('accounting/formi', $data);
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
			$data['phone_number'] = $customer_info->phone_number;
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
		$data['employee'] = $employee_info->last_name . ' ' . $employee_info->first_name;
		$this->_load_customer_data($this->sale_lib->get_customer(), $data);

		$data['sale_id_num'] = $sale_id;
		$data['code'] = $sale_info['code'];
		$data['sale_id'] = 'POS ' . $sale_id;
		$data['comments'] = $sale_info['comment'];
		$data['invoice_number'] = $sale_info['invoice_number'];
		$data['sale_uuid'] = $sale_info['sale_uuid'];
		$data['company_info'] = implode("\n", array(
			$this->config->item('address'),
			$this->config->item('phone'),
			$this->config->item('account_number')
		));
		$data['barcode'] = $this->barcode_lib->generate_receipt_barcode($data['sale_id']);
		$data['print_after_sale'] = FALSE;
		//var_dump($sale_info);die();
		//$data['c_uuid'] = $sale_info['c_uuid'];
		return $data;
	}

}
?>