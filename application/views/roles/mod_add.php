<?php $this->load->view("partial/header"); ?>

<script type="text/javascript">
$(document).ready(function()
{

	
});
</script>

<div id="required_fields_message"><?php echo $this->lang->line('common_fields_required_message'); ?></div>

<ul id="error_message_box" class="error_message_box"></ul>

<?php echo form_open('roles/mod_save/', array('id'=>'mod_form', 'class'=>'form-horizontal')); ?>
	<fieldset id="supplier_basic_info">
    <div class="form-group form-group-sm">	
			<?php echo form_label('Tên mô đun', 'mod_name', array('class'=>'control-label col-xs-3')); ?>
			<div class='col-xs-8'>
            <?php echo form_input(array(
					'name'=>'mod_name',
					'id'=>'mod_name',
					'class'=>'form-control input-sm',
					'value'=>'')
					);?>
			</div>
		</div>
		<div class="form-group form-group-sm">	
			<?php echo form_label('Module Key', 'mod_key', array('class'=>'control-label col-xs-3')); ?>
			<div class='col-xs-8'>
                <?php echo form_dropdown('mod_key', $modules, $themodule, array('class'=>'form-control','id'=>'module_key')); ?>
			</div>
		</div>
        <div class="form-group form-group-sm">	
			<div class="col-xs-3"></div>
			<div class='col-xs-8'>
				<?php echo form_submit(array(
					'name'=>'mod',
					'id'=>'mod_summit',
					'class'=>'form-control input-sm',
					'value'=>'Thêm mới')
					);?>
			</div>
		</div>
	</fieldset>
<?php echo form_close(); ?>

<?php $this->load->view("partial/footer"); ?>
<script type="text/javascript">

//validation and submit handling
$(document).ready(function()
{
	
	$('#mod_form').validate($.extend({
		rules:
		{
			mod_name: "required",
			
    		
   		},
		messages: 
		{
            mod_name: "Bạn cần nhập tên mô đun",
     		
		}
	}, form_support.error));
});
</script>