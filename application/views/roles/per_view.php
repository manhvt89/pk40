<?php $this->load->view("partial/header"); ?>
<script src="/dist/jquery.number.min.js"></script>
<style>
.switch {
  position: relative;
  display: inline-block;
  width: 60px;
  height: 34px;
}

.switch input { 
  opacity: 0;
  width: 0;
  height: 0;
}

.slider {
  position: absolute;
  cursor: pointer;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: #ccc;
  -webkit-transition: .4s;
  transition: .4s;
}

.slider:before {
  position: absolute;
  content: "";
  height: 26px;
  width: 26px;
  left: 4px;
  bottom: 4px;
  background-color: white;
  -webkit-transition: .4s;
  transition: .4s;
}

input:checked + .slider {
  background-color: #2196F3;
}

input:focus + .slider {
  box-shadow: 0 0 1px #2196F3;
}

input:checked + .slider:before {
  -webkit-transform: translateX(26px);
  -ms-transform: translateX(26px);
  transform: translateX(26px);
}

/* Rounded sliders */
.slider.round {
  border-radius: 34px;
}

.slider.round:before {
  border-radius: 50%;
}
</style>
<script type="text/javascript">
$(document).ready(function()
{
    $('#table').bootstrapTable(); // khởi tạo bảng;

    $('.btbswitch').change(function () {

      var mode= $(this).prop('checked');
      var permission_id = $(this).attr('permission-id');
      var permission_key = $(this).attr('permission-key');
      var module_id = $(this).attr('module-id');
      var module_key = $(this).attr('module-key');
      
      $.ajax({
                url: '<?=base_url('roles/switch_action') ?>',
                type: 'POST',
                dataType: 'html',
                data: {
                  mode:mode,
                  permission_id: permission_id,
                  permission_key: permission_key,
                  module_id:module_id,
                  module_key:module_key
                }
            }).done(function(ketqua) {
                //$('#noidung').html(ketqua);
                //alert(ketqua);
            });
      });
});

function save_name(ele)
{
  if(event.key === 'Enter') {
      var data_id = $(ele).attr('data_id');
      if(ele.value == '')
      {
        alert('Vui lòng nhập tên');
        console.log($(ele).attr('data_id'));
      } else {
        $.ajax({
                url: '<?=base_url('roles/save_permission_name') ?>',
                type: 'POST',
                dataType: 'html',
                data: {
                    name: $('#name_'+data_id).val(),
                    permission_key: $('#permission_key_'+data_id).val(),
                    id:$('#permission_id_'+data_id).val(),
                    'module_key':$('#module_key_'+data_id).val()
                }
            }).done(function(ketqua) {
                //$('#noidung').html(ketqua);
                alert(ketqua);
            });
      }       
  }
}
</script>

<div id="title_bar" class="btn-toolbar print_hide">
    <a href="<?php echo base_url('roles/field_add/'.$thePermission->permissions_uuid) ?>">
        <button class='btn btn-info btn-sm pull-right modal-dlg' title='<?php echo $this->lang->line($controller_name . '_new'); ?>'>
            <span class="glyphicon glyphicon-tag">&nbsp</span><?php echo $this->lang->line($controller_name. '_mod_new'); ?>
        </button>
    </a> &nbsp; &nbsp; &nbsp; <a href="<?php echo base_url('roles/mod_index') ?>">
        <button class='btn btn-info btn-sm pull-right modal-dlg' title='<?php echo $this->lang->line($controller_name . '_new'); ?>'>
            <span class="glyphicon glyphicon-tag">&nbsp</span>Danh sách mô đun
        </button>
    </a>
</div>

<fieldset id="supplier_basic_info">
  <div class="form-horizontal">
    <div class="form-group form-group-sm">	
			
      <div class='col-xs-3'>Tên quyền:</div>
			<div class='col-xs-8'>
          <?php echo $thePermission->name;?>
			</div>
		</div>
		<div class="form-group form-group-sm">	
	
      <div class='col-xs-3'>Permission Key:</div>
			<div class='col-xs-8'>
          <?=$thePermission->permission_key?>
			</div>
		</div>

    <div class="form-group form-group-sm">	

      
		</div>

    <div class="form-group form-group-sm">	
			
      <div class='col-xs-3'>UUID:</div>
			<div class='col-xs-8'>
          <?=$thePermission->permissions_uuid?>
			</div>
		</div>

    <div class="form-group form-group-sm">	
			
      <div class='col-xs-3'>ID:</div>
			<div class='col-xs-8'>
          <?=$thePermission->id?>
			</div>
		</div>    
	</fieldset>
</div>
<div id="table_holder">
    <table id="table" data-sort-order="desc" data-sort-name="item_number" data-search="true">
        <thead>
        <th data-field="stt">STT</th>
        <th data-field="name">Tên trường</th>
        <th data-field="module_key">key của trường</th>
        <th data-field="permision_key">Quyền (ẩn, đọc, chỉnh sửa)</th>
        <th data-field="actions">Actions</th>
        </thead>
        <tbody>
          <?php if(!empty($fields)): ?>
            <?php $i=1; foreach($fields as $field): ?>

            <tr>
              <td><?=$i?></td>
              <td><input class="mod_name" data_id="<?=$permission->permission_key?>" name="name_<?=$permission->permission_key?>" id="name_<?=$permission->permission_key?>" value="<?=$permission->name?>" onkeydown="save_name(this)"/></td>
              <td><?=$permission->module_key?></td>
              <td> <input type="hidden" name="permission_key_<?=$permission->permission_key?>" id="permission_key_<?=$permission->permission_key?>" value="<?=$permission->permission_key?>" />
              <input type="hidden" name="permission_id_<?=$permission->permission_key?>" id="permission_id_<?=$permission->permission_key?>" value="<?=$permission->id?>" />
              <input type="hidden" name="module_key_<?=$permission->permission_key?>" id="module_key_<?=$permission->permission_key?>" value="<?=$permission->module_key?>" />
                <?=$permission->permission_key?></td>
              <td>
                <label class="switch">
                  <input type="checkbox" class="btbswitch" permission-key="<?=$permission->permission_key?>" module-id="<?=$permission->module_id?>" module-key="<?=$permission->module_key?>" permission-id="<?=$permission->id?>" <?=$permission->flag==1?'checked':''?> id="flag_<?=$permission->permission_key?>" name="flag_<?=$permission->permission_key?>" value="1">
                  <span class="slider round"></span>
                </label>
              </td>
            </tr>
            <?php $i++; endforeach; ?>
          <?php endif; ?>
        </tbody>
    </table>
</div>

<?php $this->load->view("partial/footer"); ?>
