<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Quản lý tài điểm danh
 * By ManhVT - manhvt89@gmail.com - 0936111917
 * Dated: 22/08/2024
 * Version 1.0
 */

require_once("Secure_Controller.php");

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Csv;
//use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
class Attendances extends Secure_Controller
{
	
	public function __construct()
	{
		
		parent::__construct('attendances');
		//$this->load->library('item_lib');
		//$this->load->library('count_lib');
		$this->load->model('Attendance');
		

	}
	
	public function index()
	{
		
		$data['table_headers'] = $this->xss_clean(get_attendances_manage_table_headers());
		$data['attendance_records'] = $this->Attendance->get_all_employees();

		$open_shift_employees = $this->Attendance->open_shift_employees();
	    $open_shift_employee_ids = array_column($open_shift_employees, 'employee_id');

    	$data['open_shift_employee_ids'] = $open_shift_employee_ids;

        $this->load->view('attendances/view', $data);
	}

	public function check_in($employee_id)
    {
        $employee_info = $this->Employee->get_info($employee_id);
		$this->Attendance->check_in($employee_info);
        redirect('attendances');
    }

    public function check_out($attendance_uuid)
    {
        $this->Attendance->check_out($attendance_uuid);
        redirect('attendances');
    }

    public function report()
    {
        $data['report'] = $this->Attendance->generate_report();
        $this->load->view('attendances/report', $data);
    }

	public function search()
	{
		
		$items = $this->Attendance->get_all_employees();
		$_aClousedItems = $this->Attendance->get_all_closed_attendance();
		$total_rows = 0;

		$data_rows = [];
		foreach($_aClousedItems->result() as $item)
		{
			$total_rows++;
			$data_rows[] = $this->xss_clean(get_attendance_data_row($item, $this));
		}
		foreach($items->result() as $item)
		{
			$total_rows++;
			$data_rows[] = $this->xss_clean(get_attendance_data_row($item, $this));
		}

		echo json_encode(
							['total' => $total_rows, 'rows' => $data_rows] );
	}

	public function view_detail($uuid)
	{
		
		$info = new stdClass();
		
		$info = $this->Employee->get_info($uuid);
		
		foreach(get_object_vars($info) as $property => $value)
		{
			$info->$property = $this->xss_clean($value);
		}
		
		$city_ = get_cities_list();
        $cities = array();
        foreach ($city_ as $key=>$value)
        {
            $cities[$value] = $value;
        }
		$data['city'] = $this->config->item('default_city');//'Bình Thuận';
        /* if($data['city'] == '' || $data['city'] == 'HN')
        {
            $data['city'] = 'Bình Thuận';
        } */
		if($info->age = '')
		{
			$info->age = 30;
		}

		//$uuid = $this->input->post('uuid');
		$tests = $this->Testex->get_tests_by_uuid($uuid);
		
		$data['tests'] = $tests;

		//var_dump($info);
		$headers = $this->Attendance->employee_columns();
		$data['headers'] = transform_headers_html($headers['summary'],true,false);
		$info->first_name = get_fullname($info->first_name, $info->last_name);
		$data['person_info'] = $info;
        $data['cities'] = $cities;
		$data['total'] = $this->xss_clean($this->Customer->get_totals($info->person_id)->total);
		$this->load->view("attendances/detail", $data);

	}

	public function ajax_attendances()
	{
		$_sFromDate = $this->input->post('fromDate');
        $_sToDate = $this->input->post('toDate');

        $_aFromDate = explode('/', $_sFromDate);
        $_aToDate = explode('/', $_sToDate);
        $_sFromDate = $_aFromDate[2] . '/' . $_aFromDate[1] . '/' . $_aFromDate[0];
        $_sToDate = $_aToDate[2] . '/' . $_aToDate[1] . '/' . $_aToDate[0];
        
        $result = 1;

        $_aInput = [
				'uuid'=>$this->input->post('uuid'),
                'start_date'=>$_sFromDate,
                'end_date'=>$_sToDate,
                
            ];
		$sales = $this->Attendance->ajax_attendances($_aInput);
		$headers = $this->Attendance->employee_columns();

		if(!$sales)
		{
			$result = 0;
			$data = array(
				'headers_summary' => transform_headers_raw($headers['summary'],TRUE,false),
				'headers_details' => [],
				'summary_data' => [],
				'details_data' => [],
				'report_data' =>[]
			);
		}else{
			$summary_data = [];
			$details_data = [];
			$i = 1;
			$total_amount = 0;
			$total_quantity = 0;
			foreach($sales['summary'] as $key => $row)
			{
				
				//$row['id'] = $i;
				$summary_data[] = $this->xss_clean(get_attendance_data_detail_row($row, $this));
				$i++;
			}
			
			//$summary_data[] = $footer;
			$data = array(
				'headers_summary' => transform_headers_raw($headers['summary'],TRUE,false),
				'headers_details' => transform_headers_raw($headers['details'],TRUE,false),
				'summary_data' => $summary_data,
				'details_data' => $details_data,
				'report_data' =>$sales
			);
		}
        $json = array('result'=>$result,'data'=>$data);
        echo json_encode($json);
		
	}
	

