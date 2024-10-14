<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<base href="<?php echo base_url();?>" />
	<title><?php echo $this->config->item('company') . ' | ' . $this->lang->line('common_powered_by') . ' PK40 ' . 'version 2.0.1' ?></title>
	<link rel="shortcut icon" type="image/x-icon" href="images/favicon.ico">

	<?php if ($this->input->cookie('debug') == "true" || $this->input->get("debug") == "true") : ?>
		<!-- bower:css -->
		<link rel="stylesheet" href="components/bootstrap/css/bootstrap.css" />
		<link rel="stylesheet" href="components/bootstrap-table/dist/bootstrap-table.min.css" />
		<link rel="stylesheet" href="components/bootstrap-select/dist/css/bootstrap-select.min.css" />
		<link rel="stylesheet" href="components/daterangepicker/daterangepicker.css" />
		<!--<link rel="stylesheet" href="bower_components/bootstrap3-dialog/dist/css/bootstrap-dialog.min.css" /> 
		<link rel="stylesheet" href="bower_components/jasny-bootstrap/dist/css/jasny-bootstrap.css" />
		<link rel="stylesheet" href="bower_components/smalot-bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css" />
		
		
		<link rel="stylesheet" href="bower_components/chartist/dist/chartist.min.css" />
		<link rel="stylesheet" href="bower_components/chartist-plugin-tooltip/dist/chartist-plugin-tooltip.css" /> -->
		<!-- endbower -->
		<!-- start css template tags -->
		<link rel="stylesheet" type="text/css" href="dist/jquery-ui.css"/>
		
		<link rel="stylesheet" type="text/css" href="dist/style.css"/>
		<link rel="stylesheet" type="text/css" href="dist/font-awesome.min.css"/>
		<link rel="stylesheet" type="text/css" href="css/bootstrap.autocomplete.css"/>
		<link rel="stylesheet" type="text/css" href="css/invoice.css"/>
		<link rel="stylesheet" type="text/css" href="css/ospos.css"/>
		<link rel="stylesheet" type="text/css" href="css/ospos_print.css"/>
		<link rel="stylesheet" type="text/css" href="css/popupbox.css"/>
		<link rel="stylesheet" type="text/css" href="css/receipt.css"/>
		<link rel="stylesheet" type="text/css" href="css/register.css"/>
		<link rel="stylesheet" type="text/css" href="css/reports.css"/>
		<link rel="stylesheet" type="text/css" href="css/style.css"/>
		<!-- end css template tags -->
		<!-- bower:js -->
		<script src="components/jquery/jquery-3.7.1.min.js"></script>
		<script src="components/jquery-form/dist/jquery.form.min.js"></script>
		<script src="components/jquery-validate/dist/jquery.validate.min.js"></script>
		<script src="components/jquery-ui/jquery-ui.min.js"></script>
		<script src="components/bootstrap/js/bootstrap.js"></script>
		<script src="components/bootstrap-table/dist/bootstrap-table.min.js"></script>
		<script src="components/moment/moment.js"></script>
		<script src=" https://cdn.jsdelivr.net/npm/js-cookie@3.0.5/dist/js.cookie.min.js "></script>
		<script src="components/remarkable-bootstrap-notify/bootstrap-notify.min.js"></script>
		<script src="components/bootstrap-select/dist/js/bootstrap-select.min.js"></script>
		<script src="components/daterangepicker/daterangepicker.js"></script>

		<!-- <script src="bower_components/bootstrap3-dialog/dist/js/bootstrap-dialog.min.js"></script> -->
