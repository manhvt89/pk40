<?php $this->load->view("partial/header"); ?>
<script src="/dist/jquery.number.min.js"></script>

<script type="text/javascript">
$(document).ready(function()
{


    var data = <?=$roles?>;
    $('#table').bootstrapTable({data: data});

	
});
</script>
<div id="title_bar" class="btn-toolbar print_hide">
    <button class='btn btn-info btn-sm pull-right modal-dlg' data-btn-new='<?php echo $this->lang->line('common_new') ?>' data-btn-submit='<?php echo $this->lang->line('common_submit') ?>' data-href='<?php echo site_url($controller_name."/view"); ?>'
            title='<?php echo $this->lang->line($controller_name . '_new'); ?>'>
        <span class="glyphicon glyphicon-tag">&nbsp</span><?php echo $this->lang->line($controller_name. '_new'); ?>
    </button>
    </a> &nbsp; &nbsp; &nbsp; <a href="<?php echo base_url('roles/mod_index') ?>">
        <button class='btn btn-info btn-sm pull-right modal-dlg' title='<?php echo $this->lang->line($controller_name . '_new'); ?>'>
            <span class="glyphicon glyphicon-tag">&nbsp</span>Danh sách mô đun
        </button>
    </a>
</div>

<div id="toolbar">
   
</div>

<div id="table_holder">
    <table id="table" data-sort-order="desc" data-sort-name="item_number" data-search="true">
        <thead>
        <th data-field="stt">STT</th>
      <th data-field="name">Tên phân quyền</th>
      <th data-field="actions">Actions</th>
        </thead>
        
    </table>
</div>

<?php $this->load->view("partial/footer"); ?>
