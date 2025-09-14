<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Quản lý tài liệu kiểm kê
 * By ManhVT - manhvt89@gmail.com - 0936111917
 * Dated: 10/08/2024
 * Version 1.0
 */

require_once("Secure_Controller.php");

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Csv;
//use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
class Oincs extends Secure_Controller
{
	
	public function __construct()
	{
		parent::__construct('oincs');
		$this->load->library('item_lib');
		$this->load->library('count_lib');

	}
	
	public function index()
	{

		$data['table_headers'] = $this->xss_clean(get_oincs_manage_table_headers());

		//$data['table_headers'] = $this->xss_clean(get_items_manage_table_headers());

		
		$data['stock_location'] = $this->xss_clean($this->item_lib->get_item_location());
		$data['stock_locations'] = $this->xss_clean($this->Stock_location->get_allowed_locations());

		// filters that will be loaded in the multiselect dropdown
		$data['filters'] = [
							'O' => $this->lang->line('oincs_open_status'),
							'W' => $this->lang->line('oincs_work_status'),
							'C' => $this->lang->line('oincs_close_status'),
							'P' => $this->lang->line('oincs_post_status'),
		];

		$data['hide_unitprice'] = $this->Employee->has_grant('items_unitprice_hide');
		$this->load->view('oincs/manage', $data);
	}

	/*
	Returns Items table data rows. This will be called with AJAX.
	*/
	public function search()
	{
		$search = $this->input->get('search');
		$limit = $this->input->get('limit');
		$offset = $this->input->get('offset');
		$sort = $this->input->get('sort');
		$order = $this->input->get('order');

        //$search = str_replace(' ','%',$search);
		$this->item_lib->set_item_location($this->input->get('stock_location'));

		$filters = array('start_date' => $this->input->get('start_date'),
						'end_date' => $this->input->get('end_date'),
						'O' => FALSE,
						'W' => FALSE, 
						'C' => FALSE,
						'P' => FALSE,
						'search_custom' => FALSE,
						'is_deleted' => FALSE);
		
		// check if any filter is set in the multiselect dropdown
		$filledup = array_fill_keys($this->input->get('filters'), TRUE);
		$filters = array_merge($filters, $filledup);

		//var_dump($filters);die();

		$items = $this->Oinc->search($search, $filters, $limit, $offset, $sort, $order);
		$total_rows = $this->Oinc->get_found_rows($search, $filters);

		$data_rows = array();
		foreach($items->result() as $item)
		{
			$data_rows[] = $this->xss_clean(get_oinc_data_row($item, $this));
		}

		echo json_encode(array('total' => $total_rows, 'rows' => $data_rows));
	}
	
	
	/*
	Gives search suggestions based on what is being searched for
	*/
	public function suggest_search()
	{
		$suggestions = $this->xss_clean($this->Item->get_search_suggestions($this->input->post_get('term'),
			array('search_custom' => $this->input->post('search_custom'), 'is_deleted' => $this->input->post('is_deleted') != NULL), FALSE));

		echo json_encode($suggestions);
	}

	public function suggest()
	{
		$suggestions = $this->xss_clean($this->Item->get_search_suggestions($this->input->post_get('term'),
			array('search_custom' => FALSE, 'is_deleted' => FALSE), TRUE));

		echo json_encode($suggestions);
	}

	/*
	Gives search suggestions based on what is being searched for
	*/
	public function suggest_category()
	{
		$suggestions = $this->xss_clean($this->Item->get_category_suggestions($this->input->get('term')));

		echo json_encode($suggestions);
	}

	/*
	 Gives search suggestions based on what is being searched for
	*/
	public function suggest_location()
	{
		$suggestions = $this->xss_clean($this->Item->get_location_suggestions($this->input->get('term')));

		echo json_encode($suggestions);
	}
	
	/*
	 Gives search suggestions based on what is being searched for
	*/
	public function suggest_custom()
	{
		$suggestions = $this->xss_clean($this->Item->get_custom_suggestions($this->input->post('term'), $this->input->post('field_no')));

		echo json_encode($suggestions);
	}

	public function get_row($item_ids='')
	{
		if($item_ids == '')
		{
			echo 'Invalid Data';
			exit();
		}
		$item_infos = $this->Item->get_multiple_info(explode(":", $item_ids), $this->item_lib->get_item_location());

		$result = array();
		foreach($item_infos->result() as $item_info)
		{
			$result[$item_info->item_id] = $this->xss_clean(get_item_data_row($item_info, $this));
		}

		echo json_encode($result);
	}

	public function view($obj_id = -1)
	{
		
		//$person_id = $this->session->userdata('person_id');
		$data = [];
		$_iTime = time();
		$data['lens'] = $this->config->item('iKindOfLens');
		$data['contact_lens'] = $this->config->item('filter_contact_lens');
		$data['frame'] = $this->config->item('filter');
		$data['sun_glasses'] = $this->config->item('filter_sun_glasses');
		
		$data['medicines'] = $this->config->item('filter_other'); 
		//var_dump($data['medicines']);die();
		/*$_oObj_info = $this->Item->get_info($obj_id);
		foreach(get_object_vars($_oObj_info) as $property => $value)
		{
			$_oObj_info->$property = $this->xss_clean($value);
		}
		$data['item_info'] = $_oObj_info;
		*/
		$this->load->view('oincs/form', $data);
	}
    
	public function inventory($item_id = -1)
	{
		$item_info = $this->Item->get_info($item_id);
		foreach(get_object_vars($item_info) as $property => $value)
		{
			$item_info->$property = $this->xss_clean($value);
		}
		$data['item_info'] = $item_info;

        $data['stock_locations'] = array();
        $stock_locations = $this->Stock_location->get_undeleted_all()->result_array();
        foreach($stock_locations as $location)
        {
			$location = $this->xss_clean($location);
			$quantity = $this->xss_clean($this->Item_quantity->get_item_quantity($item_id, $location['location_id'])->quantity);
		
            $data['stock_locations'][$location['location_id']] = $location['location_name'];
            $data['item_quantities'][$location['location_id']] = $quantity;
        }

		$this->load->view('items/form_inventory', $data);
	}
	
