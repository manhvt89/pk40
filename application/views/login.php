<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<base href="<?php echo base_url();?>" />
	<title><?php echo $this->config->item('company') . ' | Phần mềm Phòng khám mắt ' . $this->config->item('application_version')  . ' | ' .  $this->lang->line('login_login'); ?></title>
	<link rel="shortcut icon" type="image/x-icon" href="images/favicon.ico">
	<link rel="stylesheet" type="text/css" href="<?php echo 'dist/bootswatch/' . (empty($this->config->item('theme')) ? 'flatly' : $this->config->item('theme')) . '/bootstrap.min.css' ?>"/>
	<!-- start css template tags -->
	<link rel="stylesheet" type="text/css" href="dist/login.css"/>
	<!-- end css template tags -->
</head>

<body>
<section class="ftco-section">
		<div class="container">
			<div class="row justify-content-center">
				<div class="col-md-6 text-center mb-5">
					<h2 class="heading-section">Hệ thống quản lý bán hàng</h2>
				</div>
			</div>
			<div class="row justify-content-center">
				<div class="col-md-7 col-lg-5">
					<div class="wrap">
						<div class="img" style="background-image: url(<?php echo base_url();?>/images/logo.png);"></div>
						<div class="login-wrap p-4 p-md-5">
							<div class="d-flex">
								<div class="w-100">
								<div align="center" style="color:red"><?php echo validation_errors(); ?></div>
								</div>		
							</div>
							<?php echo form_open('login') ?>
								<div class="form-group mt-3">
									<input id="username" class="form-control" required name="username" type="username" size=20 autofocus>
									<label class="form-control-placeholder" for="username">Tên đăng nhập</label>
								</div>
								<div class="form-group">
								<input id="password-field" type="password" class="form-control" required name="password" id="password"> 
								<label class="form-control-placeholder" for="password">Mật khẩu</label>
								<span toggle="#password-field" class="fa fa-fw fa-eye field-icon toggle-password"></span>
								</div>
								<div class="form-group">
									<button type="submit" class="form-control btn btn-primary rounded submit px-3" name="loginButton">Đăng nhập</button>
								</div>
							<?php echo form_close(); ?>
		        </div>
		      </div>
				</div>
			</div>
		</div>
	</section>


	
</body>
</html>
