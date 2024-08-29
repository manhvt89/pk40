<?php $this->load->view("partial/header"); ?>

<script type="text/javascript">
$(document).ready(function()
{
	// when any filter is clicked and the dropdown window is closed
	$('#filters').on('hidden.bs.select', function(e) {
		table_support.refresh();
	});
	
	// load the preset datarange picker
	<?php $this->load->view('partial/daterangepicker'); ?>

	$("#daterangepicker").on('apply.daterangepicker', function(ev, picker) {
		table_support.refresh();
	});

	<?php $this->load->view('partial/bootstrap_tables_locale'); ?>

	var _headers = <?php echo $table_headers; ?>;
    <?php if(true): //Phân quyền kiểm kê ?>
		var $_obt = {
			field: 'audio_read_button',
			title: '...',
			formatter: audioFormatter,
			events: {
				'click .play_audio_btn': function (e, value, row, index) {
					console.log('Button clicked!');
					playAudio(e, value, row, index);
				}, // Định nghĩa sự kiện click cho nút "Play"
			},
		};

		_headers.push($_obt); // Thêm cột vào tiêu đề bảng
    <?php endif; ?>

	table_support.init({
		resource: '<?php echo site_url($controller_name);?>',
		headers: _headers,
		pageSize: <?php echo $this->config->item('lines_per_page'); ?>,
		uniqueId: 'test_id',
		onLoadSuccess: function(response) {
			if($("#table tbody tr").length > 1) {
				$("#table tbody tr:last td:first").html("");
			}
			console.log("Table loaded successfully!");

			setTimeout(function() {
				console.log($('.play_audio_btn').length + " Play buttons found.");
				
				$('.play_audio_btn').off('click').on('click', function(event) {
					var index = $(this).closest('tr').data('index');
					var row = $('#table').bootstrapTable('getData')[index];
					playAudio(event, null, row, index);
				});
			}, 100); // Đợi 100ms để đảm bảo các nút đã được thêm vào DOM
		},
		queryParams: function() {
			return $.extend(arguments[0], {
				start_date: start_date,
				end_date: end_date,
				filters: $("#filters").val() || [""]
			});
		},
		columns: {
			'test_time':{
				'vertical-align': 'middle'
			},
			'customer_name':{
				align:'right'
			}
		}
	});
});

function audioFormatter(value, row, index) {
        console.log(row);
        console.log(value);
			
		
		var $return = '<button class="btn play_audio_btn btn-sm">Play</button>';
		return $return;
           
        //$return = '<button class="btn play_audio_btn btn-sm">Play';
		//$return = $return +	'<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-megaphone" viewBox="0 0 16 16">';
		//$return = $return +	'<path d="M13 2.5a1.5 1.5 0 0 1 3 0v11a1.5 1.5 0 0 1-3 0v-.214c-2.162-1.241-4.49-1.843-6.912-2.083l.405 2.712A1 1 0 0 1 5.51 15.1h-.548a1 1 0 0 1-.916-.599l-1.85-3.49-.202-.003A2.014 2.014 0 0 1 0 9V7a2.02 2.02 0 0 1 1.992-2.013 75 75 0 0 0 2.483-.075c3.043-.154 6.148-.849 8.525-2.199zm1 0v11a.5.5 0 0 0 1 0v-11a.5.5 0 0 0-1 0m-1 1.35c-2.344 1.205-5.209 1.842-8 2.033v4.233q.27.015.537.036c2.568.189 5.093.744 7.463 1.993zm-9 6.215v-4.13a95 95 0 0 1-1.992.052A1.02 1.02 0 0 0 1 7v2c0 .55.448 1.002 1.006 1.009A61 61 0 0 1 4 10.065m-.657.975 1.609 3.037.01.024h.548l-.002-.014-.443-2.966a68 68 0 0 0-1.722-.082z"/>';
		//$return = $return +	'</svg></button>';
		//$return = $return +	'</button>';
		//return $return;
        
}


async function playAudio(event, value, row, index) {
   
    console.log(row);
    console.log(index);
	if(row.read2speatch == '')
	{
	} else {
	text = row.read2speatch;
	}
	const response = await fetch('<?= base_url('tts/synthesize') ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: new URLSearchParams({ text: text })
	});

	if (response.ok) {
		const audioBlob = await response.blob();
		const audioUrl = URL.createObjectURL(audioBlob);
		const audioElement = document.getElementById('audio-output');
		audioElement.src = audioUrl;
		audioElement.play();
	} else {
		console.error('Có lỗi xảy ra khi gọi API Text-to-Speech');
	}
}
</script>

<?php $this->load->view('partial/print_receipt', array('print_after_sale'=>false, 'selected_printer'=>'takings_printer')); ?>

<div id="title_bar" class="print_hide btn-toolbar">
	<button onclick="javascript:printdoc()" class='btn btn-info btn-sm pull-right'>
		<span class="glyphicon glyphicon-print">&nbsp</span><?php echo $this->lang->line('common_print'); ?>
	</button>
	<?php if($is_create){ echo anchor("test", '<span class="glyphicon glyphicon-shopping-cart">&nbsp</span>' . $this->lang->line('test_new'), array('class'=>'btn btn-info btn-sm pull-right', 'id'=>'show_sales_button'));} ?>
</div>

<div id="toolbar">
	<div class="pull-left form-inline" role="toolbar">
		<!--
		<button id="delete" class="btn btn-default btn-sm print_hide">
			<span class="glyphicon glyphicon-trash">&nbsp</span><?php echo $this->lang->line("common_delete");?>
		</button>
		-->
		<?php echo form_input(array('name'=>'daterangepicker', 'class'=>'form-control input-sm', 'id'=>'daterangepicker')); ?>
		<?php if($filters != null): ?>
		<?php echo form_multiselect('filters[]', $filters, '', array('id'=>'filters', 'data-none-selected-text'=>$this->lang->line('common_none_selected_text'), 'class'=>'selectpicker show-menu-arrow', 'data-selected-text-format'=>'count > 1', 'data-style'=>'btn-default btn-sm', 'data-width'=>'fit')); ?>
		<?php endif; ?>
	</div>
</div>

<div id="table_holder">
	<table id="table" data-sort-order="desc" data-sort-name="test_time"></table>
	<audio id="audio-output" style="display: none;"></audio>
</div>

<?php $this->load->view("partial/footer"); ?>
