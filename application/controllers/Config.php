<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once("Secure_Controller.php");

class Config extends Secure_Controller 
{
	public function __construct()
	{
		parent::__construct('config');

		$this->load->library('barcode_lib');
	}

	/*
	Returns employee table data rows. This will be called with AJAX.
	*/
	public function search()
	{
		$search = $this->input->get('search');
		$limit  = $this->input->get('limit');
		$offset = $this->input->get('offset');
		$sort   = $this->input->get('sort');
		$order  = $this->input->get('order');

		$employees = $this->Ctv->search($search, $limit, $offset, $sort, $order);
		$total_rows = $this->Ctv->get_found_rows($search);

		$data_rows = array();
		foreach($employees->result() as $person)
		{
			$data_rows[] = get_person_data_row($person, $this);
		}

		$data_rows = $this->xss_clean($data_rows);

		echo json_encode(array('total' => $total_rows, 'rows' => $data_rows));
	}

	public function view($employee_id = -1){
		$person_info = $this->Ctv->get_info($employee_id);
		foreach(get_object_vars($person_info) as $property => $value)
		{
			$person_info->$property = $this->xss_clean($value);
		}
		$city_ = get_cities_list();
        $cities = array();
        foreach ($city_ as $key=>$value)
        {
            $cities[$value] = $value;
        }
        $data['city'] = $person_info->city;
        if($data['city'] == '' || $data['city'] == 'HN')
        {
            $data['city'] = 'Hà Nội';
        }
        $data['cities'] = $cities;
		$data['person_info'] = $person_info;
		$this->load->view("ctv/form", $data);
	}

	public function delete()
	{
		$employees_to_delete = $this->xss_clean($this->input->post('ids'));

		if($this->Employee->delete_list($employees_to_delete))
		{
			echo json_encode(array('success' => TRUE,'message' => $this->lang->line('employees_successful_deleted').' '.
				count($employees_to_delete).' '.$this->lang->line('employees_one_or_multiple')));
		}
		else
		{
			echo json_encode(array('success' => FALSE,'message' => $this->lang->line('employees_cannot_be_deleted')));
		}
	}

	public function save_ctv($employee_id = -1)
	{
		$person_data = array(
			'first_name' => $this->input->post('first_name'),
			'last_name' => $this->input->post('last_name'),
			'gender' => $this->input->post('gender'),
			'email' => $this->input->post('email'),
			'phone_number' => $this->input->post('phone_number'),
			'address_1' => $this->input->post('address_1'),
			'address_2' => $this->input->post('address_2'),
			'city' => $this->input->post('city'),
			'state' => $this->input->post('state'),
			'zip' => $this->input->post('zip'),
			'country' => $this->input->post('country'),
			'comments' => $this->input->post('comments')
		);
		if($this->input->post('password') != '')
		{
			$employee_data = array(
				'username' => $this->input->post('username'),
				'password' => password_hash($this->input->post('password'), PASSWORD_DEFAULT),
				'hash_version' => 2,
				'type' => 2
			);
		}
		else //Password not changed
		{
			$employee_data = array('username' => $this->input->post('username'),'type' => 2);
		}
		if($this->Ctv->save_employee($person_data, $employee_data, $employee_id))
		{
			$person_data = $this->xss_clean($person_data);
			$employee_data = $this->xss_clean($employee_data);
			//New employee
			if($employee_id == -1)
			{
				echo json_encode(array('success' => TRUE, 'message' => $this->lang->line('employees_successful_adding').' '.
					$person_data['first_name'].' '.$person_data['last_name'], 'id' => $employee_data['person_id']));
			}
			else //Existing employee
			{
				echo json_encode(array('success' => TRUE, 'message' => $this->lang->line('employees_successful_updating').' '.
					$person_data['first_name'].' '.$person_data['last_name'], 'id' => $employee_id));
			}
		}
		else//failure
		{
			$person_data = $this->xss_clean($person_data);

			echo json_encode(array('success' => FALSE, 'message' => $this->lang->line('employees_error_adding_updating').' '.
				$person_data['first_name'].' '.$person_data['last_name'], 'id' => -1));
		}
	}


