<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Printbarcode_lib
{
	private $CI;

  	public function __construct()
	{
		$this->CI =& get_instance();
	}
	// Begin Add properties of cart by ManhVT

	public function get_cart()
	{
		if(!$this->CI->session->userdata('print_cart'))
		{
			$this->set_cart(array());
		}

		return $this->CI->session->userdata('print_cart');
	}

	public function set_cart($cart_data)
	{
		$this->CI->session->set_userdata('print_cart', $cart_data);
	}

	public function empty_cart()
	{
		$this->CI->session->unset_userdata('print_cart');
	}
	//

	public function add_item(&$item_id, $quantity = 1, $price = NULL, $serialnumber = NULL)
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

			if($item['item_id'] == $item_id)
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
		//Item already exists and is not serialized, add to quantity

		if(!$itemalreadyinsale || $item_info->is_serialized)
		{
            $item = array($insertkey => array(
                    'item_id' => $item_id,
                    'line' => $insertkey,
                    'name' => $item_info->name,
                    'item_number' => $item_info->item_number,
					'item_category'=>$item_info->category,
                    'serialnumber' => $serialnumber != NULL ? $serialnumber : '',
                    'is_serialized' => $item_info->is_serialized,
                    'quantity' => $quantity,
                    'price' => $price
                )
            );
			//add to existing array
			$items += $item;
		}
        else
        {
            $line = &$items[$updatekey];
            $line['quantity'] = $quantity;
        }

		$this->set_cart($items);

		return TRUE;
	}
	
	public function get_quantity_already_added($item_id)
	{
		$items = $this->get_cart();
		$quanity_already_added = 0;
		foreach($items as $item)
		{
			if($item['item_id'] == $item_id)
			{
				$quanity_already_added+=$item['quantity'];
			}
		}
		
		return $quanity_already_added;
	}

	public function get_quantity()
	{
		$items = $this->get_cart();
		$quanity_already_added = 0;
		foreach($items as $item)
		{
			$quanity_already_added+=$item['quantity'];	
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

	public function edit_item($line, $quantity)
	{
		$items = $this->get_cart();
		if(isset($items[$line]))	
		{
			$line = &$items[$line];
			$line['quantity'] = $quantity;
			$this->set_cart($items);
		}

		return FALSE;
	}

	public function delete_item($line)
	{
		$items = $this->get_cart();
		unset($items[$line]);
		$this->set_cart($items);
	}


	public function clear_all()
	{
		
		$this->empty_cart();
		//add clear five Item below:
	}
	
}

?>
