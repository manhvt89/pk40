<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once("Secure_Controller.php");

class Roles extends Secure_Controller
{
	public function __construct()
	{
		parent::__construct('roles');

		$this->load->library('sale_lib');
		$this->load->library('barcode_lib');
		$this->load->library('email_lib');
		$this->load->library('ciqrcode'); // Load QR Code library
		$this->config->load('qrcode'); // Load QR code config file;
		$this->logedUser_type = $this->session->userdata('type');
		$this->logedUser_id = $this->session->userdata('person_id');
		$this->load->library('item_lib');
	}


	public function index()
	{
		$_aoRoles = $this->Module->get_roles()->result();
		$_aoRs = array();
		$i = 1;
		foreach($_aoRoles as $oRole)
		{
			$_aoR = new stdClass;
			$_aoR->stt = $i;
			$_aoR->name = $oRole->name;
			$_aoR->actions = '<a href="'.base_url('roles/edit/'.$oRole->role_uuid).'">Edit</a> | <a href="'.base_url('roles/view/'.$oRole->role_uuid).'">View</a>';
			$_aoRs[] = $_aoR;
			$i++;
		}

		$data['roles'] = json_encode($_aoRs);
		$this->load->view('roles/index', $data);
	}

	public function create()
	{

	}

	public function view($uuid = null)
	{
		if($uuid == null)
		{
			$data['theRole'] = new stdClass;
		} else {
			$_oTheRole = $this->Module->get_the_role_by_uuid($uuid);
			$_aoPermissions = $this->Module->get_grants_of_the_role($uuid)->result();
			$_aoAllPermissions = $this->Module->get_grants()->result();

			if(count($_aoAllPermissions) > 0)
			{

				foreach ($_aoAllPermissions as $key=>$value) {
					$value->flag = 0;
					$_aoAllPermissions[$key] = $value;
				}

				foreach($_aoAllPermissions as $key=>$value)
				{
					if(count($_aoPermissions) > 0)
					{
						foreach($_aoPermissions as $k=>$v)
						{
							if($value->permission_key == $v->permission_key)
							{
								$value->flag = 1;
								$_aoAllPermissions[$key] = $value;
							}
						}
					}
				}
			}
			
			//$data['theRole'] = json_encode($_aoTmp);
			$data['theRole'] = $_oTheRole;
			$data['permissions'] = $_aoAllPermissions;
			//var_dump($data['theRole'] );
		}
		$this->load->view('roles/view', $data);
		
	}

	public function edit()
	{

	}

	public function per_index()
	{
		$_aoPermissions = $this->Module->get_all_permissions()->result();
		$_aoPs = array();
		$i = 1;
		foreach($_aoPermissions as $oPermission)
		{
			$_aoP = new stdClass;
			$_aoP->stt = $i;
			$_aoP->name = $oPermission->name;
			$_aoP->module_key = $oPermission->module_key;
			$_aoP->permision_key = $oPermission->permission_key;
			$_aoP->actions = '<a href="'.base_url('roles/per_edit/'.$oPermission->permissions_uuid).'">Edit</a> | <a href="'.base_url('roles/per_view/'.$oPermission->permissions_uuid).'">View</a>';
			$_aoPs[] = $_aoP;
			$i++;
		}

		$data['permissions'] = json_encode($_aoPs);
		$this->load->view('roles/per_index', $data);
	}
	public function per_add()
	{
		$data = array();
		//Lấy danh sách các mô đun trong bảng modules
		$_aoManagedModules = $this->Module->get_all_modules()->result();
		//var_dump($_aoManagedModules);
		$_aModules = array();

		if(count($_aoManagedModules) > 0)
		{

			foreach($_aoManagedModules as $key=>$value)
			{
				//echo $value->module_key . '['.$key.']';
				$_pers = get_all_permissions_of_the_module($value->module_key);
				//var_dump($_pers);
				foreach($_pers as $k=>$v)
				{
					$_oTmp = new stdClass;
					$_oTmp->permission_key = $v;
					$_aModules[$value->module_key][] = $_oTmp;
				}
				
			}
		} else {
			$_aModules = array();
		}

		//var_dump($_aModules);
		
		// Lây danh sách cacs permision trong bảng permissions
		$_aoManagedPermissions = $this->Module->get_all_permissions()->result();
		$_aoTemp = array();

		foreach($_aoManagedPermissions as $key=>$value)
		{
			$_aoTemp[$value->module_key][] = $value;
		}
		//var_dump($_aoTemp);

		$_aaPermissions = array(); // tao mang dua tren _aModule
		if (count($_aModules) > 0) {
			foreach ($_aModules as $k=>$actions) {
				if (count($actions) > 0) {
					foreach ($actions as $j=>$action) {
						$_value = new stdClass();
						$_value->module_key = $k;
						$_value->module_id = $k;
						$_value->location_id = '';
						$_value->id = 0;
						$_value->flag = 0;
						$_value->name = '';
						$_value->permission_uuid = '';
						$_value->permission_key = $action->permission_key;
						$_aaPermissions[$k][] = $_value;
					}
				}
			}
		}

		if(count($_aaPermissions ) > 0)
		{
			foreach ($_aaPermissions as $k=>$actions) {
				if (!empty($_aoTemp[$k])) {
					foreach ($_aoTemp[$k] as $key=>$value) {
						foreach ($actions as $j=>$action) {
							if ($action->permission_key == $value->permission_key) {
								$value->flag = 1;
								$_aaPermissions[$k][$j] = $value;
							}
						}
					}
				}
			}
		}
				
		$data['modules'] = $_aaPermissions;
		$data['themodule'] = '';
		$this->load->view('roles/per_add', $data);
	}