	/*
	* This function loads all the licenses starting with the first one being OSPOS one
	*/
	private function _licenses()
	{
		$i = 0;
		$bower = FALSE;
		$composer = FALSE;
		$license = array();

		$license[$i]['title'] = 'Open Source Point Of Sale ' . $this->config->item('application_version');

		if(file_exists('license/LICENSE'))
		{
			$license[$i]['text'] = $this->xss_clean(file_get_contents('license/LICENSE', NULL, NULL, 0, 2000));
		}
		else
		{
			$license[$i]['text'] = 'LICENSE file must be in OSPOS license directory. You are not allowed to use OSPOS application until the distribution copy of LICENSE file is present.';
		}

		// read all the files in the dir license
		$dir = new DirectoryIterator('license');

		foreach($dir as $fileinfo)
		{
			// license files must be in couples: .version (name & version) & .license (license text)
			if($fileinfo->isFile())
			{
				if($fileinfo->getExtension() == 'version')
				{
					++$i;

					$basename = 'license/' . $fileinfo->getBasename('.version');

					$license[$i]['title'] = $this->xss_clean(file_get_contents($basename . '.version', NULL, NULL, 0, 100));

					$license_text_file = $basename . '.license';

					if(file_exists($license_text_file))
					{
						$license[$i]['text'] = $this->xss_clean(file_get_contents($license_text_file , NULL, NULL, 0, 2000));
					}
					else
					{
						$license[$i]['text'] = $license_text_file . ' file is missing';
					}
				}
				elseif($fileinfo->getBasename() == 'bower.LICENSES')
				{
					// set a flag to indicate that the JS Plugin bower.LICENSES file is available and needs to be attached at the end
					$bower = TRUE;
				}
				elseif($fileinfo->getBasename() == 'composer.LICENSES')
				{
					// set a flag to indicate that the composer.LICENSES file is available and needs to be attached at the end
					$composer = TRUE;
				}
			}
		}

		// attach the licenses from the LICENSES file generated by bower
		if($composer)
		{
			++$i;
			$license[$i]['title'] = 'Composer Libraries';
			$license[$i]['text'] = '';

			$file = file_get_contents('license/composer.LICENSES');
			$array = json_decode($file, true);

			foreach($array as $key => $val)
			{
				if(is_array($val) && $key == 'dependencies')
				{	
					foreach($val as $key1 => $val1)
					{
						if(is_array($val1))
						{	
							$license[$i]['text'] .= 'component: ' . $key1 . "\n";

							foreach($val1 as $key2 => $val2)
							{								
								if(is_array($val2))
								{	
									$license[$i]['text'] .= $key2 . ': ';

									foreach($val2 as $key3 => $val3)
									{
										$license[$i]['text'] .= $val3 . ' ';
									}

									$license[$i]['text'] .= "\n";
								}
								else
								{
									$license[$i]['text'] .= $key2 . ': ' . $val2 . "\n";
								}
							}

							$license[$i]['text'] .= "\n";
						}
						else
						{
							$license[$i]['text'] .= $key1 . ': ' . $val1 . "\n";
						}
					}
				}
			}
			
			$license[$i]['text'] = $this->xss_clean($license[$i]['text']);
		}

		// attach the licenses from the LICENSES file generated by bower
		if($bower)
		{
			++$i;
			$license[$i]['title'] = 'JS Plugins';
			$license[$i]['text'] = '';

			$file = file_get_contents('license/bower.LICENSES');
			$array = json_decode($file, true);

			foreach($array as $key => $val)
			{
				if(is_array($val))
				{
					$license[$i]['text'] .= 'component: ' . $key . "\n";
					
					foreach($val as $key1 => $val1)
					{
						if(is_array($val1))
						{
							$license[$i]['text'] .= $key1 . ': ';

							foreach($val1 as $key2 => $val2)
							{
								$license[$i]['text'] .= $val2 . ' ';
							}

							$license[$i]['text'] .= "\n";
						}
						else
						{
							$license[$i]['text'] .= $key1 . ': ' . $val1 . "\n";
						}
					}

					$license[$i]['text'] .= "\n";
				}
			}
			
			$license[$i]['text'] = $this->xss_clean($license[$i]['text']);
		}
		
		return $license;
	}

