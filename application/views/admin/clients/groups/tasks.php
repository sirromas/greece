<h4 class="customer-profile-group-heading"><?php echo _l('tasks'); ?></h4>
<?php if(isset($client)){
    init_relation_tasks_table(array( 'data-new-rel-id'=>$client->userid,'data-new-rel-type'=>'customer'));
} ?>


<script src="https://code.jquery.com/jquery-1.12.4.min.js"></script>
<script type="text/javascript">

    $(document ).ready(function() {

        <?php $roleid=$_SESSION['roleid'];

        if ($roleid==1) { ?>

            $("#DataTables_Table_0_wrapper > div:nth-child(2) > div.col-md-7 > div.dt-buttons.btn-group > a.btn.btn-default.buttons-collection.btn-default-dt-options").css('display','none');

        <?php } ?>

    }) // end of document ready..

</script>
