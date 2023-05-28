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
		<?php //do nothing for payment update ?>
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
				<?php
				if ($this->Employee->has_grant('reports_sales', $this->session->userdata('person_id')))
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

					<?php
					if ($this->Employee->has_grant('reports_sales', $this->session->userdata('person_id')))
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
	<?php if($sale_id > 0): ?>
		<?php //do nothing for payment update ?>
	<?php else: ?>
	<?php echo form_open($controller_name."/add", array('id'=>'add_item_form', 'class'=>'form-horizontal panel panel-default')); ?>
		<div class="panel-body form-group">
			<ul>
				<li class="pull-left first_li">
					<label for="item" class='control-label'><?php echo $this->lang->line('sales_find_or_scan_item_or_receipt'); ?></label>
				</li>
				<li class="pull-left">
					<?php echo form_input(array('name'=>'item', 'id'=>'item', 'class'=>'form-control input-sm', 'size'=>'50', 'tabindex'=>++$tabindex)); ?>
					<span class="ui-helper-hidden-accessible" role="status"></span>
				</li>
				<li class="pull-right">
					<button id='new_item_button' class='btn btn-info btn-sm pull-right modal-dlg' data-btn-new='<?php echo $this->lang->line('common_new') ?>' data-btn-submit='<?php echo $this->lang->line('common_submit')?>' data-href='<?php echo site_url("items/view"); ?>'
							title='<?php echo $this->lang->line($controller_name . '_new_item'); ?>'>
						<span class="glyphicon glyphicon-tag">&nbsp</span><?php echo $this->lang->line($controller_name. '_new_item'); ?>
					</button>
				</li>
			</ul>
		</div>
	<?php echo form_close(); ?>
	<?php endif; ?>

