<?php
class Employee extends Person
{
	/*
	Determines if a given person_id is an employee
	*/
	public function exists($person_id)
	{
		$this->db->from('employees');	
		$this->db->join('people', 'people.person_id = employees.person_id');
		$this->db->where('employees.person_id', $person_id);

		return ($this->db->get()->num_rows() == 1);
	}	

	/*
	Gets total of rows
	*/
	public function get_total_rows()
	{
		$this->db->from('employees');
		$this->db->where('deleted', 0);
		$this->db->where('type', 1);
		return $this->db->count_all_results();
	}

	/*
	Returns all the employees
	*/
	public function get_all($limit = 10000, $offset = 0)
	{
		$this->db->from('employees');
		$this->db->where('deleted', 0);
		$this->db->where('type', 1);
		$this->db->join('people', 'employees.person_id = people.person_id');			
		$this->db->order_by('last_name', 'asc');
		$this->db->limit($limit);
		$this->db->offset($offset);

		return $this->db->get();		
	}
	
	/*
	Gets information about a particular employee
	*/
	public function get_info($employee_id)
	{
		$this->db->from('employees');	
		$this->db->join('people', 'people.person_id = employees.person_id');
		$this->db->where('employees.person_id', $employee_id);
		$query = $this->db->get();

		if($query->num_rows() == 1)
		{
			return $query->row();
		}
		else
		{
			//Get empty base parent object, as $employee_id is NOT an employee
			$person_obj = parent::get_info(-1);

			//Get all the fields from employee table
			//append those fields to base parent object, we we have a complete empty object
			foreach($this->db->list_fields('employees') as $field)
			{
				$person_obj->$field = '';
			}

			return $person_obj;
		}
	}

	/*
	Gets information about multiple employees
	*/
	public function get_multiple_info($employee_ids)
	{
		$this->db->from('employees');
		$this->db->join('people', 'people.person_id = employees.person_id');		
		$this->db->where_in('employees.person_id', $employee_ids);
		$this->db->order_by('last_name', 'asc');

		return $this->db->get();		
	}

	/*
	Inserts or updates an employee
	*/
	public function save_employee(&$person_data, &$employee_data, &$grants_data, $employee_id = FALSE)
	{
		$success = FALSE;

		//Run these queries as a transaction, we want to make sure we do all or nothing
		$this->db->trans_start();

		if(parent::save($person_data, $employee_id))
		{
			if(!$employee_id || !$this->exists($employee_id))
			{
				$employee_data['person_id'] = $employee_id = $person_data['person_id'];
				$success = $this->db->insert('employees', $employee_data);
			}
			else
			{
				$this->db->where('person_id', $employee_id);
				$success = $this->db->update('employees', $employee_data);
			}

			//We have either inserted or updated a new employee, now lets set permissions. 
			if($success)
			{
				//First lets clear out any grants the employee currently has.
				$success = $this->db->delete('user_roles', array('user_id' => $employee_id));
				
				//Now insert the new grants
				if($success)
				{
					foreach($grants_data as $role_id)
					{
						$success = $this->db->insert('user_roles', array('role_id' => $role_id, 'user_id' => $employee_id));
					}
				}
			}
		}

		$this->db->trans_complete();

		$success &= $this->db->trans_status();

		return $success;
	}

	/*
	Deletes one employee
	*/
	public function delete($employee_id)
	{
		$success = FALSE;

		//Don't let employees delete theirself
		if($employee_id == $this->get_logged_in_employee_info()->person_id)
		{
			return FALSE;
		}

		//Run these queries as a transaction, we want to make sure we do all or nothing
		$this->db->trans_start();

		//Delete permissions
		if($this->db->delete('grants', array('person_id' => $employee_id)))
		{	
			$this->db->where('person_id', $employee_id);
			$success = $this->db->update('employees', array('deleted' => 1));
		}

		$this->db->trans_complete();

		return $success;
	}

