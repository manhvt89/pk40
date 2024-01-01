<?php
class Purchase extends CI_Model
{
	public function get_info($purchase_id)
	{	
		$this->db->from('purchases');
		$this->db->join('people', 'people.person_id = purchases.supplier_id', 'LEFT');
		$this->db->join('suppliers', 'suppliers.person_id = purchases.supplier_id', 'LEFT');
		$this->db->where('id', $purchase_id);

		return $this->db->get();
	}

	public function get_info_uuid($uuid)
	{	
		$this->db->from('purchases');
		$this->db->join('people', 'people.person_id = purchases.supplier_id', 'LEFT');
		$this->db->join('suppliers', 'suppliers.person_id = purchases.supplier_id', 'LEFT');
		$this->db->where('purchase_uuid', $uuid);

		return $this->db->get();
	}

	/*
	public function get_receiving_by_reference($reference)
	{
		$this->db->from('receivings');
		$this->db->where('reference', $reference);

		return $this->db->get();
	}
	
	public function is_valid_receipt($receipt_receiving_id)
	{
		if(!empty($receipt_receiving_id))
		{
			//RECV #
			$pieces = explode(' ', $receipt_receiving_id);

			if(count($pieces) == 2 && preg_match('/(RECV|KIT)/', $pieces[0]))
			{
				return $this->exists($pieces[1]);
			}
			else 
			{
				return $this->get_receiving_by_reference($receipt_receiving_id)->num_rows() > 0;
			}
		}

		return FALSE;
	}
	*/

	public function exists($id)
	{
		$this->db->from('purchases');
		$this->db->where('id', $id);
		return ($this->db->get()->num_rows() == 1);
	}

	public function exists_by_code($code)
	{
		$this->db->from('purchases');
		$this->db->where('code', $code);
		return ($this->db->get()->num_rows() == 1);
	}
	
