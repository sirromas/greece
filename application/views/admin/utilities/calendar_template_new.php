<div class="modal fade _event" id="newEventModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <!-- <h4 class="modal-title"><?php echo _l('utility_calendar_new_event_title'); ?></h4> -->
                <h4 class="modal-title">Add new task</h4>
            </div>
            <?php echo form_open('admin/utilities/calendar',array('id'=>'calendar-event-form')); ?>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <?php echo render_input('name','task_add_edit_subject',$value, array('required')); ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <?php $value = _d(date('Y-m-d')); ?>
                        <?php echo render_date_input('startdate','task_add_edit_start_date',$value); ?>
                    </div>
                    <div class="col-md-6">
                        <?php $value = _d(date('Y-m-d')); ?>
                        <?php $value = (isset($task) ? _d($task->duedate) : ''); ?>
                        <?php echo render_date_input('duedate','task_add_edit_due_date',$value,$project_end_date_attrs); ?>
                    </div>
                </div>
                <!-- End of div row -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="priority" class="control-label"><?php echo _l('task_add_edit_priority'); ?></label>
                            <select name="priority" class="selectpicker" id="priority" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                <option value="1" <?php if(isset($task) && $task->priority == 1 || !isset($task) && get_option('default_task_priority') == 1){echo 'selected';} ?>><?php echo _l('task_priority_low'); ?></option>
                                <option value="2" <?php if(isset($task) && $task->priority == 2 || !isset($task) && get_option('default_task_priority') == 2){echo 'selected';} ?>><?php echo _l('task_priority_medium'); ?></option>
                                <option value="3" <?php if(isset($task) && $task->priority == 3 || !isset($task) && get_option('default_task_priority') == 3){echo 'selected';} ?>><?php echo _l('task_priority_high'); ?></option>
                                <option value="4" <?php if(isset($task) && $task->priority == 4 || !isset($task) && get_option('default_task_priority') == 4){echo 'selected';} ?>><?php echo _l('task_priority_urgent'); ?></option>
                                <?php do_action('task_priorities_select',(isset($task)?$task:0)); ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="repeat_every" class="control-label"><?php echo _l('task_repeat_every'); ?></label>
                            <select name="repeat_every" id="repeat_every" class="selectpicker" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                <option value=""></option>
                                <option value="1-week" <?php if(isset($task) && $task->repeat_every == 1 && $task->recurring_type == 'week'){echo 'selected';} ?>><?php echo _l('week'); ?></option>
                                <option value="2-week" <?php if(isset($task) && $task->repeat_every == 2 && $task->recurring_type == 'week'){echo 'selected';} ?>>2 <?php echo _l('weeks'); ?></option>
                                <option value="1-month" <?php if(isset($task) && $task->repeat_every == 1 && $task->recurring_type == 'month'){echo 'selected';} ?>>1 <?php echo _l('month'); ?></option>
                                <option value="2-month" <?php if(isset($task) && $task->repeat_every == 2 && $task->recurring_type == 'month'){echo 'selected';} ?>>2 <?php echo _l('months'); ?></option>
                                <option value="3-month" <?php if(isset($task) && $task->repeat_every == 3 && $task->recurring_type == 'month'){echo 'selected';} ?>>3 <?php echo _l('months'); ?></option>
                                <option value="6-month" <?php if(isset($task) && $task->repeat_every == 6 && $task->recurring_type == 'month'){echo 'selected';} ?>>6 <?php echo _l('months'); ?></option>
                                <option value="1-year" <?php if(isset($task) && $task->repeat_every == 1 && $task->recurring_type == 'year'){echo 'selected';} ?>>1 <?php echo _l('year'); ?></option>
                                <option value="custom" <?php if(isset($task) && $task->custom_recurring == 1){echo 'selected';} ?>><?php echo _l('recurring_custom'); ?></option>
                            </select>
                        </div>
                    </div>
                </div>
                <!-- End of div row -->

                <div class="row">

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="rel_type" class="control-label"><?php echo _l('task_related_to'); ?></label>
                            <select name="rel_type" class="selectpicker" id="rel_type" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                <option value=""></option>
                                <option value="customer"
                                    <?php if(isset($task) || $this->input->get('rel_type')){if($rel_type == 'customer'){echo 'selected';}} ?>>
                                    <?php echo _l('client'); ?>
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group<?php if($rel_id == ''){echo ' hide';} ?>" id="rel_id_wrapper">
                            <label for="rel_id" class="control-label"><span class="rel_id_label"></span></label>
                            <div id="rel_id_select">
                                <select name="rel_id" id="rel_id" class="ajax-sesarch" data-width="100%" data-live-search="true" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                    <?php if($rel_id != '' && $rel_type != ''){
                                        $rel_data = get_relation_data($rel_type,$rel_id);
                                        $rel_val = get_relation_values($rel_data,$rel_type);
                                        echo '<option value="'.$rel_val['id'].'" selected>'.$rel_val['name'].'</option>';
                                    } ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <!-- End of div row -->

                </div>
                <!-- End of modal-body div --->

                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                    <button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
                </div>



                <?php echo form_close(); ?>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