	public function count_details($item_id = -1)
	{
		$item_info = $this->Item->get_info($item_id);
		foreach(get_object_vars($item_info) as $property => $value)
		{
			$item_info->$property = $this->xss_clean($value);
		}
		$data['item_info'] = $item_info;

        $data['stock_locations'] = array();
        $stock_locations = $this->Stock_location->get_undeleted_all()->result_array();
        foreach($stock_locations as $location)
        {
			$location = $this->xss_clean($location);
			$quantity = $this->xss_clean($this->Item_quantity->get_item_quantity($item_id, $location['location_id'])->quantity);
		
            $data['stock_locations'][$location['location_id']] = $location['location_name'];
            $data['item_quantities'][$location['location_id']] = $quantity;
        }

		$this->load->view('items/form_count_details', $data);
	}
	/**
	 * Luu phiếu kiểm kê
	 * @param mixed $item_id
	 * @return never
	 */
	public function save($item_id = -1)
	{
		
		$_iTime = time();
		//Save item data
		$_person_id = $this->session->userdata('person_id');
		$_oTheUser = $this->session->userdata('theUser');
	
		$_sMode = $this->input->post('mode');
		$_aCategories = $this->input->post('items');
		
		$_aItem_data = [];
		$_aaItem_data = [];
		$_aItem_data['created_at'] = $_iTime;
		$_aItem_data['oinc_mode'] = $_sMode;
		$_aItem_data['status'] = 'O';
		
		//Người tạo phiếu kiểm kê
		$_aItem_data['creator_id'] = $_person_id;
		$_aItem_data['creator_name'] = $_oTheUser->last_name . ' '. $_oTheUser->first_name;
		
		// Chưa thực hiện kiểm kê
		$_aItem_data['countor_id'] = 'O';
		$_aItem_data['countor_name'] = '';

		if($_sMode == 'A')
		{
			if(!empty($_aCategories))
			{
				/*
				foreach($_aCagories as $item){
					$_aItem_data['oinc_type'] = '';
					if($item == 'lens')
					{
						$_aItem_data['oinc_type'] = 'L';
						// Load danh sách chủng loại mắt;
						$_aCats = $this->config->item('iKindOfLens');
						if(!empty($_aCats))
						{
							foreach($_aCats as $k=>$v)
							{
								$_aItem_data['doc_entry'] = 'CL'.$_aItem_data['oinc_type'].$_iTime.'-'.$k;
								$_aItem_data['doc_num'] = 'L-'.date('Y-m-d-h-m-s',$_iTime).'-'.$k;
								$_aItem_data['zone'] = $v;
								$_aaItem_data[] = $_aItem_data;
							}
						}
						
					} 
					elseif($item == 'frame')
					{
						$_aItem_data['oinc_type'] = 'F';
						// Load sanh sách chủng loại gọng
						$_aCats = $this->config->item('filter');
						if(!empty($_aCats))
						{
							foreach($_aCats as $k=>$v)
							{
								$_aItem_data['doc_entry'] = 'CF'.$_aItem_data['oinc_type'].$_iTime.'-'.$k;
								$_aItem_data['doc_num'] = 'F-'.date('Y-m-d-h-m-s',$_iTime).'-'.$k;
								$_aItem_data['zone'] = $v;
								$_aaItem_data[] = $_aItem_data;
							}
						}
					}
					elseif($item == 'medicine')
					{
						$_aItem_data['oinc_type'] = 'M';
						//Load danh sách chủng loại thuốc
						$_aCats = $this->config->item('filter_other');
						if(!empty($_aCats))
						{
							foreach($_aCats as $k=>$v)
							{
								$_aItem_data['doc_entry'] = 'CM'.$_aItem_data['oinc_type'].$_iTime.'-'.$k;
								$_aItem_data['doc_num'] = 'M-'.date('Y-m-d-h-m-s',$_iTime).'-'.$k;
								$_aItem_data['zone'] = $v;
								$_aaItem_data[] = $_aItem_data;
							}
						}
					}
					elseif($item == 'contact_lens')
					{
						$_aItem_data['oinc_type'] = 'C';
						//Load danh sách chủng loại áp tròng
						$_aCats = $this->config->item('filter_contact_lens');
						if(!empty($_aCats))
						{
							foreach($_aCats as $k=>$v)
							{
								$_aItem_data['doc_entry'] = 'CC'.$_aItem_data['oinc_type'].$_iTime.'-'.$k;
								$_aItem_data['doc_num'] = 'C-'.date('Y-m-d-h-m-s',$_iTime).'-'.$k;
								$_aItem_data['zone'] = $v;
								$_aaItem_data[] = $_aItem_data;
							}
						}
					} 
					elseif($item == 'sun_glasses')
					{
						$_aItem_data['oinc_type'] = 'S';
						//Load danh sách chủng loại áp tròng
						$_aCats = $this->config->item('filter_sun_glasses');
						if(!empty($_aCats))
						{
							foreach($_aCats as $k=>$v)
							{
								$_aItem_data['doc_entry'] = 'CS'.$_aItem_data['oinc_type'].$_iTime.'-'.$k;
								$_aItem_data['doc_num'] = 'S-'.date('Y-m-d-h-m-s',$_iTime).'-'.$k;
								$_aItem_data['zone'] = $v;
								$_aaItem_data[] = $_aItem_data;
							}
						}
					}
					else {
						$_aItem_data['oinc_type'] = 'D';
						///var_dump($this->config->item('filter'));
					}
					
					
				} */
				foreach ($_aCategories as $item) {
					switch ($item) {
						case 'lens':
							$_aaItem_data = array_merge($_aaItem_data, $this->generateItemData('L', 'CL', 'iKindOfLens', $_iTime,$_aItem_data));
							break;
						case 'frame':
							$_aaItem_data = array_merge($_aaItem_data, $this->generateItemData('F', 'CF', 'filter', $_iTime,$_aItem_data));
							break;
						case 'medicine':
							$_aaItem_data = array_merge($_aaItem_data, $this->generateItemData('M', 'CM', 'filter_other', $_iTime,$_aItem_data));
							break;
						case 'contact_lens':
							$_aaItem_data = array_merge($_aaItem_data, $this->generateItemData('C', 'CC', 'filter_contact_lens', $_iTime, $_aItem_data));
							break;
						case 'sun_glasses':
							$_aaItem_data = array_merge($_aaItem_data, $this->generateItemData('S', 'CS', 'filter_sun_glasses', $_iTime, $_aItem_data));
							break;
						default:
							$_aItem_data['oinc_type'] = 'D';
							break;
					}
				}
			}
		} else {
			
			$_sInputName = $this->input->post('category_select');
			//echo $_sInputName;
			if($_sInputName == '') 
			{
				exit();
			} else {
				$_items = $this->input->post('m_'.$_sInputName.'[]');
				//var_dump($_items); die();
				if(is_array($_items))
				{
					switch ($_sInputName) {
						case 'lens':
							$_aaItem_data = array_merge($_aaItem_data, $this->generateItemData('L', 'CL', $_items, $_iTime,$_aItem_data));
							break;
						case 'frame':
							$_aaItem_data = array_merge($_aaItem_data, $this->generateItemData('F', 'CF', $_items, $_iTime,$_aItem_data));
							break;
						case 'medicines':
							$_aaItem_data = array_merge($_aaItem_data, $this->generateItemData('M', 'CM', $_items, $_iTime,$_aItem_data));
							break;
						case 'contact_lens':
							$_aaItem_data = array_merge($_aaItem_data, $this->generateItemData('C', 'CC', $_items, $_iTime, $_aItem_data));
							break;
						case 'sun_glasses':
							$_aaItem_data = array_merge($_aaItem_data, $this->generateItemData('S', 'CS', $_items, $_iTime, $_aItem_data));
							break;
						default:
							$_aItem_data['oinc_type'] = 'D';
							break;
					}
				} else {
					exit();
				}
			}
		}
		//var_dump($_aaItem_data);
		//die();
		$_iRows = $this->Oinc->insert_batch($_aaItem_data);
		redirect(site_url('oincs'));
		exit();

		if($_iRows)
		{
			
            	$message = $this->xss_clean($this->lang->line('oincs_successful'));
            	echo json_encode(array('success' => TRUE, 'message' => $message, 'id' => ''));
          
		}
		else//failure
		{
			$message = $this->xss_clean($this->lang->line('oincs_error_adding_updating'));

			echo json_encode(array('success' => FALSE, 'message' => $message, 'id' => ''));
		}
	}