	/*
	Deletes a list of employees
	*/
	public function delete_list($employee_ids)
	{
		$success = FALSE;

		//Don't let employees delete theirself
		if(in_array($this->get_logged_in_employee_info()->person_id, $employee_ids))
		{
			return FALSE;
		}

		//Run these queries as a transaction, we want to make sure we do all or nothing
		$this->db->trans_start();

		$this->db->where_in('person_id', $employee_ids);
		//Delete permissions
		if($this->db->delete('grants'))
		{
			//delete from employee table
			$this->db->where_in('person_id', $employee_ids);
			$success = $this->db->update('employees', array('deleted' => 1));
		}

		$this->db->trans_complete();

		return $success;
 	}

	/*
	Get search suggestions to find employees
	*/
	public function get_search_suggestions($search, $limit = 5)
	{
		$suggestions = array();

		$this->db->from('employees');
		$this->db->join('people', 'employees.person_id = people.person_id');
		$this->db->group_start();
			$this->db->like('first_name', $search);
			$this->db->or_like('last_name', $search); 
			$this->db->or_like('CONCAT(first_name, " ", last_name)', $search);
		$this->db->group_end();
		$this->db->where('deleted', 0);
		$this->db->where('type', 1);
		$this->db->order_by('last_name', 'asc');
		foreach($this->db->get()->result() as $row)
		{
			$suggestions[] = array('value' => $row->person_id, 'label' => $row->first_name.' '.$row->last_name);
		}

		$this->db->from('employees');
		$this->db->join('people', 'employees.person_id = people.person_id');
		$this->db->where('deleted', 0);
		$this->db->where('type', 1);
		$this->db->like('email', $search);
		$this->db->order_by('email', 'asc');
		foreach($this->db->get()->result() as $row)
		{
			$suggestions[] = array('value' => $row->person_id, 'label' => $row->email);
		}

		$this->db->from('employees');
		$this->db->join('people', 'employees.person_id = people.person_id');
		$this->db->where('deleted', 0);
		$this->db->where('type', 1);
		$this->db->like('username', $search);
		$this->db->order_by('username', 'asc');
		foreach($this->db->get()->result() as $row)
		{
			$suggestions[] = array('value' => $row->person_id, 'label' => $row->username);
		}

		$this->db->from('employees');
		$this->db->join('people', 'employees.person_id = people.person_id');
		$this->db->where('deleted', 0);
		$this->db->where('type', 1);
		$this->db->like('phone_number', $search);
		$this->db->order_by('phone_number', 'asc');
		foreach($this->db->get()->result() as $row)
		{
			$suggestions[] = array('value' => $row->person_id, 'label' => $row->phone_number);
		}

		//only return $limit suggestions
		if($suggestions > $limit)
		{
			$suggestions = array_slice($suggestions, 0, $limit);
		}

		return $suggestions;
	}

 	/*
	Gets rows
	*/
	public function get_found_rows($search)
	{
		$this->db->from('employees');
		$this->db->join('people', 'employees.person_id = people.person_id');
		$this->db->group_start();
			$this->db->like('first_name', $search);
			$this->db->or_like('last_name', $search);
			$this->db->or_like('email', $search);
			$this->db->or_like('phone_number', $search);
			$this->db->or_like('username', $search);
			$this->db->or_like('CONCAT(first_name, " ", last_name)', $search);
		$this->db->group_end();
		$this->db->where('deleted', 0);
		$this->db->where('type', 1);

		return $this->db->get()->num_rows();
	}

	/*
	Performs a search on employees
	*/
	public function search($search, $rows = 0, $limit_from = 0, $sort = 'last_name', $order = 'asc')
	{
		$this->db->from('employees');
		$this->db->join('people', 'employees.person_id = people.person_id');
		$this->db->group_start();
			$this->db->like('first_name', $search);
			$this->db->or_like('last_name', $search);
			$this->db->or_like('email', $search);
			$this->db->or_like('phone_number', $search);
			$this->db->or_like('username', $search);
			$this->db->or_like('CONCAT(first_name, " ", last_name)', $search);
		$this->db->group_end();
		$this->db->where('deleted', 0);
		// Disable by ManhVT to display Bac Si
		//$this->db->where('type', 1);
		$this->db->order_by($sort, $order);

		if($rows > 0)
		{
			$this->db->limit($rows, $limit_from);
		}

		return $this->db->get();	
	}