	public function update($data, $id)
	{
		$this->db->where('id', $id);
		return $this->db->update('purchases', $data);
	}
	// Save session to DB; create draff
	public function save($items,$quantity, $supplier_id, $employee_id, $name, $code ='POxxx' ,$comment = '',$completed = 0)
	{
		if(count($items) == 0)
		{
			return -1;
		}
		$_dCreatedTime = date('Y-m-d H:i:s');
		$_aPurchaseData = array(
			'purchase_time' => $_dCreatedTime,
			'supplier_id' => $this->Supplier->exists($supplier_id) ? $supplier_id : 0,
			'employee_id' => $employee_id,
			'name' => $name,
			'total_quantity'=>$quantity,
			'code' => $code,
			'completed'=>$completed,
			'comment' => $comment,
			'v'=>0
		);

		//Run these queries as a transaction, we want to make sure we do all or nothing
		$this->db->trans_start();

		$this->db->insert('purchases', $_aPurchaseData);
		$_iPurchaseID = $this->db->insert_id();

		foreach($items as $line=>$item)
		{
			if($item['status'] != 9) // Insert New products
			{
				if(!$this->Item->item_number_exists($item['item_number'])) // Nếu chưa tồn tại sp với barcode, tạo sản phẩm mới;
				{
					//$_iItemId = 0;
					// Tạo mới sản phẩm;
					$item_data = array(
                        'name'					=> $item['item_name'],
                        'description'			=> empty($item['description'])==true?'':$item['description'],
                        'category'				=> $item['item_category'],
                        'cost_price'			=> $item['item_price'],
                        'unit_price'			=> $item['item_u_price'],
                        'reorder_level'			=> 0,
                        'supplier_id'			=> $supplier_id,
                        'allow_alt_description'	=> '0',
                        'is_serialized'			=> '0',
                        'custom1'				=> empty($item['custom1'])==true?'':$item['custom1'],
                        'custom2'				=> empty($item['custom2'])==true?'':$item['custom2'],
                        'custom3'				=> empty($item['custom3'])==true?'':$item['custom3'],
                        'custom4'				=> empty($item['custom4'])==true?'':$item['custom4'],
                        'custom5'				=> empty($item['custom5'])==true?'':$item['custom5'],
                        'custom6'				=> empty($item['custom6'])==true?'':$item['custom6'],
                        'custom7'				=> empty($item['custom7'])==true?'':$item['custom7'],
                        'custom8'				=> empty($item['custom8'])==true?'':$item['custom8'],
                        'custom9'				=> empty($item['custom9'])==true?'':$item['custom9'],
                        'custom10'				=> empty($item['custom10'])==true?'':$item['custom10']
                    );
					$item_data['item_number']=$item['item_number'];
					//var_dump($item_data);die();
					if ($this->Item->save($item_data)) 
					{
						$items_taxes_data = NULL;
						//tax 1
						$items_taxes_data[] = array('name' => 'Tax', 'percent' => '8');
						// save tax values
						if (count($items_taxes_data) > 0) {
							$this->Item_taxes->save($items_taxes_data, $item_data['item_id']);
						}

						// quantities & inventory Info

						//$emp_info = $this->Employee->get_info($employee_id);
						$comment = 'Tạo từ PO';
						// array to store information if location got a quantity
						$item_quantity_data = array(
							'item_id' => $item_data['item_id'],
							'location_id' => 1,
							'quantity' => 0,
						);
						$this->Item_quantity->save($item_quantity_data, $item_data['item_id'], 1);

						$excel_data = array(
							'trans_items' => $item_data['item_id'],
							'trans_user' => $employee_id,
							'trans_comment' => $comment,
							'trans_location' => 1,
							'trans_inventory' => 0
						);

						$this->Inventory->insert($excel_data);

						$_aPurchasesItemsData = array(
							'purchase_id' => $_iPurchaseID,
							'created_time' => $_dCreatedTime,
							'item_id' => $item_data['item_id'],
							'item_name' => $item['item_name'],
							'item_number' => $item['item_number'],
							'item_quantity' => $item['item_quantity'],
							'item_price' => $item['item_price'],
							// Giá nhập 
							'item_u_price' => $item['item_u_price'],
							'item_category' => $item['item_category'],
							'line' => $line,
							'type' => 2 //Đã thêm vào bảng items
						);
						$this->db->insert('purchases_items', $_aPurchasesItemsData);
					} else {
						$_aPurchasesItemsData = array(
							'purchase_id' => $_iPurchaseID,
							'created_time' => $_dCreatedTime,
							'item_id' => 0,
							'item_name' => $item['item_name'],
							'item_number' => $item['item_number'],
							'item_quantity' => $item['item_quantity'],
							'item_price' => $item['item_price'],
							// Giá nhập 
							'item_u_price' => $item['item_u_price'],
							'item_category' => $item['item_category'],
							'line' => $line,
							'type' => 2 //đã thêm vào bảng items nhưng lỗi (check item_id=0)
						);
						$this->db->insert('purchases_items', $_aPurchasesItemsData);
					}
				} else {
					$_aPurchasesItemsData = array(
						'purchase_id' => $_iPurchaseID,
						'created_time'=>$_dCreatedTime,
						'item_id' => 0,
						'item_name' => $item['item_name'],
						'item_number'=>$item['item_number'],
						'item_quantity' => $item['item_quantity'],
						'item_price' => $item['item_price'], // Giá nhập 
						'item_u_price'=> $item['item_u_price'],
						'item_category' => $item['item_category'],
						'line' => $line,
						'type'=>3 //Chưa thêm đc vào bảng items
					);
					$this->db->insert('purchases_items', $_aPurchasesItemsData);
				}
			} else { //Sản phẩm đã tồn tại trong items table
				$cur_item_info = $this->Item->get_info_by_id_or_number($item['item_number']);
				$_aPurchasesItemsData = array(
					'purchase_id' => $_iPurchaseID,
					'created_time'=>$_dCreatedTime,
					'item_id' => $cur_item_info->item_id,
					'item_name' => $cur_item_info->name,
					'item_number'=>$cur_item_info->item_number,
					'item_quantity' => $item['item_quantity'],
					'item_price' => $item['item_price'], // Giá nhập 
					'item_u_price'=> $item['item_u_price'],
					'item_category' => $cur_item_info->category,
					'line' => $line,
					'type'=>0 //Sản phẩm cũ
				);
				$this->db->insert('purchases_items', $_aPurchasesItemsData);
			}
		}
		$this->db->trans_complete();
		if($this->db->trans_status() === FALSE)
		{
			return -1;
		}
		return $_iPurchaseID;
	}
	/**
	 * Summary of the_purchase: Get purchase with all items
	 * @param mixed $purchase_id
	 * @return mixed
	 */
	public function the_purchase($purchase_id)
	{
		$_aThePurchase = array();
		$this->db->from('purchases');
		$this->db->where('id', $purchase_id);
		$_aThePurchase = $this->db->get()->row_array(); // return a array();
		if(empty($_aThePurchase))
		{
			return array();
		} else {
			$_aThePurchase['items'] = $this->get_purchase_items($purchase_id)->result_array();
			return $_aThePurchase;
		}
	}
	public function get_purchase_items($purchase_id)
	{
		$this->db->select('purchases_items.*,items.description, items.custom1, items.custom2, items.custom3, items.custom4, items.custom5, items.custom6, items.custom7, items.custom8, items.custom9, items.custom10');
		$this->db->from('purchases_items');
		$this->db->join('items','items.item_id = purchases_items.item_id','left');
		$this->db->where('purchase_id', $purchase_id);
		return $this->db->get();
	}
	