	private function _themes()
	{
		$themes = array();

		// read all themes in the dist folder
		$dir = new DirectoryIterator('dist/bootswatch');

		foreach($dir as $dirinfo)
		{
			if($dirinfo->isDir() && !$dirinfo->isDot() && $dirinfo->getFileName() != 'fonts')
			{
				$themes[$dirinfo->getFileName()] = $dirinfo->getFileName();
			}
		}

		asort($themes);

		return $themes;
	}
	
	public function index()
	{
		$data['stock_locations'] = $this->Stock_location->get_all()->result_array();
		$data['support_barcode'] = $this->barcode_lib->get_list_barcodes();
		$data['support_template'] = $this->barcode_lib->get_list_template_barcodes();
		$data['logo_exists'] = $this->config->item('company_logo') != '';
		
		$data = $this->xss_clean($data);

		$data['lens_product'] = $this->config->item('iKindOfLens');

		$data['contact_lens_product'] = $this->config->item('filter_contact_lens');

		$data['frame_product'] = $this->config->item('filter');

		$data['glasses_product'] = $this->config->item('filter_sun_glasses');

		$data['other_product'] = $this->config->item('filter_other');
		//var_dump($data['other_product']);
		
		// load all the license statements, they are already XSS cleaned in the private function
		$data['licenses'] = $this->_licenses();
		$data['themes'] = $this->_themes();
		$data['table_headers'] = $this->xss_clean(get_people_manage_table_headers());
		$_caTab = [
			'info' =>'info_tab',
			'general'=>'general_tab',
			'product'=>'product_tab',
			'locale'=>'locale_tab',
			'barcode'=>'barcode_tab',
			'prescription' =>'prescription_tab',
			'stock'=>'stock_tab',
			'ctv'=>'ctv_tab',
			'receipt'=>'receipt_tab',
			'invoice'=>'invoice_tab',
			'email'=>'email_tab',
			'message'=>'message_tab',
			'license'=>'license_tab'
		];
		$_aTab = [];
		foreach($_caTab as $key=>$aTab)
		{
			if($this->Employee->has_grant('config_'.$aTab))
			{
				$_aTab[$key] = $aTab;
			}
		}

		$city_ = get_cities_list();
        $cities = array();
        foreach ($city_ as $key=>$value)
        {
            $cities[$value] = $value;
        }
		$data['cities'] = $cities;

		$data['aTabs'] = $_aTab;
		$this->load->view("configs/manage", $data);
	}
		
	public function save_info()
	{
		$upload_success = $this->_handle_logo_upload();
		$upload_data = $this->upload->data();

		$batch_save_data = array(
			'company' => $this->input->post('company'),
			'address' => $this->input->post('address'),
			'phone' => $this->input->post('phone'),
			'email' => $this->input->post('email'),
			'fax' => $this->input->post('fax'),
			'website' => $this->input->post('website'),	
			'guide' => $this->input->post('guide'),
			'return_policy' => $this->input->post('return_policy')
		);
		
		if (!empty($upload_data['orig_name']))
		{
			// XSS file image sanity check
			if ($this->xss_clean($upload_data['raw_name'], TRUE) === TRUE)
			{
				$batch_save_data['company_logo'] = $upload_data['raw_name'] . $upload_data['file_ext'];
			}
		}
		
		$result = $this->Appconfig->batch_save($batch_save_data);
		$success = $upload_success && $result ? TRUE : FALSE;
		$message = $this->lang->line('config_saved_' . ($success ? '' : 'un') . 'successfully');
		$message = $upload_success ? $message : strip_tags($this->upload->display_errors());

		echo json_encode(array('success' => $success, 'message' => $message));
	}
		
