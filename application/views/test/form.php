<div id="required_fields_message"><?php //echo $this->lang->line('common_fields_required_message'); ?></div>

<ul id="error_message_box" class="error_message_box"></ul>

<?php echo form_open($controller_name."/complete", array('id'=>'done_test_form', 'class'=>'form-horizontal panel panel-default')); ?>
<?php if($this->test_lib->get_test_id()):?>
	
	<table class="sales_table_100 s-100" id="register">
		<thead>
		<tr>
			<th colspan="2" style="text-align:left">Thị lực không kính</th>
		</tr>
		</thead>
		<tbody id="cart_contents">
		<tr>
			<td> 				
				<b><?php echo $this->lang->line('test_right_eye') ?></b>
			</td>
			<td>
				<?php echo form_input(array(
						'name'=>'r_va_o',
						'class'=>'input-test',
						'style'=>'width: 155px;',
						'value'=>$r_va_o)
				);?>
			</td>
		</tr>
		<tr>
			<td>
				<b><?php echo $this->lang->line('test_left_eye') ?></b>
			</td>
			<td>
				<?php echo form_input(array(
						'name'=>'l_va_o',
						'class'=>'input-test',
						'style'=>'width: 155px;',
						'value'=>$l_va_o)
				);?>
			</td>
		</tr>
		</tbody>
	</table>
	<table class="sales_table_100 s-100" id="register">
		<thead>
		<tr>
			<th colspan="8" style="text-align:left">Kính cũ</th>
		</tr>
		<tr>
			<th style="width: 15%;"><?php echo $this->lang->line('test_eyes'); ?></th>
			<th style="width: 10%;"><?php echo $this->lang->line('test_sph'); ?></th>
			<th style="width: 10%;"><?php echo $this->lang->line('test_cyl'); ?></th>
			<th style="dth: 10%;"><?php echo $this->lang->line('test_ax'); ?></th>
			<th style="width: 10%;"><?php echo $this->lang->line('test_add'); ?></th>
			<th style="width: 10%;"><?php echo $this->lang->line('test_va'); ?></th>
			<th style="width: 10%;"><?php echo $this->lang->line('test_pd'); ?></th>
			<th style="width: 25%;"></th>
		</tr>
		</thead>
		<tbody id="cart_contents">
		<tr>
			<td>
				<b><?php echo $this->lang->line('test_right_eye') ?></b>
			</td>
			<td>
				<?php echo form_input(array(
						'name'=>'r_sph_old',
						'class'=>'input-test',
						'value'=>$right_e_old['SPH'])
				);?>
			</td>
			<td>
				<?php echo form_input(array(
						'name'=>'r_cyl_old',
						'class'=>'input-test',
						'value'=>$right_e_old['CYL'])
				);?>
			</td>
			<td>
				<?php echo form_input(array(
						'name'=>'r_ax_old',
						'class'=>'input-test',
						'value'=>$right_e_old['AX'])
				);?>
			</td>
			<td>
				<?php echo form_input(array(
						'name'=>'r_add_old',
						'class'=>'input-test',
						'value'=>$right_e_old['ADD'])
				);?>
			</td>
			<td>
				<?php echo form_input(array(
						'name'=>'r_va_old',
						'class'=>'input-test',
						'value'=>$right_e_old['VA'])
				);?>
			</td>
			<td>
				<?php echo form_input(array(
						'name'=>'r_pd_old',
						'class'=>'input-test',
						'value'=>$right_e_old['PD'])
				);?>
			</td>
			<td>
				<div class="checkbox">
					<label style="font-size: 1.3em">
						<?php echo form_checkbox(array(
								'name'=>'old_distance',
								'id'=>'old_distance',
								'value'=>"Nhìn xa",
								'checked'=> $old_toltal[0]? 1: 0)
						);?>
						<span class="cr"><i class="cr-icon fa fa-check"></i></span>
						<?php echo $this->lang->line('test_distance'); ?>
					</label>
				</div>
			</td>
		</tr>
		<tr>
			<td>
				<b><?php echo $this->lang->line('test_left_eye') ?></b>
			</td>
			<td>
				<?php echo form_input(array(
						'name'=>'l_sph_old',
						'class'=>'input-test',
						'value'=>$left_e_old['SPH'])
				);?>
			</td>
			<td>
				<?php echo form_input(array(
						'name'=>'l_cyl_old',
						'class'=>'input-test',
						'value'=>$left_e_old['CYL'])
				);?>
			</td>
			<td>
				<?php echo form_input(array(
						'name'=>'l_ax_old',
						'class'=>'input-test',
						'value'=>$left_e_old['AX'])
				);?>
			</td>
			<td>
				<?php echo form_input(array(
						'name'=>'l_add_old',
						'class'=>'input-test',
						'value'=>$left_e_old['ADD'])
				);?>
			</td>
			<td>
				<?php echo form_input(array(
						'name'=>'l_va_old',
						'class'=>'input-test',
						'value'=>$left_e_old['VA'])
				);?>
			</td>
			<td>
				<?php echo form_input(array(
						'name'=>'l_pd_old',
						'class'=>'input-test',
						'value'=>$left_e_old['PD'])
				);?>
			</td>
			<td>
				<div class="checkbox">
					<label style="font-size: 1.3em">
						<?php echo form_checkbox(array(
								'name'=>'reading_old',
								'id'=>'reading_old',
								'value'=>"Nhìn gần",
								'checked'=> $old_toltal[1]? 1: 0)
						);?>
						<span class="cr"><i class="cr-icon fa fa-check"></i></span>
						<?php echo $this->lang->line('test_reading'); ?>
					</label>
				</div>
			</td>
		</tr>
		</tbody>
	</table>

	<table class="sales_table_100 s-100" id="register">
		<thead>
		<tr>
			<th colspan="8" style="text-align:left">Kính đề nghị</th>
		</tr>
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
							'value'=>$right_e['SPH'])
					);?>
				</td>
				<td>
					<?php echo form_input(array(
							'name'=>'r_cyl',
							'class'=>'input-test',
							'value'=>$right_e['CYL'])
					);?>
				</td>
				<td>
					<?php echo form_input(array(
							'name'=>'r_ax',
							'class'=>'input-test',
							'value'=>$right_e['AX'])
					);?>
				</td>
				<td>
					<?php echo form_input(array(
							'name'=>'r_add',
							'class'=>'input-test',
							'value'=>$right_e['ADD'])
					);?>
				</td>
				<td>
					<?php echo form_input(array(
							'name'=>'r_va',
							'class'=>'input-test',
							'value'=>$right_e['VA'])
					);?>
				</td>
				<td>
					<?php echo form_input(array(
							'name'=>'r_pd',
							'class'=>'input-test',
							'value'=>$right_e['PD'])
					);?>
				</td>
				<td>
					<div class="checkbox">
						<label style="font-size: 1.3em">
							<?php echo form_checkbox(array(
									'name'=>'distance',
									'id'=>'distance',
									'value'=>"Nhìn xa",
									'checked'=> $toltal[0]? 1: 0)
							);?>
							<span class="cr"><i class="cr-icon fa fa-check"></i></span>
							<?php echo $this->lang->line('test_distance'); ?>
						</label>
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
							'value'=>$left_e['SPH'])
					);?>
				</td>
				<td>
					<?php echo form_input(array(
							'name'=>'l_cyl',
							'class'=>'input-test',
							'value'=>$left_e['CYL'])
					);?>
				</td>
				<td>
					<?php echo form_input(array(
							'name'=>'l_ax',
							'class'=>'input-test',
							'value'=>$left_e['AX'])
					);?>
				</td>
				<td>
					<?php echo form_input(array(
							'name'=>'l_add',
							'class'=>'input-test',
							'value'=>$left_e['ADD'])
					);?>
				</td>
				<td>
					<?php echo form_input(array(
							'name'=>'l_va',
							'class'=>'input-test',
							'value'=>$left_e['VA'])
					);?>
				</td>
				<td>
					<?php echo form_input(array(
							'name'=>'l_pd',
							'class'=>'input-test',
							'value'=>$left_e['PD'])
					);?>
				</td>
				<td>
					<div class="checkbox">
						<label style="font-size: 1.3em">
							<?php echo form_checkbox(array(
									'name'=>'reading',
									'id'=>'reading',
									'value'=>"Nhìn gần",
									'checked'=> $toltal[1]?1:0)
							);?>
							<span class="cr"><i class="cr-icon fa fa-check"></i></span>
							<?php echo $this->lang->line('test_reading'); ?>
						</label>
					</div>
				</td>
			</tr>
		</tbody>
	</table>

	<table class="sales_table_100 s-100" id="register">
		<thead>
		<tr>
			<th colspan="4"><?php echo $this->lang->line('test_lens'); ?></th>
		</tr>
		</thead>
		<tbody id="cart_contents">
			<tr>
				<td>
					<div class="checkbox">
						<label style="font-size: 1.3em">
							<?php echo form_checkbox(array(
									'name'=>'single',
									'id'=>'single',
									'value'=>"Đơn tròng",
									'checked'=> $lens_type[0]? 1:0)
							);?>
							<span class="cr"><i class="cr-icon fa fa-check"></i></span>
							<?php echo $this->lang->line('test_single'); ?>
						</label>
					</div>

				</td>
				<td>
					<div class="checkbox">
						<label style="font-size: 1.3em">
							<?php echo form_checkbox(array(
									'name'=>'bifocal',
									'id'=>'bifocal',
									'value'=>"Hai tròng",
									'checked'=> $lens_type[1]? 1:0)
							);?>
							<span class="cr"><i class="cr-icon fa fa-check"></i></span>
							<?php echo $this->lang->line('test_bifocal'); ?>
						</label>
					</div>
				</td>
				<td>
					<div class="checkbox">
						<label style="font-size: 1.3em">
							<?php echo form_checkbox(array(
									'name'=>'progressive',
									'id'=>'progressive',
									'value'=>"Đa tròng",
									'checked'=> $lens_type[2]? 1:0)
							);?>
							<span class="cr"><i class="cr-icon fa fa-check"></i></span>
							<?php echo $this->lang->line('test_progressive'); ?>
						</label>
					</div>
				</td>
				<td>
					<div class="checkbox">
						<label style="font-size: 1.3em">
							<?php echo form_checkbox(array(
									'name'=>'rx',
									'id'=>'rx',
									'value'=>"Đặt",
									'checked'=> $lens_type[3]? 1:0)
							);?>
							<span class="cr"><i class="cr-icon fa fa-check"></i></span>
							<?php echo $this->lang->line('test_orthk'); ?>
						</label>
					</div>
				</td>
			</tr>
		</tbody>
	</table>

	<table class="sales_table_100 s-100" id="register">
		<thead>
		<tr>
			<th colspan="3"><?php echo $this->lang->line('test_note'); ?></th>
		</tr>
		</thead>
		<tbody id="cart_contents">
			<tr>
				<td colspan="3">
					<?php echo form_textarea(array(
						'name' => 'note',
						'rows' => '3',
						'cols' => '25',
						'value'=> $note,
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
									'value'=>$duration ? $duration : 6)
							);?>
						</div>

					</div>
				</td>
				<td>
					<div class="checkbox">
						<label style="font-size: 1.3em">
							<?php echo form_checkbox(array(
									'name'=>'reminder',
									'id'=>'type',
									'value'=>1,
									'checked'=>$reminder)
							);?>
							<span class="cr"><i class="cr-icon fa fa-check"></i></span>
							<?php echo 'Nhắc tái khám'; ?>
						</label>
					</div>
				</td>
				<td>
					<div class="checkbox">
						<label style="font-size: 1.3em">
							<?php echo form_checkbox(array(
									'name'=>'type',
									'id'=>'type',
									'value'=>1,
									'checked'=>$type ? 1 :0)
							);?>
							<span class="cr"><i class="cr-icon fa fa-check"></i></span>
							<?php echo $this->lang->line('test_type'); ?>
						</label>
					</div>

				</td>
			</tr>
		</tbody>
	</table>

	<!-- Đơn Thuốc -->
	<table class="sales_table_100 s-100" id="prescription">
		<thead>
		<tr style="text-align:center">
			<th colspan="5" style="text-align:center"><span class="prescription_title">Đơn Thuốc</span></th>
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
		<?php $pres_max = count($pres_names) < 6? count($pres_names) : 6; 
			  $cr_thuoc = count($prescription_list); //var_dump($prescription_list);
		 if($cr_thuoc > 0):	  
		 for($i=1; $i <= $cr_thuoc; $i++): ?>
		<tr id="row_<?=$i?>" class="pres_row">
			<td class="pres_number"><?=$i?></td>
			<td>
				<input type="hidden" name="pres_name[]" value="<?=$prescription_list[$i-1]['name']?>" /><?=$prescription_list[$i-1]['name']?>
			</td>
			<td>
				<input type="hidden" name="pres_dvt[]" value="<?=$prescription_list[$i-1]['dvt']?>" /><?=$prescription_list[$i-1]['dvt']?>
				
			</td>
			<td>
			<?php echo form_input(array(
							'name'=>'pres_amount[]',
							'class'=>'input-test pres_sl',
							'value'=>$prescription_list[$i-1]['sl'])
					);?>
			</td>
			<td>
			<input type="text" name="pres_hdsd[]" value="<?=$prescription_list[$i-1]['hdsd']?>" />
			
			</td>
		</tr>
		<?php endfor; endif;?>
		</tbody>
	</table>
	<div id="search-container" class="s-100">
			<input type="text" id="search-input" placeholder="Tìm kiếm thuốc">
			<ul id="search-results"></ul>
	</div>
	<!-- End Đơn Thuốc -->
	<?php $today = strtotime(date('Y-m-d',time()));
	      $next_day = $today + 24*60*60;//echo $test_time; ?>
	<?php if($today > $test_time )
	{

	}else{ ?>
	<div class='btn btn-sm btn-success pull-right' id='update_test_button' ><span class="glyphicon glyphicon-ok">&nbsp</span><?php echo $this->lang->line('test_complete_test'); ?></div>
	<?php }
	?>
	<div class='btn btn-sm btn-success pull-right' id='clear_test_button' ><span class="glyphicon glyphicon-ok">&nbsp</span><?php echo $this->lang->line('test_clear_test'); ?></div>

