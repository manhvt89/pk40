<style>
        /* CSS cho căn chỉnh checkbox và label */
        @media (min-width: 768px) {
			#subcategory .control-label {
				text-align: left;
				margin-bottom: 0;
				padding-top: 0px;
				height: 25px;
    			line-height: 25px;
				padding-left: 5px;
			}

			#subcategory .right {
				text-align: right;
			}

			.form-horizontal .form-group-sm .control-label {
				padding-top: 3px;
			}
			#category_select_group .control-label {
				padding-top: 8px;
				
			}

			#subcategory .form-group {
				margin-bottom: 5px;
			}
			#subcategory input {
				margin-right: -15px;
			}
		}

		#mode_manual{
			margin-left: 10px;
		}
        
</style>
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
		<div id="subcategory_checkboxes">
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
		</div>
		<!-- Dropdown danh mục chính, chỉ hiển thị khi chọn "Thủ công" -->
		<div class="form-group form-group-sm" id="category_select_group" style="display:none;">
			<?php echo form_label('Danh mục', 'category_select', array('class'=>'control-label col-xs-3')); ?>
			<div class="col-xs-6">
				<select id="category_select" name="category_select" class="form-control">
					<option value=""><?php echo $this->lang->line('oincs_select_category'); ?></option>
					<option value="lens">Mắt kính</option>
					<option value="frame">Gọng kính</option>
					<option value="sun_glasses">Kính râm</option>
					<option value="medicines">Thuốc</option>
					<option value="contact_lens">Kính áp tròng</option>
				</select>
			</div>
		</div>
		<div id="subcategory">
			<div id="lens">
				<?php checkbox_from_array($lens,'m_lens','m_is_lens','control-label col-xs-8'); ?>
			</div>
			<div id="contact_lens">
				<?php checkbox_from_array(
					$contact_lens,
					'm_contact_lens',
					'm_is_contact_lens',
					'control-label col-xs-8'); ?>
			
			</div>
			<div id="frame">
			<?php checkbox_from_array(
					$frame,
					'm_frame',
					'm_is_frame',
					'control-label col-xs-8'); ?>

			</div>
			<div id="sun_glasses">
			<?php checkbox_from_array(
					$sun_glasses,
					'm_sun_glasses',
					'm_is_sun_glasses',
					'control-label col-xs-8'); ?>
			</div>
			<div id="medicines">
			<?php checkbox_from_array(
					$medicines,
					'm_medicines',
					'm_is_medicines',
					'control-label col-xs-8'); ?>
			</div>				
		</div>						
	</fieldset>
<?php echo form_close(); ?>

<script type="text/javascript">

	function hide_subcategory(exp = '')
	{
		if(exp == '')
		{
			$('#lens').hide();
			$('#contact_lens').hide();
			$('#frame').hide();
			$('#sun_glasses').hide();
			$('#medicines').hide();
		} else {
			$('#lens').hide();
			$('#contact_lens').hide();
			$('#frame').hide();
			$('#sun_glasses').hide();
			$('#medicines').hide();
			$('#'+exp).show();
		}
	}
	$(document).ready(function() {
		// Khi chọn chế độ thủ công, hiển thị droplist và ẩn các danh mục con
		$('input[name="mode"]').change(function() {
			if ($('#mode_manual').is(':checked')) {
				$('#category_select_group').show();
				$('#subcategory').show();
				hide_subcategory();
				$('#subcategory_checkboxes div').hide(); // Ẩn tất cả danh mục con
			} else {
				$('#category_select_group').hide();
				$('#subcategory').hide();
				$('#subcategory_checkboxes div').show(); // Hiển thị tất cả danh mục con
			}
		}).trigger('change'); // Kích hoạt sự kiện thay đổi khi tải trang để thiết lập mặc định

		// Hiển thị danh mục con dựa trên lựa chọn của danh mục chính
		$('#category_select').change(function() {
			$('#subcategory_checkboxes div').hide();  // Ẩn tất cả các nhóm danh mục con
			var selectedCategory = $(this).val();
			console.log(selectedCategory);
			hide_subcategory(selectedCategory);

		});

		$('#oinc_form').submit(function(event) {
			// Kiểm tra xem có ít nhất một checkbox được chọn
			if($('#mode_manual').is(':checked'))
			{
				var selectedCategory = $('#category_select').val();
				console.log(selectedCategory);
				if ($('input[name="m_'+selectedCategory+'[]"]:checked').length == 0) {
					$('#error_message_box').html('<li>Vui lòng chọn ít nhất một hạng mục muốn kiểm kê.</li>');
					event.preventDefault(); // Ngăn form không submit
				}
			} else {
				if ($('input[name="items[]"]:checked').length == 0) {
					$('#error_message_box').html('<li>Vui lòng chọn ít nhất một hạng mục muốn kiểm kê.</li>');
					event.preventDefault(); // Ngăn form không submit
				}
			}
		});

		// Sự kiện khi nhấn nút Hủy hoạt động
        $('#cancel').on('click', function() {
            // Đóng modal khi nhấn nút Hủy
            $('.modal').modal('hide');
        });
	});
</script>

