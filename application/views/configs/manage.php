<?php $this->load->view("partial/header"); ?>

<ul class="nav nav-tabs" data-tabs="tabs">
    <?php if(!empty($aTabs)): $i = 0;?>
        <?php foreach($aTabs as $key=>$tab): ?>
    <li class="<?=$i > 0?'':'active'?>" role="presentation">
        <a data-toggle="tab" href="#<?=$tab?>" title="<?php echo $this->lang->line('config_'.$key.'_configuration'); ?>"><?php echo $this->lang->line('config_'.$key); ?></a>
    </li>
        <?php $i++; endforeach; ?>    
    <?php else: ?>
        <li role="presentation">
            Bạn cần liên hệ quản trị để bổ sung thêm quyền cho module này!
        </li>
    <?php endif; ?>
</ul>
<?php if(!empty($aTabs)): $i = 0;?>
<div class="tab-content">
<?php foreach($aTabs as $key=>$tab): ?>
    <div class="<?=$i > 0 ? 'tab-pane':'tab-pane fade in active'?>" id="<?=$tab?>">
        <?php $this->load->view("configs/".$key.'_config'); ?>
    </div>
<?php $i++; endforeach; ?>    
</div>
<?php endif; ?>
<?php $this->load->view("partial/footer"); ?>
