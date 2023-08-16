<?php $this->load->view("partial/header"); ?>
<link rel="stylesheet" rev="stylesheet" href="<?php echo base_url();?>dist/barcode_print.css" />
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

<!-- Top register controls -->
	
	<?php echo form_open('items'."/add_barcodes", array('id'=>'mode_form', 'class'=>'form-horizontal panel panel-default')); ?>
		<div class="panel-body form-group">
			<ul>
				<?php if(!empty($this->config->item('G1Barcode'))): ?>
					<li class="pull-right">
						<?php echo anchor($controller_name."/barcode1", '<span class="glyphicon glyphicon-list-alt">&nbsp</span>' . 'Tạo barcode gọng 1',
									array('class'=>'btn btn-primary btn-sm','target'=>"_blank", 'id'=>'sales_takings_button', 'title'=>'Tạo barcode gọng 1')); ?>
					</li>
				<?php endif;?>
				<li class="pull-right">
						<?php echo anchor($controller_name."/barcode", '<span class="glyphicon glyphicon-list-alt">&nbsp</span>' . 'Tạo barcode gọng',
									array('class'=>'btn btn-primary btn-sm','target'=>"_blank", 'id'=>'sales_takings_button', 'title'=>'Tạo barcode gọng')); ?>
				</li>
				<li class="pull-right">
						<?php echo anchor($controller_name."/barcode_lens", '<span class="glyphicon glyphicon-list-alt">&nbsp</span>' . 'Tạo barcode mắt',
									array('class'=>'btn btn-primary btn-sm', 'target'=>"_blank",'id'=>'sales_takings_button', 'title'=>'Tạo barcode mắt')); ?>
				</li>
				<li class="pull-right">
						<?php echo anchor($controller_name."/empty", '<span class="glyphicon glyphicon-list-alt">&nbsp</span>' . 'Tạo mới',
									array('class'=>'btn btn-primary btn-sm', 'id'=>'sales_takings_button', 'title'=>'Tạo mới')); ?>
				</li>

				<li class="pull-right">
					<button class='btn btn-default btn-sm' id='add_bulk' data-href='<?php echo site_url('items'."/add_barcodes"); ?>'
							title='Thêm sản phẩm'>
						<span class="glyphicon glyphicon-align-justify">&nbsp</span><?php echo 'Thêm sản phẩm'; ?>
					</button>
				</li>

				
				
			</ul>
		</div>
	<?php echo form_close(); ?>
	
	<?php $tabindex = 0; ?>
	
	<?php echo form_open($controller_name."/add", array('id'=>'add_item_form', 'class'=>'form-horizontal panel panel-default')); ?>
		<div class="panel-body form-group">
			<ul>
				<li class="pull-left first_li">
					<label for="item" class='control-label'><?php echo $this->lang->line('sales_find_or_scan_item_or_receipt'); ?></label>
				</li>
				<li class="pull-left">
					<?php echo form_input(array('name'=>'item', 'id'=>'item', 'class'=>'form-control input-sm', 'size'=>'50', 'tabindex'=>++$tabindex)); ?>
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
			<th style="width: 15%;"><?php echo $this->lang->line('sales_item_number'); ?></th>
			<th style="width: 35%;"><?php echo $this->lang->line('sales_item_name'); ?></th>
			<th style="width: 10%;"><?php echo $this->lang->line('sales_price'); ?></th>
			<th style="width: 10%;"><?php echo $this->lang->line('sales_quantity'); ?></th>			
			<th style="width: 5%;"><?php echo $this->lang->line('sales_update'); ?></th>
		</tr>
	</thead>

	<tbody id="cart_contents">
		<?php
		if(count($cart) == 0)
		{
		?>
			<tr>
				<td colspan='8'>
					<div class='alert alert-dismissible alert-info'><?php echo $this->lang->line('sales_no_items_in_cart'); ?></div>
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
						<td><?php echo $item['item_number']; ?></td>
						<td style="align: center;">
							<?php echo $item['name']; ?><br /> <?php //echo '[' . to_quantity_decimals($item['in_stock']) . ' in ' . $item['stock_name'] . ']'; ?>
							<?php //echo form_hidden('location', $item['item_location']); ?>
						</td>

						
						<td>
							<?php echo to_currency($item['price']); ?>
							<?php echo form_hidden('price', to_currency_no_money($item['price'])); ?>
						</td>
					

						<td>
							<?php
							if($item['is_serialized']==1)
							{
								echo to_quantity_decimals($item['quantity']);
								echo form_hidden('quantity', $item['quantity']);
							}
							else
							{								
								echo form_input(array('name'=>'quantity', 'class'=>'form-control input-sm', 'value'=>to_quantity_decimals($item['quantity']), 'tabindex'=>++$tabindex));
							}
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

<!-- Overall Sale -->
<script src="/dist/jquery.number.min.js"></script>
<script type="text/javascript">
$(document).ready(function()
{
	$('#amount_tendered').number(true,0,',','.');

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

    $("#customer").autocomplete(
    {
		source: '<?php echo site_url("customers/suggest"); ?>',
    	minChars: 0,
    	delay: 10,
		select: function (a, ui) {
			$(this).val(ui.item.value);
			$("#select_customer_form").submit();
		}
    });

	$('#item, #customer').click(clear_fields).dblclick(function(event)
	{
		$(this).autocomplete("search");
	});

	$('#customer').blur(function()
    {
    	$(this).val("<?php echo $this->lang->line('sales_start_typing_customer_name'); ?>");
    });
	$('#customer').keyup(function()
	{
		$('#dlg_form').attr('data-value', $('#customer').val());
	});

	$('#comment').keyup(function() 
	{
		$.post('<?php echo site_url($controller_name."/set_comment");?>', {comment: $('#comment').val()});
	});

	<?php
	if ($this->config->item('invoice_enable') == TRUE) 
	{
	?>
		$('#sales_invoice_number').keyup(function() 
		{
			$.post('<?php echo site_url($controller_name."/set_invoice_number");?>', {sales_invoice_number: $('#sales_invoice_number').val()});
		});

		var enable_invoice_number = function() 
		{
			var enabled = $("#sales_invoice_enable").is(":checked");
			$("#sales_invoice_number").prop("disabled", !enabled).parents('tr').show();
			return enabled;
		}

		enable_invoice_number();
		
		$("#sales_invoice_enable").change(function()
		{
			var enabled = enable_invoice_number();
			$.post('<?php echo site_url($controller_name."/set_invoice_number_enabled");?>', {sales_invoice_number_enabled: enabled});
		});
	<?php
	}
	?>

	$("#sales_print_after_sale").change(function()
	{
		$.post('<?php echo site_url($controller_name."/set_print_after_sale");?>', {sales_print_after_sale: $(this).is(":checked")});
	});

	$('#email_receipt').change(function() 
	{
		$.post('<?php echo site_url($controller_name."/set_email_receipt");?>', {email_receipt: $('#email_receipt').is(':checked') ? '1' : '0'});
	});
	
    $("#finish_sale_button").click(function()
    {
		var ctv_id = $('#ctv').val();
		$('#hidden_ctv').val(ctv_id);
    	if($('#customer').length > 0) {
			if ($('#customer').val() != '') {
				alert($('#customer').val());
				$('#customer').val('');
				$('#customer').focus();
			} else {
				$('#buttons_form').attr('action', '<?php echo site_url($controller_name . "/complete"); ?>');
				$('#buttons_form').submit();
			}
		}else{
			$('#buttons_form').attr('action', '<?php echo site_url($controller_name . "/complete"); ?>');
			$('#buttons_form').submit();
		}
    });

	$("#add_before_complete_button").click(function()
	{
		if($('#customer').length > 0){
			if($('#customer').val() != '') {
				alert($('#customer').val());
				$('#customer').val('');
				$('#customer').focus();
			}else{
				$('#add_payment_form').attr('action', '<?php echo site_url($controller_name . "/before_complete"); ?>');
				$('#add_payment_form').submit();
			}
		}else{
			
			$('#add_payment_form').attr('action', '<?php echo site_url($controller_name . "/before_complete"); ?>');
			$('#add_payment_form').submit();
		}
	});

	$("#suspend_sale_button").click(function()
	{ 	
		$('#buttons_form').attr('action', '<?php echo site_url($controller_name."/suspend"); ?>');
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

	$("#add_payment_button").click(function()
	{
		$('#add_payment_form').submit();
    });

	$("#payment_types").change(check_payment_type_giftcard).ready(check_payment_type_giftcard);

	$("#cart_contents input").keypress(function(event)
	{
		if (event.which == 13)
		{
			$(this).parents("tr").prevAll("form:first").submit();
		}
	});

	$("#amount_tendered").keypress(function(event)
	{
		if( event.which == 13 )
		{
			$('#add_payment_form').submit();
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

function check_payment_type_giftcard()
{
	var _iWei = 50000;
	if ($("#payment_types").val() == "<?php echo $this->lang->line('sales_giftcard'); ?>")
	{
		$("#amount_tendered_label").html("<?php echo $this->lang->line('sales_giftcard_number'); ?>");
		$('#amount_tendered').prop('readonly', false);
		$("#amount_tendered:enabled").val('').focus();
	}
	else if($("#payment_types").val() == "<?php echo $this->lang->line('sales_point'); ?>"){
		
		var _fPoints = parseFloat($("#c_points").val()).toFixed(0);
		//alert(_fPoints);
		var _iDiv = Math.floor(_fPoints/_iWei);
		var _iMaxPoint = _iDiv * _iWei
		//alert(_fPoints);
		$("#amount_tendered_label").html("<?php echo $this->lang->line('sales_amount_tendered'); ?>");
		$("#amount_tendered:enabled").val(_iMaxPoint);
		$('#amount_tendered').prop('readonly', true);
		//$("#c_points").val(_fPoints - _iMaxPoint);
	}
	else
	{
		$('#amount_tendered').prop('readonly', false);
		$("#amount_tendered_label").html("<?php echo $this->lang->line('sales_amount_tendered'); ?>");
		$("#amount_tendered:enabled").val('');
	}
}

</script>

<?php $this->load->view("partial/footer"); ?>
