<?php

$ci = &get_instance();
$ci->load->model('utilities_model');

?>


<div class="modal fade _event" id="newEventModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">Add new task</h4>
            </div>

            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-12">
                                <?php echo render_input('name', '<small class="req text-danger">* </small>Subject', $value); ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <?php $value = _d(date('Y-m-d')); ?>
                                <?php echo render_date_input('startdate', '<small class="req text-danger">* </small>Start Date', $value); ?>
                            </div>
                            <div class="col-md-6">
                                <?php $value = _d(date('Y-m-d')); ?>
                                <?php $value = (isset($task) ? _d($task->duedate) : ''); ?>
                                <?php echo render_date_input('duedate', 'task_add_edit_due_date', $value, $project_end_date_attrs); ?>
                            </div>
                        </div>
                        <!-- End of div row -->

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="priority"
                                           class="control-label"><?php echo _l('task_add_edit_priority'); ?></label>
                                    <select name="priority" class="selectpicker" id="priority" data-width="100%"
                                            data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                        <option value="1" <?php if (isset($task) && $task->priority == 1 || !isset($task) && get_option('default_task_priority') == 1) {
                                            echo 'selected';
                                        } ?>><?php echo _l('task_priority_low'); ?></option>
                                        <option value="2" <?php if (isset($task) && $task->priority == 2 || !isset($task) && get_option('default_task_priority') == 2) {
                                            echo 'selected';
                                        } ?>><?php echo _l('task_priority_medium'); ?></option>
                                        <option value="3" <?php if (isset($task) && $task->priority == 3 || !isset($task) && get_option('default_task_priority') == 3) {
                                            echo 'selected';
                                        } ?>><?php echo _l('task_priority_high'); ?></option>
                                        <option value="4" <?php if (isset($task) && $task->priority == 4 || !isset($task) && get_option('default_task_priority') == 4) {
                                            echo 'selected';
                                        } ?>><?php echo _l('task_priority_urgent'); ?></option>
                                        <?php do_action('task_priorities_select', (isset($task) ? $task : 0)); ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="repeat_every"
                                           class="control-label"><?php echo _l('task_repeat_every'); ?></label>
                                    <select name="repeat_every" id="repeat_every" class="selectpicker" data-width="100%"
                                            data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                        <option value=""></option>
                                        <option value="1-week" <?php if (isset($task) && $task->repeat_every == 1 && $task->recurring_type == 'week') {
                                            echo 'selected';
                                        } ?>><?php echo _l('week'); ?></option>
                                        <option value="2-week" <?php if (isset($task) && $task->repeat_every == 2 && $task->recurring_type == 'week') {
                                            echo 'selected';
                                        } ?>>2 <?php echo _l('weeks'); ?></option>
                                        <option value="1-month" <?php if (isset($task) && $task->repeat_every == 1 && $task->recurring_type == 'month') {
                                            echo 'selected';
                                        } ?>>1 <?php echo _l('month'); ?></option>
                                        <option value="2-month" <?php if (isset($task) && $task->repeat_every == 2 && $task->recurring_type == 'month') {
                                            echo 'selected';
                                        } ?>>2 <?php echo _l('months'); ?></option>
                                        <option value="3-month" <?php if (isset($task) && $task->repeat_every == 3 && $task->recurring_type == 'month') {
                                            echo 'selected';
                                        } ?>>3 <?php echo _l('months'); ?></option>
                                        <option value="6-month" <?php if (isset($task) && $task->repeat_every == 6 && $task->recurring_type == 'month') {
                                            echo 'selected';
                                        } ?>>6 <?php echo _l('months'); ?></option>
                                        <option value="1-year" <?php if (isset($task) && $task->repeat_every == 1 && $task->recurring_type == 'year') {
                                            echo 'selected';
                                        } ?>>1 <?php echo _l('year'); ?></option>
                                        <option value="custom" <?php if (isset($task) && $task->custom_recurring == 1) {
                                            echo 'selected';
                                        } ?>><?php echo _l('recurring_custom'); ?></option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <!-- End of div row -->

                        <div class="row">

                            <div class="col-md-6">

                                <label for="customerid" class="control-label">
                                    <small class="req text-danger">*</small>
                                    Contact</label>
                                <select name="customerid" id="customerid" class="selectpicker" data-width="100%"
                                        data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                    <?php
                                    $customers = $ci->utilities_model->get_customers_list();
                                    echo "<option value='0' selected>Please select</option>";
                                    foreach ($customers as $customerid) {
                                        $name = $ci->utilities_model->get_customer_name_by_id($customerid);
                                        echo "<option value='$customerid'>$name</option>";
                                    }
                                    ?>
                                </select>
                            </div>


                            <div class="col-md-6">
                                <label for="remind" class="control-label">Remind me (days)</label>
                                <select name="remind" id="remind" class="selectpicker" data-width="100%"
                                        data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                    <?php
                                    $options = $ci->utilities_model->get_remider_options();
                                    echo "<option value='0' selected>Please select</option>";
                                    foreach ($options as $op) {
                                        echo "<option value='$op'>$op</option>";
                                    }
                                    ?>
                                </select>
                                <hr/>
                            </div>

                        </div>
                        <!-- End of div row -->

                        <div class="row">
                            <div class="col-md-12">
                                <p class="bold"><?php echo _l('task_add_edit_description'); ?></p>
                                <?php echo render_textarea('description', '', (isset($task) ? $task->description : ''), array('rows' => 6, 'placeholder' => _l('task_add_description'), 'data-task-ae-editor' => true, 'onclick' => (!isset($task) || isset($task) && $task->description == '' ? 'init_editor(\'.tinymce-task\',{height:200,auto_focus: true});' : '')), array(), 'no-mbot', 'tinymce-task'); ?>
                            </div>
                        </div>
                        <!-- End of div row -->

                        <div class="clearfix"></div>

                        <div class="row">
                            <div class="col-md-12" id="task_err" style="color: red;"></div>
                        </div>

                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                <button type="submit" class="btn btn-info"
                        id="add_new_task_calendar"><?php echo _l('submit'); ?></button>
            </div>

        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<script src="https://code.jquery.com/jquery-1.12.4.min.js"></script>

<script type="text/javascript">

    $(document).ready(function () {


        $("body").click(function (event) {

            if (event.target.id == 'add_new_task_calendar') {

                var description = '';
                var name = $('#name').val();
                var startdate = $('#startdate').val();
                var duedate = $('#duedate').val();
                var priority = 2;
                var repeat_every = $('#repeat_every').val();
                var customerid = $('#customerid').val();
                var remind = $('#remind').val();
                description = tinymce.activeEditor.getContent();


                if (name == '' || startdate == '' || customerid == 0) {
                    $('#task_err').html('Please provide required fields');
                } // end if
                else {
                    $('#task_err').html('');
                    var item = {
                        name: name,
                        startdate: startdate,
                        duedate: duedate,
                        priority: priority, repeat_every: repeat_every,
                        customerid: customerid,
                        remind: remind, description: description
                    };
                    console.log('Item: ' + JSON.stringify(item));
                    var url = '/geocrm/admin/utilities/add_task_from_calendar';
                    $.post(url, {item: JSON.stringify(item)}).done(function (data) {
                        console.log(data);
                        // Hide modal dialog
                        $("[data-dismiss=modal]").trigger({type: "click"});
                        $("#newEventModal").remove();
                        $('.modal-backdrop').remove();
                        window.location.href = window.location.href;
                    }); // end of post
                } // end else

            }

        }); // end of body click event

    }); // end of document ready

</script>