<?php $this->load->view("partial/header"); ?>

<?php
if (isset($error))
{
	echo "<div class='alert alert-dismissible alert-danger'>".$error."</div>";
}

if (!empty($warning))
{
	echo "<div class='alert alert-dismissible alert-warning'>".$warning."</div>";
}

if (isset($success))
{
	echo "<div class='alert alert-dismissible alert-success'>".$success."</div>";
}

?>

<div id="register_wrapper">

<!-- Top register controls -->
	<?php if($sale_id > 0): ?> 
		<?php 
			// Sản phẩm đã tồn tại, edit đơn hàng này, hoặc thanh toán. //do nothing for payment update 
		//echo $edit;
		?>
		<?php echo form_open($controller_name."/change_mode", array('id'=>'mode_form', 'class'=>'form-horizontal panel panel-default')); ?>
		<div class="panel-body form-group">
			<ul>
				<li class="pull-left first_li">
					<label class="control-label"><?php echo $this->lang->line('sales_mode'); ?></label>
				</li>
				<li class="pull-left">
					<?php $tmp_modes[$mode] = $modes[$mode]; ?>
					<?php echo form_dropdown('mode', $tmp_modes, $mode, array('onchange'=>"$('#mode_form').submit();", 'class'=>'selectpicker show-menu-arrow', 'data-style'=>'btn-default btn-sm', 'data-width'=>'fit')); ?>
				</li>

				<?php
				if (count($stock_locations) > 1)
				{
					?>
					<li class="pull-left">
						<label class="control-label"><?php echo $this->lang->line('sales_stock_location'); ?></label>
					</li>
					<li class="pull-left">
						<?php $tmp_stock_locations[$stock_location] = $stock_locations[$stock_location]; ?>
						<?php echo form_dropdown('stock_location', $tmp_stock_locations, $stock_location, array('onchange'=>"$('#mode_form').submit();", 'class'=>'selectpicker show-menu-arrow', 'data-style'=>'btn-default btn-sm', 'data-width'=>'fit')); ?>
					</li>
					<?php
				}
				?>

				<li class="pull-right">
				<?php echo anchor($controller_name."/pending", '<span class="glyphicon glyphicon-list-alt">&nbsp</span>' . 'Danh sách chờ thanh toán',
							array('class'=>'btn btn-info btn-sm', 'id'=>'sales_pending_button', 'title'=>'Danh sách chờ thanh toán')); ?>
					
				</li>

				<?php
				if ($this->Employee->has_grant('sales_manage')) // Hiển thị danh sách đơn hàng;
				{
					?>
					<li class="pull-right">
						<?php echo anchor($controller_name."/manage", '<span class="glyphicon glyphicon-list-alt">&nbsp</span>' . $this->lang->line('sales_takings'),
							array('class'=>'btn btn-primary btn-sm', 'id'=>'sales_takings_button', 'title'=>$this->lang->line('sales_takings'))); ?>
					</li>
					<?php
				}
				?>
			</ul>
		</div>
		<?php echo form_close(); ?>
	<?php else: ?>
		<?php echo form_open($controller_name."/change_mode", array('id'=>'mode_form', 'class'=>'form-horizontal panel panel-default')); ?>
			<div class="panel-body form-group">
				<ul>
					<li class="pull-left first_li">
						<label class="control-label"><?php echo $this->lang->line('sales_mode'); ?></label>
					</li>
					<li class="pull-left">
						<?php echo form_dropdown('mode', $modes, $mode, array('onchange'=>"$('#mode_form').submit();", 'class'=>'selectpicker show-menu-arrow', 'data-style'=>'btn-default btn-sm', 'data-width'=>'fit')); ?>
					</li>

					<?php
					if (count($stock_locations) > 1)
					{
					?>
						<li class="pull-left">
							<label class="control-label"><?php echo $this->lang->line('sales_stock_location'); ?></label>
						</li>
						<li class="pull-left">
							<?php echo form_dropdown('stock_location', $stock_locations, $stock_location, array('onchange'=>"$('#mode_form').submit();", 'class'=>'selectpicker show-menu-arrow', 'data-style'=>'btn-default btn-sm', 'data-width'=>'fit')); ?>
						</li>
					<?php
					}
					?>

					<li class="pull-right">
					<?php echo anchor($controller_name."/pending", '<span class="glyphicon glyphicon-list-alt">&nbsp</span>' . 'Danh sách chờ thanh toán',
							array('class'=>'btn btn-info btn-sm', 'id'=>'sales_pending_button', 'title'=>'Danh sách chờ thanh toán')); ?>
					</li>

					<?php
					if ($this->Employee->has_grant('sales_manage'))
					{
					?>
						<li class="pull-right">
							<?php echo anchor($controller_name."/manage", '<span class="glyphicon glyphicon-list-alt">&nbsp</span>' . $this->lang->line('sales_takings'),
										array('class'=>'btn btn-primary btn-sm', 'id'=>'sales_takings_button', 'title'=>$this->lang->line('sales_takings'))); ?>
						</li>
					<?php
					}
					?>
				</ul>
			</div>
		<?php echo form_close(); ?>
	<?php endif; ?>
	<?php $tabindex = 0; ?>
	<?php if($edit == 1): //remove by manhvt04.02.2023 ?>
		
	<?php else: //remove by manhvt04.02.2023?>
	<?php echo form_open($controller_name."/add", array('id'=>'add_item_form', 'class'=>'form-horizontal panel panel-default')); ?>
		<div class="panel-body form-group">
			<ul>
				<li class="pull-left first_li">
					<label for="item" class='control-label'><?php echo $this->lang->line('sales_find_or_scan_item_or_receipt'); ?></label>
				</li>
				<li class="pull-left">
					<?php echo form_input(array('name'=>'item', 'id'=>'item', 'class'=>'form-control input-sm', 'size'=>'50', 'tabindex'=>++$tabindex)); ?>
					<?php echo form_input(array('name'=>'add_hidden_ctv', 'id'=>'add_hidden_ctv', 'class'=>'form-control input-sm', 'size'=>'50', 'type'=>'hidden')); ?>
					<span class="ui-helper-hidden-accessible" role="status"></span>
				</li>
				<li class="pull-left" style="font-size: large; font-weight: bold">
					<?php echo $this->lang->line('receivings_quantity').':'.$quantity; ?>
				</li>
			</ul>
		</div>
	<?php echo form_close(); ?>
	<?php endif; //remove by manhvt04.02.2023?>

