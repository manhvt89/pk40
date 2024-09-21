<style>
	#loadingOverlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5); /* Nền tối mờ */
        z-index: 9999; /* Đảm bảo overlay luôn nằm trên cùng */
        display: flex;
        justify-content: center;
        align-items: center;
    }
    .loadingMessage {
        font-size: 20px;
        color: white;
        background-color: rgba(0, 0, 0, 0.7);
        padding: 20px;
        border-radius: 5px;
    }
	.list_po_modal{

	}
	/* Đặt chiều rộng bảng và căn giữa */
	#list_po_modal {
            width: 100%;
            border-collapse: collapse; /* Bỏ khoảng trống giữa các ô */
            margin: 20px 0;
            font-family: Arial, sans-serif;
            box-shadow: 0 2px 3px rgba(0, 0, 0, 0.1); /* Đổ bóng nhẹ */
        }

        /* Style cho header của bảng */
        #list_po_modal thead th {
            background-color: #33A6E7; /* Màu nền */
            color: #ffffff; /* Màu chữ */
            text-align: left; /* Canh trái */
            padding: 12px 15px; /* Khoảng đệm trong */
            font-size: 16px;
        }

        /* Style cho các hàng trong bảng */
        #list_po_modal tbody td {
            padding: 10px 15px; /* Khoảng đệm trong */
            border-bottom: 1px solid #dddddd; /* Đường viền */
            font-size: 14px;
        }

        /* Thêm hiệu ứng hover khi rê chuột lên hàng */
        #list_po_modal tbody tr:hover {
            background-color: #f2f2f2; /* Màu nền khi hover */
        }

        /* Định dạng các nút */
        .btn-select-po {
            background-color: #33A6E7;
            border: none;
            color: white;
            padding: 10px 20px;
            text-align: center;
            font-size: 14px;
            cursor: pointer;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        /* Hiệu ứng hover cho nút */
        .btn-select-po:hover {
            background-color: #1c8ac2;
        }

        /* Tạo khoảng cách giữa các nút */
        .btn-select-po:not(:last-child) {
            margin-right: 10px;
        }

        /* Responsive cho thiết bị nhỏ */
        @media screen and (max-width: 600px) {
            #list_po_modal thead {
                display: none; /* Ẩn header trên thiết bị nhỏ */
            }

            #list_po_modal, #list_po_modal tbody, #list_po_modal tr, #list_po_modal td {
                display: block;
                width: 100%;
            }

            #list_po_modal tr {
                margin-bottom: 15px;
            }

            #list_po_modal tbody td {
                text-align: right;
                padding-left: 50%;
                position: relative;
            }

            #list_po_modal tbody td:before {
                content: attr(data-label);
                position: absolute;
                left: 0;
                width: 50%;
                padding-left: 15px;
                font-weight: bold;
                text-align: left;
            }
        }
    @media (min-width: 768px) {
        .modal-dlg .modal-dialog {
            width: 800px;
        }
    }
</style>

<!-- Overlay để chặn thao tác và hiển thị thông báo -->
<div id="loadingOverlay" style="display: none;">
    <div class="loadingMessage">Đang load PO...</div>
</div>
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
                <td><?=$item->code ?></td>
                <td><?=date('d/m/Y H:i:s',strtotime($item->purchase_time)) ?></td>
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

		$('#loadingOverlay').show();
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
					$('#loadingOverlay').hide();
                }
            },
            error: function() {
                alert('Lỗi kết nối, vui lòng kiểm tra lại.');
				$('#loadingOverlay').hide();
            }
        });
    });

	$('#close').on('click',function(){
		$('.modal').modal('hide');
	});
});
</script>
