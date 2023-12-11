<?php
class Receiving extends CI_Model
{
	public function get_info($receiving_id)
	{	
		
		$this->db->from('receivings');
		$this->db->join('people', 'people.person_id = receivings.supplier_id', 'LEFT');
		$this->db->join('suppliers', 'suppliers.person_id = receivings.supplier_id', 'LEFT');
		if(strlen($receiving_id) < 20)
		{
			$this->db->where('receiving_id', $receiving_id);
		} else {
			$this->db->where('receiving_uuid', $receiving_id);
		}
		return $this->db->get();
	}

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

	public function exists($receiving_id)
	{
		$this->db->from('receivings');
		$this->db->where('receiving_id', $receiving_id);

		return ($this->db->get()->num_rows() == 1);
	}
	
	public function update($receiving_data, $receiving_id)
	{
		$this->db->where('receiving_id', $receiving_id);

		return $this->db->update('receivings', $receiving_data);
	}

	public function save($items, $supplier_id, $employee_id, $comment, $reference, $payment, $receiving_id = FALSE,$purchase_id=0,$mode = 'receive')
	{
		$_iTime = time();
		$_iMode = 0;
		$_sCode = 'NH'.$_iTime;
		$_sTime = date('Y-m-d h:m:s',$_iTime);
		if(count($items) == 0)
		{
			return -1;
		}
		if($mode == '')
		{
			$mode = 'receive';
		}
		if($mode != 'receive')
		{
			$_iMode = 1;
		}
		$payment_type = '';
		$remain_amount = 0;
		$total_amount =0;
		$paid_amount = 0;
		if(!is_array($payment))
		{
			$payment_type = $payment;
		} else {
			$payment_type = $payment['payment_type'];
			$remain_amount = $payment['remain_amount'];
			$total_amount = $payment['total_amount'];
			$paid_amount = $payment['paid_amount'];
		}

		$receivings_data = array(
			'receiving_time' => date('Y-m-d H:i:s'),
			'supplier_id' => $this->Supplier->exists($supplier_id) ? $supplier_id : NULL,
			'employee_id' => $employee_id,
			'payment_type' => $payment_type,
			'comment' => $comment,
			'reference' => $reference,
			'mode'=>$_iMode,
			'code'=>$_sCode,
			'remain_amount'=>$remain_amount,
			'total_amount'=>$total_amount,
			'paid_amount'=>$paid_amount
		);

		//Run these queries as a transaction, we want to make sure we do all or nothing
		$this->db->trans_start();
		//Thêm mới bởi ManhVT;
		// Update purchase khi thực hiện nhập kho
		if($purchase_id != 0)
		{
			//update Purachse
			$_ParrentUpdateData = array(
				'completed' => 6
			);
			//Run these queries as a transaction, we want to make sure we do all or nothing
			$this->Purchase->update($_ParrentUpdateData,$purchase_id);
		}
		//Hết

		$this->db->insert('receivings', $receivings_data);
		$receiving_id = $this->db->insert_id();

		foreach($items as $line=>$item)
		{
			$cur_item_info = $this->Item->get_info($item['item_id']);

			$receivings_items_data = array(
				'receiving_id' => $receiving_id,
				'item_id' => $item['item_id'],
				'line' => $item['line'],
				'description' => $item['description'],
				'serialnumber' => $item['serialnumber'],
				'quantity_purchased' => $item['quantity'],
				'receiving_quantity' => $item['receiving_quantity'],
				'discount_percent' => $item['discount'],
				'item_cost_price' => $cur_item_info->cost_price,
				'item_unit_price' => $item['price'],
				'item_location' => $item['item_location']
			);

			$this->db->insert('receivings_items', $receivings_items_data);

			$items_received = $item['receiving_quantity'] != 0 ? $item['quantity'] * $item['receiving_quantity'] : $item['quantity'];

			// update cost price, if changed AND is set in config as wanted
			if($cur_item_info->cost_price != $item['price'] && $this->config->item('receiving_calculate_average_price') != FALSE)
			{
				$this->Item->change_cost_price($item['item_id'], $items_received, $item['price'], $cur_item_info->cost_price);
			}

			//Update stock quantity
			$item_quantity = $this->Item_quantity->get_item_quantity($item['item_id'], $item['item_location']);
            $this->Item_quantity->save(array('quantity' => $item_quantity->quantity + $items_received, 'item_id' => $item['item_id'],
                                              'location_id' => $item['item_location']), $item['item_id'], $item['item_location']);

			$recv_remarks = 'Nhập ' . $receiving_id;
			$inv_data = array(
				'trans_date' => date('Y-m-d H:i:s'),
				'trans_items' => $item['item_id'],
				'trans_user' => $employee_id,
				'trans_location' => $item['item_location'],
				'trans_comment' => $recv_remarks,
				'trans_inventory' => $items_received
			);

			$this->Inventory->insert($inv_data);

			$supplier = $this->Supplier->get_info($supplier_id);
		}

		//Thêm bởi Mạnh VT - phục vụ tính toán công nợ nhập hàng
		// Thêm vào bảng payment
		
		$_aPaymentData = array(
			'receivings_id'		 => $receiving_id,
			'payment_type'	 => $payment['payment_type'],
			'payment_amount' => $payment['paid_amount'],
			'payment_kind'=>$payment['payment_kind'],
			'payment_time'=>$_sTime
		);
		$this->db->insert('receivings_payments', $_aPaymentData);
		//Hết

		$this->db->trans_complete();
		
		if($this->db->trans_status() === FALSE)
		{
			return -1;
		}

		return $receiving_id;
	}
	