<!-- Sale Items List -->
	<?php if($sale_id > 0): ?>
		<?php //do nothing for payment update ?>
		<table class="sales_table_100" id="register">
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
						<td><?php echo $item['item_number']; ?></td>
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

						<td><?php echo to_decimals($item['discount'], 0);?></td>
						<td><?php echo to_currency($item['price']*$item['quantity']-$item['price']*$item['quantity']*$item['discount']/100); ?></td>
						<td></td>
					</tr>
					<tr>
						<?php
						if($item['allow_alt_description']==1)
						{
							?>
							<td style="color: #2F4F4F;"><?php echo $this->lang->line('sales_description_abbrv');?></td>
							<?php
						}
						?>

						<td colspan='2' style="text-align: left;">
							<?php
							if($item['allow_alt_description']==1)
							{
								echo $item['description'];
							}
							else
							{
								if ($item['description']!='')
								{
									echo $item['description'];
								}
								else
								{
									echo $this->lang->line('sales_no_description');
								}
							}
							?>
						</td>
						<td>&nbsp;</td>
						<td style="color: #2F4F4F;">
							<?php
							if($item['is_serialized']==1)
							{
								echo $this->lang->line('sales_serial');
							}
							?>
						</td>
						<td colspan='4' style="text-align: left;">
							<?php
							if($item['is_serialized']==1)
							{
								echo $item['serialnumber'];
							}
							else
							{

							}
							?>
						</td>
					</tr>
					<?php echo form_close(); ?>
					<?php
				}
			}
			?>
			</tbody>
		</table>
	<?php else: ?>
		<table class="sales_table_100" id="register">
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
							<td><?php echo $item['item_number']; ?></td>
							<td style="align: center;">
								<?php echo $item['name']; ?><br /> <?php echo '[' . to_quantity_decimals($item['in_stock']) . ' in ' . $item['stock_name'] . ']'; ?>
								<?php echo form_hidden('location', $item['item_location']); ?>
							</td>

							<?php
							if ($items_module_allowed)
							{
							?>
								<td><?php echo form_input(array('name'=>'price', 'class'=>'form-control input-sm', 'value'=>to_currency_no_money($item['price']), 'tabindex'=>++$tabindex));?></td>
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
									echo form_input(array('name'=>'quantity', 'class'=>'form-control input-sm', 'value'=>to_quantity_decimals($item['quantity']), 'tabindex'=>++$tabindex));
								}
								?>
							</td>

							<td><?php echo form_input(array('name'=>'discount', 'class'=>'form-control input-sm', 'value'=>to_decimals($item['discount'], 0), 'tabindex'=>++$tabindex));?></td>
							<td><?php echo to_currency($item['price']*$item['quantity']-$item['price']*$item['quantity']*$item['discount']/100); ?></td>
							<td><a href="javascript:document.getElementById('<?php echo 'cart_'.$line ?>').submit();" title=<?php echo $this->lang->line('sales_update')?> ><span class="glyphicon glyphicon-refresh"></span></a></td>
						</tr>
						<tr>
							<?php 
							if($item['allow_alt_description']==1)
							{
							?>
								<td style="color: #2F4F4F;"><?php echo $this->lang->line('sales_description_abbrv');?></td>
							<?php 
							}
							?>

							<td colspan='2' style="text-align: left;">
								<?php
								if($item['allow_alt_description']==1)
								{
									echo form_input(array('name'=>'description', 'class'=>'form-control input-sm', 'value'=>$item['description']));
								}
								else
								{
									if ($item['description']!='')
									{
										echo $item['description'];
										echo form_hidden('description', $item['description']);
									}
									else
									{
										echo $this->lang->line('sales_no_description');
										echo form_hidden('description','');
									}
								}
								?>
							</td>
							<td>&nbsp;</td>
							<td style="color: #2F4F4F;">
								<?php
								if($item['is_serialized']==1)
								{
									echo $this->lang->line('sales_serial');
								}
								?>
							</td>
							<td colspan='4' style="text-align: left;">
								<?php
								if($item['is_serialized']==1)
								{
									echo form_input(array('name'=>'serialnumber', 'class'=>'form-control input-sm', 'value'=>$item['serialnumber']));
								}
								else
								{
									echo form_hidden('serialnumber', '');
								}
								?>
							</td>
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
						<th style="width: 45%; text-align: right;"><?php echo $phone_number; ?></th>
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
					<th style='width: 55%;'><?php echo $this->lang->line("sales_customer_discount"); ?></th>
					<th style="width: 45%; text-align: right;"><?php echo $customer_discount_percent . ' %'; ?></th>
				</tr>
				<tr>
					<th style='width: 55%;'><?php echo $this->lang->line("sales_customer_total"); ?></th>
					<th style="width: 45%; text-align: right;"><?php echo to_currency($customer_total); ?></th>
				</tr>
				<tr>
					<td colspan="2">
						<?php if(count($detail_tests)): // Hiển thị lịch sử kiểm tra mắt?>
						<table id="list_customer_tests" class="table-bordered" style="background-color: #fff; width: 100%;">
							<tr>
								<td>
									Ngày tháng
								</td>
								<td>

								</td>
							</tr>
							<?php foreach ($detail_tests as $detail_test) : ?>
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
											<td colspan='6'><?php echo $detail_test['note']; ?></td>
										</tr>

										</tbody>
									</table>
								</td>

							</tr>
							<?php endforeach; ?>
						</table>
						<?php endif; //Kết thúc hiển thị lịch sử kiểm tra mắt ?>
					</td>
				</tr>
			</table>

			<?php echo anchor($controller_name."/remove_customer", '<span class="glyphicon glyphicon-remove">&nbsp</span>' . 'Đổi thông tin',
								array('class'=>'btn btn-danger btn-sm', 'id'=>'remove_customer_button', 'title'=>'Đổi thông tin')); ?>
		<?php
		}
		else
		{
		?>
			<?php echo form_open($controller_name."/select_customer", array('id'=>'select_customer_form', 'class'=>'form-horizontal')); ?>

		<?php if(count($tests)): //Hiển thị danh sách bệnh nhân hôm nay?>
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
						<td> <?php// var_dump($test); ?>
							<?php echo $test['last_name']. ' ' . $test['first_name']; ?>
						</td>
						<td>
							<?php echo $test['phone_number']; ?>
						</td>
						<td>
							<?php echo anchor("sales/test/". $test['customer_id']."/0", '<span class="glyphicon glyphicon-ok"></span>',
							array('title' => 'Chọn khách hàng')); ?>
						</td>
					</tr>
				<?php endforeach; ?>
			</table>
		<?php endif; //kết thúc danh sách bệnh nhân hôm nay ?>
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
				<th style='width: 55%;'><?php echo $this->lang->line('order_total'); ?></th>
				<th style="width: 45%; text-align: right;"><?php echo to_currency($total); ?></th>
			</tr>
		</table>
	
		<?php
		// Only show this part if there are Items already in the sale.
		if(count($cart) > 0) //đã thêm sản phẩm vào cart
		{
		?>
			<div id="payment_details">
				<?php echo form_open($controller_name."/add_payment", array('id'=>'add_payment_form', 'class'=>'form-horizontal')); ?>
				<table class="sales_table_100">
					<tr>
						<td><?php echo $this->lang->line('sales_ctv');?></td>
						<td>
							<?php echo form_dropdown('ctvs', $ctvs, array(), array('id'=>'ctv', 'class'=>'selectpicker show-menu-arrow', 'data-style'=>'btn-default btn-sm', 'data-width'=>'auto')); ?>
						</td>
					</tr>
					<tr>
						<td><?php echo $this->lang->line('shipping_methods');?></td>
						<td>
							<?php if($order_completed < 2){?>
							<?php echo form_dropdown('shipping_methods', $shipping_methods, $shipping_method, array('id'=>'shipping_methods', 'class'=>'selectpicker show-menu-arrow', 'data-style'=>'btn-default btn-sm', 'data-width'=>'auto')); ?>
							<?php } else { ?>
								<?php echo form_input(array('name'=>'shipping_methods_txt', 'id'=>'shipping_methods_txt','disabled'=>'disabled', 'class'=>'form-control input-sm', 'value'=>get_shiping_method_by_id($shipping_method), 'size'=>'5', 'tabindex'=>'')); ?> </br>
							<?php }?>
			</td>
					</tr>
					<tr>
						<td><?php echo $this->lang->line('order_source');?></td>
						<td>
							<?php if($order_completed < 2){?>
								<?php echo form_dropdown('order_source', $sources, $order_source, array('id'=>'order_source', 'class'=>'selectpicker show-menu-arrow', 'data-style'=>'btn-default btn-sm', 'data-width'=>'auto')); ?>
							<?php } else { ?>
								<?php echo form_input(array('name'=>'order_source_txt', 'id'=>'order_source_txt','disabled'=>'disabled', 'class'=>'form-control input-sm', 'value'=>get_the_order_source_by_id($order_source), 'size'=>'5', 'tabindex'=>'')); ?> </br>
							<?php }?>
							</td>
					</tr>
					<tr>
						<td><?php echo $this->lang->line('shipping_address');?></td>
						<td>
							<?php if($order_completed < 2){?>
							<?php echo $this->lang->line('same_add');?> <?php echo form_radio('add', 1, $shipping_add_type == 1?TRUE:FALSE, "id='same_add'"); ?> </br>
							<?php echo $this->lang->line('diff_add');?> <?php echo form_radio('add', 2, $shipping_add_type == 2?TRUE:FALSE, "id='diff_add'"); ?> </br>
							<?php } else { ?>
								<?php echo $this->lang->line('same_add');?> <?php echo form_radio('add', 1, $shipping_add_type == 1?TRUE:FALSE, "id='same_add' disabled='disabled'"); ?> </br>
								<?php echo $this->lang->line('diff_add');?> <?php echo form_radio('add', 2, $shipping_add_type == 2?TRUE:FALSE, "id='diff_add' disabled='disabled'" ); ?> </br>

							<?php }?>
							<?php if($shipping_add_type == 2): ?>
								<div class="" id="shipping_address_input">
									<?php if($order_completed < 2){?>
										<label class=""><?php echo $this->lang->line('shipping_phone');?>:</label>
										<?php echo form_input(array('name'=>'shipping_phone', 'id'=>'shipping_phone', 'class'=>'form-control input-sm', 'value'=>$shipping_phone, 'size'=>'5', 'tabindex'=>++$tabindex)); ?> </br>
										<label class=""><?php echo $this->lang->line('shipping_address');?>:</label>
										<?php echo form_input(array('name'=>'shipping_address', 'id'=>'shipping_address', 'class'=>'form-control input-sm', 'value'=>$shipping_address, 'size'=>'5', 'tabindex'=>++$tabindex)); ?> </br>
										<label class=""><?php echo $this->lang->line('shipping_city');?>:</label>
										<?php echo form_dropdown('shipping_city', $cities, $shipping_city, array('class'=>'form-control','id'=>'shipping_city')); ?></br>

									<?php } else { ?>
										<label class=""><?php echo $this->lang->line('shipping_phone');?>:</label>
										<?php echo form_input(array('name'=>'shipping_phone', 'id'=>'shipping_phone', 'class'=>'form-control input-sm', 'value'=>$shipping_phone, 'size'=>'5', 'tabindex'=>'','disable'=>'disable')); ?> </br>
										<label class=""><?php echo $this->lang->line('shipping_address');?>:</label>
										<?php echo form_input(array('name'=>'shipping_address', 'id'=>'shipping_address', 'class'=>'form-control input-sm', 'value'=>$shipping_address, 'size'=>'5', 'tabindex'=>'','disable'=>'disable')); ?> </br>
										<label class=""><?php echo $this->lang->line('shipping_city');?>:</label>
										<?php echo form_dropdown('shipping_city', $cities, $shipping_city, array('class'=>'form-control','id'=>'shipping_city','disabled'=>'disabled')); ?></br>


									<?php }?>
									</div>
							<?php else: ?>
								<div style="display: none" id="shipping_address_input">
									<label class=""><?php echo $this->lang->line('shipping_phone');?>:</label>
									<?php echo form_input(array('name'=>'shipping_phone', 'id'=>'shipping_phone', 'class'=>'form-control input-sm', 'value'=>$shipping_phone, 'size'=>'5', 'tabindex'=>++$tabindex)); ?> </br>
									<label class=""><?php echo $this->lang->line('shipping_address');?>:</label>
									<?php echo form_input(array('name'=>'shipping_address', 'id'=>'shipping_address', 'class'=>'form-control input-sm', 'value'=>$shipping_address, 'size'=>'5', 'tabindex'=>++$tabindex)); ?> </br>
									<label class=""><?php echo $this->lang->line('shipping_city');?>:</label>
									<?php echo form_dropdown('shipping_city', $cities, $shipping_city, array('class'=>'form-control','id'=>'shipping_city')); ?>
									</br>
								</div>
							<?php endif; ?>
						</td>
					</tr>
					<tr>
						<td><?php echo $this->lang->line('sales_payment');?></td>
						<td>
							<?php if($order_completed < 2){?>
								<?php echo form_dropdown('payment_type', $payment_options, $payment_type, array('id'=>'payment_types', 'class'=>'selectpicker show-menu-arrow', 'data-style'=>'btn-default btn-sm', 'data-width'=>'auto')); ?>
							<?php } else { ?>
								<?php echo form_input(array('name'=>'payment_type_txt', 'id'=>'payment_type_txt','disabled'=>'disabled', 'class'=>'form-control input-sm', 'value'=>get_the_payment_method_by_id($payment_type), 'size'=>'5', 'tabindex'=>'')); ?> </br>
							<?php }?>
							</td>
					</tr>
					<tr>
						<td><?php echo $this->lang->line('order_completed');?></td>
						<td>
							<?php echo form_dropdown('order_completed', $order_completeds, '', array('id'=>'order_completed', 'class'=>'selectpicker show-menu-arrow', 'data-style'=>'btn-default btn-sm', 'data-width'=>'auto')); ?>
						</td>
					</tr>
					<tr>
						<td><?php echo $this->lang->line('shipping_code');?></td>
						<td>
							<?php echo form_input(array('name'=>'shipping_code', 'id'=>'shipping_code', 'class'=>'form-control input-sm', 'value'=>$shipping_code, 'size'=>'5', 'tabindex'=>'')); ?>
						</td>
					</tr>
					<tr>
						<td>
							<label class=""><?php echo $this->lang->line('shipping_fee');?>:</label>
						</td>
						<td>
							<?php if($order_completed < 2){?>
								<?php echo form_input(array('name'=>'shipping_fee', 'id'=>'shipping_fee', 'class'=>'form-control input-sm', 'value'=>$shipping_fee, 'size'=>'5', 'tabindex'=>++$tabindex)); ?> </br>
							<?php } else { ?>
								<?php echo form_input(array('name'=>'shipping_fee', 'id'=>'shipping_fee','disabled'=>'disabled', 'class'=>'form-control input-sm', 'value'=>$shipping_fee, 'size'=>'5', 'tabindex'=>'')); ?> </br>
							<?php }?>
							</td>
					</tr>
				</table>
				<div class="container-fluid">
					<div class="no-gutter row">
						<div class="form-group form-group-sm">
							<div class="col-xs-12">
								<?php echo form_label($this->lang->line('common_comments'), 'comments', array('class'=>'control-label', 'id'=>'comment_label', 'for'=>'comment')); ?>
								<?php echo form_textarea(array('name'=>'comment', 'id'=>'comment', 'class'=>'form-control input-sm', 'value'=>$comment, 'rows'=>'2')); ?>
							</div>
						</div>
					</div>
				</div>
				<?php echo form_close(); ?>
				<div class='btn btn-sm btn-success pull-right' id='finish_sale_button' tabindex='<?php echo ++$tabindex; ?>'><span class="glyphicon glyphicon-ok">&nbsp</span><?php echo $this->lang->line('order_complete_sale'); ?></div>

			</div>

			<?php echo form_open($controller_name."/cancel", array('id'=>'buttons_form')); ?>
				<div class="form-group" id="buttons_sale">
					<?php echo form_input(array('name'=>'hidden_form', 'id'=>'hidden_form', 'class'=>'form-control input-sm', 'value'=>'1', 'type'=>'hidden')); ?>
					<?php echo form_input(array('name'=>'hidden_ctv', 'id'=>'hidden_ctv', 'class'=>'form-control input-sm', 'value'=>'', 'type'=>'hidden')); ?>
					<?php echo form_input(array('name'=>'hidden_completed', 'id'=>'hidden_completed', 'class'=>'form-control input-sm', 'value'=>'', 'type'=>'hidden')); ?>

					<div class='btn btn-sm btn-danger pull-right' id='cancel_sale_button'><span class="glyphicon glyphicon-remove">&nbsp</span><?php echo $this->lang->line('sales_cancel_sale'); ?></div>
				</div>
			<?php echo form_close(); ?>
			<?php
				// Only show this part if the payment cover the total
				if(true)
				{
				?>

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

	$('#shipping_fee').change(function(){
		$.post('<?php echo site_url($controller_name."/change_shipping_fee");?>',
			{shipping_fee: $(this).val()}
		);
	});

	$('#shipping_city').change(function(){
		$.post('<?php echo site_url($controller_name."/change_shipping_city");?>',
			{shipping_city: $(this).val()}
		);
	});

	$('#payment_types').change(function(){
		$.post('<?php echo site_url($controller_name."/change_payment_type");?>',
			{payment_type: $(this).val()}
		);
	});

	$('#shipping_methods').change(function(){
		$.post('<?php echo site_url($controller_name."/change_shipping_method");?>',
				{shipping_methods: $(this).val()}
				);
	});

	$('#order_source').change(function(){
		$.post('<?php echo site_url($controller_name."/change_source");?>',
			{order_source: $(this).val()}
		);
	});

	$( "input[name=add]:radio" ).change(function(){
		var posting = $.post('<?php echo site_url($controller_name."/change_shipping_address_type");?>',
			{add: $(this).val()},
			function (result){
				if(result.data == 2)
				{
					$('#shipping_address_input').show();

				}else {
					$('#shipping_address_input').hide();
				}
			},
			'json'
		);
	});

	$('#shipping_fee').number(true,0,',','.');

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
    	minChars: 0,
    	delay: 10,
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
		$('#dlg_form').attr('data-value', $('#customer').val());
	});

	$('#comment').keyup(function() 
	{
		$.post('<?php echo site_url($controller_name."/set_comment");?>', {comment: $('#comment').val()});
	});

	$('#shipping_code').keyup(function()
	{
		$.post('<?php echo site_url($controller_name."/set_shipping_code");?>', {shipping_code: $('#shipping_code').val()});
	});
	$('#shipping_code').keypress(function (e) {
		if (e.which == 13) {
			$.post('<?php echo site_url($controller_name."/set_shipping_code");?>', {shipping_code: $('#shipping_code').val()});
			return false;
		}
	});


	$('#shipping_phone').keyup(function()
	{
		$.post('<?php echo site_url($controller_name."/set_shipping_phone");?>', {shipping_phone: $('#shipping_phone').val()});
	});

	$('#shipping_address').keyup(function()
	{
		$.post('<?php echo site_url($controller_name."/set_shipping_address");?>', {shipping_address: $('#shipping_address').val()});
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
	
    $("#finish_sale_button").click(function()
    {
		var ctv_id = $('#ctv').val();
		$('#hidden_ctv').val(ctv_id);

		$('#hidden_completed').val($('#order_completed').val());
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
		if($('#customer').length > 0){
			if($('#customer').val() != '') {
				alert($('#customer').val());
				$('#customer').val('');
				$('#customer').focus();
			}else{
				$('#buttons_form').attr('action', '<?php echo site_url($controller_name . "/before_complete"); ?>');
				$('#buttons_form').submit();
			}
		}else{
			$('#buttons_form').attr('action', '<?php echo site_url($controller_name . "/before_complete"); ?>');
			$('#buttons_form').submit();
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


	$("#payment_types").change(check_payment_type_giftcard).ready(check_payment_type_giftcard);

	$("#cart_contents input").keypress(function(event)
	{
		if (event.which == 13)
		{
			$(this).parents("tr").prevAll("form:first").submit();
		}
	});

	$("#shipping_fee").keypress(function(event)
	{
		if( event.which == 13 )
		{

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
	if ($("#payment_types").val() == "<?php echo $this->lang->line('sales_giftcard'); ?>")
	{
		$("#amount_tendered_label").html("<?php echo $this->lang->line('sales_giftcard_number'); ?>");
		$("#amount_tendered:enabled").val('').focus();
	}
	else
	{
		$("#amount_tendered_label").html("<?php echo $this->lang->line('order_amount_tendered'); ?>");
		$("#amount_tendered:enabled").val('');
	}
}

</script>

<?php $this->load->view("partial/footer"); ?>
