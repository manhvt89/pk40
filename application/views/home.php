<?php $this->load->view("partial/header"); ?>

<h3 class="text-center"><?php echo $this->lang->line('common_welcome_message'); ?></h3>

<div id="home_module_list">
	<?php
	foreach($allowed_modules as $module)
	{
	?>
		<div class="module_item" title="<?php echo $this->lang->line('module_'.$module->module_key.'_desc');?>">
			<a href="<?php echo site_url("$module->module_key");?>"><img width="64px" src="<?php echo base_url().'images/menubar/'.$module->module_key.'.png';?>" border="0" alt="Menubar Image" /></a>
			<a href="<?php echo site_url("$module->module_key");?>"><?php echo $this->lang->line("module_".$module->module_key) ?></a>
		</div>
	<?php
	}
	?>
</div>

<?php $this->load->view("partial/footer"); ?>