	/**
	 * END Điểm Danh
	 */

	/*
	Returns Items table data rows. This will be called with AJAX.
	*/
	
	
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

	public function save($item_id = -1)
	{
		
		$_iTime = time();
		//Save item data
		$_person_id = $this->session->userdata('person_id');
		$_oTheUser = $this->session->userdata('theUser');
	
		$_sMode = $this->input->post('mode');
		$_aCagories = $this->input->post('items');
		
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

		if(!empty($_aCagories))
		{
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

	public function count($uuid='')
	{
		
		$_oTheOinc = $this->Oinc->get_info($uuid);
		if($_oTheOinc->status == 'P' || $_oTheOinc->status == 'C')
		{
			redirect('oincs/check'.$uuid);
		}
	
		if($_oTheOinc->oinc_id > 0)
		{
			$_iOincID = $this->count_lib->get_oinc_id();
			
			if($_iOincID != $_oTheOinc->oinc_id)
			{
				
				$this->count_lib->clearAll(); // Clean all session khi chuyển sang tài liệu mới
				$this->update_memory($_oTheOinc);
			}

			// Update tài liệu mới vào session
			$data['oinc_id'] = $this->count_lib->get_oinc_id();
			$data['oinc_uuid'] = $this->count_lib->get_oinc_uuid();
			$data['TheOinc'] = $this->get_memory();

			$this->count_lib->load_doc_to_cart($_oTheOinc->oinc_id);

			$data['cart'] = $this->count_lib->get_cart();

			$data['quantity'] = $this->count_lib->get_quantity();
			//$data['points'] = $this->count_lib->get_points();
			//$data['subtotal'] = $this->count_lib->get_subtotal(TRUE);
			//$data['discount'] = $this->count_lib->get_discount();
			//$data['total'] = $this->count_lib->get_total();
			
			//$data['items_module_allowed'] = $this->Employee->has_grant('sales_price_edit');
			//$data['payments_cover_total'] = $this->count_lib->get_amount_due() <= 0;
			//$data['edit'] = $this->count_lib->get_edit();
			//var_dump($this->sale_lib->get_customer());
			
			//$data = $this->xss_clean($data);

			$this->load->view("oincs/register", $data);
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
			$data['error'] = $this->lang->line('oincs_unable_to_add_item');
		}
		else
		{
			$data['warning'] = '';
		}
		
		$this->_reload($data);
	}

	private function _reload($data = array(),$screen = '')
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

			// Update tài liệu mới vào session
			$data['oinc_id'] = $this->count_lib->get_oinc_id();
			$data['oinc_uuid'] = $this->count_lib->get_oinc_uuid();
			$data['TheOinc'] = $this->get_memory();

			$data['cart'] = $this->count_lib->get_cart();

			$data['quantity'] = $this->count_lib->get_quantity();
			//$data['points'] = $this->count_lib->get_points();
			//$data['subtotal'] = $this->count_lib->get_subtotal(TRUE);
			//$data['discount'] = $this->count_lib->get_discount();
			//$data['total'] = $this->count_lib->get_total();
			
			//$data['items_module_allowed'] = $this->Employee->has_grant('sales_price_edit');
			//$data['payments_cover_total'] = $this->count_lib->get_amount_due() <= 0;
			//$data['edit'] = $this->count_lib->get_edit();
			//var_dump($this->sale_lib->get_customer());
			
			//$data = $this->xss_clean($data);

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
		if($_sStatus == 'O' || $_sStatus == 'W')
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
		if($_sStatus == 'O' || $_sStatus == 'W')
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

}
?>
