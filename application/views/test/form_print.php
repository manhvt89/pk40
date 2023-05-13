<div id="required_fields_message"><?php //echo $this->lang->line('common_fields_required_message'); ?></div>

<ul id="error_message_box" class="error_message_box"></ul>

<?php echo form_open($controller_name."/print", array('id'=>'print_test_form', 'class'=>'form-horizontal panel panel-default')); ?>
<?php if($this->test_lib->get_test_id()):?>
<table class="precription_header_print">
	<tbody>
		<tr>
			<td colspan="2" class="print_title">ĐƠN KÍNH</td>
		</tr>
		<tr>
			<td width="60%">
				<table>
					<tr>
						<td>Tên khách hàng: <?php echo 	$customer;?></td>
					</tr>
					<tr>
						<td>Địa chỉ: <?php echo $customer_address; ?></td>
					</tr>
				</table>
				
			</td>
			<td width="40%" style="text-align: center; vertical-align: middle;">
				
				<?php $barcode = $this->barcode_lib->generate_receipt_barcode($customer_account_number); ?>
				<img src='data:image/png;base64,<?php echo $barcode; ?>' /><br/>
				<span class="label_barcode"><?=$customer_account_number?></span>
			</td>
		</tr>
	</tbody>
</table>
<table class="sales_table_100" id="print_data">
	<thead>
	<tr>
		<th style="width: 13%;"><?php echo $this->lang->line('test_eyes'); ?></th>
		<th style="width: 10%;"><?php echo $this->lang->line('test_sph'); ?></th>
		<th style="width: 10%;"><?php echo $this->lang->line('test_cyl'); ?></th>
		<th style="width: 10%;"><?php echo $this->lang->line('test_ax'); ?></th>
		<th style="width: 10%;"><?php echo $this->lang->line('test_add'); ?></th>
		<th style="width: 10%;"><?php echo $this->lang->line('test_va'); ?></th>
		<th style="width: 9%;"><?php echo $this->lang->line('test_pd'); ?></th>
		<th style="width: 28%;"></th>
	</tr>
	</thead>
	<tbody id="print_contents">
		<tr>
			<td>
				<b>Phải (<?php echo $this->lang->line('test_right_eye') ?>)</b>
			</td>
			<td>
				<?php echo ($right_e['SPH']==0)? "PLANO": $right_e['SPH'];?>
			</td>
			<td>
				<?php echo $right_e['CYL'];?>
			</td>
			<td>
				<?php echo $right_e['AX'];?>
			</td>
			<td>
				<?php echo $right_e['ADD'];?>
			</td>
			<td>
				<?php echo $right_e['VA'];?>
			</td>
			<td>
				<?php echo $right_e['PD'];?>
			</td>
			<td>
				<div class="form-group form-group-sm">

					<div class='col-xs-1'>
						<?php echo form_checkbox(array(
								'name'=>'distance',
								'id'=>'distance',
								'value'=>"Nhìn xa",
								'checked'=> $toltal[0]? 1: 0)
						);?>
					</div>
					<?php echo form_label($this->lang->line('test_distance'), 'distance', array('class'=>'control-label col-xs-9')); ?>
				</div>
			</td>
		</tr>
		<tr>
			<td>
				<b>Trái (<?php echo $this->lang->line('test_left_eye') ?>)</b>
			</td>
			<td>
				<?php echo ($left_e['SPH']==0)?'PLANO': $left_e['SPH'];?>
			</td>
			<td>
				<?php echo $left_e['CYL'];?>
			</td>
			<td>
				<?php echo $left_e['AX'];?>
			</td>
			<td>
				<?php echo $left_e['ADD'];?>
			</td>
			<td>
				<?php echo $left_e['VA'];?>
			</td>
			<td>
				<?php echo $left_e['PD'];?>
			</td>
			<td>
				<div class="form-group form-group-sm">

					<div class='col-xs-1'>
						<?php echo form_checkbox(array(
								'name'=>'reading',
								'id'=>'reading',
								'value'=>"Nhìn gần",
								'checked'=> $toltal[1]?1:0)
						);?>
					</div>
					<?php echo form_label($this->lang->line('test_reading'), 'reading', array('class'=>'control-label col-xs-9')); ?>
				</div>
			</td>
		</tr>
	</tbody>
