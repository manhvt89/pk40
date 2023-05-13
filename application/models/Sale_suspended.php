<?php
class Sale_suspended extends CI_Model
{
	public function get_all()
	{
		$this->db->from('sales_suspended');
		$this->db->where('lock < ',2);
		$this->db->order_by('sale_id','desc');
		return $this->db->get();
	}

	public function get_item($sale_id)
	{
		$this->db->from('sales_suspended');
		$this->db->where('sale_id', $sale_id);
		return $this->db->get()->result();
	}
	
	public function get_info($sale_id)
	{
		$this->db->from('sales_suspended');
		$this->db->where('sale_id', $sale_id);
		$this->db->join('people', 'people.person_id = sales_suspended.customer_id', 'LEFT');

		return $this->db->get();
	}

	/*
	Gets total of invocie rows
	*/
	public function get_invoice_count()
	{
		$this->db->from('sales_suspended');
		$this->db->where('invoice_number IS NOT NULL');

		return $this->db->count_all_results();
	}
	
	public function get_sale_by_invoice_number($invoice_number)
	{
		$this->db->from('sales_suspended');
		$this->db->where('invoice_number', $invoice_number);

		return $this->db->get();
	}

	public function exists($sale_id)
	{
		$this->db->from('sales_suspended');
		$this->db->where('sale_id', $sale_id);

		return ($this->db->get()->num_rows() == 1);
	}
	
	public function update($sale_data, $sale_id)
	{
		$this->db->where('sale_id', $sale_id);

		return $this->db->update('sales_suspended', $sale_data);
	}
	
	public function save($items, $customer_id, $employee_id, $comment, $invoice_number, $payments, $sale_id = FALSE)
	{
		//var_dump($payments); die();
		if(count($items) == 0)
		{
			return -1;
		}

		$sales_data = array(
			'sale_time'      => date('Y-m-d H:i:s'),
			'customer_id'    => $this->Customer->exists($customer_id) ? $customer_id : null,
			'employee_id'    => $employee_id,
			'comment'        => $comment,
			'invoice_number' => $invoice_number
		);

		//Run these queries as a transaction, we want to make sure we do all or nothing
		$this->db->trans_start();

		$this->db->insert('sales_suspended', $sales_data);
		$sale_id = $this->db->insert_id();
		/*
		foreach($payments as $payment_id=>$payment)
		{
			$sales_payments_data = array(
				'sale_id'        => $sale_id,
				'payment_type'   => $payment['payment_type'],
				'payment_amount' => $payment['payment_amount']
			);

			$this->db->insert('sales_suspended_payments', $sales_payments_data);
		}
		*/
		foreach($items as $line=>$item)
		{
			$cur_item_info = $this->Item->get_info($item['item_id']);

			$sales_items_data = array(
				'sale_id'            => $sale_id,
				'item_id'            => $item['item_id'],
				'line'               => $item['line'],
				'description'        => character_limiter($item['description'], 30),
				'serialnumber'       => character_limiter($item['serialnumber'], 30),
				'quantity_purchased' => $item['quantity'],
				'discount_percent'   => $item['discount'],
				'item_cost_price'    => $cur_item_info->cost_price,
				'item_unit_price'    => $item['price'],
				'item_location'      => $item['item_location']
			);

			$this->db->insert('sales_suspended_items', $sales_items_data);

			$customer = $this->Customer->get_info($customer_id);
 			if($customer_id == -1 || $customer->taxable)
 			{
				foreach($this->Item_taxes->get_info($item['item_id']) as $row)
				{
					$sales_items_taxes = array(
						'sale_id' => $sale_id,
						'item_id' => $item['item_id'],
						'line'    => $item['line'],
						'name'    => $row['name'],
						'percent' => $row['percent']
					);
					
					$this->db->insert('sales_suspended_items_taxes', $sales_items_taxes);
				}
			}
		}

		$this->db->trans_complete();
		
		if($this->db->trans_status() === FALSE)
		{
			return -1;
		}
		
		return $sale_id;
	}
	
	public function delete($sale_id)
	{
		//Run these queries as a transaction, we want to make sure we do all or nothing
		$this->db->trans_start();
		
		$this->db->delete('sales_suspended_payments', array('sale_id' => $sale_id));
		$this->db->delete('sales_suspended_items_taxes', array('sale_id' => $sale_id));
		$this->db->delete('sales_suspended_items', array('sale_id' => $sale_id));
		$this->db->delete('sales_suspended', array('sale_id' => $sale_id)); 
		
		$this->db->trans_complete();
				
		return $this->db->trans_status();
	}

	public function unsuspended($sale_id)
	{
		$data = $this->get_item($sale_id);
		$data[0]->lock = 1;
		if(isset($data)) {
			$this->update($data[0], $sale_id);
		}
	}

	public function locksuspend($sale_id)
	{
		$data = $this->get_item($sale_id);
		$data[0]->lock = 2;
		if(isset($data)) {
			$this->update($data[0], $sale_id);
		}
	}

	public function get_sale_items($sale_id)
	{
		$this->db->from('sales_suspended_items');
		$this->db->where('sale_id', $sale_id);

		return $this->db->get();
	}

	public function get_sale_payments($sale_id)
	{
		$this->db->from('sales_suspended_payments');
		$this->db->where('sale_id', $sale_id);

		return $this->db->get();
	}

	public function get_comment($sale_id)
	{
		$this->db->from('sales_suspended');
		$this->db->where('sale_id', $sale_id);

		return $this->db->get()->row()->comment;
	}


}
?>