	public function delete_list($receiving_ids, $employee_id, $update_inventory = TRUE)
	{
		$success = TRUE;

		// start a transaction to assure data integrity
		$this->db->trans_start();

		foreach($receiving_ids as $receiving_id)
		{
			$success &= $this->delete($receiving_id, $employee_id, $update_inventory);
		}

		// execute transaction
		$this->db->trans_complete();

		$success &= $this->db->trans_status();

		return $success;
	}
	
	public function delete($receiving_id, $employee_id, $update_inventory = TRUE)
	{
		// start a transaction to assure data integrity
		$this->db->trans_start();

		if($update_inventory)
		{
			// defect, not all item deletions will be undone??
			// get array with all the items involved in the sale to update the inventory tracking
			$items = $this->get_receiving_items($receiving_id)->result_array();
			foreach($items as $item)
			{
				// create query to update inventory tracking
				$inv_data = array(
					'trans_date' => date('Y-m-d H:i:s'),
					'trans_items' => $item['item_id'],
					'trans_user' => $employee_id,
					'trans_comment' => 'Deleting receiving ' . $receiving_id,
					'trans_location' => $item['item_location'],
					'trans_inventory' => $item['quantity_purchased'] * -1
				);
				// update inventory
				$this->Inventory->insert($inv_data);

				// update quantities
				$this->Item_quantity->change_quantity($item['item_id'], $item['item_location'], $item['quantity_purchased'] * -1);
			}
		}

		// delete all items
		$this->db->delete('receivings_items', array('receiving_id' => $receiving_id));
		// delete sale itself
		$this->db->delete('receivings', array('receiving_id' => $receiving_id));

		// execute transaction
		$this->db->trans_complete();
	
		return $this->db->trans_status();
	}

	public function get_receiving_items($receiving_id)
	{
		$this->db->from('receivings_items');
		$this->db->where('receiving_id', $receiving_id);

		return $this->db->get();
	}
	
	public function get_supplier($receiving_id)
	{
		$this->db->from('receivings');
		$this->db->where('receiving_id', $receiving_id);

		return $this->Supplier->get_info($this->db->get()->row()->supplier_id);
	}

	public function get_payment_options()
	{
		return [
			$this->lang->line('sales_cash') => $this->lang->line('sales_cash'),
			$this->lang->line('sales_check') => $this->lang->line('sales_check')
		];
	}

	/*
	We create a temp table that allows us to do easy report/receiving queries
	*/
	public function create_temp_table(array $inputs)
	{
		if(empty($inputs['receiving_id']))
		{
			$where = 'WHERE DATE(receiving_time) BETWEEN ' . $this->db->escape($inputs['start_date']) . ' AND ' . $this->db->escape($inputs['end_date']);
		}
		else
		{
			$where = 'WHERE receivings_items.receiving_id = ' . $this->db->escape($inputs['receiving_id']);
		}
		
		$this->db->query('CREATE TEMPORARY TABLE IF NOT EXISTS ' . $this->db->dbprefix('receivings_items_temp') . 
			' (INDEX(receiving_date), INDEX(receiving_id))
			(
				SELECT 
					DATE(receiving_time) AS receiving_date,
					receiving_time,
					receiving_uuid,
					receivings_items.receiving_id,
					comment,
					receivings.mode,
					item_location,
					reference,
					payment_type,
					employee_id, 
					items.item_id,
					receivings.supplier_id,
					quantity_purchased,
					receivings_items.receiving_quantity,
					item_cost_price,
					item_unit_price,
					discount_percent,
					receivings_items.line,
					serialnumber,
					items.category,
					receivings_items.description,
					(item_unit_price * quantity_purchased - item_unit_price * quantity_purchased * discount_percent / 100) AS subtotal,
					(item_unit_price * quantity_purchased - item_unit_price * quantity_purchased * discount_percent / 100) AS total,
					(item_unit_price * quantity_purchased - item_unit_price * quantity_purchased * discount_percent / 100) - (item_cost_price * quantity_purchased) AS profit,
					(item_cost_price * quantity_purchased) AS cost
				FROM ' . $this->db->dbprefix('receivings_items') . ' AS receivings_items
				INNER JOIN ' . $this->db->dbprefix('receivings') . ' AS receivings
					ON receivings_items.receiving_id = receivings.receiving_id
				INNER JOIN ' . $this->db->dbprefix('items') . ' AS items
					ON receivings_items.item_id = items.item_id
				' . "
				$where
				" . '
				GROUP BY receivings_items.receiving_id, items.item_id, receivings_items.line
			)'
		);
	}

