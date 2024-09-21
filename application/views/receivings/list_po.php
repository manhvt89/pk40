<style>
    @media (min-width: 768px) {
        .modal-dlg .modal-dialog {
            width: 800px;
        }
    }
</style>

<div id="note_message"><?php echo $this->lang->line('receivings_note_message'); ?></div>

<table id="list_po_modal" class="list_po_modal" style="width: 100%;">
    <thead>
        <th>STT</th>
        <th>Mã PO</th>
        <th>Ngày</th>
        <th>Lựa chọn</th>
    </thead>
    <tbody>
        <?php if(count($aPO)): $i = 0; ?>
            <?php foreach($aPO as $item): $i++; ?>
            <tr>
                <td><?=$i ?></td>
                <td><?=$item->name ?></td>
                <td><?=$item->purchase_time ?></td>
                <td>
                    <button 
						class="btn-select-po" 
						data-id="<?=$item->purchase_uuid?>"
						data-url="<?=site_url('receivings/loadPO')?>"
						data-redirect_url="<?=site_url('receivings')?>"
						>Chọn để nhập kho</button>
                </td>
            </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="4">
                    Hiện tại chưa có PO nào cần nhập hàng.
                </td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<script type="text/javascript">
$(document).ready(function() {
    // Thêm sự kiện click cho nút 'Chọn để nhập kho'
    $('.btn-select-po').on('click', function() {
        var poUUId = $(this).data('id'); // Lấy ID PO từ thuộc tính data-id
		var url = $(this).data('url');
		var redirect_url = $(this).data('redirect_url');
		
		var csrfName = '<?=$this->security->get_csrf_token_name() ?>';
		var csrfHash = '<?=$this->security->get_csrf_hash(); ?>';
        // Gửi request để lưu PO vào session
        $.ajax({
            url: url,  // URL tới controller xử lý lưu PO vào session
            method: 'POST',
			dataType: 'json', 
            data: { 
				uuid: poUUId,
				[csrfName]: csrfHash},
            success: function(response) {
				console.log(response);
                if (response.success) {
                    // Chuyển hướng về trang nhập kho sau khi lưu thành công
                    window.location.href = redirect_url;
                } else {
                    alert('Có lỗi xảy ra khi lưu PO. Vui lòng thử lại.');
                }
            },
            error: function() {
                alert('Lỗi kết nối, vui lòng kiểm tra lại.');
            }
        });
    });

	$('#close').on('click',function(){
		$('.modal').modal('hide');
	});
});
</script>
