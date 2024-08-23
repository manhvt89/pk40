<div id="required_fields_message"><?php echo $this->lang->line('common_fields_required_message'); ?></div>

<ul id="error_message_box" class="error_message_box"></ul>


<?php echo form_open('oincs/save/'.'-1', array('id'=>'oinc_form', 'enctype'=>'multipart/form-data', 'class'=>'form-horizontal')); ?>
	<fieldset id="oinc_basic_info">
		
		<div class="form-group form-group-sm">
			
			<?php echo form_label($this->lang->line('oincs_mode'), 'mode', array('class'=>'control-label col-xs-3')); ?>
			<div>
				<?php 
					echo form_radio('mode', 'A', TRUE, 'id="mode_auto"');
					echo form_label($this->lang->line('oincs_mode_auto'), 'mode_auto');

					echo form_radio('mode', 'M', FALSE, 'id="mode_manual"');
					echo form_label($this->lang->line('oincs_mode_manual'), 'mode_manual');
				?>
			</div>
		</div>
		<div class="form-group form-group-sm">
			<?php echo form_label($this->lang->line('oincs_lens'), 'is_lens', array('class'=>'control-label col-xs-3')); ?>
			<div class='col-xs-1'>
				<?php echo form_checkbox(array(
						'name'=>'items[]',
						'id'=>'is_lens',
						'value'=>'lens',
						'checked'=>0)
						);?>
			</div>
		</div>

		<div class="form-group form-group-sm">
			<?php echo form_label($this->lang->line('oincs_frame'), 'is_frame', array('class'=>'control-label col-xs-3')); ?>
			<div class='col-xs-1'>
				<?php echo form_checkbox(array(
						'name'=>'items[]',
						'id'=>'is_frame',
						'value'=>'frame',
						'checked'=>0)
						);?>
			</div>
		</div>

		<div class="form-group form-group-sm">
			<?php echo form_label($this->lang->line('oincs_sun_glasses'), 'is_sun_glasses', array('class'=>'control-label col-xs-3')); ?>
			<div class='col-xs-1'>
				<?php echo form_checkbox(array(
						'name'=>'items[]',
						'id'=>'is_sun_glasses',
						'value'=>'sun_glasses',
						'checked'=>0)
						);?>
			</div>
		</div>

		
		<div class="form-group form-group-sm">
			<?php echo form_label($this->lang->line('oincs_thuoc'), 'is_thuoc', array('class'=>'control-label col-xs-3')); ?>
			<div class='col-xs-1'>
				<?php echo form_checkbox(array(
						'name'=>'items[]',
						'id'=>'is_thuoc',
						'value'=>'medicine',
						'checked'=>0)
						);?>
			</div>
		</div>

		<div class="form-group form-group-sm">
			<?php echo form_label($this->lang->line('oincs_contact_lens'), 'is_contact_lens', array('class'=>'control-label col-xs-3')); ?>
			<div class='col-xs-1'>
				<?php echo form_checkbox(array(
						'name'=>'items[]',
						'id'=>'is_contact_lens',
						'value'=>'contact_lens',
						'checked'=>0)
						);?>
			</div>
		</div>

	</fieldset>
<?php echo form_close(); ?>

<script type="text/javascript">
	$(document).ready(function() {
		$('#oinc_form').submit(function(event) {
			// Kiểm tra xem có ít nhất một checkbox được chọn
			if ($('input[name="items[]"]:checked').length == 0) {
				$('#error_message_box').html('<li>Vui lòng chọn ít nhất một hạng mục muốn kiểm kê.</li>');
				event.preventDefault(); // Ngăn form không submit
			}
		});
	});
</script>

