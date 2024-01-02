<?php echo form_open('config/save_barcode/', array('id' => 'barcode_config_form', 'class' => 'form-horizontal')); ?>
    <div id="config_wrapper">
        <fieldset id="config_info">
            <div id="required_fields_message"><?php echo $this->lang->line('common_fields_required_message'); ?></div>
            <ul id="barcode_error_message_box" class="error_message_box"></ul>
            
            <div class="config-title">
                <b style="text-transform: uppercase;">Thiết lập chung</b>
            </div>

            <div class="form-group form-group-sm">	
				<?php echo form_label($this->lang->line('config_debug_barcode'), 'debug_barcode', array('class' => 'control-label col-xs-2')); ?>
				<div class='col-xs-2'>
					<?php echo form_checkbox(array(
						'name' => 'debug_barcode',
						'id' => 'debug_barcode',
						'value' => '1',
						'checked'=>$this->config->item('debug_barcode'))); ?>
				</div>
			</div>

            <div class="form-group form-group-sm">    
            <?php echo form_label($this->lang->line('Phone_Barcode'), 'Phone_Barcode', array('class' => 'control-label col-xs-2')); ?>
                <div class="col-sm-2">
                    <div class='input-group'>
                        <?php echo form_input(array(
                            'name' => 'Phone_Barcode',
                            'id' => 'Phone_Barcode',
                            'class' => 'form-control input-sm',
                            'value'=>$this->config->item('Phone_Barcode'))); ?>
                        <span class="input-group-addon input-sm"></span>
                    </div>
                </div>
            </div>
            <div class="form-group form-group-sm">    
            <?php echo form_label($this->lang->line('Slogan_Barcode'), 'Slogan_Barcode', array('class' => 'control-label col-xs-2')); ?>
                <div class="col-sm-2">
                    <div class='input-group'>
                        <?php echo form_input(array(
                            'name' => 'Slogan_Barcode',
                            'id' => 'Slogan_Barcode',
                            'class' => 'form-control input-sm',
                            'value'=>$this->config->item('Slogan_Barcode'))); ?>
                        <span class="input-group-addon input-sm"></span>
                    </div>
                </div>
            </div>
            <div class="form-group form-group-sm">    
            <?php echo form_label($this->lang->line('Location_Barcode'), 'Location_Barcode', array('class' => 'control-label col-xs-2 ')); ?>
                <div class="col-sm-2">
                    <div class='input-group'>
                        <?php echo form_input(array(
                            'name' => 'Location_Barcode',
                            'id' => 'Location_Barcode',
                            'class' => 'form-control input-sm ',
                            'value'=>$this->config->item('Location_Barcode'))); ?>
                        <span class="input-group-addon input-sm"></span>
                    </div>
                </div>
            </div>
            <div class="config-title">
                <b style="text-transform: uppercase;">Thiết lập In Barcode Gọng kính</b>
            </div>
            <div class="form-group form-group-sm">    
				<?php echo form_label($this->lang->line('GBarcode'), 'GBarcode', array('class' => 'control-label col-xs-2')); ?>
                <div class='col-xs-2'>
                    <?php echo form_dropdown(
                            'GBarcode', 
                            $support_template['Gong'], 
                            $this->config->item('GBarcode')['template'], 
                            array('class' => 'form-control input-sm')); ?>
					
                </div>
            </div>

            <div class="form-group form-group-sm">    
				<?php echo form_label($this->lang->line('config_barcode_type'), 'barcode_type', array('class' => 'control-label col-xs-2')); ?>
                <div class='col-xs-2'>
					<?php echo form_dropdown('barcode_type', $support_barcode, $this->config->item('barcode_type'), array('class' => 'form-control input-sm')); ?>
                </div>
            </div>
            
            <div class="form-group form-group-sm">    
				<?php echo form_label($this->lang->line('config_barcode_quality'), 'barcode_quality', array('class' => 'control-label col-xs-2 required')); ?>
                <div class='col-xs-2'>
					<?php echo form_input(array(
						'max' => '100',
						'min' => '10',
						'type' => 'number',
						'name' => 'barcode_quality',
						'id' => 'barcode_quality',
						'class' => 'form-control input-sm required',
						'value'=>$this->config->item('barcode_quality'))); ?>
                </div>
            </div>
            
            <div class="form-group form-group-sm">    
				<?php echo form_label($this->lang->line('config_barcode_width'), 'barcode_width', array('class' => 'control-label col-xs-2 required')); ?>
                <div class='col-xs-2'>
                    <div class='input-group'>
                        <?php echo form_input(array(
                            'step' => '5',
                            'max' => '100',
                            'min' => '10',
                            'type' => 'number',
                            'name' => 'barcode_width',
                            'id' => 'barcode_width',
                            'class' => 'form-control input-sm required',
                            'value'=>$this->config->item('barcode_width'))); ?>
                            <span class="input-group-addon input-sm">mm</span>
                    </div>
                </div>
            </div>

            <div class="form-group form-group-sm">    
				<?php echo form_label($this->lang->line('config_barcode_height'), 'barcode_height', array('class' => 'control-label col-xs-2 required')); ?>
                <div class='col-xs-2'>
                    <div class='input-group'>
                        <?php echo form_input(array(
                            'type' => 'number',
                            'min' => 10,
                            'max' => 120,
                            'name' => 'barcode_height',
                            'id' => 'barcode_height',
                            'class' => 'form-control input-sm required',
                            'value'=>$this->config->item('barcode_height'))); ?>
                        <span class="input-group-addon input-sm">mm</span>
                    </div>
                        
                    
					
                </div>
            </div>
            
            <div class="form-group form-group-sm">    
				<?php echo form_label($this->lang->line('config_barcode_font'), 'barcode_font', array('class' => 'control-label col-xs-2 required')); ?>
                <div class='col-sm-2'>
					<?php echo form_dropdown('barcode_font', 
						$this->barcode_lib->listfonts("fonts"),
						$this->config->item('barcode_font'), array('class' => 'form-control input-sm required'));
						?>
                </div>
                <div class="col-sm-2">
                    <div class='input-group'>
                        <?php echo form_input(array(
                            'type' => 'number',
                            'min' => '1',
                            'max' => '30',
                            'name' => 'barcode_font_size',
                            'id' => 'barcode_font_size',
                            'class' => 'form-control input-sm required',
                            'value'=>$this->config->item('barcode_font_size'))); ?>
                                    
                    </div>
                            
                </div>
            </div>
            
            <div class="form-group form-group-sm">    
                <?php echo form_label($this->lang->line('name_store_barcode'), 'name_store_barcode', array('class' => 'control-label col-xs-2 ')); ?>
                <div class="col-sm-6">
                    <div class='input-group'>
                        <?php echo form_input(array(
                            'name' => 'name_store_barcode',
                            'id' => 'name_store_barcode',
                            'class' => 'form-control input-sm ',
                            'value'=>$this->config->item('name_store_barcode'))); ?>
                        <span class="input-group-addon input-sm"></span>
                    </div>
                </div>
            </div>
            <div class="form-group form-group-sm">    
				<?php echo form_label($this->lang->line('name_store_barcode_font'), 'name_store_barcode_font', array('class' => 'control-label col-xs-2 required')); ?>
                <div class='col-sm-2'>
					<?php echo form_dropdown('name_store_barcode_font', 
						$this->barcode_lib->listfonts("fonts"),
						$this->config->item('name_store_barcode_font'), array('class' => 'form-control input-sm required'));
						?>
                </div>
                <div class="col-sm-2">
                    <div class='input-group'>
                        <?php echo form_input(array(
                            'type' => 'number',
                            'min' => '1',
                            'max' => '30',
                            'name' => 'name_store_barcode_font_size',
                            'id' => 'name_store_barcode_font_size',
                            'class' => 'form-control input-sm required',
                            'value'=>$this->config->item('name_store_barcode_font_size'))); ?>
                                    
                    </div>
                            
                </div>
            </div>

            <div class="form-group form-group-sm">    
                <?php echo form_label($this->lang->line('add_store_barcode'), 'add_store_barcode', array('class' => 'control-label col-xs-2 ')); ?>
                <div class="col-sm-6">
                    <div class='input-group'>
                        <?php echo form_input(array(
                            'name' => 'add_store_barcode',
                            'id' => 'add_store_barcode',
                            'class' => 'form-control input-sm ',
                            'value'=>$this->config->item('add_store_barcode'))); ?>
                        <span class="input-group-addon input-sm"></span>
                    </div>
                </div>
            </div>
            <div class="form-group form-group-sm">    
				<?php echo form_label($this->lang->line('add_store_barcode_font'), 'add_store_barcode_font', array('class' => 'control-label col-xs-2 required')); ?>
                <div class='col-sm-2'>
					<?php echo form_dropdown('add_store_barcode_font', 
						$this->barcode_lib->listfonts("fonts"),
						$this->config->item('add_store_barcode_font'), array('class' => 'form-control input-sm required'));
						?>
                </div>
                <div class="col-sm-2">
                    <div class='input-group'>
                        <?php echo form_input(array(
                            'type' => 'number',
                            'min' => '1',
                            'max' => '30',
                            'name' => 'add_store_barcode_font_size',
                            'id' => 'add_store_barcode_font_size',
                            'class' => 'form-control input-sm required',
                            'value'=>$this->config->item('add_store_barcode_font_size'))); ?>
                                    
                    </div>
                            
                </div>
            </div>
            
			<div class="form-group form-group-sm">
				<?php echo form_label($this->lang->line('config_barcode_content'), 'barcode_content', array('class' => 'control-label col-xs-2')); ?>
				<div class='col-xs-8'>
                    <label class="radio-inline">
                        <?php echo form_radio(array(
                            'name' => 'barcode_content',
                            'value' => 'id',
                            'checked'=>$this->config->item('barcode_content') === "id")); ?>
                        <?php echo $this->lang->line('config_barcode_id'); ?>
                    </label>
					<label class="radio-inline">
                        <?php echo form_radio(array(
                            'name' => 'barcode_content',
                            'value' => 'number',
                            'checked'=>$this->config->item('barcode_content') === "number")); ?>
                        <?php echo $this->lang->line('config_barcode_number'); ?>
                    </label>
					<label class="checkbox-inline">
                        <?php echo form_checkbox(array(
                            'name' => 'barcode_generate_if_empty',
                            'value' => 'barcode_generate_if_empty',
                            'checked'=>$this->config->item('barcode_generate_if_empty'))); ?>
                        <?php echo $this->lang->line('config_barcode_generate_if_empty'); ?>
                    </label>
				</div>
			</div>

            <div class="form-group form-group-sm">    
				<?php echo form_label($this->lang->line('config_barcode_layout'), 'barcode_layout', array('class' => 'control-label col-xs-2')); ?>
                <div class="col-sm-10">
                    <div class="form-group form-group-sm row">
                        <label class="control-label col-sm-1"><?php echo $this->lang->line('config_barcode_first_row').' '; ?></label>
                        <div class='col-sm-2'>
                            <?php echo form_dropdown('barcode_first_row', array(
                                'not_show' => $this->lang->line('config_none'),
                                'name' => $this->lang->line('items_name'),
                                'category' => $this->lang->line('items_category'),
                                'cost_price' => $this->lang->line('items_cost_price'),
                                'unit_price' => $this->lang->line('items_unit_price'),
                                'company_name' => $this->lang->line('suppliers_company_name')
                            ),
                            $this->config->item('barcode_first_row'), array('class' => 'form-control input-sm')); ?>
                        </div>
                        <label class="control-label col-sm-1"><?php echo $this->lang->line('config_barcode_second_row').' '; ?></label>
                        <div class='col-sm-2'>
                            <?php echo form_dropdown('barcode_second_row', array(
                                'not_show' => $this->lang->line('config_none'),
                                'name' => $this->lang->line('items_name'),
                                'category' => $this->lang->line('items_category'),
                                'cost_price' => $this->lang->line('items_cost_price'),
                                'unit_price' => $this->lang->line('items_unit_price'),
                                'item_code' => $this->lang->line('items_item_number'),
                                'company_name' => $this->lang->line('suppliers_company_name')
                            ),
                            $this->config->item('barcode_second_row'), array('class' => 'form-control input-sm')); ?>
                        </div>
                        <label class="control-label col-sm-1"><?php echo $this->lang->line('config_barcode_third_row').' '; ?></label>
                        <div class='col-sm-2'>
                            <?php echo form_dropdown('barcode_third_row', array(
                                'not_show' => $this->lang->line('config_none'),
                                'name' => $this->lang->line('items_name'),
                                'category' => $this->lang->line('items_category'),
                                'cost_price' => $this->lang->line('items_cost_price'),
                                'unit_price' => $this->lang->line('items_unit_price'),
                                'item_code' => $this->lang->line('items_item_number'),
                                'company_name' => $this->lang->line('suppliers_company_name')
                            ),
                            $this->config->item('barcode_third_row'), array('class' => 'form-control input-sm')); ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="form-group form-group-sm">    
				<?php echo form_label($this->lang->line('config_barcode_number_in_row'), 'barcode_num_in_row', array('class' => 'control-label col-xs-2 required')); ?>
                <div class='col-xs-2'>
					<?php echo form_input(array(
						'name' => 'barcode_num_in_row',
						'id' => 'barcode_num_in_row',
						'class' => 'form-control input-sm required',
						'value'=>$this->config->item('barcode_num_in_row'))); ?>
                </div>
            </div>
            
            <div class="form-group form-group-sm">    
            <?php echo form_label($this->lang->line('config_barcode_page_width'), 'barcode_page_width', array('class' => 'control-label col-xs-2 required')); ?>
                <div class="col-sm-2">
                    <div class='input-group'>
                        <?php echo form_input(array(
                            'name' => 'barcode_page_width',
                            'id' => 'barcode_page_width',
                            'class' => 'form-control input-sm required',
                            'value'=>$this->config->item('barcode_page_width'))); ?>
                        <span class="input-group-addon input-sm">mm</span>
                    </div>
                </div>
            </div>
            
            <div class="form-group form-group-sm">    
            <?php echo form_label($this->lang->line('config_barcode_page_cellspacing'), 'barcode_page_cellspacing', array('class' => 'control-label col-xs-2 required')); ?>
                <div class='col-sm-2'>
                    <div class="input-group">
                        <?php echo form_input(array(
                            'name' => 'barcode_page_cellspacing',
                            'id' => 'barcode_page_cellspacing',
                            'class' => 'form-control input-sm required',
                            'value'=>$this->config->item('barcode_page_cellspacing'))); ?>
                        <span class="input-group-addon input-sm">mm</span>
                    </div>
                </div>
            </div>
            <div class="config-title">
                <b style="text-transform: uppercase;">Thiết lập Barcode tròng kính</b>
            </div>
            <div class="form-group form-group-sm">    
				<?php echo form_label($this->lang->line('MBarcode'), 'MBarcode', array('class' => 'control-label col-xs-2')); ?>
                <div class='col-xs-2'>
                    <?php echo form_dropdown(
                            'MBarcode', 
                            $support_template['Mat'], 
                            $this->config->item('MBarcode')['template'], 
                            array('class' => 'form-control input-sm')); ?>
					
                </div>
            </div>

            <div class="form-group form-group-sm">    
				<?php echo form_label($this->lang->line('config_barcode_type'), 'barcode_type', array('class' => 'control-label col-xs-2')); ?>
                <div class='col-xs-2'>
					<?php echo form_dropdown('lens_barcode_type', $support_barcode, $this->config->item('lens_barcode_type'), array('class' => 'form-control input-sm')); ?>
                </div>
            </div>
            
            <div class="form-group form-group-sm">    
				<?php echo form_label($this->lang->line('config_barcode_quality'), 'lens_barcode_quality', array('class' => 'control-label col-xs-2 required')); ?>
                <div class='col-xs-2'>
					<?php echo form_input(array(
						'max' => '100',
						'min' => '10',
						'type' => 'number',
						'name' => 'lens_barcode_quality',
						'id' => 'lens_barcode_quality',
						'class' => 'form-control input-sm required',
						'value'=>$this->config->item('lens_barcode_quality'))); ?>
                </div>
            </div>
            
            <div class="form-group form-group-sm">    
				<?php echo form_label($this->lang->line('config_barcode_width'), 'lens_barcode_width', array('class' => 'control-label col-xs-2 required')); ?>
                <div class='col-xs-2'>
                <div class='input-group'>
                <?php echo form_input(array(
						'step' => '1',
						'max' => '150',
						'min' => '5',
						'type' => 'number',
						'name' => 'lens_barcode_width',
						'id' => 'lens_barcode_width',
						'class' => 'form-control input-sm required',
						'value'=>$this->config->item('lens_barcode_width'))); ?>
                            <span class="input-group-addon input-sm">mm</span>
                    </div>
					
                </div>
            </div>

            <div class="form-group form-group-sm">    
				<?php echo form_label($this->lang->line('config_barcode_height'), 'lens_barcode_height', array('class' => 'control-label col-xs-2 required')); ?>
                <div class='col-xs-2'>
                    <div class='input-group'>
                        <?php echo form_input(array(
                                'type' => 'number',
                                'min' => 10,
                                'max' => 120,
                                'name' => 'lens_barcode_height',
                                'id' => 'lens_barcode_height',
                                'class' => 'form-control input-sm required',
                                'value'=>$this->config->item('lens_barcode_height'))); ?>
                            <span class="input-group-addon input-sm">mm</span>
                    </div>
					
                </div>
                
            </div>
            
            <div class="form-group form-group-sm">    
				<?php echo form_label($this->lang->line('config_barcode_font'), 'lens_barcode_font', array('class' => 'control-label col-xs-2 required')); ?>
                <div class='col-sm-2'>
					<?php echo form_dropdown('lens_barcode_font', 
						$this->barcode_lib->listfonts("fonts"),
						$this->config->item('lens_barcode_font'), array('class' => 'form-control input-sm required'));
						?>
                </div>
                <div class="col-sm-2">
                    <?php echo form_input(array(
                        'type' => 'number',
                        'min' => '1',
                        'max' => '30',
                        'name' => 'lens_barcode_font_size',
                        'id' => 'lens_barcode_font_size',
                        'class' => 'form-control input-sm required',
                        'value'=>$this->config->item('lens_barcode_font_size'))); ?>
                </div>
            </div>
            
			<div class="form-group form-group-sm">
				<?php echo form_label($this->lang->line('config_barcode_content'), 'lens_barcode_content', array('class' => 'control-label col-xs-2')); ?>
				<div class='col-xs-8'>
                    <label class="radio-inline">
                        <?php echo form_radio(array(
                            'name' => 'lens_barcode_content',
                            'value' => 'id',
                            'checked'=>$this->config->item('lens_barcode_content') === "id")); ?>
                        <?php echo $this->lang->line('config_barcode_id'); ?>
                    </label>
					<label class="radio-inline">
                        <?php echo form_radio(array(
                            'name' => 'lens_barcode_content',
                            'value' => 'number',
                            'checked'=>$this->config->item('lens_barcode_content') === "number")); ?>
                        <?php echo $this->lang->line('config_barcode_number'); ?>
                    </label>
					<label class="checkbox-inline">
                        <?php echo form_checkbox(array(
                            'name' => 'lens_barcode_generate_if_empty',
                            'value' => 'lens_barcode_generate_if_empty',
                            'checked'=>$this->config->item('lens_barcode_generate_if_empty'))); ?>
                        <?php echo $this->lang->line('config_barcode_generate_if_empty'); ?>
                    </label>
				</div>
			</div>

            <div class="form-group form-group-sm">    
				<?php echo form_label($this->lang->line('config_barcode_layout'), 'lens_barcode_layout', array('class' => 'control-label col-xs-2')); ?>
                <div class="col-sm-10">
                    <div class="form-group form-group-sm row">
                        <label class="control-label col-sm-1"><?php echo $this->lang->line('config_barcode_first_row').' '; ?></label>
                        <div class='col-sm-2'>
                            <?php echo form_dropdown('lens_barcode_first_row', array(
                                'not_show' => $this->lang->line('config_none'),
                                'name' => $this->lang->line('items_name'),
                                'category' => $this->lang->line('items_category'),
                                'cost_price' => $this->lang->line('items_cost_price'),
                                'unit_price' => $this->lang->line('items_unit_price'),
                                'company_name' => $this->lang->line('suppliers_company_name')
                            ),
                            $this->config->item('lens_barcode_first_row'), array('class' => 'form-control input-sm')); ?>
                        </div>
                        <label class="control-label col-sm-1"><?php echo $this->lang->line('config_barcode_second_row').' '; ?></label>
                        <div class='col-sm-2'>
                            <?php echo form_dropdown('lens_barcode_second_row', array(
                                'not_show' => $this->lang->line('config_none'),
                                'name' => $this->lang->line('items_name'),
                                'category' => $this->lang->line('items_category'),
                                'cost_price' => $this->lang->line('items_cost_price'),
                                'unit_price' => $this->lang->line('items_unit_price'),
                                'item_code' => $this->lang->line('items_item_number'),
                                'company_name' => $this->lang->line('suppliers_company_name')
                            ),
                            $this->config->item('lens_barcode_second_row'), array('class' => 'form-control input-sm')); ?>
                        </div>
                        <label class="control-label col-sm-1"><?php echo $this->lang->line('config_barcode_third_row').' '; ?></label>
                        <div class='col-sm-2'>
                            <?php echo form_dropdown('lens_barcode_third_row', array(
                                'not_show' => $this->lang->line('config_none'),
                                'name' => $this->lang->line('items_name'),
                                'category' => $this->lang->line('items_category'),
                                'cost_price' => $this->lang->line('items_cost_price'),
                                'unit_price' => $this->lang->line('items_unit_price'),
                                'item_code' => $this->lang->line('items_item_number'),
                                'company_name' => $this->lang->line('suppliers_company_name')
                            ),
                            $this->config->item('lens_barcode_third_row'), array('class' => 'form-control input-sm')); ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="form-group form-group-sm">    
				<?php echo form_label($this->lang->line('config_barcode_number_in_row'), 'lens_barcode_num_in_row', array('class' => 'control-label col-xs-2 required')); ?>
                <div class='col-xs-2'>
					<?php echo form_input(array(
						'name' => 'lens_barcode_num_in_row',
						'id' => 'lens_barcode_num_in_row',
						'class' => 'form-control input-sm required',
						'value'=>$this->config->item('lens_barcode_num_in_row'))); ?>
                </div>
            </div>
            
            <div class="form-group form-group-sm">    
            <?php echo form_label($this->lang->line('config_barcode_page_width'), 'lens_barcode_page_width', array('class' => 'control-label col-xs-2 required')); ?>
                <div class="col-sm-2">
                    <div class='input-group'>
                        <?php echo form_input(array(
                            'name' => 'lens_barcode_page_width',
                            'id' => 'lens_barcode_page_width',
                            'class' => 'form-control input-sm required',
                            'value'=>$this->config->item('lens_barcode_page_width'))); ?>
                        <span class="input-group-addon input-sm">mm</span>
                    </div>
                </div>
            </div>
            
            <div class="form-group form-group-sm">    
            <?php echo form_label($this->lang->line('config_barcode_page_cellspacing'), 'lens_barcode_page_cellspacing', array('class' => 'control-label col-xs-2 required')); ?>
                <div class='col-sm-2'>
                    <div class="input-group">
                        <?php echo form_input(array(
                            'name' => 'lens_barcode_page_cellspacing',
                            'id' => 'lens_barcode_page_cellspacing',
                            'class' => 'form-control input-sm required',
                            'value'=>$this->config->item('lens_barcode_page_cellspacing'))); ?>
                        <span class="input-group-addon input-sm">mm</span>
                    </div>
                </div>
            </div>

            <div class="config-title">
                <b>Thiết lập Barcode Thuốc</b>
            </div>
            <div class="form-group form-group-sm">    
				<?php echo form_label($this->lang->line('Thuoc'), 'Thuoc', array('class' => 'control-label col-xs-2')); ?>
                <div class='col-xs-2'>
                    <?php echo form_dropdown(
                            'Thuoc', 
                            $support_template['Thuoc'], 
                            $this->config->item('Thuoc')['template'], 
                            array('class' => 'form-control input-sm')); ?>
					
                </div>
            </div>

            <div class="form-group form-group-sm">    
				<?php echo form_label($this->lang->line('config_barcode_type'), 'barcode_type', array('class' => 'control-label col-xs-2')); ?>
                <div class='col-xs-2'>
					<?php echo form_dropdown('t_barcode_type', $support_barcode, $this->config->item('t_barcode_type'), array('class' => 'form-control input-sm')); ?>
                </div>
            </div>
            
            <div class="form-group form-group-sm">    
				<?php echo form_label($this->lang->line('config_barcode_quality'), 't_barcode_quality', array('class' => 'control-label col-xs-2 required')); ?>
                <div class='col-xs-2'>
					<?php echo form_input(array(
						'max' => '100',
						'min' => '10',
						'type' => 'number',
						'name' => 't_barcode_quality',
						'id' => 't_barcode_quality',
						'class' => 'form-control input-sm required',
						'value'=>$this->config->item('t_barcode_quality'))); ?>
                </div>
            </div>
            
            <div class="form-group form-group-sm">    
				<?php echo form_label($this->lang->line('config_barcode_width'), 't_barcode_width', array('class' => 'control-label col-xs-2 required')); ?>
                <div class='col-xs-2'>
                <div class='input-group'>
                <?php echo form_input(array(
						'step' => '1',
						'max' => '150',
						'min' => '5',
						'type' => 'number',
						'name' => 't_barcode_width',
						'id' => 't_barcode_width',
						'class' => 'form-control input-sm required',
						'value'=>$this->config->item('t_barcode_width'))); ?>
                            <span class="input-group-addon input-sm">mm</span>
                    </div>
					
                </div>
            </div>

            <div class="form-group form-group-sm">    
				<?php echo form_label($this->lang->line('config_barcode_height'), 't_barcode_height', array('class' => 'control-label col-xs-2 required')); ?>
                <div class='col-xs-2'>
                    <div class='input-group'>
                        <?php echo form_input(array(
                                'type' => 'number',
                                'min' => 10,
                                'max' => 120,
                                'name' => 't_barcode_height',
                                'id' => 't_barcode_height',
                                'class' => 'form-control input-sm required',
                                'value'=>$this->config->item('t_barcode_height'))); ?>
                            <span class="input-group-addon input-sm">mm</span>
                    </div>
					
                </div>
                
            </div>
            
            <div class="form-group form-group-sm">    
				<?php echo form_label($this->lang->line('config_barcode_font'), 't_barcode_font', array('class' => 'control-label col-xs-2 required')); ?>
                <div class='col-sm-2'>
					<?php echo form_dropdown('t_barcode_font', 
						$this->barcode_lib->listfonts("fonts"),
						$this->config->item('t_barcode_font'), array('class' => 'form-control input-sm required'));
						?>
                </div>
                <div class="col-sm-2">
                    <?php echo form_input(array(
                        'type' => 'number',
                        'min' => '1',
                        'max' => '30',
                        'name' => 't_barcode_font_size',
                        'id' => 't_barcode_font_size',
                        'class' => 'form-control input-sm required',
                        'value'=>$this->config->item('t_barcode_font_size'))); ?>
                </div>
            </div>
            
			<div class="form-group form-group-sm">
				<?php echo form_label($this->lang->line('config_barcode_content'), 't_barcode_content', array('class' => 'control-label col-xs-2')); ?>
				<div class='col-xs-8'>
                    <label class="radio-inline">
                        <?php echo form_radio(array(
                            'name' => 't_barcode_content',
                            'value' => 'id',
                            'checked'=>$this->config->item('t_barcode_content') === "id")); ?>
                        <?php echo $this->lang->line('config_barcode_id'); ?>
                    </label>
					<label class="radio-inline">
                        <?php echo form_radio(array(
                            'name' => 't_barcode_content',
                            'value' => 'number',
                            'checked'=>$this->config->item('t_barcode_content') === "number")); ?>
                        <?php echo $this->lang->line('config_barcode_number'); ?>
                    </label>
					<label class="checkbox-inline">
                        <?php echo form_checkbox(array(
                            'name' => 't_barcode_generate_if_empty',
                            'value' => 't_barcode_generate_if_empty',
                            'checked'=>$this->config->item('t_barcode_generate_if_empty'))); ?>
                        <?php echo $this->lang->line('config_barcode_generate_if_empty'); ?>
                    </label>
				</div>
			</div>

            <div class="form-group form-group-sm">    
				<?php echo form_label($this->lang->line('config_barcode_layout'), 't_barcode_layout', array('class' => 'control-label col-xs-2')); ?>
                <div class="col-sm-10">
                    <div class="form-group form-group-sm row">
                        <label class="control-label col-sm-1"><?php echo $this->lang->line('config_barcode_first_row').' '; ?></label>
                        <div class='col-sm-2'>
                            <?php echo form_dropdown('t_barcode_first_row', array(
                                'not_show' => $this->lang->line('config_none'),
                                'name' => $this->lang->line('items_name'),
                                'category' => $this->lang->line('items_category'),
                                'cost_price' => $this->lang->line('items_cost_price'),
                                'unit_price' => $this->lang->line('items_unit_price'),
                                'company_name' => $this->lang->line('suppliers_company_name')
                            ),
                            $this->config->item('t_barcode_first_row'), array('class' => 'form-control input-sm')); ?>
                        </div>
                        <label class="control-label col-sm-1"><?php echo $this->lang->line('config_barcode_second_row').' '; ?></label>
                        <div class='col-sm-2'>
                            <?php echo form_dropdown('t_barcode_second_row', array(
                                'not_show' => $this->lang->line('config_none'),
                                'name' => $this->lang->line('items_name'),
                                'category' => $this->lang->line('items_category'),
                                'cost_price' => $this->lang->line('items_cost_price'),
                                'unit_price' => $this->lang->line('items_unit_price'),
                                'item_code' => $this->lang->line('items_item_number'),
                                'company_name' => $this->lang->line('suppliers_company_name')
                            ),
                            $this->config->item('t_barcode_second_row'), array('class' => 'form-control input-sm')); ?>
                        </div>
                        <label class="control-label col-sm-1"><?php echo $this->lang->line('config_barcode_third_row').' '; ?></label>
                        <div class='col-sm-2'>
                            <?php echo form_dropdown('t_barcode_third_row', array(
                                'not_show' => $this->lang->line('config_none'),
                                'name' => $this->lang->line('items_name'),
                                'category' => $this->lang->line('items_category'),
                                'cost_price' => $this->lang->line('items_cost_price'),
                                'unit_price' => $this->lang->line('items_unit_price'),
                                'item_code' => $this->lang->line('items_item_number'),
                                'company_name' => $this->lang->line('suppliers_company_name')
                            ),
                            $this->config->item('t_barcode_third_row'), array('class' => 'form-control input-sm')); ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="form-group form-group-sm">    
				<?php echo form_label($this->lang->line('config_barcode_number_in_row'), 't_barcode_num_in_row', array('class' => 'control-label col-xs-2 required')); ?>
                <div class='col-xs-2'>
					<?php echo form_input(array(
						'name' => 't_barcode_num_in_row',
						'id' => 't_barcode_num_in_row',
						'class' => 'form-control input-sm required',
						'value'=>$this->config->item('t_barcode_num_in_row'))); ?>
                </div>
            </div>
            
            <div class="form-group form-group-sm">    
            <?php echo form_label($this->lang->line('config_barcode_page_width'), 't_barcode_page_width', array('class' => 'control-label col-xs-2 required')); ?>
                <div class="col-sm-2">
                    <div class='input-group'>
                        <?php echo form_input(array(
                            'name' => 't_barcode_page_width',
                            'id' => 't_barcode_page_width',
                            'class' => 'form-control input-sm required',
                            'value'=>$this->config->item('t_barcode_page_width'))); ?>
                        <span class="input-group-addon input-sm">mm</span>
                    </div>
                </div>
            </div>
            
            <div class="form-group form-group-sm">    
            <?php echo form_label($this->lang->line('config_barcode_page_cellspacing'), 't_barcode_page_cellspacing', array('class' => 'control-label col-xs-2 required')); ?>
                <div class='col-sm-2'>
                    <div class="input-group">
                        <?php echo form_input(array(
                            'name' => 't_barcode_page_cellspacing',
                            'id' => 't_barcode_page_cellspacing',
                            'class' => 'form-control input-sm required',
                            'value'=>$this->config->item('t_barcode_page_cellspacing'))); ?>
                        <span class="input-group-addon input-sm">mm</span>
                    </div>
                </div>
            </div>

            <div class="config-title">
                <b>Thiết lập Barcode Gọng 2 (Có tem chống hàng giả)</b>
            </div>
            <div class="form-group form-group-sm">    
				<?php echo form_label($this->lang->line('G1Barcode'), 'G1Barcode', array('class' => 'control-label col-xs-2')); ?>
                <div class='col-xs-2'>
                    <?php echo form_dropdown(
                            'G1Barcode', 
                            $support_template['Gong'], 
                            $this->config->item('G1Barcode')['template'], 
                            array('class' => 'form-control input-sm')); ?>
					
                </div>
            </div>

            <div class="form-group form-group-sm">    
				<?php echo form_label($this->lang->line('config_barcode_type'), 'g2_barcode_type', array('class' => 'control-label col-xs-2')); ?>
                <div class='col-xs-2'>
					<?php echo form_dropdown('g2_barcode_type', $support_barcode, $this->config->item('barcode_type'), array('class' => 'form-control input-sm')); ?>
                </div>
            </div>
            
            <div class="form-group form-group-sm">    
				<?php echo form_label($this->lang->line('config_barcode_quality'), 'barcode_quality', array('class' => 'control-label col-xs-2 required')); ?>
                <div class='col-xs-2'>
					<?php echo form_input(array(
						'max' => '100',
						'min' => '10',
						'type' => 'number',
						'name' => 'g2_barcode_quality',
						'id' => 'g2_barcode_quality',
						'class' => 'form-control input-sm required',
						'value'=>$this->config->item('g2_barcode_quality'))); ?>
                </div>
            </div>
            
            <div class="form-group form-group-sm">    
				<?php echo form_label($this->lang->line('config_barcode_width'), 'g2_barcode_width', array('class' => 'control-label col-xs-2 required')); ?>
                <div class='col-xs-2'>
					<?php echo form_input(array(
						'step' => '5',
						'max' => '350',
						'min' => '60',
						'type' => 'number',
						'name' => 'g2_barcode_width',
						'id' => 'g2_barcode_width',
						'class' => 'form-control input-sm required',
						'value'=>$this->config->item('g2_barcode_width'))); ?>
                </div>
            </div>

            <div class="form-group form-group-sm">    
				<?php echo form_label($this->lang->line('config_barcode_height'), 'g2_barcode_height', array('class' => 'control-label col-xs-2 required')); ?>
                <div class='col-xs-2'>
					<?php echo form_input(array(
						'type' => 'number',
						'min' => 10,
						'max' => 120,
						'name' => 'g2_barcode_height',
						'id' => 'g2_barcode_height',
						'class' => 'form-control input-sm required',
						'value'=>$this->config->item('g2_barcode_height'))); ?>
                </div>
            </div>
            
            <div class="form-group form-group-sm">    
				<?php echo form_label($this->lang->line('config_barcode_font'), 'g2_barcode_font', array('class' => 'control-label col-xs-2 required')); ?>
                <div class='col-sm-2'>
					<?php echo form_dropdown('g2_barcode_font', 
						$this->barcode_lib->listfonts("fonts"),
						$this->config->item('g2_barcode_font'), array('class' => 'form-control input-sm required'));
						?>
                </div>
                <div class="col-sm-2">
                    <?php echo form_input(array(
                        'type' => 'number',
                        'min' => '1',
                        'max' => '30',
                        'name' => 'g2_barcode_font_size',
                        'id' => 'g2_barcode_font_size',
                        'class' => 'form-control input-sm required',
                        'value'=>$this->config->item('g2_barcode_font_size'))); ?>
                </div>
            </div>
            
			<div class="form-group form-group-sm">
				<?php echo form_label($this->lang->line('config_barcode_content'), 'g2_barcode_content', array('class' => 'control-label col-xs-2')); ?>
				<div class='col-xs-8'>
                    <label class="radio-inline">
                        <?php echo form_radio(array(
                            'name' => 'g2_barcode_content',
                            'value' => 'id',
                            'checked'=>$this->config->item('g2_barcode_content') === "id")); ?>
                        <?php echo $this->lang->line('config_barcode_id'); ?>
                    </label>
					<label class="radio-inline">
                        <?php echo form_radio(array(
                            'name' => 'g2_barcode_content',
                            'value' => 'number',
                            'checked'=>$this->config->item('g2_barcode_content') === "number")); ?>
                        <?php echo $this->lang->line('config_barcode_number'); ?>
                    </label>
					<label class="checkbox-inline">
                        <?php echo form_checkbox(array(
                            'name' => 'g2_barcode_generate_if_empty',
                            'value' => 'g2_barcode_generate_if_empty',
                            'checked'=>$this->config->item('g2_barcode_generate_if_empty'))); ?>
                        <?php echo $this->lang->line('config_barcode_generate_if_empty'); ?>
                    </label>
				</div>
			</div>

            <div class="form-group form-group-sm">    
				<?php echo form_label($this->lang->line('config_barcode_layout'), 'g2_barcode_layout', array('class' => 'control-label col-xs-2')); ?>
                <div class="col-sm-10">
                    <div class="form-group form-group-sm row">
                        <label class="control-label col-sm-1"><?php echo $this->lang->line('config_barcode_first_row').' '; ?></label>
                        <div class='col-sm-2'>
                            <?php echo form_dropdown('g2_barcode_first_row', array(
                                'not_show' => $this->lang->line('config_none'),
                                'name' => $this->lang->line('items_name'),
                                'category' => $this->lang->line('items_category'),
                                'cost_price' => $this->lang->line('items_cost_price'),
                                'unit_price' => $this->lang->line('items_unit_price'),
                                'company_name' => $this->lang->line('suppliers_company_name')
                            ),
                            $this->config->item('g2_barcode_first_row'), array('class' => 'form-control input-sm')); ?>
                        </div>
                        <label class="control-label col-sm-1"><?php echo $this->lang->line('config_barcode_second_row').' '; ?></label>
                        <div class='col-sm-2'>
                            <?php echo form_dropdown('g2_barcode_second_row', array(
                                'not_show' => $this->lang->line('config_none'),
                                'name' => $this->lang->line('items_name'),
                                'category' => $this->lang->line('items_category'),
                                'cost_price' => $this->lang->line('items_cost_price'),
                                'unit_price' => $this->lang->line('items_unit_price'),
                                'item_code' => $this->lang->line('items_item_number'),
                                'company_name' => $this->lang->line('suppliers_company_name')
                            ),
                            $this->config->item('g2_barcode_second_row'), array('class' => 'form-control input-sm')); ?>
                        </div>
                        <label class="control-label col-sm-1"><?php echo $this->lang->line('config_barcode_third_row').' '; ?></label>
                        <div class='col-sm-2'>
                            <?php echo form_dropdown('g2_barcode_third_row', array(
                                'not_show' => $this->lang->line('config_none'),
                                'name' => $this->lang->line('items_name'),
                                'category' => $this->lang->line('items_category'),
                                'cost_price' => $this->lang->line('items_cost_price'),
                                'unit_price' => $this->lang->line('items_unit_price'),
                                'item_code' => $this->lang->line('items_item_number'),
                                'company_name' => $this->lang->line('suppliers_company_name')
                            ),
                            $this->config->item('g2_barcode_third_row'), array('class' => 'form-control input-sm')); ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="form-group form-group-sm">    
				<?php echo form_label($this->lang->line('config_barcode_number_in_row'), 'g2_barcode_num_in_row', array('class' => 'control-label col-xs-2 required')); ?>
                <div class='col-xs-2'>
					<?php echo form_input(array(
						'name' => 'g2_barcode_num_in_row',
						'id' => 'g2_barcode_num_in_row',
						'class' => 'form-control input-sm required',
						'value'=>$this->config->item('g2_barcode_num_in_row'))); ?>
                </div>
            </div>
            
            <div class="form-group form-group-sm">    
            <?php echo form_label($this->lang->line('config_barcode_page_width'), 'g2_barcode_page_width', array('class' => 'control-label col-xs-2 required')); ?>
                <div class="col-sm-2">
                    <div class='input-group'>
                        <?php echo form_input(array(
                            'name' => 'g2_barcode_page_width',
                            'id' => 'g2_barcode_page_width',
                            'class' => 'form-control input-sm required',
                            'value'=>$this->config->item('g2_barcode_page_width'))); ?>
                        <span class="input-group-addon input-sm">%</span>
                    </div>
                </div>
            </div>
            
            <div class="form-group form-group-sm">    
            <?php echo form_label($this->lang->line('config_barcode_page_cellspacing'), 'g2_barcode_page_cellspacing', array('class' => 'control-label col-xs-2 required')); ?>
                <div class='col-sm-2'>
                    <div class="input-group">
                        <?php echo form_input(array(
                            'name' => 'g2_barcode_page_cellspacing',
                            'id' => 'g2_barcode_page_cellspacing',
                            'class' => 'form-control input-sm required',
                            'value'=>$this->config->item('g2_barcode_page_cellspacing'))); ?>
                        <span class="input-group-addon input-sm">px</span>
                    </div>
                </div>
            </div>
    
    
            <?php echo form_submit(array(
                'name' => 'submit_form',
                'id' => 'submit_form',
                'value'=>$this->lang->line('common_submit'),
                'class' => 'btn btn-primary btn-sm pull-right')); ?>
        </fieldset>
    </div>
