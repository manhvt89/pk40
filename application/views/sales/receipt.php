<?php $this->load->view("partial/header"); ?>

<?php
if (isset($error_message))
{
	echo "<div class='alert alert-dismissible alert-danger'>".$error_message."</div>";
	exit;
}
?>

<?php if(!empty($customer_email)): ?>
<script type="text/javascript">
$(document).ready(function()
{
	var send_email = function()
	{
		$.get('<?php echo site_url() . "/sales/send_receipt/" . $sale_id_num; ?>',
			function(response)
			{
				$.notify(response.message, { type: response.success ? 'success' : 'danger'} );
			}, 'json'
		);
	};

	$("#show_email_button").click(send_email);

	<?php if(!empty($email_receipt)): ?>
		send_email();
	<?php endif; ?>
});
</script>
<style type="text/css">
	.name {
        font-size: 20px;
    }
    .time {
        font-size: 15px;
    }
    .customer_number,
    .phone {
        font-size: 16px;
    }
    #receipt_items {
        font-size: 16px;
    }
    #receipt_items thead th:not(:first-child) {
        display: none;
    }
    #receipt_items tbody th {
        font-weight: normal;
    }
    #receipt_items td:not(:last-child) {
        display: none;
    }
    td,
    th {
        display: block;
    }
    td[data-th]:before {
        content: attr(data-th);
    }
</style>
<?php endif; ?>
<style type="text/css">
	.name {
        font-size: 20px;
    }
    .time {
        font-size: 15px;
    }
    .customer_number,
    .phone {
        font-size: 14px;
    }
    #receipt_items {
        font-size: 16px;
    }
    #receipt_items thead th:not(:first-child) {
        display: none;
    }
    #receipt_items tbody th {
        font-weight: normal;
    }
    #receipt_items td:not(:last-child) {
        display: none;
    }
    td,
    th {
        display: block;
    }
    td[data-th]:before {
        content: attr(data-th);
    }
</style>

<?php $this->load->view('partial/print_receipt', array('print_after_sale'=>$print_after_sale, 'selected_printer'=>'receipt_printer')); ?>
	<?php $this->load->view('partial/print_receipt_css'); ?>
<div class="print_hide" id="control_buttons" style="text-align:right">
	<a href="javascript:printdoc();"><div class="btn btn-info btn-sm", id="show_print_button"><?php echo '<span class="glyphicon glyphicon-print">&nbsp</span>' . $this->lang->line('common_print'); ?></div></a>
	<?php /* this line will allow to print and go back to sales automatically.... echo anchor("sales", '<span class="glyphicon glyphicon-print">&nbsp</span>' . $this->lang->line('common_print'), array('class'=>'btn btn-info btn-sm', 'id'=>'show_print_button', 'onclick'=>'window.print();')); */ ?>
	<?php if($status == 1){
        echo anchor($controller_name."/payment/$sale_uuid", '<span class="glyphicon glyphicon-briefcase">&nbsp</span>' . 'Thanh toán',
        array('class'=>'btn btn-info btn-sm', 'id'=>'sales_pending_button', 'title'=>'Thanh toán'));
    } 
    ?>	
    <?php if(isset($customer_email) && !empty($customer_email)): ?>
		<a href="javascript:void(0);"><div class="btn btn-info btn-sm", id="show_email_button"><?php echo '<span class="glyphicon glyphicon-envelope">&nbsp</span>' . $this->lang->line('sales_send_receipt'); ?></div></a>
	<?php endif; ?>
	<?php echo anchor("sales", '<span class="glyphicon glyphicon-shopping-cart">&nbsp</span>' . $this->lang->line('sales_register'), array('class'=>'btn btn-info btn-sm', 'id'=>'show_sales_button')); ?>
	
	<?php echo anchor($controller_name."/pending", '<span class="glyphicon glyphicon-list-alt">&nbsp</span>' . 'Danh sách chờ thanh toán',
							array('class'=>'btn btn-info btn-sm', 'id'=>'sales_pending_button', 'title'=>'Danh sách chờ thanh toán')); ?>
					
			
    <?php //echo anchor("sales/manage", '<span class="glyphicon glyphicon-list-alt">&nbsp</span>' . $this->lang->line('sales_takings'), array('class'=>'btn btn-info btn-sm', 'id'=>'show_takings_button')); ?>
</div>
<?php if($sale_uuid != '' ): ?>
<?php $this->load->view("sales/" . $this->config->item('receipt_template')); ?>
<?php endif;?>

<?php $this->load->view("partial/footer"); ?>