	/*
	Attempts to login employee and set session. Returns boolean based on outcome.
	*/
	public function login($username, $password)
	{
		$query = $this->db->get_where('employees', array('username' => $username, 'deleted' => 0), 1);

		if($query->num_rows() == 1)
		{
			$row = $query->row();

			// compare passwords depending on the hash version
			if ($row->hash_version == 1 && $row->password == md5($password))
			{
				$this->db->where('person_id', $row->person_id);
				$this->session->set_userdata('person_id', $row->person_id);
				$password_hash = password_hash($password, PASSWORD_DEFAULT);
				$this->session->set_userdata('type', $row->type);
				return $this->db->update('employees', array('hash_version' => 2, 'password' => $password_hash));
			}
			else if ($row->hash_version == 2 && password_verify($password, $row->password))
			{
				$this->session->set_userdata('person_id', $row->person_id);
				$this->session->set_userdata('type', $row->type);
				// Put ThUser to the session
				$_oTheLoginedUser = $this->get_info($this->session->userdata('person_id')); //get user object to put session

				$this->session->set_userdata('theUser', $_oTheLoginedUser); // Put the logined user to the session

				// Put permission to session, it is deleting after logouted

				//$this->db->from('grants');				
				//$this->db->where('person_id', $this->session->userdata('person_id'));
				
				//$this->db->where('person_id', 2);
				//$_aoGrants = $this->db->get()->result();	
				$_grants = $this->Module->get_grants_of_the_user($this->session->userdata('person_id'));
				$_aoGrants = $_grants->result();		
				//var_dump($_aoGrants);die();
				$this->session->set_userdata('grants', $_aoGrants); // Put the _aoGrants to the session

				//load modules of use after login

				$_aoAllowed_Modules = $this->Module->get_allowed_modules($this->session->userdata('person_id'))->result();
				//var_dump($_aoAllowed_Modules);die();
				if(empty($_aoAllowed_Modules))
				{
					$this->session->set_userdata('allowedmodules', array()); // Put the empty of array to the session
				} else{
					$_aoAllowedmodules = array();
					foreach ($_aoAllowed_Modules as $key=>$allowed_module) {


						if ($allowed_module->permission_key == $allowed_module->module_key.'_index' ) 
						{
							$_aoAllowedmodules[] = $allowed_module;
						}
					}
					$this->session->set_userdata('allowedmodules', $_aoAllowedmodules); // Put the _aoAllowed_Modules to the session
				}

				$_aoRolesOfTheUser = $this->Module->get_roles_of_the_user($this->session->userdata('person_id'))->result();
				//var_dump($_aoRolesOfTheUser);die();
				if(empty($_aoRolesOfTheUser))
				{
					$this->session->set_userdata('RolesOfTheUser', array()); // Put the empty of array to the session
				} else {
					$this->session->set_userdata('RolesOfTheUser', $_aoRolesOfTheUser); // Put the _aoAllowed_Modules to the session
				}

				$_aoRoles = $this->Module->get_roles()->result();
				if(empty($_aoRoles))
				{
					$this->session->set_userdata('Roles', array()); // Put the empty of array to the session
				} else {
					$this->session->set_userdata('Roles', $_aoRoles); // Put the _aoRoles to the session
				}

				$_aoAllGrants = $this->Module->get_grants()->result();
				//var_dump($_aoAllGrants);
				if(empty($_aoAllGrants))
				{
					$this->session->set_userdata('AllGrants', array()); // Put the empty of array to the session
				} else {
					$this->session->set_userdata('AllGrants', $_aoAllGrants); // Put the _aoAllGrants to the session
				}

				return TRUE;
			}

		}

		return FALSE;
	}

