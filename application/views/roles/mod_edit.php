<?php $this->load->view("partial/header"); ?>
<script type="text/javascript">
$(document).ready(function()
{

	
});
</script>
<div id="title_bar" class="btn-toolbar print_hide">
    <a href="<?php echo base_url('roles/mod_add') ?>">
        <button class='btn btn-info btn-sm pull-right modal-dlg' title='<?php echo $this->lang->line($controller_name . '_new'); ?>'>
            <span class="glyphicon glyphicon-tag">&nbsp</span>Thêm mới
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
    <?php echo form_open('roles/mod_update/', array('id'=>'mod_form', 'class'=>'form-horizontal'));?>
    <table id="table"
    data-toggle="table"
  data-show-columns="true">
        <thead>
            <tr>
                <td data-field="name"></td>
                <td data-field="value"></td>
            </tr>
        </thead>
        <tbody>
    <tr id="tr-id-1" class="tr-class-1" data-title="bootstrap table" data-object='{"key": "value"}'>
      <td id="td-id-1" class="td-class-1" data-title="bootstrap table">
         Tên mô đun
      </td>
      <td data-value="526">
      <?php echo form_input(array(
					'name'=>'mod_name',
					'id'=>'mod_name',
					'class'=>'form-control input-sm',
					'value'=>$theModule->name)
					);?>  
      <?php echo form_hidden('mod_id', $theModule->id);?>
    </tr>
    <tr id="tr-id-2" class="tr-class-1" data-title="bootstrap table" data-object='{"key": "value"}'>
      <td id="td-id-2" class="td-class-1" data-title="bootstrap table">
         Mã (code)
      </td>
      <td data-value="526"><?=$theModule->code?></td>
    </tr>
    <tr>
      <td id="td-id-2" class="td-class-1" data-title="bootstrap table">
         Module Key
      </td>
      <td data-value="526"><?=$theModule->module_key?></td>
      
    </tr>
    <tr>
      <td id="td-id-2" class="td-class-1" data-title="bootstrap table">
         Thứ tự
      </td>
      <td data-value="526"><?=$theModule->sort?></td>
      
    </tr>
    <tr>
      <td id="td-id-2" class="td-class-1" data-title="bootstrap table">
         UUID
      </td>
      <td data-value="526"><?=$theModule->module_uuid?></td>
      
    </tr>
    <tr>
      <td id="td-id-2" class="td-class-1" data-title="bootstrap table">
         ID
      </td>
      <td data-value="526"><?=$theModule->id?></td>
      
    </tr>
    <tr>
      <td id="td-id-2" class="td-class-1" data-title="bootstrap table">
         
      </td>
      <td data-value="526">
        <div class='col-xs-8'>
          <?php echo form_submit(array(
            'name'=>'mod',
            'id'=>'mod_summit',
            'class'=>'form-control input-sm',
            'value'=>'Cập nhật')
            );?>
        </div>
      </td>
      
    </tr>
  </tbody>



    </table>
    <?php echo form_close(); ?>
</div>

<?php $this->load->view("partial/footer"); ?>
<script type="text/javascript">

//validation and submit handling
$(document).ready(function()
{
	
	$('#mod_form').validate($.extend({
		submitHandler:function(form)
		{
			
		},
		rules:
		{
			mod_name: "required",
			
    		
   		},
		messages: 
		{
            mod_name: "Bạn cần nhập tên mô đun",
     		
		}
	}, form_support.error));
});
</script>