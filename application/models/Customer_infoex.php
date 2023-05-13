<?php
class Customer_infoex extends CI_Model
{

	public function create(array $inputs)
	{
		//Create our temp tables to work with the data in our report
		$this->create_temp_table($inputs);
	}
	public function getDataColumns()
	{
		return array(
			'summary' => array(
				array('id' => $this->lang->line('reports_sale_id')),
				array('sale_date' => $this->lang->line('reports_date')),
				array('customer_name' => 'Tên KH'),
				array('phone_number' => 'Điện thoại'),
				array('quantity' => $this->lang->line('reports_quantity')),
				array('employee_name' => $this->lang->line('reports_sold_by')),
				array('total' => $this->lang->line('reports_total'), 'sorter' => 'number_sorter'),
				array('comment' => $this->lang->line('reports_comments')),

			),
			'details' => array(
				$this->lang->line('reports_name'),
				$this->lang->line('reports_category'),
				$this->lang->line('reports_quantity'),
				$this->lang->line('reports_subtotal'),
				$this->lang->line('reports_tax'),
				$this->lang->line('reports_total'),
				$this->lang->line('reports_discount'))
		);
	}

	public function getData(array $inputs)
	{
		$this->db->select('sales_items_temp.*,sale_id, sale_date, SUM(quantity_purchased) AS items_purchased, employee_name, SUM(subtotal) AS subtotal, SUM(tax) AS tax, SUM(total) AS total, SUM(cost) AS cost, SUM(profit) AS profit, payment_type, comment');
		$this->db->from('sales_items_temp');

		$term = $inputs['term'];

		if(strpos($term,'C1') === 0)
		{
			$this->db->where('account_number', $term);
		}else{
			$regex = "/^(\d[\s-]?)?[\(\[\s-]{0,2}?\d{3}[\)\]\s-]{0,2}?\d{3}[\s-]?\d{4}$/i";
			if(preg_match( $regex, $term ))
			{
				$this->db->like('phone_number', $term);
			}
			else{
				$this->db->like('customer_name', $term);
			}

		}
//		if ($inputs['sale_type'] == 'sales')
//		{
//			$this->db->where('quantity_purchased > 0');
//		}
//		elseif ($inputs['sale_type'] == 'returns')
//		{
//			$this->db->where('quantity_purchased < 0');
//		}

		$this->db->group_by('sale_id');
		$this->db->order_by('sale_date','desc');

		$data = array();
		$data['summary'] = $this->db->get()->result_array();
		$data['details'] = array();
		//var_dump($data['summary'][0]);die();
		foreach($data['summary'] as $key=>$value)
		{
			$this->db->select('name, category, quantity_purchased, subtotal, tax, total, discount_percent');
			$this->db->from('sales_items_temp');
			$this->db->where('sale_id', $value['sale_id']);
			$data['details'][$key] = $this->db->get()->result_array();
		}
		return $data;
	}

	public function getSummaryData(array $inputs)
	{
		$this->db->select('SUM(subtotal) AS subtotal, SUM(tax) AS tax, SUM(total) AS total, SUM(cost) AS cost, SUM(profit) AS profit');
		$this->db->from('sales_items_temp');
		$term = $inputs['term'];

		if(strpos($term,'C1') === 0)
		{
			$this->db->where('account_number', $term);
		}else{
			$regex = "/^(\d[\s-]?)?[\(\[\s-]{0,2}?\d{3}[\)\]\s-]{0,2}?\d{3}[\s-]?\d{4}$/i";
			if(preg_match( $regex, $term ))
			{
				$this->db->like('phone_number', $term);
			}
			else{
				$this->db->like('customer_name', $term);
			}

		}

		return $this->db->get()->row_array();
	}

