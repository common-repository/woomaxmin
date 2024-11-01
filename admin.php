<?php
function woomaxmin_admin_init() {
    if ( is_admin() ){ // for Admin Dashboard Only
        // Embed the Script on Plugin's Option Page Only
        if ( isset($_GET['page']) && $_GET['page'] == 'woomaxmin-settings' ) {
            $pluginfolder = get_bloginfo('url') . '/' . PLUGINDIR . '/' . dirname(plugin_basename(__FILE__));
            wp_enqueue_script('jquery');
            wp_enqueue_style('bootstrap_css', $pluginfolder . '/css/bootstrap.min.css');
            wp_enqueue_style('custom_css', $pluginfolder . '/css/custom_css.css');
            wp_enqueue_script('bootstrap_js', $pluginfolder . '/js/bootstrap.min.js', array('jquery') );
            wp_enqueue_script('custom_js', $pluginfolder . '/js/custom_js.js', array('jquery') );
        }
   }
}
add_action('admin_init', 'woomaxmin_admin_init');
add_action('admin_menu', 'woomaxmin_admin_page');
function woomaxmin_admin_page(){
    add_menu_page('Woomaxmin Settings', 'WooMaxMin', 'administrator', 'woomaxmin-settings', 'woomaxmin_admin_page_callback',plugin_dir_url( __FILE__ ) . '/img/woomaxmin.png');
}
/*
 * Register the settings
 */
add_action('admin_init', 'woomaxmin_register_settings');
function woomaxmin_register_settings(){
    //this will save the option in the wp_options table as 'woomaxmin_settings'
    register_setting('woomaxmin_settings', 'woomaxmin_settings', 'woomaxmin_settings_validate');
}
add_action('admin_init', 'woomaxmin_reset_option');
function woomaxmin_reset_option() {
    if ( isset( $_POST['reset_options'] ) && $_POST['reset_options'] === 'true' ) {
        delete_option('woomaxmin_settings');
        $all_user_ids = get_users( 'fields=ID' );
        foreach ( $all_user_ids as $user_id ) {
            delete_user_meta( $user_id, 'woomaxmin_orderId' );
            delete_user_meta( $user_id, 'woomaxmin_total' );
        }
    }
}
function woomaxmin_settings_validate($args){
    if(!isset($args['woomaxmin_maxvalue']) || !is_numeric($args['woomaxmin_maxvalue']) || !isset($args['woomaxmin_minvalue']) || !is_numeric($args['woomaxmin_minvalue']) || !isset($args['woomaxmin_error']) ){
        $args['woomaxmin_maxvalue'] = '';
        $args['woomaxmin_minvalue'] = '';
        $args['woomaxmin_error'] = '';
        add_settings_error('woomaxmin_settings', 'woomaxmin_invalid_maxvalue', 'Please enter a valid data!', $type = 'error');   
    }
    return $args;
}

//Display the validation errors and update messages
/*
 * Admin notices
 */
