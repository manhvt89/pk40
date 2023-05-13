<div id="required_fields_message"><?php //echo $this->lang->line('common_fields_required_message'); ?></div>

<ul id="error_message_box" class="error_message_box"></ul>

<?php echo form_open($controller_name."/complete", array('id'=>'done_test_form', 'class'=>'form-horizontal panel panel-default')); ?>
<?php if($this->test_lib->get_test_id()):?>
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

<table class="sales_table_100" id="register">
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
						<?php echo $this->lang->line('test_rx'); ?>
					</label>
				</div>
			</td>
		</tr>
	</tbody>
</table>

<table class="sales_table_100" id="register">
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
					'rows' => '7',
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

	<table class="sales_table_100" id="register">
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
						<?php echo $this->lang->line('test_rx'); ?>
					</label>
				</div>
			</td>
		</tr>
		</tbody>
	</table>

	<table class="sales_table_100" id="register">
		<thead>
		<tr>
			<th colspan="3">Đơn thuốc/Ghi chú</th>
		</tr>
		</thead>
		<tbody id="cart_contents">
		<tr>
			<td colspan="3">
				<?php echo form_textarea(array(
					'wrap'=>'hard',
					'name' => 'note',
					'rows' => '5',
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
	<div class='btn btn-sm btn-success pull-right' id='update_test_button' ><span class="glyphicon glyphicon-ok">&nbsp</span><?php echo $this->lang->line('test_complete_test'); ?></div>
<?php endif; ?>


<?php echo form_close(); ?>