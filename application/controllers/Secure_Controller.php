<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Secure_Controller extends CI_Controller 
{
	/*
	* Controllers that are considered secure extend Secure_Controller, optionally a $module_id can
	* be set to also check if a user can access a particular module in the system.
	*/
	public function __construct($module_id = NULL, $submodule_id = NULL)
	{
		parent::__construct();
		//$this->session->sess_destroy();
		$this->load->model('Employee');
		$model = $this->Employee;

		if(!$model->is_logged_in())
		{
			redirect('login');
		}

		//$this->router->fetch_class();
		$action = $this->router->fetch_method();
		//echo $action;
		$task = $module_id."_".$action;
		$this->track_page($module_id, $action);
		//echo $module_id . ' |' . $submodule_id;
		$_astrManagedActions = $model->get_actions_by_module($module_id); // Cacs action duoc quan ly boi he thong phan quyen. CHỉ kiểm soát các actions này;
		//var_dump($_astrManagedActions);die();
		if(in_array($task,$_astrManagedActions))
		{
			if(!$model->has_grant($task))
			{
				redirect('no_access/' . $module_id . '/' . $submodule_id);
			}
		}
		$logged_in_employee_info = $model->get_logged_in_employee_info(); //get login_id in session
		//var_dump($logged_in_employee_info->type);die();
		if($logged_in_employee_info->type != 2)
		{
			if(!$model->has_module_grant($module_id, $logged_in_employee_info->person_id) || 
				(isset($submodule_id) && !$model->has_module_grant($submodule_id, $logged_in_employee_info->person_id)))
			{
				redirect('no_access/' . $module_id . '/' . $submodule_id);
			}
			//Thêm bởi ManhjVT: Nếu không có quyền manager (_index) thì không thể hiển thị menu list all
			//$_aoAllowed_Modules = $this->Module->get_allowed_modules($logged_in_employee_info->person_id)->result();
			//var_dump($_aoAllowed_Modules);
			$_aoAllowed_Modules = $model->get_allowed_modules();
			$data['allowed_modules'] = $_aoAllowed_Modules;
			//$data['allowed_modules'] = $this->Module->get_allowed_modules($logged_in_employee_info->person_id);
		} else{
			//echo $logged_in_employee_info->type;
			//Nếu là bác sĩ, cấp quyền (Khám, Bệnh, Đơn Thuốc. Xem các đơn hàng liên quan đến đơn khám của bác sĩ.
			//$data['allowed_modules'] = $this->Module->get_allowed_modules($logged_in_employee_info->person_id)->result();
			$_aoAllowed_Modules = $model->get_allowed_modules();
			$data['allowed_modules'] = $_aoAllowed_Modules;
		}

		//var_dump($data['allowed_modules']);

		// load up global data visible to all the loaded views
		//$data['allowed_modules'] = $this->Module->get_allowed_modules($logged_in_employee_info->person_id);
		
		//var_dump ($data['allowed_modules']);
		$data['user_info'] = $logged_in_employee_info;
		$data['controller_name'] = $module_id;

		$csrf = array(
			'name' => $this->security->get_csrf_token_name(),
			'hash' => $this->security->get_csrf_hash()
		);
		$data['csrf'] = $csrf;
		$this->logedUser_type = $this->session->userdata('type');
		$this->logedUser_id = $this->session->userdata('person_id');

		$this->load->vars($data);
	}
	
	/*
	* Internal method to do XSS clean in the derived classes
	*/
	protected function xss_clean($str, $is_image = FALSE)
	{
		// This setting is configurable in application/config/config.php.
		// Users can disable the XSS clean for performance reasons
		// (cases like intranet installation with no Internet access)
		if($this->config->item('ospos_xss_clean') == FALSE)
		{
			return $str;
		}
		else
		{
			return $this->security->xss_clean($str, $is_image);
		}
	}

	protected function track_page($path, $page)
	{
		if(get_instance()->Appconfig->get('statistics'))
		{
			//$this->load->library('tracking_lib');

			if(empty($path))
			{
				$path = 'home';
				$page = 'home';
			}

			//$this->tracking_lib->track_page('controller/' . $path, $page);
		}
	}

	protected function track_event($category, $action, $label, $value = NULL)
	{
		if(get_instance()->Appconfig->get('statistics'))
		{
			//$this->load->library('tracking_lib');

			//$this->tracking_lib->track_event($category, $action, $label, $value);
		}
	}

	public function numeric($str)
	{
        $str = str_replace(',','',$str);
		return parse_decimals($str);
		
	}

	public function numeric_zero($str)
	{
        if($str == '')
			$str = 0;
		$str = str_replace(',','',$str);
		
		if(parse_decimals($str) < 0)
		{
			return FALSE;
		} else {
			return parse_decimals($str);
		}
	}



	public function check_numeric()
	{
		$result = TRUE;

		foreach($this->input->get() as $str)
		{
			$result = parse_decimals($str);
		}

		echo $result !== FALSE ? 'true' : 'false';
	}


	// this is the basic set of methods most OSPOS Controllers will implement
	public function index() { return FALSE; }
	public function search() { return FALSE; }
	public function suggest_search() { return FALSE; }
	public function view($data_item_id = -1) { return FALSE; }
	public function save($data_item_id = -1) { return FALSE; }
	public function delete() { return FALSE; }

}
?>