<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once("Secure_Controller.php");
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Csv;
//use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
class Receivings extends Secure_Controller
{
	public function __construct()
	{
		parent::__construct('receivings');

		$this->load->library('receiving_lib');
		$this->load->library('purchase_lib');
		$this->load->library('barcode_lib');
	}

	public function index()
	{
		$this->_reload();
	}

	public function item_search()
	{
		$suggestions = $this->Item->get_search_suggestions($this->input->get('term'), array('search_custom' => FALSE, 'is_deleted' => FALSE), TRUE);
		$suggestions = array_merge($suggestions, $this->Item_kit->get_search_suggestions($this->input->get('term')));

		$suggestions = $this->xss_clean($suggestions);

		echo json_encode($suggestions);
	}

	public function select_supplier()
	{
		$supplier_id = $this->input->post('supplier');
		if($this->Supplier->exists($supplier_id))
		{
			$this->receiving_lib->set_supplier($supplier_id);
		}

		$this->_reload();
	}

	public function change_mode()
	{
		$stock_destination = $this->input->post('stock_destination');
		$stock_source = $this->input->post('stock_source');

		if((!$stock_source || $stock_source == $this->receiving_lib->get_stock_source()) &&
			(!$stock_destination || $stock_destination == $this->receiving_lib->get_stock_destination()))
		{
			$this->receiving_lib->clear_reference();
			$mode = $this->input->post('mode');
			$this->receiving_lib->set_mode($mode);
		}
		elseif($this->Stock_location->is_allowed_location($stock_source, 'receivings'))
		{
			$this->receiving_lib->set_stock_source($stock_source);
			$this->receiving_lib->set_stock_destination($stock_destination);
		}

		$this->_reload();
	}
	
	public function set_comment()
	{
		$this->receiving_lib->set_comment($this->input->post('comment'));
	}

	public function set_print_after_sale()
	{
		$this->receiving_lib->set_print_after_sale($this->input->post('recv_print_after_sale'));
	}
	
	public function set_reference()
	{
		$this->receiving_lib->set_reference($this->input->post('recv_reference'));
	}
	
	public function add()
	{
		$data = array();

		$mode = $this->receiving_lib->get_mode();
		$item_id_or_number_or_item_kit_or_receipt = $this->input->post('item');
		$quantity = ($mode == 'receive' || $mode == 'requisition') ? 1 : -1;
		$item_location = $this->receiving_lib->get_stock_source();
		//var_dump($item_location);
		if($mode == 'return' && $this->Receiving->is_valid_receipt($item_id_or_number_or_item_kit_or_receipt))
		{
			$this->receiving_lib->return_entire_receiving($item_id_or_number_or_item_kit_or_receipt);
		}
		elseif($this->Item_kit->is_valid_item_kit($item_id_or_number_or_item_kit_or_receipt))
		{
			$this->receiving_lib->add_item_kit($item_id_or_number_or_item_kit_or_receipt, $item_location);
		}
		elseif(!$this->receiving_lib->add_item($item_id_or_number_or_item_kit_or_receipt, $quantity, $item_location))
		{
			$data['error'] = $this->lang->line('receivings_unable_to_add_item');
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
			$this->receiving_lib->edit_item($item_id, $description, $serialnumber, $quantity, $discount, $price);
		}
		else
		{
			$data['error']=$this->lang->line('receivings_error_editing_item');
		}

		$this->_reload($data);
	}
	
	public function edit($receiving_id)
	{
		$data = array();

		$data['suppliers'] = array('' => 'No Supplier');
		foreach($this->Supplier->get_all()->result() as $supplier)
		{
			$data['suppliers'][$supplier->person_id] = $this->xss_clean($supplier->first_name . ' ' . $supplier->last_name);
		}
	
		$data['employees'] = array();
		foreach ($this->Employee->get_all()->result() as $employee)
		{
			$data['employees'][$employee->person_id] = $this->xss_clean($employee->first_name . ' '. $employee->last_name);
		}
	
		$receiving_info = $this->xss_clean($this->Receiving->get_info($receiving_id)->row_array());
		$data['selected_supplier_name'] = !empty($receiving_info['supplier_id']) ? $receiving_info['company_name'] : '';
		$data['selected_supplier_id'] = $receiving_info['supplier_id'];
		$data['receiving_info'] = $receiving_info;
	
		$this->load->view('receivings/form', $data);
	}

	public function delete_item($item_number)
	{
		$this->receiving_lib->delete_item($item_number);

		$this->_reload();
	}
	
	public function delete($receiving_id = -1, $update_inventory = TRUE) 
	{
		$employee_id = $this->Employee->get_logged_in_employee_info()->person_id;
		$receiving_ids = $receiving_id == -1 ? $this->input->post('ids') : array($receiving_id);
	
		if($this->Receiving->delete_list($receiving_ids, $employee_id, $update_inventory))
		{
			echo json_encode(array('success' => TRUE, 'message' => $this->lang->line('receivings_successfully_deleted') . ' ' .
							count($receiving_ids) . ' ' . $this->lang->line('receivings_one_or_multiple'), 'ids' => $receiving_ids));
		}
		else
		{
			echo json_encode(array('success' => FALSE, 'message' => $this->lang->line('receivings_cannot_be_deleted')));
		}
	}

