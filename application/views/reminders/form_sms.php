<div id="required_fields_message"><?php echo $this->lang->line('common_fields_required_message'); ?></div>

<ul id="error_message_box" class="error_message_box"></ul>
	
<?php echo form_open("reminders/send_form/".$reminder_info->id, array('id'=>'send_sms_form', 'class'=>'form-horizontal')); ?>
	<fieldset>
		<div class="form-group form-group-sm">
			<?php echo form_label($this->lang->line('reminder_name'), 'name_label', array('for'=>'name', 'class'=>'control-label col-xs-2')); ?>
			<div class="col-xs-10">
				<?php echo form_input(array('class'=>'form-control input-sm', 'type'=>'text', 'name'=>'first_name', 'value'=>$reminder_info->name, 'readonly'=>'true'));?>
			</div>
		</div>

		<div class="form-group form-group-sm">
			<?php echo form_label($this->lang->line('reminder_phone'), 'phone_label', array('for'=>'phone', 'class'=>'control-label col-xs-2')); ?>
			<div class="col-xs-10">
				<div class="input-group">
					<span class="input-group-addon input-sm"><span class="glyphicon glyphicon-phone-alt"></span></span>
					<?php echo form_input(array('class'=>'form-control input-sm', 'type'=>'text', 'name'=>'phone', 'value'=>$reminder_info->phone, 'readonly'=>'true'));?>
				</div>
			</div>
		</div>
		<div class="form-group form-group-sm">
			<?php echo form_label('Địa chỉ', 'address_label', array('for'=>'address', 'class'=>'control-label col-xs-2')); ?>
			<div class="col-xs-10">
				<div class="input-group">
					<span class="input-group-addon input-sm"><span class="glyphicon glyphicon-address"></span></span>
					<?php echo form_input(array('class'=>'form-control input-sm', 'type'=>'text', 'name'=>'address', 'value'=>$reminder_info->address, 'readonly'=>'true'));?>
				</div>
			</div>
		</div>
		<div class="form-group form-group-sm">
			<?php echo form_label('Kết quả', 'status_label', array('for'=>'status', 'class'=>'control-label col-xs-2 required')); ?>
			<div class="col-xs-10">
				<div class="input-group">
					<span class="input-group-addon input-sm"><span class="glyphicon glyphicon-phone-alt"></span></span>
					<?php echo form_dropdown('status', $status, $reminder_info->status, array('class'=>'form-control')); ?>
				</div>
			</div>
		</div>
		<div class="form-group form-group-sm">
			<?php echo form_label('Lưu ý', 'message_label', array('for'=>'message', 'class'=>'control-label col-xs-2')); ?>
			<div class="col-xs-10">
				<?php echo form_textarea(array('class'=>'form-control input-sm', 'name'=>'message', 'id'=>'message', 'value'=>$this->config->item('msg_msg')));?>
			</div>
		</div>
	</fieldset>
<?php echo form_close(); ?>
<div>
	<?php if($histories != null):?>
	<ul>
		<?php
		 $i=1;
		 foreach($histories as $history): 
		 $_status = '';
		 if($history->status < 5)
		 {
			$_status = $status[$history->status];
		 } else {
			$_status = 'Đang khám lại';
		 }
		 ?>
		<li>
			<?php echo 'Lần '.$i .': <br/>'. date('d/m/Y',$history->created_time). ' - '. $_status .'<br/>'.$history->content;?> 
		</li>
		<?php $i++; endforeach;?>
	</ul>
	<?php endif;?>
</div>

<script type="text/javascript">
$(document).ready(function()
{
	$('#send_sms_form').validate($.extend({
		submitHandler:function(form) 
		{
			$(form).ajaxSubmit({
				success:function(response)
				{
					//alert(response);
					//alert(response.id);
					dialog_support.hide();
					table_support.handle_submit('<?php echo site_url('messages'); ?>', response);
				},
				dataType:'json'
			});
		},
		rules:
		{
			status:
			{
				required:true
			},
			
   		},
		messages:
		{
			status:
			{
				required:"Bạn cần chọn kết quả cuộc gọi"
			},
			
		}
	}, form_support.error));
});
</script>