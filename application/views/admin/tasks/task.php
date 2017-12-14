<?php

$ci = &get_instance();
$ci->load->model('utilities_model');

//echo form_open_multipart(admin_url('tasks/task/'.$id),array('id'=>'task-form')); ?>
<div class="modal fade<?php if (isset($task)) {
    echo ' edit';
} ?>" id="_task_modal" tabindex="-1" role="dialog"
     aria-labelledby="myModalLabel"<?php if ($this->input->get('opened_from_lead_id')) {
    echo 'data-lead-id=' . $this->input->get('opened_from_lead_id');
} ?>>
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="myModalLabel">
                    <?php echo $title; ?>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">

                    <!-- Task subject -->
                    <div class="col-md-12">
                        <?php
                        $rel_type = '';
                        $rel_id = '';
                        $querytsring = $_SERVER['QUERY_STRING'];
                        if ($querytsring != '') {
                            $clientid = $_REQUEST['rel_id'];
                            //echo "Customer ID: ".$task_customer_id."<br>";
                        }
                        if (isset($task) || ($this->input->get('rel_id') && $this->input->get('rel_type'))) {
                            echo "<input type='hidden' id='current_taskid' value='$task->id'>";
                            $new_task = 0;
                            if ($this->input->get('rel_id')) {
                                $rel_id = $this->input->get('rel_id');
                                $rel_type = $this->input->get('rel_type');
                            } // end if
                            else {
                                $rel_id = $task->rel_id;
                                $rel_type = $task->rel_type;
                            } // end else
                            ?>
                            <div class="clearfix"></div>
                        <?php } // end if isset($task)
                        else {
                            $new_task = 1;
                            echo "<input type='hidden' id='current_taskid' value='0'>";
                        } // end else
                        ?>
                        <?php $value = (isset($task) ? $task->name : ''); ?>
                        <?php echo render_input('name', '<small class="req text-danger">*</small> Subject', $value); ?>

                    </div> <!-- end of div class="col-md-12" -->
                </div> <!-- <div class="row">  -->

                <div class="row">
                    <div class="col-md-6">
                        <?php if (isset($task)) {
                            $value = _d($task->startdate);
                        } else if (isset($start_date)) {
                            $value = $start_date;
                        } else {
                            $value = _d(date('Y-m-d h:i:s'));
                        }
                        ?>
                        <?php echo render_datetime_input('startdate', '<small class="req text-danger">*</small> Start Date', $value); ?>
                    </div>
                    <div class="col-md-6">
                        <?php $value = (isset($task) ? _d($task->duedate) : ''); ?>
                        <?php echo render_datetime_input('duedate', '<small class="req text-danger">*</small> Due Date', $value, $project_end_date_attrs); ?>
                    </div>

                </div> <!-- End of div class='row' -->

                <div class="row">

                    <!-- Select with customer preselected  -->
                    <div class="col-md-6">
                        <label for="customerid" class="control-label">
                            <small class="req text-danger">*</small>
                            Contact <?php // echo "Query string: $querytsring"; ?> </label>
                        <select name="customerid" id="customerid" class="selectpicker" data-width="100%"
                                data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                            <?php
                            $customers = $ci->utilities_model->get_customers_list();
                            if ($rel_id == '') {
                                echo "<option value='0' selected>Please select</option>";
                                foreach ($customers as $customerid) {
                                    $name = $ci->utilities_model->get_customer_name_by_id($customerid);
                                    echo "<option value='$customerid'>$name</option>";
                                } // end foreach
                            } // end if $rel_id == ''
                            else {
                                $task_customer_id = $ci->utilities_model->get_task_customer_id($task->id);
                                foreach ($customers as $customerid) {
                                    $name = $ci->utilities_model->get_customer_name_by_id($customerid);
                                    if ($querytsring == '') {
                                        if ($new_task == 0) {
                                            if ($task_customer_id == $customerid) {
                                                echo "<option value='$customerid' selected>$name</option>";
                                            } // end if
                                        } // end if $new_task==0
                                        else {
                                            echo "<option value='$customerid' >$name</option>";
                                        } // end else
                                    } // end if $querytsring==''
                                    else {
                                        if ($customerid== $clientid) {
                                            $name = $ci->utilities_model->get_customer_name_by_id($clientid);
                                            echo "<option value='$customerid' selected>$name</option>";
                                        } // end if
                                    } // end else
                                } // end foreach
                            } // end else
                            ?>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label for="remind" class="control-label">Remind me (days)</label>
                        <?php $taskid = (isset($task) ? $task->id : 0);
                        $reminders_select = $ci->utilities_model->get_customer_reminder_options($taskid);
                        echo $reminders_select;
                        ?>
                    </div>

                </div> <!-- End of div class="row" -->

                <div class="row" style="margin-top: 15px;">

                    <div class="col-md-12">
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

                </div> <!-- End of div class="row" -->

                <p class="bold">
                    <small class="req text-danger">*</small>
                    Task description
                </p>
                <?php echo render_textarea('description', '', (isset($task) ? $task->description : ''), array('rows' => 6, 'placeholder' => _l('task_add_description'), 'data-task-ae-editor' => true, 'onclick' => (!isset($task) || isset($task) && $task->description == '' ? 'init_editor(\'.tinymce-task\',{height:200,auto_focus: true});' : '')), array(), 'no-mbot', 'tinymce-task'); ?>

                <div class="clearfix"></div>

                <div class="row" style="margin-top: 15px;margin-bottom: 15px;">
                    <div class="col-md-12" id="task_err" style="color: red;"></div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default"
                            data-dismiss="modal"><?php echo _l('close'); ?></button>
                    <button id='add_new_task_calendar' type="submit"
                            class="btn btn-info"><?php echo _l('submit'); ?></button>
                </div>
            </div>
        </div>

    </div> <!-- End of div class="modal-dialog" role="document" -->