	public function save_general()
	{

		$batch_save_data = array(
			'theme' => $this->input->post('theme'),
			'default_tax_1_rate' => parse_decimals($this->input->post('default_tax_1_rate')),
			'default_tax_1_name' => $this->input->post('default_tax_1_name'),
			'default_tax_2_rate' => parse_decimals($this->input->post('default_tax_2_rate')),
			'default_tax_2_name' => $this->input->post('default_tax_2_name'),
			'tax_included' => $this->input->post('tax_included') != NULL,
			'receiving_calculate_average_price' => $this->input->post('receiving_calculate_average_price') != NULL,
			'lines_per_page' => $this->input->post('lines_per_page'),
			'default_sales_discount' => $this->input->post('default_sales_discount'),
			'notify_horizontal_position' => $this->input->post('notify_horizontal_position'),
			'notify_vertical_position' => $this->input->post('notify_vertical_position'),
			'custom1_name' => $this->input->post('custom1_name'),
			'custom2_name' => $this->input->post('custom2_name'),
			'custom3_name' => $this->input->post('custom3_name'),
			'custom4_name' => $this->input->post('custom4_name'),
			'custom5_name' => $this->input->post('custom5_name'),
			'custom6_name' => $this->input->post('custom6_name'),
			'custom7_name' => $this->input->post('custom7_name'),
			'custom8_name' => $this->input->post('custom8_name'),
			'custom9_name' => $this->input->post('custom9_name'),
			'custom10_name' => $this->input->post('custom10_name'),
			'config_partner'=>$this->input->post('config_partner'),
			'default_city'=>$this->input->post('default_city'),
			'api_url'=>$this->input->post('api_url'),
			'statistics' => $this->input->post('statistics') != NULL,
		);
		
		$result = $this->Appconfig->batch_save($batch_save_data);
		$success = $result ? TRUE : FALSE;

		echo json_encode(array('success' => $success, 'message' => $this->lang->line('config_saved_' . ($success ? '' : 'un') . 'successfully')));
	}

	public function save_prescription()
	{

		$batch_save_data = [
			'has_prescription'=>$this->input->post('has_prescription'),
			'test_header'=>$this->input->post('test_header'),
			'ten_phong_kham'=>$this->input->post('ten_phong_kham'),
			'lien_he'=>$this->input->post('lien_he'),
			'hien_thi_VA'=>$this->input->post('hien_thi_VA'),
			'hien_thi_kinh_cu'=>$this->input->post('hien_thi_kinh_cu'),
			'loai_mat_kinh'=>$this->input->post('loai_mat_kinh'),
			'hien_thi_ten_bac_si'=>$this->input->post('hien_thi_ten_bac_si'),
			'ten_bac_si'=>$this->input->post('ten_bac_si'),
			'test_display_nurse'=>$this->input->post('test_display_nurse'),
			'test_display_kxv'=>$this->input->post('test_display_kxv')
		];
		
		$result = $this->Appconfig->batch_save($batch_save_data);
		$success = $result ? TRUE : FALSE;

		echo json_encode(array('success' => $success, 'message' => $this->lang->line('config_saved_' . ($success ? '' : 'un') . 'successfully')));
	}
	
	public function check_number_locale()
	{
		$number_locale = $this->input->post('number_locale');
		$fmt = new \NumberFormatter($number_locale, \NumberFormatter::CURRENCY);
		$currency_symbol = empty($this->input->post('currency_symbol')) ? $fmt->getSymbol(\NumberFormatter::CURRENCY_SYMBOL) : $this->input->post('currency_symbol');
		if ($this->input->post('thousands_separator') == "false")
		{
			$fmt->setAttribute(\NumberFormatter::GROUPING_SEPARATOR_SYMBOL, '');
		}
		$fmt->setSymbol(\NumberFormatter::CURRENCY_SYMBOL, $currency_symbol);
		$number_local_example = $fmt->format(1234567890.12300);
		echo json_encode(array(
			'success' => $number_local_example != FALSE,
			'number_locale_example' => $number_local_example,
			'currency_symbol' => $currency_symbol,
			'thousands_separator' => $fmt->getAttribute(\NumberFormatter::GROUPING_SEPARATOR_SYMBOL) != ''
		));
	}