<!-- Sale Items List -->
	<?php if($edit == 1): //remove by manhvt04.02.2023?>
		<?php //do nothing for payment update ?>
		<table class="sales_table_100 edit" id="register">
			<thead>
			<tr>
				<th style="width: 5%;"><?php echo $this->lang->line('common_delete'); ?></th>
				<th style="width: 15%;"><?php echo $this->lang->line('sales_item_number'); ?></th>
				<th style="width: 35%;"><?php echo $this->lang->line('sales_item_name'); ?></th>
				<th style="width: 10%;"><?php echo $this->lang->line('sales_price'); ?></th>
				<th style="width: 10%;"><?php echo $this->lang->line('sales_quantity'); ?></th>
				<th style="width: 10%;"><?php echo $this->lang->line('sales_discount'); ?></th>
				<th style="width: 10%;"><?php echo $this->lang->line('sales_total'); ?></th>
				<th style="width: 5%;"><?php echo $this->lang->line('sales_update'); ?></th>
			</tr>
			</thead>

			<tbody id="cart_contents">
			<?php
			if(count($cart) == 0)
			{
				?>
				<tr>
					<td colspan='8'>
						<div class='alert alert-dismissible alert-info'><?php echo $this->lang->line('sales_no_items_in_cart'); ?></div>
					</td>
				</tr>
				<?php
			}
			else
			{
				foreach(array_reverse($cart, true) as $line=>$item)
				{
					?>
					<?php echo form_open($controller_name."/edit_item/$line", array('class'=>'form-horizontal', 'id'=>'view_'.$line)); ?>
					<tr>
						<td></td>
						<td><?php echo $item['item_number']; ?> - <?php echo form_hidden('edit_hidden_ctv', '0'); ?></td>
						<td style="align: center;">
							<?php echo $item['name']; ?><br /> <?php echo '[' . to_quantity_decimals($item['in_stock']) . ' trong kho ' . $item['stock_name'] . ']'; ?>
						</td>

						<td>
								<?php echo to_currency($item['price']); ?>
						</td>


						<td>
							<?php
							if($item['is_serialized']==1)
							{
								echo to_quantity_decimals($item['quantity']);
							}
							else
							{
								echo to_quantity_decimals($item['quantity']);
							}
							?>
						</td>

						<td><?php echo to_decimals($item['discount'], 2);?></td>
						<td><?php echo to_currency($item['price']*$item['quantity']-$item['price']*$item['quantity']*$item['discount']/100); ?></td>
						<td></td>
					</tr>
					<?php echo form_close(); ?>
					<?php
				}
			}
			?>
			</tbody>
		</table>
	<?php else: ?>
		<table class="sales_table_100 add-new" id="register">
		<thead>
			<tr>
				<th style="width: 5%;"><?php echo $this->lang->line('common_delete'); ?></th>
				<th style="width: 15%;"><?php echo $this->lang->line('sales_item_number'); ?></th>
				<th style="width: 35%;"><?php echo $this->lang->line('sales_item_name'); ?></th>
				<th style="width: 10%;"><?php echo $this->lang->line('sales_price'); ?></th>
				<th style="width: 10%;"><?php echo $this->lang->line('sales_quantity'); ?></th>
				<th style="width: 10%;"><?php echo $this->lang->line('sales_discount'); ?></th>
				<th style="width: 10%;"><?php echo $this->lang->line('sales_total'); ?></th>
				<th style="width: 5%;"><?php echo $this->lang->line('sales_update'); ?></th>
			</tr>
		</thead>

		<tbody id="cart_contents">
			<?php
			if(count($cart) == 0)
			{
			?>
				<tr>
					<td colspan='8'>
						<div class='alert alert-dismissible alert-info'><?php echo $this->lang->line('sales_no_items_in_cart'); ?></div>
					</td>
				</tr>
			<?php
			}
			else
			{				
				foreach(array_reverse($cart, true) as $line=>$item)
				{					
			?>
					<?php echo form_open($controller_name."/edit_item/$line", array('class'=>'form-horizontal', 'id'=>'cart_'.$line)); ?>
						<tr>
							<td><?php echo anchor($controller_name."/delete_item/$line", '<span class="glyphicon glyphicon-trash"></span>');?></td>
							<td><?php echo $item['item_number']; ?><?php echo form_hidden('edit_hidden_ctv', '0'); ?></td>
							<td style="align: center;">
								<?php echo $item['name']; ?><br /> <?php echo '[' . to_quantity_decimals($item['in_stock']) . ' in ' . $item['stock_name'] . ']'; ?>
								<?php echo form_hidden('location', $item['item_location']); ?>
							</td>

							<?php
							if ($items_module_allowed)
							{ 
							?>
								<td><?php echo form_input(array('name'=>'price', 'class'=>'form-control input-sm decimal', 'value'=>to_currency_no_money($item['price']), 'tabindex'=>++$tabindex));?></td>
							<?php
							}
							else
							{
							?>
								<td>
									<?php echo to_currency($item['price']); ?>
									<?php echo form_hidden('price', to_currency_no_money($item['price'])); ?>
								</td>
							<?php
							}
							?>

							<td>
								<?php
								if($item['is_serialized']==1)
								{
									echo to_quantity_decimals($item['quantity']);
									echo form_hidden('quantity', $item['quantity']);
								}
								else
								{								
									echo form_input(array('name'=>'quantity', 'class'=>'form-control input-sm quantity', 'value'=>to_quantity_decimals($item['quantity']), 'tabindex'=>++$tabindex));
								}
								?>
							</td>

							<td><?php echo form_input(array('name'=>'discount', 'class'=>'form-control input-sm bonus', 'value'=>to_decimals($item['discount'], 2), 'tabindex'=>++$tabindex));?></td>
							<td><?php echo to_currency($item['price']*$item['quantity']-$item['price']*$item['quantity']*$item['discount']/100); ?></td>
							<td><a href="javascript:document.getElementById('<?php echo 'cart_'.$line ?>').submit();" title=<?php echo $this->lang->line('sales_update')?> ><span class="glyphicon glyphicon-refresh"></span></a></td>
						</tr>
				
					<?php echo form_close(); ?>
			<?php
				}
			}
			?>
		</tbody>
	</table>
	<?php endif; ?>