	public function get_items_by_category($category_name)
	{
		$this->db->from('items');
		$this->db->where('category', $category_name);
		return $this->db->get();
	}

	public function get_found_rows($search, $filters, $bUser_type = 0, $iUser_Id = 0)
	{
		return $this->search($search, $filters,0,0,'receiving_time','desc',$bUser_type, $iUser_Id)->num_rows();
	}

	/*
	 Get the sales data for the takings (sales/manage) view
	*/
	public function search($search, $filters, $rows = 0, $limit_from = 0, $sort = 'receiving_time', $order = 'desc', $bUser_type = 0, $iUser_Id = 0)
	{
		// NOTE: temporary tables are created to speed up searches due to the fact that are ortogonal to the main query
		// create a temporary table to contain all the payments per sale item
		$decimals = totals_decimals();
		$this->db->select('
				r.receiving_id,
				r.code,
				r.created_time,
				
				IFNULL(ROUND(r.total_amount, $decimals), 0) AS total_amount,
				IFNULL(ROUND(r.paid_amount, $decimals), 0) AS paid_amount,
				IFNULL(ROUND(r.remain_amount, $decimals), 0) AS remain_amount,
				r.payment_type AS payment_type
		');

		$this->db->from('receivings AS r');
		$this->db->join('suppliers AS s', 's.person_id = r.supplier_id', 'left');
		$this->db->where('s.supplier_uuid', $filters['supplier_uuid']); //added by ManhVT hỗ trợ việc chỉ xét các bản ghi có hiệu lực;
		
		$this->db->where('DATE(r.receiving_time) BETWEEN ' . $this->db->escape($filters['start_date']) . ' AND ' . $this->db->escape($filters['end_date']));
			
		
		$this->db->order_by($sort, $order);

		if($rows > 0)
		{
			$this->db->limit($rows, $limit_from);
		}

		return $this->db->get();
	}
	/**
	 * input ['receiving_uuid'=>'',
	 * 			'payment_type'=>'',
	 * 			'paid_amount'=>'',
	 * 			'remain_amount'=>'']
	 */
	public function payment(array $aPayment=[])
	{
		if(empty($aPayment))
			return ['result'=>0,'data'=>''];

		$_iTime = time();
		$_sTime = date('Y-m-d h:m:s',$_iTime);
		
		$_oTheReceiving = $this->get_receiving($aPayment['receiving_uuid'])->row();
		if(empty($_oTheReceiving))
			return ['result'=>0,'data'=>''];
		
		$_sPaymentType = $aPayment['payment_type'];
		$_fRemainAmount = $aPayment['remain_amount'];
		$_fPaidAmount = $aPayment['paid_amount'];
		$_iReceivingID =  $_oTheReceiving->receiving_id;
		

		$receivings_data = array(
			'payment_type' => $_sPaymentType,
			'remain_amount'=>$_fRemainAmount,
			'paid_amount'=>$_fPaidAmount + $_oTheReceiving->paid_amount
		);

		//Run these queries as a transaction, we want to make sure we do all or nothing
		$this->db->trans_start();
		//Thêm mới bởi ManhVT;
		// Update purchase khi thực hiện nhập kho
		$result = $this->update($receivings_data,$_iReceivingID);

		//Thêm bởi Mạnh VT - phục vụ tính toán công nợ nhập hàng
		// Thêm vào bảng payment
		
		$_aPaymentData = array(
			'receivings_id'		 => $_iReceivingID,
			'payment_type'	 => $_sPaymentType,
			'payment_amount' => $_fPaidAmount,
			'payment_kind'=>0,
			'payment_time'=>$_sTime
		);
		$this->db->insert('receivings_payments', $_aPaymentData);
		//Hết

		$this->db->trans_complete();
		
		if($this->db->trans_status() === FALSE)
		{
			return -1;
		}
		return 1;
	}
	public function get_receiving($receiving_uuid)
	{	
		$this->db->from('receivings');
		$this->db->where('receiving_uuid', $receiving_uuid);
		return $this->db->get();
	}

	public function get_payments($receiving_id)
	{
		$this->db->from('receivings_payments');
		$this->db->where('receivings_id', $receiving_id);
		return $this->db->get();
	}
}
?>