add_action('admin_notices', 'woomaxmin_admin_notices');
function woomaxmin_admin_notices(){
   settings_errors();
}
//The markup for your plugin settings page
function woomaxmin_admin_page_callback(){ ?>
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
    <h2>Set WooCommerce Purchase Limit</h2>
    <br />
    <!-- message box -->
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div id="error_msg"></div>
    </div>
    <div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">
        <form class="form-horizontal" action="options.php" method="post" id="woomaxmin-option">
            <?php
            settings_fields( 'woomaxmin_settings' );
            do_settings_sections( __FILE__ );
            //get the older values
            $options = get_option( 'woomaxmin_settings' );

            function get_roles() {
                global $wp_roles;

                $all_roles = $wp_roles->roles;
                $editable_roles = apply_filters('editable_roles', $all_roles);

                return $editable_roles;
            }
            ?>
            <!-- New Update User role -->
            <div class="form-group">
                <label for="maximum" class="control-label col-lg-5 col-md-5 col-xs-12">Select User Role</label>
                <div class="col-lg-7 col-md-7 col-xs-12">
                     <select name="" multiple class="col-lg-12 col-md-12 col-sm-12 col-xs-12" SIZE=7 disabled="">
                         <option value="alluser" selected="selected">All</option>
                         <?php foreach(get_roles() as $key=>$value) { ?>
                            <option value="<?php echo $$value['name']; ?>"><?php echo $value['name']; ?></option>
                         <?php } ?>
                         <option value="gest">Non Login User</option>                                            
                    </select> 
                    <span class="description">(Available on pro version)</span>
                </div>
             </div>
            <div class="form-group">
                <label for="maximum" class="control-label col-lg-5 col-md-5 col-xs-12">Enable Limit Only Cart</label>
                <div class="col-lg-7 col-md-7 col-xs-12">
                    <div class="checkbox">
                        <label>
                          <input type="checkbox" name="" value="" disabled="" />Yes
                        </label>
                    </div>
                    <span class="description">Give limit only on one time cart purchase <small>(if you enable this option then plugin not able to track user life time purchase)</small></span>
                </div>
             </div>
            <div class="form-group">
                    <label for="maximum" class="control-label col-lg-5 col-md-5 col-xs-12">Maximum Value<sup style="color:#ff0000;font-size: 12px;">*</sup></label>
                    <div class="col-lg-7 col-md-7 col-xs-12">
                        <input class="form-control" name="woomaxmin_settings[woomaxmin_maxvalue]" type="text" id="woomaxmin_maxvalue" value="<?php echo (isset($options['woomaxmin_maxvalue']) && $options['woomaxmin_maxvalue'] != '') ? $options['woomaxmin_maxvalue'] : ''; ?>" required/>
                        <span class="description">Please Enter a Maximum Purchase Amount.( For make infinity enter " 0 " )</span>
                    </div>
             </div>
             <div class="form-group">
                <label for="maximum" class="control-label col-lg-5 col-md-5 col-xs-12">Minimum Value<sup style="color:#ff0000;font-size: 12px;">*</sup></label>
                <div class="col-lg-7 col-md-7 col-xs-12">
                    <input class="form-control" name="woomaxmin_settings[woomaxmin_minvalue]" type="text" id="woomaxmin_minvalue" value="<?php echo (isset($options['woomaxmin_minvalue']) && $options['woomaxmin_minvalue'] != '') ? $options['woomaxmin_minvalue'] : ''; ?>" required/>
                    <span class="description">Please Enter a Minimum Purchase Amount.</span>
                </div>
             </div>
             <div class="form-group">
                <label for="maximum" class="control-label col-lg-5 col-md-5 col-xs-12">End Date</label>
                <div class="col-lg-7 col-md-7 col-xs-12">
                    <input class="form-control" name="" type="text" value="" disabled="" />
                    <span class="description">Please Enter End Date. (Select date range available on pro version)</span>
                </div>
             </div>
             <div class="form-group">
                <label for="maximum" class="control-label col-lg-5 col-md-5 col-xs-12">Notification Message<sup style="color:#ff0000;font-size: 12px;">*</sup></label>
                <div class="col-lg-7 col-md-7 col-xs-12">
                    <input class="form-control" name="woomaxmin_settings[woomaxmin_error]" type="text" id="woomaxmin_error" value="<?php echo (isset($options['woomaxmin_error']) && $options['woomaxmin_error'] != '') ? $options['woomaxmin_error'] : ''; ?>" required/>
                    <span class="description">Enter notification message</span>
                </div>
             </div>
             <div class="form-group">
                <label for="maximum" class="control-label col-lg-5 col-md-5 col-xs-12">Notification Show On</label>
                <div class="col-lg-7 col-md-7 col-xs-12">
                    <div class="checkbox">
                        <label>
                          <input type="checkbox" name="" value="" disabled="" /> Product listing Page
                        </label>
                    </div>
                    <div class="checkbox">
                        <label>
                          <input type="checkbox" name="" value="" disabled="" />Single Product Page
                        </label>
                    </div>
                    <span class="description">Available on Pro version</span>
                </div>
             </div>
             <div class="form-group">
                <label for="maximum" class="control-label col-lg-5 col-md-5 col-xs-12">Disable "Proceed to Checkout" Button</label>
                <div class="col-lg-7 col-md-7 col-xs-12">
                    <div class="checkbox">
                        <label>
                          <input type="checkbox" name="" value="" disabled="" />Yes
                        </label>
                    </div>
                    <span class="description">Disable "Proceed to Checkout" button available on Pro version</span>
                </div>
             </div>
             <div class="form-group">
                <label for="maximum" class="control-label col-lg-5 col-md-5 col-xs-12">Disable "Add To Cart" Button</label>
                <div class="col-lg-7 col-md-7 col-xs-12">
                    <div class="checkbox">
                        <label>
                          <input type="checkbox" name="" value="" disabled="" />Yes
                        </label>
                    </div>
                    <span class="description">Disable "Add To Cart" button from prodcut listing page available on Pro Version</span>
                </div>
             </div>
             <div class="form-group">
                <label for="maximum" class="control-label col-lg-5 col-md-5 col-xs-12">Remove "Add To Cart" Button From</label>
                <div class="col-lg-7 col-md-7 col-xs-12">
                    <div class="checkbox">
                        <label>
                          <input type="checkbox" name="" value="" disabled="" />Product Listing Page
                        </label>
                    </div>
                    <div class="checkbox">
                        <label>
                          <input type="checkbox" name="woomaxmin_settings[woomaxmin_remove][]" value="2" <?php if(isset($options['woomaxmin_remove'])) {foreach($options['woomaxmin_remove'] as $selected){if(isset($selected) && $selected == '2'){echo "checked";}}}?> />Single Product Page
                        </label>
                    </div>
                </div>
             </div>
             <br /><br />
             <input type="submit" style="margin-bottom:20px" class="btn btn-success custom_button sbmt_op" value="Save Settings" />        
        </form>
    </div>
    <!-- Option table End -->
    <!-- About block-->
    <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="row">
                <!-- Reset options -->
                <form action="<?php echo admin_url( 'admin.php?page=woomaxmin-settings' ); ?>" method="post">
                  <input type="submit" value="Click to reset plugin options" class="btn btn-info pull-right custom_button" />
                  <input type="hidden" name="reset_options" value="true" />
                </form>
            </div>
        </div>
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 about_box">
            <div class="col-lg-7 col-md-7 col-sm-12 col-xs-12">
                <div class="row">
                    <h2>WooMaxMin</h2>
                    <p><strong>By: </strong><a href="mailto:himanshubhuyan0@gmail.com?Subject=WooMaxMin Free plugin query" target="_top"> Himanshu Bhuyan</a></p>
                </div>
            </div>
             <div class="col-lg-5 col-md-5 col-sm-12 col-xs-12 version">
                <div class="row">
                    <strong>Version:</strong> 1.1</p>
                </div>                
            </div>
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="row">
                    <p>We are a creative team with unique ideas in mind and service in heart. We love what we do.</p>
                </div>
            </div>
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 powered">
                <div class="row">
                    <a href="http://www.iitechnology.in/" target="_blank">Powered By iitechnology.in</a>
                </div>
            </div>
        </div>
        <!-- Demo link button -->
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="row">
                <a href="http://womaxmin.iitechnology.in/" target="_blank">
                    <button class="btn btn-warning custom_button">BUY NOW</button>
                </a>
            </div>
        </div>
        <!-- Ads block -->
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 ads_box">
            <div class="row">
                <a href="http://www.iitechnology.in/request.html" target="_blank"><img class="img-responsive" src="<?php echo get_bloginfo('url') . '/' . PLUGINDIR . '/' . dirname(plugin_basename(__FILE__));?>/img/banner.jpg" /></a>
            </div>
        </div>
    </div>
    <!-- Note block -->
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 note_box">
        <div class="row">
            <b>Note:</b>
            Notification, Disable Button and Remove  Button this options are apply when user cross Maximum or Minimun limit.
        </div>
    </div>
</div>
<?php }
?>