	public function ajax_load_actions_by_module($module_key='')
	{
		$retrun = array();
		echo json_encode($retrun);
	}
	public function per_view($uuid)
	{
		if($uuid == null)
		{
			$data['thePermission'] = new stdClass;
			$data['fields'] = array();
		} else {
			$_aoFields = array(); // tao mang dua tren _aModule
			$_oThePermission = $this->Module->get_the_permission_by_uuid($uuid);

			if($_oThePermission == null)
			{
				echo 'Invalid Data';
				exit();
			}
			
			$_aoFields = $this->Module->get_fields_by_permission($_oThePermission->id)->result();
			//var_dump($_aoManagedPermissions);
			
			
			$data['thePermission'] = $_oThePermission;
			$data['fields'] = $_aoFields;
			
			//var_dump($data['permissions'] );
		}
		$this->load->view('roles/per_view', $data);	
	}
	public function per_edit()
	{

	}

	public function mod_index()
	{
		$_aoModules = $this->Module->get_all_modules()->result();
		$_aoMs = array();
		$i = 1;
		foreach($_aoModules as $oModule)
		{
			$_aoM = new stdClass;
			$_aoM->stt = $i;
			$_aoM->name = $oModule->name;
			$_aoM->module_key = $oModule->module_key;
			$_aoM->actions = '<a href="'.base_url('roles/mod_edit/'.$oModule->module_uuid).'">Edit</a> | <a href="'.base_url('roles/mod_view/'.$oModule->module_uuid).'">View</a>';
			$_aoMs[] = $_aoM;
			$i++;
		}

		$data['modules'] = json_encode($_aoMs);
		$this->load->view('roles/mod_index', $data);
	}
	public function mod_add()
	{
		$data = array();
		$_aoManagedModules = $this->Module->get_all_modules()->result();
		$_aAllModule = get_all_modules();
		$_aExcludeMOdule = $this->config->item('exclude_module');
		foreach($_aAllModule as $key=>$module)
		{
			if (count($_aoManagedModules) > 0) {
				foreach ($_aoManagedModules as $k=>$v) {
					if ($module == $v->module_key) {
						unset($_aoManagedModules[$k]);
						unset($_aAllModule[$key]);
					}
				}
			} else{
				break;
			}
		}

		foreach($_aAllModule as $key=>$module)
		{
			if (count($_aExcludeMOdule) > 0) {
				foreach ($_aExcludeMOdule as $k=>$v) {
					if ($module == $v) {
						unset($_aoManagedModules[$k]);
						unset($_aAllModule[$key]);
					}
				}
			} else{
				break;
			}
		}
		$modules = array();
		if(count($_aAllModule) > 0)
		{
			foreach($_aAllModule as $v)
			{
				$modules[$v] = $v;
			}
		}
		//var_dump($_aAllModule);
		$data['modules'] = $modules;
		$data['themodule'] = '';
		$this->load->view('roles/mod_add', $data);
	}

