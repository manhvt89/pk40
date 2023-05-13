<script type="text/javascript">
function printdoc()
{

    // receipt layout sanity check
	if ( $("#receipt_items, #items, #table_holder, #principle_print").length > 0)
	{
		// install firefox addon in order to use this plugin
		if (window.jsPrintSetup) 
		{
		    // set top margins in millimeters
			jsPrintSetup.setOption('marginTop','20');
			jsPrintSetup.setOption('marginLeft','3');
			jsPrintSetup.setOption('marginBottom','20');
			jsPrintSetup.setOption('marginRight','0');



				// set page header
			jsPrintSetup.setOption('headerStrLeft','');
			jsPrintSetup.setOption('headerStrCenter','');
			jsPrintSetup.setOption('headerStrRight','');

				// set empty page footer
			jsPrintSetup.setOption('footerStrLeft','');
			jsPrintSetup.setOption('footerStrCenter','');
			jsPrintSetup.setOption('footerStrRight','');

			
			var printers = jsPrintSetup.getPrintersList().split(',');
            //alert(jsPrintSetup.getPrintersList());
			// get right printer here..
			for(var index in printers) {
				var default_ticket_printer = window.localStorage && localStorage['<?php echo $selected_printer; ?>'];
                //alert(default_ticket_printer);
				var selected_printer = printers[index];
				if (selected_printer == default_ticket_printer) {
					// select epson label printer
					jsPrintSetup.setPrinter(selected_printer);
					// clears user preferences always silent print value
					// to enable using 'printSilent' option
					jsPrintSetup.clearSilentPrint();
					<?php if (!$this->config->item('print_silently')) 
					{
					?>
						// Suppress print dialog (for this context only)
						jsPrintSetup.setOption('printSilent', 1);
					<?php 
					}
					?>
					// Do Print 
					// When print is submitted it is executed asynchronous and
					// script flow continues after print independently of completetion of print process! 
					jsPrintSetup.print();
				}
			}
		}
		else
		{
			window.print();
		}
	}
}
</script>