</div>
<!-- Overall Sale -->
<div id="overall_sale" class="panel panel-default">
	<div class="panel-body">
		<?php
		if(isset($customer))
		{
		?>
			<table class="sales_table_100">
				<tr>
					<th style='width: 55%;'><?php echo $this->lang->line("sales_customer"); ?></th>
					<th style="width: 45%; text-align: right;"><?php echo $customer; ?></th>
				</tr>
				<?php
				if(!empty($phone_number))
				{
				?>
					<tr>
						<th style='width: 55%;'><?php echo $this->lang->line("sales_customer_phone"); ?></th>
						<th style="width: 45%; text-align: right;"><a target="_blank" href="/customer_info/index/<?php echo $phone_number ?>" ><?php echo $phone_number; ?></a></th>
					</tr>
				<?php
				}
				?>
				<?php
				if(!empty($customer_address))
				{
				?>
					<tr>
						<th style='width: 55%;'><?php echo $this->lang->line("sales_customer_address"); ?></th>
						<th style="width: 45%; text-align: right;"><?php echo $customer_address; ?></th>
					</tr>
				<?php
				}
				?>

				<tr>
					<th style='width: 55%;'><?php echo $this->lang->line("sales_customer_point"); ?></th>
					<th style="width: 45%; text-align: right;">
						<?php echo to_currency_no_money($points); ?>
						<input type="hidden" name="c_points" id="c_points" value="<?php echo $points; ?>" />
					</th>
				</tr>
				<tr>
					<th style='width: 55%;'><?php echo $this->lang->line("sales_customer_total"); ?></th>
					<th style="width: 45%; text-align: right;"><?php echo to_currency($customer_total); ?></th>
				</tr>
				<tr>
					<td colspan="2">
						<?php if(count($detail_tests)): ?>
						<table id="list_customer_tests" class="table-bordered" style="background-color: #fff; width: 100%;">
							<tr>
								<td>
									Ngày tháng
								</td>
								<td>

								</td>
							</tr>
							<?php foreach ($detail_tests as $detail_test) : $lists = json_decode($detail_test['prescription']); //var_dump($list); die();?>
							<tr>
								<td><?php echo date('d-m-Y',$detail_test['test_time']);?></td>
								<?php $reArr =  json_decode($detail_test['right_e'],true);
								$leArr =  json_decode($detail_test['left_e'],true);
								?>
								<td>

									<table id="list_tested" class='table table-hover table-striped' width='100%'>
										<thead>
										<tr>
											<th>

											</th>
											<th>
												SPH
											</th>
											<th>
												CYL
											</th>
											<th>
												AX
											</th>
											<th>
												ADD
											</th>
											<th>
												VA
											</th>
											<th>
												PD
											</th>
										</tr>
										</thead>
										<tbody>
										<tr>
											<td><b>MP</b></td>
											<td> <?php echo $reArr['SPH'] ?> </td>
											<td> <?php echo $reArr['CYL']; ?></td>
											<td> <?php echo $reArr['AX']; ?></td>
											<td><?php echo $reArr['ADD']; ?></td>
											<td><?php echo $reArr['VA']; ?></td>
											<td><?php echo $reArr['PD']; ?></td>
										</tr>
										<tr>
											<td><b>MT</b></td>
											<td><?php echo $leArr['SPH']; ?></td>
											<td><?php echo $leArr['CYL']; ?></td>
											<td><?php echo $leArr['AX']; ?></td>
											<td><?php echo $leArr['ADD']; ?></td>
											<td><?php echo $leArr['VA']; ?></td>
											<td><?php echo $leArr['PD']; ?></td>
										</tr>
										<tr>
											<td></td>
											<td colspan='6' style="text-align: left;">
												<?php echo $detail_test['note']; ?> <br>
												<?php
													if(!empty($lists)): 
														$_index = 1; 
														foreach($lists as $list): ?>
															<?=$_index?>.<?=$list->name?> | <?=$list->sl?> <?=$list->dvt?><br/>
												<?php 
												 			$_index++; 
														endforeach;
													endif;
													?>
											</td>
										</tr>
										</tbody>
									</table>
								</td>

							</tr>
							<?php endforeach; ?>
						</table>
						<?php endif; ?>
					</td>
				</tr>
			</table>
			<?php if($sale_id == 0): ?>					
				<?php echo anchor($controller_name."/remove_customer", '<span class="glyphicon glyphicon-remove">&nbsp</span>'.'Đổi khách hàng',
								array('class'=>'btn btn-danger btn-sm', 'id'=>'remove_customer_button', 'title'=>'Đổi khách hàng')); ?>
			<?php endif; ?>
		<?php
		}
		else
		{
		?>
			<?php echo form_open($controller_name."/select_customer", array('id'=>'select_customer_form', 'class'=>'form-horizontal')); ?>

		<?php if(count($tests)): //Hiển thị danh sách khách hàng khám trong ngày; ?>
			<table id="list_tested_today" class="table table-hover table-striped" style="background-color: #fff;">

				<tr style="text-align: left; background-color: #e4e4d7">
					<td>Họ và tên</td>
					<td>
						Số điện thoại
					</td>
					<td>

					</td>
				</tr>
				<?php foreach ($tests as $test): ?>
					<tr style="text-align: left">
						<td>
							<?php echo $test['last_name']. ' ' . $test['first_name']; ?>
						</td>
						<td>
							<?php echo $test['phone_number']; ?>
						</td>
						<td>
							<?php echo anchor("sales/test/". $test['customer_id']."/".$test['test_id']."/".$test['employeer_id'], '<span class="glyphicon glyphicon-ok"></span>',
							array('title' => 'Chọn khách hàng')); ?>
						</td>
					</tr>
				<?php endforeach; ?>
			</table>
		<?php endif; ?>
			<div class="form-group" id="select_customer">
					<label id="customer_label" for="customer" class="control-label" style="margin-bottom: 1em; margin-top: -1em;"><?php echo $this->lang->line('sales_select_customer'); ?></label>
					<?php echo form_input(array('name'=>'customer', 'id'=>'customer', 'class'=>'form-control input-sm', 'value'=>$this->lang->line('sales_start_typing_customer_name')));?>

					<button class='btn btn-info btn-sm modal-dlg' data-btn-submit='<?php echo $this->lang->line('common_submit') ?>' id="dlg_form" data-value="" data-href='<?php echo site_url("customers/view"); ?>'
							title='<?php echo $this->lang->line($controller_name. '_new_customer'); ?>'>
						<span class="glyphicon glyphicon-user">&nbsp</span><?php echo $this->lang->line($controller_name. '_new_customer'); ?>
					</button>

				</div>
			<?php echo form_close(); ?>
		<?php
		}
		?>

		<table class="sales_table_100" id="sale_totals">
			<tr>
				<th style="width: 55%;"><?php echo $this->lang->line('sales_sub_total'); ?></th>
				<th style="width: 45%; text-align: right;"><?php echo to_currency($this->config->item('tax_included') ? $tax_exclusive_subtotal : $subtotal); ?></th>
			</tr>
			
			<?php
			foreach($taxes as $name=>$value)
			{
			?>
				<tr>
					<th style='width: 55%;'><?php echo $name; ?></th>
					<th style="width: 45%; text-align: right;"><?php echo to_currency($value); ?></th>
				</tr>
			<?php
			}
			?>

			<tr>
				<th style='width: 55%;'><?php echo $this->lang->line('sales_total'); ?></th>
				<th style="width: 45%; text-align: right;"><?php echo to_currency($total); ?></th>
			</tr>
		</table>
	
		<?php
		// Only show this part if there are Items already in the sale.
		if(count($cart) > 0)
		{
		?>
			<table class="sales_table_100" id="payment_totals">
				<tr>
					<th style="width: 55%;"><?php echo $this->lang->line('sales_payments_total');?></th>
					<th style="width: 45%; text-align: right;"><?php echo to_currency($payments_total); ?></th>
				</tr>
				<tr>
					<th style="width: 55%;"><?php echo $this->lang->line('sales_amount_due_1');?></th>
					<th style="width: 45%; text-align: right;">
						<?php echo to_currency($amount_due); ?>
						<input type="hidden" name="hd_amount_due" id="hd_amount_due" value="<?=number_format($amount_due,0,',','')?>" />
					</th>
				</tr>
			</table>

			<div id="payment_details">
					<?php
					// Show Complete sale button instead of Add Payment if there is no amount due left
					if($payments_cover_total)
					{
					?>
						<?php echo form_open($controller_name."/add_payment", array('id'=>'add_payment_form', 'class'=>'form-horizontal')); ?>
							<table class="sales_table_100">
								<tr>
									<td><?php echo $this->lang->line('sales_ctv');?></td>
									<td>
									<?php if($partner_id != ''): ?>
										<?php echo form_dropdown('ctvs', $ctvs, $partner_id, array('id'=>'ctv', 'class'=>'selectpicker show-menu-arrow', 'data-style'=>'btn-default btn-sm', 'data-width'=>'auto')); ?>
										<?php else:?>
											<?php echo form_dropdown('ctvs', $ctvs, '0', array('id'=>'ctv', 'class'=>'selectpicker show-menu-arrow', 'data-style'=>'btn-default btn-sm', 'data-width'=>'auto')); ?>
										<?php endif;?>
									</td>
								</tr>
								<?php if($this->sale_lib->get_edit() == 2):?>
								<?php else : ?>
								<tr>
									<td><?php echo $this->lang->line('sales_payment');?></td>
									<td>
										<?php echo form_dropdown('payment_type', $payment_options, array(), array('id'=>'payment_types', 'class'=>'selectpicker show-menu-arrow', 'data-style'=>'btn-default btn-sm', 'data-width'=>'auto', 'disabled'=>'disabled')); ?>
									</td>
								</tr>
								<tr>
									<td><span id="amount_tendered_label"><?php echo $this->lang->line('sales_amount_tendered'); ?></span></td>
									<td>
										<?php echo form_input(array('name'=>'amount_tendered', 'id'=>'amount_tendered', 'class'=>'form-control input-sm disabled', 'disabled'=>'disabled', 'value'=>'0', 'size'=>'5', 'tabindex'=>++$tabindex)); ?>
									</td>
								</tr>
								<?php endif;?>
							</table>
						<?php echo form_close(); ?>
						<?php if($this->sale_lib->get_edit() == 2):?>
							<?php if(empty($payments['Thanh toán'])): ?>
							<div class='btn btn-sm btn-success pull-left' id='add_before_complete_button' tabindex='<?php echo ++$tabindex; ?>'><span class="glyphicon glyphicon-credit-card">&nbsp</span>Cập nhật</div>
							<?php endif; ?>
						<?php else : ?>
							<div class='btn btn-sm btn-success pull-right' id='finish_sale_button' tabindex='<?php echo ++$tabindex; ?>'><span class="glyphicon glyphicon-ok">&nbsp</span><?php echo $this->lang->line('sales_complete_sale'); ?></div>
						<?php endif; ?>	
						
					<?php
					}
					else
					{
					?>
						<?php echo form_open($controller_name."/add_payment", array('id'=>'add_payment_form', 'class'=>'form-horizontal')); ?>
							<table class="sales_table_100">
								<tr>
									<td><?php echo $this->lang->line('sales_ctv');?></td>
									<td>
										<?php if($partner_id != ''): ?>
										<?php echo form_dropdown('ctvs', $ctvs, $partner_id, array('id'=>'ctv', 'class'=>'selectpicker show-menu-arrow', 'data-style'=>'btn-default btn-sm', 'data-width'=>'auto')); ?>
										<?php else:?>
											<?php echo form_dropdown('ctvs', $ctvs, '0', array('id'=>'ctv', 'class'=>'selectpicker show-menu-arrow', 'data-style'=>'btn-default btn-sm', 'data-width'=>'auto')); ?>
										<?php endif;?>
									</td>
								</tr>
								<?php if($this->sale_lib->get_edit() == 2):?>
								<?php else : ?>
									<tr>
										<td><?php echo $this->lang->line('sales_payment');?></td>
										<td>
											<?php echo form_dropdown('payment_type', $payment_options, array(), array('id'=>'payment_types', 'class'=>'selectpicker show-menu-arrow', 'data-style'=>'btn-default btn-sm', 'data-width'=>'auto')); ?>
										</td>
									</tr>
									<tr>
										<td><span id="amount_tendered_label"><?php echo $this->lang->line('sales_amount_tendered'); ?></span></td>
										<td>
											<?php echo form_input(array('name'=>'amount_tendered', 'id'=>'amount_tendered', 'class'=>'form-control input-sm', 'value'=>'', 'size'=>'5', 'tabindex'=>++$tabindex)); ?>
										</td>
									</tr>
								<?php endif; ?>
							</table>
						<?php echo form_close(); ?>
						<?php if($this->sale_lib->get_edit() == 2):?>
							<?php if(empty($payments['Thanh toán'])): ?>
							<div class='btn btn-sm btn-success pull-left' id='add_before_complete_button' tabindex='<?php echo ++$tabindex; ?>'><span class="glyphicon glyphicon-credit-card">&nbsp</span>Cập nhật</div>
							<?php endif; ?>
						<?php else : ?>
							<?php if($this->sale_lib->get_edit() == 0) : ?>
								<div class='btn btn-sm btn-success pull-left' id='add_before_complete_button' tabindex='<?php echo ++$tabindex; ?>'><span class="glyphicon glyphicon-credit-card">&nbsp</span>Xuất hàng</div>
							<?php endif; ?>
							<div class='btn btn-sm btn-success pull-right' id='add_payment_button' tabindex='<?php echo ++$tabindex; ?>'><span class="glyphicon glyphicon-credit-card">&nbsp</span><?php echo $this->lang->line('sales_add_payment'); ?></div>
						<?php endif; ?>
							
					<?php
					}
					?>

				<?php
				// Only show this part if there is at least one payment entered.
				if(isset($payments[$this->lang->line('sales_paid_money')]))
				{
					$new_payments = $payments[$this->lang->line('sales_paid_money')];
				}else{
					$new_payments = null;
				}
				//if(count($new_payments) > 0)
				if(!empty($new_payments))
				{
				?>
					<table class="sales_table_100" id="register">
						<thead>
							<tr>
								<th style="width: 10%;"><?php echo $this->lang->line('common_delete'); ?></th>
								<th style="width: 60%;"><?php echo $this->lang->line('sales_payment_type'); ?></th>
								<th style="width: 20%;"><?php echo $this->lang->line('sales_payment_amount'); ?></th>
							</tr>
						</thead>
			
						<tbody id="payment_contents">
							<?php

							foreach($new_payments as $payment_id=>$payment)
							{
							?>
								<tr>
									<td><?php echo anchor($controller_name."/delete_payment/$payment_id", '<span class="glyphicon glyphicon-trash"></span>'); ?></td>
									<td><?php echo $payment['payment_type']; ?></td>
									<td style="text-align: right;"><?php echo to_currency( $payment['payment_amount'] ); ?></td>
								</tr>
							<?php
							}
							?>
						</tbody>
					</table>
				<?php
				}
				?>
			</div>

			<?php echo form_open($controller_name."/cancel", array('id'=>'buttons_form')); ?>
				<div class="form-group" id="buttons_sale">
					<?php echo form_input(array('name'=>'hidden_form', 'id'=>'hidden_form', 'class'=>'form-control input-sm', 'value'=>'1', 'type'=>'hidden')); ?>
					<!-- <div class='btn btn-sm btn-default pull-left' id='suspend_sale_button'><span class="glyphicon glyphicon-align-justify">&nbsp</span><?php echo $this->lang->line('sales_suspend_sale'); ?></div> -->
					<?php echo form_input(array('name'=>'hidden_ctv', 'id'=>'hidden_ctv', 'class'=>'form-control input-sm', 'value'=>'', 'type'=>'hidden')); ?>

					<div class='btn btn-sm btn-danger pull-right' id='cancel_sale_button'><span class="glyphicon glyphicon-remove">&nbsp</span><?php echo $this->lang->line('sales_cancel_sale'); ?></div>
				</div>
			<?php echo form_close(); ?>


			<?php
				// Only show this part if the payment cover the total
				if($payments_cover_total)
				{
				?>
				<div class="container-fluid">
					<div class="no-gutter row">
						<div class="form-group form-group-sm">
							<div class="col-xs-12">
								<?php echo form_label($this->lang->line('common_comments'), 'comments', array('class'=>'control-label', 'id'=>'comment_label', 'for'=>'comment')); ?>
								<?php echo form_textarea(array('name'=>'comment', 'id'=>'comment', 'class'=>'form-control input-sm', 'value'=>$comment, 'rows'=>'2')); ?>
							</div>
						</div>
					</div>
					<div class="row">

						<div class="form-group form-group-sm">
							<div class="col-xs-6">
								<label for="sales_print_after_sale" class="control-label checkbox">
									<?php echo form_checkbox(array('name'=>'sales_print_after_sale', 'id'=>'sales_print_after_sale', 'value'=>1, 'checked'=>$print_after_sale)); ?>
									<?php echo $this->lang->line('sales_print_after_sale')?>
								</label>
							</div>

							<?php
							if(!empty($customer_email))
							{
							?>
								<div class="col-xs-6">
									<label for="email-receipt" class="control-label checkbox">
										<?php echo form_checkbox(array('name'=>'email_receipt', 'id'=>'email_receipt', 'value'=>1, 'checked'=>$email_receipt)); ?>
										<?php echo $this->lang->line('sales_email_receipt');?>
									</label>
								</div>
							<?php
							}
							?>
						</div>
					</div>
				<?php
				if ($mode == "sale" && $this->config->item('invoice_enable') == TRUE)
				{
				?>
					<div class="row">
						<div class="form-group form-group-sm">
							<div class="col-xs-6">
								<label class="control-label checkbox" for="sales_invoice_enable">
									<?php echo form_checkbox(array('name'=>'sales_invoice_enable', 'id'=>'sales_invoice_enable', 'value'=>1, 'checked'=>$invoice_number_enabled)); ?>
									<?php echo $this->lang->line('sales_invoice_enable');?>
								</label>
							</div>

							<div class="col-xs-6">
								<div class="input-group input-group-sm">
									<span class="input-group-addon input-sm">#</span>
									<?php echo form_input(array('name'=>'sales_invoice_number', 'id'=>'sales_invoice_number', 'class'=>'form-control input-sm', 'value'=>$invoice_number));?>
								</div>
							</div>
						</div>
					</div>
				<?php
				}
				?>
				</div>
			<?php
			}
			?>
		<?php
		}
		?>
	</div>
