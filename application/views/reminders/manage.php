<?php $this->load->view("partial/header"); ?>

<script type="text/javascript">
$(document).ready(function()
{
	<?php $this->load->view('partial/bootstrap_tables_locale'); ?>

	table_support.init({
		resource: '<?php echo site_url($controller_name);?>',
		headers: <?php echo $table_headers; ?>,
		pageSize: <?php echo $this->config->item('lines_per_page'); ?>,
		uniqueId: 'id',
		enableActions: function()
		{
			var email_disabled = $("td input:checkbox:checked").parents("tr").find("td a[href^='mailto:']").length == 0;
			$("#email").prop('disabled', email_disabled);
		}
	});

	$("#email").click(function(evvent)
	{
		var recipients = $.map($("tr.selected a[href^='mailto:']"), function(element)
		{
			return $(element).attr('href').replace(/^mailto:/, '');
		});
		location.href = "mailto:" + recipients.join(",");
	});

	

});
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
					'background-color':'#FF851B'
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
					color: 'red'
				}
			}
			break;
	}
  }
</script>
<div id="table_holder">
	<table id="table" data-search="true" data-row-style="rowStyle"></table>
</div>

<?php $this->load->view("partial/footer"); ?>
