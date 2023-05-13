<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Sale_lib
{
	private $CI;

  	public function __construct()
	{
		$this->CI =& get_instance();
	}
	// Begin Add properties of cart by ManhVT

	public function set_points($fPoint)
	{
		$this->CI->session->set_userdata('points', $fPoint);
	}

	public function get_points()
	{
		// avoid returning a NULL that results in a 0 in the comment if nothing is set/available
		$points = $this->CI->session->userdata('points');

    	return empty($points) ? '' : $points;
	}

	public function clear_points()
	{
		$this->CI->session->unset_userdata('points');
	}

	/////////////////
	public function set_item_name($sItemName)
	{
		$this->CI->session->set_userdata('item_name', $sItemName);
	}

	public function get_item_name()
	{
		// avoid returning a NULL that results in a 0 in the comment if nothing is set/available
		$item_name = $this->CI->session->userdata('item_name');

    	return empty($item_name) ? '' : $item_name;
	}

	public function clear_item_name()
	{
		$this->CI->session->unset_userdata('item_name');
	}
    ///////////////////////
	public function set_item_category($sItemCategory)
	{
		$this->CI->session->set_userdata('item_category', $sItemCategory);
	}

	public function get_item_category()
	{
		// avoid returning a NULL that results in a 0 in the comment if nothing is set/available
		$item_category = $this->CI->session->userdata('item_category');

    	return empty($item_category) ? '' : $item_category;
	}

	public function clear_item_category()
	{
		$this->CI->session->unset_userdata('item_category');
	}
	//////////////////////////////
	public function set_item_supplier_id($sItemSupplierId)
	{
		$this->CI->session->set_userdata('item_supplier_id', $sItemSupplierId);
	}

	public function get_item_supplier_id()
	{
		// avoid returning a NULL that results in a 0 in the comment if nothing is set/available
		$item_supplier_id = $this->CI->session->userdata('item_supplier_id');

    	return empty($item_supplier_id) ? '' : $item_supplier_id;
	}

	public function clear_item_supplier_id()
	{
		$this->CI->session->unset_userdata('item_supplier_id');
	}
	//////////////////////////////
	public function set_item_number($sItemNumber)
	{
		$this->CI->session->set_userdata('item_number', $sItemNumber);
	}

	public function get_item_number()
	{
		// avoid returning a NULL that results in a 0 in the comment if nothing is set/available
		$item_number = $this->CI->session->userdata('item_number');

    	return empty($item_number) ? '' : $item_number;
	}

	public function clear_item_number()
	{
		$this->CI->session->unset_userdata('item_number');
	}
	///////////////////////////
	public function set_item_description($sItemDescription)
	{
		$this->CI->session->set_userdata('item_description', $sItemDescription);
	}

	public function get_item_description()
	{
		// avoid returning a NULL that results in a 0 in the comment if nothing is set/available
		$item_description = $this->CI->session->userdata('item_description');

    	return empty($item_description) ? '' : $item_description;
	}

	public function clear_item_description()
	{
		$this->CI->session->unset_userdata('item_description');
	}
	// END: Add properties of cart by ManhVT
	/**-------------- */

	public function set_ctv($ctv_data)
	{
		$this->CI->session->set_userdata('ctv', $ctv_data);
	}

	public function get_ctv()
	{
		if(!$this->CI->session->userdata('ctv'))
		{
			$this->set_ctv(array());
		}

		return $this->CI->session->userdata('ctv');
	}

	public function get_cart()
	{
		if(!$this->CI->session->userdata('sales_cart'))
		{
			$this->set_cart(array());
		}

		return $this->CI->session->userdata('sales_cart');
	}

	public function set_cart($cart_data)
	{
		$this->CI->session->set_userdata('sales_cart', $cart_data);
	}

	public function empty_cart()
	{
		$this->CI->session->unset_userdata('sales_cart');
		$this->set_quantity(0);
	}
	
	public function get_comment() 
	{
		// avoid returning a NULL that results in a 0 in the comment if nothing is set/available
		$comment = $this->CI->session->userdata('sales_comment');

    	return empty($comment) ? '' : $comment;
	}

	public function set_comment($comment) 
	{
		$this->CI->session->set_userdata('sales_comment', $comment);
	}

	public function clear_comment() 	
	{
		$this->CI->session->unset_userdata('sales_comment');
	}
	
	public function get_invoice_number()
	{
		return $this->CI->session->userdata('sales_invoice_number');
	}
	
	public function set_invoice_number($invoice_number, $keep_custom = FALSE)
	{
		$current_invoice_number = $this->CI->session->userdata('sales_invoice_number');
		if(!$keep_custom || empty($current_invoice_number))
		{
			$this->CI->session->set_userdata('sales_invoice_number', $invoice_number);
		}
	}
	
	public function clear_invoice_number()
	{
		$this->CI->session->unset_userdata('sales_invoice_number');
	}
	
	public function is_invoice_number_enabled() 
	{
		return ($this->CI->session->userdata('sales_invoice_number_enabled') == 'true' ||
				$this->CI->session->userdata('sales_invoice_number_enabled') == '1') &&
				$this->CI->config->item('invoice_enable') == TRUE;
	}
	
	public function set_invoice_number_enabled($invoice_number_enabled)
	{
		return $this->CI->session->set_userdata('sales_invoice_number_enabled', $invoice_number_enabled);
	}
	
	public function is_print_after_sale() 
	{
		return ($this->CI->session->userdata('sales_print_after_sale') == 'true' ||
				$this->CI->session->userdata('sales_print_after_sale') == '1');
	}
	
	public function set_print_after_sale($print_after_sale)
	{
		return $this->CI->session->set_userdata('sales_print_after_sale', $print_after_sale);
	}
	
	public function get_email_receipt() 
	{
		return $this->CI->session->userdata('sales_email_receipt');
	}

	public function set_email_receipt($email_receipt) 
	{
		$this->CI->session->set_userdata('sales_email_receipt', $email_receipt);
	}

	public function clear_email_receipt() 	
	{
		$this->CI->session->unset_userdata('sales_email_receipt');
	}

	// Multiple Payments
	public function get_payments()
	{
		if(!$this->CI->session->userdata('sales_payments'))
		{
			$this->set_payments(array());
		}

		return $this->CI->session->userdata('sales_payments');
	}

	// Multiple Payments
	public function set_payments($payments_data)
	{
		$this->CI->session->set_userdata('sales_payments', $payments_data);
	}

	// Multiple Payments
	public function add_payment($payment_type, $payment_amount,$payment_kind='',$payment_id=0)
	{
		//echo $payment_amount;
		$paymentalls = $this->get_payments();
		$payments_reserve = array();
		$payments = array();

		if($payment_kind == '')
		{
			$payment_kind = $this->CI->lang->line('sales_paid_money');
		}
		if(isset($paymentalls[$this->CI->lang->line('sales_reserve_money')]))
		{
			$payments_reserve = $paymentalls[$this->CI->lang->line('sales_reserve_money')];
		}
		if(isset($paymentalls[$this->CI->lang->line('sales_paid_money')]))
		{
			$payments = $paymentalls[$this->CI->lang->line('sales_paid_money')];
		}
		//echo $payment_kind;
		if($payment_kind == $this->CI->lang->line('sales_reserve_money'))
		{
			$_RsPayment = array(
				$payment_type => array('payment_type' => $payment_type,
					'payment_amount' => $payment_amount,
					'payment_kind' => $payment_kind,
					'payment_id'=>$payment_id));

			$payments_reserve += $_RsPayment;

			//var_dump($payments_reserve);
		} else{
			if(isset($payments[$payment_type]))
			{
				//payment_method already exists, add to payment_amount
				$payments[$payment_type]['payment_amount'] = bcadd($payments[$payment_type]['payment_amount'], $payment_amount);
			}
			else
			{
				//add to existing array
				$payment = array(
					$payment_type => array('payment_type' => $payment_type,
						'payment_amount' => $payment_amount,
						'payment_kind' => $payment_kind,
						'payment_id'=>$payment_id));

				$payments += $payment;
				//add by ManhVT for resolute points
				if($payment_type == $this->CI->lang->line('sales_point'))
				{
					$_fpoints = bcsub($this->get_points(),$payment_amount);
					$this->set_points($_fpoints);
				}

			}

		}
		//var_dump($payments_reserve);
		$paymentalls[$this->CI->lang->line('sales_reserve_money')] = $payments_reserve;
		$paymentalls[$this->CI->lang->line('sales_paid_money')] =  $payments;
		$this->set_payments($paymentalls);
	}

	// Multiple Payments
	public function edit_payment($payment_id, $payment_amount)
	{
		$payments = $this->get_payments();
		if(isset($payments[$payment_id]))
		{
			$payments[$payment_id]['payment_type'] = $payment_id;
			$payments[$payment_id]['payment_amount'] = $payment_amount;
			$this->set_payments($payments);

			return TRUE;
		}

		return FALSE;
	}

	// Multiple Payments
	public function delete_payment($payment_id)
	{
		$_aaFullPayments = $this->get_payments();
		$payments = $_aaFullPayments[$this->CI->lang->line('sales_paid_money')];

		//$this->CI->lang->line('sales_reserve_money')
		//var_dump($payments);die();
		$_aThePayment = $payments[urldecode($payment_id)];
		unset($payments[urldecode($payment_id)]);
		$_aaFullPayments[$this->CI->lang->line('sales_paid_money')] = $payments;

		if($_aThePayment['payment_type'] == $this->CI->lang->line('sales_point'))
		{
			$_fPoints = bcadd($this->get_points(),$_aThePayment['payment_amount']);
			//echo $_fPoints;die();
			$this->set_points($_fPoints);
			$this->set_paid_points(0);
		}
		$this->set_payments($_aaFullPayments);
	}

	// Multiple Payments
	public function empty_payments()
	{
		$this->CI->session->unset_userdata('sales_payments');
	}

	// Multiple Payments
	public function get_payments_total()
	{
		$subtotal = 0;
		foreach($this->get_payments() as $payments)
		{
			//var_dump($payments);
		    foreach($payments as $payment)
			{
				$subtotal = bcadd($payment['payment_amount'], $subtotal);
			}

		}

		return $subtotal;
	}

	// Multiple Payments
	public function get_amount_due()
	{
		$payment_total = $this->get_payments_total();
		$sales_total = $this->get_total();
		$amount_due = bcsub($sales_total, $payment_total);
		$precision = $this->CI->config->item('currency_decimals');
		$rounded_due = bccomp(round($amount_due, $precision, PHP_ROUND_HALF_EVEN), 0, $precision);
		// take care of rounding error introduced by round tripping payment amount to the browser
 		return  $rounded_due == 0 ? 0 : $amount_due;
	}

	public function get_customer()
	{
		if(!$this->CI->session->userdata('sales_customer'))
		{
			$this->set_customer(-1);
		}

		return $this->CI->session->userdata('sales_customer');
	}

	public function set_customer($customer_id)
	{
		$this->CI->session->set_userdata('sales_customer', $customer_id);
	}

	public function remove_customer()
	{
		$this->CI->session->unset_userdata('sales_customer');
		$this->clear_customer_cellphone();
		$this->clear_customer_total();
		$this->clear_customer_name();
		$this->clear_obj_customer();
	}
	
	public function get_employee()
	{
		if(!$this->CI->session->userdata('sales_employee'))
		{
			$this->set_employee(-1);
		}

		return $this->CI->session->userdata('sales_employee');
	}

	public function set_employee($employee_id)
	{
		$this->CI->session->set_userdata('sales_employee', $employee_id);
	}

	public function remove_employee()
	{
		$this->CI->session->unset_userdata('sales_employee');
	}

	public function get_mode()
	{
		if(!$this->CI->session->userdata('sales_mode'))
		{
			$this->set_mode('sale');
		}

		return $this->CI->session->userdata('sales_mode');
	}

	public function set_mode($mode)
	{
		$this->CI->session->set_userdata('sales_mode', $mode);
	}

	public function clear_mode()
	{
		$this->CI->session->unset_userdata('sales_mode');
	}

    public function get_sale_location()
    {
        if(!$this->CI->session->userdata('sales_location'))
        {
			$this->set_sale_location($this->CI->Stock_location->get_default_location_id());
        }

        return $this->CI->session->userdata('sales_location');
    }

    public function set_sale_location($location)
    {
        $this->CI->session->set_userdata('sales_location', $location);
    }
    
    public function clear_sale_location()
    {
    	$this->CI->session->unset_userdata('sales_location');
    }
    
    public function set_giftcard_remainder($value)
    {
    	$this->CI->session->set_userdata('sales_giftcard_remainder', $value);
    }
    
    public function get_giftcard_remainder()
    {
    	return $this->CI->session->userdata('sales_giftcard_remainder');
    }
    
    public function clear_giftcard_remainder()
    {
    	$this->CI->session->unset_userdata('sales_giftcard_remainder');
    }

	public function add_item(&$item_id, $quantity = 1, $item_location, $discount = 0, $price = NULL, $description = NULL, $serialnumber = NULL, $include_deleted = FALSE)
	{
		$item_info = $this->CI->Item->get_info_by_id_or_number($item_id);

		//make sure item exists		
		if(empty($item_info))
		{
			$item_id = -1;
            return FALSE;			
		}
		
		$item_id = $item_info->item_id;

		// Serialization and Description

		//Get all items in the cart so far...
		$items = $this->get_cart();

        //We need to loop through all items in the cart.
        //If the item is already there, get it's key($updatekey).
        //We also need to get the next key that we are going to use in case we need to add the
        //item to the cart. Since items can be deleted, we can't use a count. we use the highest key + 1.

        $maxkey = 0;                       //Highest key so far
        $itemalreadyinsale = FALSE;        //We did not find the item yet.
		$insertkey = 0;                    //Key to use for new entry.
		$updatekey = 0;                    //Key to use to update(quantity)

		foreach($items as $item)
		{
            //We primed the loop so maxkey is 0 the first time.
            //Also, we have stored the key in the element itself so we can compare.

			if($maxkey <= $item['line'])
			{
				$maxkey = $item['line'];
			}

			if($item['item_id'] == $item_id && $item['item_location'] == $item_location)
			{
				$itemalreadyinsale = TRUE;
				$updatekey = $item['line'];
                if(!$item_info->is_serialized)
                {
                    $quantity = bcadd($quantity, $items[$updatekey]['quantity']);
                }
			}
		}

		$insertkey = $maxkey+1;
		//array/cart records are identified by $insertkey and item_id is just another field.
		$price = $price != NULL ? $price : $item_info->unit_price;
		$total = $this->get_item_total($quantity, $price, $discount);
        $discounted_total = $this->get_item_total($quantity, $price, $discount, TRUE);
		//Item already exists and is not serialized, add to quantity

		if(!$itemalreadyinsale || $item_info->is_serialized)
		{
            $item = array($insertkey => array(
                    'item_id' => $item_id,
                    'item_location' => $item_location,
                    'stock_name' => $this->CI->Stock_location->get_location_name($item_location),
                    'line' => $insertkey,
                    'name' => $item_info->name,
                    'item_number' => $item_info->item_number,
					'item_category'=>$item_info->category,
					'item_supplier_id'=>$item_info->supplier_id,
                    'description' => $description != NULL ? $description : $item_info->description,
                    'serialnumber' => $serialnumber != NULL ? $serialnumber : '',
                    'allow_alt_description' => $item_info->allow_alt_description,
                    'is_serialized' => $item_info->is_serialized,
                    'quantity' => $quantity,
                    'discount' => $discount,
                    'in_stock' => $this->CI->Item_quantity->get_item_quantity($item_id, $item_location)->quantity,
                    'price' => $price,
                    'total' => $total,
                    'discounted_total' => $discounted_total,
                )
            );
			//add to existing array
			$items += $item;
		}
        else
        {
            $line = &$items[$updatekey];
            $line['quantity'] = $quantity;
            $line['total'] = $total;
            $line['discounted_total'] = $discounted_total;
        }

		$this->set_cart($items);
		$this->calculate_quantity();
		return TRUE;
	}
	
	public function out_of_stock($item_id, $item_location)
	{
		//make sure item exists		
		if($item_id != -1)
		{
			$item_quantity = $this->CI->Item_quantity->get_item_quantity($item_id, $item_location)->quantity;
			$quantity_added = $this->get_quantity_already_added($item_id, $item_location);

			if($item_quantity - $quantity_added < 0)
			{
				return $this->CI->lang->line('sales_quantity_less_than_zero');
			}
			elseif($item_quantity - $quantity_added < $this->CI->Item->get_info_by_id_or_number($item_id)->reorder_level)
			{
				return $this->CI->lang->line('sales_quantity_less_than_reorder_level');
			}
		}

		return '';
	}
	
	public function get_quantity_already_added($item_id, $item_location)
	{
		$items = $this->get_cart();
		$quanity_already_added = 0;
		foreach($items as $item)
		{
			if($item['item_id'] == $item_id && $item['item_location'] == $item_location)
			{
				$quanity_already_added+=$item['quantity'];
			}
		}
		
		return $quanity_already_added;
	}
	
	public function get_item_id($line_to_get)
	{
		$items = $this->get_cart();

		foreach($items as $line=>$item)
		{
			if($line == $line_to_get)
			{
				return $item['item_id'];
			}
		}
		
		return -1;
	}

	public function edit_item($line, $description, $serialnumber, $quantity, $discount, $price)
	{
		$items = $this->get_cart();
		if(isset($items[$line]))	
		{
			$line = &$items[$line];
			$line['description'] = $description;
			$line['serialnumber'] = $serialnumber;
			$line['quantity'] = $quantity;
			$line['discount'] = $discount;
			$line['price'] = $price;
			$line['total'] = $this->get_item_total($quantity, $price, $discount);
			$line['discounted_total'] = $this->get_item_total($quantity, $price, $discount, TRUE);
			$this->set_cart($items);
			$this->calculate_quantity();
		}

		return FALSE;
	}

	public function delete_item($line)
	{
		$items = $this->get_cart();
		unset($items[$line]);
		$this->set_cart($items);
		$this->calculate_quantity();
	}

	public function return_entire_sale($receipt_sale_id)
	{
		/* remove by manhvt to support code
		//POS #
		$pieces = explode(' ', $receipt_sale_id);
		$sale_id = $pieces[1];
		*/
		$sale_id = 0;
		$mode = $this->get_mode();
		$_oSale = $this->CI->Sale->get_sale_by_code($receipt_sale_id);
		//var_dump($_oSale);
		if(!empty($_oSale))
		{
			$sale_id = $_oSale->sale_id;
		}

		$this->empty_cart();
		$this->remove_customer();

		foreach($this->CI->Sale->get_sale_items($sale_id)->result() as $row)
		{
			if ($mode == 'return') {
				$this->add_item($row->item_id, -$row->quantity_purchased, $row->item_location, $row->discount_percent, $row->item_unit_price, $row->description, $row->serialnumber, TRUE);
			} else {
				$this->add_item($row->item_id, $row->quantity_purchased, $row->item_location, $row->discount_percent, $row->item_unit_price, $row->description, $row->serialnumber, TRUE);
			}
		}
		
		$this->set_customer($this->CI->Sale->get_customer($sale_id)->person_id);
	}
	
	public function add_item_kit($external_item_kit_id, $item_location, $discount)
	{
		//KIT #
		$pieces = explode(' ', $external_item_kit_id);
		$item_kit_id = $pieces[1];
		$result = TRUE;
		
		foreach($this->CI->Item_kit_items->get_info($item_kit_id) as $item_kit_item)
		{
			$result &= $this->add_item($item_kit_item['item_id'], $item_kit_item['quantity'], $item_location, $discount);
		}
		
		return $result;
	}

	public function copy_entire_sale($sale_id)
	{
		$this->empty_cart();
		$this->remove_customer();

		foreach($this->CI->Sale->get_sale_items($sale_id)->result() as $row)
		{
			$this->add_item($row->item_id, $row->quantity_purchased, $row->item_location, $row->discount_percent, $row->item_unit_price, $row->description, $row->serialnumber, TRUE);
		}

		foreach($this->CI->Sale->get_sale_payments($sale_id)->result() as $row)
		{
			$this->add_payment($row->payment_type, $row->payment_amount,$row->payment_kind,$row->payment_id);
		}

		$this->set_customer($this->CI->Sale->get_customer($sale_id)->person_id);
		$this->set_employee($this->CI->Sale->get_employee($sale_id)->person_id);
		$this->set_sale_id($sale_id);
	}
	
	public function copy_entire_suspended_sale($sale_id)
	{
		$this->empty_cart();
		$this->remove_customer();

		foreach($this->CI->Sale_suspended->get_sale_items($sale_id)->result() as $row)
		{
			$this->add_item($row->item_id, $row->quantity_purchased, $row->item_location, $row->discount_percent, $row->item_unit_price, $row->description, $row->serialnumber);
		}
		foreach($this->CI->Sale_suspended->get_sale_payments($sale_id)->result() as $row)
		{
			$this->add_payment($row->payment_type, $row->payment_amount);
		}
		$suspended_sale_info = $this->CI->Sale_suspended->get_info($sale_id)->row();
		$this->set_customer($suspended_sale_info->person_id);
		$this->set_comment($suspended_sale_info->comment);
		$this->set_invoice_number($suspended_sale_info->invoice_number);
		$this->clear_suspend_id();
		$this->set_suspend_id($sale_id);
	}

	public function clear_all()
	{
		$this->set_invoice_number_enabled(FALSE);
		$this->clear_mode();
		$this->empty_cart();
		$this->clear_comment();
		$this->clear_email_receipt();
		$this->clear_invoice_number();
		$this->clear_giftcard_remainder();
		$this->empty_payments();
		$this->remove_customer();
		$this->clear_sale_id();
		$this->clear_suspend_id();
		$this->clear_test_id();
		$this->clear_partner_id();
		//add clear five Item below:
		$this->clear_item_name();
		$this->clear_item_category();
		$this->clear_item_number();
		$this->clear_item_supplier_id();
		$this->clear_item_description();
		$this->clear_points();
		$this->clear_paid_points();
		$this->clear_edit();
		$this->clear_status();
		$this->clear_parent_id();
		$this->clear_current();
	}
	
	public function is_customer_taxable()
	{
		$customer_id = $this->get_customer();
		$customer = $this->CI->Customer->get_info($customer_id);
		
		//Do not charge sales tax if we have a customer that is not taxable
		return $customer->taxable or $customer_id == -1;
	}

	public function get_taxes()
	{
		$taxes = array();

		//Do not charge sales tax if we have a customer that is not taxable
		if($this->is_customer_taxable())
		{
			foreach($this->get_cart() as $line => $item)
			{
				$tax_info = $this->CI->Item_taxes->get_info($item['item_id']);

				foreach($tax_info as $tax)
				{
					$name = to_tax_decimals($tax['percent']) . '% ' . $tax['name'];
					$tax_amount = $this->get_item_tax($item['quantity'], $item['price'], $item['discount'], $tax['percent']);

					if(!isset($taxes[$name]))
					{
						$taxes[$name] = 0;
					}

					$taxes[$name] = bcadd($taxes[$name], $tax_amount);
				}
			}
		}

		return $taxes;
	}

	public function apply_customer_discount($discount_percent)
	{	
		// Get all items in the cart so far...
		$items = $this->get_cart();
		
		foreach($items as &$item)
		{
			$quantity = $item['quantity'];
			$price = $item['price'];

			// set a new discount only if the current one is 0
			if($item['discount'] == 0)
			{
				$item['discount'] = $discount_percent;
				$item['total'] = $this->get_item_total($quantity, $price, $discount_percent);
				$item['discounted_total'] = $this->get_item_total($quantity, $price, $discount_percent, TRUE);
			}
		}

		$this->set_cart($items);
	}
	
	public function get_discount()
	{
		$discount = 0;
		foreach($this->get_cart() as $item)
		{
			if($item['discount'] > 0)
			{
				$item_discount = $this->get_item_discount($item['quantity'], $item['price'], $item['discount']);
				$discount = bcadd($discount, $item_discount);
			}
		}

		return $discount;
	}

	public function get_subtotal($include_discount = FALSE, $exclude_tax = FALSE)
	{
		return $this->calculate_subtotal($include_discount, $exclude_tax);
	}
	
	public function get_item_total_tax_exclusive($item_id, $quantity, $price, $discount_percentage, $include_discount = FALSE) 
	{
		$tax_info = $this->CI->Item_taxes->get_info($item_id);
		$item_price = $this->get_item_total($quantity, $price, $discount_percentage, $include_discount);
		// only additive tax here
		foreach($tax_info as $tax)
		{
			$tax_percentage = $tax['percent'];
			$item_price = bcsub($item_price, $this->get_item_tax($quantity, $price, $discount_percentage, $tax_percentage));
		}
		
		return $item_price;
	}
	
	public function get_item_total($quantity, $price, $discount_percentage, $include_discount = FALSE)  
	{
		$total = bcmul($quantity, $price);
		if($include_discount)
		{
			$discount_amount = $this->get_item_discount($quantity, $price, $discount_percentage);

			return bcsub($total, $discount_amount);
		}

		return $total;
	}
	
	public function get_item_discount($quantity, $price, $discount_percentage)
	{
		//echo locale_get_default();
		//$config = get_instance()->config;
		//locale_set_default($config->item('number_locale'));
		//echo locale_get_default();die();
		/*
		$total = bcmul($quantity, $price);
		$discount_fraction = bcdiv($discount_percentage, '100', 4);

		return bcmul($total, $discount_fraction);
		*/
		
		$total = bcmul($quantity, $price);
		//$discount_fraction = bcdiv($discount_percentage, 100);
		$discount_time = bcdiv($total, '100');
		$discount_percentage = str_replace(',','.', $discount_percentage);
		//var_dump($discount_time); die();
		return bcmul($discount_time, $discount_percentage);
	}
	
	public function get_item_tax($quantity, $price, $discount_percentage, $tax_percentage) 
	{
		$price = $this->get_item_total($quantity, $price, $discount_percentage, TRUE);
		if($this->CI->config->config['tax_included'])
		{
			$tax_fraction = bcadd(100, $tax_percentage);
			$tax_fraction = bcdiv($tax_fraction, 100);
			$price_tax_excl = bcdiv($price, $tax_fraction);

			return bcsub($price, $price_tax_excl);
		}
		$tax_fraction = bcdiv($tax_percentage, 100);

		return bcmul($price, $tax_fraction);
	}

	public function calculate_subtotal($include_discount = FALSE, $exclude_tax = FALSE) 
	{
		$subtotal = 0;
		foreach($this->get_cart() as $item)
		{
			if($exclude_tax && $this->CI->config->config['tax_included'])
			{
				$subtotal = bcadd($subtotal, $this->get_item_total_tax_exclusive($item['item_id'], $item['quantity'], $item['price'], $item['discount'], $include_discount));
			}
			else 
			{
				$subtotal = bcadd($subtotal, $this->get_item_total($item['quantity'], $item['price'], $item['discount'], $include_discount));
			}
		}

		return $subtotal;
	}

	public function get_total()
	{
		$total = $this->calculate_subtotal(TRUE);		
		if(!$this->CI->config->config['tax_included'])
		{
			foreach($this->get_taxes() as $tax)
			{
				$total = bcadd($total, $tax);
			}
		}

		return $total;
	}

	public function set_sale_id($sale_id)
	{
		$this->CI->session->set_userdata('sale_id', $sale_id);
	}
	public function get_sale_id()
	{
		if(!$this->CI->session->userdata('sale_id'))
		{
			$this->set_sale_id(0); // ID = 0; không có bản ghi nào;
		}
		return $this->CI->session->userdata('sale_id');
	}

	public function clear_sale_id()
	{
		$this->CI->session->unset_userdata('sale_id');
	}
	public function set_suspend_id($sale_id)
	{
		$this->CI->session->set_userdata('sale_suspend_id', $sale_id);
	}
	public function get_suspend_id()
	{
		return $this->CI->session->userdata('sale_suspend_id');
	}

	public function clear_suspend_id()
	{
		$this->CI->session->unset_userdata('sale_suspend_id');
	}
	public function set_test_id($test_id)
	{
		$this->CI->session->set_userdata('sale_test_id', $test_id);
	}
	public function get_test_id()
	{
		if(!$this->CI->session->userdata('sale_test_id'))
		{
			$this->set_test_id(0);
		}
		return $this->CI->session->userdata('sale_test_id');
	}

	public function clear_test_id()
	{
		$this->CI->session->unset_userdata('sale_test_id');
	}

	public function set_partner_id($partner_id)
	{
		$this->CI->session->set_userdata('sale_partner_id', $partner_id);
	}
	public function get_partner_id()
	{
		return $this->CI->session->userdata('sale_partner_id');
	}

	public function clear_partner_id()
	{
		$this->CI->session->unset_userdata('sale_partner_id');
	}

	public function get_customer_total()
	{
		if (!empty($this->CI->session->userdata('customer_total'))) {
			return $this->CI->session->userdata('customer_total');
		} else{
			return 0;
		}
	}

	public function set_customer_total($customer_total)
	{
		$this->CI->session->set_userdata('customer_total', $customer_total);
	}

	public function clear_customer_total()
	{
		$this->CI->session->unset_userdata('customer_total');
	}

	public function get_customer_cellphone()
	{
		if (!empty($this->CI->session->userdata('customer_cellphone'))) {
			return $this->CI->session->userdata('customer_cellphone');
		} else{
			return '';
		}
	}

	public function set_customer_cellphone($customer_cellphone)
	{
		$this->CI->session->set_userdata('customer_cellphone', $customer_cellphone);
	}

	public function clear_customer_cellphone()
	{
		$this->CI->session->unset_userdata('customer_cellphone');
	}

	public function get_customer_name()
	{
		if (!empty($this->CI->session->userdata('customer_name'))) {
			return $this->CI->session->userdata('customer_name');
		} else{
			return '';
		}
	}

	public function set_customer_name($customer_name)
	{
		$this->CI->session->set_userdata('customer_name', $customer_name);
	}
	
	public function clear_customer_name()
	{
		$this->CI->session->unset_userdata('customer_name');
	}

	public function get_obj_customer()
	{
		if (!empty($this->CI->session->userdata('_acustomer'))) {
			return $this->CI->session->userdata('_acustomer');
		} else{
			return array();
		}
	}
	public function set_obj_customer($aCustomer)
	{
		$this->CI->session->set_userdata('_acustomer', $aCustomer);
	}

	public function clear_obj_customer()
	{
		$this->CI->session->unset_userdata('_acustomer');
	}

	public function set_paid_points($_point)
	{
		$this->CI->session->set_userdata('_fpoint', $_point);
	}

	public function get_paid_points()
	{
		if (!empty($this->CI->session->userdata('_fpoint'))) {
			return $this->CI->session->userdata('_fpoint');
		} else{
			return 0;
		}
	}

	public function clear_paid_points()
	{
		$this->CI->session->unset_userdata('_fpoint');
	}

	public function set_edit($_edit)
	{
		$this->CI->session->set_userdata('edit', $_edit);
	}

	public function get_edit()
	{
		if (empty($this->CI->session->userdata('edit'))) {
			$this->set_edit(0);
		} 
		return $this->CI->session->userdata('edit');
	}
	public function clear_edit()
	{
		$this->CI->session->unset_userdata('edit'); 
	}
	/* Added By ManhVT 06/01/2023 - add SL */
	public function get_quantity()
	{
		if(!$this->CI->session->userdata('sale_quantity'))
		{
			$this->set_quantity(0);
		}

		return $this->CI->session->userdata('sale_quantity');
	}

	public function set_quantity($quantity)
	{
		$this->CI->session->set_userdata('sale_quantity', $quantity);
	}

	public function clear_quantity()
	{
		$this->CI->session->unset_userdata('sale_quantity');
	}	
	public function calculate_quantity()
	{
		$items = $this->get_cart();
		$quantity = 0;
		foreach ($items as $item)
		{
			$quantity = $quantity + $item['quantity'];
		}
		$this->set_quantity($quantity);

		return false;
	}

	/**
	 * Thêm mới ManhVT, phục vụ thực hiện từng bước quy trình bán hàng.
	 * -- [1] Khám mắt
	 * -- [2] Xuất hàng - In phiếu (xuất hàng - bán hàng); chỉnh sửa nếu có; có thể có tạm ứng
	 * -- [3] Thanh toán (Hoàn thành đơn hàng);
	 * Ngày 02.02.2023
	 */
	public function set_parent_id($parent_id)
	{
		$this->CI->session->set_userdata('parent_id', $parent_id);
	}
	public function get_parent_id()
	{
		if(!$this->CI->session->userdata('parent_id'))
		{
			$this->set_parent_id(0);
		}
		return $this->CI->session->userdata('parent_id');
	}

	public function clear_parent_id()
	{
		$this->CI->session->unset_userdata('parent_id');
	}

	public function set_current($current)
	{
		$this->CI->session->set_userdata('current', $current);
	}
	public function get_current()
	{
		if(!$this->CI->session->userdata('current'))
		{
			$this->set_current(1); // Mặc định đơn hàng này đang có hiệu lực; nếu = 0 đơn hàng không có hiệu lực
		}
		return $this->CI->session->userdata('current');
	}

	public function clear_current()
	{
		$this->CI->session->unset_userdata('current');
	}

	public function set_status($status)
	{
		$this->CI->session->set_userdata('status', $status);
	}
	public function get_status()
	{
		if(!$this->CI->session->userdata('status'))
		{
			$this->set_status(1); // Trạng thái chưa thanh toán, hoặc thanh toán 1 phần (tạm ứng)
		}
		return $this->CI->session->userdata('status');
	}

	public function clear_status()
	{
		$this->CI->session->unset_userdata('status');
	}

	
}

?>
