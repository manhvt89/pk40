<div id="receipt_wrapper">
	<div id="receipt_header">
		<?php
		if ($this->config->item('company_logo') != '') 
        { 
        ?>
			<div id="company_name" class="logo">
				<img id="image" src="<?php echo base_url('uploads/' . $this->config->item('company_logo')); ?>" alt="company_logo" />
			</div>
		<?php
		}
		?>

		<div id="company_name1" class="name" ><?php echo $this->config->item('company'); ?></div>
		<div id="company_address" class="address" ><?php echo $this->lang->line('order_address')?>: <?php echo nl2br($this->config->item('address')); ?></div>
		<div id="company_address" class="phone" ><?php echo $this->lang->line('order_phone')?>: <?php echo $this->config->item('phone'); ?></div>
		<div id="company_phone" class="phone" ><?php echo $this->lang->line('order_website')?>: <?php echo $this->config->item('website'); ?></div>

		<div class="clearboth"></div>
		<div id="sale_receipt"><?php echo $this->lang->line('order_receive_title'); ?></div>
		<div id=""><?php echo $this->lang->line('order_id').": ".$code; ?></div>

		<div class="clearboth"></div>

	</div>

	<div id="receipt_general_info">

		<?php
		if(isset($customer))
		{
		?>
			<div id="customer_number" class="customer_number"><?php echo $this->lang->line('order_customer_id')?>: <?php echo $account_number; ?></div>
			<div id="customer" class="customer"><?php echo $this->lang->line('customers_customer').": ". mb_convert_case($customer, MB_CASE_TITLE, "UTF-8"); ?></div>
			<div class="clearboth"></div>
			<div id="customer_phone" class="order_phone"><?php echo $this->lang->line('order_shipping_phone')?>: <?php echo $phone_number; ?></div>
            <div id="customer_address" class="order_address"><?php echo $this->lang->line('shipping_address')?>: <?php echo $shipping_address; ?></div>
			<?php
		}
		?>

	</div>
	<div class="order_receipt_items">
		<table id="receipt_items">
		<tr>
			<th style="width:40%;"><?php echo $this->lang->line('sales_description_abbrv'); ?></th>
			<th style="width:20%;"><?php echo $this->lang->line('sales_price'); ?></th>
			<th style="width:10%;"><?php echo $this->lang->line('sales_quantity'); ?></th>
			<th style="width:10%;">[KM]</th>
			<th style="width:20%;" class="total-value"><?php echo $this->lang->line('sales_total_amount'); ?></th>
		</tr>
		<?php
		$i = 0;
		foreach(array_reverse($cart, true) as $line=>$item)
		{ $i++;
		?>
			<tr>
				<td><?php echo $i .'. '. ucfirst($item['name']); ?></td>
				<td><?php echo number_format($item['price']); ?></td>
				<td><?php echo to_quantity_decimals($item['quantity']); ?></td>
				<?php
				if ($item['discount'] > 0)
				{
					?>
						<td class="discount"><?php echo number_format($item['discount'], 0) . "%"?></td>
					<?php
				}else{ ?>
					<td class="discount">0</td>
				<?php
				}
				?>
				<td class="total-value"><?php echo number_format($item[($this->config->item('receipt_show_total_discount') ? 'total' : 'discounted_total')]); ?></td>
			</tr>
			<!-- <tr>
				<?php
				if($this->config->item('receipt_show_description'))
				{
				?>
					<td colspan="2"><?php echo $item['description']; ?></td>
				<?php
				}
				?>

			</tr> -->

		<?php
		}
		?>
	
		<?php
		if ($this->config->item('receipt_show_total_discount') && $discount > 0)
		{
		?> 
			<tr>
				<td colspan="4" style='text-align:right;border-top: 1px dashed black;'><?php echo $this->lang->line('order_sub_total'); ?></td>
				<td style='text-align:right;border-top: 1px dashed black;'><?php echo number_format($subtotal); ?></td>
			</tr>
			<tr>
				<td colspan="4" class="total-value"><?php echo $this->lang->line('sales_discount'); ?>:</td>
				<td class="total-value"><?php echo number_format($discount * 1); ?></td>
			</tr>
		<?php
		}
		?>


		<tr class="total">
			<td colspan="4" style="text-align:right;border-top: 1px dashed black;"><?php echo $this->lang->line('order_total'); ?></td>
			<td style="text-align:right;border-top: 1px dashed black;"><?php echo number_format($total); ?></td>
		</tr>

		<tr class="total_words">
			<td colspan="5">Bằng chữ:&nbsp;<?php echo convert_number_to_words(number_format($total,0,'.','')); ?> đồng</td>
		</tr>
			<tr class="total_payment_method">
				<td colspan="4">Phương thức thanh toán:&nbsp;<?php echo $payment_type; ?></td>
				<td style="text-align:right;"><?php echo number_format($shipping_fee); ?></td>
			</tr>

		<tr class="total_total">
			<td colspan="4" style="text-align:right;"><?php echo $this->lang->line('order_total_amount'); ?></td>
			<td class="total-value"><?php echo number_format($shipping_fee + $total); ?></td>
		</tr>
			<tr class="total_comment">
				<td colspan="5" style="text-align:left;">
					<?php echo $comment; ?>
				</td>

			</tr>
	</table>
	</div>
	<div id="" class="time"><?php echo $this->lang->line('order_date') . ': '.$transaction_time ?></div>
	<div class="clearboth"></div>
	<div class="barcode">
		<?php
		if(isset($customer))
		{
			?>
			<?php $barcode = $this->barcode_lib->generate_receipt_barcode($account_number); ?>
			<img src='data:image/png;base64,<?php echo $barcode; ?>' />
			<?php
		}
		?>

	</div>
    <br />
    <br />
    <br />
	<!-- <div id="sale_return_policy">
		<?php echo nl2br($this->config->item('return_policy')); ?>
	</div>

	<div id="barcode">
		<img src='data:image/png;base64,<?php echo $barcode; ?>' /><br>
		<?php echo $sale_id; ?>
	</div> -->
</div>