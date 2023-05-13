<?php
class Module extends CI_Model 
{
    function __construct()
    {
        parent::__construct();
    }

	public function get_the_module($id)
	{
		$query = $this->db->get_where('modules', array('module_key' => $id), 1);
		$row = null;
		
		if ($query->num_rows() == 1) {
			$row = $query->row();
		}

		return $row; // return obj

	}

	public function get_the_module_by_key($module_key)
	{
		$query = $this->db->get_where('modules', array('module_key' => $module_key), 1);
		$row = null;
		
		if ($query->num_rows() == 1) {
			$row = $query->row();
		}

		return $row; // return obj
	}
	
	public function get_module_name($module_key)
	{
		$query = $this->db->get_where('modules', array('module_key' => $module_key), 1);
		
		if($query->num_rows() == 1)
		{
			$row = $query->row();

			return $this->lang->line($row->name_lang_key);
		}
		
		return $this->lang->line('error_unknown');
	}
	
	public function get_module_desc($module_key)
	{
		$query = $this->db->get_where('modules', array('module_key' => $module_key), 1);

		if($query->num_rows() == 1)
		{
			$row = $query->row();

			return $this->lang->line($row->desc_lang_key);
		}
	
		return $this->lang->line('error_unknown');	
	}
	
	public function get_all_permissions()
	{
		$this->db->from('permissions');

		return $this->db->get();
	}
	
	public function get_all_subpermissions()
	{
		$this->db->from('permissions');
		$this->db->join('modules', 'modules.id = permissions.module_id1');
		// can't quote the parameters correctly when using different operators..
		$this->db->where($this->db->dbprefix('modules') . '.module_id!=', 'permission_id', FALSE);

		return $this->db->get();
	}
	
	public function get_all_modules()
	{
		$this->db->from('modules');
		$this->db->order_by('sort', 'asc');
		return $this->db->get();
	}
	
	public function get_allowed_modules($person_id)
	{
		$this->db->select('modules.*, permissions.permission_key');
		$this->db->from('modules');
		$this->db->join('permissions', 'permissions.module_id = modules.module_key');
		$this->db->join('grants', 'permissions.id = grants.permission_id');
		$this->db->join('roles', 'roles.id = grants.role_id');
		$this->db->join('user_roles', 'user_roles.role_id = roles.id');
		$this->db->where('user_id', $person_id);
		//$this->db->distinct();
		$this->db->order_by('sort', 'asc');
		return $this->db->get();		
	}

	public function get_grants_of_the_user($user_id)
	{
		$this->db->from('permissions');
		$this->db->join('grants', 'permissions.id = grants.permission_id');
		$this->db->join('roles', 'roles.id = grants.role_id');
		$this->db->join('user_roles', 'user_roles.role_id = roles.id');
		$this->db->where('user_id', $user_id);
		//$this->db->distinct();
		return $this->db->get();		
	}
	/* 
	FUNCTION NAME: get_roles_of_the_user
	INPUT PARAM: user id
	OUTPUT PARAM: 
	This funtion to get all roles of the use is logined. This call after logined
	*/
	public function get_roles_of_the_user( $user_id)
	{
		$this->db->from('roles');
		$this->db->join('user_roles', 'user_roles.role_id = roles.id');
		$this->db->where('user_id', $user_id);
		return $this->db->get();
	}

	/* 
	FUNCTION NAME: get_roles_of_the_user
	@output mixed 
	This funtion to get all roles of the use is logined. This call after logined
	*/
	public function get_roles()
	{
		$this->db->from('roles');
		$this->db->where('status', 0);
		return $this->db->get();
	}

	public function add_module($aModule)
	{
		//name_lang_key        | desc_lang_key          | sort | module_key    | id | code | name | created_at | updated_at | deleted_at | module_uuid
		if($this->get_the_module_by_key($aModule['module_key']))
		{
			return false; // Neu ton tai roi ko them nua, dam bao module_key la duy nhat
		} else {
			return $this->db->insert('modules', $aModule);
		}
	}

	public function edit_module($aModule)
	{
		$this->db->where('id', $aModule['id']);
		return $this->db->update('modules', $aModule);	
	}

	public function get_the_role_by_uuid($uuid)
	{
		$query = $this->db->get_where('roles', array('role_uuid' => $uuid), 1);
		$row = null;
		
		if ($query->num_rows() == 1) {
			$row = $query->row();
		}

		return $row; // return obj
	}

	/* 
	Lây các quyền của nhóm quyền thông qua uuid của nhóm quyền (role)
	*/
	public function get_grants_of_the_role($role_uuid)
	{
		$this->db->select('permissions.*');
		$this->db->from('permissions');
		$this->db->join('grants', 'permissions.id = grants.permission_id');
		$this->db->join('roles', 'roles.id = grants.role_id');
		$this->db->where('roles.role_uuid', $role_uuid);
		//$this->db->distinct();
		return $this->db->get();		
	}
	/**
	 * Lấy tất cả các quyền trong hệ thống
	 */
	public function get_grants()
	{
		$this->db->select('permissions.*');
		$this->db->from('permissions');
		//$this->db->distinct();
		return $this->db->get();		
	}


	public function get_the_module_by_uuid($uuid)
	{
		$query = $this->db->get_where('modules', array('module_uuid' => $uuid), 1);
		$row = null;
		
		if ($query->num_rows() == 1) {
			$row = $query->row();
		}

		return $row; // return obj
	}

	public function get_all_permissions_by_module_key($module_key)
	{
		$this->db->from('permissions');
		$this->db->where('permissions.module_key', $module_key);
		return $this->db->get();
	}

	public function edit_permission($aPermission)
	{
		$this->db->where('id', $aPermission['id']);
		return $this->db->update('permissions', $aPermission);
	}

	public function add_permission($aPermission)
	{
		return $this->db->insert('permissions', $aPermission);
	}

	public function add_permission_to_grants($_aT)
	{
		return $this->db->insert('grants', $_aT);
	}

	public function del_permission_to_grants($_aT)
	{
		$this->db->where('role_id',$_aT['role_id']);
		$this->db->where('permission_id',$_aT['permission_id']);
		return $this->db->delete('grants');
	}

	public function add_permission_to_permissions($_aT)
	{
		return $this->db->insert('permissions', $_aT);
	}

	public function del_permission_from_permissions($permission_id=0)
	{
		if($permission_id == 0)
		{
			return -1;
		}
		$this->db->trans_start();
		
		$this->db->where('id',$permission_id);
		$success = $this->db->delete('permissions');
		$this->db->where('permission_id',$permission_id);
		$success &= $this->db->delete('grants');

		$this->db->trans_complete();
		
		$success &= $this->db->trans_status();
		return $success;
	}

	public function get_the_permission_by_uuid($uuid =0)
	{
		# code...
		$query = $this->db->get_where('permissions', array('permissions_uuid' => $uuid), 1);
		$row = null;
		
		if ($query->num_rows() == 1) {
			$row = $query->row();
		}

		return $row; // return obj
	}

	public function get_fields_by_permission($per_id)
	{
		$this->db->select('fields.*');
		$this->db->from('fields');
		$this->db->where('permission_id',$per_id);
		//$this->db->distinct();
		return $this->db->get();
	}

}
?>