</div>
<script src="/dist/jquery.number.min.js"></script>
<script type="text/javascript">
$(document).ready(function()
{
	
	$('#amount_tendered').number(true,0,',','.');
	$('.decimal').number(true,0,',','.');
	$('.bonus').number(true,2,',','.');
	$('.quantity').number(true,0,',','.');

	$("#item").autocomplete(
	{
		source: '<?php echo site_url($controller_name."/item_search"); ?>',
    	minChars: 0,
    	autoFocus: false,
       	delay: 600,
		select: function (a, ui) {
			$(this).val(ui.item.value);
			$("#add_item_form").submit();
			return false;
		}
    });

	$('#item').focus();

	$('#item').keypress(function (e) {
		if (e.which == 13) {
			$('#add_item_form').submit();
			return false;
		}
	});


	$('#customer').keypress(function (e) {
		if (e.which == 13) {
			$('#select_customer_form').submit();
			return false;
		}
	});

    $('#item').blur(function()
    {
        $(this).val("<?php echo $this->lang->line('sales_start_typing_item_name'); ?>");
    });

    var clear_fields = function()
    {
        if ($(this).val().match("<?php echo $this->lang->line('sales_start_typing_item_name') . '|' . $this->lang->line('sales_start_typing_customer_name'); ?>"))
        {
            $(this).val('');
        }
    };

    $("#customer").autocomplete(
    {
		source: '<?php echo site_url("customers/suggest"); ?>',
    	minChars: 4,
		autoFocus: false,
    	delay: 600,
		select: function (a, ui) {
			$(this).val(ui.item.value);
			$("#select_customer_form").submit();
		}
    });

	$('#item, #customer').click(clear_fields).dblclick(function(event)
	{
		$(this).autocomplete("search");
	});

	$('#customer').blur(function()
    {
    	$(this).val("<?php echo $this->lang->line('sales_start_typing_customer_name'); ?>");
    });
	$('#customer').keyup(function()
	{
		console.log('Key Up');
		$('#dlg_form').attr('data-value', $('#customer').val());
	});

	$('#comment').keyup(function() 
	{
		$.post('<?php echo site_url($controller_name."/set_comment");?>', {comment: $('#comment').val()});
	});

	<?php
	if ($this->config->item('invoice_enable') == TRUE) 
	{
	?>
		$('#sales_invoice_number').keyup(function() 
		{
			$.post('<?php echo site_url($controller_name."/set_invoice_number");?>', {sales_invoice_number: $('#sales_invoice_number').val()});
		});

		var enable_invoice_number = function() 
		{
			var enabled = $("#sales_invoice_enable").is(":checked");
			$("#sales_invoice_number").prop("disabled", !enabled).parents('tr').show();
			return enabled;
		}

		enable_invoice_number();
		
		$("#sales_invoice_enable").change(function()
		{
			var enabled = enable_invoice_number();
			$.post('<?php echo site_url($controller_name."/set_invoice_number_enabled");?>', {sales_invoice_number_enabled: enabled});
		});
	<?php
	}
	?>

	$("#sales_print_after_sale").change(function()
	{
		$.post('<?php echo site_url($controller_name."/set_print_after_sale");?>', {sales_print_after_sale: $(this).is(":checked")});
	});

	$('#email_receipt').change(function() 
	{
		$.post('<?php echo site_url($controller_name."/set_email_receipt");?>', {email_receipt: $('#email_receipt').is(':checked') ? '1' : '0'});
	});

	$("#ctv").change(function()
	{
		var ctv_id = $('#ctv').val();
		
		$('#hidden_ctv').val(ctv_id);
		//var ctv_id = $('#ctv').val();
		$('#add_hidden_ctv').val(ctv_id);
		$("input[name='edit_hidden_ctv']").val(ctv_id);

		console.log($('#hidden_ctv').val());
		$.post('<?php echo site_url($controller_name."/change_ctv");?>', {ctv_id: $('#ctv').val()});
	});
	
    $("#finish_sale_button").click(function()
    {
		var ctv_id = $('#ctv').val();
		$('#hidden_ctv').val(ctv_id);
    	if($('#customer').length > 0) {
			if ($('#customer').val() != '') {
				alert($('#customer').val());
				$('#customer').val('');
				$('#customer').focus();
			} else {
				$('#buttons_form').attr('action', '<?php echo site_url($controller_name . "/complete"); ?>');
				$('#buttons_form').submit();
			}
		}else{
			$('#buttons_form').attr('action', '<?php echo site_url($controller_name . "/complete"); ?>');
			$('#buttons_form').submit();
		}
    });

	$("#add_before_complete_button").click(function()
	{
		var paymentMethod = $("#payment_types").val();
		var totalAmount = parseFloat($("#hd_amount_due").val());
		//var paymentAmount = parseFloat($(this).val());
		var paymentAmount = parseFloat($("#amount_tendered").val());
		if (((paymentMethod == "Chuyển khoản") && paymentAmount > totalAmount)) {
			alert("Số tiền thanh toán không thể lớn hơn tổng tiền hàng.");
			$(this).val(totalAmount.toFixed(0));
			return false;
		} else if ((paymentMethod == "Giảm thêm") && ((paymentAmount > 10000) || (paymentAmount > totalAmount))) {
			alert("Số tiền thanh toán không thể lớn hơn 10.000 hoặc Giảm thêm.");
			//console.log($(this));
			//$(this).val(totalAmount.toFixed(0));
			//console.log(totalAmount.toFixed(0));
			$(this).focus();
			return false;
		}

		var payment_list = $('#payment_contents tr');
		console.log(payment_list.length);
		if(payment_list.length != 0)
		{
			alert('Bạn cần xóa hết các mục đã thanh toán');
			return false;
		} 
		if($('#customer').length > 0){
			if($('#customer').val() != '') {
				alert($('#customer').val());
				$('#customer').val('');
				$('#customer').focus();
			}else{
				$('#add_payment_form').attr('action', '<?php echo site_url($controller_name . "/before_complete"); ?>');
				$('#add_payment_form').submit();
			}
		}else{
			
			$('#add_payment_form').attr('action', '<?php echo site_url($controller_name . "/before_complete"); ?>');
			$('#add_payment_form').submit();
		}
	});

	$("#suspend_sale_button").click(function()
	{ 	
		$('#buttons_form').attr('action', '<?php echo site_url($controller_name."/suspend"); ?>');
		$('#buttons_form').submit();
	});

    $("#cancel_sale_button").click(function()
    {
    	if (confirm('<?php echo $this->lang->line("sales_confirm_cancel_sale"); ?>'))
    	{
			$('#buttons_form').attr('action', '<?php echo site_url($controller_name."/cancel"); ?>');
    		$('#buttons_form').submit();
    	}
    });

	$("#add_payment_button").click(function()
	{
		//$('#add_payment_form').submit();
		var paymentMethod = $("#payment_types").val();
		var totalAmount = parseFloat($("#hd_amount_due").val());
		//var paymentAmount = parseFloat($(this).val());
		var paymentAmount = parseFloat($("#amount_tendered").val());

		//var ctv_id = $('#ctv').val();
		//$('#add_hidden_ctv').val(ctv_id);
		
		console.log(totalAmount);
		console.log(paymentAmount);
		console.log(paymentMethod);
		if (((paymentMethod == "Chuyển khoản") && paymentAmount > totalAmount)) {
			alert("Số tiền thanh toán không thể lớn hơn tổng tiền hàng.");
			$(this).val(totalAmount.toFixed(0));
			return false;
		} else if ((paymentMethod == "Giảm thêm") && ((paymentAmount > 10000) || (paymentAmount > totalAmount))) {
			alert("Số tiền thanh toán không thể lớn hơn 10.000 hoặc Giảm thêm.");
			//console.log($(this));
			//$(this).val(totalAmount.toFixed(0));
			//console.log(totalAmount.toFixed(0));
			$(this).focus();
			return false;
		} else {
			$('#add_payment_form').submit();
		}
    });

	//$("#payment_types").change(check_payment_type_giftcard).ready(check_payment_type_giftcard);
	$("#payment_types").change(function(){
		
		var paymentMethod = $(this).val();
        var totalAmount = parseFloat($("#hd_amount_due").val());
		if (paymentMethod == "Chuyển khoản" || paymentMethod == "Tiền mặt") {
            $("#amount_tendered").val(totalAmount.toFixed(0));
        } else if(paymentMethod == "Giảm thêm"){
			$("#amount_tendered").val("");
		}else {
            $("#amount_tendered").val("");
        }
		console.log(paymentMethod);
	});
	$("#payment_types").val("Tiền mặt");
	$("#amount_tendered").val(0);
	// Kiểm tra và cập nhật số tiền thanh toán nếu vượt quá tổng tiền hàng
	$("#amount_tendered").change(function() {
                var paymentMethod = $("#payment_types").val();
                var totalAmount = parseFloat($("#hd_amount_due").val());
                var paymentAmount = parseFloat($(this).val());
				console.log(totalAmount);
				console.log(paymentAmount);
                if (((paymentMethod == "Chuyển khoản") && paymentAmount > totalAmount)) {
                    //alert("Số tiền thanh toán không thể lớn hơn tổng tiền hàng.");
                    //$(this).val(totalAmount.toFixed(0));
                } else if ((paymentMethod == "Giảm thêm") && (paymentAmount > 10000 || paymentAmount > totalAmount)){
					//alert("Số tiền thanh toán không thể lớn hơn 10.000 hoặc Giảm thêm.");
					//$(this).val(0);
					//return false;
				}
    });

	$("#cart_contents input").keypress(function(event)
	{
		if (event.which == 13)
		{
			$(this).parents("tr").prevAll("form:first").submit();
		}
	});

	$("#amount_tendered").keypress(function(event)
	{
		if( event.which == 13 )
		{
			//$('#add_payment_form').submit();
			var paymentMethod = $("#payment_types").val();
			var totalAmount = parseFloat($("#hd_amount_due").val());
			var paymentAmount = parseFloat($(this).val());
			var ctv_id = $('#ctv').val();
			$('#add_hidden_ctv').val(ctv_id);

			console.log(totalAmount);
			console.log(paymentAmount);
			if (((paymentMethod == "Chuyển khoản") && paymentAmount > totalAmount)) {
				alert("Số tiền thanh toán không thể lớn hơn tổng tiền hàng.");
				$(this).val(totalAmount.toFixed(0));
				return false;
			} else if((paymentMethod == "Giảm thêm") && ((paymentAmount > 10000) || (paymentAmount > totalAmount))) {
				alert("Số tiền thanh toán không thể lớn hơn 10.000 hoặc Giảm thêm.");
				$(this).focus();
				return false;
			} else {
				$('#add_payment_form').submit();
			}
		}
	});
	
    $("#finish_sale_button").keypress(function(event)
	{
		if ( event.which == 13 )
		{
			$('#finish_sale_form').submit();
		}
	});

	dialog_support.init("a.modal-dlg, button.modal-dlg");

	table_support.handle_submit = function(resource, response, stay_open)
	{
		if(response.success) {
			if (resource.match(/customers$/))
			{
				console.log('#select_customer_form submit');
				$("#customer").val(response.id);
				$("#select_customer_form").submit();
			}
			else
			{
				var $stock_location = $("select[name='stock_location']").val();
				$("#item_location").val($stock_location);
				$("#item").val(response.id);
				if (stay_open)
				{
					$("#add_item_form").ajaxSubmit();
				}
				else
				{
					$("#add_item_form").submit();
				}
			}
		}
	}
});

