<?php $this->load->view("partial/header"); ?>

<?php
if (isset($error))
{
	echo "<div class='alert alert-dismissible alert-danger'>".$error."</div>";
}

if (!empty($warning))
{
	echo "<div class='alert alert-dismissible alert-warning'>".$warning."</div>";
}

if (isset($success))
{
	echo "<div class='alert alert-dismissible alert-success'>".$success."</div>";
}

?>

<div id="register_wrapper">


	

	<?php $tabindex = 0; //var_dump($edit); ?>
	<?php echo form_open($controller_name."/add", array('id'=>'add_item_form', 'class'=>'form-horizontal panel panel-default')); ?>
		<div class="panel-body form-group">
			<ul>
				<li class="pull-left first_li">
					<label for="item" class='control-label'><?php echo $this->lang->line('oincs_scan_item'); ?></label>
				</li>
				<li class="pull-left">
					<?php echo form_input(array('name'=>'item', 'id'=>'item', 'class'=>'form-control input-sm', 'size'=>'50', 'tabindex'=>++$tabindex)); ?>
					<?php echo form_input(array('name'=>'add_hidden_ctv', 'id'=>'add_hidden_ctv', 'class'=>'form-control input-sm', 'size'=>'50', 'type'=>'hidden')); ?>
					<span class="ui-helper-hidden-accessible" role="status"></span>
				</li>
				<li class="pull-left" style="font-size: large; font-weight: bold">
					<?php echo $this->lang->line('receivings_quantity').':'.$quantity; ?>
				</li>
			</ul>
		</div>
	<?php echo form_close(); ?>
	

<!-- Sale Items List -->
	
	<table class="sales_table_100 add-new" id="register">
		<thead>
			<tr>
				<th style="width: 5%;"><?php echo $this->lang->line('common_delete'); ?></th>
				<th style="width: 15%;"><?php echo $this->lang->line('oincs_item_number'); ?></th>
				<th style="width: 35%;"><?php echo $this->lang->line('oincs_item_name'); ?></th>
				<th style="width: 10%;"><?php echo $this->lang->line('oincs_quantity'); ?></th>
				<th style="width: 10%;"><?php echo $this->lang->line('oinc_zone'); ?></th>
				<th style="width: 5%;"><?php echo $this->lang->line('oincs_update'); ?></th>
			</tr>
		</thead>

		<tbody id="cart_contents">
			<?php
			if(count($cart) == 0)
			{
			?>
				<tr>
					<td colspan='8'>
						<div class='alert alert-dismissible alert-info'><?php echo $this->lang->line('oincs_no_items_in_cart'); ?></div>
					</td>
				</tr>
			<?php
			}
			else
			{				
				foreach(array_reverse($cart, true) as $line=>$item)
				{					
			?>
					<?php echo form_open($controller_name."/edit_item/$line", array('class'=>'form-horizontal', 'id'=>'cart_'.$line)); ?>
						<tr>
							<td><?php echo anchor($controller_name."/delete_item/$line", '<span class="glyphicon glyphicon-trash"></span>');?></td>
							<td><?php echo $item['item_number']; ?><?php echo form_hidden('edit_hidden_ctv', '0'); ?></td>
							<td style="align: center;">
								<?php echo $item['name']; ?>								
							</td>
							<td>
								<?php							
									echo form_input(array('name'=>'quantity', 'class'=>'form-control input-sm quantity', 'value'=>to_quantity_decimals($item['quantity']), 'tabindex'=>++$tabindex));
								?>
							</td>
							<td>
								<?php							
									echo $item['item_category'];
								?>
							</td>
							<td><a href="javascript:document.getElementById('<?php echo 'cart_'.$line ?>').submit();" title=<?php echo $this->lang->line('sales_update')?> ><span class="glyphicon glyphicon-refresh"></span></a></td>
						</tr>
				
					<?php echo form_close(); ?>
			<?php
				}
			}
			?>
		</tbody>
	</table>
