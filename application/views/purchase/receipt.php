<?php $this->load->view("partial/header"); ?>

<?php
if (isset($error_message))
{
	echo "<div class='alert alert-dismissible alert-danger'>".$error_message."</div>";
	exit;
}
?>

<?php $this->load->view('partial/print_receipt', array('print_after_sale', $print_after_sale, 'selected_printer'=>'receipt_printer')); ?>
<?php echo form_open($controller_name."/act", array('id'=>'action_form', 'class'=>'form-horizontal panel panel-default')); ?>
<div class="print_hide" id="control_buttons" style="text-align:right">
	<a href="javascript:printdoc();"><div class="btn btn-info btn-sm" id="show_print_button"><?php echo '<span class="glyphicon glyphicon-print">&nbsp</span>' . $this->lang->line('common_print'); ?></div></a>
	<?php if($valid_cart == false): ?>
		<div class='btn btn-sm btn-info pull-right' id='edit_button'><span class="glyphicon glyphicon-edit">&nbsp</span>Chỉnh sửa</div>
	<?php else: ?>	
		<?php if($completed == 0): ?>
			<div class='btn btn-sm btn-info pull-right' id='send_button'><span class="glyphicon glyphicon-send">&nbsp</span>Trình xem xét</div>
			<div class='btn btn-sm btn-info pull-right' id='edit_button'><span class="glyphicon glyphicon-edit">&nbsp</span>Chỉnh sửa</div>
		<?php elseif($completed == 1): ?>
			<div class='btn btn-sm btn-info pull-right' id='edit_button'><span class="glyphicon glyphicon-edit">&nbsp</span>Chỉnh sửa</div>
		<?php elseif($completed == 2): ?>
			<?php if($this->Employee->has_grant('purchases_approve')):?>
			<div class='btn btn-sm btn-info pull-right' id='approve_button'><span class="glyphicon glyphicon-ok">&nbsp</span>Duyệt</div>
			<div class='btn btn-sm btn-cancel pull-right' id='cancel_button'><span class="glyphicon glyphicon-remove">&nbsp</span>Sửa lại</div>
			<?php else: ?>
				<div class='btn btn-sm btn-pedding pull-right' id='pedding_button'><span class="glyphicon glyphicon-hourglass">&nbsp</span>Đang chờ duyệt</div>
			<?php endif;?>
		<?php elseif($completed == 3): ?>
			<div class='btn btn-sm btn-info pull-right' id='barcode_excel_export_button'><span class="glyphicon glyphicon-import">&nbsp</span>Xuất Excel (in Barcode)</div>
			<div class='btn btn-sm btn-info pull-right' id='barcode_button'><span class="glyphicon glyphicon-import">&nbsp</span>In Barcode</div>
			<div class='btn btn-sm btn-info pull-right' id='excel_export_button'><span class="glyphicon glyphicon-import">&nbsp</span>Xuất Excel</div>
			<div class='btn btn-sm btn-info pull-right' id='import_button'><span class="glyphicon glyphicon-import">&nbsp</span>Nhập kho</div>		
		<?php elseif($completed == 6): ?>
			<div class='btn btn-sm btn-info pull-right' id='barcode_excel_export_button'><span class="glyphicon glyphicon-import">&nbsp</span>Xuất Excel (in Barcode)</div>
			<div class='btn btn-sm btn-info pull-right' id='barcode_button'><span class="glyphicon glyphicon-import">&nbsp</span>In Barcode</div>
			<?php echo anchor("purchases", '<span class="glyphicon glyphicon-file">&nbsp</span>' .'Đơn đặt hàng mới', array('class'=>'btn btn-info btn-sm', 'id'=>'show_sales_button')); ?>
		<?php endif; ?>
	<?php endif; ?>	
	<?php echo anchor("purchases/manage", '<span class="glyphicon glyphicon-file">&nbsp</span>' .'Danh sách PO', array('class'=>'btn btn-info btn-sm', 'id'=>'show_po_list')); ?>
	
</div>
<?php //echo form_hidden('purchase_uuid', $purchase_uuid);
	echo form_input(array('name' => 'purchase_uuid', 'type'=>'hidden', 'id' =>'purchase_uuid','value'=>$purchase_uuid));
