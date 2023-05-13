<?php $this->load->view("partial/header"); ?>

<script type="text/javascript">
$(document).ready(function()
{


   
    $('#table').bootstrapTable();

	
});
</script>

<div id="title_bar" class="btn-toolbar print_hide">
    <a href="<?php echo base_url('roles/per_add'); ?>">
        <button class='btn btn-info btn-sm pull-right' data-btn-new='<?php echo $this->lang->line('common_new') ?>' title='<?php echo $this->lang->line($controller_name . '_new'); ?>'>
            <span class="glyphicon glyphicon-tag">&nbsp</span><?php echo $this->lang->line($controller_name. '_new'); ?>
        </button>
    </a>
</div>

<div id="toolbar">
   
</div>

<div id="table_holder">
    <table id="table" data-sort-order="desc" data-sort-name="item_number" data-search="true">
        <thead>
        <th data-field="stt">STT</th>
        <th data-field="name">Tên quyền</th>
        <th data-field="module_name">Tên Mô đun</th>
		<th data-field="module_key">Mô đun Key</th>
        <th data-field="permision_key">Actions Key</th>
        <th data-field="actions"></th>
        </thead>
		<tbody>
			<?php $i = 1; if(count($modules) > 0): ?>
				<?php foreach($modules as $module): ?>
				<tr>
					<td><?=$i?></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
				</tr>
				<?php $i++; endforeach; ?>
			<?php else: ?>
				<tr><td colspan="6"> Chưa có thông tin nào</td></tr>
			<?php endif;?>
		</tbody>
        
    </table>
</div>
<?php $this->load->view("partial/footer"); ?>