	// Hàm tạo thông tin doc_entry và doc_num
	private function generateItemData($type, $prefix, $configKey, $time, $Item) {
		$items = [];
		if(is_array($configKey))
		{
			$categories = $configKey;
		} else {
			$categories = $this->config->item($configKey);
		}
		if (!empty($categories)) {
			foreach ($categories as $k => $v) {
				$items[] = array_merge($Item,[
					'oinc_type' => $type,
					'doc_entry' => $prefix . $type . $time . '-' . $k,
					'doc_num' => $type . '-' . date('Y-m-d-h-m-s', $time) . '-' . $k,
					'zone' => $v
				]);
			}
		}
		return $items;
	}


	/** ---------------- */
	
	public function check_item_number()
	{
		$exists = $this->Item->item_number_exists($this->input->post('item_number'), $this->input->post('item_id'));
		echo !$exists ? 'true' : 'false';
	}
	
	public function save_inventory($item_id = -1)
	{	
		$employee_id = $this->Employee->get_logged_in_employee_info()->person_id;
		$cur_item_info = $this->Item->get_info($item_id);
        $location_id = $this->input->post('stock_location');
		$inv_data = array(
			'trans_date' => date('Y-m-d H:i:s'),
			'trans_items' => $item_id,
			'trans_user' => $employee_id,
			'trans_location' => $location_id,
			'trans_comment' => $this->input->post('trans_comment'),
			'trans_inventory' => parse_decimals($this->input->post('newquantity'))
		);
		
		$this->Inventory->insert($inv_data);
		
		//Update stock quantity
		$item_quantity = $this->Item_quantity->get_item_quantity($item_id, $location_id);
		$item_quantity_data = array(
			'item_id' => $item_id,
			'location_id' => $location_id,
			'quantity' => $item_quantity->quantity + parse_decimals($this->input->post('newquantity'))
		);

		if($this->Item_quantity->save($item_quantity_data, $item_id, $location_id))
		{
			$message = $this->xss_clean($this->lang->line('items_successful_updating') . ' ' . $cur_item_info->name);
			
			echo json_encode(array('success' => TRUE, 'message' => $message, 'id' => $item_id));
		}
		else//failure
		{
			$message = $this->xss_clean($this->lang->line('items_error_adding_updating') . ' ' . $cur_item_info->name);
			
			echo json_encode(array('success' => FALSE, 'message' => $message, 'id' => -1));
		}
	}

	public function delete()
	{
		$items_to_delete = $this->input->post('ids');

		if($this->Item->delete_list($items_to_delete))
		{
			$message = $this->lang->line('items_successful_deleted') . ' ' . count($items_to_delete) . ' ' . $this->lang->line('items_one_or_multiple');
			echo json_encode(array('success' => TRUE, 'message' => $message));
		}
		else
		{
			echo json_encode(array('success' => FALSE, 'message' => $this->lang->line('items_cannot_be_deleted')));
		}
	}

	/*
	Items import from excel spreadsheet
	*/
	public function excel()
	{
		$name = 'import_items.xlsx';
		$data = file_get_contents('../' . $name);
		force_download($name, $data);
	}
	
	public function excel_import()
	{
		$this->load->view('items/form_excel_import', NULL);
	}