	public function save_locale()
	{
		$exploded = explode(":", $this->input->post('language'));
		$batch_save_data = array(
			'currency_symbol' => $this->input->post('currency_symbol'),
			'language_code' => $exploded[0],
			'language' => $exploded[1],
			'timezone' => $this->input->post('timezone'),
			'dateformat' => $this->input->post('dateformat'),
			'timeformat' => $this->input->post('timeformat'),
			'thousands_separator' => $this->input->post('thousands_separator'),
			'number_locale' => $this->input->post('number_locale'),	
			'currency_decimals' => $this->input->post('currency_decimals'),
			'tax_decimals' => $this->input->post('tax_decimals'),
			'quantity_decimals' => $this->input->post('quantity_decimals'),
			'country_codes' => $this->input->post('country_codes'),
			'payment_options_order' => $this->input->post('payment_options_order')
		);
	
		$result = $this->Appconfig->batch_save($batch_save_data);
		$success = $result ? TRUE : FALSE;

		echo json_encode(array('success' => $success, 'message' => $this->lang->line('config_saved_' . ($success ? '' : 'un') . 'successfully')));
	}

	public function save_email()
	{
		$password = '';

		if($this->_check_encryption())
		{
			$password = $this->encryption->encrypt($this->input->post('smtp_pass'));
		}
		
		$batch_save_data = array(
			'protocol' => $this->input->post('protocol'),
			'mailpath' => $this->input->post('mailpath'),
			'smtp_host' => $this->input->post('smtp_host'),
			'smtp_user' => $this->input->post('smtp_user'),
			'smtp_pass' => $password,
			'smtp_port' => $this->input->post('smtp_port'),
			'smtp_timeout' => $this->input->post('smtp_timeout'),
			'smtp_crypto' => $this->input->post('smtp_crypto')
		);

		$result = $this->Appconfig->batch_save($batch_save_data);
		$success = $result ? TRUE : FALSE;

		echo json_encode(array('success' => $success, 'message' => $this->lang->line('config_saved_' . ($success ? '' : 'un') . 'successfully')));
	}

	public function save_message()
	{
		$password = '';

		if($this->_check_encryption())
		{
			$password = $this->encryption->encrypt($this->input->post('msg_pwd'));
		}

		$batch_save_data = array(	
			'msg_msg' => $this->input->post('msg_msg'),
			'msg_uid' => $this->input->post('msg_uid'),
			'msg_pwd' => $password,
			'msg_src' => $this->input->post('msg_src')
		);
	
		$result = $this->Appconfig->batch_save($batch_save_data);
		$success = $result ? TRUE : FALSE;

		echo json_encode(array('success' => $success, 'message' => $this->lang->line('config_saved_' . ($success ? '' : 'un') . 'successfully')));
	}
	
	public function stock_locations() 
	{
		$stock_locations = $this->Stock_location->get_all()->result_array();
		
		$stock_locations = $this->xss_clean($stock_locations);

		$this->load->view('partial/stock_locations', array('stock_locations' => $stock_locations));
	} 
	
	private function _clear_session_state()
	{
		$this->load->library('sale_lib');
		$this->sale_lib->clear_sale_location();
		$this->sale_lib->clear_all();
		$this->load->library('receiving_lib');
		$this->receiving_lib->clear_stock_source();
		$this->receiving_lib->clear_stock_destination();
		$this->receiving_lib->clear_all();
	}
	
	public function save_locations() 
	{
		$this->db->trans_start();
		
		$deleted_locations = $this->Stock_location->get_allowed_locations();
		foreach($this->input->post() as $key => $value)
		{
			if (strstr($key, 'stock_location'))
			{
				$location_id = preg_replace("/.*?_(\d+)$/", "$1", $key);
				unset($deleted_locations[$location_id]);
				// save or update
				$location_data = array('location_name' => $value);
				if ($this->Stock_location->save($location_data, $location_id))
				{
					$this->_clear_session_state();
				}
			}
		}

		// all locations not available in post will be deleted now
		foreach ($deleted_locations as $location_id => $location_name)
		{
			$this->Stock_location->delete($location_id);
		}

		$this->db->trans_complete();
		
		$success = $this->db->trans_status();
		
		echo json_encode(array('success' => $success, 'message' => $this->lang->line('config_saved_' . ($success ? '' : 'un') . 'successfully')));
	}

