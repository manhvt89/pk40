<?php echo form_open('config/save_info/', array('id' => 'info_config_form', 'enctype' => 'multipart/form-data', 'class' => 'form-horizontal')); ?>
	<div id="config_wrapper">
		<fieldset id="config_info">
			<div id="required_fields_message"><?php echo $this->lang->line('common_fields_required_message'); ?></div>
			<ul id="info_error_message_box" class="error_message_box"></ul>

			<div class="form-group form-group-sm">	
				<?php echo form_label($this->lang->line('config_company'), 'company', array('class' => 'control-label col-xs-2 required')); ?>
				<div class="col-xs-6">
					<div class="input-group">
						<span class="input-group-addon input-sm"><span class="glyphicon glyphicon-home"></span></span>
						<?php echo form_input(array(
							'name' => 'company',
							'id' => 'company',
							'class' => 'form-control input-sm required',
							'value'=>$this->config->item('company'))); ?>
					</div>
				</div>
			</div>

			<div class="form-group form-group-sm">	
				<?php echo form_label($this->lang->line('config_company_logo'), 'company_logo', array('class' => 'control-label col-xs-2')); ?>
				<div class='col-xs-6'>
					<div class="fileinput <?php echo $logo_exists ? 'fileinput-exists' : 'fileinput-new'; ?>" data-provides="fileinput">
						<div class="fileinput-new thumbnail" style="width: 200px; height: 200px;"></div>
						<div class="fileinput-preview fileinput-exists thumbnail" style="max-width: 200px; max-height: 200px;">
							<img data-src="holder.js/100%x100%" alt="<?php echo $this->lang->line('config_company_logo'); ?>"
								 src="<?php if($logo_exists) echo base_url('uploads/' . $this->config->item('company_logo')); else echo ''; ?>"
								 style="max-height: 100%; max-width: 100%;">
						</div>
						<div>
							<span class="btn btn-default btn-sm btn-file">
								<span class="fileinput-new"><?php echo $this->lang->line("config_company_select_image"); ?></span>
								<span class="fileinput-exists"><?php echo $this->lang->line("config_company_change_image"); ?></span>
								<input type="file" name="company_logo">
							</span>
							<a href="#" class="btn btn-default btn-sm fileinput-exists" data-dismiss="fileinput"><?php echo $this->lang->line("config_company_remove_image"); ?></a>
						</div>
					</div>
				</div>
			</div>

			<?php 
				echo form_field($this->lang->line('config_address'), 'address', $this->config->item('address'), 'textarea', true);
                echo form_field($this->lang->line('config_website'), 'website', $this->config->item('website'), 'text', false, ['icon' => 'globe']);
                echo form_field($this->lang->line('common_email'), 'email', $this->config->item('email'), 'email', false, ['icon' => 'envelope']);
                echo form_field($this->lang->line('config_phone'), 'phone', $this->config->item('phone'), 'text', true, ['icon' => 'phone-alt']);
                echo form_field($this->lang->line('config_fax'), 'fax', $this->config->item('fax'), 'text', false, ['icon' => 'phone-alt']);
                echo form_field($this->lang->line('config_guide'), 'guide', $this->config->item('guide'), 'text', false, ['icon' => 'globe']);
				//add thông tin thanh toán
				echo form_field($this->lang->line('config_qr_payment_size'), 'qr_payment_size', $this->config->item('qr_payment_size'), 'number', false, ['icon' => 'resize-full','suffix'=>'px']);
				echo form_field($this->lang->line('config_qr_payment_bankaccount'), 'qr_payment_bankaccount', $this->config->item('qr_payment_bankaccount'), 'text', false, ['icon' => 'credit-card']);
                echo form_field($this->lang->line('config_qr_payment_accountname'), 'qr_payment_accountname', $this->config->item('qr_payment_accountname'), 'text', false, ['icon' => 'user']);
				echo form_field($this->lang->line('config_qr_payment_bankname'), 'qr_payment_bankname', $this->config->item('qr_payment_bankname'), 'text', false, ['icon' => 'piggy-bank']);
				echo form_field($this->lang->line('config_qr_payment_bank_code'), 'qr_payment_bank_code', $this->config->item('qr_payment_bank_code'), 'text', false, ['icon' => 'barcode']);
				
				echo form_field($this->lang->line('common_return_policy'), 'return_policy', $this->config->item('return_policy'), 'textarea', true);
            ?>

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
	$("a.fileinput-exists").click(function() {
		$.ajax({
			type: "GET",
			url: "<?php echo site_url("$controller_name/remove_logo"); ?>",
			dataType: "json"
		})
	});

	$('#info_config_form').validate($.extend(form_support.handler, {

		errorLabelContainer: "#info_error_message_box",

		rules:
		{
			company: "required",
			phone: "required",
    		email: "email",
    		return_policy: "required" 		
   		},

		messages: 
		{
			company: "<?php echo $this->lang->line('config_company_required'); ?>",
			phone: "<?php echo $this->lang->line('config_phone_required'); ?>",
			email: "<?php echo $this->lang->line('common_email_invalid_format'); ?>",
			return_policy: "<?php echo $this->lang->line('config_return_policy_required'); ?>"
		}
	}));
});
</script>