</table>

<table class="sales_table_100" id="body_precription">
	<thead>
	<tr>
		<th colspan="4"><?php echo $this->lang->line('test_lens'); ?></th>
	</tr>
	</thead>
	<tbody id="cart_contents">
		<tr>
			<td>
				<div class="form-group form-group-sm">
					<div class='col-xs-1'>
						<?php echo form_checkbox(array(
								'name'=>'single',
								'id'=>'single',
								'value'=>"Đơn tròng",
								'checked'=> $lens_type[0]? 1:0)
						);?>
					</div>
					<?php echo form_label($this->lang->line('test_single'), 'single', array('class'=>'control-label col-xs-8')); ?>
				</div>
			</td>
			<td>
				<div class="form-group form-group-sm">
					<div class='col-xs-1'>
						<?php echo form_checkbox(array(
								'name'=>'bifocal',
								'id'=>'bifocal',
								'value'=>"Hai tròng",
								'checked'=> $lens_type[1]? 1:0)
						);?>
					</div>
					<?php echo form_label($this->lang->line('test_bifocal'), 'bifocal', array('class'=>'control-label col-xs-8')); ?>
				</div>
			</td>
			<td>
				<div class="form-group form-group-sm">

					<div class='col-xs-1'>
						<?php echo form_checkbox(array(
								'name'=>'progressive',
								'id'=>'progressive',
								'value'=>"Đa tròng",
								'checked'=> $lens_type[2]? 1:0)
						);?>
					</div>
					<?php echo form_label($this->lang->line('test_progressive'), 'progressive', array('class'=>'control-label col-xs-8')); ?>
				</div>
			</td>
			<td>
				<div class="form-group form-group-sm">

					<div class='col-xs-1'>
						<?php echo form_checkbox(array(
								'name'=>'rx',
								'id'=>'rx',
								'value'=>"Đặt",
								'checked'=> $lens_type[3]? 1:0)
						);?>
					</div>
					<?php echo form_label($this->lang->line('test_rx'), 'rx', array('class'=>'control-label col-xs-8')); ?>
				</div>
			</td>
		</tr>
	</tbody>
</table>

<table class="sales_table_100" id="footer_precription">
	<thead>
	<tr>
		<th colspan="3">Đơn thuốc/Ghi chú</th>
	</tr>
	</thead>
	<tbody id="footer_precription_contents">
		<tr>
			<td colspan="3" >
				<?php
				if($note != ''){ echo nl2br($note);}
				else{
					echo "<br/>";
				}

				?>
			</td>
		</tr>
		<tr>

			<td colspan="3">
				<?php echo ($type==1)? "Đơn theo yêu cầu khách hàng":''; ?>

			</td>
		</tr>
	<tr>
		<td class="precription_note" colspan="3">
			<b>Chú ý: </br></b>
			<p>Khám lại sau <?php echo $duration; ?> tháng, khi đi nhớ mang theo đơn này.</p>
		</td>
	</tr>
		<tr><td colspan="3" style="text-align: right;">Hà Nội, ngày <?php echo date('d',$test_time); ?> tháng <?php echo date('m',$test_time); ?> năm <?php echo date('Y',$test_time); ?>.
				<br/><br/><br/></td></tr>
	<tr>
		<td style="text-align: center; vertical-align: bottom"><b>Bác sĩ</b></td>
		<td width="30%" style="text-align: center; vertical-align: bottom"><b>Y tá</b></td>
		<td width="30%" style="text-align: center">
			<b>Khúc xạ viên</b></td>
	</tr>
	</tbody>