	//We create a temp table that allows us to do easy report/sales queries
	public function create_temp_table(array $inputs)
	{
		if($this->config->item('tax_included'))
		{
			$sale_total = '(sales_items.item_unit_price * sales_items.quantity_purchased * (1 - sales_items.discount_percent / 100))';
			$sale_subtotal = '(sales_items.item_unit_price * sales_items.quantity_purchased * (1 - sales_items.discount_percent / 100) * (100 / (100 + SUM(sales_items_taxes.percent))))';
			$sale_tax = '(sales_items.item_unit_price * sales_items.quantity_purchased * (1 - sales_items.discount_percent / 100) * (1 - 100 / (100 + SUM(sales_items_taxes.percent))))';
		}
		else
		{
			$sale_total = '(sales_items.item_unit_price * sales_items.quantity_purchased * (1 - sales_items.discount_percent / 100) * (1 + (SUM(sales_items_taxes.percent) / 100)))';
			$sale_subtotal = '(sales_items.item_unit_price * sales_items.quantity_purchased * (1 - sales_items.discount_percent / 100))';
			$sale_tax = '(sales_items.item_unit_price * sales_items.quantity_purchased * (1 - sales_items.discount_percent / 100) * (SUM(sales_items_taxes.percent) / 100))';
		}

		$sale_cost  = '(sales_items.item_cost_price * sales_items.quantity_purchased)';

		$decimals = totals_decimals();

		$term = $inputs['term'];

		if(strpos($term,'C1')===0)
		{
			$where = 'WHERE customer.account_number = ' . $this->db->escape($term);
		}else{
			$regex = "/^(\d[\s-]?)?[\(\[\s-]{0,2}?\d{3}[\)\]\s-]{0,2}?\d{3}[\s-]?\d{4}$/i";
			if(preg_match( $regex, $term ))
			{
				$where = 'WHERE customer_p.phone_number like ' . $this->db->escape($term);
			}
			else{
				$where = 'WHERE CONCAT(customer_p.last_name, " ", customer_p.first_name) like ' . "'%$term%'";
			}

		}

		// create a temporary table to contain all the payment types and amount
		$this->db->query('CREATE TEMPORARY TABLE IF NOT EXISTS ' . $this->db->dbprefix('sales_payments_temp') . 
			' (PRIMARY KEY(sale_id), INDEX(sale_id))
			(
				SELECT payments.sale_id AS sale_id, 
					IFNULL(SUM(payments.payment_amount), 0) AS sale_payment_amount,
					GROUP_CONCAT(CONCAT(payments.payment_type, " ", payments.payment_amount) SEPARATOR ", ") AS payment_type,
					CONCAT(customer_p.last_name, " ", customer_p.first_name) AS customer_name,
					sales.comment AS comment
				FROM ' . $this->db->dbprefix('sales_payments') . ' AS payments
				INNER JOIN ' . $this->db->dbprefix('sales') . ' AS sales
					ON sales.sale_id = payments.sale_id
				LEFT OUTER JOIN ' . $this->db->dbprefix('people') . ' AS customer_p
					ON sales.customer_id = customer_p.person_id
				LEFT OUTER JOIN ' . $this->db->dbprefix('customers') . ' AS customer
					ON sales.customer_id = customer.person_id	
				' . "
				$where
				" . '
				GROUP BY payments.sale_id
			)'
		);

		$this->db->query('CREATE TEMPORARY TABLE IF NOT EXISTS ' . $this->db->dbprefix('sales_items_temp') . 
			' (INDEX(account_number), INDEX(sale_id), INDEX(customer_name),INDEX(phone_number))
			(
				SELECT
					DATE(sales.sale_time) AS sale_date,
					sales.sale_time,
					sales.sale_id,
					sales.comment,
					sales.invoice_number,
					sales.customer_id,
					CONCAT(customer_p.last_name, " ", customer_p.first_name) AS customer_name,
					customer_p.first_name AS customer_first_name,
					customer_p.phone_number,
					customer.account_number,
					customer_p.last_name AS customer_last_name,
					customer_p.email AS customer_email,
					customer_p.comments AS customer_comments, 
					customer.company_name AS customer_company_name,
					sales.employee_id,
					CONCAT(employee.first_name, " ", employee.last_name) AS employee_name,
					items.item_id,
					items.name,
					items.category,
					items.supplier_id,
					sales_items.quantity_purchased,
					sales_items.item_cost_price,
					sales_items.item_unit_price,
					sales_items.discount_percent,
					sales_items.line,
					sales_items.serialnumber,
					sales_items.item_location,
					sales_items.description,
					payments.payment_type,
					payments.sale_payment_amount,
					IFNULL(SUM(sales_items_taxes.percent), 0) AS item_tax_percent,
					' . "
					ROUND($sale_subtotal, $decimals) AS subtotal,
					IFNULL(ROUND($sale_tax, $decimals), 0) AS tax,
					IFNULL(ROUND($sale_total, $decimals), ROUND($sale_subtotal, $decimals)) AS total,
					ROUND($sale_cost, $decimals) AS cost,
					ROUND($sale_total - IFNULL($sale_tax, 0) - $sale_cost, $decimals) AS profit
					" . '
				FROM ' . $this->db->dbprefix('sales_items') . ' AS sales_items
				INNER JOIN ' . $this->db->dbprefix('sales') . ' AS sales
					ON sales_items.sale_id = sales.sale_id
				INNER JOIN ' . $this->db->dbprefix('items') . ' AS items
					ON sales_items.item_id = items.item_id
				LEFT OUTER JOIN ' . $this->db->dbprefix('sales_payments_temp') . ' AS payments
					ON sales_items.sale_id = payments.sale_id		
				LEFT OUTER JOIN ' . $this->db->dbprefix('suppliers') . ' AS supplier
					ON items.supplier_id = supplier.person_id
				LEFT OUTER JOIN ' . $this->db->dbprefix('people') . ' AS customer_p
					ON sales.customer_id = customer_p.person_id
				LEFT OUTER JOIN ' . $this->db->dbprefix('customers') . ' AS customer
					ON sales.customer_id = customer.person_id
				LEFT OUTER JOIN ' . $this->db->dbprefix('people') . ' AS employee
					ON sales.employee_id = employee.person_id
				LEFT OUTER JOIN ' . $this->db->dbprefix('sales_items_taxes') . ' AS sales_items_taxes
					ON sales_items.sale_id = sales_items_taxes.sale_id AND sales_items.item_id = sales_items_taxes.item_id AND sales_items.line = sales_items_taxes.line
				' . "
				$where
				" . '
				GROUP BY sales.sale_id, items.item_id, sales_items.line
			)'
		);

		// drop the temporary table to contain memory consumption as it's no longer required
		$this->db->query('DROP TEMPORARY TABLE IF EXISTS ' . $this->db->dbprefix('sales_payments_temp'));
	}

	//public function
}
?>
