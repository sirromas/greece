<?php
$customer_tabs = array(
  array(
    'name'=>'profile',
    'url'=>admin_url('clients/client/'.$client->userid.'?group=profile'),
    'icon'=>'fa fa-user-circle',
    'lang'=>_l('client_add_edit_profile'),
    'visible'=>true,
    'order'=>1
    ),
  array(
    'name'=>'notes',
    'url'=>admin_url('clients/client/'.$client->userid.'?group=notes'),
    'icon'=>'fa fa-sticky-note-o',
    'lang'=>_l('contracts_notes_tab'),
    'visible'=>true,
    'order'=>2
    ),

    array(
    'name'=>'tasks',
    'url'=>admin_url('clients/client/'.$client->userid.'?group=tasks'),
    'icon'=>'fa fa-tasks',
    'lang'=>_l('tasks'),
    'visible'=>true,
    'order'=>12
    ),
  array(
    'name'=>'map',
    'url'=>admin_url('clients/client/'.$client->userid.'?group=map'),
    'icon'=>'fa fa-map-marker',
    'lang'=>_l('customer_map'),
    'visible'=>true,
    'order'=>17
    ),

  );

$hook_data = do_action('customer_profile_tabs',array('tabs'=>$customer_tabs,'client'=>$client));
$customer_tabs = $hook_data['tabs'];

usort($customer_tabs, function($a, $b) {
  return $a['order'] - $b['order'];
});

?>
<ul class="nav navbar-pills nav-tabs nav-stacked customer-tabs" role="tablist">
   <?php foreach($customer_tabs as $tab){
      if((isset($tab['visible']) && $tab['visible'] == true) || !isset($tab['visible'])){ ?>
      <li class="<?php if($tab['name'] == 'profile'){echo 'active ';} ?>customer_tab_<?php echo $tab['name']; ?>">
        <a data-group="<?php echo $tab['name']; ?>" href="<?php echo $tab['url']; ?>"><i class="<?php echo $tab['icon']; ?> menu-icon" aria-hidden="true"></i><?php echo $tab['lang']; ?>
            <?php if(isset($tab['id']) && $tab['id'] == 'reminders'){
              $total_reminders = total_rows('tblreminders',
                  array(
                   'isnotified'=>0,
                   'staff'=>get_staff_user_id(),
                   'rel_type'=>'customer',
                   'rel_id'=>$client->userid
                   )
                  );
              if($total_reminders > 0){
                echo '<span class="badge">'.$total_reminders.'</span>';
              }
          }
          ?>
      </a>
  </li>
  <?php } ?>
  <?php } ?>
</ul>
