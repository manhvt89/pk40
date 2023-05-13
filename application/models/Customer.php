<?php
class Customer extends Person
{	
	/*
	Determines if a given person_id is a customer
	*/
	public function exists($person_id)
	{
		$this->db->from('customers');	
		$this->db->join('people', 'people.person_id = customers.person_id');
		$this->db->where('customers.person_id', $person_id);
		
		return ($this->db->get()->num_rows() == 1);
	}

	/*
	Checks if account number exists
	*/
	public function account_number_exists($account_number, $person_id = '')
	{
		$this->db->from('customers');
		$this->db->where('account_number', $account_number);

		if(!empty($person_id))
		{
			$this->db->where('person_id !=', $person_id);
		}

		return ($this->db->get()->num_rows() == 1);
	}	

	/*
	Gets total of rows
	*/
	public function get_total_rows()
	{
		$this->db->from('customers');
		$this->db->where('deleted', 0);

		return $this->db->count_all_results();
	}
	
	/*
	Returns all the customers
	*/
	public function get_all($rows = 0, $limit_from = 0)
	{
		$this->db->from('customers');
		$this->db->join('people', 'customers.person_id = people.person_id');			
		$this->db->where('deleted', 0);
		$this->db->order_by('last_name', 'asc');

		if($rows > 0)
		{
			$this->db->limit($rows, $limit_from);
		}

		return $this->db->get();		
	}

	//added by ManhVT 15/02/2017
	public function get_info_by_account_number($account_number)
	{
		$this->db->from('customers');
		$this->db->join('people', 'people.person_id = customers.person_id');
		$this->db->where('customers.account_number', $account_number);
		$query = $this->db->get();

		if($query->num_rows() == 1)
		{
			return $query->row();
		}
		else
		{
			return null;
		}
	}


	/*
	Gets information about a particular customer
	*/
	public function get_info($customer_id)
	{
		$this->db->from('customers');
		$this->db->join('people', 'people.person_id = customers.person_id');
		$this->db->where('customers.person_id', $customer_id);
		$query = $this->db->get();
		
		if($query->num_rows() == 1)
		{
			return $query->row();
		}
		else
		{
			//Get empty base parent object, as $customer_id is NOT a customer
			$person_obj = parent::get_info(-1);
			
			//Get all the fields from customer table
			//append those fields to base parent object, we we have a complete empty object
			foreach($this->db->list_fields('customers') as $field)
			{
				$person_obj->$field = '';
			}
			
			return $person_obj;
		}
	}
	
	/*
	Gets total about a particular customer
	*/
	public function get_totals($customer_id)
	{
		$this->db->select('SUM(payment_amount) AS total');
		$this->db->from('sales');
		$this->db->join('sales_payments', 'sales.sale_id = sales_payments.sale_id');
		$this->db->where('sales.customer_id', $customer_id);

		return $this->db->get()->row();
	}
	
	/*
	Gets information about multiple customers
	*/
	public function get_multiple_info($customer_ids)
	{
		$this->db->from('customers');
		$this->db->join('people', 'people.person_id = customers.person_id');		
		$this->db->where_in('customers.person_id', $customer_ids);
		$this->db->order_by('last_name', 'asc');

		return $this->db->get();
	}
	
	/*
	Inserts or updates a customer
	*/
	public function save_customer(&$person_data, &$customer_data, $customer_id = FALSE)
	{
		$success = FALSE;

		//Run these queries as a transaction, we want to make sure we do all or nothing
		$this->db->trans_start();
		
		if(parent::save($person_data, $customer_id))
		{
			if(!$customer_id || !$this->exists($customer_id))
			{
				$customer_data['person_id'] = $person_data['person_id'];
				$success = $this->db->insert('customers', $customer_data);
			}
			else
			{
				$this->db->where('person_id', $customer_id);
				$success = $this->db->update('customers', $customer_data);
			}
		}
		
		$this->db->trans_complete();
		
		$success &= $this->db->trans_status();

		return $success;
	}
	
	/*
	Deletes one customer
	*/
	public function delete($customer_id)
	{
		$this->db->where('person_id', $customer_id);

		return $this->db->update('customers', array('deleted' => 1));
	}
	
	/*
	Deletes a list of customers
	*/
	public function delete_list($customer_ids)
	{
		$this->db->where_in('person_id', $customer_ids);

		return $this->db->update('customers', array('deleted' => 1));
 	}
 	