	public function remove_supplier()
	{
		$this->receiving_lib->clear_reference();
		$this->receiving_lib->remove_supplier();

		$this->_reload();
	}

	public function complete()
	{
		$data = array();
		
		$data['cart'] = $this->receiving_lib->get_cart();
	
		$data['total'] = $this->receiving_lib->get_total();
		$data['quantity'] = $this->receiving_lib->get_quantity();
		
		$data['transaction_time'] = date($this->config->item('dateformat') . ' ' . $this->config->item('timeformat'));
		$data['mode'] = $this->receiving_lib->get_mode();
		$data['comment'] = $this->receiving_lib->get_comment();
		$data['reference'] = $this->receiving_lib->get_reference();
		$data['payment_type'] = $this->input->post('payment_type');
		$data['show_stock_locations'] = $this->Stock_location->show_locations('receivings');
		$data['stock_location'] = $this->receiving_lib->get_stock_source();
		$data['purchase_id'] = $this->receiving_lib->get_purchase_id();
		if($data['mode']=='return')
		{
			$data['receipt_title'] = 'PHIẾU TRẢ HÀNG';
		} else {
			$data['receipt_title'] = $this->lang->line('receivings_receipt');
		}
		if($this->input->post('amount_tendered') != NULL)
		{
			$_inputValue = $this->input->post('amount_tendered');
			$_cleanedValue = str_replace('.', '', $_inputValue);
			$_cleanedValue = str_replace(',', '.', $_cleanedValue);

			// Chuyển đổi thành số
			$_numericValue = floatval($_cleanedValue);
			$data['amount_tendered'] = $_numericValue;
			$data['amount_change'] = $data['total'] - $data['amount_tendered'];
		}
		
		$employee_id = $this->Employee->get_logged_in_employee_info()->person_id;
		$employee_info = $this->Employee->get_info($employee_id);
		$data['employee'] = $employee_info->first_name . ' ' . $employee_info->last_name;

		$supplier_info = '';
		$supplier_id = $this->receiving_lib->get_supplier();
		$data['print_after_sale'] = $this->receiving_lib->is_print_after_sale();
		if($supplier_id != -1)
		{
			$supplier_info = $this->Supplier->get_info($supplier_id);
			$data['supplier'] = $supplier_info->company_name;
			$data['first_name'] = $supplier_info->first_name;
			$data['last_name'] = $supplier_info->last_name;
			$data['supplier_email'] = $supplier_info->email;
			$data['supplier_address'] = $supplier_info->address_1;
			if(!empty($supplier_info->zip) or !empty($supplier_info->city))
			{
				$data['supplier_location'] = $supplier_info->zip . ' ' . $supplier_info->city;				
			}
			else
			{
				$data['supplier_location'] = '';
			}
		}
		$_aPayments = [
			'payment_type'=>$data['payment_type'],
			'payment_kind'=>0,
			'remain_amount'=>$data['amount_change'],
			'paid_amount'=>$data['amount_tendered'],
			'total_amount'=>$data['total']
		];
		//var_dump($_aPayments);die();
		//SAVE receiving to database
		$data['_receive_id'] = $this->Receiving->save($data['cart'], $supplier_id, $employee_id, $data['comment'], $data['reference'], $_aPayments, $data['stock_location'], $data['purchase_id'],$data['mode']);
		$data['receiving_id'] = 'RECV ' .$data['_receive_id'];
		$data = $this->xss_clean($data);

		if($data['receiving_id'] == 'RECV -1')
		{
			$data['error_message'] = $this->lang->line('receivings_transaction_failed');
		}
		else
		{
			$this->receiving_lib->clear_all();
			$data['barcode'] = $this->barcode_lib->generate_receipt_barcode($data['receiving_id']);	
			$receiving_info = $this->Receiving->get_info($data['_receive_id'])->row();
			$this->receiving_lib->copy_entire_receiving($receiving_info);
			//$data['_receive_id'] = $receiving_info->receiving_id;
			$data['cart'] = $this->receiving_lib->get_cart();
			$data['total'] = $this->receiving_lib->get_total();
			$data['amount'] = $this->receiving_lib->get_amount();
			$data['mode'] = $this->receiving_lib->get_mode();
			$data['receipt_title'] = $this->lang->line('receivings_receipt');
			$data['transaction_time'] = date($this->config->item('dateformat') . ' ' . $this->config->item('timeformat'), strtotime($receiving_info->receiving_time));
			$data['show_stock_locations'] = $this->Stock_location->show_locations('receivings');
			$data['payment_type'] = $receiving_info->payment_type;
			$data['reference'] = $this->receiving_lib->get_reference();
			$data['receiving_id'] = $receiving_info->receiving_id;
			$data['code'] = $receiving_info->code;
			$data['barcode'] = $this->barcode_lib->generate_receipt_barcode($receiving_info->code);
			$data['payments'] = $this->Receiving->get_payments($receiving_info->receiving_id)->result_array();
			$employee_info = $this->Employee->get_info($receiving_info->employee_id);
			$data['employee'] = $employee_info->first_name . ' ' . $employee_info->last_name;
			if($data['mode']=='return')
			{
				$data['receipt_title'] = 'PHIẾU TRẢ HÀNG';
			} else {
				$data['receipt_title'] = $this->lang->line('receivings_receipt');
			}
			$supplier_id = $this->receiving_lib->get_supplier();
			if($supplier_id != -1) {
				$supplier_info = $this->Supplier->get_info($supplier_id);
				$data['supplier'] = $supplier_info->company_name;
				$data['first_name'] = $supplier_info->first_name;
				$data['last_name'] = $supplier_info->last_name;
				$data['supplier_email'] = $supplier_info->email;
				$data['supplier_address'] = $supplier_info->address_1;
				if(!empty($supplier_info->zip) or !empty($supplier_info->city)) {
					$data['supplier_location'] = $supplier_info->zip . ' ' . $supplier_info->city;
				} else {
					$data['supplier_location'] = '';
				}
			}
			//var_dump($data['payments']);
			$data['paid_total'] = $receiving_info->paid_amount;;
			
			$data['remain_amount'] = $receiving_info->remain_amount;
			$data['print_after_sale'] = false;

			$data = $this->xss_clean($data);	

		}
		$this->receiving_lib->clear_all();
		$this->load->view("receivings/receipt",$data);

		
	}