<?php else: ?>
	<!-- TL Không kính -->
	<table class="sales_table_100 s-100" id="register">
		<thead>
		<tr>
			<th colspan="2" style="width: 15%;">Thị lực không kính</th>
		</tr>
		</thead>
		<tbody id="cart_contents">
		<tr>
			<td style="width: 15%;"> 				
				<b><?php echo $this->lang->line('test_right_eye') ?></b>
			</td>
			<td>
				<?php echo form_input(array(
						'name'=>'r_va_o',
						'class'=>'input-test',
						'style'=>'width: 155px;',
						'value'=>'')
				);?>
			</td>
		</tr>
		<tr>
			<td>
				<b><?php echo $this->lang->line('test_left_eye') ?></b>
			</td>
			<td>
				<?php echo form_input(array(
						'name'=>'l_va_o',
						'class'=>'input-test',
						'style'=>'width: 155px;',
						'value'=>'')
				);?>
			</td>
		</tr>
		</tbody>
	</table>
	<!-- Kính cũ -->
	<table class="sales_table_100 s-100" id="register">
		<thead>
		<tr>
			<th colspan="8" style="width: 15%;">Kính cũ</th>
		</tr>
		<tr>
			<th style="width: 15%;"><?php echo $this->lang->line('test_eyes'); ?></th>
			<th style="width: 10%;"><?php echo $this->lang->line('test_sph'); ?></th>
			<th style="width: 10%;"><?php echo $this->lang->line('test_cyl'); ?></th>
			<th style="dth: 10%;"><?php echo $this->lang->line('test_ax'); ?></th>
			<th style="width: 10%;"><?php echo $this->lang->line('test_add'); ?></th>
			<th style="width: 10%;"><?php echo $this->lang->line('test_va'); ?></th>
			<th style="width: 10%;"><?php echo $this->lang->line('test_pd'); ?></th>
			<th style="width: 25%;"></th>
		</tr>
		</thead>
		<tbody id="cart_contents">
		<tr>
			<td>
				<b><?php echo $this->lang->line('test_right_eye') ?></b>
			</td>
			<td>
				<?php echo form_input(array(
						'name'=>'r_sph_old',
						'class'=>'input-test',
						'value'=>'')
				);?>
			</td>
			<td>
				<?php echo form_input(array(
						'name'=>'r_cyl_old',
						'class'=>'input-test',
						'value'=>'')
				);?>
			</td>
			<td>
				<?php echo form_input(array(
						'name'=>'r_ax_old',
						'class'=>'input-test',
						'value'=>'')
				);?>
			</td>
			<td>
				<?php echo form_input(array(
						'name'=>'r_add_old',
						'class'=>'input-test',
						'value'=>'')
				);?>
			</td>
			<td>
				<?php echo form_input(array(
						'name'=>'r_va_old',
						'class'=>'input-test',
						'value'=>'')
				);?>
			</td>
			<td>
				<?php echo form_input(array(
						'name'=>'r_pd_old',
						'class'=>'input-test',
						'value'=>'')
				);?>
			</td>
			<td>
				<div class="checkbox">
					<label style="font-size: 1.3em">
						<?php echo form_checkbox(array(
								'name'=>'distance_old',
								'id'=>'distance_old',
								'value'=>"Nhìn xa",
								'checked'=> 0)
						);?>
						<span class="cr"><i class="cr-icon fa fa-check"></i></span>
						<?php echo $this->lang->line('test_distance'); ?>
					</label>
				</div>
			</td>
		</tr>
		<tr>
			<td>
				<b><?php echo $this->lang->line('test_left_eye') ?></b>
			</td>
			<td>
				<?php echo form_input(array(
						'name'=>'l_sph_old',
						'class'=>'input-test',
						'value'=>'')
				);?>
			</td>
			<td>
				<?php echo form_input(array(
						'name'=>'l_cyl_old',
						'class'=>'input-test',
						'value'=>'')
				);?>
			</td>
			<td>
				<?php echo form_input(array(
						'name'=>'l_ax_old',
						'class'=>'input-test',
						'value'=>'')
				);?>
			</td>
			<td>
				<?php echo form_input(array(
						'name'=>'l_add_old',
						'class'=>'input-test',
						'value'=>'')
				);?>
			</td>
			<td>
				<?php echo form_input(array(
						'name'=>'l_va_old',
						'class'=>'input-test',
						'value'=>'')
				);?>
			</td>
			<td>
				<?php echo form_input(array(
						'name'=>'l_pd_old',
						'class'=>'input-test',
						'value'=>'')
				);?>
			</td>
			<td>
				<div class="checkbox">
					<label style="font-size: 1.3em">
						<?php echo form_checkbox(array(
								'name'=>'reading_old',
								'id'=>'reading_old',
								'value'=>"Nhìn gần",
								'checked'=> 0)
						);?>
						<span class="cr"><i class="cr-icon fa fa-check"></i></span>
						<?php echo $this->lang->line('test_reading'); ?>
					</label>
				</div>
			</td>
		</tr>
		</tbody>
	</table>
	<!-- Kính đề  nghị-->					
	<table class="sales_table_100 s-100" id="register">
		<thead>
		<tr>
			<th colspan="8" style="width: 15%;">Kính đề nghị</th>
		</tr>
		<tr>
			<th style="width: 15%;"><?php echo $this->lang->line('test_eyes'); ?></th>
			<th style="width: 10%;"><?php echo $this->lang->line('test_sph'); ?></th>
			<th style="width: 10%;"><?php echo $this->lang->line('test_cyl'); ?></th>
			<th style="dth: 10%;"><?php echo $this->lang->line('test_ax'); ?></th>
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
				<div class="checkbox">
					<label style="font-size: 1.3em">
						<?php echo form_checkbox(array(
								'name'=>'distance',
								'id'=>'distance',
								'value'=>"Nhìn xa",
								'checked'=> 0)
						);?>
						<span class="cr"><i class="cr-icon fa fa-check"></i></span>
						<?php echo $this->lang->line('test_distance'); ?>
					</label>
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
				<div class="checkbox">
					<label style="font-size: 1.3em">
						<?php echo form_checkbox(array(
								'name'=>'reading',
								'id'=>'reading',
								'value'=>"Nhìn gần",
								'checked'=> 0)
						);?>
						<span class="cr"><i class="cr-icon fa fa-check"></i></span>
						<?php echo $this->lang->line('test_reading'); ?>
					</label>
				</div>
			</td>
		</tr>
		</tbody>
	</table>
	<!-- Phân Loại -->
	<table class="sales_table_100 s-100" id="register">
		<thead>
		<tr>
			<th colspan="4"><?php echo $this->lang->line('test_lens'); ?></th>
		</tr>
		</thead>
		<tbody id="cart_contents">
		<tr>
			<td>
				<div class="checkbox">
					<label style="font-size: 1.3em">
						<?php echo form_checkbox(array(
								'name'=>'single',
								'id'=>'single',
								'value'=>"Đơn tròng",
								'checked'=> 0)
						);?>
						<span class="cr"><i class="cr-icon fa fa-check"></i></span>
						<?php echo $this->lang->line('test_single'); ?>
					</label>
				</div>
			</td>
			<td>
				<div class="checkbox">
					<label style="font-size: 1.3em">
						<?php echo form_checkbox(array(
								'name'=>'bifocal',
								'id'=>'bifocal',
								'value'=>"Hai tròng",
								'checked'=> 0)
						);?>
						<span class="cr"><i class="cr-icon fa fa-check"></i></span>
						<?php echo $this->lang->line('test_bifocal'); ?>
					</label>
				</div>
			</td>
			<td>
				<div class="checkbox">
					<label style="font-size: 1.3em">
						<?php echo form_checkbox(array(
								'name'=>'progressive',
								'id'=>'progressive',
								'value'=>"Đa tròng",
								'checked'=> 0)
						);?>
						<span class="cr"><i class="cr-icon fa fa-check"></i></span>
						<?php echo $this->lang->line('test_progressive'); ?>
					</label>
				</div>
			</td>
			<td>
				<div class="checkbox">
					<label style="font-size: 1.3em">
						<?php echo form_checkbox(array(
								'name'=>'rx',
								'id'=>'rx',
								'value'=>"Đặt",
								'checked'=> 0)
						);?>
						<span class="cr"><i class="cr-icon fa fa-check"></i></span>
						<?php echo $this->lang->line('test_orthk'); ?>
					</label>
				</div>
			</td>
		</tr>
		</tbody>
	</table>
	<!-- Thông tin khác-->
	<table class="sales_table_100 s-100" id="register">
		<thead>
		<tr>
			<th colspan="3"><?php echo $this->lang->line('test_note'); ?></th>
		</tr>
		</thead>
		<tbody id="cart_contents">
		<tr>
			<td colspan="3">
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
				<div class="checkbox">
					<label style="font-size: 1.3em">
						<?php echo form_checkbox(array(
								'name'=>'reminder',
								'id'=>'type',
								'value'=>1,
								'checked'=>0)
						);?>
						<span class="cr"><i class="cr-icon fa fa-check"></i></span>
						<?php echo 'Nhắc tái khám'; ?>
					</label>
				</div>
			</td>
			<td>
				<div class="checkbox">
					<label style="font-size: 1.3em">
						<?php echo form_checkbox(array(
								'name'=>'type',
								'id'=>'type',
								'value'=>1,
								'checked'=>0)
						);?>
						<span class="cr"><i class="cr-icon fa fa-check"></i></span>
						<?php echo $this->lang->line('test_type'); ?>
					</label>
				</div>
			</td>
		</tr>
		</tbody>
	</table>
	<!-- Button -->
	<?php if($this->Employee->has_grant('test_step_one')): ?>
		<!-- Phiếu khám -->
		<table class="sales_table_100 s-200" id="register">
			<thead>
			<tr>
				<th colspan="3"><?php echo $this->lang->line('test_reason'); ?></th>
			</tr>
			</thead>
			<tbody id="cart_contents">
			<tr>
				<td colspan="3">
					<?php echo form_textarea(array(
						'name' => 'reason',
						'rows' => '3',
						'cols' => '25',
						'value'=> '',
						'class'=>'textarea_test'));?>
				</td>
			</tr>
			</tbody>
		</table>
		<div class='btn btn-sm btn-success pull-right' id='update_test_button' ><span class="glyphicon glyphicon-ok">&nbsp</span>Tạo phiếu khám</div>
	<?php else: ?>
		<div class='btn btn-sm btn-success pull-right' id='update_test_button' ><span class="glyphicon glyphicon-ok">&nbsp</span><?php echo $this->lang->line('test_complete_test'); ?></div>
	<?php endif; ?>
	<!-- Đơn Thuốc -->
	<table class="sales_table_100 s-100" id="prescription">		
		<thead>
		<tr style="text-align:center">
			<th colspan="6" style="text-align:center"><span class="prescription_title">Đơn Thuốc</span></th>
		</tr>
		<tr class="prescription_head" style="text-align:center">
			<th style="text-align:center" width="5%"><span >STT</span></th>
			<th style="text-align:center"><span >Tên thuốc</span></th>
			<th style="text-align:center"><span >Đơn vị</span></th>
			<th style="text-align:center"><span >SL</span></th>
			<th style="text-align:center"><span >Chỉ dẫn dùng</span></th>
			<th style="padding: 5px;"></th>
		</tr>
		</thead>
		<tbody id="cart_contents2">
		</tbody>
	</table>
	<div id="search-container" class="s-100">
			<input type="text" id="search-input" placeholder="Tìm kiếm thuốc">
			<ul id="search-results"></ul>
	</div>
	<!-- End Đơn Thuốc -->
<?php endif; ?>


<?php echo form_close(); ?>