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

<div id="register_wrapper_oinc" class="col-12 col-md-8 order-3 order-md-2">
	<?php $tabindex = 0; //var_dump($edit); ?>
	<?php echo form_open($controller_name."/#", array('id'=>'add_item_form', 'class'=>'form-horizontal panel panel-default')); ?>
		<div class="panel-body form-group">
			<ul>
				<li class="pull-left" style="font-size: large; font-weight: bold">
					<?php echo $this->lang->line('receivings_quantity').':'.$quantity; ?>
				</li>
			</ul>
		</div>
	<?php echo form_close(); ?>

<!-- Sale Items List -->
	
	<table class="sales_table_100 add-new" id="count">
		<thead>
			<tr>
				
				<th style="width: 15%;"><?php echo $this->lang->line('oincs_item_number'); ?></th>
				<th style="width: 35%;"><?php echo $this->lang->line('oincs_item_name'); ?></th>
				
				<th style="width: 10%;"><?php echo $this->lang->line('oinc_zone'); ?></th>
				<th style="width: 10%;"><?php echo $this->lang->line('oincs_quantity'); ?></th>
				<th style="width: 10%;"><?php echo $this->lang->line('oincs_in_whs_quantity'); ?></th>
				<th style="width: 10%;"><?php echo $this->lang->line('oincs_diffirent_quantity'); ?></th>
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
				//make_diff_first($cart);				
				foreach(make_diff_first($cart) as $line=>$item)
				{					
			?>
					<?php echo form_open($controller_name."/#/$line", array('class'=>'form-horizontal', 'id'=>'cart_'.$line)); ?>
						<tr class="<?= ($item['quantity'] - $item['in_whs_quantity']) == 0? "good":"lech" ?>">
							<td><?php echo $item['item_number']; ?><?php echo form_hidden('edit_hidden_ctv', '0'); ?></td>
							<td style="text-align: left;">
								<?php echo $item['name']; ?>								
							</td>
							<td>
								<?php							
									echo $item['item_category'];
								?>
							</td>
							<td style="text-align: right;">
								<?php	
									echo number_format($item['quantity'],2);						
									
								?>
							</td>
							
							<td style="text-align: right;">
								<?php							
									echo number_format($item['in_whs_quantity'],2);
								?>
							</td>
							<td style="text-align: right;">
								<?php							
									echo number_format($item['quantity'] - $item['in_whs_quantity'],2);
								?>
							</td>
				
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
<div id="overall_oinc" class="col-12 col-md-4 order-1 order-md-1 panel panel-default">
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

					<div class='btn btn-sm btn-danger pull-right' id='cancel_sale_button'><span class="glyphicon glyphicon-remove">&nbsp</span><?php echo $this->lang->line('oincs_cancel_post'); ?></div>
				</div>
			<?php echo form_close(); ?>
			<?php if(has_grant('is_can_post','oincs') && $TheOinc['status']=='C'): ?>
				<div class='btn btn-sm btn-success pull-right' id='finish_post_button' tabindex='<?php echo ++$tabindex; ?>'><span class="glyphicon glyphicon-ok">&nbsp</span><?php echo $this->lang->line('oincs_post_count'); ?></div>
			<?php endif; ?>
		<?php
		}
		?>
	</div>
