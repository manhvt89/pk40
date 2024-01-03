<div id="receipt_wrapper">
	<?php if ($this->config->item('print_header_receipt') == 1) :?>
		<div id="receipt_header">
			<?php
			if ($this->config->item('company_logo') != '') 
			{ 
			?>
				<div id="company_name" class="logo"><img id="image" src="<?php echo base_url('uploads/' . $this->config->item('company_logo')); ?>" alt="company_logo" /></div>
			<?php
			}
			?>
			<?php if($this->config->item('company_name_display') == 1): ?>
			<div id="company_name1" class="name" ><?php echo $this->config->item('company'); ?></div>
			<?php endif; ?>
			<div id="company_address" class="address" >Địa chỉ: <?php echo nl2br($this->config->item('address')); ?></div>
			<div id="company_address" class="phone" >Điện thoại: <?php echo $this->config->item('phone'); ?></div>
			<div id="company_phone" class="phone" >Website: <?php echo $this->config->item('website'); ?></div>

			<div class="clearboth"></div>
			<div id="sale_receipt"><?php echo $receipt_title; ?></div>
			<div id=""><?php echo $this->lang->line('sales_id').": ".$code; ?></div>
			<div id="" class="time">Ngày <?php echo $transaction_time ?></div>
			<div class="clearboth"></div>

		</div>
	<?php endif; ?>
	<div id="receipt_general_info">

		<?php
		if(isset($customer))
		{
		?>
			<div id="customer_number" class="customer_number">Mã KH: <?php echo $account_number; ?></div>
			<div id="customer" class="customer"><?php echo $this->lang->line('customers_customer').": ". mb_convert_case($customer, MB_CASE_TITLE, "UTF-8"); ?></div>
			<div class="clearboth"></div>
			<div id="customer_phone" class="phone">Điện thoại: <?php echo $phone_number; ?></div>
			<?php
		}
		?>

	</div>

	<table id="receipt_items">
	    <thead>
		<tr>
			<th style="width:40%;"><?php echo $this->lang->line('sales_description_abbrv'); ?></th>
			<th style="width:20%;"><?php echo $this->lang->line('sales_price'); ?></th>
			<th style="width:10%;"><?php echo $this->lang->line('sales_quantity'); ?></th>
			<th style="width:10%;">[KM]</th>
			<th style="width:20%;" class="total-value"><?php echo $this->lang->line('sales_total_amount'); ?></th>
		</tr>
		</thead>
		<?php
		$i = 0;
		foreach(array_reverse($cart, true) as $line=>$item)
		{ $i++;
		    $data_th = '';
		?>
			<tr>
				<th><?php echo $i .'. '. ucfirst($item['name']); ?></th>
				<td><?php echo number_format($item['price']); $data_th = number_format($item['price']); ?></td>
				<td><?php echo to_quantity_decimals($item['quantity']); $data_th = $data_th . ' x '.to_quantity_decimals($item['quantity']);?></td>
				<?php
				if ($item['discount'] > 0)
				{
					?>
						<td class="discount"><?php echo number_format($item['discount'], 0) . "%"; $data_th = $data_th.'[-'.number_format($item['discount'], 0) . "%]";?></td>
					<?php
				}else{ ?>
					<td class="discount">0</td>
				<?php
				}
				    $data_th = $data_th . '=';
				?>
				<td class="total-value" data-th="<?=$data_th?> "><?php echo number_format($item[($this->config->item('receipt_show_total_discount') ? 'total' : 'discounted_total')]); ?></td>
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
				<?php
				if($this->config->item('receipt_show_serialnumber'))
				{
				?>
					<td><?php echo $item['serialnumber']; ?></td>
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
				<td colspan="4" style='text-align:right;border-top:1px dashed black;'><?php echo $this->lang->line('sales_sub_total'); ?></td>
				<td style='text-align:right;border-top:1px dashed black;' data-th="<?php echo $this->lang->line('sales_sub_total'); ?> "><?php echo number_format($subtotal); ?></td>
			</tr>
			<tr>
				<td colspan="4" class="total-value"><?php echo $this->lang->line('sales_discount'); ?>:</td>
				<td class="total-value" data-th="<?php echo $this->lang->line('sales_discount'); ?>: "><?php echo number_format($discount * 1); ?></td>
			</tr>
		<?php
		}
		?>

		<?php
		if ($this->config->item('receipt_show_taxes'))
		{
		?> 
			<tr>
				<td colspan="4" style='text-align:right;border-top:2px solid #000000;'><?php echo $this->lang->line('sales_sub_total'); ?></td>
				<td style='text-align:right;border-top:2px solid #000000;'><?php echo number_format($this->config->item('tax_included') ? $tax_exclusive_subtotal : $discounted_subtotal); ?></td>
			</tr>
			<?php
			foreach($taxes as $name=>$value)
			{
			?>
				<tr>
					<td colspan="4" class="total-value"><?php echo $name; ?>:</td>
					<td class="total-value"><?php echo number_format($value); ?></td>
				</tr>
			<?php
			}
			?>
		<?php
		}
		?>
		<?php $border = (!$this->config->item('receipt_show_taxes') && !($this->config->item('receipt_show_total_discount') && $discount > 0)); ?> 
		<tr class="total">
			<td colspan="4" style="text-align:right;<?php echo $border? 'border-top: 1px dashed black;' :''; ?>"><?php echo $this->lang->line('sales_total'); ?></td>
			<td style="text-align:right;<?php echo $border? 'border-top: 1px dashed black;' :''; ?>" data-th="<?php echo $this->lang->line('sales_total'); ?> "><?php echo number_format($total); ?></td>
		</tr>

		<tr class="total_words">
			<td colspan="5">Bằng chữ:&nbsp;<?php echo convert_number_to_words(number_format($total,0,'.','')); ?> đồng</td>
		</tr>

		<?php
		$only_sale_check = FALSE;
		$show_giftcard_remainder = FALSE;
		$_has_prepaid = FALSE;

		if(!empty($payments[$this->lang->line('sales_reserve_money')]))
		{
			$old_payments = $payments[$this->lang->line('sales_reserve_money')];
			foreach ($old_payments as $payment_id => $payment) {
				$only_sale_check |= $payment['payment_type'] == $this->lang->line('sales_check');
				$splitpayment = explode(':', $payment['payment_type']);
				$show_giftcard_remainder |= $splitpayment[0] == $this->lang->line('sales_giftcard');

				$_sTitleDislayReceipt = 'Đặt trước: ';
				if ($payment['payment_amount'] > 0) {
					$_has_prepaid = TRUE;
					?>
				<tr class="total_reserve">
					<td colspan="4" style="text-align:right;"><?php echo $_sTitleDislayReceipt; ?></td>
					<td class="total-value" data-th="<?php echo $_sTitleDislayReceipt; ?>"><?php echo number_format($payment['payment_amount'] * 1); ?></td>
				</tr>
				<?php
				}
			}
		}

		if(!empty($payments[$this->lang->line('sales_paid_money')])) {
			$new_payments = $payments[$this->lang->line('sales_paid_money')];
			foreach ($new_payments as $payment_id => $payment) {
				$only_sale_check |= $payment['payment_type'] == $this->lang->line('sales_check');
				$splitpayment = explode(':', $payment['payment_type']);
				$show_giftcard_remainder |= $splitpayment[0] == $this->lang->line('sales_giftcard');
				$_sTitleDislayReceipt = '' . $payment['payment_type']. ': ';
				?>
				<tr class="total_paid 890809">
					<td colspan="4" style="text-align:right;"><?php echo $_sTitleDislayReceipt; //echo $this->lang->line('sales_paid_rev'); ?> </td>
					<td class="total-value" data-th="<?php echo $_sTitleDislayReceipt;//echo $this->lang->line('sales_paid_rev'); ?> "><?php echo number_format($payment['payment_amount'] * 1); ?></td>
				</tr>
				<?php
			}
		}
		?>
		<?php 
		if (isset($cur_giftcard_value) && $show_giftcard_remainder)
		{
		?>
		<tr>
			<td colspan="4" style="text-align:right;"><?php echo $this->lang->line('sales_giftcard_balance'); ?></td>
			<td class="total-value" data-th="<?php echo $this->lang->line('sales_giftcard_balance'); ?> "><?php echo number_format($cur_giftcard_value); ?></td>
		</tr>
		<?php 
		}
		?>
		<?php if($_has_prepaid): //không có trả trước?>
		<tr class="total_blance">
			<td colspan="4" style="text-align:right;"> <?php echo $this->lang->line($amount_change >= 0 ? ($only_sale_check ? 'sales_check_balance' : 'sales_change_due') : 'sales_amount_due_1') ; ?> </td>
			<td class="total-value" data-th="<?php echo $this->lang->line($amount_change >= 0 ? ($only_sale_check ? 'sales_check_balance' : 'sales_change_due') : 'sales_amount_due_1') ; ?> "><?php echo number_format($amount_change >= 0 ? $amount_change : $amount_change * -1); ?></td>
		</tr>
		<?php endif; ?>
	</table>
	<div class="employee_signal">
		<div>Nhân viên bán hàng</div>
		<div id="employee"><?php echo $employee; ?></div>
	</div>
	<div class="clearboth"></div>
	<?php if($this->config->item('barcode') ==1 ): ?>
	<div class="barcode">
		<img src='data:image/png;base64,<?php echo $barcode; ?>' />
	</div>
	<?php endif; ?>
	<?php if($this->config->item('qrcode') ==1 ): ?>
		<?php if($footer_string != ''): //Khoong hien thi phieu tam ung ?>
	<div class="qrcode" style="text-align: center;">
			<span><?php echo $footer_string ?></span><br/>
			<img src='data:image/png;base64,<?php echo $qrcode_string; ?>' />
			<?php //echo $url_string; ?>
	</div>
		<?php endif;?>
	<?php endif; ?>
	<div id="sale_return_policy">
		<?php //echo nl2br($this->config->item('return_policy')); ?>
	</div>
</div>