<?php echo form_close(); ?>
<script type="text/javascript">
//validation and submit handling
$(document).ready(function()
{
    $('#barcode_config_form').validate($.extend(form_support.handler, {

        errorLabelContainer: "#barcode_error_message_box",

        rules:
        {
            barcode_width: 
            {
                required:true,
                number:true
            },
            barcode_height: 
            {
                required:true,
                number:true
            },
            barcode_quality: 
            {
                required:true,
                number:true
            },
            barcode_font_size:
            {
                required:true,
                number:true
            },
            barcode_num_in_row:
            {
                required:true,
                number:true
            },
            barcode_page_width:
            {
                required:true,
                number:true
            },
            barcode_page_cellspacing:
            {
                required:true,
                number:true
            },
            /** Len */
            lens_barcode_width: 
            {
                required:true,
                number:true
            },
            lens_barcode_height: 
            {
                required:true,
                number:true
            },
            lens_barcode_quality: 
            {
                required:true,
                number:true
            },
            lens_barcode_font_size:
            {
                required:true,
                number:true
            },
            lens_barcode_num_in_row:
            {
                required:true,
                number:true
            },
            lens_barcode_page_width:
            {
                required:true,
                number:true
            },
            lens_barcode_page_cellspacing:
            {
                required:true,
                number:true
            },
            /** Gong 2 */ 
            g2_barcode_width: 
            {
                required:true,
                number:true
            },
            g2_barcode_height: 
            {
                required:true,
                number:true
            },
            g2_barcode_quality: 
            {
                required:true,
                number:true
            },
            g2_barcode_font_size:
            {
                required:true,
                number:true
            },
            g2_barcode_num_in_row:
            {
                required:true,
                number:true
            },
            g2_barcode_page_width:
            {
                required:true,
                number:true
            },
            g2_barcode_page_cellspacing:
            {
                required:true,
                number:true
            }
        },

        messages: 
        {
            barcode_width:
            {
                required:"Chiều rộng barcode cần nhập",
                number:"Dữ liệu chiều rộng barcode phải là một số"
            },
            barcode_height:
            {
                required:"Chiều cao barcode cần nhập",
                number:"Dữ cao chiều rộng barcode phải là một số"
            },
            barcode_quality:
            {
                required:"Chất lượng hình ảnh barcode cần nhập",
                number:"Dữ liệu chất lượng barcode phải là một số"
            },
            barcode_font_size:
            {
                required:"Cỡ chữ barcode cần nhập",
                number:"Dữ liệu cỡ chữ barcode phải là một số"
            },
            barcode_num_in_row:
            {
                required:"Số dòng barcode cần nhập",
                number:"Dữ liệu số dòng barcode phải là một số"
            },
            barcode_page_width:
            {
                required:"Chiều rộng trang barcode cần nhập",
                number:"Dữ liệu chiều rộng trang barcode phải là một số"
            },
            barcode_page_cellspacing:
            {
                required:"Khoảng cách các ô trang barcode cần nhập",
                number:"Dữ liệu khoảng cách ô trang barcode phải là một số"
            },
            /** Len */
            lens_barcode_width:
            {
                required:"Chiều rộng barcode tròng kính cần nhập",
                number:"Dữ liệu chiều rộng barcode phải là một số"
            },
            lens_barcode_height:
            {
                required:"Chiều cao barcode tròng kính cần nhập",
                number:"Dữ cao chiều rộng barcode phải là một số"
            },
            lens_barcode_quality:
            {
                required:"Chất lượng hình ảnh barcode tròng kính cần nhập",
                number:"Dữ liệu chất lượng barcode phải là một số"
            },
            lens_barcode_font_size:
            {
                required:"Cỡ chữ barcode tròng kính cần nhập",
                number:"Dữ liệu cỡ chữ barcode phải là một số"
            },
            lens_barcode_num_in_row:
            {
                required:"Số dòng barcode tròng kính cần nhập",
                number:"Dữ liệu số dòng barcode phải là một số"
            },
            lens_barcode_page_width:
            {
                required:"Chiều rộng trang barcode tròng kính cần nhập",
                number:"Dữ liệu chiều rộng trang barcode phải là một số"
            },
            lens_barcode_page_cellspacing:
            {
                required:"Khoảng cách các ô trang barcode tròng kính cần nhập",
                number:"Dữ liệu khoảng cách ô trang barcode phải là một số"
            },
            /**
             * Gong 2 */
            g2_barcode_width:
            {
                required:"Chiều rộng barcode gọng kính 2 cần nhập",
                number:"Dữ liệu chiều rộng barcode phải là một số"
            },
            g2_barcode_height:
            {
                required:"Chiều cao barcode gọng kính 2 cần nhập",
                number:"Dữ cao chiều rộng barcode phải là một số"
            },
            g2_barcode_quality:
            {
                required:"Chất lượng hình ảnh barcode gọng kính 2 cần nhập",
                number:"Dữ liệu chất lượng barcode phải là một số"
            },
            g2_barcode_font_size:
            {
                required:"Cỡ chữ barcode gọng kính 2 cần nhập",
                number:"Dữ liệu cỡ chữ barcode phải là một số"
            },
            g2_barcode_num_in_row:
            {
                required:"Số dòng barcode gọng kính 2 cần nhập",
                number:"Dữ liệu số dòng barcode phải là một số"
            },
            g2_barcode_page_width:
            {
                required:"Chiều rộng trang barcode gọng kính 2 cần nhập",
                number:"Dữ liệu chiều rộng trang barcode phải là một số"
            },
            g2_barcode_page_cellspacing:
            {
                required:"Khoảng cách các ô trang barcode gọng kính 2 cần nhập",
                number:"Dữ liệu khoảng cách ô trang barcode phải là một số"
            }                                 
        }
    }));
});
</script>