</div>
<script src="/dist/jquery.number.min.js"></script>
<script type="text/javascript">
$(document).ready(function()
{
	
	$('#amount_tendered').number(true,0,',','.');
	$('.decimal').number(true,0,',','.');
	$('.bonus').number(true,2,',','.');
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


	$('#customer').keypress(function (e) {
		if (e.which == 13) {
			$('#select_customer_form').submit();
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
    	minChars: 4,
		autoFocus: false,
    	delay: 600,
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
		console.log('Key Up');
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

	$("#ctv").change(function()
	{
		var ctv_id = $('#ctv').val();
		
		$('#hidden_ctv').val(ctv_id);
		//var ctv_id = $('#ctv').val();
		$('#add_hidden_ctv').val(ctv_id);
		$("input[name='edit_hidden_ctv']").val(ctv_id);

		console.log($('#hidden_ctv').val());
		$.post('<?php echo site_url($controller_name."/change_ctv");?>', {ctv_id: $('#ctv').val()});
	});
	
    $("#finish_post_button").click(function()
    {
		var ctv_id = 0;
		$('#hidden_ctv').val(ctv_id);
    	
		$('#buttons_form').attr('action', '<?php echo site_url($controller_name . "/post"); ?>');
		$('#buttons_form').submit();
		
    });

	$("#add_before_complete_button").click(function()
	{
		var paymentMethod = $("#payment_types").val();
		var totalAmount = parseFloat($("#hd_amount_due").val());
		//var paymentAmount = parseFloat($(this).val());
		var paymentAmount = parseFloat($("#amount_tendered").val());
		if (((paymentMethod == "Chuyển khoản") && paymentAmount > totalAmount)) {
			alert("Số tiền thanh toán không thể lớn hơn tổng tiền hàng.");
			$(this).val(totalAmount.toFixed(0));
			return false;
		} else if ((paymentMethod == "Giảm thêm") && ((paymentAmount > 10000) || (paymentAmount > totalAmount))) {
			alert("Số tiền thanh toán không thể lớn hơn 10.000 hoặc Giảm thêm.");
			//console.log($(this));
			//$(this).val(totalAmount.toFixed(0));
			//console.log(totalAmount.toFixed(0));
			$(this).focus();
			return false;
		}

		var payment_list = $('#payment_contents tr');
		console.log(payment_list.length);
		if(payment_list.length != 0)
		{
			alert('Bạn cần xóa hết các mục đã thanh toán');
			return false;
		} 
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
    	
			$('#buttons_form').attr('action', '<?php echo site_url($controller_name."/cancel"); ?>');
    		$('#buttons_form').submit();
    	
    });

	$("#add_payment_button").click(function()
	{
		//$('#add_payment_form').submit();
		var paymentMethod = $("#payment_types").val();
		var totalAmount = parseFloat($("#hd_amount_due").val());
		//var paymentAmount = parseFloat($(this).val());
		var paymentAmount = parseFloat($("#amount_tendered").val());

		//var ctv_id = $('#ctv').val();
		//$('#add_hidden_ctv').val(ctv_id);
		
		console.log(totalAmount);
		console.log(paymentAmount);
		console.log(paymentMethod);
		if (((paymentMethod == "Chuyển khoản") && paymentAmount > totalAmount)) {
			alert("Số tiền thanh toán không thể lớn hơn tổng tiền hàng.");
			$(this).val(totalAmount.toFixed(0));
			return false;
		} else if ((paymentMethod == "Giảm thêm") && ((paymentAmount > 10000) || (paymentAmount > totalAmount))) {
			alert("Số tiền thanh toán không thể lớn hơn 10.000 hoặc Giảm thêm.");
			//console.log($(this));
			//$(this).val(totalAmount.toFixed(0));
			//console.log(totalAmount.toFixed(0));
			$(this).focus();
			return false;
		} else {
			$('#add_payment_form').submit();
		}
    });

	//$("#payment_types").change(check_payment_type_giftcard).ready(check_payment_type_giftcard);
	$("#payment_types").change(function(){
		
		var paymentMethod = $(this).val();
        var totalAmount = parseFloat($("#hd_amount_due").val());
		if (paymentMethod == "Chuyển khoản" || paymentMethod == "Tiền mặt") {
            $("#amount_tendered").val(totalAmount.toFixed(0));
        } else if(paymentMethod == "Giảm thêm"){
			$("#amount_tendered").val("");
		}else {
            $("#amount_tendered").val("");
        }
		console.log(paymentMethod);
	});
	$("#payment_types").val("Tiền mặt");
	$("#amount_tendered").val(0);
	// Kiểm tra và cập nhật số tiền thanh toán nếu vượt quá tổng tiền hàng
	$("#amount_tendered").change(function() {
                var paymentMethod = $("#payment_types").val();
                var totalAmount = parseFloat($("#hd_amount_due").val());
                var paymentAmount = parseFloat($(this).val());
				console.log(totalAmount);
				console.log(paymentAmount);
                if (((paymentMethod == "Chuyển khoản") && paymentAmount > totalAmount)) {
                    //alert("Số tiền thanh toán không thể lớn hơn tổng tiền hàng.");
                    //$(this).val(totalAmount.toFixed(0));
                } else if ((paymentMethod == "Giảm thêm") && (paymentAmount > 10000 || paymentAmount > totalAmount)){
					//alert("Số tiền thanh toán không thể lớn hơn 10.000 hoặc Giảm thêm.");
					//$(this).val(0);
					//return false;
				}
    });

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
			//$('#add_payment_form').submit();
			var paymentMethod = $("#payment_types").val();
			var totalAmount = parseFloat($("#hd_amount_due").val());
			var paymentAmount = parseFloat($(this).val());
			var ctv_id = $('#ctv').val();
			$('#add_hidden_ctv').val(ctv_id);

			console.log(totalAmount);
			console.log(paymentAmount);
			if (((paymentMethod == "Chuyển khoản") && paymentAmount > totalAmount)) {
				alert("Số tiền thanh toán không thể lớn hơn tổng tiền hàng.");
				$(this).val(totalAmount.toFixed(0));
				return false;
			} else if((paymentMethod == "Giảm thêm") && ((paymentAmount > 10000) || (paymentAmount > totalAmount))) {
				alert("Số tiền thanh toán không thể lớn hơn 10.000 hoặc Giảm thêm.");
				$(this).focus();
				return false;
			} else {
				$('#add_payment_form').submit();
			}
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