	/*
	Logs out a user by destorying all session data and redirect to login
	*/
	public function logout()
	{
		$this->session->sess_destroy();

		redirect('login');
	}
	
	/*
	Determins if a employee is logged in
	*/
	public function is_logged_in()
	{
		return ($this->session->userdata('person_id') != FALSE);
	}

	/*
	Gets information about the currently logged in employee.
	*/
	public function get_logged_in_employee_info()
	{
		if($this->is_logged_in())
		{
			return $this->session->userdata('theUser');
			//return $this->get_info($this->session->userdata('person_id'));
		}
		
		return FALSE;
	}

	/*
	Determines whether the employee has access to at least one submodule
	 */
	public function has_module_grant($permission_id, $person_id)
	{
		$_aoGrants = $this->session->userdata('grants');
		if(empty($_aoGrants))
		{
			return FALSE;
		}

		if(count($_aoGrants) > 1)
		{
			return TRUE;
		}
		/* Disbale by ManhVT to support session.

		$this->db->from('grants');
		$this->db->like('permission_id', $permission_id, 'after');
		$this->db->where('person_id', $person_id);
		$result_count = $this->db->get()->num_rows();

		if($result_count != 1)
		{
			return ($result_count != 0);
		}
		*/

		return $this->has_subpermissions($permission_id);
	}

 	/*
	Checks permissions
	*/
	public function has_subpermissions($permission_id)
	{
		$this->db->from('permissions');
		$this->db->like('permission_id', $permission_id.'_', 'after');

		return ($this->db->get()->num_rows() == 0);
	}

	/*
	Determines whether the employee specified employee has access the specific module.
	*/
	public function has_grant($permission_key, $person_id=0)
	{
		//if no module_id is null, allow access
		if($permission_key == null)
		{
			return TRUE;
		}
		$_aoGrants = $this->session->userdata('grants');
		//var_dump($_aoGrants);die();
		if(empty($_aoGrants))
		{
			return FALSE;
		} else{
			foreach($_aoGrants as $_oGrant)
			{
				if($_oGrant->permission_key == $permission_key)
				{
					return TRUE;
				}
			}
		}
		return FALSE;
		/*
		$query = $this->db->get_where('grants', array('person_id' => $person_id, 'permission_id' => $permission_id), 1);

		return ($query->num_rows() == 1); 
		*/
	}

	public function get_allowed_modules()
	{
		//var_dump($this->session->userdata('allowedmodules'));
		if(empty($this->session->userdata('allowedmodules')))
		{
			return array();
		}
		return $this->session->userdata('allowedmodules');
	} 

 	/*
	Gets employee permission grants
	*/
	public function get_employee_grants()
	{
		if(empty($this->session->userdata('grants')))
		{
			return array();
		}
		return $this->session->userdata('grants');
	}

	public function get_roles_of_the_user()
	{
		if(empty($this->session->userdata('RolesOfTheUser')))
		{
			return array();
		}
		return $this->session->userdata('RolesOfTheUser');
	}

	public function get_roles()
	{
		if(empty($this->session->userdata('Roles')))
		{
			return array();
		}
		return $this->session->userdata('Roles');
	}

	public function get_actions_by_module($module_key)
	{
		if(empty($this->session->userdata('AllGrants')))
		{
			return array();
		}
		$_aoGrants = $this->session->userdata('AllGrants');
		$_astrReturn = array();
		//var_dump($_aoGrants); die();
		foreach($_aoGrants as $key=>$_oGrant)
		{
			if($_oGrant->module_key == $module_key)
			{
				$_astrReturn[] = $_oGrant->permission_key;
			}
		}
		return $_astrReturn;
		//return $this->session->userdata('grants');
	}

	public function update_employee($log,$employee_id)
	{
		$employee_data = array('log' => $log);
		$this->db->where('person_id', $employee_id);
		$success = $this->db->update('employees', $employee_data);
		return $success;

	}
}
?>
