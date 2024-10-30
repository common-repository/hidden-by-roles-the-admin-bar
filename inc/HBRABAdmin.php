<?php

if ( ! function_exists( 'HBRABinit' ) ) {
    // init option plugin with translation
    function HBRABinit(){
        
        if ( current_user_can('manage_options') ) {
     
            // Global Wordpress BDD && Variables for locality, comparisons, verification and plugin table name 
            global  $wpdb;
            $locale = get_locale();
            $hbrabLocale = array("fr_FR","es_ES","pt_PT","de_DE","zh_TW","ja");
            $table_name = $wpdb->prefix . 'hbrab_roles_list';
            $listIdRoles = $wpdb->get_results("SELECT id FROM $table_name WHERE $table_name.role LIKE '%$role%'");
            $support = 'https://kheloshop.com/support/';
            $contact = 'contact@kheloshop.com';
            $donation = 'https://kheloshop.com/donation/';
            $kheloshop ='https://kheloshop.com/';
            $kheloshopPlugin ='https://kheloshop.com/nos-plugins/';
            $logo = plugin_dir_url( __DIR__ ).'inc/LogoKheloShopFlat.png';
            
            // Comparisons locality and request SQL
            if(!in_array($locale,$hbrabLocale)){
                $listRoles = $wpdb->get_results("SELECT * FROM $table_name WHERE $table_name.locale LIKE '%null%'");
            }else{
                $listRoles = $wpdb->get_results("SELECT * FROM $table_name WHERE $table_name.locale LIKE '%$locale%'");
            }
            
            // Skeleton of the plugin admin page with loop of data from the database
            _e('<section id="hbrabOptionPage"><div class="hbrabTitle"><h1>Hidden By Roles the Admin Bar</h1><h2>Customization of roles</h2></div>', 'hidden-by-roles-the-admin-bar');
            _e('<div class="hbrabrow"><div class="tablerow"><table id="bodyRolesDisplay"><thead><tr class="rowbodyRolesDisplay"><th colspan="1">Roles</th><th colspan="1">Display</th><th colspan="1">Status</th></tr></thead><tbody id="bodyListRolesDisplay">', 'hidden-by-roles-the-admin-bar');
            
            // Loop database field with comparison
            for ($i=0; $i< count($listRoles); $i++){
                
                // Variables database field and CheckBox Status
                $idRole = $listRoles[$i]->id;
                $nameRole = $listRoles[$i]->role;
                $nameLocalRole = $listRoles[$i]->localerole;
                $roleDisplay = $listRoles[$i]->display;
                $displayStatut = '';
                
                // Comparisons CheckBox Status
                if($roleDisplay === '1'){ 
                    $displayStatut = 'checked';
                    echo '<tr class="roleDisplayRow" data-id="'.esc_attr($idRole).'"><td colspan="1" class="colRole" data-role="'.esc_attr($nameRole).'">'.esc_attr($nameLocalRole).'</td></td><td colspan="1"  class="colCheckDisplay"><input class="checkDisplay" type="checkbox" '.esc_attr($displayStatut).' data-display="'.esc_attr($roleDisplay).'"></td><td id="'.esc_attr($idRole).'" colspan="1" class="colStatus" data-color></td></tr>';
                }else if($roleDisplay === '0'){ 
                    $displayStatut = '';
                    echo '<tr class="roleDisplayRow" data-id="'.esc_attr($idRole).'"><td colspan="1" class="colRole" data-role="'.esc_attr($nameRole).'">'.esc_attr($nameLocalRole).'</td></td><td colspan="1"  class="colCheckDisplay"><input class="checkDisplay" type="checkbox"  data-display="'.esc_attr($roleDisplay).'"></td><td id="'.esc_attr($idRole).'" colspan="1" class="colStatus" data-color></td></tr>';
                }
            }
            
            // Links donate, support, contact and status messages with translate
            _e('</tbody></table></div><div class="col2"><div class="getcustom"><h3 class="getCust">Roles Custom!</h3><div class="inside"><p>If you use custom user roles, you can manage them thanks to the premium version</p>', 'hidden-by-roles-the-admin-bar');
            echo'<a href="'.esc_url($kheloshopPlugin).'"';
            _e('target="_blank" class="button button-primary">Go Premium</a></div></div>', 'hidden-by-roles-the-admin-bar');
            _e('<div class="donate"><h3 class="thank">Thank You!</h3>', 'hidden-by-roles-the-admin-bar');
            echo'<div class="inside"><a href="'.esc_url($kheloshop).'" target="_blank"><img class="logokheloshop" src="'.esc_attr($logo).'" alt="Logo KhéloShop" /></a>';
            _e('<p>If you find this free plugin useful and want to help us finance future developments, you can make a donation. We look forward to your participation, it will be of great use. Again A big thank you to you!</p>', 'hidden-by-roles-the-admin-bar');
            echo '<p><a href="'.esc_url($donation).'"';
            _e('target="_blank" class="button button-primary">Make a Donation</a></p></div></div></div></div>', 'hidden-by-roles-the-admin-bar');
            _e('<div><p>In the event of a problem, you can be assisted by depositing a ticket at this address: ', 'hidden-by-roles-the-admin-bar');
            echo '<a href="'.esc_url($support).'" target="_blank">'.esc_url($support).'</a>.</p></div>';
            _e('<p>In the event of translation errors, please contact us at the following address: ', 'hidden-by-roles-the-admin-bar');
            echo '<a href="mailto:'.sanitize_email($contact).'">'.sanitize_email($contact).'</a>'; 
            _e('.</p><p id="statusSave">Save!</p><p id="statusError">Error!</p></section>', 'hidden-by-roles-the-admin-bar');
        }
    }
}

if ( ! function_exists( 'HBRABsetupMenu' ) ) {
    // Functions Init && Script CSS for option page
    function HBRABsetupMenu(){
        add_menu_page( 'HBRAB Admin Page', 'Hidden By Roles the Admin Bar', 'manage_options', 'HBRABinit', 'HBRABinit','dashicons-visibility' );
    }
    add_action('admin_menu', 'HBRABsetupMenu');
}

if ( ! function_exists( 'hbrabAdminScript' ) ) {
    function hbrabAdminScript() {
        wp_enqueue_script('jquery');
        wp_register_style( 'hbrab_menuStyle', plugins_url( 'HBRABMenuStyle.css', __FILE__));
        wp_enqueue_style( 'hbrab_menuStyle', plugins_url( 'HBRABMenuStyle.css', __FILE__));
    }
    add_action( 'admin_enqueue_scripts', 'hbrabAdminScript' );
}