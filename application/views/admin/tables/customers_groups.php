<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$aColumns = array('name', 'color', 'description');
$roleid = $_SESSION['roleid'];
$sIndexColumn = "id";
$sTable = 'tblcustomersgroups';

$ci = &get_instance();
$ci->load->model('clients_model');

$result = data_tables_init($aColumns, $sIndexColumn, $sTable, array(), array(), array('id'));
$output = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = array();
    $color = $ci->clients_model->get_group_color_by_id($aRow['id']);
    for ($i = 0; $i < count($aColumns); $i++) {

        $_data = '<a href="#" data-toggle="modal" style="color:' . $color . ';" data-target="#customer_group_modal" data-id="' . $aRow['id'] . '">' . $aRow[$aColumns[$i]] . '</a>';

        $row[] = $_data;
    }

    $options = icon_btn('#', 'pencil-square-o', 'btn-default', array('data-toggle' => 'modal', 'data-target' => '#customer_group_modal', 'data-id' => $aRow['id']));
    $row[] = $options .= icon_btn('clients/delete_group/' . $aRow['id'], 'remove', 'btn-danger _delete');

    $output['aaData'][] = $row;
}