	public function requisition_complete()
	{
		if($this->receiving_lib->get_stock_source() != $this->receiving_lib->get_stock_destination()) 
		{
			foreach($this->receiving_lib->get_cart() as $item)
			{
				$this->receiving_lib->delete_item($item['line']);
				$this->receiving_lib->add_item($item['item_id'], $item['quantity'], $this->receiving_lib->get_stock_destination());
				$this->receiving_lib->add_item($item['item_id'], -$item['quantity'], $this->receiving_lib->get_stock_source());
			}
			
			$this->complete();
		}
		else 
		{
			$data['error'] = $this->lang->line('receivings_error_requisition');

			$this->_reload($data);	
		}
	}
	
	public function receipt($receiving_id)
	{
		$data = array();
		$receiving_info = $this->Receiving->get_info($receiving_id)->row();
		//var_dump($receiving_info);
		if(empty($receiving_info))
		{
			$data['error_message'] = 'Không tồn tại phiếu này';
			$this->load->view("receivings/receipt", $data);
		} else {
			$this->receiving_lib->copy_entire_receiving($receiving_info);
			$data['_receive_id'] = $receiving_info->receiving_id;
			$data['cart'] = $this->receiving_lib->get_cart();
			$data['total'] = $this->receiving_lib->get_total();
			$data['amount'] = $this->receiving_lib->get_amount();
			$data['mode'] = $this->receiving_lib->get_mode();
			$data['receipt_title'] = $this->lang->line('receivings_receipt');
			$data['transaction_time'] = date($this->config->item('dateformat') . ' ' . $this->config->item('timeformat'), strtotime($receiving_info->receiving_time));
			$data['show_stock_locations'] = $this->Stock_location->show_locations('receivings');
			$data['payment_type'] = $receiving_info->payment_type;
			$data['reference'] = $this->receiving_lib->get_reference();
			$data['receiving_id'] = $receiving_info->receiving_id;
			$data['code'] = $receiving_info->code;
			$data['barcode'] = $this->barcode_lib->generate_receipt_barcode($receiving_info->code);
			$data['payments'] = $this->Receiving->get_payments($receiving_info->receiving_id)->result_array();
			$employee_info = $this->Employee->get_info($receiving_info->employee_id);
			$data['employee'] = $employee_info->first_name . ' ' . $employee_info->last_name;
			if($data['mode']=='return')
			{
				$data['receipt_title'] = 'PHIẾU TRẢ HÀNG';
			} else {
				$data['receipt_title'] = $this->lang->line('receivings_receipt');
			}
			$supplier_id = $this->receiving_lib->get_supplier();
			if($supplier_id != -1) {
				$supplier_info = $this->Supplier->get_info($supplier_id);
				$data['supplier'] = $supplier_info->company_name;
				$data['first_name'] = $supplier_info->first_name;
				$data['last_name'] = $supplier_info->last_name;
				$data['supplier_email'] = $supplier_info->email;
				$data['supplier_address'] = $supplier_info->address_1;
				if(!empty($supplier_info->zip) or !empty($supplier_info->city)) {
					$data['supplier_location'] = $supplier_info->zip . ' ' . $supplier_info->city;
				} else {
					$data['supplier_location'] = '';
				}
			}
			//var_dump($data['payments']);
			$data['paid_total'] = $receiving_info->paid_amount;;
			
			$data['remain_amount'] = $receiving_info->remain_amount;
			$data['print_after_sale'] = false;

			$data = $this->xss_clean($data);

			$this->load->view("receivings/receipt", $data);

			$this->receiving_lib->clear_all();
		}
	}

