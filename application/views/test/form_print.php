<div id="required_fields_message"><?php //echo $this->lang->line('common_fields_required_message'); ?></div>

<ul id="error_message_box" class="error_message_box"></ul>
<?php if($this->config->item('hien_thi_kinh_cu')){
		$_sFont_size = 'font-size: 12px;';
	} else {
		$_sFont_size = '';
	}
?>
<?php echo form_open($controller_name."/print", array('id'=>'print_test_form', 'class'=>'form-horizontal panel panel-default')); ?>
<?php if($this->test_lib->get_test_id()):?>
	<table class="precription_header_print">
	<?php if($this->config->item('test_header') == 1): ?>
	<thead>
		<tr>
			<th colspan="2">
				<?=$this->config->item('ten_phong_kham')?>
			</th>
		</tr>
		<tr>
			<th colspan="2">
				Địa chỉ: <?=$this->config->item('address')?>
			</th>
		</tr>
		<tr>
			<th colspan="2">
				Điện thoại: <?=$this->config->item('lien_he')?>
			</th>
		</tr>
	</thead>
	<?php endif; ?>
	<tbody>
		<tr>
			<td colspan="2" class="print_title">KẾT QUẢ KHÁM</td>
		</tr>
		<tr>
			<td width="60%">
				<table>
					<tr>
						<td>Họ tên: <?php echo 	$customer;?></td>
					</tr>
					<tr>
						<td>Địa chỉ: <?php echo $customer_address; ?></td>
					</tr>
					<tr>
						<td>Năm sinh: <?php echo $age; ?></td>
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
	<?php if($this->config->item('hien_thi_VA')): ?>
		<?php if( (trim($r_va_o) != '') || (trim($l_va_o) != '') || (trim($r_va_lo) != '') || (trim($l_va_lo) != '')):  ?>
			<table class="sales_table_100" id="print_data" style="margin-bottom: 5px; <?=$_sFont_size?>">
				<thead>
					<tr>
						<th colspan="3" style="text-align: center; border: 0px; background-color: white !important;">
							THỊ LỰC
						</th>
					</tr>
					<tr>
						<th style="width: 33%;"></th>
						<th style="width: 33%;">Mắt phải (R)</th>
						<th style="width: 34%;">Mắt trái (L)</th>
					</tr>	
				</thead>
				<tbody>
					<tr>
						<td style="text-align: left;"><b>Thị lực không kính</b></td>
						<td><?=$r_va_o?></td>
						<td><?=$l_va_o?></td>
					</tr>
					<tr>
						<td style="text-align: left;"><b>Thị lực kính lỗ</b></td>
						<td><?=$r_va_lo?></td>
						<td><?=$l_va_lo?></td>
					</tr>
				</tbody>
			</table>
		<?php endif; ?>
	<?php endif; ?>
	<?php if($this->config->item('hien_thi_kinh_cu')):?>
		<?php if( (trim($right_e_old['SPH']) != '') 
					|| (trim($right_e_old['CYL']) != '')
					|| (trim($right_e_old['AX']) != '') 
					|| (trim($right_e_old['ADD']) != '')
					|| (trim($right_e_old['VA']) != '') 
					|| (trim($right_e_old['PD']) != '')
					|| (trim($left_e_old['SPH']) != '') || (trim($left_e_old['CYL']) != '') 
					|| (trim($left_e_old['AX']) != '') || (trim($left_e_old['ADD']) != '')
					|| (trim($left_e_old['VA']) != '') || (trim($left_e_old['PD']) != '')):  ?>
			<table class="sales_table_100" id="print_data" style="<?=$_sFont_size?>">
			<thead>
				<tr>
					<th colspan="8" style="text-align: center; border: 0px; background-color: white !important;">
						KÍNH CŨ
					</th>
				</tr>
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
					<td style="text-align: left;">
						<b>Phải (<?php echo $this->lang->line('test_right_eye') ?>)</b>
					</td>
					<td>
						<?php echo ($right_e_old['SPH']==0)? "PLANO": $right_e_old['SPH'];?>
					</td>
					<td>
						<?php echo $right_e_old['CYL'];?>
					</td>
					<td>
						<?php echo $right_e_old['AX'];?>
					</td>
					<td>
						<?php echo $right_e_old['ADD'];?>
					</td>
					<td>
						<?php echo $right_e_old['VA'];?>
					</td>
					<td>
						<?php echo $right_e_old['PD'];?>
					</td>
					<td>
						<div class="form-group form-group-sm">

							<div class='col-xs-1'>
								<?php echo form_checkbox(array(
										'name'=>'distance',
										'id'=>'distance',
										'value'=>"Nhìn xa",
										'checked'=> $old_toltal[0]? 1: 0)
								);?>
							</div>
							<?php echo form_label($this->lang->line('test_distance'), 'distance', array('class'=>'control-label col-xs-9')); ?>
						</div>
					</td>
				</tr>
				<tr>
					<td style="text-align: left;">
						<b>Trái (<?php echo $this->lang->line('test_left_eye') ?>)</b>
					</td>
					<td>
						<?php echo ($left_e_old['SPH']==0)?'PLANO': $left_e_old['SPH'];?>
					</td>
					<td>
						<?php echo $left_e_old['CYL'];?>
					</td>
					<td>
						<?php echo $left_e_old['AX'];?>
					</td>
					<td>
						<?php echo $left_e_old['ADD'];?>
					</td>
					<td>
						<?php echo $left_e_old['VA'];?>
					</td>
					<td>
						<?php echo $left_e_old['PD'];?>
					</td>
					<td>
						<div class="form-group form-group-sm">

							<div class='col-xs-1'>
								<?php echo form_checkbox(array(
										'name'=>'reading',
										'id'=>'reading',
										'value'=>"Nhìn gần",
										'checked'=> $old_toltal[1]?1:0)
								);?>
							</div>
							<?php echo form_label($this->lang->line('test_reading'), 'reading', array('class'=>'control-label col-xs-9')); ?>
						</div>
					</td>
				</tr>
			</tbody>
			</table>
		<?php endif; ?>
	<?php endif; ?>

	<?php if( (trim($right_e_old['SPH']) != '') 
					|| (trim($right_e['CYL']) != '')
					|| (trim($right_e['AX']) != '') 
					|| (trim($right_e['ADD']) != '')
					|| (trim($right_e['VA']) != '') 
					|| (trim($right_e['PD']) != '')
					|| (trim($left_e['SPH']) != '') || (trim($left_e['CYL']) != '') 
					|| (trim($left_e['AX']) != '') || (trim($left_e['ADD']) != '')
					|| (trim($left_e['VA']) != '') || (trim($left_e['PD']) != '')):  ?>
		<table class="sales_table_100" id="print_data" style="<?=$_sFont_size?>">
		<thead>
			<tr>
				<th colspan="8" style="text-align: center; border: 0px; background-color: white !important;">
					ĐƠN KÍNH
				</th>
			</tr>
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
				<td style="text-align: left;">
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
				<td style="text-align: left;">
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
	<?php endif; ?>
	<?php if($this->config->item('loai_mat_kinh') ==1): ?>
		<table class="sales_table_100" id="body_precription" style="<?=$_sFont_size?>">
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
	<?php endif;?>
	<table class="sales_table_100" id="footer_precription" style="<?=$_sFont_size?>">
		<thead>
		<tr>
			<th colspan="3"><?php if($note != ''){ echo mb_strtoupper($this->lang->line('test_note')); }?></th>
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
				<?php $pres_max = count($pres_names) < 6? count($pres_names) : 6; 
						$cr_thuoc = count($prescription_list); //var_dump($prescription_list);
					if($cr_thuoc > 0): ?>
				<table class="sales_table_100 s-100" id="prescription-print">
					<thead>
						<tr>
							<th colspan="5" style="border: 0px; background-color: white !important;">
								ĐƠN THUỐC
							</th>
						</tr>
					<tr class="prescription_head" style="text-align:center">
						<th style="text-align:center" width="5%"><span >STT</span></th>
						<th style="text-align:center"><span >Tên thuốc</span></th>
						<th style="text-align:center"><span >Đơn vị</span></th>
						<th style="text-align:center"><span >SL</span></th>
						<th style="text-align:center"><span >Chỉ dẫn dùng</span></th>
					</tr>
					</thead>
					<tbody id="cart_contents2">
					
					<?php 	
					for($i=1; $i <= $cr_thuoc; $i++): ?>
					<tr id="row_<?=$i?>" class="pres_row">
						<td class="pres_number"><?=$i?></td>
						<td>
							<?=$prescription_list[$i-1]['name']?>
						</td>
						<td>
							<?=$prescription_list[$i-1]['dvt']?>
							
						</td>
						<td>
							<?=$prescription_list[$i-1]['sl']?>
						</td>
						<td>
							<?=$prescription_list[$i-1]['hdsd']?>
						</td>
					</tr>
					<?php endfor; ?>
					</tbody>
				</table>
				<?php endif;?>
				</td>
			</tr>
			<tr>

				<td colspan="3">
					<?php echo ($type==1)? "Đơn theo yêu cầu khách hàng":''; ?>

				</td>
			</tr>
		<tr>
			<td class="precription_note" colspan="3">
				<b>Chú ý: </b>Khám lại sau <?php echo $duration; ?> <?=$duration_dvt?>, khi đi nhớ mang theo đơn này.
			</td>
		</tr>
			<tr><td colspan="3" style="text-align: right;"><?=$this->config->item('default_city')?>, ngày <?php echo date('d',$test_time); ?> tháng <?php echo date('m',$test_time); ?> năm <?php echo date('Y',$test_time); ?>.
					</td></tr>
		<tr>
			<td style="text-align: center; vertical-align: bottom">
				<?php if($this->config->item('test_display_kxv')==1): ?><b>Khúc xạ viên</b><?php endif; ?>
			</td>
			<td width="30%" style="text-align: center; vertical-align: bottom">
				<b><?php if($this->config->item('test_display_nurse')==1): ?>Y tá<?php endif; ?></b></td>
			<td width="50%" style="text-align: center">
				<b>Bác sĩ</b></br><br/><br/><br/>
				<?php if($this->config->item('hien_thi_ten_bac_si')==1): ?><?=$this->config->item('ten_bac_si')?><?php endif; ?>
			</td>
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