<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Utilities_model extends CRM_Model
{
    public function __construct()
    {
        parent::__construct();

    }


    /*************************************************************************************************
     *
     *                                      Custom Code
     *
     **************************************************************************************************/


    public function delete_task($taskid)
    {
        $this->delete_task_followers($taskid);
        $this->delete_task_comments($taskid);
        $this->delet_task_custom_fields($taskid);
        $this->delete_task_assignees($taskid);
        $query = "delete from tblstafftasks where id=$taskid";
        $this->db->query($query);
    }

    /**
     * @param $taskid
     */
    public function delet_task_custom_fields($taskid)
    {
        $query = "delete from tblcustomfieldsvalues where relid=$taskid";
        $this->db->query($query);
    }

    /**
     * @param $taskid
     */
    public function delete_task_assignees($taskid)
    {
        $query = "delete from tblstafftaskassignees where taskid=$taskid";
        $this->db->query($query);
    }


    /**
     * @param $taskid
     */
    public function delete_task_comments($taskid)
    {
        $query = "delete from tblstafftaskcomments where taskid=$taskid";
        $this->db->query($query);
    }

    /**
     * @param $taskid
     */
    public function delete_task_followers($taskid)
    {
        $query = "delete from tblstafftasksfollowers where taskid=$taskid";
        $this->db->query($query);
    }

    /**
     * @param $stamp
     * @return string
     */
    public function get_euro_date_time_value($stamp)
    {
        $data = explode(' ', $stamp);
        $date = $data[0];
        $time = $data[1];
        $fdate = str_replace('/', '-', $date);
        $mdate = date('Y-m-d', strtotime($fdate));
        $eurodate = $mdate . ' ' . $time;
        return $eurodate;
    }


    /**
     * @param $data
     */
    public function update_calendar_task($data)
    {
        $taskid = $data->taskid;
        $name = $data->name;
        $description = $data->description;
        $startdate = $this->get_euro_date_time_value($data->startdate);
        $duedate = $this->get_euro_date_time_value($data->duedate);
        $remind = $data->remind; // custom field
        $recurringArr = explode('-', $data->repeat_every);
        $repeat_every = $recurringArr[0];
        $recurring_type = $recurringArr[1];
        $recurring = ($repeat_every == 0) ? 0 : 1;

        $query = "update tblstafftasks 
                    set name='$name', 
                        description='$description',
                        startdate='$startdate',
                        duedate='$duedate',
                        recurring_type='$recurring_type',
                        repeat_every='$repeat_every',
                        recurring='$recurring'      
                        where id=$taskid";
        echo "Query: " . $query . "<br>";
        $this->db->query($query);
        $this->update_task_existing_field($taskid, $remind);

        if ($repeat_every > 0) {
            $this->create_recurring_task_instances($data, $repeat_every, $recurring_type, false);
        }
    }

    /**
     * @param $data
     * @param $repeat_every
     * @param $recurring_type
     * @param bool $new
     */
    public function create_recurring_task_instances($data, $repeat_every, $recurring_type, $new = true)
    {
        $prefix = '+' . $repeat_every . ' ' . $recurring_type;
        $euro_start_date = $this->get_euro_date_time_value($data->startdate);
        $euro_due_date = $this->get_euro_date_time_value($data->duedate);
        $startdate = strtotime($euro_start_date); // unix timestamp
        $duedate = strtotime($euro_due_date); // unix timestamp
        $ftime = $startdate; // unix timestamp
        while ($ftime < $duedate) {
            if ($new == false) {
                // Exsisting task updated ...
                if ($ftime != $startdate) {
                    if (date('Y-m-d', $ftime) != date('Y-m-d', $duedate)) {
                        $data->startdate = date('Y-m-d h:i:s', $ftime);
                        $data->duedate = date('Y-m-d h:i:s', ($ftime + 60));
                        $data->repeat_every = '';
                        $this->add_task_from_calendar($data);
                    }
                } // end if $ftime!=$startdate
            } // end if
            else {
                // New task is added ....
                $data->startdate = date('Y-m-d h:i:s', $ftime);
                $data->duedate = date('Y-m-d h:i:s', ($ftime + 60));
                $data->repeat_every = '';
                $this->add_task_from_calendar($data);
            } // end else
            $ftime = strtotime($prefix, $ftime);
        } // end while
    }


    public function days_between($datefrom, $dateto)
    {
        $fromday_start = mktime(0, 0, 0, date("m", $datefrom), date("d", $datefrom), date("Y", $datefrom));
        $diff = $dateto - $datefrom;
        $days = intval($diff / 86400); // 86400  / day

        if (($datefrom - $fromday_start) + ($diff % 86400) > 86400)
            $days++;
        return $days;
    }


    /**
     * @param $startdate
     * @param $duedate
     * @return mixed
     */
    public function is_task_exists($startdate, $duedate)
    {
        $query = "select * from tblstafftasks 
                  where DAYOFMONTH(startdate)=DAYOFMONTH(duedate)";
        $result = $this->db->query($query);
        $num = $result->num_rows();
        return $num;
    }

    /**
     * @param $data
     */
    public function add_task_from_calendar($data)
    {

        $name = $data->name;
        $description = $data->description;
        $addedfrom = 1;
        $status = 4;
        $priority = 2;
        $dateadded = date('Y-m-d h:i:s', time());
        $startdate = $this->get_euro_date_time_value($data->startdate);
        $duedate = $this->get_euro_date_time_value($data->duedate);
        $remind = $data->remind;
        $recurringArr = explode('-', $data->repeat_every);
        $repeat_every = $recurringArr[0];
        $recurring_type = $recurringArr[1];
        $datefinished = '0000-00-00 00:00:00';
        $recurring = ($repeat_every == 0) ? 0 : 1;
        $customerid = $data->customerid;
        if ($customerid > 0) {
            $rel_id = $customerid;
            $rel_type = 'customer';
        } // end if
        else {
            $rel_id = null;
            $rel_type = null;
        } // end else
        $is_public = 1;


        $query = "insert into tblstafftasks 
                       (name,
                       description,
                       priority,
                       dateadded, 
                       startdate,
                       duedate,
                       datefinished,
                       addedfrom,
                       status,
                       recurring_type,
                       repeat_every,
                       recurring,
                       rel_id,
                       rel_type,
                       is_public) 
                       values ('$name',
                               '$description',
                                '$priority', 
                                '$dateadded',
                                '$startdate',
                                '$duedate',
                                '$datefinished',
                                '$addedfrom', 
                                '$status',
                                '$recurring_type',
                                '$repeat_every',
                                '$recurring',
                                '$rel_id',
                                '$rel_type',
                                '$is_public')";
        echo "Query: " . $query . "<br>";
        $this->db->query($query);
        $id = $this->db->insert_id();

        if ($id > 0) {
            if ($remind > 0) {
                $this->update_task_custom_fields($id, $remind);
            } // end if $remind > 0
            $this->add_task_assignees($id);

            if ($repeat_every > 0) {
                $this->delete_task($id);
                $this->create_recurring_task_instances($data, $repeat_every, $recurring_type);
            }

        } // end if $id > 0

    }

    /**
     * @param $taskid
     * @param $value
     */
    public function update_task_custom_fields($taskid, $value)
    {
        $query = "insert into tblcustomfieldsvalues 
                    (relid,
                    fieldid,
                    fieldto,
                    value) 
                    values ($taskid,12,'tasks', '$value')";
        $this->db->query($query);
    }

    /**
     * @param $taskid
     * @param $value
     */
    public function update_task_existing_field($taskid, $value)
    {
        $status = $this->is_task_custom_field_exists($taskid);
        if ($status > 0) {
            $query = "update tblcustomfieldsvalues 
                        set value='$value' where relid=$taskid";
            $this->db->query($query);
        } // end if $status>0
        else {
            $this->update_task_custom_fields($taskid, $value);
        }
    }


    public function is_task_custom_field_exists($taskid)
    {
        $query = "select * from  tblcustomfieldsvalues where relid=$taskid";
        $result = $this->db->query($query);
        $num = $result->num_rows();
        return $num;
    }

    /**
     * @param $taskid
     */
    public function add_task_assignees($taskid)
    {
        $staffusers = $this->get_staff_ids();
        foreach ($staffusers as $staffid) {
            $query = "insert into tblstafftaskassignees 
                      (staffid,
                      taskid,
                      assigned_from) 
                      values ($staffid,$taskid,1)";
            $this->db->query($query);
        } // end foreach
    }

    /**
     * @return array
     */
    public function get_staff_ids()
    {
        $query = "select * from tblstaff";
        $result = $this->db->query($query);
        foreach ($result->result() as $row) {
            $ids[] = $row->staffid;
        }
        return $ids;
    }

    /**
     * @return array
     */
    public function get_customers_list()
    {
        $query = "select * from tblclients order by company";
        $result = $this->db->query($query);
        foreach ($result->result() as $row) {
            $users[] = $row->userid;
        }
        return $users;
    }

    /**
     * @param $userid
     * @return mixed
     */
    public function get_customer_name_by_id($userid)
    {
        $query = "select * from tblclients where userid=$userid";
        $result = $this->db->query($query);
        foreach ($result->result() as $row) {
            $name = $row->company;
        }
        return $name;
    }

    /**
     * @return array
     */
    public function get_remider_options()
    {
        $query = "select * from tblcustomfields where id=12";
        $result = $this->db->query($query);
        foreach ($result->result() as $row) {
            $options = $row->options;
        }
        $options_arr = explode(',', $options);
        return $options_arr;
    }

    /**
     * @param $taskid
     * @return mixed
     */
    public function get_task_customer_id($taskid)
    {
        if ($taskid > 0) {
            $query = "select * from tblstafftasks where id=$taskid";
            $result = $this->db->query($query);
            foreach ($result->result() as $row) {
                $relid = $row->rel_id;
            }
        } // end if
        else {
            $relid = 0;
        }
        return $relid;
    }

    /**
     * @param $taskid
     * @return string
     */
    public function get_customer_reminder_options($taskid)
    {
        $list = "";
        $list .= "<select name='remind' id='remind' class='selectpicker' data-width='100%'";
        $list .= "<option value='0' selected>ΕΠΙΛΕΞΤΕ</option>";

        $query = "select * from tblcustomfields where id=12";
        $result = $this->db->query($query);
        foreach ($result->result() as $row) {
            $optionsArr = explode(',', $row->options);
        }

        if ($taskid > 0) {
            $query = "select * from tblcustomfieldsvalues 
                  where fieldto='tasks' and 
                        fieldid=12 and relid=$taskid";
            $result = $this->db->query($query);
            foreach ($result->result() as $row) {
                $value = $row->value;
            }
            foreach ($optionsArr as $item) {
                if ($item == $value) {
                    $list .= "<option value='$item' selected>$item</option>";
                } // end if
                else {
                    $list .= "<option value='$item'>$item</option>";
                } // end else
            } // end foreach
        } // end if $taskid > 0
        else {
            $list .= "<option value='0' selected>ΕΠΙΛΕΞΤΕ</option>";
            foreach ($optionsArr as $item) {
                $list .= "<option value='$item'>$item</option>";
            } // end foreach
        } // end else
        $list .= "</select>";
        return $list;
    }

    /*************************************************************************************************
     *
     *                                      Original Code
     *
     **************************************************************************************************/

    /**
     * Add new event
     * @param array $data event $_POST data
     */
    public function event($data)
    {
        $data['userid'] = get_staff_user_id();
        $data['start'] = to_sql_date($data['start'], true);
        if ($data['end'] == '') {
            unset($data['end']);
        } else {
            $data['end'] = to_sql_date($data['end'], true);
        }
        if (isset($data['public'])) {
            $data['public'] = 1;
        } else {
            $data['public'] = 0;
        }
        $data['description'] = nl2br($data['description']);
        if (isset($data['eventid'])) {
            unset($data['userid']);
            $this->db->where('eventid', $data['eventid']);
            $event = $this->db->get('tblevents')->row();
            if (!$event) {
                return false;
            }
            if ($event->isstartnotified == 1) {
                if ($data['start'] > $event->start) {
                    $data['isstartnotified'] = 0;
                }
            }

            $this->db->where('eventid', $data['eventid']);
            $this->db->update('tblevents', $data);
            if ($this->db->affected_rows() > 0) {
                return true;
            }

            return false;
        } else {

            $this->db->insert('tblevents', $data);
            $insert_id = $this->db->insert_id();
        }
        if ($insert_id) {
            return true;
        }

        return false;
    }

    /**
     * Get event by passed id
     * @param  mixed $id eventid
     * @return object
     */
    public function get_event_by_id($id)
    {
        $this->db->where('eventid', $id);

        return $this->db->get('tblevents')->row();
    }

    /**
     * Get all user events
     * @return array
     */
    public function get_all_events($start, $end)
    {
        $is_staff_member = is_staff_member();
        $this->db->select('title,start,end,eventid,userid,color,public');
        // Check if is passed start and end date
        $this->db->where('(start BETWEEN "' . $start . '" AND "' . $end . '")');
        $this->db->where('userid', get_staff_user_id());
        if ($is_staff_member) {
            $this->db->or_where('public', 1);
        }

        return $this->db->get('tblevents')->result_array();
    }

    /**
     * @param $id
     * @return mixed
     */
    public function get_event($id)
    {
        $this->db->where('eventid', $id);

        return $this->db->get('tblevents')->row();
    }

    /**
     * @param $start
     * @param $end
     * @param string $client_id
     * @param string $contact_id
     * @param bool $filters
     * @return array
     */
    public function get_calendar_data($start, $end, $client_id = '', $contact_id = '', $filters = false)
    {
        $is_admin = is_admin();
        $has_permission_invoices = has_permission('invoices', '', 'view');
        $has_permission_invoices_own = has_permission('invoices', '', 'view_own');
        $has_permission_estimates = has_permission('estimates', '', 'view');
        $has_permission_estimates_own = has_permission('estimates', '', 'view_own');
        $has_permission_contracts = has_permission('contracts', '', 'view');
        $has_permission_contracts_own = has_permission('contracts', '', 'view_own');
        $has_permission_proposals = has_permission('proposals', '', 'view');
        $has_permission_proposals_own = has_permission('proposals', '', 'view_own');
        $data = array();

        $client_data = false;
        if (is_numeric($client_id) && is_numeric($contact_id)) {
            $client_data = true;
            $has_contact_permission_invoices = has_contact_permission('invoices', $contact_id);
            $has_contact_permission_estimates = has_contact_permission('estimates', $contact_id);
            $has_contact_permission_proposals = has_contact_permission('proposals', $contact_id);
            $has_contact_permission_contracts = has_contact_permission('contracts', $contact_id);
            $has_contact_permission_projects = has_contact_permission('projects', $contact_id);
        }

        $hook_data = array(
            'data' => $data,
            'client_data' => $client_data
        );

        if ($client_data == true) {
            $hook_data['client_id'] = $client_id;
            $hook_data['contact_id'] = $contact_id;
        }

        $hook_data = do_action('before_fetch_events', $hook_data);
        $data = $hook_data['data'];

        // excluded calendar_filters from post
        $ff = (count($filters) > 1 && isset($filters['calendar_filters']) ? true : false);

        if (get_option('show_invoices_on_calendar') == 1 && !$ff || $ff && array_key_exists('invoices', $filters)) {
            $this->db->select('duedate as date,number,id,clientid,hash');
            $this->db->from('tblinvoices');
            $this->db->where_not_in('status', array(
                2,
                5
            ));

            $this->db->where('(duedate BETWEEN "' . $start . '" AND "' . $end . '")');

            if ($client_data) {
                $this->db->where('clientid', $client_id);

                if (get_option('exclude_invoice_from_client_area_with_draft_status') == 1) {
                    $this->db->where('status !=', 6);
                }
            } else {
                if (!$has_permission_invoices) {
                    $this->db->where('addedfrom', get_staff_user_id());
                }
            }
            $invoices = $this->db->get()->result_array();
            foreach ($invoices as $invoice) {
                if (!$has_permission_invoices && !$has_permission_invoices_own && !$client_data) {
                    continue;
                } elseif ($client_data && !$has_contact_permission_invoices) {
                    continue;
                }

                $rel_showcase = '';

                /**
                 * Show company name on calendar tooltip for admins
                 */
                if (!$client_data) {
                    $rel_showcase = ' (' . get_company_name($invoice['clientid']) . ')';
                }

                $number = format_invoice_number($invoice['id']);

                $invoice['_tooltip'] = _l('calendar_invoice') . ' - ' . $number . $rel_showcase;
                $invoice['title'] = $number;
                $invoice['color'] = get_option('calendar_invoice_color');

                if (!$client_data) {
                    $invoice['url'] = admin_url('invoices/list_invoices/' . $invoice['id']);
                } else {
                    $invoice['url'] = site_url('viewinvoice/' . $invoice['id'] . '/' . $invoice['hash']);
                }

                array_push($data, $invoice);
            }
        }
        if (get_option('show_estimates_on_calendar') == 1 && !$ff || $ff && array_key_exists('estimates', $filters)) {


            $this->db->select('number,id,clientid,hash,CASE WHEN expirydate IS NULL THEN date ELSE expirydate END as date', false);
            $this->db->from('tblestimates');

            $this->db->where('status !=', 3, false);
            $this->db->where('status !=', 4, false);
            // $this->db->where('expirydate IS NOT NULL');

            $this->db->where("CASE WHEN expirydate IS NULL THEN (date BETWEEN '$start' AND '$end') ELSE (expirydate BETWEEN '$start' AND '$end') END", null, false);

            if ($client_data) {
                $this->db->where('clientid', $client_id, false);

                if (get_option('exclude_estimate_from_client_area_with_draft_status') == 1) {
                    $this->db->where('status !=', 1, false);
                }
            } else {
                if (!$has_permission_estimates) {
                    $this->db->where('addedfrom', get_staff_user_id(), false);
                }
            }

            $estimates = $this->db->get()->result_array();

            foreach ($estimates as $estimate) {
                if (!$has_permission_estimates && !$has_permission_estimates_own && !$client_data) {
                    continue;
                } elseif ($client_data && !$has_contact_permission_estimates) {
                    continue;
                }

                $rel_showcase = '';
                if (!$client_data) {
                    $rel_showcase = ' (' . get_company_name($estimate['clientid']) . ')';
                }

                $number = format_estimate_number($estimate['id']);
                $estimate['_tooltip'] = _l('calendar_estimate') . ' - ' . $number . $rel_showcase;
                $estimate['title'] = $number;
                $estimate['color'] = get_option('calendar_estimate_color');
                if (!$client_data) {
                    $estimate['url'] = admin_url('estimates/list_estimates/' . $estimate['id']);
                } else {
                    $estimate['url'] = site_url('viewestimate/' . $estimate['id'] . '/' . $estimate['hash']);
                }
                array_push($data, $estimate);
            }
        }
        if (get_option('show_proposals_on_calendar') == 1 && !$ff || $ff && array_key_exists('proposals', $filters)) {
            $this->db->select('subject,id,hash,CASE WHEN open_till IS NULL THEN date ELSE open_till END as date', false);
            $this->db->from('tblproposals');
            $this->db->where('status !=', 2, false);
            $this->db->where('status !=', 3, false);


            $this->db->where("CASE WHEN open_till IS NULL THEN (date BETWEEN '$start' AND '$end') ELSE (open_till BETWEEN '$start' AND '$end') END", null, false);

            if ($client_data) {
                $this->db->where('rel_type', 'customer');
                $this->db->where('rel_id', $client_id, false);

                if (get_option('exclude_proposal_from_client_area_with_draft_status')) {
                    $this->db->where('status !=', 6, false);
                }

            } else {
                if (!$has_permission_proposals) {
                    $this->db->where('addedfrom', get_staff_user_id(), false);
                }
            }

            $proposals = $this->db->get()->result_array();
            foreach ($proposals as $proposal) {
                if (!$has_permission_proposals && !$has_permission_proposals_own && !$client_data) {
                    continue;
                } elseif ($client_data && !$has_contact_permission_proposals) {
                    continue;
                }

                $proposal['_tooltip'] = _l('proposal');
                $proposal['title'] = $proposal['subject'];
                $proposal['color'] = get_option('calendar_proposal_color');
                if (!$client_data) {
                    $proposal['url'] = admin_url('proposals/list_proposals/' . $proposal['id']);
                } else {
                    $proposal['url'] = site_url('viewproposal/' . $proposal['id'] . '/' . $proposal['hash']);
                }
                array_push($data, $proposal);
            }
        }

        if (get_option('show_tasks_on_calendar') == 1 && !$ff || $ff && array_key_exists('tasks', $filters)) {

            $this->db->select('name as title,id,' . tasks_rel_name_select_query() . ' as rel_name,rel_id,status,CASE WHEN duedate IS NULL THEN startdate ELSE duedate END as date', false);
            $this->db->from('tblstafftasks');
            $this->db->where('status !=', 5);

            //$this->db->where("CASE WHEN duedate IS NULL THEN (startdate BETWEEN '$start' AND '$end') ELSE (duedate BETWEEN '$start' AND '$end') END",null,false);

            if ($client_data) {
                $this->db->where('rel_type', 'project');
                $this->db->where('rel_id IN (SELECT id FROM tblprojects WHERE clientid=' . $client_id . ')');
                $this->db->where('rel_id IN (SELECT project_id FROM tblprojectsettings WHERE name="view_tasks" AND value=1)');
                $this->db->where('visible_to_client', 1);
            }

            if (!$is_admin && !$client_data) {
                $this->db->where('(id IN (SELECT taskid FROM tblstafftaskassignees WHERE staffid = ' . get_staff_user_id() . '))');
            }
            $tasks = $this->db->get()->result_array();

            foreach ($tasks as $task) {
                $rel_showcase = '';

                if (!empty($task['rel_id']) && !$client_data) {
                    $rel_showcase = ' (' . $task['rel_name'] . ')';
                }

                $task['date'] = $task['date'];

                $name = mb_substr($task['title'], 0, 60) . '...';
                $task['_tooltip'] = _l('calendar_task') . ' - ' . $name . $rel_showcase;
                $task['title'] = $name;
                $status = get_task_status_by_id($task['status']);
                $task['color'] = $status['color'];

                if (!$client_data) {
                    $task['onclick'] = 'init_task_modal(' . $task['id'] . '); return false';
                    $task['url'] = '#';
                } else {
                    $task['url'] = site_url('clients/project/' . $task['rel_id'] . '?group=project_tasks&taskid=' . $task['id']);
                }
                array_push($data, $task);
            }
        } // end if show tasks on calendar

        if (!$client_data) {
            $available_reminders = $this->perfex_base->get_available_reminders_keys();
            $hideNotifiedReminders = get_option('hide_notified_reminders_from_calendar');
            foreach ($available_reminders as $key) {
                if (get_option('show_' . $key . '_reminders_on_calendar') == 1 && !$ff || $ff && array_key_exists($key . '_reminders', $filters)) {
                    $this->db->select('date,description,firstname,lastname,creator,staff,rel_id')
                        ->from('tblreminders')
                        ->where('(date BETWEEN "' . $start . '" AND "' . $end . '")')
                        ->where('rel_type', $key)
                        ->join('tblstaff', 'tblstaff.staffid = tblreminders.staff');
                    if ($hideNotifiedReminders == '1') {
                        $this->db->where('isnotified', 0);
                    }
                    $reminders = $this->db->get()->result_array();
                    foreach ($reminders as $reminder) {
                        if ((get_staff_user_id() == $reminder['creator'] || get_staff_user_id() == $reminder['staff']) || $is_admin) {
                            $_reminder['title'] = '';

                            if (get_staff_user_id() != $reminder['staff']) {
                                $_reminder['title'] .= '(' . $reminder['firstname'] . ' ' . $reminder['lastname'] . ') ';
                            }

                            $name = mb_substr($reminder['description'], 0, 60) . '...';

                            $_reminder['_tooltip'] = _l('calendar_' . $key . '_reminder') . ' - ' . $name;
                            $_reminder['title'] .= $name;
                            $_reminder['date'] = $reminder['date'];
                            $_reminder['color'] = get_option('calendar_reminder_color');

                            if ($key == 'customer') {
                                $url = admin_url('clients/client/' . $reminder['rel_id']);
                            } elseif ($key == 'invoice') {
                                $url = admin_url('invoices/list_invoices/' . $reminder['rel_id']);
                            } elseif ($key == 'estimate') {
                                $url = admin_url('estimates/list_estimates/' . $reminder['rel_id']);
                            } elseif ($key == 'lead') {
                                $url = '#';
                                $_reminder['onclick'] = 'init_lead(' . $reminder['rel_id'] . '); return false;';
                            } elseif ($key == 'proposal') {
                                $url = admin_url('proposals/list_proposals/' . $reminder['rel_id']);
                            } elseif ($key == 'expense') {
                                $url = 'expenses/list_expenses/' . $reminder['rel_id'];
                            } elseif ($key == 'credit_note') {
                                $url = 'credit_notes/list_credit_notes/' . $reminder['rel_id'];
                            }

                            $_reminder['url'] = $url;
                            array_push($data, $_reminder);
                        }
                    }
                }
            }
        }

        if (get_option('show_contracts_on_calendar') == 1 && !$ff || $ff && array_key_exists('contracts', $filters)) {
            $this->db->select('subject as title,dateend,datestart,id,client,content');
            $this->db->from('tblcontracts');
            $this->db->where('trash', 0);
            if ($client_data) {
                $this->db->where('client', $client_id);
                $this->db->where('not_visible_to_client', 0);
            } else {
                if (!$has_permission_contracts) {
                    $this->db->where('addedfrom', get_staff_user_id());
                }
            }

            $this->db->where('(dateend > "' . date('Y-m-d') . '" AND dateend IS NOT NULL AND dateend BETWEEN "' . $start . '" AND "' . $end . '" OR datestart >"' . date('Y-m-d') . '")');


            $contracts = $this->db->get()->result_array();

            foreach ($contracts as $contract) {
                if (!$has_permission_contracts && !$has_permission_contracts_own && !$client_data) {
                    continue;
                } elseif ($client_data && !$has_contact_permission_contracts) {
                    continue;
                }

                $rel_showcase = '';
                if (!$client_data) {
                    $rel_showcase = ' (' . get_company_name($contract['client']) . ')';
                }

                $name = $contract['title'];
                $_contract['title'] = $name;
                $_contract['color'] = get_option('calendar_contract_color');
                $_contract['_tooltip'] = _l('calendar_contract') . ' - ' . $name . $rel_showcase;
                if (!$client_data) {
                    $_contract['url'] = admin_url('contracts/contract/' . $contract['id']);
                } else {
                    if (empty($contract['content'])) {
                        // No url for contracts
                        $_contract['url'] = '#';
                    } else {
                        $_contract['url'] = site_url('clients/contract_pdf/' . $contract['id']);
                    }
                }
                if (!empty($contract['dateend'])) {
                    $_contract['date'] = $contract['dateend'];
                } else {
                    $_contract['date'] = $contract['datestart'];
                }
                array_push($data, $_contract);
            }
        }
        //calendar_project
        if (get_option('show_projects_on_calendar') == 1 && !$ff || $ff && array_key_exists('projects', $filters)) {
            $this->load->model('projects_model');
            $this->db->select('name as title,id,clientid, CASE WHEN deadline IS NULL THEN start_date ELSE deadline END as date', false);
            $this->db->from('tblprojects');

            // Exclude cancelled and finished
            $this->db->where('status !=', 4);
            $this->db->where('status !=', 5);

            $this->db->where("CASE WHEN deadline IS NULL THEN (start_date BETWEEN '$start' AND '$end') ELSE (deadline BETWEEN '$start' AND '$end') END", null, false);

            if ($client_data) {
                $this->db->where('clientid', $client_id);
            }

            $projects = $this->db->get()->result_array();
            foreach ($projects as $project) {
                $rel_showcase = '';

                if (!$client_data) {
                    if (!$this->projects_model->is_member($project['id']) && !$is_admin) {
                        continue;
                    }

                    $rel_showcase = ' (' . get_company_name($project['clientid']) . ')';

                } else {
                    if (!$has_contact_permission_projects) {
                        continue;
                    }
                }

                $name = $project['title'];
                $_project['title'] = $name;
                $_project['color'] = get_option('calendar_project_color');
                $_project['_tooltip'] = _l('calendar_project') . ' - ' . $name . $rel_showcase;
                if (!$client_data) {
                    $_project['url'] = admin_url('projects/view/' . $project['id']);
                } else {
                    $_project['url'] = site_url('clients/project/' . $project['id']);
                }

                $_project['date'] = $project['date'];

                array_push($data, $_project);
            }
        }
        if (!$client_data && !$ff || (!$client_data && $ff && array_key_exists('events', $filters))) {
            $events = $this->get_all_events($start, $end);
            foreach ($events as $event) {
                if ($event['userid'] != get_staff_user_id() && !$is_admin) {
                    $event['is_not_creator'] = true;
                    $event['onclick'] = true;
                }
                $event['_tooltip'] = _l('calendar_event') . ' - ' . $event['title'];
                $event['color'] = $event['color'];
                array_push($data, $event);
            }
        }

        return $data;
    }

    /**
     * Delete user event
     * @param  mixed $id event id
     * @return boolean
     */
    public function delete_event($id)
    {
        $this->db->where('eventid', $id);
        $this->db->delete('tblevents');
        if ($this->db->affected_rows() > 0) {
            logActivity('Event Deleted [' . $id . ']');

            return true;
        }

        return false;
    }
}
