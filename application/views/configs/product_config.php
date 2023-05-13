<?php echo form_open('config/save_products/', array('id' => 'product_config_form', 'class' => 'form-horizontal')); ?>
    <div id="config_wrapper">
        <fieldset id="config_info">
            <div id="required_fields_message"><?php echo $this->lang->line('common_fields_required_message'); ?></div>
            <ul id="product_error_message_box" class="error_message_box"></ul>

            <div id="lens_product">
				<?php $this->load->view('partial/_product', array('_products' =>array('value'=>$lens_product,'key'=>'iKindOfLens'))); ?>
			</div>
			<div id="contact_lens_product">
				<?php $this->load->view('partial/_product', array('_products' => array('value'=>$contact_lens_product,'key'=>'filter_contact_lens'))); ?>
			</div>
			<div id="frame_product">
				<?php $this->load->view('partial/_product', array('_products' => array('value'=>$frame_product,'key'=>'filter'))); ?>
			</div>
			<div id="glasses_product">
				<?php $this->load->view('partial/_product', array('_products' => array('value'=>$glasses_product,'key'=>'filter_sun_glasses'))); ?>
			</div>
			<div id="other_product">
				<?php $this->load->view('partial/_product', array('_products' => array('value'=>$other_product,'key'=>'other_filter'))); ?>
			</div>
            
            <?php echo form_submit(array(
                'name' => 'submit',
                'id' => 'submit',
                'value'=>$this->lang->line('common_submit'),
                'class' => 'btn btn-primary btn-sm pull-right')); ?>
        </fieldset>
    </div>
<?php echo form_close(); ?>

<script type="text/javascript">
//validation and submit handling
$(document).ready(function()
{
	
	$('#product_config_form').validate($.extend(form_support.handler, {
		submitHandler: function(form) {
			$(form).ajaxSubmit({
				success: function(response)	{
					$.notify({ message: response.message }, { type: response.success ? 'success' : 'danger'});
				},
				dataType: 'json'
			});
		},

		errorLabelContainer: "#product_error_message_box",

		rules:
		{
   		},

		messages: 
		{
		}
	}));
});
</script>
