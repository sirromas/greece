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
                                <?php $value = _d(date('Y-m-d h:i:s')); ?>
                                <?php echo render_datetime_input('startdate', '<small class="req text-danger">* </small>Start Date', $value); ?>
                            </div>
                            <div class="col-md-6">
                                <?php $value = _d(date('Y-m-d h:i:s')); ?>
                                <?php $value = (isset($task) ? _d($task->duedate) : ''); ?>
                                <?php echo render_datetime_input('duedate', '<small class="req text-danger">* </small>Due Date', $value, $project_end_date_attrs); ?>
                            </div>
                        </div> <!-- End of div row -->


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
                            </div>

                        </div> <!-- End of div row -->

                        <div class="row" style="margin-top:15px;">
                            <div class="col-md-12" >
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
                                    </select>
                                </div>
                            </div>
                        </div> <!-- End of div row -->


                        <div class="row">
                            <div class="col-md-12">
                                <p class="bold">
                                    <small class="req text-danger">*</small>
                                    Task Description
                                </p>
                                <?php echo render_textarea('description', '', (isset($task) ? $task->description : ''), array('rows' => 6, 'placeholder' => _l('task_add_description'), 'data-task-ae-editor' => true, 'onclick' => (!isset($task) || isset($task) && $task->description == '' ? 'init_editor(\'.tinymce-task\',{height:200,auto_focus: true});' : '')), array(), 'no-mbot', 'tinymce-task'); ?>
                            </div>
                        </div> <!-- End of div row -->


                        <div class="clearfix"></div>

                        <div class="row" style="margin-top: 15px;margin-bottom: 15px;">
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

                var name = $('#name').val();
                var startdate = $('#startdate').val();
                var duedate = $('#duedate').val();
                var priority = 2;
                var repeat_every = $('#repeat_every').val();
                var customerid = $('#customerid').val();
                var remind = $('#remind').val();
                var description = tinymce.activeEditor.getContent();

                var item = {
                    name: name,
                    startdate: startdate,
                    duedate: duedate,
                    priority: priority, repeat_every: repeat_every,
                    customerid: customerid,
                    remind: remind, description: description
                };
                console.log('Item: ' + JSON.stringify(item));

                if (name == '' || startdate == '' || duedate == '' || customerid == 0 || description == '') {
                    $('#task_err').html('Please provide required fields');
                } // end if
                else {
                    $('#task_err').html('');
                    var url = '/geocrm/admin/utilities/add_task_from_calendar';
                    $.post(url, {item: JSON.stringify(item)}).done(function (data) {
                        console.log(data);
                        // Hide modal dialog
                        $("[data-dismiss=modal]").trigger({type: "click"});
                        $("#newEventModal").remove();
                        $('.modal-backdrop').remove();
                        document.location.reload();
                    }); // end of post
                } // end else
            }

        }); // end of body click event
    }); // end of document ready

</script>