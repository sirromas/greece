<h4 class="customer-profile-group-heading"><?php echo _l('client_add_edit_profile'); ?></h4>
<div class="row">
   <?php echo form_open($this->uri->uri_string(),array('class'=>'client-form','autocomplete'=>'off')); ?>
   <div class="additional"></div>
   <div class="col-md-12">
      <ul class="nav nav-tabs profile-tabs row customer-profile-tabs" role="tablist">
         <li role="presentation" class="<?php if(!$this->input->get('tab')){echo 'active';}; ?>">
            <a href="#contact_info" aria-controls="contact_info" role="tab" data-toggle="tab">
               <?php echo _l( 'customer_profile_details'); ?>
            </a>
         </li>

      </ul>

      <div class="tab-content">
         <?php do_action('after_custom_profile_tab_content',isset($client) ? $client : false); ?>

         <div role="tabpanel" class="tab-pane<?php if(!$this->input->get('tab')){echo ' active';}; ?>" id="contact_info">
            <div class="row">
               <div class="col-md-12<?php if(isset($client) && (!is_empty_customer_company($client->userid) && total_rows('tblcontacts',array('userid'=>$client->userid,'is_primary'=>1)) > 0)) { echo ''; } else {echo ' hide';} ?>" id="client-show-primary-contact-wrapper">

               </div>

                <!-- Original profile fields -->
                <div class="col-md-12">

                    <?php $value=( isset($client) ? $client->company : ''); ?>
                    <?php $attrs = (isset($client) ? array() : array('autofocus'=>true)); ?>
                    <?php echo render_input( 'company', 'ΟΝΟΜΑΤΕΠΩΝΥΜΟ',$value,'text',$attrs); ?>

                </div>

                <!--
                <div class="col-md-6">

                    <?php

                    $selected = array();
                    if(isset($customer_groups)){
                        foreach($customer_groups as $group){
                            array_push($selected,$group['groupid']);
                        }
                    }
                     // echo render_select('groups_in[]',$groups,array('id','name'),'customer_groups',$selected,array('multiple'=>true),array(),'','',false);

                    ?>

                </div>

                <div class="col-md-6">

                    <?php $value=( isset($client) ? $client->zip : ''); ?>
                    <?php // echo render_input( 'zip', 'client_postal_code',$value); ?>


                </div>
                -->


                <!-- Rendering custom fields -->
                <div class="col-md-12">
                    <?php $rel_id=( isset($client) ? $client->userid : false); ?>
                    <?php // echo render_custom_fields( 'customers',$rel_id); ?>
                    <?php
                    $where=array();
                    $zip_value=( isset($client) ? $client->zip : '');
                    echo render_custom_fields2( 'customers',$rel_id, $where, $groups, $customer_groups, $zip_value);

                    ?>
                </div>


                <!-- Longitude & Lattitude block -->
                <div class="col-md-6">

                <?php
               $value=( isset($client) ? $client->latitude : ''); ?>
               <div class="form-group">
                  <label for="website"><?php echo _l('customer_latitude'); ?></label>
                  <div class="input-group">
                     <input type="text" name="latitude" id="latitude" value="<?php echo $value; ?>" class="form-control">
                     <div class="input-group-addon">
                        <span><a href="#" tabindex="-1" class="pull-left mright5" onclick="fetch_lat_long_from_google_cprofile(); return false;" data-toggle="tooltip" data-title="<?php echo _l('fetch_from_google') . ' - ' . _l('customer_fetch_lat_lng_usage'); ?>"><i id="gmaps-search-icon" class="fa fa-google" aria-hidden="true"></i></a></span>
                     </div>
                  </div>
               </div>

                </div>

                <div class="col-md-6">

                    <?php $value=( isset($client) ? $client->longitude : ''); ?>
                    <?php echo render_input( 'longitude', 'customer_longitude',$value); ?>

                </div>

            </div> <!-- End of div class=row -->

         </div> <!-- End if div class tab pane -->


      </div> <!-- End of div class tab-content -->
       <!-- End of contact info tab -->

      <?php if(isset($client)){ ?>

      <!--
      <div role="tabpanel" class="tab-pane<?php if($this->input->get('tab') && $this->input->get('tab') == 'contacts'){echo ' active';}; ?>" id="contacts">
         <?php if(has_permission('customers','','create') || is_customer_admin($client->userid)){
            $disable_new_contacts = false;
            if(is_empty_customer_company($client->userid) && total_rows('tblcontacts',array('userid'=>$client->userid)) == 1){
               $disable_new_contacts = true;
            }
            ?>
            <div class="inline-block"<?php if($disable_new_contacts){ ?> data-toggle="tooltip" data-title="<?php echo _l('customer_contact_person_only_one_allowed'); ?>"<?php } ?>>
               <a href="#" onclick="contact(<?php echo $client->userid; ?>); return false;" class="btn btn-info mbot25<?php if($disable_new_contacts){echo ' disabled';} ?>"><?php echo _l('new_contact'); ?></a>
            </div>
            <?php } ?>
            <?php
            $table_data = array(_l('client_firstname'),_l('client_lastname'),_l('client_email'),_l('contact_position'),_l('client_phonenumber'),_l('contact_active'),_l('clients_list_last_login'));
            $custom_fields = get_custom_fields('contacts',array('show_on_table'=>1));
            foreach($custom_fields as $field){
               array_push($table_data,$field['name']);
            }
            array_push($table_data,_l('options'));
            echo render_datatable($table_data,'contacts'); ?>
         </div>


         <div role="tabpanel" class="tab-pane" id="customer_admins">
            <?php if (has_permission('customers', '', 'create') || has_permission('customers', '', 'edit')) { ?>
            <a href="#" data-toggle="modal" data-target="#customer_admins_assign" class="btn btn-info mbot30"><?php echo _l('assign_admin'); ?></a>
            <?php } ?>
            <table class="table dt-table">
               <thead>
                  <tr>
                     <th><?php echo _l('staff_member'); ?></th>
                     <th><?php echo _l('customer_admin_date_assigned'); ?></th>
                     <?php if(has_permission('customers','','create') || has_permission('customers','','edit')){ ?>
                     <th><?php echo _l('options'); ?></th>
                     <?php } ?>
                  </tr>
               </thead>
               <tbody>
                  <?php foreach($customer_admins as $c_admin){ ?>
                  <tr>
                     <td><a href="<?php echo admin_url('profile/'.$c_admin['staff_id']); ?>">
                        <?php echo staff_profile_image($c_admin['staff_id'], array(
                           'staff-profile-image-small',
                           'mright5'
                        ));
                        echo get_staff_full_name($c_admin['staff_id']); ?></a>
                     </td>
                     <td data-order="<?php echo $c_admin['date_assigned']; ?>"><?php echo _dt($c_admin['date_assigned']); ?></td>
                     <?php if(has_permission('customers','','create') || has_permission('customers','','edit')){ ?>
                     <td>
                        <a href="<?php echo admin_url('clients/delete_customer_admin/'.$client->userid.'/'.$c_admin['staff_id']); ?>" class="btn btn-danger _delete btn-icon"><i class="fa fa-remove"></i></a>
                     </td>
                     <?php } ?>
                  </tr>
                  <?php } ?>
               </tbody>
            </table>
         </div>
          -->

         <?php } ?>

          <!--
          <div role="tabpanel" class="tab-pane" id="billing_and_shipping">
            <div class="row">
               <div class="col-md-12">
                  <div class="row">
                     <div class="col-md-6">
                        <h4 class="no-mtop"><?php echo _l('billing_address'); ?> <a href="#" class="pull-right billing-same-as-customer"><small class="font-medium-xs"><?php echo _l('customer_billing_same_as_profile'); ?></small></a></h4>
                        <hr />
                        <?php $value=( isset($client) ? $client->billing_street : ''); ?>
                        <?php echo render_textarea( 'billing_street', 'billing_street',$value); ?>
                        <?php $value=( isset($client) ? $client->billing_city : ''); ?>
                        <?php echo render_input( 'billing_city', 'billing_city',$value); ?>
                        <?php $value=( isset($client) ? $client->billing_state : ''); ?>
                        <?php echo render_input( 'billing_state', 'billing_state',$value); ?>
                        <?php $value=( isset($client) ? $client->billing_zip : ''); ?>
                        <?php echo render_input( 'billing_zip', 'billing_zip',$value); ?>
                        <?php $selected=( isset($client) ? $client->billing_country : '' ); ?>
                        <?php echo render_select( 'billing_country',$countries,array( 'country_id',array( 'short_name')), 'billing_country',$selected,array('data-none-selected-text'=>_l('dropdown_non_selected_tex'))); ?>
                     </div>
                     <div class="col-md-6">
                        <h4 class="no-mtop">
                           <i class="fa fa-question-circle" data-toggle="tooltip" data-title="<?php echo _l('customer_shipping_address_notice'); ?>"></i>
                           <?php echo _l('shipping_address'); ?> <a href="#" class="pull-right customer-copy-billing-address"><small class="font-medium-xs"><?php echo _l('customer_billing_copy'); ?></small></a>
                        </h4>
                        <hr />
                        <?php $value=( isset($client) ? $client->shipping_street : ''); ?>
                        <?php echo render_textarea( 'shipping_street', 'shipping_street',$value); ?>
                        <?php $value=( isset($client) ? $client->shipping_city : ''); ?>
                        <?php echo render_input( 'shipping_city', 'shipping_city',$value); ?>
                        <?php $value=( isset($client) ? $client->shipping_state : ''); ?>
                        <?php echo render_input( 'shipping_state', 'shipping_state',$value); ?>
                        <?php $value=( isset($client) ? $client->shipping_zip : ''); ?>
                        <?php echo render_input( 'shipping_zip', 'shipping_zip',$value); ?>
                        <?php $selected=( isset($client) ? $client->shipping_country : '' ); ?>
                        <?php echo render_select( 'shipping_country',$countries,array( 'country_id',array( 'short_name')), 'shipping_country',$selected,array('data-none-selected-text'=>_l('dropdown_non_selected_tex'))); ?>
                     </div>
                     <?php if(isset($client) &&
                     (total_rows('tblinvoices',array('clientid'=>$client->userid)) > 0 || total_rows('tblestimates',array('clientid'=>$client->userid)) > 0 || total_rows('tblcreditnotes',array('clientid'=>$client->userid)) > 0)){ ?>
                     <div class="col-md-12">
                        <div class="alert alert-warning">
                           <div class="checkbox checkbox-default">
                              <input type="checkbox" name="update_all_other_transactions" id="update_all_other_transactions">
                              <label for="update_all_other_transactions">
                                 <?php echo _l('customer_update_address_info_on_invoices'); ?><br />
                              </label>
                           </div>
                           <b><?php echo _l('customer_update_address_info_on_invoices_help'); ?></b>
                           <div class="checkbox checkbox-default">
                              <input type="checkbox" name="update_credit_notes" id="update_credit_notes">
                              <label for="update_credit_notes">
                                 <?php echo _l('customer_profile_update_credit_notes'); ?><br />
                              </label>
                           </div>
                        </div>
                     </div>
                     <?php } ?>
                  </div>
               </div>
            </div>
         </div>
          <!-- End of Billing & Shipping div -->
      </div>
   </div>
   <?php echo form_close(); ?>
</div>
<div id="contact_data"></div>
<?php if(isset($client)){ ?>
<?php if (has_permission('customers', '', 'create') || has_permission('customers', '', 'edit')) { ?>
<div class="modal fade" id="customer_admins_assign" tabindex="-1" role="dialog">
   <div class="modal-dialog">
      <?php echo form_open(admin_url('clients/assign_admins/'.$client->userid)); ?>
      <div class="modal-content">
         <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title"><?php echo _l('assign_admin'); ?></h4>
         </div>
         <div class="modal-body">
            <?php
            $selected = array();
            foreach($customer_admins as $c_admin){
               array_push($selected,$c_admin['staff_id']);
            }
            echo render_select('customer_admins[]',$staff,array('staffid',array('firstname','lastname')),'',$selected,array('multiple'=>true),array(),'','',false); ?>
         </div>
         <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
            <button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
         </div>
      </div>
      <!-- /.modal-content -->
      <?php echo form_close(); ?>
   </div>
   <!-- /.modal-dialog -->
</div>
<!-- /.modal -->
<?php } ?>
<?php } ?>