</table>
<?php else: ?>
	<table class="sales_table_100" id="register">
		<thead>
		<tr>
			<th style="width: 15%;"><?php echo $this->lang->line('test_eyes'); ?></th>
			<th style="width: 10%;"><?php echo $this->lang->line('test_sph'); ?></th>
			<th style="width: 10%;"><?php echo $this->lang->line('test_cyl'); ?></th>
			<th style="width: 10%;"><?php echo $this->lang->line('test_ax'); ?></th>
			<th style="width: 10%;"><?php echo $this->lang->line('test_add'); ?></th>
			<th style="width: 10%;"><?php echo $this->lang->line('test_va'); ?></th>
			<th style="width: 10%;"><?php echo $this->lang->line('test_pd'); ?></th>
			<th style="width: 25%;"></th>
		</tr>
		</thead>
		<tbody id="cart_contents">
		<tr>
			<td> <?php //var_dump($lens_type); ?>
				<?php echo form_input(array(
						'name'=>'hidden_test_id',
						'class'=>'input-test',
						'type'=>'hidden',
						'value'=>$this->test_lib->get_test_id() ? $this->test_lib->get_test_id() : 0)
				);?>
				<?php echo form_input(array(
						'name'=>'hidden_test',
						'class'=>'input-test',
						'type'=>'hidden',
						'value'=>1)
				);?>
				<b><?php echo $this->lang->line('test_right_eye') ?></b>
			</td>
			<td>
				<?php echo form_input(array(
						'name'=>'r_sph',
						'class'=>'input-test',
						'value'=>'')
				);?>
			</td>
			<td>
				<?php echo form_input(array(
						'name'=>'r_cyl',
						'class'=>'input-test',
						'value'=>'')
				);?>
			</td>
			<td>
				<?php echo form_input(array(
						'name'=>'r_ax',
						'class'=>'input-test',
						'value'=>'')
				);?>
			</td>
			<td>
				<?php echo form_input(array(
						'name'=>'r_add',
						'class'=>'input-test',
						'value'=>'')
				);?>
			</td>
			<td>
				<?php echo form_input(array(
						'name'=>'r_va',
						'class'=>'input-test',
						'value'=>'')
				);?>
			</td>
			<td>
				<?php echo form_input(array(
						'name'=>'r_pd',
						'class'=>'input-test',
						'value'=>'')
				);?>
			</td>
			<td>
				<div class="form-group form-group-sm">

					<div class='col-xs-1'>
						<?php echo form_checkbox(array(
								'name'=>'distance',
								'id'=>'distance',
								'value'=>"Nhìn xa",
								'checked'=> 0)
						);?>
					</div>
					<?php echo form_label($this->lang->line('test_distance'), 'distance', array('class'=>'control-label col-xs-5')); ?>
				</div>
			</td>
		</tr>
		<tr>
			<td>
				<b><?php echo $this->lang->line('test_left_eye') ?></b>
			</td>
			<td>
				<?php echo form_input(array(
						'name'=>'l_sph',
						'class'=>'input-test',
						'value'=>'')
				);?>
			</td>
			<td>
				<?php echo form_input(array(
						'name'=>'l_cyl',
						'class'=>'input-test',
						'value'=>'')
				);?>
			</td>
			<td>
				<?php echo form_input(array(
						'name'=>'l_ax',
						'class'=>'input-test',
						'value'=>'')
				);?>
			</td>
			<td>
				<?php echo form_input(array(
						'name'=>'l_add',
						'class'=>'input-test',
						'value'=>'')
				);?>
			</td>
			<td>
				<?php echo form_input(array(
						'name'=>'l_va',
						'class'=>'input-test',
						'value'=>'')
				);?>
			</td>
			<td>
				<?php echo form_input(array(
						'name'=>'l_pd',
						'class'=>'input-test',
						'value'=>'')
				);?>
			</td>
			<td>
				<div class="form-group form-group-sm">

					<div class='col-xs-1'>
						<?php echo form_checkbox(array(
								'name'=>'reading',
								'id'=>'reading',
								'value'=>"Nhìn gần",
								'checked'=> 0)
						);?>
					</div>
					<?php echo form_label($this->lang->line('test_reading'), 'reading', array('class'=>'control-label col-xs-5')); ?>
				</div>
			</td>
		</tr>
		</tbody>
	</table>

	<table class="sales_table_100" id="register">
		<thead>
		<tr>
			<th colspan="4"><?php echo $this->lang->line('test_lens'); ?></th>
		</tr>
		</thead>
		<tbody id="cart_contents">
		<tr>
			<td>
				<div class="form-group form-group-sm">
					<div class='col-xs-1'>
						<?php echo form_checkbox(array(
								'name'=>'single',
								'id'=>'single',
								'value'=>"Đơn tròng",
								'checked'=> 0)
						);?>
					</div>
					<?php echo form_label($this->lang->line('test_single'), 'single', array('class'=>'control-label col-xs-6')); ?>
				</div>
			</td>
			<td>
				<div class="form-group form-group-sm">
					<div class='col-xs-1'>
						<?php echo form_checkbox(array(
								'name'=>'bifocal',
								'id'=>'bifocal',
								'value'=>"Hai tròng",
								'checked'=> 0)
						);?>
					</div>
					<?php echo form_label($this->lang->line('test_bifocal'), 'bifocal', array('class'=>'control-label col-xs-5')); ?>
				</div>
			</td>
			<td>
				<div class="form-group form-group-sm">

					<div class='col-xs-1'>
						<?php echo form_checkbox(array(
								'name'=>'progressive',
								'id'=>'progressive',
								'value'=>"Đa tròng",
								'checked'=> 0)
						);?>
					</div>
					<?php echo form_label($this->lang->line('test_progressive'), 'progressive', array('class'=>'control-label col-xs-5')); ?>
				</div>
			</td>
			<td>
				<div class="form-group form-group-sm">

					<div class='col-xs-1'>
						<?php echo form_checkbox(array(
								'name'=>'rx',
								'id'=>'rx',
								'value'=>"Đặt",
								'checked'=> 0)
						);?>
					</div>
					<?php echo form_label($this->lang->line('test_rx'), 'rx', array('class'=>'control-label col-xs-5')); ?>
				</div>
			</td>
		</tr>
		</tbody>
	</table>

	<table class="sales_table_100" id="register">
		<thead>
		<tr>
			<th colspan="2"><?php echo $this->lang->line('test_note'); ?></th>
		</tr>
		</thead>
		<tbody id="cart_contents">
		<tr>
			<td colspan="2">
				<?php echo form_textarea(array(
					'name' => 'note',
					'rows' => '3',
					'cols' => '25',
					'value'=> '',
					'class'=>'textarea_test'));?>
			</td>
		</tr>
		<tr>
			<td>
				<div class="form-group form-group-sm">
					<?php echo form_label($this->lang->line('test_duration'), 'duration', array('class'=>'control-label col-xs-5')); ?>
					<div class='col-xs-1'>
						<?php echo form_input(array(
								'name'=>'duration',
								'class'=>'input-test',
								'value'=>6)
						);?>
					</div>

				</div>
			</td>
			<td>
				<div class="form-group form-group-sm">
					<?php echo form_label($this->lang->line('test_type'), 'type', array('class'=>'control-label col-xs-5')); ?>
					<div class='col-xs-1'>
						<?php echo form_checkbox(array(
								'name'=>'type',
								'id'=>'type',
								'value'=>1,
								'checked'=>0)
						);?>
					</div>

				</div>

			</td>
		</tr>
		</tbody>
	</table>
	<?php endif; ?>
<?php echo form_close(); ?>