 	/*
	Get search suggestions to find customers
	*/
	public function get_search_suggestions($search, $unique = TRUE, $limit = 25)
	{
		$suggestions = array();
		$this->db->from('customers');
		$this->db->join('people', 'customers.person_id = people.person_id');
		$this->db->group_start();		
			//$this->db->like('first_name', $search);
			//$this->db->or_like('last_name', $search); 
			
		if(ctype_digit($search))
		{
			$this->db->like('phone_number',$search,'after');
			//$this->db->where('MATCH (phone_number) AGAINST ("'.$search.'")', NULL, FALSE);
		} else {
			$pattern = '/^C\d+$/';
			//$string = 'C1234';
			if (preg_match($pattern, $search)) {
				$this->db->where('account_number', $search);
				
			} else {
				$pattern2 = '/^VH\d+$/';
				if (preg_match($pattern2, $search)) {
					$this->db->where('account_number', $search);
					
				} else {
					$this->db->like('first_name', $search);
					$this->db->or_like('last_name', $search); 
					$this->db->or_like('CONCAT(last_name, " ", first_name)', $search);
					//$this->db->where('MATCH (last_name, first_name) AGAINST ("'.$search.'")',NULL,FALSE);
				}
			}
			//$this->db->like('CONCAT(last_name, " ", first_name)', $search);
			//$this->db->or_like('account_number', $search);
		}
		$this->db->group_end();
		$this->db->where('deleted', 0);
		$this->db->order_by('last_name', 'asc');
		$this->db->limit($limit);
		foreach($this->db->get()->result() as $row)
		{
			$suggestions[] = array('value' => $row->person_id, 'label' => $row->last_name.' '.$row->first_name . ' - '.$row->phone_number . ' - '.$row->address_1);
		}

		if(!$unique)
		{
			$this->db->from('customers');
			$this->db->join('people', 'customers.person_id = people.person_id');
			$this->db->where('deleted', 0);
			$this->db->like('email', $search);
			$this->db->order_by('email', 'asc');
			foreach($this->db->get()->result() as $row)
			{
				$suggestions[] = array('value' => $row->person_id, 'label' => $row->email);
			}

			$this->db->from('customers');
			$this->db->join('people', 'customers.person_id = people.person_id');
			$this->db->where('deleted', 0);
			$this->db->like('phone_number', $search);
			$this->db->order_by('phone_number', 'asc');
			foreach($this->db->get()->result() as $row)
			{
				$suggestions[] = array('value' => $row->person_id, 'label' => $row->phone_number);
			}

			$this->db->from('customers');
			$this->db->join('people', 'customers.person_id = people.person_id');
			$this->db->where('deleted', 0);
			$this->db->like('account_number', $search);
			$this->db->order_by('account_number', 'asc');
			foreach($this->db->get()->result() as $row)
			{
				$suggestions[] = array('value' => $row->person_id, 'label' => $row->account_number);
			}
		}
		
		//only return $limit suggestions
		//if(count($suggestions > $limit))
		/* $query = $this->db->last_query();
		$explain_sql = 'EXPLAIN '.$query;
		$explain = $this->db->query($explain_sql);
		$explain_result = $explain->result_array();
		echo $query;
		var_dump($explain_result); */
		//if($suggestions > $limit)
		if(count($suggestions) > $limit)
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
		$this->db->from('customers');
		$this->db->join('people', 'customers.person_id = people.person_id');
		$this->db->group_start();
			$this->db->like('first_name', $search);
			$this->db->or_like('last_name', $search);
			$this->db->or_like('email', $search);
			$this->db->or_like('phone_number', $search);
			$this->db->or_like('account_number', $search);
			$this->db->or_like('CONCAT(first_name, " ", last_name)', $search);
		$this->db->group_end();
		$this->db->where('deleted', 0);

		return $this->db->get()->num_rows();
	}
	
	/*
	Performs a search on customers
	*/
	public function search($search, $rows = 0, $limit_from = 0, $sort = 'last_name', $order = 'asc')
	{
		$this->db->from('customers');
		$this->db->join('people', 'customers.person_id = people.person_id');
		if($search != "")
		{
			$this->db->group_start();
			//	$this->db->like('first_name', $search);
			//	$this->db->or_like('last_name', $search);
				//$this->db->or_like('email', $search);
			
			if(ctype_digit($search))
			{
				$this->db->like('phone_number',$search,'after');
				//$this->db->where('MATCH (phone_number) AGAINST ("'.$search.'")', NULL, FALSE);
			} else {
				$pattern = '/^C\d+$/';
				//$string = 'C1234';
				if (preg_match($pattern, $search)) {
					$this->db->where('account_number', $search);
					
				} else {
					$pattern2 = '/^VH\d+$/';
					if (preg_match($pattern2, $search)) {
						$this->db->where('account_number', $search);
						
					} else {
						$this->db->like('first_name', $search);
						$this->db->or_like('last_name', $search); 
						$this->db->or_like('CONCAT(last_name, " ", first_name)', $search);
						//$this->db->where('MATCH (last_name, first_name) AGAINST ("'.$search.'")',NULL,FALSE);
					}
				}
			}
			$this->db->group_end();
		}
		$this->db->where('deleted', 0);
		$this->db->order_by($sort, $order);

		if($rows > 0)
		{
			$this->db->limit($rows, $limit_from);
		}

		$return = $this->db->get();	
		/*   $query = $this->db->last_query();
		$explain_sql = 'EXPLAIN '.$query;
		$explain = $this->db->query($explain_sql);
		$explain_result = $explain->result_array();
		echo $query;
		var_dump($explain_result);  */
		return $return;
	}

	public function get_info_by_uuic($sCuuid)
	{
		$this->db->from('customers');
		$this->db->join('people', 'people.person_id = customers.person_id');
		$this->db->where('customers.customer_uuid', $sCuuid);
		$query = $this->db->get();
		
		if($query->num_rows() == 1)
		{
			return $query->row();
		}
		else
		{
			//Get empty base parent object, as $customer_id is NOT a customer
			$person_obj = parent::get_info(-1);
			
			//Get all the fields from customer table
			//append those fields to base parent object, we we have a complete empty object
			foreach($this->db->list_fields('customers') as $field)
			{
				$person_obj->$field = '';
			}
			
			return $person_obj;
		}
	}
}
?>