    public function save_barcode()
    {
        $batch_save_data = array(
			/**
			 * General
			 */
			'Phone_Barcode' => $this->input->post('Phone_Barcode'),
			'Slogan_Barcode' => $this->input->post('Slogan_Barcode'),
			'Location_Barcode' => $this->input->post('Location_Barcode'),
			/**
			 * Gọng
			 */
			'barcode_type' => $this->input->post('barcode_type'),
			'barcode_quality' => $this->input->post('barcode_quality'),
			'barcode_width' => $this->input->post('barcode_width'),
			'barcode_height' => $this->input->post('barcode_height'),
			'barcode_font' => $this->input->post('barcode_font'),
			'barcode_font_size' => $this->input->post('barcode_font_size'),
			'barcode_first_row' => $this->input->post('barcode_first_row'),
			'barcode_second_row' => $this->input->post('barcode_second_row'),
			'barcode_third_row' => $this->input->post('barcode_third_row'),
			'barcode_num_in_row' => $this->input->post('barcode_num_in_row'),
			'barcode_page_width' => $this->input->post('barcode_page_width'),
			'barcode_page_cellspacing' => $this->input->post('barcode_page_cellspacing'),
			'barcode_generate_if_empty' => $this->input->post('barcode_generate_if_empty') != NULL,
			'barcode_content' => $this->input->post('barcode_content'),
			'GBarcode'=>$this->input->post('GBarcode'),
			
		
			/** Lens */
			'lens_barcode_type' => $this->input->post('lens_barcode_type'),
			'lens_barcode_quality' => $this->input->post('lens_barcode_quality'),
			'lens_barcode_width' => $this->input->post('lens_barcode_width'),
			'lens_barcode_height' => $this->input->post('lens_barcode_height'),
			'lens_barcode_font' => $this->input->post('lens_barcode_font'),
			'lens_barcode_font_size' => $this->input->post('lens_barcode_font_size'),
			'lens_barcode_first_row' => $this->input->post('lens_barcode_first_row'),
			'lens_barcode_second_row' => $this->input->post('lens_barcode_second_row'),
			'lens_barcode_third_row' => $this->input->post('lens_barcode_third_row'),
			'lens_barcode_num_in_row' => $this->input->post('lens_barcode_num_in_row'),
			'lens_barcode_page_width' => $this->input->post('lens_barcode_page_width'),
			'lens_barcode_page_cellspacing' => $this->input->post('lens_barcode_page_cellspacing'),
			'lens_barcode_generate_if_empty' => $this->input->post('lens_barcode_generate_if_empty') != NULL,
			'lens_barcode_content' => $this->input->post('lens_barcode_content'),
			'MBarcode'=>$this->input->post('MBarcode'),
			
			/** Thuoc */
			't_barcode_type' => $this->input->post('t_barcode_type'),
			't_barcode_quality' => $this->input->post('t_barcode_quality'),
			't_barcode_width' => $this->input->post('t_barcode_width'),
			't_barcode_height' => $this->input->post('t_barcode_height'),
			't_barcode_font' => $this->input->post('t_barcode_font'),
			't_barcode_font_size' => $this->input->post('t_barcode_font_size'),
			't_barcode_first_row' => $this->input->post('t_barcode_first_row'),
			't_barcode_second_row' => $this->input->post('t_barcode_second_row'),
			't_barcode_third_row' => $this->input->post('t_barcode_third_row'),
			't_barcode_num_in_row' => $this->input->post('t_barcode_num_in_row'),
			't_barcode_page_width' => $this->input->post('t_barcode_page_width'),
			't_barcode_page_cellspacing' => $this->input->post('t_barcode_page_cellspacing'),
			't_barcode_generate_if_empty' => $this->input->post('t_barcode_generate_if_empty') != NULL,
			't_barcode_content' => $this->input->post('t_barcode_content'),
			'Thuoc'=>$this->input->post('Thuoc'),
			
			
			/** Gong2 */
			'g2_barcode_type' => $this->input->post('g2_barcode_type'),
			'g2_barcode_quality' => $this->input->post('g2_barcode_quality'),
			'g2_barcode_width' => $this->input->post('g2_barcode_width'),
			'g2_barcode_height' => $this->input->post('g2_barcode_height'),
			'g2_barcode_font' => $this->input->post('g2_barcode_font'),
			'g2_barcode_font_size' => $this->input->post('g2_barcode_font_size'),
			'g2_barcode_first_row' => $this->input->post('g2_barcode_first_row'),
			'g2_barcode_second_row' => $this->input->post('g2_barcode_second_row'),
			'g2_barcode_third_row' => $this->input->post('g2_barcode_third_row'),
			'g2_barcode_num_in_row' => $this->input->post('g2_barcode_num_in_row'),
			'g2_barcode_page_width' => $this->input->post('g2_barcode_page_width'),
			'g2_barcode_page_cellspacing' => $this->input->post('g2_barcode_page_cellspacing'),
			'g2_barcode_generate_if_empty' => $this->input->post('g2_barcode_generate_if_empty') != NULL,
			'g2_barcode_content' => $this->input->post('g2_barcode_content'),
			'G1Barcode'=>$this->input->post('G1Barcode')
        );
        
        $result = $this->Appconfig->batch_save($batch_save_data);
        $success = $result ? TRUE : FALSE;
		
        echo json_encode(array('success' => $success, 'message' => $this->lang->line('config_saved_' . ($success ? '' : 'un') . 'successfully')));
    }
    