</div>

<?php // echo form_close(); ?>
<script>

    /* Custom code */

    $("body").click(function (event) {


        if (event.target.id == 'add_new_task_calendar') {

            var taskid = $('#current_taskid').val();
            console.log('Task id: '+taskid);
            var name = $('#name').val();
            var startdate = $('#startdate').val();
            var duedate = $('#duedate').val();
            var priority = 2;
            var repeat_every = $('#repeat_every').val();
            var customerid = $('#customerid').val();
            var remind = $('#remind').val();
            var description = tinymce.activeEditor.getContent();

            var item = {
                taskid: taskid,
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
                if (taskid == 0) {
                    var url = '/geocrm/admin/utilities/add_task_from_calendar';
                } // end if
                else {
                    var url = '/geocrm/admin/utilities/update_calendar_task';
                }
                $.post(url, {item: JSON.stringify(item)}).done(function (data) {
                    console.log(data);
                    // Hide modal dialog
                    $("[data-dismiss=modal]").trigger({type: "click"});
                    $("#_task_modal").remove();
                    $('.modal-backdrop').remove();
                    document.location.reload();
                }); // end of post
            } // end else
        }
        

    }); // end of body click event


    /* Original code */

    var _rel_id = $('#rel_id'),
        _rel_type = $('#rel_type'),
        _rel_id_wrapper = $('#rel_id_wrapper'),
        data = {};

    var _milestone_selected_data;
    _milestone_selected_data = undefined;

    $(function () {

        $("body").off("change", "#rel_id");

        var inner_popover_template = '<div class="popover"><div class="arrow"></div><div class="popover-inner"><h3 class="popover-title"></h3><div class="popover-content"></div></div></div>';

        $('#_task_modal .task-menu-options .trigger').popover({
            html: true,
            placement: "bottom",
            trigger: 'click',
            title: "<?php echo _l('actions'); ?>",
            content: function () {
                return $('body').find('#_task_modal .task-menu-options .content-menu').html();
            },
            template: inner_popover_template
        });

        custom_fields_hyperlink();


        _validate_form($('#task-form'), {
            name: 'required',
            startdate: 'required'
        }, task_form_handler);

        $('.rel_id_label').html(_rel_type.find('option:selected').text());
        _rel_type.on('change', function () {
            var clonedSelect = _rel_id.html('').clone();
            _rel_id.selectpicker('destroy').remove();
            _rel_id = clonedSelect;
            $('#rel_id_select').append(clonedSelect);
            $('.rel_id_label').html(_rel_type.find('option:selected').text());

            task_rel_select();
            if ($(this).val() != '') {
                _rel_id_wrapper.removeClass('hide');
            } else {
                _rel_id_wrapper.addClass('hide');
            }
            init_project_details(_rel_type.val());
        });

        init_datepicker();
        init_color_pickers();
        init_selectpicker();
        task_rel_select();
        $('body').on('change', '#rel_id', function () {
            if ($(this).val() != '') {
                if (_rel_type.val() == 'project') {
                    $.get(admin_url + 'projects/get_rel_project_data/' + $(this).val() + '/' + taskid, function (project) {
                        $("select[name='milestone']").html(project.milestones);
                        if (typeof(_milestone_selected_data) != 'undefined') {
                            $("select[name='milestone']").val(_milestone_selected_data.id);
                            $('input[name="duedate"]').val(_milestone_selected_data.due_date)
                        }
                        $("select[name='milestone']").selectpicker('refresh');
                        if (project.billing_type == 3) {
                            $('.task-hours').addClass('project-task-hours');
                        } else {
                            $('.task-hours').removeClass('project-task-hours');
                        }
                        init_project_details(_rel_type.val(), project.allow_to_view_tasks);
                    }, 'json');
                }
            }
        });

        <?php if(!isset($task) && $rel_id != ''){ ?>
        _rel_id.change();
        <?php } ?>

    });

    <?php if(isset($_milestone_selected_data)){ ?>
    _milestone_selected_data = '<?php echo json_encode($_milestone_selected_data); ?>';
    _milestone_selected_data = JSON.parse(_milestone_selected_data);
    <?php } ?>

    function task_rel_select() {
        var serverData = {};
        serverData.rel_id = _rel_id.val();
        data.type = _rel_type.val();
        <?php if(isset($task)){ ?>
        data.connection_type = 'task';
        data.connection_id = '<?php echo $task->id; ?>';
        <?php } ?>
        init_ajax_search(_rel_type.val(), _rel_id, serverData);
    }

    function init_project_details(type, tasks_visible_to_customer) {
        var wrap = $('.non-project-details');
        var wrap_task_hours = $('.task-hours');
        if (type == 'project') {
            if (wrap_task_hours.hasClass('project-task-hours') == true) {
                wrap_task_hours.removeClass('hide');
            } else {
                wrap_task_hours.addClass('hide');
            }
            wrap.addClass('hide');
            $('.project-details').removeClass('hide');
        } else {
            wrap_task_hours.removeClass('hide');
            wrap.removeClass('hide');
            $('.project-details').addClass('hide');
            $('.task-visible-to-customer').addClass('hide').prop('checked', false);
        }
        if (typeof(tasks_visible_to_customer) != 'undefined') {
            if (tasks_visible_to_customer == 1) {
                $('.task-visible-to-customer').removeClass('hide');
                $('.task-visible-to-customer input').prop('checked', true);
            } else {
                $('.task-visible-to-customer').addClass('hide')
                $('.task-visible-to-customer input').prop('checked', false);
            }
        }
    }
</script>