<!--
		<script src="bower_components/jasny-bootstrap/dist/js/jasny-bootstrap.js"></script>
		<script src="bower_components/smalot-bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js"></script>
		
		
		<script src="bower_components/bootstrap-table/dist/extensions/export/bootstrap-table-export.js"></script>
		<script src="bower_components/bootstrap-table/dist/extensions/mobile/bootstrap-table-mobile.js"></script>
		<script src="components/moment/dist/moment.js"></script>
		<script src="bower_components/bootstrap-daterangepicker/daterangepicker.js"></script>
		<script src="bower_components/file-saver.js/FileSaver.js"></script>
		<script src="bower_components/html2canvas/build/html2canvas.js"></script>
		<script src="bower_components/jspdf/dist/jspdf.min.js"></script>
		<script src="bower_components/jspdf-autotable/dist/jspdf.plugin.autotable.js"></script>
		<script src="bower_components/tableExport.jquery.plugin/tableExport.min.js"></script>
		<script src="bower_components/chartist/dist/chartist.min.js"></script>
		<script src="bower_components/chartist-plugin-axistitle/dist/chartist-plugin-axistitle.min.js"></script>
		<script src="bower_components/chartist-plugin-pointlabels/dist/chartist-plugin-pointlabels.min.js"></script>
		<script src="bower_components/chartist-plugin-tooltip/dist/chartist-plugin-tooltip.min.js"></script>
		<script src="bower_components/chartist-plugin-barlabels/dist/chartist-plugin-barlabels.min.js"></script>
		<script src="components/remarkable-bootstrap-notify/bootstrap-notify.min.js"></script>
		<script src="bower_components/js-cookie/src/js.cookie.js"></script>
		<script src="bower_components/blockUI/jquery.blockUI.js"></script> -->
		<!-- endbower -->
		<!-- start js template tags -->
		<script type="text/javascript" src="js/imgpreview.full.jquery.js"></script>
		<script type="text/javascript" src="js/manage_tables.js"></script>
		<script type="text/javascript" src="js/nominatim.autocomplete.js"></script>
		<!-- end js template tags -->
	<?php else : ?>
		<!--[if lte IE 8]>
		<link rel="stylesheet" media="print" href="dist/print.css" type="text/css" />
		<![endif]-->
		<!-- start mincss template tags -->
		<link rel="stylesheet" type="text/css" href="dist/jquery-ui.css"/>
		<link rel="stylesheet" type="text/css" href="dist/opensourcepos.min.css?rel=d5b9522f2f"/>
		<link rel="stylesheet" type="text/css" href="dist/style.css"/>
		<link rel="stylesheet" type="text/css" href="dist/font-awesome.min.css"/>
		<!-- end mincss template tags -->
		<!-- start minjs template tags -->
		<script type="text/javascript" src="dist/opensourcepos.min.js?rel=bc5842b19a"></script>
		<!-- end minjs template tags -->
	<?php endif; ?>
		

	<link rel="stylesheet" type="text/css" href="<?php echo 'dist/bootswatch/' . (empty($this->config->item('theme')) ? 'flatly' : $this->config->item('theme')) . '/bootstrap.min.css' ?>"/>
	<!-- Added By ManhVT support data grid like excel-->
	<script src="dist/jspreadsheet/jexcel.js"></script>

	<script src="dist/jspreadsheet/jsuites.js"></script>
	<script type="text/javascript" src="js/utils.js"></script>
	<link rel="stylesheet" href="dist/jspreadsheet/jexcel.css" type="text/css" />

	<link rel="stylesheet" href="dist/jspreadsheet/jsuites.css" type="text/css" />
	<link rel="stylesheet" href="dist/pres.css" type="text/css" />
	<link rel="manifest" href="/manifest.json">

	<?php $this->load->view('partial/header_js'); ?>
	<script src="/app.js"></script>
	<?php $this->load->view('partial/lang_lines'); ?>

	<style type="text/css">
		html {
			overflow: auto;
		}
	</style>
	<?php if($this->Employee->has_grant('test_step_one')):?>
		<style type="text/css">
		.s-100 {
			display: none;
		}
	</style>
	<?php endif; ?>
</head>

<body>
	<div class="wrapper">
		<header class="fixed-menu" id="header1">
			<div class="topbar">
				<div class="container">
					<div class="row">
						<!-- Sử dụng các lớp tùy chỉnh d-flex và justify-content-between -->
						<div class="col-xs-12 col-md-4 d-flex justify-content-between flex-wrap">
							<div id="liveclock">
								<?php echo date($this->config->item('dateformat') . ' ' . $this->config->item('timeformat')) ?>
							</div>
							<a href="https://docs.google.com/document/d/15-AAz6FNdPSoJUmGpATPykfwYCRMvezyttfjvHXp7yM/edit#heading=h.oja56yjzebgv" target="_blank">
								<b style="color: #fff;">Hướng dẫn sử dụng</b>
							</a>
						</div>
						
						<div class="col-xs-12 col-md-8 text-right">
							<?php echo strip_tags($this->config->item('company')) . "  |  $user_info->first_name $user_info->last_name  |  " . ($this->input->get("debug") == "true" ? $this->session->userdata('session_sha1') : ""); ?>
							<?php echo anchor("home/logout", $this->lang->line("common_logout")); ?>
						</div>
					</div>
				</div>
			</div>


			<div class="navbar navbar-default " role="navigation">
				<div class="container">
					<div class="navbar-header d-flex">
						<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target=".navbar-collapse">
							<span class="sr-only">Toggle navigation</span>
							<span class="icon-bar"></span>
							<span class="icon-bar"></span>
							<span class="icon-bar"></span>
						</button>
						<!-- <a class="navbar-brand hidden-sm" href="<?php echo site_url(); ?>">ESS</a> -->
						<!-- Những mục bạn muốn hiển thị ra ngoài menu collapse -->
						<ul class="navbar-nav-xs">
						
							<?php $i =0; foreach($allowed_modules as $module): $i++; if($i > 6) { break;}?>
							<li class="<?php echo $module->module_key == $this->uri->segment(1)? 'active': ''; ?>">
								<a href="<?php echo site_url("$module->module_key");?>" title="<?php echo $this->lang->line("module_".$module->module_key);?>" class="menu-icon">
									<img src="<?php echo base_url().'images/menubar/'.$module->module_key.'.png';?>" border="0" alt="Module Icon" /><br />
									<?php echo $this->lang->line("module_".$module->module_key) ?>
								</a>
							</li>
							<?php endforeach; ?>
						
						</ul>
					</div>

					<div class="navbar-collapse collapse">
						<ul class="nav navbar-nav navbar-right">
							<?php foreach($allowed_modules as $module): ?>
							<li class="<?php echo $module->module_key == $this->uri->segment(1)? 'active': ''; ?>">
								<a href="<?php echo site_url("$module->module_key");?>" title="<?php echo $this->lang->line("module_".$module->module_key);?>" class="menu-icon">
									<img src="<?php echo base_url().'images/menubar/'.$module->module_key.'.png';?>" border="0" alt="Module Icon" /><br />
									<?php echo $this->lang->line("module_".$module->module_key) ?>
								</a>
							</li>
							<?php endforeach; ?>
						</ul>
					</div>
				</div>
			</div>
		</header>

		<div class="container" id="content">
			<div class="row flex-column flex-md-row">