    public function save_receipt()
    {
    	$batch_save_data = array (
			'receipt_template' => $this->input->post('receipt_template'),
			'receipt_show_taxes' => $this->input->post('receipt_show_taxes') != NULL,
			'receipt_show_total_discount' => $this->input->post('receipt_show_total_discount') != NULL,
			'receipt_show_description' => $this->input->post('receipt_show_description') != NULL,
			'receipt_show_serialnumber' => $this->input->post('receipt_show_serialnumber') != NULL,
			'print_silently' => $this->input->post('print_silently') != NULL,
			'print_header' => $this->input->post('print_header') != NULL,
			'print_footer' => $this->input->post('print_footer') != NULL,
			'print_top_margin' => $this->input->post('print_top_margin'),
			'print_left_margin' => $this->input->post('print_left_margin'),
			'print_bottom_margin' => $this->input->post('print_bottom_margin'),
			'print_right_margin' => $this->input->post('print_right_margin'),
			'receipt_printer'=>$this->input->post('receipt_printer'),
			'takings_printer'=>$this->input->post('takings_printer')
		);

    	$result = $this->Appconfig->batch_save($batch_save_data);
    	$success = $result ? TRUE : FALSE;

    	echo json_encode(array('success' => $success, 'message' => $this->lang->line('config_saved_' . ($success ? '' : 'un') . 'successfully')));
    }

    public function save_invoice()
    {
    	$batch_save_data = array (
			'invoice_enable' => $this->input->post('invoice_enable') != NULL,
			'sales_invoice_format' => $this->input->post('sales_invoice_format'),
			'recv_invoice_format' => $this->input->post('recv_invoice_format'),
			'invoice_default_comments' => $this->input->post('invoice_default_comments'),
			'invoice_email_message' => $this->input->post('invoice_email_message')
		);

    	$result = $this->Appconfig->batch_save($batch_save_data);
    	$success = $result ? TRUE : FALSE;

    	echo json_encode(array('success' => $success, 'message' => $this->lang->line('config_saved_' . ($success ? '' : 'un') . 'successfully')));
    }

	public function remove_logo()
	{
		$result = $this->Appconfig->batch_save(array('company_logo' => ''));
		
		echo json_encode(array('success' => $result));
	}
    
