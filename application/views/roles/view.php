<?php $this->load->view("partial/header"); ?>

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
   
    $('#table').bootstrapTable();

    $('.btbswitch').change(function () {

      var mode= $(this).prop('checked');
      var permission_id = $(this).attr('permission-id');
      var role_id = $('#module_id').val();
      $.ajax({
                url: '<?=base_url('roles/switch_permission') ?>',
                type: 'POST',
                dataType: 'html',
                data: {
                  mode:mode,
                  permission_id: permission_id,
                  role_id:role_id
                }
            }).done(function(ketqua) {
                //$('#noidung').html(ketqua);
                //alert(ketqua);
            });
    });
  
});
</script>

<div id="title_bar" class="btn-toolbar print_hide">
    <button class='btn btn-info btn-sm pull-right modal-dlg' data-btn-new='<?php echo $this->lang->line('common_new') ?>' data-btn-submit='<?php echo $this->lang->line('common_submit') ?>' data-href='<?php echo site_url($controller_name."/view"); ?>'
            title='<?php echo $this->lang->line($controller_name . '_new'); ?>'>
        <span class="glyphicon glyphicon-tag">&nbsp</span><?php echo $this->lang->line($controller_name. '_new'); ?>
    </button>
</div>

<div id="toolbar">
   
</div>

<div id="table_holder">
    <table>
        <thead>
            <tr>
                <td data-field="name"></td>
                <td data-field="value"></td>
            </tr>
        </thead>
        <tbody>
    <tr>
      <td >
         Tên nhóm quyền: 
      </td>
      <td data-value="526"><?=$theRole->name?></td>
    </tr>
    <tr >
      <td >
         Mã (code)
      </td>
      <td data-value="526">
        <input type="hidden" id="module_id" value="<?=$theRole->id?>" />
        <?=$theRole->code?>
      </td>
    </tr>
  </tbody>
    </table>
    <table id="table" data-toggle="table" data-show-columns="true">
            <thead>
                <tr>
                    <th>STT</th>   
                    <th>Mô đun key</th>
                    <th>Tên quyền</th>
                    <th>Task</th>
                    <th>...</th>
                </tr>
            </thead>
            <tbody>
                <?php if(empty($permissions)): ?>
                    <tr><td colspan="3">Chưa được cấp quyền nào</td></tr>
                <?php  else : ?>
                  <?php $i =1; foreach($permissions as $permission): ?>
                    <tr>
                      <td><?=$i?></td>
                      <td><?=$permission->module_key?></td>
                      <td><?=$permission->name?></td>                      
                      <td><?=$permission->permission_key?>
                            <input type="hidden" id="permission_key_<?=$permission->id?>" value="<?=$permission->id?>" />
                            
                    </td>
                      <td>
                      <label class="switch">
                  <input class="btbswitch" type="checkbox" permission-id="<?=$permission->id?>" <?=$permission->flag==1?'checked':''?> id="flag_<?=$permission->permission_key?>" name="flag_<?=$permission->permission_key?>">
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
