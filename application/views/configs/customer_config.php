<?php echo form_open('config/save_customer/', array('id' => 'customer_config_form', 'enctype' => 'multipart/form-data', 'class' => 'form-horizontal')); ?>
	<div id="config_wrapper">
		<fieldset id="config_info">
			<div id="required_fields_message"><?php echo $this->lang->line('common_fields_required_message'); ?></div>
			<ul id="general_error_message_box" class="error_message_box"></ul>

			<div class="form-group form-group-sm">
				<?php echo form_label($this->lang->line('config_dob_type'), 'dob_type', array('class' => 'control-label col-xs-2')); ?>
				<div class='col-xs-3'>
					<?php echo form_dropdown('dob_type', $dob_types, $this->config->item('dob_type'), array('class' => 'form-control input-sm')); ?>
				</div>
			</div>

			<?php echo checkbox_config($this->lang->line('customer_is_facebook'),'customer_is_facebook','',$this->config->item('customer_is_facebook')); ?>
			<?php echo checkbox_config($this->lang->line('customer_is_comments'),'customer_is_comments','',$this->config->item('customer_is_comments')); ?>
			<?php echo checkbox_config($this->lang->line('customer_is_company_name'),'customer_is_company_name','',$this->config->item('customer_is_company_name')); ?>
			<?php echo checkbox_config($this->lang->line('customer_is_total'),'customer_is_total','',$this->config->item('customer_is_total')); ?>
			<?php echo checkbox_config($this->lang->line('customer_is_discount_percent'),'customer_is_discount_percent','',$this->config->item('customer_is_discount_percent')); ?>
			

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

	$('#customer_config_form').validate($.extend(form_support.handler, {

		errorLabelContainer: "#general_error_message_box",

		rules: 
		{
    		default_tax_1_rate:
    		{
    			required: true,
				decimalNumber:true
    		},
			default_tax_1_name: "required",
			default_tax2_rate:
			{
				decimalNumber:true
			},
    		lines_per_page:
    		{
        		required: true,
				number:true
    		},
    		default_sales_discount: 
        	{
        		required: true,
				number:true
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
