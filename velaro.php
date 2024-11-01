<?php
    
/**
 * @package Velaro_Live_Chat
 * @version 1.0.0
 */
/*
Plugin Name: Velaro Live Chat
Plugin URI: http://wordpress.org/plugins/velaro-live-chat/
Description: Plugin for adding velaro's live chat service to your wordpress site
Author: Velaro Live Chat
Version: 1.0.0
Author URI: http://velaro.com
*/

// setup needed constants
define( 'VELARO_BASE_URL', "https://app.velaro.com/" );
define( 'VELARO_BLOBSTORAGE_URL', "//galleryuseastprod.blob.core.windows.net/" );
define( 'VELARO_PLUGINS_URL', VELARO_BASE_URL . "api/plugins/login" );
define( 'VELARO_GROUPS_URL', VELARO_BASE_URL . "api/plugins/groups" );

// echo in the noscript
function velaro_load_inline_chat() {
    echo '
<noscript>
    <a href="https://www.velaro.com" title="Contact us" target="_blank">Questions?</a>
    powered by <a href="http://www.velaro.com" title="Velaro live chat">velaro live chat</a>
</noscript>';
}

// if the user has linked their account, load the needed scripts for chats on this page
function velaro_check_load_scripts(){
    // get options
    $velaro_site_identifier = get_option('velaro_site_identifier');
    $velaro_vm_active = get_option('velaro_vm_enabled');
    $velaro_mobile_enabled = get_option('velaro_mobile_enabled');
    $velaro_page_assignments = get_option('velaro_page_assignments');
    // determine whether or not we should load the scripts
    if(FALSE != $velaro_site_identifier){
        wp_register_script('velaro_globals', VELARO_BLOBSTORAGE_URL .'velaroscripts/'. $velaro_site_identifier . '/globals.js');
        wp_register_script('velaro_loader',VELARO_BLOBSTORAGE_URL .'velaroscripts/velaro.loadscripts.js');
        wp_register_script('velaro_constants',plugins_url('/velaro-chat/js/constants.js'),array('velaro_globals'));
        wp_enqueue_script( 'velaro_globals', '', array(), '1.0.0', true );
        wp_enqueue_script( 'velaro_constants', '', array('velaro_globals'), '1.0.0', true );
        $velaro_args = array(
        'velaro_siteID'            => $velaro_site_identifier,
        'velaro_vm_active'         => $velaro_vm_active,
        'velaro_mobile_enabled'         => $velaro_mobile_enabled,
        'velaro_page_assingments' => $velaro_page_assignments,
        'velaro_page_id' => get_the_ID()
            );
        wp_localize_script( 'velaro_constants', 'velaro_args', $velaro_args );
        wp_enqueue_script( 'velaro_loader', '', array(), '1.0.0', true );
    }
}

// add the admin menu option
function velaro_setup_adminpage(){
    add_menu_page( 'Velaro', 'Velaro', 'manage_options', 'velaro_configuration','velaro_account_config',plugins_url( 'velaro-chat/images/velaro-logo.png' ) );
}