	public function get_supplier($purchase_id)
	{
		$this->db->from('purchases');
		$this->db->where('id', $purchase_id);
		return $this->Supplier->get_info($this->db->get()->row()->supplier_id);
	}

	/*
	We create a temp table that allows us to do easy report/purchase queries
	*/
	public function create_temp_table(array $inputs)
	{
		if(empty($inputs['purchase_id']))
		{
			$where = 'WHERE DATE(purchase_time) BETWEEN ' . $this->db->escape($inputs['start_date']) . ' AND ' . $this->db->escape($inputs['end_date']);
		}
		else
		{
			$where = 'WHERE purchases_items.purchase_id = ' . $this->db->escape($inputs['purchase_id']);
		}
		
		$this->db->query('CREATE TEMPORARY TABLE IF NOT EXISTS ' . $this->db->dbprefix('purchases_items_temp') . 
			' (INDEX(purchase_date), INDEX(purchase_id))
			(
				SELECT 
					DATE(purchase_time) AS purchase_date,
					purchase_time,
					purchases_items.purchase_id,
					comment,
					name,
					employee_id, 
					items.item_id,
					purchases.supplier_id,
					item_quantity as quantity_purchased,
					item_price,
					purchases_items.line,
					items.category,
					(item_price * quantity_purchased) AS total
				FROM ' . $this->db->dbprefix('purchases_items') . ' AS purchases_items
				INNER JOIN ' . $this->db->dbprefix('purchases') . ' AS purchases
					ON purchases_items.purchase_id = purchases.id
				INNER JOIN ' . $this->db->dbprefix('items') . ' AS items
					ON purchases_items.item_id = items.item_id
				' . "
				$where
				" . '
				GROUP BY purchases_items.purchase_id, items.item_id, purchases_items.line
			)'
		);
	}

