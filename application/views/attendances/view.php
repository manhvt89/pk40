<?php $this->load->view("partial/header"); ?>
<script type="text/javascript">
$(document).ready(function()
{
    <?php $this->load->view('partial/bootstrap_tables_locale'); ?>

    var _headers = <?php echo $table_headers; ?>;
    <?php //if(has_grant('is_show_post','oincs')): //can so sánh số lượng hệ thống và số lượng kiểm kê ?>
    $_obt = {
                field: 'post_button',
                title: '...',
                formatter: CheckFormatter,
                events: {
                    'click .view-recipe-btn': redirectToCheckForm,
                },
            };
    _headers.push($_obt);
    <?php //endif; ?>

    table_support.init({
        employee_id: <?php echo $this->Employee->get_logged_in_employee_info()->person_id; ?>,
        resource: '<?php echo site_url($controller_name);?>',
        headers: _headers,
        pageSize: <?php echo $this->config->item('lines_per_page'); ?>,
        uniqueId: 'items.item_id',
        showExport: false,
        showFooter: false,
        pagination: false, 
        onLoadSuccess: function(response) {
            $('a.rollover').imgPreview({
				imgCSS: { width: 200 },
				distanceFromCursor: { top:10, left:-210 }
			})
        }
    });
});
function viewFormatter(value, row, index) {
        console.log(row);
        console.log(value);
        if(row.raw_status == 'O')
        {
            
            return '<button class="btn btn-warning view-recipe-btn btn-sm">Bắt đầu</button>';
        } 
        else if(row.raw_status == 'W')
        {
            return '<button class="btn btn-info view-recipe-btn btn-sm">Sửa</button>';
        }
        else if(row.raw_status == 'P' || row.raw_status == 'C')
        {   
            return '<button class="btn btn-success view-recipe-btn btn-sm">Xem</button>';
        } else {

        }
}


function redirectToForm(event, value, row, index) {
    // Thay đổi đường dẫn theo nhu cầu của bạn
    var url = '';
    if(row.raw_status == 'P' || row.raw_status == 'C')
    {
        url = '<?php echo site_url($controller_name . "/check"); ?>/' + row.uuid; 
    } else {
        url = '<?php echo site_url($controller_name . "/count"); ?>/' + row.uuid; 
    }
    window.location.href = url;
}

function CheckFormatter(value, row, index) {
        console.log(row);
        console.log(value);
        if(row.step == 'I')
        {
            return '<button class="btn btn-success view-recipe-btn btn-sm">' +row.command + '</button>'; 
        } else if(row.step == 'O'){
            return '<button class="btn btn-warning view-recipe-btn btn-sm">' +row.command + '</button>'; 
        }
         else {
            return '';
        }
        //return row.command;
        
}


function redirectToCheckForm(event, value, row, index) {
    // Thay đổi đường dẫn theo nhu cầu của bạn
    if(row.step == 'I')
    {
        var url = '<?php echo site_url($controller_name . "/check_in"); ?>/' + row.employee_id;
    } else {
        var url = '<?php echo site_url($controller_name . "/check_out"); ?>/' + row.attendance_uuid; 
    }
    window.location.href = url;
}
    
function rowStyle(row, index) {
    var classes = [
      'bg-blue',
      'bg-green',
      'bg-orange',
      'bg-yellow',
      'bg-red'
    ]
	console.log(row.style);
	switch (row.style) {
		case '1':
			return {
				css: {
					color: '#000000',
					'background-color':'#228B22'
				}
			}
			break;
		case '2':
			return {
				css: {
					color: '#000000',
					'background-color':'#FFDC00'
				}
			}
			break;	
		case '3':
			return {
				css: {
					color: '#000000',
					'background-color':'#FFDC00'
				}
			}
			break;
		case '4':
			return {
				css: {
					color: '#000000',
					'background-color':'#2ECC40'
				}
			}
			break;
		case '5':
			return {
				css: {
					color: '#000000',
					'background-color':'#0074D9'
				}
			}
			break;
		default:
			return {
				css: {
					color: '#000000'
				}
			}
			break;
	}
  }

</script>
<h1>Chấm công tự động</h1>
<div id="table_holder">
    <table id="table" 
        data-sort-order="desc" 
        data-sort-name="oinc_id"
        data-show-export="false" 
        data-row-style="rowStyle">
    
    </table>
</div>
<?php $this->load->view("partial/footer"); ?>