	private function _reload($data = array())
	{
		$data['cart'] = $this->receiving_lib->get_cart();
		$data['quantity'] = $this->receiving_lib->get_quantity();
		$data['modes'] = array('receive' => $this->lang->line('receivings_receiving'), 'return' => $this->lang->line('receivings_return'));
		$data['mode'] = $this->receiving_lib->get_mode();
		$data['stock_locations'] = $this->Stock_location->get_allowed_locations('receivings');
		$data['show_stock_locations'] = count($data['stock_locations']) > 1;
		if($data['show_stock_locations']) 
		{
			$data['modes']['requisition'] = $this->lang->line('receivings_requisition');
			$data['stock_source'] = $this->receiving_lib->get_stock_source();
			$data['stock_destination'] = $this->receiving_lib->get_stock_destination();
		}

		$data['total'] = $this->receiving_lib->get_total();
		$data['items_module_allowed'] = $this->Employee->has_grant('items_inventory', $this->Employee->get_logged_in_employee_info()->person_id);
		$data['comment'] = $this->receiving_lib->get_comment();
		$data['reference'] = $this->receiving_lib->get_reference();
		$data['payment_options'] = $this->Receiving->get_payment_options();

		$supplier_id = $this->receiving_lib->get_supplier();
		$supplier_info = '';
		if($supplier_id != -1)
		{
			$supplier_info = $this->Supplier->get_info($supplier_id);
			$data['supplier'] = $supplier_info->company_name;
			$data['first_name'] = $supplier_info->first_name;
			$data['last_name'] = $supplier_info->last_name;
			$data['supplier_email'] = $supplier_info->email;
			$data['supplier_address'] = $supplier_info->address_1;
			if(!empty($supplier_info->zip) or !empty($supplier_info->city))
			{
				$data['supplier_location'] = $supplier_info->zip . ' ' . $supplier_info->city;				
			}
			else
			{
				$data['supplier_location'] = '';
			}
		}
		
		$data['print_after_sale'] = $this->receiving_lib->is_print_after_sale();

		$data = $this->xss_clean($data);

		$this->load->view("receivings/receiving", $data);
	}
	
	public function save($receiving_id = -1)
	{
		$newdate = $this->input->post('date');
		
		$date_formatter = date_create_from_format($this->config->item('dateformat') . ' ' . $this->config->item('timeformat'), $newdate);

		$receiving_data = array(
			'receiving_time' => $date_formatter->format('Y-m-d H:i:s'),
			'supplier_id' => $this->input->post('supplier_id') ? $this->input->post('supplier_id') : NULL,
			'employee_id' => $this->input->post('employee_id'),
			'comment' => $this->input->post('comment'),
			'reference' => $this->input->post('reference') != '' ? $this->input->post('reference') : NULL
		);
	
		if($this->Receiving->update($receiving_data, $receiving_id))
		{
			echo json_encode(array('success' => TRUE, 'message' => $this->lang->line('receivings_successfully_updated'), 'id' => $receiving_id));
		}
		else
		{
			echo json_encode(array('success' => FALSE, 'message' => $this->lang->line('receivings_unsuccessfully_updated'), 'id' => $receiving_id));
		}
	}

	public function cancel_receiving()
	{
		$this->receiving_lib->clear_all();

		$this->_reload();
	}