	// Import sản phẩm của hệ thống mới
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
		if($_FILES['file_path']['error'] != UPLOAD_ERR_OK)
		{
			echo json_encode(array('success' => FALSE, 'message' => $this->lang->line('items_excel_import_failed')));
		}
		else
		{
			$array_file = explode('.', $_FILES['file_path']['name']);
            $extension  = end($array_file);
            if('csv' == $extension) {
				if(($handle = fopen($_FILES['file_path']['tmp_name'], 'r')) !== FALSE)
				{
					// Skip the first row as it's the table description
					fgetcsv($handle);
					$i = 1;

					$failCodes = array();

					while(($data = fgetcsv($handle)) !== FALSE)
					{
						// XSS file data sanity check
						$data = $this->xss_clean($data);

						if(sizeof($data) >= 23)
						{
							$item_data = array(
								'name'					=> $data[1],
								'description'			=> $data[11],
								'category'				=> $data[2],
								'cost_price'			=> $data[4],
								'unit_price'			=> $data[5],
								'reorder_level'			=> $data[10],
								'supplier_id'			=> $this->Supplier->exists($data[3]) ? $data[3] : NULL,
								'allow_alt_description'	=> $data[12] != '' ? '1' : '0',
								'is_serialized'			=> $data[13] != '' ? '1' : '0',
								'custom1'				=> $data[14],
								'custom2'				=> $data[15],
								'custom3'				=> $data[16],
								'custom4'				=> $data[17],
								'custom5'				=> $data[18],
								'custom6'				=> $data[19],
								'custom7'				=> $data[20],
								'custom8'				=> $data[21],
								'custom9'				=> $data[22],
								'custom10'				=> $data[23]
							);
							$item_number = $data[0];
							$invalidated = FALSE;
							if($item_number != '')
							{
								$item_data['item_number'] = $item_number;
								$invalidated = $this->Item->item_number_exists($item_number);
							}
						}
						else
						{
							$invalidated = TRUE;
						}

						if(!$invalidated && $this->Item->save($item_data))
						{
							$items_taxes_data = NULL;
							//tax 1
							if(is_numeric($data[7]) && $data[6] != '')
							{
								$items_taxes_data[] = array('name' => $data[6], 'percent' => $data[7] );
							}

							//tax 2
							if(is_numeric($data[9]) && $data[8] != '')
							{
								$items_taxes_data[] = array('name' => $data[8], 'percent' => $data[9] );
							}

							// save tax values
							if(count($items_taxes_data) > 0)
							{
								$this->Item_taxes->save($items_taxes_data, $item_data['item_id']);
							}

							// quantities & inventory Info
							$employee_id = $this->Employee->get_logged_in_employee_info()->person_id;
							$emp_info = $this->Employee->get_info($employee_id);
							$comment =$this->lang->line('items_qty_file_import');

							$cols = count($data);

							// array to store information if location got a quantity
							$allowed_locations = $this->Stock_location->get_allowed_locations();
							for ($col = 24; $col < $cols; $col = $col + 2)
							{
								$location_id = $data[$col];
								if(array_key_exists($location_id, $allowed_locations))
								{
									$item_quantity_data = array(
										'item_id' => $item_data['item_id'],
										'location_id' => $location_id,
										'quantity' => $data[$col + 1],
									);
									$this->Item_quantity->save($item_quantity_data, $item_data['item_id'], $location_id);

									$excel_data = array(
										'trans_items' => $item_data['item_id'],
										'trans_user' => $employee_id,
										'trans_comment' => $comment,
										'trans_location' => $data[$col],
										'trans_inventory' => $data[$col + 1]
									);

									$this->Inventory->insert($excel_data);
									unset($allowed_locations[$location_id]);
								}
							}

							/*
							* now iterate through the array and check for which location_id no entry into item_quantities was made yet
							* those get an entry with quantity as 0.
							* unfortunately a bit duplicate code from above...
							*/
							foreach($allowed_locations as $location_id => $location_name)
							{
								$item_quantity_data = array(
									'item_id' => $item_data['item_id'],
									'location_id' => $location_id,
									'quantity' => 0,
								);
								$this->Item_quantity->save($item_quantity_data, $item_data['item_id'], $data[$col]);

								$excel_data = array(
									'trans_items' => $item_data['item_id'],
									'trans_user' => $employee_id,
									'trans_comment' => $comment,
									'trans_location' => $location_id,
									'trans_inventory' => 0
								);

								$this->Inventory->insert($excel_data);
							}
						}
						else //insert or update item failure
						{
							$failCodes[] = $i;
						}

						++$i;
					}

					if(count($failCodes) > 0)
					{
						$message = $this->lang->line('items_excel_import_partially_failed') . ' (' . count($failCodes) . '): ' . implode(', ', $failCodes);

						echo json_encode(array('success' => FALSE, 'message' => $message));
					}
					else
					{
						echo json_encode(array('success' => TRUE, 'message' => $this->lang->line('items_excel_import_success')));
					}
				}
				else
				{
					echo json_encode(array('success' => FALSE, 'message' => $this->lang->line('items_excel_import_nodata_wrongformat')));
				}
                //$reader = new Csv();
				exit();
            } else {
                $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
            }

			$spreadsheet = $reader->load($_FILES['file_path']['tmp_name']);
            $sheet_data  = $spreadsheet->getActiveSheet(0)->toArray();
			$worksheet = $spreadsheet->getActiveSheet(0);
			//var_dump($worksheet);
            
			$highestColumn = 6;
			
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
			$failCodes = [];
			// Bỏ qua dòng đầu tiên, start với i=1
			for($i = 1; $i < count($sheet_data); $i++) {
				//$rowData = $sheet->rangeToArray('A' . $i . ':' . $highestColumn . $i,NULL,TRUE,FALSE);
				
				if(isEmptyRow($sheet_data[$i],$highestColumn)) { continue; } // skip empty row
				$data = $sheet_data[$i];
				//var_dump($data);
				$item_data = array(
					'name'					=> $data[1] != null ? $data[1]:'',
					'description'			=> $data[11] != null ? $data[11]:0,
					'category'				=> $data[2] != null ? $data[2]:'',
					'cost_price'			=> is_numeric($data[4]) == true ? $data[4]:0,
					'unit_price'			=> is_numeric($data[5]) == true ? $data[5]:0,
					'reorder_level'			=> is_numeric($data[10]) == true ? $data[10]:0,
					'supplier_id'			=> $this->Supplier->exists($data[3]) ? $data[3] : NULL,
					'allow_alt_description'	=> $data[12] != null ? $data[12] : '',
					'is_serialized'			=> $data[13] != null ? '1' : '0',
					'custom1'				=> $data[14] != null ? $data[14]:'',
					'custom2'				=> $data[15] != null ? $data[15]:'',
					'custom3'				=> $data[16] != null ? $data[16]:'',
					'custom4'				=> $data[17] != null ? $data[17]:'',
					'custom5'				=> $data[18] != null ? $data[18]:'',
					'custom6'				=> $data[19] != null ? $data[19]:'',
					'custom7'				=> $data[20] != null ? $data[20]:'',
					'custom8'				=> $data[21] != null ? $data[21]:'',
					'custom9'				=> $data[22] != null ? $data[22]:'',
					'custom10'				=> $data[23] != null ? $data[23]:''
				);
				$item_number = $data[0] != null ? $data[0]:'';
				$invalidated = FALSE;
				if($item_number != '')
				{
					$item_data['item_number'] = $item_number;
					$invalidated = $this->Item->item_number_exists($item_number);
				}
				//var_dump($item_data);die();
				if(!$invalidated && $this->Item->save($item_data))
						{
							$items_taxes_data = NULL;
							//tax 1
							if(is_numeric($data[7]) && $data[6] != '')
							{
								$items_taxes_data[] = array('name' => $data[6], 'percent' => $data[7] );
							}

							//tax 2
							if(is_numeric($data[9]) && $data[8] != '')
							{
								$items_taxes_data[] = array('name' => $data[8], 'percent' => $data[9] );
							}

							// save tax values
							if(count($items_taxes_data) > 0)
							{
								$this->Item_taxes->save($items_taxes_data, $item_data['item_id']);
							}

							// quantities & inventory Info
							$employee_id = $this->Employee->get_logged_in_employee_info()->person_id;
							$emp_info = $this->Employee->get_info($employee_id);
							$comment =$this->lang->line('items_qty_file_import');

							$cols = count($data);

							// array to store information if location got a quantity
							$allowed_locations = $this->Stock_location->get_allowed_locations();
							for ($col = 24; $col < $cols; $col = $col + 2)
							{
								$location_id = $data[$col];
								if(array_key_exists($location_id, $allowed_locations))
								{
									$item_quantity_data = array(
										'item_id' => $item_data['item_id'],
										'location_id' => $location_id,
										'quantity' => $data[$col + 1],
									);
									$this->Item_quantity->save($item_quantity_data, $item_data['item_id'], $location_id);

									$excel_data = array(
										'trans_items' => $item_data['item_id'],
										'trans_user' => $employee_id,
										'trans_comment' => $comment,
										'trans_location' => $data[$col],
										'trans_inventory' => $data[$col + 1]
									);

									$this->Inventory->insert($excel_data);
									unset($allowed_locations[$location_id]);
								}
							}

							/*
							* now iterate through the array and check for which location_id no entry into item_quantities was made yet
							* those get an entry with quantity as 0.
							* unfortunately a bit duplicate code from above...
							*/
							foreach($allowed_locations as $location_id => $location_name)
							{
								$item_quantity_data = array(
									'item_id' => $item_data['item_id'],
									'location_id' => $location_id,
									'quantity' => 0,
								);
								$this->Item_quantity->save($item_quantity_data, $item_data['item_id'], $data[$col]);

								$excel_data = array(
									'trans_items' => $item_data['item_id'],
									'trans_user' => $employee_id,
									'trans_comment' => $comment,
									'trans_location' => $location_id,
									'trans_inventory' => 0
								);

								$this->Inventory->insert($excel_data);
							}
						}
						else //insert or update item failure
						{
							$failCodes[] = $i;
						}
				
				
			}
			if(count($failCodes) > 0)
			{
				$message = $this->lang->line('items_excel_import_partially_failed') . ' (' . count($failCodes) . '): ' . implode(', ', $failCodes);

				echo json_encode(array('success' => FALSE, 'message' => $message));
			}
			else
			{
				echo json_encode(array('success' => TRUE, 'message' => $this->lang->line('items_excel_import_success')));
			}
		}
	}
	/**
	 * Kiểm kê
	 * @param mixed $uuid
	 * @return void
	 */
	public function count($uuid='',$re='')
	{
		//echo '$uuid'.$uuid;
		//echo '$re'.$re;
		//die();
		$data = [];
		$_oTheOinc = $this->Oinc->get_info($uuid);
		if($_oTheOinc->status == 'P' || $_oTheOinc->status == 'C') // không làm gì
		{
			redirect('oincs/check'.$uuid); 
			exit();
		}

		$this->count_lib->set_oinc_uuid($uuid); // thiết lập UUID
		if($_oTheOinc->oinc_id > 0)
		{
			/*
			$_iOincID = $this->count_lib->get_oinc_id(); // Lấy trong session
			
			if($_iOincID != $_oTheOinc->oinc_id) // kiểm tra nếu session khác với hiện tại clear session
			{
				
				$this->count_lib->clearAll(); // Clean all session khi chuyển sang tài liệu mới
				$this->update_memory($_oTheOinc); //load tài liệu hiện tại
			} */
			if($re == '')
			{
				
				$this->count_lib->clearAll(); // Clean all session khi chuyển sang tài liệu mới
				$this->update_memory($_oTheOinc); //load tài liệu hiện tại
			

				// Update tài liệu mới vào session
				$data['oinc_id'] = $this->count_lib->get_oinc_id();
				$data['oinc_uuid'] = $this->count_lib->get_oinc_uuid();
				$data['TheOinc'] = $this->get_memory();
				$data['lens'] = $this->config->item('iKindOfLens');
				$data['is_lens'] = false;

				if ( in_array($data['TheOinc']['zone'], $data['lens']))
				{
					$data['is_lens'] = true;
				} 

				$this->count_lib->load_doc_to_cart($_oTheOinc->oinc_id); //
				//var_dump($this->count_lib->get_cart());
				//if($re == 'B')
				//{
				$data['cart'] = make_diff_first($this->count_lib->get_cart());
				//var_dump($data['cart']);die();
				//} else {
				//	$data['cart'] = $this->count_lib->get_cart();
				//}

				//$data['cart'] = $this->count_lib->get_cart();
				//var_dump($data['cart']);die();
				$data['quantity'] = $this->count_lib->get_quantity();
					
				$this->load->view("oincs/register", $data);
			} else {
				$this->_reload($data);
			}
		} else {
			//echo '123';
			redirect('oincs');
		}
	}

	private function update_memory($oTheOinc)
	{
		$this->count_lib->set_oinc_id($oTheOinc->oinc_id);

		$this->count_lib->set_oinc_uuid($oTheOinc->oinc_uuid);
		$this->count_lib->set_doc_entry($oTheOinc->doc_entry);
		$this->count_lib->set_doc_num($oTheOinc->doc_num);

		$this->count_lib->set_zone($oTheOinc->zone);
		$this->count_lib->set_creator_name($oTheOinc->creator_name);
		$this->count_lib->set_creator_id($oTheOinc->creator_id);

		$this->count_lib->set_countor_name($oTheOinc->countor_name);
		$this->count_lib->set_countor_id($oTheOinc->countor_id);
		$this->count_lib->set_mode($oTheOinc->oinc_mode);

		$this->count_lib->set_status($oTheOinc->status);
		$this->count_lib->set_created_at($oTheOinc->created_at);
		$this->count_lib->set_type($oTheOinc->oinc_type);
		$this->count_lib->set_count_at($oTheOinc->count_at);
	}

	private function get_memory()
	{
		$_aTheOinc = [];
		$_aTheOinc['oinc_id'] = $this->count_lib->get_oinc_id();
		
		$_aTheOinc['oinc_uuid'] = $this->count_lib->get_oinc_uuid();
		$_aTheOinc['doc_entry'] = $this->count_lib->get_doc_entry();
		$_aTheOinc['doc_num'] = $this->count_lib->get_doc_num();

		$_aTheOinc['zone'] = $this->count_lib->get_zone();
		$_aTheOinc['creator_name'] = $this->count_lib->get_creator_name();
		$_aTheOinc['creator_id'] = $this->count_lib->get_creator_id();

		$_aTheOinc['countor_name'] = $this->count_lib->get_countor_name();
		$_aTheOinc['countor_id'] = $this->count_lib->get_countor_id();
		$_aTheOinc['oinc_mode'] = $this->count_lib->get_mode();

		$_aTheOinc['status'] = $this->count_lib->get_status();
		$_aTheOinc['created_at'] = $this->count_lib->get_created_at();
		$_aTheOinc['oinc_type'] = $this->count_lib->get_type();
		$_aTheOinc['count_at'] = $this->count_lib->get_count_at();
		return $_aTheOinc;
	}

	public function item_search()
	{
		$suggestions = [];
		$search = $this->input->get('term') != '' ? $this->input->get('term') : NULL;

		$suggestions = array_merge($suggestions, $this->Item->get_search_suggestions($search, array('search_custom' => FALSE, 'is_deleted' => FALSE), TRUE));
		
		$suggestions = $this->xss_clean($suggestions);

		echo json_encode($suggestions);
	}

	public function add()
	{
		$data = [];
		// check if any discount is assigned to the selected customer
		$mode = $this->count_lib->get_mode();
		//var_dump($this->sale_lib->get_ctv());die();
		$quantity =  1;
		$item_id_or_number = $this->input->post('item');
		if(!$this->count_lib->add_item($item_id_or_number, $quantity))
		{
			if($item_id_or_number == -2)
			{
				$data['error'] = 'Sản phẩm này không thuộc khu vực này, hãy kiểm tra lại vị trí trên sản phẩm';
			} else {
				$data['error'] = $this->lang->line('oincs_unable_to_add_item');
			}
		}
		else
		{
			$data['warning'] = '';
		}
		$this->_reload($data);
	}

	private function _reload($data = [],$screen = '')
	{		
		
		$uuid = $this->count_lib->get_oinc_uuid();
		$_oTheOinc = $this->Oinc->get_info($uuid);
	
		if($_oTheOinc->oinc_id > 0)
		{
			$_iOincID = $this->count_lib->get_oinc_id();
			
			if($_iOincID != $_oTheOinc->oinc_id)
			{
				
				$this->count_lib->clearAll(); // Clean all session khi chuyển sang tài liệu mới
				$this->update_memory($_oTheOinc);
			}

			//Get session vào $data chuẩn bị hiển thị
			$data['oinc_id'] = $this->count_lib->get_oinc_id();
			$data['oinc_uuid'] = $this->count_lib->get_oinc_uuid();
			$data['TheOinc'] = $this->get_memory();
			$data['lens'] = $this->config->item('iKindOfLens');
			$data['is_lens'] = false;

			if ( in_array($data['TheOinc']['zone'], $data['lens']))
			{
				$data['is_lens'] = true;
			} 

			//$data['cart'] = $this->count_lib->get_cart();
			$data['cart'] = make_diff_first($this->count_lib->get_cart());
			//var_dump($data['cart']);die();

			$data['quantity'] = $this->count_lib->get_quantity();
			
			if($screen == 'check')
			{ 
				$this->load->view("oincs/check", $data);
			} else {
				$this->load->view("oincs/register", $data);
			}
		} else {

		}
	}

	public function cancel()
	{
		$this->count_lib->clearAll();

		redirect(base_url('oincs'));
	}

	public function complete()
	{
		$data = [];
		$data['cart'] = $this->count_lib->get_cart();
		
		$employee_id = $this->Employee->get_logged_in_employee_info()->person_id;
		$employee_info = $this->Employee->get_info($employee_id);
		$data['employee'] = get_fullname($employee_info->first_name,$employee_info->last_name);
		
		$oinc_id = $this->count_lib->get_oinc_id();
		$data['status'] = 0;
		//Thiết lập trạng thái
		$this->count_lib->set_state_code(0);
		$this->count_lib->set_state_id(0);

		$_sStatus =$this->count_lib->get_status();
		//if($_sStatus == 'O' || $_sStatus == 'W' || $_sStatus == 'B')
		if($_sStatus == 'B') // CHi hoàn thành khi nhân viên đã kiểm tra;
		{ 
			if($this->input->post('hidden_form')) {
				if ($oinc_id > 0) {
					
					$_iTime = time();
					$_aOinc = [
						'oinc_id'=>$oinc_id,
						'count_at'=>$_iTime,
						'countor_id'=>$employee_id,
						'countor_name'=>$data['employee'],
						'status'=>'C' // Đã thực hiện kiểm kê, chưa update lên hệ thống KHO
					];

					//var_dump($data['cart']);die();
					if(count($data['cart']) > 0)
					{
						$_aaItem = [];
						foreach($data['cart'] as $item)
						{
							$_aItem = [
								'oinc_id'=>$oinc_id,
								'line_num'=>$item['line'],
								'item_id'=>$item['item_id'],
								'item_name'=>$item['name'],
								'item_number'=> $item['item_number'],
								'item_category' =>$item['item_category'],
								'whs_code'=>1,
								'counted_quantity'=>$item['quantity'],
								'in_whs_quantity'=>$item['in_whs_quantity'],
								'difference_quantity'=>$item['in_whs_quantity'] - $item['quantity'],
								'created_at'=>$_iTime

							];
							$_aaItem[] = $_aItem;
						}
						//var_dump($_aaItem);die();
						$rs = $this->Oinc->save_doc($_aOinc,$_aaItem);
						if($rs)
						{
							$this->count_lib->clearAll(); // clean session of count
							$this->count_lib->set_state_code(1); //Thành công
							$this->count_lib->set_state_id($oinc_id);

							redirect(site_url('oincs'));
							exit();
						} else {
							$data['error'] = 'Bạn không được Refresh lại web hoặc nhấn F5';
							$this->_reload($data);
						}
					} else {
						$data['error'] = 'Bạn không được Refresh lại web hoặc nhấn F5';
						$this->_reload($data);
					}
					
				} else {
					$data['error'] = 'Bạn không được Refresh lại web hoặc nhấn F5';
					$this->_reload($data);
				}
					//$this->sale_lib->clear_all(); //CLEAR ALL DATA CART in SESSION
			}else{
				$data['error'] = 'Bạn không được Refresh lại web hoặc nhấn F5';
				$this->_reload($data);
			}
		} else {
			$data['error'] = 'Bạn không thực hiện được lệnh này, do tài liệu này đang được xem xét';
			$this->_reload($data);
		}
	}

	public function edit_item($item_id=0)
	{
		if($item_id == 0)
		{
			$data['error'] = $this->lang->line('sales_error_editing_item');
			$this->_reload($data);
			//exit();
		} else {
			$data = array();
			
			
			$this->form_validation->set_rules('quantity', 'lang:items_quantity', 'required|callback_numeric');
			$quantity = parse_decimals($this->input->post('quantity'));
			if($quantity == null)
			{
				$data['error'] = $this->lang->line('sales_error_editing_item');
				$this->_reload($data);
			}
			if($this->form_validation->run() != FALSE)
			{
				$this->count_lib->edit_item($item_id, $quantity);
			}
			else
			{
				$data['error'] = $this->lang->line('sales_error_editing_item');
			}

			$data['warning'] = '';

			$this->_reload($data);
		}
	}

	public function delete_item($item_id=0)
	{
		$this->count_lib->delete_item($item_id);

		$this->_reload();
	}

	public function check($uuid=0)
	{
		$_oTheOinc = $this->Oinc->get_info($uuid);
	
		if($_oTheOinc->oinc_id > 0)
		{
			
				
			$this->count_lib->clearAll(); // Clean all session khi chuyển sang tài liệu mới
			$this->update_memory($_oTheOinc);
			

			// Update tài liệu mới vào session
			$data['oinc_id'] = $this->count_lib->get_oinc_id();
			$data['oinc_uuid'] = $this->count_lib->get_oinc_uuid();
			$data['TheOinc'] = $this->get_memory();

			$this->count_lib->load_doc_to_cart($_oTheOinc->oinc_id);

			$data['cart'] = $this->count_lib->get_cart();
			
			$data['quantity'] = $this->count_lib->get_quantity();


			$this->load->view("oincs/check", $data);
		} else {

		}
	}

	public function post()
	{
		if(has_grant('is_can_post'))
		{
			
			$data['cart'] = $this->count_lib->get_cart();
		
			$employee_id = $this->session->userdata('person_id');
			$employee_info = $this->session->userdata('theUser');
			$_oTheUser = $this->session->userdata('theUser');

			$data['employee'] = get_fullname($employee_info->first_name,$employee_info->last_name);
			
			$oinc_id = $this->count_lib->get_oinc_id();
			$_aTheOinc = $this->get_memory();
			if($_aTheOinc['status'] == 'C')
			{
				if ($oinc_id > 0) {
					
					//update - payment, and sale status from 1 to 0
					$_iTime = time();
					$_aOinc = [
						'oinc_id'=>$oinc_id,
						'count_at'=>$_aTheOinc['count_at'],
						'status'=>'P' // Đã thực hiện update lên hệ thống KHO
					];
					if(count($data['cart']) > 0)
					{
						
							$_aaItem = [];
							foreach($data['cart'] as $item)
							{
								$_aItem = [
									'oinc_id'=>$oinc_id,
									'line_num'=>$item['line'],
									'item_id'=>$item['item_id'],
									'item_name'=>$item['name'],
									'item_number'=> $item['item_number'],
									'item_category' =>$item['item_category'],
									'whs_code'=>1,
									'counted_quantity'=>$item['quantity'],
									'in_whs_quantity'=>$item['in_whs_quantity'],
									'difference_quantity'=>$item['quantity'] - $item['in_whs_quantity'],
									'quantity'=>$item['quantity'] - $item['in_whs_quantity'],
									'created_at'=>$_iTime

								];
								$_aaItem[] = $_aItem;
							}

							$rs = $this->Oinc->post_doc($_aOinc,$_aaItem,$employee_id);

							if($rs)
							{
								$this->count_lib->set_status('P'); // Set trạng thái, không cho phép ghi lần 2; thành công rồi, không ghi nữa;
								$data['success'] = 'Đã thực hiện điều chỉnh kho thành công theo phiếu kiểm kê này '.$_aTheOinc['doc_num'];
								$this->_reload($data,'check');
							} else {
								$data['error'] = 'Bạn không được Refresh lại web hoặc nhấn F5';
								$this->_reload($data,'check');
							}

					} else {
						$data['error'] = 'Chưa có sản phẩm kiểm kê';
								$this->_reload($data,'check');
					}
				} else {
					$data['error'] = 'Bạn không được Refresh lại web hoặc nhấn F5';
								$this->_reload($data,'check');
				}
			} else {
				$data['error'] = 'Kiểm kê này đã hoàn thành, không thể thực hiện bất cứ hành động nào';
				$this->_reload($data,'check');
			}

		} else {
			//Do nothing
			$data['error'] = 'Bạn không chưa được cấp quyền để thực hiện hoạt động này';
			$this->_reload($data,'check');
		}
	}

	public function do_save()
	{
		$data = [];
		$data['cart'] = $this->count_lib->get_cart();
		
		$employee_id = $this->Employee->get_logged_in_employee_info()->person_id;
		$employee_info = $this->Employee->get_info($employee_id);
		$data['employee'] = get_fullname($employee_info->first_name,$employee_info->last_name);
		
		$oinc_id = $this->count_lib->get_oinc_id();
		$data['status'] = 0;
		//Thiết lập trạng thái
		$this->count_lib->set_state_code(0);
		$this->count_lib->set_state_id(0);

		$_sStatus =$this->count_lib->get_status();
		if($_sStatus == 'O' || $_sStatus == 'W' || $_sStatus == 'B')
		{ 

			if($this->input->post('hidden_form')) {
				if ($oinc_id > 0) {
					//update - payment, and sale status from 1 to 0
					$_iTime = time();
					$_aOinc = [
						'oinc_id'=>$oinc_id,
						'count_at'=>$_iTime,
						'countor_id'=>$employee_id,
						'countor_name'=>$data['employee'],
						'status'=>'W' // Đã thực hiện kiểm kê, chưa update lên hệ thống KHO
					];

					//var_dump($data['cart']);die();
					if(count($data['cart']) > 0)
					{
						$_aaItem = [];
						foreach($data['cart'] as $item)
						{
							$_aItem = [
								'oinc_id'=>$oinc_id,
								'line_num'=>$item['line'],
								'item_id'=>$item['item_id'],
								'item_name'=>$item['name'],
								'item_number'=> $item['item_number'],
								'item_category' =>$item['item_category'],
								'whs_code'=>1,
								'counted_quantity'=>$item['quantity'],
								'in_whs_quantity'=>$item['in_whs_quantity'],
								'difference_quantity'=>$item['in_whs_quantity'] - $item['quantity'],
								'created_at'=>$_iTime

							];
							$_aaItem[] = $_aItem;
						}
						//var_dump($_aaItem);die();
						$rs = $this->Oinc->save_doc($_aOinc,$_aaItem);
						if($rs)
						{
							$this->count_lib->clearAll(); // clean session of count
							$this->count_lib->set_state_code(1); //Thành công
							$this->count_lib->set_state_id($oinc_id);

							redirect(site_url('oincs'));
							exit();
						} else {
							$data['error'] = 'Bạn không được Refresh lại web hoặc nhấn F5';
							$this->_reload($data);
						}
					} else {
						$data['error'] = 'Bạn không được Refresh lại web hoặc nhấn F5';
						$this->_reload($data);
					}
					
				} else {
					$data['error'] = 'Bạn không được Refresh lại web hoặc nhấn F5';
					$this->_reload($data);
				}
					//$this->sale_lib->clear_all(); //CLEAR ALL DATA CART in SESSION
			}else{
				$data['error'] = 'Bạn không được Refresh lại web hoặc nhấn F5';
				$this->_reload($data);
			}
		} else {
			$data['error'] = 'Bạn không thực hiện được lệnh này, do tài liệu này đang được xem xét';
			$this->_reload($data);
		}
	}
	/*
	public function do_check()
	{
		//echo '123';
		$data = [];
		$employee_id = $this->Employee->get_logged_in_employee_info()->person_id;
		$employee_info = $this->Employee->get_info($employee_id);
		$data['employee'] = get_fullname($employee_info->first_name,$employee_info->last_name);
		
		$oinc_id = $this->count_lib->get_oinc_id();
		$data['status'] = 0;
		//Thiết lập trạng thái
		$this->count_lib->set_state_code(0);
		$this->count_lib->set_state_id(0);
		$data['cart'] = $this->count_lib->get_cart();
		$_sStatus =$this->count_lib->get_status();
		$_sCategory = $this->count_lib->get_zone();
		if($_sStatus == 'O' || $_sStatus == 'W' || $_sStatus == 'B')
		{ 
			if($this->input->post('hidden_form')) {
				if ($oinc_id > 0) {
					//update - payment, and sale status from 1 to 0
					$_iTime = time();
					$_aOinc = [
						'oinc_id'=>$oinc_id,
						'count_at'=>$_iTime,
						'countor_id'=>$employee_id,
						'countor_name'=>$data['employee'],
						'status'=>'B' // Đã thực hiện kiểm kê, chưa update lên hệ thống KHO
					];

					$_aItemId = [];
					
					//var_dump($data['cart']);die();
					if(count($data['cart']) > 0)
					{
						$_aaItem = [];
						//var_dump($data['cart']);die();
						foreach($data['cart'] as $item)
						{
							
							$_aItem = [
								'oinc_id'=>$oinc_id,
								'line_num'=>$item['line'],
								'item_id'=>$item['item_id'],
								'item_name'=>$item['name'],
								'item_number'=> $item['item_number'],
								'item_category' =>$_sCategory,
								'whs_code'=>1,
								'counted_quantity'=>$item['quantity'],
								'in_whs_quantity'=>$item['in_whs_quantity'],
								'difference_quantity'=>$item['in_whs_quantity'] - $item['quantity'],
								'created_at'=>$_iTime

							];
							$_aaItem[] = $_aItem;
							$_aItemId[] = $item['item_id'];
						}
						// Lây dữ liệu trong bản items rong danh mục mà có số lượng khác 0, không thuộc các item trong cart;
						$_aItems = $this->Item->get_items_for_inventory($_sCategory,$_aItemId); // location = 1
						if(!empty($_aItems))
						{
							foreach($_aItems as $_item)
							{
								$_aItem = [
									'oinc_id'=>$oinc_id,
									'line_num'=>0,
									'item_id'=>$_item['item_id'],
									'item_name'=>$_item['name'],
									'item_number'=> $_item['item_number'],
									'item_category' =>$_item['category'],
									'whs_code'=>1,
									'counted_quantity'=>0,
									'in_whs_quantity'=>$_item['quantity'],
									'difference_quantity'=>$_item['quantity'],
									'created_at'=>$_iTime

								];
								$_aaItem[] = $_aItem;
							}
						}
						//var_dump($_sCategory);
						//var_dump($_aItems);die();
						//var_dump($_aaItem);die();
						// step 1: Lưu bản kiểm kê (lưu session to mysql)
						$rs = $this->Oinc->save_doc($_aOinc,$_aaItem);
						if($rs)
						{
							
						// step 2: So sanh kiem ke
						
							//$this->count_lib->clearAll(); // clean session of count
							$this->count_lib->set_status('B');
							$this->count_lib->set_state_code(1); //Thành công
							$this->count_lib->set_state_id($oinc_id);
							$this->count_lib->load_doc_to_cart($oinc_id); // Load danh items của tài liệu

							$data['cart'] = $this->count_lib->get_cart();
							//$_oTheOinc = $this->Oinc->get_info($this->count_lib->get_oinc_uuid());
							$data['TheOinc'] = $this->get_memory();

						// step 3: hiển thị kế t quả so sánh	
							redirect(site_url('oincs/count/'.$this->count_lib->get_oinc_uuid().'/B')); // Refresh this page
							exit();
						} else {
							$data['error'] = 'Bạn không được Refresh lại web hoặc nhấn F54';
							$this->_reload($data);
						}
					} else {
						$data['error'] = 'Bạn không được Refresh lại web hoặc nhấn F53';
						$this->_reload($data);
					}
					
				} else {
					$data['error'] = 'Bạn không được Refresh lại web hoặc nhấn F52';
					$this->_reload($data);
				}
					//$this->sale_lib->clear_all(); //CLEAR ALL DATA CART in SESSION
			}else{
				$data['error'] = 'Bạn không được Refresh lại web hoặc nhấn F51';
				$this->_reload($data);
			}
		} else {
			$data['error'] = 'Bạn không thực hiện được lệnh này, do tài liệu này đang được xem xét';
			$this->_reload($data);
		}

	} */
	/**
	 * BEGIN PHÂN QUYỀN *
	 */

	/**
	 * Phân quyền hiển thị số lượng item trong hệ thống 
	 * 
	 * @return bool
	 */
	public function is_show_whs_quantity()
	{
		return true;
	}
	/**
	 * Cho phép hiển thị form thêm mới tài liệu kiểm kê/ Tạo kiểm kê
	 * @return bool
	 */
	public function is_show_view()
	{
		return true;
	}
	/**
	 * Cho phép hiển thị màn hình nhập kiểm kê/ Sau khi tạo kiểm kê
	 * Màn hình danh sách kiểm kê có thêm nút bắt đầu kiểm kê/ đối với tài liệu đang mở.
	 * Tài liệu đóng/ cho phép xem lại kết quả kiểm kê;
	 * @return bool
	 */
	public function is_show_count()
	{
		return true;
	}
	/**
	 * Cho phép thực hiện, tạo hiêu chỉnh kho từ tài liệu kiểm kê;
	 * Chức năng này cho quản lý của hàng hoặc chủ cửa hàng thực hiện;
	 * @return bool
	 */
	public function is_show_post()
	{
		return true;
	}
	/**
	 * Cho phép thực hiện đồng bộ 
	 */

	public function is_can_post()
	{
		return true;
	}
	/**
	 * END PHÂN QUYỀN *
	 */

	public function do_check()
	{
		 $data = [];
		 $employee = $this->Employee->get_logged_in_employee_info();
		 $data['employee'] = get_fullname($employee->first_name, $employee->last_name);
	 
		 $oinc_id = $this->count_lib->get_oinc_id();
		 $this->count_lib->set_state_code(0);
		 $this->count_lib->set_state_id(0);
		 $data['status'] = 0;
		 $data['cart'] = $this->count_lib->get_cart();
		 //var_dump($data['cart']);die();
	 
		 $_sStatus = $this->count_lib->get_status();
		 $_sCategory = $this->count_lib->get_zone();
	 
		 if (!in_array($_sStatus, ['O', 'W', 'B'])) {
			 return $this->_show_error($data, 'Tài liệu này đang được xem xét');
		 }
	 
		 if (!$this->input->post('hidden_form')) {
			 return $this->_show_error($data, 'Bạn không được Refresh lại web hoặc nhấn F51');
		 }
	 
		 if ($oinc_id <= 0) {
			 return $this->_show_error($data, 'Bạn không được Refresh lại web hoặc nhấn F52');
		 }
	 
		 if (empty($data['cart'])) {
			 return $this->_show_error($data, 'Bạn không được Refresh lại web hoặc nhấn F53');
		 }
	 
		 $_iTime = time();
		 $_aOinc = [
			 'oinc_id' => $oinc_id,
			 'count_at' => $_iTime,
			 'countor_id' => $employee->person_id,
			 'countor_name' => $data['employee'],
			 'status' => 'B'
		 ];
	 
		 $_aaItem = $this->_build_count_items($data['cart'], $_sCategory, $_iTime, $oinc_id);
	 
		 // Lấy các item còn lại trong danh mục chưa được kiểm kê (còn tồn kho)
		 $_aItemId = array_column($data['cart'], 'item_id');
		 $_aItems = $this->Item->get_items_for_inventory($_sCategory, $_aItemId);
	 
		$max_line = 0;

		foreach ($data['cart'] as $item) {
			if (isset($item['line']) && $item['line'] > $max_line) {
				$max_line = $item['line'];
			}
		}

		$next_line_num = $max_line + 1;

		 foreach ($_aItems as $_item) {
			 $_aaItem[] = [
				 'oinc_id' => $oinc_id,
				 'line_num' => $next_line_num,
				 'item_id' => $_item['item_id'],
				 'item_name' => $_item['name'],
				 'item_number' => $_item['item_number'],
				 'item_category' => $_item['category'],
				 'whs_code' => 1,
				 'counted_quantity' => 0,
				 'in_whs_quantity' => $_item['quantity'],
				 'difference_quantity' => $_item['quantity'],
				 'created_at' => $_iTime,
				 'type'=>1
			 ];
			 $next_line_num++;
		 }
		 //var_dump($_aaItem);die();
		 if ($this->Oinc->save_doc($_aOinc, $_aaItem)) {
			 $this->count_lib->set_status('B');
			 $this->count_lib->set_state_code(1);
			 $this->count_lib->set_state_id($oinc_id);
			 $this->count_lib->load_doc_to_cart($oinc_id);
	 
			 $data['cart'] = $this->count_lib->get_cart();
			 //var_dump($data['cart']);die();
			 $data['TheOinc'] = $this->get_memory();
	 
			 redirect(site_url('oincs/count/' . $this->count_lib->get_oinc_uuid() . '/B'));
		 } else {
			 return $this->_show_error($data, 'Bạn không được Refresh lại web hoặc nhấn F54');
		 }
	 }
	 

	 private function _show_error($data, $message)
	{
		$data['error'] = $message;
		return $this->_reload($data);
	}

	private function _build_count_items($cart, $category, $time, $oinc_id)
	{
		$items = [];
		foreach ($cart as $item) {
			//var_dump($item);die();
			$items[] = [
				'oinc_id' => $oinc_id,
				'line_num' => $item['line'],
				'item_id' => $item['item_id'],
				'item_name' => $item['name'],
				'item_number' => $item['item_number'],
				'item_category' => $category,
				'whs_code' => 1,
				'counted_quantity' => $item['quantity'],
				'in_whs_quantity' => $item['in_whs_quantity'],
				'difference_quantity' => $item['in_whs_quantity'] - $item['quantity'],
				'created_at' => $time,
				'type'=>$item['type']
			];
		}
		return $items;
	}

	public function lens()
	{
		$data = array();
		//echo '123';die();
		$uuid = $this->count_lib->get_oinc_uuid(); // lấy UUID
		$_oTheOinc = $this->Oinc->get_info($uuid);
		if($_oTheOinc->oinc_id == "")
		{
			redirect('oincs'); 
			exit();
		}
		if($_oTheOinc->status == 'P' || $_oTheOinc->status == 'C') // không làm gì
		{
			redirect('oincs/check'.$uuid); 
			exit();
		}

		
		$data['oinc_id'] = $this->count_lib->get_oinc_id();
		$data['oinc_uuid'] = $uuid;
		$data['TheOinc'] = $this->get_memory();
		$data['lens'] = $this->config->item('iKindOfLens');
		$data['is_lens'] = false;

		if ( in_array($data['TheOinc']['zone'], $data['lens']))
		{
			$data['is_lens'] = true;
		} 

        $data['item_count'] = $data['lens'];
		//var_dump($data['item_count']);
		$data['page_title'] = "KIỂM KÊ MẮT KÍNH <b>$_oTheOinc->zone</b>";

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
			$this->load->view("oincs/lens", $data);
		} else {
			// Nhập sản phẩm //Mắt
			$category = $this->input->post('category');
			// Lấy tất cả tròng kính trong danh mục này;
			$_aALens = $this->Receiving->get_items_by_category($category)->result_array();
			//var_dump($_aALens);
			//echo $category; die();
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
				//var_dump($_aALens);
				//var_dump($_aTmp); die();
				//$this->purchase_lib->set_kind(2);
				foreach($_aTmp as $key=>$value)
				{
					foreach($_aALens as $k=>$v)
					{
						
						if(strpos($v['name'],$key) > 0)
						{
							//echo $v['name'] .'-' . $v['item_id'] . ':'. trim($value).'<br>';
							//$this->receiving_lib->add_item($item_id, $quantity, $item_location);
							//$this->receiving_lib->add_item($v['item_id'], trim($value), 1);
							//$this->purchase_lib->add_item_by_itemID($v['item_id'], trim($value));
							$this->count_lib->add_item($v['item_id'], trim($value));
							//echo $v['item_id'] . '<br>';
						}
						
					}
				}
				//die();
				//$_aCart = $this->receiving_lib->get_cart();
				redirect('oincs/count/'.$uuid.'/lens');
			} else{
				//echo '1234';die();
				$this->load->view("oincs/lens", $data);
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

}
?>
