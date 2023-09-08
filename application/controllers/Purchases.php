<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once("Secure_Controller.php");

//require realpath(APPPATH . '../vendor/autoload.php');

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Csv;
//use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Purchases extends Secure_Controller
{
	public function __construct()
	{
		parent::__construct('purchases');

		$this->load->library('purchase_lib');
		$this->load->library('printbarcode_lib');
		$this->load->library('receiving_lib');
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
			$this->purchase_lib->set_supplier($supplier_id);
		}

		$this->_reload();
	}

	public function change_mode()
	{
		$stock_destination = $this->input->post('stock_destination');
		$stock_source = $this->input->post('stock_source');

		if((!$stock_source || $stock_source == $this->purchase_lib->get_stock_source()) &&
			(!$stock_destination || $stock_destination == $this->purchase_lib->get_stock_destination()))
		{
			$this->purchase_lib->clear_reference();
			$mode = $this->input->post('mode');
			$this->purchase_lib->set_mode($mode);
		}
		elseif($this->Stock_location->is_allowed_location($stock_source, 'receivings'))
		{
			$this->purchase_lib->set_stock_source($stock_source);
			$this->purchase_lib->set_stock_destination($stock_destination);
		}

		$this->_reload();
	}
	
	public function set_comment()
	{
		$this->purchase_lib->set_comment($this->input->post('comment'));
	}

	public function set_print_after_sale()
	{
		$this->purchase_lib->set_print_after_sale($this->input->post('recv_print_after_sale'));
	}
	
	public function set_reference()
	{
		$this->purchase_lib->set_reference($this->input->post('recv_reference'));
	}
	
	public function add()
	{
		$data = array();
		$item_id = $this->input->post('item');
		$quantity = 1;
		//$_aItem = array();
		if(!$this->purchase_lib->add_item_by_itemID($item_id, $quantity))
		{
			$data['error'] = $this->lang->line('receivings_unable_to_add_item');
		}

		$this->_reload($data);
	}

	public function edit_item($line)
	{
		$data = array();

		$this->form_validation->set_rules('item_price', 'lang:items_price', 'required|callback_numeric');
		$this->form_validation->set_rules('item_quantity', 'lang:items_quantity', 'required|callback_numeric');
		$this->form_validation->set_rules('item_number', 'lang:items_number', 'required');
		$price = parse_decimals($this->input->post('item_price'));
		$unit_price = parse_decimals($this->input->post('item_u_price'));
		$quantity = parse_decimals($this->input->post('item_quantity'));
		$item_number = strtoupper($this->input->post('item_number'));
		$name = $this->input->post('item_name');
		$category = $this->input->post('item_category');
		$old_item_number = $this->input->post('old_item_number');
		$status = $this->input->post('item_status');
		if($this->form_validation->run() != FALSE)
		{
			//($line, $item_number, $item_name,$cost_price = 0, $quantity = 1, $category='')
			$this->purchase_lib->edit_item($line, $item_number,$name,$status, $unit_price ,$price, $quantity, $category);
			$this->purchase_lib->set_status_by_item_number($old_item_number); // set all 0
			$this->purchase_lib->set_check(0);
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
		$this->purchase_lib->delete_item($item_number);
		$this->purchase_lib->set_check(0);
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
		//$this->purchase_lib->clear_reference();
		$this->purchase_lib->remove_supplier();

		$this->_reload();
	}

	public function complete()
	{
		$purchase_id = $this->input->post('purchase_id');
		//$comment = $this->input->post('comment');
		//$data['print_after_sale'] = $this->input->post('recv_print_after_sale');
		$data = array();
		
		$data['cart'] = $this->purchase_lib->get_cart();

		$data['total'] = $this->purchase_lib->get_total();
		$data['quantity'] = $this->purchase_lib->get_quantity();
		$data['receipt_title'] = $this->lang->line('receivings_receipt');
		$data['transaction_time'] = date($this->config->item('dateformat') . ' ' . $this->config->item('timeformat'));

		//$data['comment'] = $this->purchase_lib->get_comment();
		$data['reference'] = $this->purchase_lib->get_reference();
		//$data['payment_type'] = $this->input->post('payment_type');
		//$data['show_stock_locations'] = $this->Stock_location->show_locations('receivings');
		//$data['stock_location'] = $this->purchase_lib->get_stock_source();
		/* if($this->input->post('amount_tendered') != NULL)
		{
		$data['amount_tendered'] = $this->input->post('amount_tendered');
		$data['amount_change'] = to_currency($data['amount_tendered'] - $data['total']);
		} */

		$employee_id = $this->Employee->get_logged_in_employee_info()->person_id;
		$employee_info = $this->Employee->get_info($employee_id);
		$data['employee'] = $employee_info->first_name . ' ' . $employee_info->last_name;

		$supplier_info = '';
		$supplier_id = $this->purchase_lib->get_supplier();
		if ($supplier_id != -1) {
			$supplier_info = $this->Supplier->get_info($supplier_id);
			$data['supplier'] = $supplier_info->company_name;
			$data['first_name'] = $supplier_info->first_name;
			$data['last_name'] = $supplier_info->last_name;
			$data['supplier_email'] = $supplier_info->email;
			$data['supplier_address'] = $supplier_info->address_1;
			if (!empty($supplier_info->zip) or !empty($supplier_info->city)) {
				$data['supplier_location'] = $supplier_info->zip . ' ' . $supplier_info->city;
			} else {
				$data['supplier_location'] = '';
			}
		}
		$_purchase_id = 0;
		if ($purchase_id == 0) {	
			$name = "Đơn nhập ngày " . date('d/m/Y hms', time());
			$comment = '';
			$data['reference'] = '';
			$code = 'PO' . time();
			$completed = 0;
			$data = $this->xss_clean($data);
			//SAVE PO to database
			$_purchase_id = $this->Purchase->save($data['cart'], $data['quantity'], $supplier_id, $employee_id, $name, $code, $comment, $completed);
			$data['purchase_id'] = 'POID ' . $_purchase_id;
		} else {
			$purchase_info = $this->Purchase->get_info($purchase_id)->row_array();
			$name = $purchase_info['name'];
			$comment = '';
			$data['reference'] = '';
			$code = $purchase_info['code'];
			$completed = 0;
			$data = $this->xss_clean($data);
			$_purchase_id = $this->Purchase->_save($data['cart'], $data['quantity'], $supplier_id, $employee_id, $name, $code, $comment, $completed, $purchase_id);
			$data['purchase_id'] = 'POID ' . $_purchase_id;
		}
		$data['completed'] = $completed;
		
		if ($data['purchase_id'] == 'POID -1') {
			$_purchase_id = 0;
			$data['purchase_uuid'] = 0;
			$data['error_message'] = 'Chưa lưu đơn đặt hàng thành công';
			$data['valid_cart'] = false;
		} else {
			//echo $_purchase_id;
			$purchase_info = $this->Purchase->get_info($_purchase_id)->row_array();
			//var_dump($purchase_info);
			$data['purchase_uuid'] = $purchase_info['purchase_uuid'];
			$data['barcode'] = $this->barcode_lib->generate_receipt_barcode($data['purchase_id']);
			$data['valid_cart'] = true;
		}

		$data['print_after_sale'] = 0;
		

		$this->load->view("purchase/receipt",$data);

		$this->purchase_lib->clear_all();
	}

	public function requisition_complete()
	{
		if($this->purchase_lib->get_stock_source() != $this->purchase_lib->get_stock_destination()) 
		{
			foreach($this->purchase_lib->get_cart() as $item)
			{
				$this->purchase_lib->delete_item($item['line']);
				$this->purchase_lib->add_item($item['item_id'], $item['quantity'], $this->purchase_lib->get_stock_destination());
				$this->purchase_lib->add_item($item['item_id'], -$item['quantity'], $this->purchase_lib->get_stock_source());
			}
			
			$this->complete();
		}
		else 
		{
			$data['error'] = $this->lang->line('receivings_error_requisition');

			$this->_reload($data);	
		}
	}

	function load_receipt_data($purchase_id=0)
	{
		$this->purchase_lib->clear_all();
		$purchase_info = $this->Purchase->get_info_uuid($purchase_id)->row_array();
		$this->purchase_lib->copy_entire_purchase($purchase_info['id']);

		$data['cart'] = $this->purchase_lib->get_cart();
		$data['total'] = $this->purchase_lib->get_total();
		$data['receipt_title'] = $this->lang->line('receivings_receipt');
		$data['transaction_time'] = date($this->config->item('dateformat') . ' ' . $this->config->item('timeformat'), strtotime($purchase_info['purchase_time']));
		
		$data['purchase_id'] = 'PO ' . $purchase_info['id'];
		$data['barcode'] = $this->barcode_lib->generate_receipt_barcode($data['purchase_id']);
		$employee_info = $this->Employee->get_info($purchase_info['employee_id']);
		$data['employee'] = $employee_info->first_name . ' ' . $employee_info->last_name;
		$data['completed'] = $purchase_info['completed'];
		$data['purchase_uuid'] = $purchase_id;
		$data['valid_cart'] = $this->purchase_lib->validate_cart();
		$supplier_id = $this->purchase_lib->get_supplier();
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
		$this->purchase_lib->clear_all();
		return $data;
	}
	
	public function receipt($purchase_id)
	{
		
		$data = $this->load_receipt_data($purchase_id);
		$data['print_after_sale'] = 0;

		$data = $this->xss_clean($data);
		
		$this->load->view("purchase/receipt", $data);

		$this->purchase_lib->clear_all();
	}

	private function _reload($data = array())
	{
		$data['cart'] = $this->purchase_lib->get_cart();
		$data['quantity'] = $this->purchase_lib->get_quantity();
		$data['check'] = $this->purchase_lib->get_check();
		//$data['modes'] = array('receive' => $this->lang->line('receivings_receiving'), 'return' => $this->lang->line('receivings_return'));
		//$data['mode'] = 1;
		
		$data['total'] = $this->purchase_lib->get_total();
		$data['comment'] = '';
		//$data['reference'] = $this->purchase_lib->get_reference();
		//$data['payment_options'] = $this->Receiving->get_payment_options();

		$supplier_id = $this->purchase_lib->get_supplier();
		$supplier_info = '';
		$data['purchase_id'] = $this->purchase_lib->get_purchase_id();
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
		//var_dump($data['cart']);
		$data['print_after_sale'] = 0;

		$data = $this->xss_clean($data);

		$this->load->view("purchase/purchase", $data);
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
		$this->purchase_lib->clear_all();

		$this->_reload();
	}

	public function lens()
	{
		$data = array();
		
        $data['item_count'] = $this->config->item('KindOfLens');
		$data['page_title'] = 'NHẬP MẮT KÍNH';

		$cyls = $this->config->item('cyls');
		$mysphs = $this->config->item('mysphs');
		$hysphs = $this->config->item('hysphs');
		
		$data['cyls'] = $cyls;
		$data['mysphs'] = $mysphs;
		$data['hysphs'] = $hysphs;
		$this->form_validation->set_rules('myo101', 'myo101', 'callback_number_empty');
		
		if($this->form_validation->run() == FALSE)
		{
			$this->load->view("receivings/lens", $data);
		} else {
			// Nhập sản phẩm //Mắt
			$category = $this->input->post('category');
			// Lấy tất cả tròng kính trong danh mục này;
			$_aALens = $this->Receiving->get_items_by_category($category)->result_array();
			//var_dump($_aALens);
			//echo $category;
			// For Myo
			$_aTmp = array();
			foreach($mysphs  as $key=>$sph)
			{
				if($key > 0)
				{
					foreach($cyls as $k=>$cyl)
					{
						if($k > 0)
						{
							if($k < 10)
							{
								$k = '0'.$k;
							}
							if($this->input->post('myo'.$key.$k) != "" && is_numeric($this->input->post('myo'.$key.$k)))
							{
								$_aTmp['S-'.$sph.' C-'.$cyl] = $this->input->post('myo'.$key.$k);
							}
						}	
					}
				}
			}

			// For Hyo
			foreach($hysphs  as $key=>$sph)
			{
				if($key > 0)
				{
					foreach($cyls as $k=>$cyl)
					{
						if($k > 0)
						{
							if($k < 10)
							{
								$k = '0'.$k;
							}
							if($this->input->post('hyo'.$key.$k) != "" && is_numeric($this->input->post('hyo'.$key.$k)))
							{
								$_aTmp['S+'.$sph.' C-'.$cyl] = $this->input->post('hyo'.$key.$k);
							}
						}	
					}
				}
			}
			//var_dump($_aTmp);die();
			if(!empty($_aTmp))
			{
				$this->purchase_lib->clear_all();
				foreach($_aTmp as $key=>$value)
				{
					foreach($_aALens as $k=>$v)
					{
						if($key == substr($v['name'],-13))
						{
							//$this->purchase_lib->add_item($item_id, $quantity, $item_location);
							$this->purchase_lib->add_item($v['item_id'], trim($value), 1);
						}
						
					}
				}

				//$_aCart = $this->purchase_lib->get_cart();
				redirect('receivings/');
			} else{
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

	public function excel_import()
	{
		$this->load->view('purchase/form_excel_import', NULL);
	}
	public function excel()
	{
		$name = 'form_order_.xlsx';
		$data = file_get_contents('../' . $name);
		force_download($name, $data);
	}
	public function do_excel_import()
	{
		
		$this->load->helper('file');

        /* Allowed MIME(s) File */
        $file_mimes = array(
            'application/octet-stream', 
            'application/vnd.ms-excel', 
            'application/x-csv', 
            'text/x-csv', 
            'text/csv', 
            'application/csv', 
            'application/excel', 
            'application/vnd.msexcel', 
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
        );
		//var_dump(extension_loaded('zip'));
		if(!isset($_FILES['file_path']) && $_FILES['file_path']['error'] != UPLOAD_ERR_OK )
		{
			echo json_encode(array('success' => FALSE, 'message' => $this->lang->line('items_excel_import_failed')));
		}
		else
		{	
			$array_file = explode('.', $_FILES['file_path']['name']);
            $extension  = end($array_file);
            if('csv' == $extension) {
                $reader = new Csv();
            } else {
                $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
            }

            $spreadsheet = $reader->load($_FILES['file_path']['tmp_name']);
            $sheet_data  = $spreadsheet->getActiveSheet(0)->toArray();
			$worksheet = $spreadsheet->getActiveSheet(0);
			//var_dump($worksheet);
            $array_data  = [];
			$highestColumn = 6;
			$_blFlag = true;
			$_iMaxColumn = 0;

			foreach($sheet_data[0] as $item)
			{
				if($item != null)
				{
					$_iMaxColumn++;

				} else {
					break;
				}
			}
			//echo $_iMaxColumn; die();
			if ($_iMaxColumn == 5) // Chỉ xử  lý định dạng 5 cột; không có barcode tự sinh bacode
			{
				$_iLogedUserID = $this->Employee->get_logged_in_employee_info()->person_id;
				$_oLogedUser = $this->Employee->get_info($_iLogedUserID);
				$_sLastBarcode = $_oLogedUser->log;

				$_sLastDate = substr($_sLastBarcode, 0, 6);
				$_sDate = date('dmy'); //070223 - 07.02.23
				$_iBegin = 0; // Số bắt đầu chạy
				$_iWork = 0; 
				if($_sLastDate == $_sDate)
				{
					$_iBegin = intval(substr($_sLastBarcode, 6, 9));
				}
				//$_iWork = $_iBegin;
				$_sBarcode = '';
				for($i = 1; $i < count($sheet_data); $i++) {
					//$rowData = $sheet->rangeToArray('A' . $i . ':' . $highestColumn . $i,NULL,TRUE,FALSE);
					
					if(isEmptyRow($sheet_data[$i],$highestColumn)) { continue; } // skip empty row
					$_iWork++;
					$_sBarcode = $_sDate.sprintf('%04d',$_iBegin + $i);
					$data = array(
						'item_number'       => $_sBarcode,
						'item_name'      => $sheet_data[$i]['0'],
						'category'        => $sheet_data[$i]['1'],
						'unit_price' => extract_price_excel_to_vnd($sheet_data[$i]['2']),//Giá bán
						'cost_price'=> extract_price_excel_to_vnd($sheet_data[$i]['3']), //Giá nhập
						'quanlity' => $sheet_data[$i]['4']
					);
					
					$this->purchase_lib->add_item($data);
					$array_data[] = $data;
				}
				$_iEnd = $_iBegin + $_iWork;

				$this->Employee->update_employee($_sBarcode, $_iLogedUserID);
				//var_dump($array_data);
				$this->purchase_lib->set_check(0); //reset lại biến kiểm tra; cần phải kiểm tra. nhấn nút kiểm tra;
				echo json_encode(array('success' => TRUE, 'message' => $this->lang->line('items_excel_import_success')));
			}
			elseif($_iMaxColumn == 6) // Xử lý định dạng 6 cột
			{
				for($i = 1; $i < count($sheet_data); $i++) {
					//$rowData = $sheet->rangeToArray('A' . $i . ':' . $highestColumn . $i,NULL,TRUE,FALSE);
					if(isEmptyRow($sheet_data[$i],$highestColumn)) { continue; } // skip empty row
		
					$_quanlity = 0;
					if(is_numeric($sheet_data[$i]['5']))
					{
						$_quanlity = (int ) $sheet_data[$i]['5'];
					}
					$data = array(
						'item_number'       => $sheet_data[$i]['0'],
						'item_name'      => $sheet_data[$i]['1'],
						'category'        => $sheet_data[$i]['2'],
						'unit_price' => extract_price_excel_to_vnd($sheet_data[$i]['3']),//Giá bán
						'cost_price'=> extract_price_excel_to_vnd($sheet_data[$i]['4']), //Giá nhập
						'quanlity' => $_quanlity
					);
					if(!$this->Item->item_number_exists($data['item_number']))
					{
						$this->purchase_lib->add_item($data);
					} else {
						$this->purchase_lib->add_item_by_itemID($data['item_number'],$data['quanlity']);
					}
					$array_data[] = $data;
				}
				//var_dump($array_data);
				$this->purchase_lib->set_check(0); //reset lại biến kiểm tra; cần phải kiểm tra. nhấn nút kiểm tra;
				echo json_encode(array('success' => TRUE, 'message' => $this->lang->line('items_excel_import_success')));

			} else {
				$_blFlag = false;
				$this->purchase_lib->set_check(0); //reset lại biến kiểm tra; cần phải kiểm tra. nhấn nút kiểm tra;
				echo json_encode(array('success' => FALSE, 'message' => 'Kiểm tra lại, file không đúng định dạng yêu cầu'));
			}
			
            

		}
	}

	public function check_purchase()
	{
		$cart = $this->purchase_lib->get_cart();
		$_aIN = array();
		if(!empty($cart))
		{
			/*
			var_dump($cart);
			$cart = check_dup($cart,'item_number');
			var_dump($cart);
			*/
			foreach($cart as $k=>$v	) //remove all items get from products
			{
				if($v['status'] == 9)
				{
					unset($cart[$k]);
				} else{
					$_aIN[] = $v['item_number'];
				}
			}

			if(!empty($_aIN))
			{
				$_aItem = $this->Item->get_items_in_cart($_aIN);
				if(!empty($_aItem))
				{
					$_cart = $this->purchase_lib->get_cart();
					foreach ($_aItem as $_k => $_v) {
						foreach ($_cart as $k => $v) {
							if ($v['status'] != 9) { // Nếu không phải sản phẩm kiểm tra sự tồn tại barcode
								if ($v['item_number'] == $_v->item_number) {
									$_aTheItem = &$_cart[$k];
									$_aTheItem['status'] = 7;
								}
							}
						}
					}
					check_dup($_cart,'item_number');
					$this->purchase_lib->set_cart($_cart);
					echo json_encode(array('success' => TRUE, 'message' => 'Kiểm tra thành công'));
				} else{
					$_cart = $this->purchase_lib->get_cart();
					foreach ($_cart as $k => $v) {
						if ($v['status'] == 7 || $v['status'] == 6) {
							$_aTheItem = &$_cart[$k];
							$_aTheItem['status'] = 0;
						}
					}
					if(check_dup($_cart,'item_number') == false) //Duplicate
					{
						$this->purchase_lib->set_cart($_cart);
						$this->purchase_lib->set_check(0); // đã kiểm tra hợp lê
						echo json_encode(array('success' => TRUE, 'message' => 'Trùng barcode'));
					} else {
						$this->purchase_lib->set_cart($_cart);
						$this->purchase_lib->set_check(1); // đã kiểm tra hợp lê
						echo json_encode(array('success' => TRUE, 'message' => 'Các barcode hợp lệ'));
					}
				}
				
			} else {
				$this->purchase_lib->set_check(1); // đã kiểm tra hợp lê
				echo json_encode(array('success' => TRUE, 'message' => 'Các barcode hợp lệ'));
			}
		}

	}

	public function manage()
	{		
		$data['table_headers'] = get_purchases_manage_table_headers();

		// filters that will be loaded in the multiselect dropdown
		
		$data['filters'] = array('new' => 'Mới tạo',
									'edit'=>'Đã chỉnh sửa',
									'cancel'=>'Yêu cầu sửa',
									'waiting'=>'Yêu cầu duyệt',
									'approved'=>'Đã duyệt',
									'imported'=>'Nhập kho'
									);		

		if ($this->Employee->has_grant('purchases_index')) {
			$data['is_created'] = 1;
		} else {
			$data['is_created'] = 0;
		}
		
		$this->load->view('purchase/manage', $data);
		
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
						'new' => FALSE,
						'cancel' => FALSE,
						);

		// check if any filter is set in the multiselect dropdown
		if($this->input->get('filters') == null)
		{
			echo 'Invalid Data';
			exit();
		}
		$filledup = array_fill_keys($this->input->get('filters'), TRUE);
		$filters = array_merge($filters, $filledup);
		if ($sort == '')
			$sort = 'purchase_time';

		$purchases = $this->Purchase->search($search, $filters, $limit, $offset, $sort, $order);
		$total_rows = $this->Purchase->get_found_rows($search, $filters);
		//$sales = $this->Sale->search($search, $filters, $limit, $offset, $sort, $order, $this->logedUser_type, $this->logedUser_id);
		//$total_rows = $this->Sale->get_found_rows($search, $filters, $this->logedUser_type, $this->logedUser_id);		
		$data_rows = array();
		foreach($purchases->result() as $item)
		{
			$data_rows[] = $this->xss_clean(get_purchase_data_row($item));
		}
		echo json_encode(array('total' => $total_rows, 'rows' => $data_rows));
	}

	public function send()
	{
		$purchase_uuid = $this->input->post('purchase_uuid');
		$purchase_info = $this->Purchase->get_info_uuid($purchase_uuid)->row_array();
		if(!empty($purchase_info))
		{
			//$purchase_info['completed'] = 2; // Đã gửi, đang chờ phê duyệt
			$data_update['completed'] = 2;
			$this->Purchase->update($data_update, $purchase_info['id']);
			redirect(base_url("purchases/receipt/$purchase_uuid"));
		}
	}

	public function approve()
	{
		$purchase_uuid = $this->input->post('purchase_uuid');
		$purchase_info = $this->Purchase->get_info_uuid($purchase_uuid)->row_array();
		if(!empty($purchase_info))
		{
			//$purchase_info['completed'] = 2; // Đã gửi, đang chờ phê duyệt
			$data_update['completed'] = 3;
			$this->Purchase->update($data_update, $purchase_info['id']);
			redirect(base_url("purchases/receipt/$purchase_uuid"));
		}
	}

	public function cancel()
	{
		$purchase_uuid = $this->input->post('purchase_uuid');
		$purchase_info = $this->Purchase->get_info_uuid($purchase_uuid)->row_array();
		if(!empty($purchase_info))
		{
			//$purchase_info['completed'] = 2; // Đã gửi, đang chờ phê duyệt
			$data_update['completed'] = 1;
			$this->Purchase->update($data_update, $purchase_info['id']);
			redirect(base_url("purchases/receipt/$purchase_uuid"));
		}
	}
	public function import() // Nhập hàng vào kho, chuyển đến chức năng nhập kho
	{
		$purchase_uuid = $this->input->post('purchase_uuid');
		$purchase_info = $this->Purchase->get_info_uuid($purchase_uuid)->row_array();
		if(!empty($purchase_info))
		{
			$this->receiving_lib->clear_all();
			//$purchase_info['completed'] = 2; // Đã gửi, đang chờ phê duyệt
			$_aThePurchase = $this->Purchase->the_purchase($purchase_info['id']);
			$this->receiving_lib->set_purchase_id($purchase_info['id']);
			foreach($_aThePurchase['items'] as $item)
			{
				if(is_numeric($item['item_quantity'])){
					$item_quantity = (int) $item['item_quantity'];
				} else {
					$item_quantity = 0;
				}
				$this->receiving_lib->add_item($item['item_id'],$item_quantity,1);
			}
			
			redirect(base_url("receivings"));
		}
	}

	public function editpurchase($sdf='',$uuid='')
	{
		$uuid = $this->input->get('purchase_uuid');
		$this->purchase_lib->clear_all(); // The first, to clear all things the the cart before load data into the cart;
		$purchase_id = $uuid;
		$purchase_info = $this->Purchase->get_info_uuid($purchase_id)->row_array();
		//var_dump($purchase_info);
		// to load data to the cart;
		$this->purchase_lib->set_purchase_id($purchase_info['id']);
		$this->purchase_lib->copy_entire_purchase($purchase_info['id']);

		$data['cart'] = $this->purchase_lib->get_cart();
		$data['total'] = $this->purchase_lib->get_total();
		$data['receipt_title'] = $this->lang->line('receivings_receipt');
		$data['transaction_time'] = date($this->config->item('dateformat') . ' ' . $this->config->item('timeformat'), strtotime($purchase_info['purchase_time']));
		
		$data['purchase_id'] = 'PO ' . $purchase_info['id'];
		$data['barcode'] = $this->barcode_lib->generate_receipt_barcode($data['purchase_id']);
		$employee_info = $this->Employee->get_info($purchase_info['employee_id']);
		$data['employee'] = $employee_info->first_name . ' ' . $employee_info->last_name;
		$data['completed'] = $purchase_info['completed'];
		$data['purchase_uuid'] = $purchase_id;
		$data['valid_cart'] = $this->purchase_lib->validate_cart();
		$supplier_id = $this->purchase_lib->get_supplier();
		$data['quantity'] = $this->purchase_lib->get_quantity();
		$data['check'] = $this->purchase_lib->get_check();
		$data['comment'] = '';
		$data['purchase_id'] = $this->purchase_lib->get_purchase_id();
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
		//var_dump($data['cart']); 
		//$this->purchase_lib->clear_all();
		$data['print_after_sale'] = 0;

		$data = $this->xss_clean($data);
		
		$this->load->view("purchase/purchase", $data);
	}

	public function printbarcode()
	{
		$purchase_uuid = $this->input->post('purchase_uuid');
		$purchase_info = $this->Purchase->get_info_uuid($purchase_uuid)->row_array();
		if (!empty($purchase_info)) {
			
			//var_dump($purchase_info);
			// to load data to the cart;
			$this->printbarcode_lib->clear_all();
			$this->purchase_lib->set_purchase_id($purchase_info['id']);
			$this->purchase_lib->copy_entire_purchase($purchase_info['id']);

			$data['cart'] = $this->purchase_lib->get_cart();
			//var_dump($data['cart']);
			foreach($data['cart'] as $item)
			{
				$this->printbarcode_lib->add_item($item['item_id'], $item['item_quantity']);
			}
			$this->purchase_lib->clear_all();
			redirect(base_url('barcodes'));

		} else{
			echo 'Bạn đang truy cập không hợp lệ';
		}
	}

	public function export($purchase_uuid=0)
	{
		$purchase_uuid = $this->input->get('purchase_uuid');
		$data = $this->load_receipt_data($purchase_uuid);
		//var_dump($data);
		$spreadsheet = new Spreadsheet(); // instantiate Spreadsheet
		$spreadsheet->getDefaultStyle()->getFont()->setName('Arial');
        $sheet = $spreadsheet->getActiveSheet();

		/**
		 * Thiết lập độ rộng các cột
		 */

		$sheet->getColumnDimension('A')->setWidth(24,'pt');
		$sheet->getColumnDimension('B')->setWidth(75,'pt');
		$sheet->getColumnDimension('C')->setWidth(250,'pt');
		$sheet->getColumnDimension('D')->setWidth(36,'pt');
		$sheet->getColumnDimension('E')->setWidth(46,'pt');
		
		$sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_PORTRAIT);
		$sheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
		$sheet->getPageSetup()->setFitToWidth(1);
		$sheet->getPageSetup()->setFitToHeight(0);
		//$sheet->getPageSetup()->setPrintArea('A1:E5');




        $writer = new Xlsx($spreadsheet); // instantiate Xlsx

		// Title
		//$title = $data['receipt_title'];
		$title = 'PHIẾU NHẬP HÀNG';
		$date = $data['transaction_time'];
		$employee = $data['employee'];
		$supplier = 'Nhà cung cấp: '.$data['supplier'];
		$supplier_add = $data['supplier_address'];
		$barcode = $data['barcode'];

		
                
                

		$company_name = $this->config->item('company');
		$company_add = $this->config->item('address');
		$company_phone = $this->config->item('phone');

		$top_title1="CỘNG HÒA XÃ HỘI CHỦ NGHĨA VIỆT NAM";
		$top_title2="Độc lập - Tự do - Hạnh Phúc";
		
		$index = 2;
		$styleArray = [
			'font' => [
				'bold' => false,
			],
			'alignment' => [
				'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
			],
			
		];
		$sheet->mergeCells('A'.$index.':B'.$index);
		$sheet->getStyle('A'.$index)->applyFromArray($styleArray);
		$sheet->setCellValue('A'.$index, $company_name); 

		$sheet->mergeCells('C'.$index.':E'.$index);
		$sheet->getStyle('C'.$index)->applyFromArray($styleArray);
		$sheet->setCellValue('C'.$index, $top_title1); 

		$index++;
		$sheet->mergeCells('A'.$index.':B'.$index);
		$sheet->getStyle('A'.$index)->applyFromArray($styleArray);
		$sheet->setCellValue('A'.$index, $company_add); 

		$sheet->mergeCells('C'.$index.':E'.$index);
		$sheet->getStyle('C'.$index)->applyFromArray($styleArray);
		$sheet->setCellValue('C'.$index, $top_title2);
		$index++;
		$sheet->mergeCells('A'.$index.':B'.$index);
		$sheet->getStyle('A'.$index)->applyFromArray($styleArray);
		$sheet->setCellValue('A'.$index, $company_phone);

		$sheet->mergeCells('C'.$index.':E'.$index);

	

		$index = $index + 2;
		$sheet->mergeCells('A'.$index.':E'.$index);
		$styleArray = [
			'font' => [
				'bold' => true,
			],
			'alignment' => [
				'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
			],
			'fill' => [
				'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_GRADIENT_LINEAR,
				'rotation' => 90,
				'startColor' => [
					'argb' => 'FFA0A0A0',
				],
				'endColor' => [
					'argb' => 'FFFFFFFF',
				],
			],
		];
		
		$sheet->getStyle('A'.$index)->applyFromArray($styleArray);
		$sheet->setCellValue('A'.$index, strtoupper($title)); 
		$index++;
		$sheet->setCellValue('A'.$index, $date);

		$sheet->mergeCells('C'.$index.':E'.$index);
		$styleArray = [
			'font' => [
				'bold' => false,
			],
			'alignment' => [
				'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
			],
		];
		
		$sheet->getStyle('C'.$index)->applyFromArray($styleArray);
		$sheet->setCellValue('C'.$index, $supplier);

		$index++;
		$sheet->mergeCells('C'.$index.':E'.$index);
		$styleArray = [
			'font' => [
				'bold' => false,
			],
			'alignment' => [
				'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
			],
		];
		
		$sheet->getStyle('C'.$index)->applyFromArray($styleArray);
		//$sheet->setCellValue('C'.$index, $supplier);
		// Header ---
		$index++;
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
				'left'=>[
					'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
				],
				'right'=>[
					'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
				],
				'bottom'=>[
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
		$sheet->setCellValue('A'.$index, 'STT'); 

		$sheet->getStyle('B'.$index)->applyFromArray($styleArray);
		$sheet->getStyle('E'.$index)->applyFromArray($styleArray);
		$sheet->getStyle('C'.$index)->applyFromArray($styleArray);
		$sheet->getStyle('D'.$index)->applyFromArray($styleArray);

		

		$sheet->setCellValue('B'.$index, 'Mã sản phẩm');
		$sheet->setCellValue('C'.$index, 'Tên sản phẩm');
		$sheet->setCellValue('D'.$index, 'ĐVT'); 
		$sheet->setCellValue('E'.$index, 'Số lượng'); 
        $filename = 'Yeu_cau_Nhap_Hang'.time(); // set filename for excel file to be exported
		// Body
		
		if(!empty($data['cart']))
		{	$i = 0;
			foreach($data['cart'] as $item)
			{
				$index++;
				$i++;
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
						'left'=>[
							'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
						],
						'right'=>[
							'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
						],
						'bottom'=>[
							'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
						]
					],
				];
				$sheet->getStyle('A'.$index)->applyFromArray($styleArray);
				$sheet->getStyle('B'.$index)->applyFromArray($styleArray);
				$sheet->getStyle('E'.$index)->applyFromArray($styleArray);

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
						'left'=>[
							'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
						],
						'right'=>[
							'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
						],
						'bottom'=>[
							'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
						]
					],
				];
				$sheet->getStyle('C'.$index)->applyFromArray($styleArray);
				$sheet->getStyle('D'.$index)->applyFromArray($styleArray);

				$sheet->setCellValue('A'.$index, $i); 
				$sheet->setCellValue('B'.$index, $item['item_number']);
				$sheet->setCellValue('C'.$index, $item['item_name']);
				$sheet->setCellValue('D'.$index, 'Miếng'); 
				$sheet->setCellValue('E'.$index, $item['item_quantity']);
			}
		} else {
			$sheet->setCellValue('A'.$index, 'Chưa có sản phẩm trong yêu cầu đặt hàng'); 
		}

		// footer
		$index++;
		$index++;
		$sheet->mergeCells('C'.$index.':E'.$index);
		$styleArray = [
			'font' => [
				'bold' => false,
			],
			'alignment' => [
				'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
			],
		];
		
		$sheet->getStyle('C'.$index)->applyFromArray($styleArray);
		$sheet->setCellValue('C'.$index, 'Người lập phiếu'); 

		$index++;
		$sheet->mergeCells('C'.$index.':E'.$index);
		$styleArray = [
			'font' => [
				'bold' => false,
			],
			'alignment' => [
				'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
			],
		];
		
		$sheet->getStyle('C'.$index)->applyFromArray($styleArray);
		$sheet->setCellValue('C'.$index, $employee); 
 
		$sheet->getPageSetup()->setPrintArea('A1:E'.$index);

        header('Content-Type: application/vnd.ms-excel'); // generate excel file
        header('Content-Disposition: attachment;filename="'. $filename .'.xlsx"'); 
        header('Cache-Control: max-age=0');
        
        $writer->save('php://output');	// download file 
	}

	public function barcode_export($purchase_uuid=0)
	{
		$purchase_uuid = $this->input->get('purchase_uuid');
		$data = $this->load_receipt_data($purchase_uuid);
		//var_dump($data);
		$spreadsheet = new Spreadsheet(); // instantiate Spreadsheet
		$spreadsheet->getDefaultStyle()->getFont()->setName('Arial');
        $sheet = $spreadsheet->getActiveSheet();

		/**
		 * Thiết lập độ rộng các cột
		 */

		$sheet->getColumnDimension('A')->setWidth(24,'pt');
		$sheet->getColumnDimension('B')->setWidth(75,'pt');
		$sheet->getColumnDimension('C')->setWidth(250,'pt');
		$sheet->getColumnDimension('D')->setWidth(36,'pt');
		$sheet->getColumnDimension('E')->setWidth(46,'pt');
		
		$sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_PORTRAIT);
		$sheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
		$sheet->getPageSetup()->setFitToWidth(1);
		$sheet->getPageSetup()->setFitToHeight(0);
		//$sheet->getPageSetup()->setPrintArea('A1:E5');




        $writer = new Xlsx($spreadsheet); // instantiate Xlsx

		// Title
		//$title = $data['receipt_title'];
	
		$employee = $data['employee'];
		
		$index = 1;
		$styleArray = [
			'font' => [
				'bold' => false,
			],
			'alignment' => [
				'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
			],
			
		];
	
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
				'left'=>[
					'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
				],
				'right'=>[
					'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
				],
				'bottom'=>[
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
		//$sheet->setCellValue('A'.$index, 'STT'); 

		$sheet->getStyle('B'.$index)->applyFromArray($styleArray);
		$sheet->getStyle('C'.$index)->applyFromArray($styleArray);
		$sheet->getStyle('D'.$index)->applyFromArray($styleArray);

		

		$sheet->setCellValue('A'.$index, 'Barcode');
		$sheet->setCellValue('B'.$index, 'Tên sản phẩm');
		$sheet->setCellValue('C'.$index, 'Giá');
		$sheet->setCellValue('D'.$index, 'Số lượng');
        $filename = 'Yeu_cau_Nhap_Hang_'.$purchase_uuid.'_'.time(); // set filename for excel file to be exported
		// Body
		
		if(!empty($data['cart']))
		{	$i = 0;
			foreach($data['cart'] as $item)
			{
				$index++;
				$i++;
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
						'left'=>[
							'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
						],
						'right'=>[
							'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
						],
						'bottom'=>[
							'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
						]
					],
				];
				$sheet->getStyle('A'.$index)->applyFromArray($styleArray);
				$sheet->getStyle('B'.$index)->applyFromArray($styleArray);
				$sheet->getStyle('E'.$index)->applyFromArray($styleArray);

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
						'left'=>[
							'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
						],
						'right'=>[
							'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
						],
						'bottom'=>[
							'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
						]
					],
				];
				$sheet->getStyle('C'.$index)->applyFromArray($styleArray);
				$sheet->getStyle('D'.$index)->applyFromArray($styleArray);

				
				$sheet->setCellValue('A'.$index, $item['item_number']);
				$sheet->setCellValue('B'.$index, $item['item_name']);
				$sheet->setCellValue('D'.$index, $item['unit_price']); 
				$sheet->setCellValue('C'.$index, $item['item_quantity']);
			}
		} else {
			$sheet->setCellValue('A'.$index, 'Chưa có sản phẩm trong yêu cầu đặt hàng'); 
		}

		// footer
 
		$sheet->getPageSetup()->setPrintArea('A1:D'.$index);
        header('Content-Type: application/vnd.ms-excel'); // generate excel file
        header('Content-Disposition: attachment;filename="'. $filename .'.xlsx"'); 
        header('Cache-Control: max-age=0');
        
        $writer->save('php://output');	// download file 
	}

}
?>
