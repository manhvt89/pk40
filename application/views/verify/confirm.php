
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<base href="<?php echo base_url();?>" />
	<title> He thong thong tin</title>
	<link rel="shortcut icon" type="image/x-icon" href="images/favicon.ico">
	<link rel="stylesheet" type="text/css" href="<?php echo 'dist/bootswatch/' . (empty($this->config->item('theme')) ? 'flatly' : $this->config->item('theme')) . '/bootstrap.min.css' ?>"/>
	<!-- start css template tags -->
	<link rel="stylesheet" type="text/css" href="dist/login.css"/>
	<!-- end css template tags -->
</head>

<body>
	<div id="logo" align="center"><img id="image" src="<?php echo base_url('uploads/' . $this->config->item('company_logo')); ?>" alt="company_logo" /></div>

	<div id="login">
		<?php echo form_open('verify/confirm') ?>
			<div id="container">
				<div style="color:red; align-content: center"><?php echo validation_errors(); ?></div>
				<div id="login_form">
					<div class="input-group">
						<label>Vui lòng nhập mã xác thực là 6 (sáu) số cuối của điện thoại di động của bạn</label>
						<span>Nếu không xác thực được vui lòng liên hệ đến hotline: </span>
					</div>
					<div class="input-group">
						<span class="input-group-addon input-sm"><span class="glyphicon glyphicon-lock"></span></span>
						<input class="form-control" placeholder="Vui lòng nhập mã xác thực" name="token" id="token" type="password" size=20></input>
						<input type="hidden" name="sale_uuid" id="sale_uuid" value="<?php echo $sale_uuid ?>" />
						<input type="hidden" name="sale_token" id="sale_token" value="<?php echo $token ?>" />
					</div>
					
					<input class="btn btn-primary btn-block" type="submit" name="loginButton" value="Xác nhận"/>
				</div>
			</div>
		<?php echo form_close(); ?>
	</div>
<script type="text/javascript">
</script>
</body>
</html>