<?php $this->load->view("partial/header"); ?>

<?php
if(isset($error))
{
	echo "<div class='alert alert-dismissible alert-danger'>".$error."</div>";
}
?>

<div class="row">
	<div class="col-md-3">
		<div class="panel panel-primary">
		  	<div class="panel-heading">
				<h3 class="panel-title"><span class="glyphicon glyphicon-list-alt">&nbsp</span><?php echo $this->lang->line('reports_detailed_reports'); ?></h3>
		  	</div>
			<div class="list-group">
				<?php 			
				$person_id = $this->session->userdata('person_id');
				show_report_if_allowed('detailed', 'sales', $person_id);
				?>
				<a class="list-group-item" href="<?php echo site_url('reports/sale_by_product');?>"><?php echo 'Bán hàng theo sản phẩm'; ?></a>
				<?php
				show_report_if_allowed('detailed', 'receivings', $person_id);
				//show_report_if_allowed('specific', 'customer', $person_id, 'reports_customers');
				//show_report_if_allowed('specific', 'discount', $person_id, 'reports_discounts');
				show_report_if_allowed('specific', 'employee', $person_id, 'reports_employees');
				show_report_if_allowed('specific', 'ctvs', $person_id, 'reports_ctvs');
				$bUser_type = $this->session->userdata('type');
				?>
				<a class="list-group-item" href="<?php echo site_url('reports/partner');?>"><?php echo 'Doanh thu theo CTV'; ?></a>
				<a class="list-group-item" href="<?php echo site_url('reports/customer_care');?>"><?php echo 'Kết quả chăm sóc khác hàng'; ?></a>
				<a class="list-group-item" href="<?php echo site_url('reports/cosoone');?>"><?php echo 'Xuất nội bộ'; ?></a>
				<?php  if( $bUser_type == 2): ?>
				<a class="list-group-item" href="/reports/graphical_summary_sales">Báo cáo hình ảnh</a>
				<?php endif; ?>
			 </div>
		</div>
	</div>
	<div class="col-md-3">
		<?php
		if ($this->Employee->has_grant('reports_inventory'))
		{
		?>
			<div class="panel panel-primary">
				<div class="panel-heading">
					<h3 class="panel-title"><span class="glyphicon glyphicon-book">&nbsp</span><?php echo $this->lang->line('reports_inventory_reports'); ?></h3>
				</div>
				<div class="list-group">
				<?php 
					//show_report('', 'reports_inventory_low');
					//show_report('', 'reports_inventory_summary');
					show_report('', 'reports_inventory_lens');
					//show_report('', 'reports_inventory_import_lens');
				//Contact Lens
					//show_report('', 'reports_inventory_contact_lens');
					//show_report('', 'reports_inventory_import_contact_lens');
				// FRAME
					//show_report('', 'reports_inventory_frame');
					//show_report('', 'reports_inventory_sun_glasses');
			
					//show_report('', 'reports_inventory_detail_lens');
				?>
				<a class="list-group-item" href="<?php echo site_url('reports/inventory_thuoc');?>"><?php echo 'Báo cáo Thuốc'; ?></a>
				<a class="list-group-item" href="<?php echo site_url('reports/inventory_frame');?>"><?php echo 'Báo cáo Gọng Kính'; ?></a>
				<a class="list-group-item" href="<?php echo site_url('reports/inventory_sun_glasses');?>"><?php echo 'Báo cáo Kính Mát'; ?></a>
				<a class="list-group-item" href="<?php echo site_url('reports/inventory_detail_lens');?>"><?php echo 'Báo cáo chi tiết Mắt Kính'; ?></a>
				<a class="list-group-item" href="<?php echo site_url('reports/inventory_contact_lens');?>"><?php echo 'Báo cáo Áp Tròng'; ?></a>
				</div>
			</div>
		<?php 
		}
		?>
	</div>
	<div class="col-md-3">
		<?php
		if ($this->Employee->has_grant('reports_inventory'))
		{
			?>
			<div class="panel panel-primary">
				<div class="panel-heading">
					<h3 class="panel-title"><span class="glyphicon glyphicon-book">&nbsp</span><?php echo $this->lang->line('reports_import_reports'); ?></h3>
				</div>
				<div class="list-group">
					<?php
					show_report('', 'reports_inventory_import_lens');
					//Contact Lens
					//show_report('', 'reports_inventory_import_contact_lens');
					// FRAME
					?>
				</div>
			</div>
			<?php
		}
		?>
	</div>
	<div class="col-md-3">
		<?php
		if ($this->Employee->has_grant('reports_inventory', $this->session->userdata('person_id')))
		{
			?>
			<div class="panel panel-primary">
				<div class="panel-heading">
					<h3 class="panel-title"><span class="glyphicon glyphicon-book">&nbsp</span><?php echo $this->lang->line('reports_import_lens_reports'); ?></h3>
				</div>
				<div class="list-group">
					<?php
					show_report('', 'reports_detail_import_lens');
					//Contact Lens
					//show_report('', 'reports_inventory_import_contact_lens');
					// FRAME
					?>
				</div>
			</div>
			<?php
		}
		?>
	</div>
</div>

<?php $this->load->view("partial/footer"); ?>