	public function lens()
	{
		$data = array();
		//echo '123';die();
		
        $data['item_count'] = $this->config->item('iKindOfLens');
		//var_dump($data['item_count']);
		$data['page_title'] = 'NHẬP MẮT KÍNH';

		$cyls = $this->config->item('cyls');
		$mysphs = $this->config->item('mysphs');
		$hysphs = $this->config->item('hysphs');
		
		$data['cyls'] = $cyls;
		$data['mysphs'] = $mysphs;
		$data['hysphs'] = $hysphs;
		
		$this->form_validation->set_rules('hhmyo', 'hhmyo', 'callback_number_empty');
		
		if($this->form_validation->run() == FALSE)
		{
			//echo '123'; die();
			$this->load->view("receivings/lens", $data);
		} else {
			// Nhập sản phẩm //Mắt
			$category = $this->config->item('iKindOfLens')[$this->input->post('category')];
			// Lấy tất cả tròng kính trong danh mục này;
			$_aALens = $this->Receiving->get_items_by_category($category)->result_array();
			//var_dump($_aALens);
			//echo $category;
			// For Myo
			$_aTmp = array();
			$_strMyo =  $this->input->post('hhmyo');
			$_aaMyo = json_decode($_strMyo,true);
			//var_dump($_aaMyo);
			foreach($_aaMyo  as $key=>$_aSPH)
			{
				$key = $key + 1;
				//$sph = $mysphs[$key];
				$sph = $_aSPH[0];
				foreach($_aSPH as $k=>$value)
				{
					if($k > 0)
					{
						if($value != "")
						{
							$cyl = $cyls[$k];
							$_aTmp['S-'.$sph.' C-'.$cyl] = $value;
						}
					}
				}
			}

			//var_dump($_aTmp);
			// For Hyo
			$_strHyo =  $this->input->post('hhhyo');
			$_aaHyo = json_decode($_strHyo,true);
			foreach($_aaHyo  as $key=>$_aSPH)
			{
				$key = $key + 1;
				//$sph = $hysphs[$key];
				$sph = $_aSPH[0];
				foreach($_aSPH as $k=>$value)
				{
					if($k > 0)
					{
						if($value != "")
						{
							$cyl = $cyls[$k];
							$_aTmp['S+'.$sph.' C-'.$cyl] = $value;
						}
					}
				}
			}

			//var_dump($_aTmp);die();
			if(!empty($_aTmp))
			{
				$this->receiving_lib->clear_all();
				$this->purchase_lib->set_kind(2);
				foreach($_aTmp as $key=>$value)
				{
					foreach($_aALens as $k=>$v)
					{
						
						if(strpos($v['name'],$key) > 0)
						{
							//$this->receiving_lib->add_item($item_id, $quantity, $item_location);
							//$this->receiving_lib->add_item($v['item_id'], trim($value), 1);
							$this->purchase_lib->add_item_by_itemID($v['item_id'], trim($value));
						}
						
					}
				}

				//$_aCart = $this->receiving_lib->get_cart();
				redirect('purchases/');
			} else{
				//echo '1234';die();
				$this->load->view("receivings/lens", $data);
			}
			
		}
		//$this->load->view("receivings/lens", $data);
	}
	public function number_empty($str)
	{
		$return = TRUE;
		$_aTmp = array();
		$_strTmp = '';
		//var_dump($_POST['hyo101']);die();
		foreach($_POST as $key=>$value)
		{
			
			if(substr($key,0,3) == 'myo' || substr($key,0,3) == 'hyo')
			{
				if($value != ''){
					
					if(is_numeric($value))
					{
						$_aTmp[$key] = TRUE;
					} else {
						$return = FALSE;
						$_aTmp[$key] = FALSE;
						if($_strTmp == '')
						{
							$_strTmp = substr($key,3,strlen($key)-5). ' cột '. substr($key,-2);
						} else{
							$_strTmp = $_strTmp . ', ' . substr($key,3,strlen($key)-5). ' cột '. substr($key,-2);
						}
					}
				}
			}
		}
		if($return == FALSE)
		{
			$this->form_validation->set_message('number_empty', 'Vui lòng kiểm tra lại dữ liệu tại dòng '. $_strTmp);
		}
		return $return;
	}

