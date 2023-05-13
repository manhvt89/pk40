<?php $this->load->view("partial/header"); ?>

<?php
if (isset($error_message))
{
	echo "<div class='alert alert-dismissible alert-danger'>".$error_message."</div>";
	exit;
}
?>


<?php $this->load->view('partial/print_receipt', array('print_after_sale'=>$print_after_sale, 'selected_printer'=>'receipt_printer')); ?>
	<?php $this->load->view('partial/print_receipt_css'); ?>
<div class="print_hide" id="control_buttons" style="text-align:right">
	<a href="javascript:printdoc();"><div class="btn btn-info btn-sm", id="show_print_button"><?php echo '<span class="glyphicon glyphicon-print">&nbsp</span>' . $this->lang->line('common_print'); ?></div></a>
	<?php /* this line will allow to print and go back to sales automatically.... echo anchor("sales", '<span class="glyphicon glyphicon-print">&nbsp</span>' . $this->lang->line('common_print'), array('class'=>'btn btn-info btn-sm', 'id'=>'show_print_button', 'onclick'=>'window.print();')); */ ?>
	<?php echo anchor("order", '<span class="glyphicon glyphicon-shopping-cart">&nbsp</span>' . $this->lang->line('create_new_order_register_button'), array('class'=>'btn btn-info btn-sm', 'id'=>'show_sales_button')); ?>
	<?php echo anchor("order/manage", '<span class="glyphicon glyphicon-list-alt">&nbsp</span>' . $this->lang->line('order_takings'), array('class'=>'btn btn-info btn-sm', 'id'=>'show_takings_button')); ?>
</div>

<?php $this->load->view("orders/" . $this->config->item('receipt_template')); ?>

<?php $this->load->view("partial/footer"); ?>