<?php $this->load->view("partial/header"); ?>
<script src="/dist/jquery.number.min.js"></script>

<script type="text/javascript">
$(document).ready(function()
{


    var data = <?=$permissions?>;
    $('#table').bootstrapTable({data: data});

	
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
        <th data-field="module_key">Tên quyền</th>
        <th data-field="permision_key">Actions</th>
        <th data-field="actions">Actions</th>
        </thead>
        
    </table>
</div>

<?php $this->load->view("partial/footer"); ?>
