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
			$data['amount_tendered'] = $this->input->post('amount_tendered');
			$data['amount_change'] = to_currency($data['amount_tendered'] - $data['total']);
		}
		
		$employee_id = $this->Employee->get_logged_in_employee_info()->person_id;
		$employee_info = $this->Employee->get_info($employee_id);
		$data['employee'] = $employee_info->first_name . ' ' . $employee_info->last_name;

		$supplier_info = '';
		$supplier_id = $this->receiving_lib->get_supplier();
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

		//SAVE receiving to database
		$data['receiving_id'] = 'RECV ' . $this->Receiving->save($data['cart'], $supplier_id, $employee_id, $data['comment'], $data['reference'], $data['payment_type'], $data['stock_location'], $data['purchase_id'],$data['mode']);

		$data = $this->xss_clean($data);

		if($data['receiving_id'] == 'RECV -1')
		{
			$data['error_message'] = $this->lang->line('receivings_transaction_failed');
		}
		else
		{
			$data['barcode'] = $this->barcode_lib->generate_receipt_barcode($data['receiving_id']);				
		}

		$data['print_after_sale'] = $this->receiving_lib->is_print_after_sale();

		$this->load->view("receivings/receipt",$data);

		$this->receiving_lib->clear_all();
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
			$data['receive_id'] = $receiving_id;
			$data['cart'] = $this->receiving_lib->get_cart();
			$data['total'] = $this->receiving_lib->get_total();
			$data['mode'] = $this->receiving_lib->get_mode();
			$data['receipt_title'] = $this->lang->line('receivings_receipt');
			$data['transaction_time'] = date($this->config->item('dateformat') . ' ' . $this->config->item('timeformat'), strtotime($receiving_info->receiving_time));
			$data['show_stock_locations'] = $this->Stock_location->show_locations('receivings');
			$data['payment_type'] = $receiving_info->payment_type;
			$data['reference'] = $this->receiving_lib->get_reference();
			$data['receiving_id'] = 'RECV ' . $receiving_id;
			$data['barcode'] = $this->barcode_lib->generate_receipt_barcode($data['receiving_id']);
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
		$data['items_module_allowed'] = $this->Employee->has_grant('items', $this->Employee->get_logged_in_employee_info()->person_id);
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
		
        $data['item_count'] = $this->config->item('KindOfLens');
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
			$category = $this->input->post('category');
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
				$sph = $mysphs[$key];
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


			// For Hyo
			$_strHyo =  $this->input->post('hhhyo');
			$_aaHyo = json_decode($_strHyo,true);
			foreach($_aaHyo  as $key=>$_aSPH)
			{
				$key = $key + 1;
				$sph = $hysphs[$key];
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

		if(empty($receiving_info))
		{
			$data['error_message'] = 'Không tồn tại phiếu này';
			$this->load->view("receivings/receipt", $data);
		} else {
			$this->receiving_lib->copy_entire_receiving($receiving_info);
			$data['receive_id'] = $receiving_id;
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

			$sheet->getColumnDimension('A')->setWidth(100, 'pt');
			$sheet->getColumnDimension('B')->setWidth(175, 'pt');
			$sheet->getColumnDimension('C')->setWidth(70, 'pt');
			$sheet->getColumnDimension('D')->setWidth(36, 'pt');

			$sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_PORTRAIT);
			$sheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
			$sheet->getPageSetup()->setFitToWidth(1);
			$sheet->getPageSetup()->setFitToHeight(0);
			//$sheet->getPageSetup()->setPrintArea('A1:E5');




			$writer = new Xlsx($spreadsheet); // instantiate Xlsx

			// Title
			//$title = $data['receipt_title'];


			$index = 1;
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
			//$sheet->setCellValue('A'.$index, 'STT');

			$sheet->getStyle('B'.$index)->applyFromArray($styleArray);
			$sheet->getStyle('C'.$index)->applyFromArray($styleArray);
			$sheet->getStyle('D'.$index)->applyFromArray($styleArray);



			$sheet->setCellValue('A'.$index, 'STT');
			$sheet->setCellValue('B'.$index, 'Tên sản phẩm');
			$sheet->setCellValue('C'.$index, 'Giá');
			$sheet->setCellValue('D'.$index, 'Số lượng');
			$filename = 'Yeu_cau_Nhap_Hang_'.$purchase_uuid.'_'.time(); // set filename for excel file to be exported
			// Body

			if(!empty($data['cart'])) {
				$i = 0;
				foreach($data['cart'] as $item) {
					var_dump($item);die();
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
					$sheet->getStyle('C'.$index)->applyFromArray($styleArray);
					$sheet->getStyle('D'.$index)->applyFromArray($styleArray);

					//var_dump( $item);die();
					$sheet->setCellValue('A'.$index, $i+1);
					$sheet->setCellValue('B'.$index, $item['item_name']);
					$sheet->setCellValue('C'.$index, $item['item_u_price']);
					$sheet->setCellValue('D'.$index, $item['item_quantity']);
				}
			} else {
				$sheet->setCellValue('A'.$index, 'Chưa có sản phẩm trong phiếu trả hàng');
			}

			// footer

			$sheet->getPageSetup()->setPrintArea('A1:D'.$index);
			header('Content-Type: application/vnd.ms-excel'); // generate excel file
			header('Content-Disposition: attachment;filename="'. $filename .'.xlsx"');
			header('Cache-Control: max-age=0');

			$writer->save('php://output');	// download file
		}
	}
}
?>