	public function mod_save()
	{
		$name = $this->input->post('mod_name');
		$key = $this->input->post('mod_key');

		$_aMod = array(
			'name'=>$name,
			'module_key'=>$key,
			'created_at'=>time(),
			'updated_at'=>0,
			'deleted_at'=>0,
			'code'=>$key,
			'sort'=> 10,
			'name_lang_key'=>$key,
			'desc_lang_key'=>$key
		);

		$this->Module->add_module($_aMod);

		redirect(base_url('roles/mod_index'));
	}
	public function mod_view($uuid = null)
	{
		if($uuid == null)
		{
			$data['theModule'] = new stdClass;
			$data['Permissions'] = array();
		} else {
			$_aPermissions = []; // tao mang dua tren _aModule
			$_oTheModule = $this->Module->get_the_module_by_uuid($uuid);
			//$_sModul
			$_pers = get_all_permissions_of_the_module($_oTheModule->module_key);
			//var_dump($_pers);
			//var_dump($_oTheModule->module_key);
			if(!empty($this->config->item('exclude_actions')[$_oTheModule->module_key])) //Nếu tồn tại
			{
				$_aExludeModules = MakeExludeModules($this->config->item('exclude_actions')[$_oTheModule->module_key],$_oTheModule->module_key);
				//var_dump($_aExludeModules);//die();
				$result = array_filter($_pers, function ($item) use ($_aExludeModules) {
					return !matchWithWildcards($item, $_aExludeModules);
				});
				
				$_pers = $result;//array_diff($_pers,$_aExludeModules);
				var_dump($_pers);die();
			}
			$_aoManagedPermissions = $this->Module->get_all_permissions_by_module_key($_oTheModule->module_key)->result();
			//var_dump($_aoManagedPermissions);
			if(!empty($_pers))
			{
				foreach($_pers as $key=>$value)
				{
					$_value = new stdClass();
						$_value->module_key = $_oTheModule->module_key;
						$_value->module_id = $_oTheModule->module_key;
						$_value->location_id = '';
						$_value->id = 0;
						$_value->flag = 0;
						$_value->name = '';
						$_value->permission_uuid = '';
						$_value->permission_key = $value;						
						$_aPermissions[$key] = $_value;
				}
			}
			//var_dump($_aPermissions);
			if(count($_aPermissions) > 0)
			{
				foreach($_aPermissions as $key=>$value)
				{
					if(!empty($_aoManagedPermissions))
					{
						foreach($_aoManagedPermissions as $k=>$v)
						{
							if($value->permission_key == $v->permission_key)
							{
								$v->flag = 1;
								$_aPermissions[$key] = $v;
								unset($_aoManagedPermissions[$k]);
								break;
							}
						}
					}
				}
			}
			if (!empty($_aoManagedPermissions)) {
				foreach ($_aoManagedPermissions as $k=>$v) {
					$v->flag = 1;
					$_aPermissions[] = $v;
				}
			}
			//var_dump($_aPermissions);
			
			$data['theModule'] = $_oTheModule;
			$data['permissions'] = $_aPermissions;
			
			//var_dump($data['permissions'] );
		}
		$this->load->view('roles/mod_view', $data);
	}
	public function mod_edit($uuid = null)
	{
		if($uuid == null)
		{
			$data['theRole'] = new stdClass;
		} else {
			$_oTheModule = $this->Module->get_the_module_by_uuid($uuid);
			
			$data['theModule'] = $_oTheModule;
			
			//var_dump($data['permissions'] );
		}
		$this->load->view('roles/mod_edit', $data);
	}

	public function mod_update()
	{
		$name = $this->input->post('mod_name');
		$id = $this->input->post('mod_id');

		$_aMod = array(
			'name'=>$name,
			'id'=>$id);
		
		$this->Module->edit_module($_aMod);

		redirect(base_url('roles/mod_index'));
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
		$ctvs = $this->Ctv->get_list();
		$this->sale_lib->set_ctv($ctvs);
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


	/*
	AJAX HERE
	*/

	public function save_permission_name()
	{
		$name = $this->input->post('name');
		$permission_key = $this->input->post('permission_key');
		$permission_id = $this->input->post('id');
		$module_key = $this->input->post('module_key');

		if($permission_id > 0)
		{
			$aPermission = array(
				'name'=>$name,
				/* 'permission_key'=>$permission_key,
				'module_key'=>$module_key,
				'module_id'=>$module_key, */
				'id'=>$permission_id
			);
			$ret = $this->Module->edit_permission($aPermission);
		} else{
			$aPermission = array(
				'name'=>$name,
				'permission_key'=>$permission_key,
				'module_key'=>$module_key,
				'module_id'=>$module_key,
				
			);
			$ret = $this->Module->add_permission($aPermission);

		}
		if($ret) 
		{
			echo "Cập nhật thành công";
		} else {
			echo "Cập nhật thất bại";
		}
	}

	public function switch_permission()
	{
		$role_id = $this->input->post('role_id');
		$mode = $this->input->post('mode');
		$permission_id = $this->input->post('permission_id');
		if(empty($role_id) || empty($permission_id))
		{
			echo 'Not Validate';
			exit();
		}
		$_aT = array(
			'role_id'=>$role_id,
			'permission_id'=>$permission_id
		);
		if($mode == 'false')
		{
			//delete permission
			echo 'Deactive the permission';
			$this->Module->del_permission_to_grants($_aT);
		} else {
			//insert permission
			echo 'Active the permission';
			$this->Module->add_permission_to_grants($_aT);
		}
	}

	public function switch_action()
	{
		$mode = $this->input->post('mode');
		$permission_id = $this->input->post('permission_id');
		$permission_key = $this->input->post('permission_key');
		$module_id = $this->input->post('module_id');
		$module_key = $this->input->post('module_key');

		$_aT = array(
			'module_id'=>$module_id,
			'permission_key'=>$permission_key,
			'module_key'=>$module_key,
			'location_id'=>1,
			'name'=>''
		);
		
		if($permission_id==null)
		{
			echo 'Not Validate';
			exit();
		}
		if($mode == 'false')
		{
			//delete permission
			echo 'Deactive the permission';
			$this->Module->del_permission_from_permissions($permission_id);
		} else {
			//insert permission
			echo 'Active the permission';
			$this->Module->add_permission_to_permissions($_aT);
		}
	}


	/* public function edit($sale_id)
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
	} */
}
?>