function check_payment_type_giftcard()
{
	var _iWei = 50000;
	if ($("#payment_types").val() == "<?php echo $this->lang->line('sales_giftcard'); ?>")
	{
		$("#amount_tendered_label").html("<?php echo $this->lang->line('sales_giftcard_number'); ?>");
		$('#amount_tendered').prop('readonly', false);
		$("#amount_tendered:enabled").val('').focus();
	}
	else if($("#payment_types").val() == "<?php echo $this->lang->line('sales_point'); ?>"){
		
		var _fPoints = parseFloat($("#c_points").val()).toFixed(0);
		//alert(_fPoints);
		var _iDiv = Math.floor(_fPoints/_iWei);
		var _iMaxPoint = _iDiv * _iWei
		//alert(_fPoints);
		$("#amount_tendered_label").html("<?php echo $this->lang->line('sales_amount_tendered'); ?>");
		$("#amount_tendered:enabled").val(_iMaxPoint);
		$('#amount_tendered').prop('readonly', true);
		//$("#c_points").val(_fPoints - _iMaxPoint);
	}
	else
	{
		$('#amount_tendered').prop('readonly', false);
		$("#amount_tendered_label").html("<?php echo $this->lang->line('sales_amount_tendered'); ?>");
		$("#amount_tendered:enabled").val('');
	}
}

</script>

<?php $this->load->view("partial/footer"); ?>
