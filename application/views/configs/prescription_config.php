<?php echo form_open('config/save_prescription/', array('id' => 'general_config_form', 'enctype' => 'multipart/form-data', 'class' => 'form-horizontal')); ?>
	<div id="config_wrapper">
		<fieldset id="config_info">
			<div id="required_fields_message"><?php echo $this->lang->line('common_fields_required_message'); ?></div>
			<ul id="general_error_message_box" class="error_message_box"></ul>


			<div class="form-group form-group-sm">	
				<?php echo form_label($this->lang->line('test_header'), 'test_header', array('class' => 'control-label col-xs-2')); ?>
				<div class='col-xs-2'>
					<?php echo form_checkbox(array(
						'name' => 'test_header',
						'id' => 'test_header',
						'value' => '1',
						'checked'=>$this->config->item('test_header'))); ?>
				</div>
			</div>

			<div class="form-group form-group-sm">	
				<?php echo form_label($this->lang->line('ten_phong_kham'), 'ten_phong_kham', array('class' => 'control-label col-xs-2')); ?>
				<div class="col-xs-6">
					<div class="input-group">
						<span class="input-group-addon input-sm"><span class="glyphicon glyphicon-home"></span></span>
						<?php echo form_input(array(
							'name' => 'ten_phong_kham',
							'id' => 'ten_phong_kham',
							'class' => 'form-control input-sm required',
							'value'=>$this->config->item('ten_phong_kham'))); ?>
					</div>
				</div>
			</div>

			<div class="form-group form-group-sm">	
				<?php echo form_label($this->lang->line('lien_he'), 'lien_he', array('class' => 'control-label col-xs-2')); ?>
				<div class="col-xs-6">
					<div class="input-group">
						<span class="input-group-addon input-sm"><span class="glyphicon glyphicon-contact"></span></span>
						<?php echo form_input(array(
							'name' => 'lien_he',
							'id' => 'lien_he',
							'class' => 'form-control input-sm required',
							'value'=>$this->config->item('lien_he'))); ?>
					</div>
				</div>
			</div>

			<div class="form-group form-group-sm">	
				<?php echo form_label($this->lang->line('hien_thi_VA'), 'hien_thi_VA', array('class' => 'control-label col-xs-2')); ?>
				<div class='col-xs-2'>
					<?php echo form_checkbox(array(
						'name' => 'hien_thi_VA',
						'id' => 'hien_thi_VA',
						'value' => '1',
						'checked'=>$this->config->item('hien_thi_VA'))); ?>
				</div>
			</div>

			<div class="form-group form-group-sm">	
				<?php echo form_label($this->lang->line('hien_thi_kinh_cu'), 'hien_thi_kinh_cu', array('class' => 'control-label col-xs-2')); ?>
				<div class='col-xs-2'>
					<?php echo form_checkbox(array(
						'name' => 'hien_thi_kinh_cu',
						'id' => 'hien_thi_kinh_cu',
						'value' => '1',
						'checked'=>$this->config->item('hien_thi_kinh_cu'))); ?>
				</div>
			</div>
			<div class="form-group form-group-sm">	
				<?php echo form_label($this->lang->line('loai_mat_kinh'), 'loai_mat_kinh', array('class' => 'control-label col-xs-2')); ?>
				<div class='col-xs-2'>
					<?php echo form_checkbox(array(
						'name' => 'loai_mat_kinh',
						'id' => 'loai_mat_kinh',
						'value' => '1',
						'checked'=>$this->config->item('loai_mat_kinh'))); ?>
				</div>
			</div>

			<div class="form-group form-group-sm">	
				<?php echo form_label($this->lang->line('hien_thi_ten_bac_si'), 'hien_thi_ten_bac_si', array('class' => 'control-label col-xs-2')); ?>
				<div class='col-xs-2'>
					<?php echo form_checkbox(array(
						'name' => 'hien_thi_ten_bac_si',
						'id' => 'hien_thi_ten_bac_si',
						'value' => '1',
						'checked'=>$this->config->item('hien_thi_ten_bac_si'))); ?>
				</div>
			</div>

			<div class="form-group form-group-sm">	
				<?php echo form_label($this->lang->line('ten_bac_si'), 'ten_bac_si', array('class' => 'control-label col-xs-2')); ?>
				<div class="col-xs-6">
					<div class="input-group">
						<span class="input-group-addon input-sm"><span class="glyphicon glyphicon-contact"></span></span>
						<?php echo form_input(array(
							'name' => 'ten_bac_si',
							'id' => 'ten_bac_si',
							'class' => 'form-control input-sm required',
							'value'=>$this->config->item('ten_bac_si'))); ?>
					</div>
				</div>
			</div>

			<div class="form-group form-group-sm">	
				<?php echo form_label($this->lang->line('test_display_nurse'), 'test_display_nurse', array('class' => 'control-label col-xs-2')); ?>
				<div class='col-xs-2'>
					<?php echo form_checkbox(array(
						'name' => 'test_display_nurse',
						'id' => 'test_display_nurse',
						'value' => '1',
						'checked'=>$this->config->item('test_display_nurse'))); ?>
				</div>
			</div>

			<div class="form-group form-group-sm">	
				<?php echo form_label($this->lang->line('test_display_kxv'), 'test_display_kxv', array('class' => 'control-label col-xs-2')); ?>
				<div class='col-xs-2'>
					<?php echo form_checkbox(array(
						'name' => 'test_display_kxv',
						'id' => 'test_display_kxv',
						'value' => '1',
						'checked'=>$this->config->item('test_display_kxv'))); ?>
				</div>
			</div>

			<div class="form-group form-group-sm">	
				<?php echo form_label($this->lang->line('has_prescription'), 'has_prescription', array('class' => 'control-label col-xs-2')); ?>
				<div class='col-xs-2'>
					<?php echo form_checkbox(array(
						'name' => 'has_prescription',
						'id' => 'has_prescription',
						'value' => '1',
						'checked'=>$this->config->item('has_prescription'))); ?>
				</div>
			</div>
			

			<?php echo form_submit(array(
				'name' => 'submit_form',
				'id' => 'submit_form',
				'value'=>$this->lang->line('common_submit'),
				'class' => 'btn btn-primary btn-sm pull-right')); ?>
		</fieldset>
	</div>