	function export($receive_id=0)
	{
		$receive_id = $this->input->get('receive_id');
		$receiving_info = $this->Receiving->get_info($receive_id)->row();
		//var_dump($receiving_info);die();
		if(empty($receiving_info))
		{
			$data['error_message'] = 'Không tồn tại phiếu này';
			$this->load->view("receivings/receipt", $data);
		} else {
			$this->receiving_lib->copy_entire_receiving($receiving_info);
			$data['receive_id'] = $receive_id;
			$data['cart'] = $this->receiving_lib->get_cart();
			
			//$purchase_info = $this->Purchase->get_info_uuid($purchase_uuid)->row_array();
			//$cart = $this->Purchase->get_purchase_items($purchase_info['id'])->result();
			//var_dump($data);
			//var_dump($cart);die();
			$spreadsheet = new Spreadsheet(); // instantiate Spreadsheet
			$spreadsheet->getDefaultStyle()->getFont()->setName('Arial');
			$sheet = $spreadsheet->getActiveSheet();

			/**
			 * Thiết lập độ rộng các cột
			 */

			$sheet->getColumnDimension('A')->setWidth(35, 'pt');
			$sheet->getColumnDimension('B')->setWidth(175, 'pt');
			$sheet->getColumnDimension('C')->setWidth(70, 'pt');
			$sheet->getColumnDimension('D')->setWidth(36, 'pt');
			$sheet->getColumnDimension('E')->setWidth(100, 'pt');

			$sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_PORTRAIT);
			$sheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
			$sheet->getPageSetup()->setFitToWidth(1);
			$sheet->getPageSetup()->setFitToHeight(0);
			//$sheet->getPageSetup()->setPrintArea('A1:E5');




			$writer = new Xlsx($spreadsheet); // instantiate Xlsx
			$title = 'Phiếu nhập hàng';
			$name_ncc = '';
			$name_ch = '';

			// Title
			$filename = 'Phieu_tra_hang_'.$receive_id.'_'.time(); // set filename for excel file to be exported
			$j = 1;
			if($receiving_info->mode == 0)
			{
				$title = 'PHIẾU NHẬP HÀNG';
				if($receiving_info->company_name == null)
				{
					$name_ncc = 'Nhà cung cấp:';
				} else {
					$name_ncc = 'Nhà cung cấp: '.$receiving_info->company_name;
				}
				$name_ch = $this->config->item('company');
				$filename = 'Phieu_nhap_hang_'.$receive_id.'_'.time(); // set filename for excel file to be exported
			} else {
				$j = 0-1;
				$title = 'PHIẾU TRẢ HÀNG NHÀ CUNG CẤP';
				if($receiving_info->company_name == null)
				{
					$name_ncc = 'Nhà cung cấp:';
				} else {
					$name_ncc = 'Nhà cung cấp: '.$receiving_info->company_name;
				}
				$name_ch = $this->config->item('company');
			}
			
			$title_vt = date('d/m/Y',strtotime($receiving_info->receiving_time));
			$index = 1;

			$sheet->mergeCells("A$index:E$index");
			$styleArray = [
				'font' => [
					'bold' => false,
				],
				'alignment' => [
					'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
				],
				'borders' => [
					'top' => [
						'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
					],
					'left' => [
						'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
					],
					'right' => [
						'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
					],
					'bottom' => [
						'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
					]
				],
				'fill' => [
					'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_GRADIENT_LINEAR,
					'rotation' => 90,
					'startColor' => [
						'argb' => '00A0A0A0',
					],
					'endColor' => [
						'argb' => 'FFFFFFFF',
					],
				],
			];
			$sheet->getStyle('A'.$index)->applyFromArray($styleArray);
			$sheet->setCellValue('A'.$index,  $title);

			$index++;
			$sheet->mergeCells("A$index:E$index");
			$styleArray = [
				'font' => [
					'bold' => false,
				],
				'alignment' => [
					'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
				],
				'borders' => [
					'top' => [
						'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
					],
					'left' => [
						'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
					],
					'right' => [
						'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
					],
					'bottom' => [
						'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
					]
				],
				'fill' => [
					'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_GRADIENT_LINEAR,
					'rotation' => 90,
					'startColor' => [
						'argb' => '00A0A0A0',
					],
					'endColor' => [
						'argb' => 'FFFFFFFF',
					],
				],
			];
			$sheet->getStyle('A'.$index)->applyFromArray($styleArray);
			$sheet->setCellValue('A'.$index, $name_ncc);

			$index++;
			$sheet->mergeCells("A$index:E$index");
			$styleArray = [
				'font' => [
					'bold' => false,
				],
				'alignment' => [
					'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
				],
				'borders' => [
					'top' => [
						'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
					],
					'left' => [
						'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
					],
					'right' => [
						'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
					],
					'bottom' => [
						'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
					]
				],
				'fill' => [
					'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_GRADIENT_LINEAR,
					'rotation' => 90,
					'startColor' => [
						'argb' => '00A0A0A0',
					],
					'endColor' => [
						'argb' => 'FFFFFFFF',
					],
				],
			];
			$sheet->getStyle('A'.$index)->applyFromArray($styleArray);
			$sheet->setCellValue('A'.$index, $name_ch);

			$index++;
			$sheet->mergeCells("A$index:E$index");
			$styleArray = [
				'font' => [
					'bold' => false,
				],
				'alignment' => [
					'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
				],
				'borders' => [
					'top' => [
						'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
					],
					'left' => [
						'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
					],
					'right' => [
						'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
					],
					'bottom' => [
						'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
					]
				],
				'fill' => [
					'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_GRADIENT_LINEAR,
					'rotation' => 90,
					'startColor' => [
						'argb' => '00A0A0A0',
					],
					'endColor' => [
						'argb' => 'FFFFFFFF',
					],
				],
			];
			$sheet->getStyle('A'.$index)->applyFromArray($styleArray);
			$sheet->setCellValue('A'.$index, $title_vt);

			$styleArray = [
				'font' => [
					'bold' => false,
				],
				'alignment' => [
					'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
				],
				'borders' => [
					'top' => [
						'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
					],
					'left' => [
						'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
					],
					'right' => [
						'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
					],
					'bottom' => [
						'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
					]
				],
				'fill' => [
					'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_GRADIENT_LINEAR,
					'rotation' => 90,
					'startColor' => [
						'argb' => '00A0A0A0',
					],
					'endColor' => [
						'argb' => 'FFFFFFFF',
					],
				],
			];

			$index++;
			$sheet->getStyle('A'.$index)->applyFromArray($styleArray);
			//$sheet->setCellValue('A'.$index, 'STT');

			$sheet->getStyle('B'.$index)->applyFromArray($styleArray);
			$sheet->getStyle('C'.$index)->applyFromArray($styleArray);
			$sheet->getStyle('D'.$index)->applyFromArray($styleArray);
			$sheet->getStyle('E'.$index)->applyFromArray($styleArray);


			$sheet->setCellValue('A'.$index, 'STT');
			$sheet->setCellValue('B'.$index, 'Tên sản phẩm');
			$sheet->setCellValue('C'.$index, 'Giá');
			$sheet->setCellValue('D'.$index, 'Số lượng');
			$sheet->setCellValue('E'.$index, 'Thành tiền');
			
			// Body
			$_dTotal = 0;
			$_sum =0;
			if(!empty($data['cart'])) {
				
				$i = 0;
				foreach($data['cart'] as $item) {
					//var_dump($item);die();
					$_dSubtotal = $item['price']*$item['quantity']*$j;
					$index++;
					$i++;
					$_sum = $_sum + $item['quantity']*$j;
					$styleArray = [
						'font' => [
							'bold' => false,
						],
						'alignment' => [
							'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
						],
						'borders' => [
							'top' => [
								'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
							],
							'left' => [
								'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
							],
							'right' => [
								'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
							],
							'bottom' => [
								'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
							]
						],
					];
					$sheet->getStyle('A'.$index)->applyFromArray($styleArray);
					$sheet->getStyle('B'.$index)->applyFromArray($styleArray);

					$styleArray_left = [
						'font' => [
							'bold' => false,
						],
						'alignment' => [
							'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
						],
						'borders' => [
							'top' => [
								'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
							],
							'left' => [
								'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
							],
							'right' => [
								'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
							],
							'bottom' => [
								'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
							]
						],
					];
					$styleArray_right = [
						'font' => [
							'bold' => false,
						],
						'alignment' => [
							'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT,
						],
						'borders' => [
							'top' => [
								'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
							],
							'left' => [
								'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
							],
							'right' => [
								'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
							],
							'bottom' => [
								'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
							]
						],
					];
					$sheet->getStyle('C'.$index)->applyFromArray($styleArray_right);
					$sheet->getStyle('D'.$index)->applyFromArray($styleArray);
					$sheet->getStyle('E'.$index)->applyFromArray($styleArray_right);
					//var_dump( $item);die();
					$sheet->setCellValue('A'.$index, $i);
					$sheet->setCellValue('B'.$index, $item['name']);
					$sheet->setCellValue('C'.$index, number_format($item['price']));
					$sheet->setCellValue('D'.$index, number_format($item['quantity']*$j));
					$sheet->setCellValue('E'.$index,number_format($_dSubtotal));
					$_dTotal = $_dTotal + $_dSubtotal;
				}
			} else {
				$sheet->setCellValue('A'.$index, 'Chưa có sản phẩm trong phiếu trả hàng');
			}
			$index++;
			$sheet->mergeCells("A$index:C$index");
			$styleArray = [
				'font' => [
					'bold' => false,
				],
				'alignment' => [
					'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
				],
				'borders' => [
					'top' => [
						'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
					],
					'left' => [
						'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
					],
					'right' => [
						'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
					],
					'bottom' => [
						'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
					]
				],
				'fill' => [
					'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_GRADIENT_LINEAR,
					'rotation' => 90,
					'startColor' => [
						'argb' => '00A0A0A0',
					],
					'endColor' => [
						'argb' => 'FFFFFFFF',
					],
				],
			];
			$sheet->getStyle('A'.$index)->applyFromArray($styleArray);
			$sheet->setCellValue('A'.$index, 'Tổng cộng');
			$sheet->getStyle('D'.$index)->applyFromArray($styleArray);
			$sheet->setCellValue('D'.$index, number_format($_sum));

			$styleArray = [
				'font' => [
					'bold' => false,
				],
				'alignment' => [
					'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT,
				],
				'borders' => [
					'top' => [
						'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
					],
					'left' => [
						'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
					],
					'right' => [
						'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
					],
					'bottom' => [
						'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
					]
				],
				'fill' => [
					'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_GRADIENT_LINEAR,
					'rotation' => 90,
					'startColor' => [
						'argb' => '00A0A0A0',
					],
					'endColor' => [
						'argb' => 'FFFFFFFF',
					],
				],
			];
			$sheet->getStyle('E'.$index)->applyFromArray($styleArray);

			$sheet->setCellValue('E'.$index, number_format($_dTotal));
			// footer
			$styleArray = [
				'font' => [
					'bold' => false,
				],
				'alignment' => [
					'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
				],
				'borders' => [
					'top' => [
						'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
					],
					'left' => [
						'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
					],
					'right' => [
						'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
					],
					'bottom' => [
						'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
					]
				],
			];
			$index++;
			$sheet->mergeCells("A$index:E$index");
			$footer = "    Người nhận                                   Người giao                                               Người lập";
			$sheet->getStyle('A'.$index)->applyFromArray($styleArray);
			$sheet->setCellValue('A'.$index, $footer);

			$sheet->getPageSetup()->setPrintArea('A1:D'.$index);
			header('Content-Type: application/vnd.ms-excel'); // generate excel file
			header('Content-Disposition: attachment;filename="'. $filename .'.xlsx"');
			header('Cache-Control: max-age=0');

			$writer->save('php://output');	// download file
		}
	}

	/**
	 * Thêm mới ManhVT: hỗ trợ hiển thị danh sách công nợ, cho phép chọn từ ngày đến ngày;
	 */
	public function manage()
	{
		
		//$data['table_headers'] = get_receiving_manage_table_headers();

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
		
		$this->load->view('receivings/manage', $data);
		
	}

	public function ajax_receivings()
	{
		$this->load->model('reports/Detailed_receivings');
        $model = $this->Detailed_receivings;
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
        $headers = $this->xss_clean($model->getDataColumns1());
        if($this->Employee->has_grant('items_unitprice_hide'))
        {
            //unset();
            unset($headers['details']['cost_price']); //cost_price
            //unset($headers['details']['sub_total']); //cost_price
        }
        $report_data = $model->getData1($inputs);
        $data = null;
        if(!$report_data)
        {
            $result = 0;
        }else{
            $summary_data = array();
            $details_data = array();
            $i = 1;
            //var_dump($report_data['summary']);die();
            foreach($report_data['summary'] as $key => $row)
            {
                //var_dump($row);die();
                
                $summary_data[] = $this->xss_clean(array(
                    'receiving_id' => $i,
                    'supplier_name' => $row['company_name'],
                    'total_amount' => number_format($row['tong_cong']),
                    'paid_amount' => number_format($row['da_thanh_toan']),
                    'remain_amount' => number_format($row['con_lai']),
					'supplier_uuid' => $row['supplier_uuid']
                ));

                $i++;
            }

            $data = array(
                'headers_summary' => transform_headers_raw($headers['summary'],TRUE),
                'headers_details' => transform_headers_raw($headers['details'],TRUE),
                'summary_data' => $summary_data,
                'report_data' =>$report_data
            );

        }


        $json = array('result'=>$result,'data'=>$data);
        echo json_encode($json);
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

	public function detail($supplier_uuid='')
	{
		$data['table_headers'] = get_receiving_manage_table_headers();

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
		
		$this->load->view('receivings/detail', $data);
	}

	public function ajax_receivings_detail($supplier_uuid='')
	{
		$supplier_uuid = $this->input->get('supplier_uuid');
        $this->load->model('reports/Detailed_receivings');
        $model = $this->Detailed_receivings;
        $_sFromDate = $this->input->get('fromDate');
        $_sToDate = $this->input->get('toDate');

        $_aFromDate = explode('/', $_sFromDate);
        $_aToDate = explode('/', $_sToDate);
        $_sFromDate = $_aFromDate[2] . '/' . $_aFromDate[1] . '/' . $_aFromDate[0];
        $_sToDate = $_aToDate[2] . '/' . $_aToDate[1] . '/' . $_aToDate[0];
        $result = 1;
        $location_id = 1;
        $inputs = array('location_id'=>$location_id, 'fromDate'=>$_sFromDate,'toDate'=>$_sToDate);
        
        //var_dump($headers);
        $report_data = $model->_getDetailData1($inputs,$supplier_uuid);
        $data = null;
        if(!$report_data)
        {
            $result = 0;
        }else{
            $summary_data = array();
            $details_data = array();
            $i = 1;
            
            foreach($report_data['details'] as $drow)
            {
                //var_dump(to_currency($drow['unit_price']));die();
                  $details_data[] = $this->xss_clean(
                        [
                            'stt'=>$i,
							'receiving_id'=>$drow['receiving_id'],
                            'code'=>'<a href="'.base_url('receivings/receipt/'.$drow['receiving_uuid']).'">'.$drow['code'].'</a>',
                            'receiving_time'=>$drow['receiving_time'],
                            'total_amount'=>number_format($drow['total_amount']), 
                            'paid_amount'=>number_format($drow['paid_amount']), 
                            //'cost_price'=>to_currency($drow['cost_price']),
                            'remain_amount'=>number_format($drow['remain_amount']),  
                            'payment_type'=>$drow['payment_type'],
							'receiving_uuid'=>$drow['receiving_uuid']
                        ]);
                
						$i++;
            }
            
            
            $data = array(
                'details_data' => $details_data,
            );

        }


        $json = array('result'=>$result,'data'=>$data);
        echo json_encode($json);
	}

	public function process_payment()
	{
		$_sReceivingUUID = $this->input->post('receiving_id');
		$_fPaymentAmount = $this->input->post('paymentAmount');
		$_sPaymentMethod = $this->input->post('paymentMethod');
        $_fRemainAmount = $this->input->post('remainAmount');
		$_aPayment = [
			'payment_type'=>$_sPaymentMethod,
			'receiving_uuid'=>$_sReceivingUUID,
			'paid_amount'=>$_fPaymentAmount,
			'remain_amount'=>$_fRemainAmount
		];
		if(!is_numeric($_fPaymentAmount) || !is_numeric($_fRemainAmount) || empty($_sReceivingUUID))
		{
			$json = ['result'=>-1,'data'=>['Input data is invalid']];
        	echo json_encode($json);
		} else {
			$result = $this->Receiving->payment($_aPayment);
			$data = array(
				//'details_data' => $details_data,
			);
			$json = array('result' => $result,'data' => $data);
			echo json_encode($json);
		}
	}

	public function is_input_cost_price()
	{
		return true;
	}
}
?>