</div>
<!-- Overall Count [Right Panel]-->
<div id="overall_sale" class="panel panel-default">
	<div class="panel-body">
	<?php
	if(($TheOinc['oinc_id'] > 0))
	{
	?>
		<table class="sales_table_100">
			<tr>
				<th style='width: 55%;'><?php echo $this->lang->line("oinc_doc_entry"); ?></th>
				<th style="width: 45%; text-align: right;"><?php echo $TheOinc['doc_entry']; ?></th>
			</tr>
			<tr>
				<th style='width: 55%;'><?php echo $this->lang->line("oinc_doc_num"); ?></th>
				<th style="width: 45%; text-align: right;"><?php echo $TheOinc['doc_num']; ?></th>
			</tr>
			<tr>
				<th style='width: 55%;'><?php echo $this->lang->line("oinc_zone"); ?></th>
				<th style="width: 45%; text-align: right;"><?php echo $TheOinc['zone']; ?></th>
			</tr>
			<tr>
				<th style='width: 55%;'><?php echo $this->lang->line("oinc_created_at"); ?></th>
				<th style="width: 45%; text-align: right;"><?php echo date('d/m/Y h:m',$TheOinc['created_at']); ?></th>
			</tr>
			<tr>
				<th style='width: 55%;'><?php echo $this->lang->line("oinc_creator_name"); ?></th>
				<th style="width: 45%; text-align: right;"><?php echo $TheOinc['creator_name']; ?></th>
			</tr>
			
		</table>
	<?php }	?>
		
	<?php
		// Only show this part if there are Items already in the sale.
	if(count($cart) > 0)
	{
	?>
			<?php echo form_open($controller_name."/cancel", array('id'=>'buttons_form')); ?>
				<div class="form-group" id="buttons_sale">
					<?php echo form_input(array('name'=>'hidden_form', 'id'=>'hidden_form', 'class'=>'form-control input-sm', 'value'=>'1', 'type'=>'hidden')); ?>
					<!-- <div class='btn btn-sm btn-default pull-left' id='suspend_sale_button'><span class="glyphicon glyphicon-align-justify">&nbsp</span><?php echo $this->lang->line('sales_suspend_sale'); ?></div> -->
					<?php echo form_input(array('name'=>'hidden_ctv', 'id'=>'hidden_ctv', 'class'=>'form-control input-sm', 'value'=>'', 'type'=>'hidden')); ?>

					<div class='btn btn-sm btn-danger pull-right' id='cancel_sale_button'><span class="glyphicon glyphicon-remove">&nbsp</span><?php echo $this->lang->line('sales_cancel_sale'); ?></div>
				</div>
			<?php echo form_close(); ?>
			<div class='btn btn-sm btn-warning pull-right' id='finish_sale_button' tabindex='<?php echo ++$tabindex; ?>'><span class="glyphicon glyphicon-ok">&nbsp</span><?php echo $this->lang->line('oincs_complete_count'); ?></div>
			<div class='btn btn-sm btn-success pull-right' id='save_button' tabindex='<?php echo ++$tabindex; ?>'><span class="glyphicon glyphicon-ok">&nbsp</span><?php echo $this->lang->line('oincs_save_count'); ?></div>
			
		<?php
		}
		?>
	</div>
</div>
<script src="/dist/jquery.number.min.js"></script>
<script type="text/javascript">
$(document).ready(function()
{

	$('.quantity').number(true,0,',','.');

	$("#item").autocomplete(
	{
		source: '<?php echo site_url($controller_name."/item_search"); ?>',
    	minChars: 0,
    	autoFocus: false,
       	delay: 600,
		select: function (a, ui) {
			$(this).val(ui.item.value);
			$("#add_item_form").submit();
			return false;
		}
    });

	$('#item').focus();

	$('#item').keypress(function (e) {
		if (e.which == 13) {
			$('#add_item_form').submit();
			return false;
		}
	});

    $('#item').blur(function()
    {
        $(this).val("<?php echo $this->lang->line('sales_start_typing_item_name'); ?>");
    });

    var clear_fields = function()
    {
        if ($(this).val().match("<?php echo $this->lang->line('sales_start_typing_item_name') . '|' . $this->lang->line('sales_start_typing_customer_name'); ?>"))
        {
            $(this).val('');
        }
    };

	$('#item').click(clear_fields).dblclick(function(event)
	{
		$(this).autocomplete("search");
	});

	
    $("#finish_sale_button").click(function()
    {
		$('#hidden_ctv').val(1);
		$('#buttons_form').attr('action', '<?php echo site_url($controller_name . "/complete"); ?>');
		$('#buttons_form').submit();
		
    });

	$("#save_button").click(function()
    {
		
		$('#hidden_ctv').val(1);
		$('#buttons_form').attr('action', '<?php echo site_url($controller_name . "/do_save"); ?>');
		$('#buttons_form').submit();
    });

	

    $("#cancel_sale_button").click(function()
    {
    	if (confirm('<?php echo $this->lang->line("sales_confirm_cancel_sale"); ?>'))
    	{
			$('#buttons_form').attr('action', '<?php echo site_url($controller_name."/cancel"); ?>');
    		$('#buttons_form').submit();
    	}
    });

	$("#cart_contents input").keypress(function(event)
	{
		if (event.which == 13)
		{
			$(this).parents("tr").prevAll("form:first").submit();
		}
	});


    $("#finish_sale_button").keypress(function(event)
	{
		if ( event.which == 13 )
		{
			$('#finish_sale_form').submit();
		}
	});

	dialog_support.init("a.modal-dlg, button.modal-dlg");

	table_support.handle_submit = function(resource, response, stay_open)
	{
		if(response.success) {
			if (resource.match(/customers$/))
			{
				console.log('#select_customer_form submit');
				$("#customer").val(response.id);
				$("#select_customer_form").submit();
			}
			else
			{
				var $stock_location = $("select[name='stock_location']").val();
				$("#item_location").val($stock_location);
				$("#item").val(response.id);
				if (stay_open)
				{
					$("#add_item_form").ajaxSubmit();
				}
				else
				{
					$("#add_item_form").submit();
				}
			}
		}
	}
});

</script>

<?php $this->load->view("partial/footer"); ?>