?>
<?php echo form_close(); ?>
<div id="receipt_wrapper">
	<div id="receipt_header">
		<?php
		if ($this->config->item('company_logo') == '') 
        { 
        ?>
			<div id="company_name"><?php echo $this->config->item('company'); ?></div>
		<?php 
		}
		else 
		{ 
		?>
			<div id="company_name"><img id="image" src="<?php echo base_url('uploads/' . $this->config->item('company_logo')); ?>" alt="company_logo" /></div>			
		<?php
		}
		?>
		<div id="company_address"><?php echo nl2br($this->config->item('address')); ?></div>
		<div id="company_phone"><?php echo $this->config->item('phone'); ?></div>
		<div id="sale_receipt">Phiếu đặt hàng</div>
		<div id="sale_time"><?php echo $transaction_time ?></div>
	</div>

	<div id="receipt_general_info">
		<?php
		if(isset($supplier))
		{
		?>
			<div id="customer"><?php echo $this->lang->line('suppliers_supplier').": ".$supplier; ?></div>
		<?php
		}
		?>
		<div id="sale_id"><?php echo $this->lang->line('receivings_id').": ".$purchase_id; ?></div>
		<?php 
		if (!empty($reference))
		{
		?>
			<div id="reference"><?php echo $this->lang->line('receivings_reference').": ".$reference; ?></div>	
		<?php 
		}
		?>
		<div id="employee"><?php echo "Người lập phiếu: ".$employee; ?></div>
	</div>

	<table id="receipt_items">
		<tr>
			<th style="width:40%;"><?php echo $this->lang->line('items_item'); ?></th>
			<th style="width:20%;"><?php echo $this->lang->line('common_price'); ?></th>
			<th style="width:20%;"><?php echo $this->lang->line('sales_quantity'); ?></th>
			<th style="width:15%;text-align:right;"><?php echo $this->lang->line('sales_total'); ?></th>
		</tr>

		<?php
		foreach(array_reverse($cart, true) as $line=>$item)
		{
		?>
			<tr>
				<td><?php echo $item['item_name']; ?></td>
				<td><?php echo to_currency($item['item_price']); ?></td>
				<td><?php echo to_quantity_decimals($item['item_quantity']);?>&nbsp;&nbsp;&nbsp;</td>
				<td><div class="total-value"><?php echo to_currency($item['total']); ?></div></td>
			</tr>
		<?php
		}
		?>	
		<tr>
			<td colspan="3" style='text-align:right;border-top:2px solid #000000;'><?php echo $this->lang->line('sales_total'); ?></td>
			<td style='border-top:2px solid #000000;'><div class="total-value"><?php echo to_currency($total); ?></div></td>
		</tr>
		
	</table>

	<div id="sale_return_policy">
		<?php echo nl2br($this->config->item('return_policy')); ?>
	</div>

	<div id='barcode'>
		<img src='data:image/png;base64,<?php echo $barcode; ?>' /><br>
		<?php echo $code; ?>
	</div>
</div>
<script type="text/javascript">
$(document).ready(function()
{

    $("#send_button").click(function()
    {	
			$('#action_form').attr('action', '<?php echo site_url($controller_name . "/send"); ?>');
			$('#action_form').submit();
		
    });
	$("#approve_button").click(function()
    {	
		$('#action_form').attr('action', '<?php echo site_url($controller_name . "/approve"); ?>');
		$('#action_form').submit();
		
    });

	$("#cancel_button").click(function()
    {	
		$('#action_form').attr('action', '<?php echo site_url($controller_name . "/cancel"); ?>');
		$('#action_form').submit();
		
    });
	

	$("#import_button").click(function()
    {	
		$('#action_form').attr('action', '<?php echo site_url($controller_name . "/import"); ?>');
		$('#action_form').submit();
		
    });

	$("#edit_button").click(function()
    {	
		$('#action_form').attr('action', '<?php echo site_url($controller_name . "/editpurchase"); ?>');
		$('#action_form').attr('method', 'get');
		$('#action_form').submit();
		
    });

	$('#barcode_button').click(function()
    {	
		$('#action_form').attr('action', '<?php echo site_url($controller_name . "/printbarcode"); ?>');
		$('#action_form').attr('method', 'post');
		$('#action_form').submit();
		
    });

	$("#excel_export_button").click(function()
    {	
		var purchase_uuid = $('#purchase_uuid').val();
		$.ajax({
			type: 'GET',
			//url: '<?php echo site_url($controller_name . "/export/purchase_uuid/"); ?>'+purchase_uuid,
			url: '<?php echo site_url($controller_name . "/export"); ?>',
			data: { purchase_uuid: purchase_uuid},
			dataType: 'binary',
			success: function(data) {
				var blob = new Blob([data], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
				var url = URL.createObjectURL(blob);
				var a = document.createElement('a');
				a.href = url;
				a.download = 'danh_sach.xlsx';
				document.body.appendChild(a);
				a.click();
				document.body.removeChild(a);
				URL.revokeObjectURL(url);
			}
		});
		//$('#action_form').attr('action', '<?php echo site_url($controller_name . "/export"); ?>');
		//$('#action_form').attr('method', 'get');
		//$('#action_form').submit();
		
    });

	$("#barcode_excel_export_button").click(function()
    {	
		$('#action_form').attr('action', '<?php echo site_url($controller_name . "/barcode_export"); ?>');
		$('#action_form').attr('method', 'get');
		$('#action_form').submit();
		
    });
	

	
	
});



</script>
<?php $this->load->view("partial/footer"); ?>
