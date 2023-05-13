<div id="required_fields_message"><?php echo $this->lang->line('common_fields_required_message'); ?></div>

<ul id="error_message_box" class="error_message_box"></ul>

<?php echo form_open('config/save_ctv/'.$person_info->person_id, array('id'=>'ctv_form', 'class'=>'form-horizontal')); ?>
	<ul class="nav nav-tabs nav-justified" data-tabs="tabs">
		<li class="active" role="presentation">
			<a data-toggle="tab" href="#employee_basic_info"><?php echo $this->lang->line("ctv_basic_information"); ?></a>
		</li>
		<li role="presentation">
			<a data-toggle="tab" href="#employee_login_info"><?php echo $this->lang->line("ctv_login_info"); ?></a>
		</li>
	</ul>

	<div class="tab-content">
		<div class="tab-pane fade in active" id="employee_basic_info">
			<fieldset>
				<?php $this->load->view("people/form_basic_info"); ?>
			</fieldset>
		</div>

		<div class="tab-pane" id="employee_login_info">
			<fieldset>
				<div class="form-group form-group-sm">	
					<?php echo form_label($this->lang->line('employees_username'), 'username', array('class'=>'required control-label col-xs-3')); ?>
					<div class='col-xs-8'>
						<div class="input-group">
							<span class="input-group-addon input-sm"><span class="glyphicon glyphicon-user"></span></span>
							<?php echo form_input(array(
									'name'=>'username',
									'id'=>'username',
									'class'=>'form-control input-sm',
									'value'=>$person_info->username)
									);?>
						</div>
					</div>
				</div>

				<?php $password_label_attributes = $person_info->person_id == "" ? array('class'=>'required') : array(); ?>

				<div class="form-group form-group-sm">	
					<?php echo form_label($this->lang->line('employees_password'), 'password', array_merge($password_label_attributes, array('class'=>'control-label col-xs-3'))); ?>
					<div class='col-xs-8'>
						<div class="input-group">
							<span class="input-group-addon input-sm"><span class="glyphicon glyphicon-lock"></span></span>
							<?php echo form_password(array(
									'name'=>'password',
									'id'=>'password',
									'class'=>'form-control input-sm')
									);?>
						</div>
					</div>
				</div>

				<div class="form-group form-group-sm">	
				<?php echo form_label($this->lang->line('employees_repeat_password'), 'repeat_password', array_merge($password_label_attributes, array('class'=>'control-label col-xs-3'))); ?>
					<div class='col-xs-8'>
						<div class="input-group">
							<span class="input-group-addon input-sm"><span class="glyphicon glyphicon-lock"></span></span>
							<?php echo form_password(array(
									'name'=>'repeat_password',
									'id'=>'repeat_password',
									'class'=>'form-control input-sm')
									);?>
						</div>
					</div>
				</div>
			</fieldset>
		</div>
	</div>
<?php echo form_close(); ?>

<script type="text/javascript">
//validation and submit handling
$(document).ready(function()
{
	$.validator.setDefaults({ ignore: [] });
	$('#ctv_form').validate($.extend({
		submitHandler:function(form) 
		{
			$(form).ajaxSubmit({
				success:function(response)
				{
					dialog_support.hide();
					table_support.handle_submit('<?php echo site_url('config'); ?>', response);
				},
				dataType:'json'
			});
		},
		rules:
		{
			first_name: "required",
			last_name: "required",
			username:
			{
				required:true,
				minlength: 5
			},
			
			password:
			{
				<?php
				if($person_info->person_id == "")
				{
				?>
				required:true,
				<?php
				}
				?>
				minlength: 8
			},	
			repeat_password:
			{
 				equalTo: "#password"
			},
    		email: "email"
   		},
		messages: 
		{
     		first_name: "<?php echo $this->lang->line('common_first_name_required'); ?>",
     		last_name: "<?php echo $this->lang->line('common_last_name_required'); ?>",
     		username:
     		{
     			required: "<?php echo $this->lang->line('employees_username_required'); ?>",
     			minlength: "<?php echo $this->lang->line('employees_username_minlength'); ?>"
     		},
     		
			password:
			{
				<?php
				if($person_info->person_id == "")
				{
				?>
				required:"<?php echo $this->lang->line('employees_password_required'); ?>",
				<?php
				}
				?>
				minlength: "<?php echo $this->lang->line('employees_password_minlength'); ?>"
			},
			repeat_password:
			{
				equalTo: "<?php echo $this->lang->line('employees_password_must_match'); ?>"
     		},
     		email: "<?php echo $this->lang->line('common_email_invalid_format'); ?>"
		}
	}, form_support.error));
});
</script>