// setup the admin menu page
function velaro_account_config(){
    //must check that the user has the required capability 
    if (!current_user_can('manage_options'))
    {
      wp_die( __('You do not have sufficient permissions to access this page.') );
    }
    ?>
        <style>
            .velaro-form{
                background-color: white;
                border-radius: .5em;
            }
            .velaro-form-header{
                border-bottom: 1px solid rgba(0, 0, 0, 0.05);
                font-weight: bold;
                color: #23282d;
                font-size: 1.3em;
                vertical-align: middle;
                padding: 10px 0 0 10px;
                margin: 0 5px 20px 5px;
            }
            .velaro-label-wrapper{
                width: 200px;
                float: left;
                padding-left: 50px;
                font-weight: bold;
                color: #23282d;
                font-size: 1.1em;
            }
            .velaro-input-group{
                margin-bottom: 15px;
            }
            .thin{
                font-weight: 300;
            }
            .velaro-button{
                margin-left: 360px;
            }
            div.velaro-button input{
                width: 80px;
            }
        </style>

        <div class="wrap">
        <h2><img src="<?php echo plugins_url( 'velaro-chat/images/velaro-logo.png' );?>" alt="Velaro logo" style="padding-right: 5px;">Velaro Live Chat</h2>
        <form method="post" action="options.php" class="velaro-form">
            <div class="velaro-form-header">Link Account</div>
            <?php
            settings_fields( 'velaro_settings' );
            do_settings_sections( 'velaro_settings');
            ?>
            <input type="hidden" name="velaro_site_identifier" value="<?php echo esc_attr( get_option('velaro_site_identifier') ); ?>" />
            <input type="hidden" name="velaro_api_key" value="<?php echo esc_attr( get_option('velaro_api_key') ); ?>" />
            <input type="hidden" name="velaro_page_assignments" value="<?php echo esc_attr( get_option('velaro_page_assignments') ); ?>" />

            <div class="velaro-input-group">
                <div class="velaro-label-wrapper"><label>Username <span class="thin">(email)</span></label></div>
                <input type="text" name="velaro_username" value="<?php echo esc_attr( get_option('velaro_username') ); ?>" />
            </div>
            <div class="velaro-input-group">
                <div class="velaro-label-wrapper"><label class="velaro-label">Password</label></div>
                <input type="password" name="velaro_password" value="<?php echo esc_attr( get_option('velaro_password') ); ?>" />
            </div>
            <div class="velaro-input-group">
                <div class="velaro-label-wrapper"><label class="velaro-label">Account is linked</label></div>
                <label><?php echo(FALSE != get_option('velaro_site_identifier') ? 'Yes' : 'No <span class="thin">(Account must be linked to recieve chats)</span>') ?></label>
            </div>
            <div class="velaro-button">
                <p class="submit"><input type="button" onclick="" name="velaro_attach" id="velaro_attach" class="button button-primary" value="Link"></p>
            </div>
            <div class="velaro-form-header">Plugin Settings</div>
            <div class="velaro-input-group">
                <div class="velaro-label-wrapper"><label class="velaro-label">Enable Visitor Monitoring</label></div>
                <input type="checkbox" name="velaro_vm_enabled" value="1" <?php checked( get_option('velaro_vm_enabled'), "1"); ?> />
            </div>
            <div class="velaro-form-header">Chat Routing</div>
            <div class="velaro-input-group">
                <div class="velaro-label-wrapper"><label class="velaro-label">When on Page</label></div>
                <select class="velaro-page-select">
                <?php 
                    // get all pages for pages dropdown
                    $velaro_pages =  get_pages('parent=0&sort_column=menu_order' );
                    $velaro_page_select = '<select>';
                    foreach ( $velaro_pages as $velaro_page ) {
                	$option = '<option value="' . $velaro_page->ID  . '">';
	                $option .= $velaro_page->post_title;
	                $option .= '</option>';
	                echo $option;
                }
                ?>
                </select>
             </div>
            <div class="velaro-input-group">
                <div class="velaro-label-wrapper"><label class="velaro-label">Route chats to group</label></div>
                <?php
                    // if we have an api key, get list of groups from velaro, display in dropdown
                    $velaro_api_key = get_option('velaro_api_key');
                    $velaro_group_args = array( 'headers'     => array('APIKEY' => $velaro_api_key));
                    $velaro_response = wp_remote_get( VELARO_GROUPS_URL, $velaro_group_args );
                    $velaro_groups = json_decode( wp_remote_retrieve_body( $velaro_response ), true );
                    $velaro_group_select = '<select class="velaro-group-select"><option value ="0">Default Group</option>';
                    if($velaro_api_key){
                        foreach($velaro_groups as &$velaro_group){
                    	$velaro_group_select .= "\
                        <option value='$velaro_group[Key]'>$velaro_group[Value]</option>";
                    }
                    }
                    $velaro_group_select .= "\
                    </select>";
                    echo $velaro_group_select;
                ?>
              </div>

            
            <div class="velaro-button">
                <?php submit_button('Save'); ?>
            </div>
        </form>
        </div>
    <?php 
}

// pull in the velaro-wp-admin.js script (sets up buttons, select list handlers
function velaro_attach_button(){
    wp_register_script('velaro_admin_buttons', plugins_url('/velaro-chat/js/velaro-wp-admin.js'));
    wp_enqueue_script( 'velaro_admin_buttons', '', array(), '1.0.0', true );
    $velaro_args = array(
    'velaro_url'            => VELARO_PLUGINS_URL
);
wp_localize_script( 'velaro_admin_buttons', 'velaro_args', $velaro_args );
}

// register velaros wordpress settings for this plugin
function register_velaro_settings() {
	register_setting('velaro_settings','velaro_username'); 
	register_setting('velaro_settings','velaro_password');
	register_setting('velaro_settings','velaro_vm_enabled'); 
	register_setting('velaro_settings','velaro_mobile_enabled'); 
	register_setting('velaro_settings','velaro_site_identifier'); 
	register_setting('velaro_settings','velaro_api_key'); 
	register_setting('velaro_settings','velaro_page_assignments'); 
} 

// add all of our actions
add_action('wp_footer', 'velaro_load_inline_chat');
add_action('wp_footer', 'velaro_check_load_scripts');
add_action('admin_menu', 'velaro_setup_adminpage');
add_action('admin_init', 'register_velaro_settings');
add_action('admin_footer', 'velaro_attach_button'); 
?>
