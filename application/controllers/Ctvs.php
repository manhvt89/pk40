<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once("Persons.php");

class Ctvs extends Persons
{
	public function __construct()
	{
		parent::__construct('ctvs');
	}
	
	public function index()
	{
		$data['table_headers'] = $this->xss_clean(get_ctv_manage_table_headers());
		$this->load->view('ctv/manage', $data);
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
			//var_dump($person);
			$data_rows[] = get_ctv_data_row($person, $this);
		}

		$data_rows = $this->xss_clean($data_rows);

		echo json_encode(array('total' => $total_rows, 'rows' => $data_rows));
	}
	
	/*
	Gives search suggestions based on what is being searched for
	*/
	public function suggest_search()
	{
		$suggestions = $this->xss_clean($this->Employee->get_search_suggestions($this->input->post('term')));

		echo json_encode($suggestions);
	}
	
	/*
	Loads the employee edit form
	*/
	public function view($employee_id = -1)
	{
		$person_info = $this->Employee->get_info($employee_id);
		foreach(get_object_vars($person_info) as $property => $value)
		{
			$person_info->$property = $this->xss_clean($value);
		}
		$person_info->first_name = get_fullname($person_info->first_name, $person_info->last_name);

		$data['person_info'] = $person_info;
        $city_ = get_cities_list();
        $cities = array();
        foreach ($city_ as $key=>$value)
        {
            $cities[$value] = $value;
        }
        $data['city'] = $person_info->city;
        if($data['city'] == '' || $data['city'] == 'HN')
        {
            $data['city'] = $this->config->item('default_city');
        }
        $data['cities'] = $cities;

		$this->load->view("ctv/_form", $data);
	}
	
	/*
	Inserts/updates an employee
	*/
	public function save($employee_id = -1)
	{

		$_firstname = $this->input->post('first_name');
		$_aName = extract_fullname($_firstname);
		$person_data = array(
			'first_name' => mb_convert_case($_aName['firstname'], MB_CASE_TITLE, "UTF-8"),
			'last_name' => mb_convert_case($_aName['lastname'], MB_CASE_TITLE, "UTF-8"),
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
		//Password has been changed OR first time password set
		if($this->input->post('password') != '')
		{
			$employee_data = array(
				'username' => $this->input->post('username'),
				'password' => password_hash($this->input->post('password'), PASSWORD_DEFAULT),
				'hash_version' => 2,
				'code' => $this->input->post('code') == ''? 'CTV'.time() : $this->input->post('code'),
				'comission_rate' => $this->input->post('comission_rate')
			);
		}
		else //Password not changed
		{
			$employee_data = [
				'username' => $this->input->post('username'),
				'code' => $this->input->post('code') == ''? 'CTV'.time() : $this->input->post('code'),
				'comission_rate' => $this->input->post('comission_rate')
			];
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
	This deletes employees from the employees table
	*/
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
}
?>