	public function search($search, $filters, $rows = 0, $limit_from = 0, $sort = 'purchase_time', $order = 'desc')
	{
		

		$this->db->select('
				purchases.id AS purchase_id,
				purchases.*	');

		$this->db->from('purchases as purchases');
		$this->db->where('curent', 1);
		$this->db->where('DATE(purchases.edited_time) BETWEEN ' . $this->db->escape($filters['start_date']) . ' AND ' . $this->db->escape($filters['end_date']));
		
		if(!empty($search))
		{
					
			$this->db->group_start();
				// customer last name
				$this->db->like('purchases.name', $search);
				// customer first name
			$this->db->group_end();
			
		}

		//var_dump($this->db);
		if($filters['new'] != FALSE)
		{
			$this->db->group_start();
				$this->db->like('purchases.completed', 0, 'after');			
				$this->db->like('purchases.parent_id', 0, 'after');			
			$this->db->group_end();
		}
		if($filters['cancel'] != FALSE)
		{
			$this->db->group_start();
				$this->db->like('purchases.completed', 3, 'after');				
			$this->db->group_end();
		}

		$this->db->group_by('purchases.id');
		$this->db->order_by($sort, $order);

		if($rows > 0)
		{
			$this->db->limit($rows, $limit_from);
		}

		return $this->db->get();
	}

	public function get_found_rows($search, $filters)
	{
		return $this->search($search, $filters,0,0,'id','desc')->num_rows();
	}

	/**
	 * items: danh sách các sản phẩm
	 * purchase_info: thông tin PO
	 */
	public function _save($purchase_info,$data, $supplier_id, $employee_id)
	{
		$items = $data['cart'];
		$quantity = $data['quantity'];
		if(count($items) == 0)
		{
			return -1;
		}
		if(empty($purchase_info))
		{
			return -1;
		}
		//$_dCreatedTime = date('Y-m-d H:i:s');
		$_dEditTime = date('Y-m-d H:i:s');
		$_dCreatedTime = $purchase_info['purchase_time'];
		$_aPurchaseData = array(
			'purchase_time' => $_dCreatedTime,
			'supplier_id' => $this->Supplier->exists($supplier_id) ? $supplier_id : 0,
			'employee_id' => $employee_id,
			'name' => $purchase_info['name'],
			'total_quantity'=>$quantity,
			'code' => $purchase_info['code'],
			'completed'=>0, //Trạng thái ban đầu
			'comment' => $purchase_info['comment'],
			'parent_id'=>$purchase_info['id'],
			'curent'=>1,
			'edited_time' => $_dEditTime,
			'v'=>$purchase_info['v'] + 1,
		);
		$_ParrentUpdateData = array(
			'curent' => 0,
			'edited_time' => $_dEditTime,
			'edited_employee_id' => $employee_id
		);

		//Run these queries as a transaction, we want to make sure we do all or nothing
		$this->db->trans_start();
		$this->update($_ParrentUpdateData,$purchase_info['id']);

		$this->db->insert('purchases', $_aPurchaseData);
		$_iPurchaseID = $this->db->insert_id();

		foreach($items as $line=>$item)
		{
			if($item['status'] != 9) // Insert New products
			{
				if(!$this->Item->item_number_exists($item['item_number'])) // Nếu chưa tồn tại sp với barcode, tạo sản phẩm mới;
				{
					//$_iItemId = 0;
					// Tạo mới sản phẩm;
					$item_data = array(
                        'name'					=> $item['item_name'],
                        'description'			=> empty($item['description'])==true?'':$item['description'],
                        'category'				=> $item['item_category'],
                        'cost_price'			=> $item['item_price'],
                        'unit_price'			=> $item['item_u_price'],
                        'reorder_level'			=> 0,
                        'supplier_id'			=> $supplier_id,
                        'allow_alt_description'	=> '0',
                        'is_serialized'			=> '0',
                        'custom1'				=> empty($item['custom1'])==true?'':$item['custom1'],
                        'custom2'				=> '',
                        'custom3'				=> '',
                        'custom4'				=> '',
                        'custom5'				=> '',
                        'custom6'				=> '',
                        'custom7'				=> '',
                        'custom8'				=> '',
                        'custom9'				=> '',
                        'custom10'				=> ''
                    );
					$item_data['item_number']=$item['item_number'];

					if ($this->Item->save($item_data)) 
					{
						$items_taxes_data = NULL;
						//tax 1
						$items_taxes_data[] = array('name' => 'Tax', 'percent' => '8');
						// save tax values
						if (count($items_taxes_data) > 0) {
							$this->Item_taxes->save($items_taxes_data, $item_data['item_id']);
						}

						// quantities & inventory Info

						//$emp_info = $this->Employee->get_info($employee_id);
						$comment = 'Tạo từ PO';
						// array to store information if location got a quantity
						$item_quantity_data = array(
							'item_id' => $item_data['item_id'],
							'location_id' => 1,
							'quantity' => 0,
						);
						$this->Item_quantity->save($item_quantity_data, $item_data['item_id'], 1);

						$excel_data = array(
							'trans_items' => $item_data['item_id'],
							'trans_user' => $employee_id,
							'trans_comment' => $comment,
							'trans_location' => 1,
							'trans_inventory' => 0
						);

						$this->Inventory->insert($excel_data);

						$_aPurchasesItemsData = array(
							'purchase_id' => $_iPurchaseID,
							'created_time' => $_dCreatedTime,
							'item_id' => $item_data['item_id'],
							'item_name' => $item['item_name'],
							'item_number' => $item['item_number'],
							'item_quantity' => $item['item_quantity'],
							'item_price' => $item['item_price'],
							// Giá nhập 
							'item_u_price' => $item['item_u_price'],
							'item_category' => $item['item_category'],
							'line' => $line,
							'type' => 2 //Đã thêm vào bảng items
						);
						$this->db->insert('purchases_items', $_aPurchasesItemsData);
					} else {
						$_aPurchasesItemsData = array(
							'purchase_id' => $_iPurchaseID,
							'created_time' => $_dCreatedTime,
							'item_id' => 0,
							'item_name' => $item['item_name'],
							'item_number' => $item['item_number'],
							'item_quantity' => $item['item_quantity'],
							'item_price' => $item['item_price'],
							// Giá nhập 
							'item_u_price' => $item['item_u_price'],
							'item_category' => $item['item_category'],
							'line' => $line,
							'type' => 2 //đã thêm vào bảng items nhưng lỗi (check item_id=0)
						);
						$this->db->insert('purchases_items', $_aPurchasesItemsData);
					}
				} else {
					$_aPurchasesItemsData = array(
						'purchase_id' => $_iPurchaseID,
						'created_time'=>$_dEditTime,
						'item_id' => 0,
						'item_name' => $item['item_name'],
						'item_number'=>$item['item_number'],
						'item_quantity' => $item['item_quantity'],
						'item_price' => $item['item_price'], // Giá nhập 
						'item_u_price'=> $item['item_u_price'],
						'item_category' => $item['item_category'],
						'line' => $line,
						'type'=>3 //Chưa thêm đc vào bảng items
					);
					$this->db->insert('purchases_items', $_aPurchasesItemsData);
				}
			} else { //Sản phẩm đã tồn tại trong items table
				//echo '123';die();
				$cur_item_info = $this->Item->get_info_by_id_or_number($item['item_number']);
				$_aPurchasesItemsData = array(
					'purchase_id' => $_iPurchaseID,
					'created_time'=>$_dEditTime,
					'item_id' => $cur_item_info->item_id,
					'item_name' => $cur_item_info->name,
					'item_number'=>$cur_item_info->item_number,
					'item_quantity' => $item['item_quantity'],
					'item_price' => $item['item_price'], // Giá nhập 
					'item_u_price'=> $item['item_u_price'],
					'item_category' => $cur_item_info->category,
					'line' => $line,
					'type'=>0 //Sản phẩm cũ
				);
				$this->db->insert('purchases_items', $_aPurchasesItemsData);
			}

			
		}

		$this->db->trans_complete();
		
		if($this->db->trans_status() === FALSE)
		{
			return -1;
		}

		return $_iPurchaseID;
	}

}
?>