<?php echo form_close(); ?>

<script type="text/javascript">
//validation and submit handling
$(document).ready(function()
{

	$("#backup_db").click(function() {
		window.location='<?php echo site_url('config/backup_db') ?>';
	});

	$('#general_config_form').validate($.extend(form_support.handler, {

		errorLabelContainer: "#general_error_message_box",

		rules: 
		{
    		default_tax_1_rate:
    		{
    			required: true,
				number:true
				//remote: "<?php echo site_url($controller_name . '/check_numeric')?>"
    		},
			default_tax_1_name: "required",
			default_tax2_rate:
			{
				number:true
				//remote: "<?php echo site_url($controller_name . '/check_numeric')?>"
			},
    		lines_per_page:
    		{
        		required: true,
				number:true
				//remote: "<?php echo site_url($controller_name . '/check_numeric')?>"
    		},
    		default_sales_discount: 
        	{
        		required: true,
				number:true
				//remote: "<?php echo site_url($controller_name . '/check_numeric')?>"
    		}  		
   		},

		messages: 
		{
			default_tax_1_rate:
			{
				required: "<?php echo $this->lang->line('config_default_tax_rate_required'); ?>",
				number: "<?php echo $this->lang->line('config_default_tax_rate_number'); ?>"
			},
			default_tax_1_name:
			{
				required: "<?php echo $this->lang->line('config_default_tax_name_required'); ?>",
				number: "<?php echo $this->lang->line('config_default_tax_name_number'); ?>"
			},
			default_sales_discount:
			{
				required: "<?php echo $this->lang->line('config_default_sales_discount_required'); ?>",
				number: "<?php echo $this->lang->line('config_default_sales_discount_number'); ?>"
			},
			lines_per_page: 
			{
				required: "<?php echo $this->lang->line('config_lines_per_page_required'); ?>",
				number: "<?php echo $this->lang->line('config_lines_per_page_number'); ?>"
			}
		}
	}));
});
</script>