    private function _handle_logo_upload()
    {
    	$this->load->helper('directory');

    	// load upload library
    	$config = array('upload_path' => './uploads/',
    			'allowed_types' => 'gif|jpg|png',
    			'max_size' => '1024',
    			'max_width' => '800',
    			'max_height' => '680',
    			'file_name' => 'company_logo');
    	$this->load->library('upload', $config);
    	$this->upload->do_upload('company_logo');

    	return strlen($this->upload->display_errors()) == 0 || !strcmp($this->upload->display_errors(), '<p>'.$this->lang->line('upload_no_file_selected').'</p>');
	}
	
	private function _check_encryption()
	{
		$encryption_key = $this->config->item('encryption_key');
		
		// check if the encryption_key config item is the default one
		if($encryption_key == '' || $encryption_key == 'YOUR KEY')
		{
			// Config path
			$config_path = APPPATH . 'config/config.php';
			
			// Open the file
			$config = file_get_contents($config_path);
			
			// $key will be assigned a 32-byte (256-bit) hex-encoded random key
			$key = bin2hex($this->encryption->create_key(32));
			
			// replace the empty placeholder with a real randomly generated encryption key
			if($encryption_key == '')
			{
				$config = str_replace("['encryption_key'] = '';", "['encryption_key'] = '" . $key . "';", $config);
			}
			else
			{
				$config = str_replace("['encryption_key'] = 'YOUR KEY';", "['encryption_key'] = '" . $key . "';", $config);				
			}

			// set the encryption key in the config item
			$this->config->set_item('encryption_key', $key);

			// Write the new config.php file
			$handle = fopen($config_path, 'w+');

			// Chmod the file
			@chmod($config_path, 0777);
			
			$result = FALSE;

			// Verify file permissions
			if(is_writable($config_path))
			{
				// Write the file
				$result = (fwrite($handle, $config) === FALSE) ? FALSE : TRUE;
			}
			
			// Chmod the file
			@chmod($config_path, 0444);
			
			fclose($handle);

			return $result;
		}
		
		return TRUE;
	}
    
    public function backup_db()
    {
    	$employee_id = $this->Employee->get_logged_in_employee_info()->person_id;
    	if($this->Employee->has_module_grant('config', $employee_id))
    	{
    		$this->load->dbutil();

    		$prefs = array(
				'format' => 'zip',
				'filename' => 'ospos.sql'
    		);
    		 
    		$backup = $this->dbutil->backup($prefs);
    		 
			$file_name = 'ospos-' . date("Y-m-d-H-i-s") .'.zip';
    		$save = 'uploads/' . $file_name;
    		$this->load->helper('download');
    		while(ob_get_level())
			{
    			ob_end_clean();
    		}

    		force_download($file_name, $backup);
    	}
    	else 
    	{
    		redirect('no_access/config');
    	}
    }

	public function save_products()
	{
		$_data = array(
				'iKindOfLens'=>$this->input->post('iKindOfLens'),
				'filter_sun_glasses'=>$this->input->post('filter_sun_glasses'),
				'filter_contact_lens'=>$this->input->post('filter_contact_lens'),
				'filter'=>$this->input->post('filter'),
				'filter_other'=>$this->input->post('other_filter')
			);

		$result = $this->Appconfig->batch_save($_data);
		
		echo json_encode(array('success' => 1, 'message' =>'Đã lưu thành công'));
	}
	/**
	 * Bổ sung function để phân quyền
	 */
	public function info_tab()
	{
		return true;
	}
	public function general_tab()
	{
		return true;
	}
	public function product_tab()
	{
		return true;
	}

	public function locale_tab()
	{
		return true;
	}
	public function barcode_tab()
	{
		return true;
	}
	public function stock_tab()
	{
		return true;
	}
	public function ctv_tab()
	{
		return true;
	}
	public function receipt_tab()
	{
		return true;
	}

	public function invoice_tab()
	{
		return true;
	}

	public function email_tab()
	{
		return true;
	}

	public function message_tab()
	{
		return true;
	}
	public function license_tab()
	{
		return true;
	}
	public function prescription_tab()
	{
		return true;
	}
}
?>
