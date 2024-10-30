<?php
/**
 * @package hidden by roles the admin bar
 * @version 1.0.
 */
/*
Plugin Name: Hidden By Roles the Admin Bar
Plugin URI: https://www.kheloshop.com/
Description: Free version of Hidden By Roles the Admin Bar allowing to hide the Admin Bar according to the roles of basic users. Configurable from the dedicated tab directly on the Wordpress Dashboard. A premium version managing custom roles available on https://kheloshop.com/boutique/wordpress/plugins/. Plugin translated into the following languages: Deutsche, Català, English, Français, Nihonjin, Português, Pǔtōnghuà.
Author: Saunier François
Text Domain: hidden-by-roles-the-admin-bar
Domain Path: /languages
Version: 1.0
Author URI: https://www.khelosinnovation.com/
*/


if ( ! defined( 'ABSPATH' ) ) {
	die();
}

define( 'HBRAB_DIR', plugin_dir_path( __FILE__ ) );

require_once(ABSPATH .'wp-includes/pluggable.php');
require_once( HBRAB_DIR . 'inc/HBRABAdmin.php' );

// Variable database version
global $hbrab_db_version;
$hbrab_db_version = '1.0';

if ( ! function_exists( 'hbrabInstall' ) ) {
    // Create database table at activation
    function hbrabInstall() {
        
	global $wpdb;
        global $hbrab_db_version;
        
    // Variables and table creation
        $table_name = $wpdb->prefix . 'hbrab_roles_list';
        $charset_collate = $wpdb->get_charset_collate();
        $sql = "CREATE TABLE $table_name (
        id int(11) NOT NULL AUTO_INCREMENT,
        role varchar(255) DEFAULT '' NOT NULL,
        localerole varchar(255) DEFAULT '' NOT NULL,
        locale varchar(255) DEFAULT '' NOT NULL,
        display BOOLEAN NOT NULL,
        PRIMARY KEY (id)) $charset_collate;";
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );
        add_option( 'hbrab_db_version', $hbrab_db_version );
    }
    register_activation_hook (__FILE__, 'hbrabInstall'); 
}

if ( ! function_exists( 'hbrabInstallData' ) ) {
    // Filling the database with translation at activation
    function hbrabInstallData() {
        
	global $wpdb;
        
    // Variables for insert table 
    $table_name = $wpdb->prefix . 'hbrab_roles_list';
    $roleDisplay = array(array("administrator","Administrator","null",TRUE),array("editor","Editor","null",TRUE),array("author","Author","null",TRUE),array("contributor","Contributor","null",TRUE),array("subscriber","Subscriber","null",TRUE),array("administrator","Administrateur","fr_FR",TRUE),array("editor","Éditeur","fr_FR",TRUE),array("author","Auteur","fr_FR",TRUE),array("contributor","Contributeur","fr_FR",TRUE),array("subscriber","Abonné","fr_FR",TRUE),array("administrator","Administrador","es_ES",TRUE),array("editor","Editor","es_ES",TRUE),array("author","Autor","es_ES",TRUE),array("contributor","Colaborador","es_ES",TRUE),array("subscriber","Subscriptor","es_ES",TRUE),array("administrator","Administrador","pt_PT",TRUE),array("editor","Editor","pt_PT",TRUE),array("author","Autor","pt_PT",TRUE),array("contributor","Contribuinte","pt_PT",TRUE),array("subscriber","Assinante","pt_PT",TRUE),array("administrator","Administrator","de_DE",TRUE),array("editor","Editor","de_DE",TRUE),array("author","Autor","de_DE",TRUE),array("contributor","Mitwirkender","de_DE",TRUE),array("subscriber","Teilnehmer","de_DE",TRUE),array("administrator","Guǎnlǐ yuán","zh_TW",TRUE),array("editor","Biānjí","zh_TW",TRUE),array("author","Zuòzhě","zh_TW",TRUE),array("contributor","Gòngxiàn zhě","zh_TW",TRUE),array("subscriber","Dìnghù","zh_CN",TRUE),array("administrator","Kanrisha","ja",TRUE),array("editor","Henshū-sha","ja",TRUE),array("author","Chosha","ja",TRUE),array("contributor","Kikō-sha","ja",TRUE),array("subscriber","kanyū-sha","ja",TRUE));
    
    // Variables and comparison to prevent duplication and insertion into the table 
    $verif = $wpdb->get_results("SELECT * FROM $table_name ");
    $nRows = count( $verif );
    if($nRows < 1){
        for ($i=0; $i< count($roleDisplay); $i++){
            $wpdb->insert( 
                $table_name,
                array( 
                    'role' => $roleDisplay[$i][0],
                    'localerole' => $roleDisplay[$i][1],
                    'locale' => $roleDisplay[$i][2],
                    'display' => $roleDisplay[$i][3]
                ) 
            );
        }
    }
}
    register_activation_hook (__FILE__, 'hbrabInstallData');
}

if ( ! function_exists( 'hbrabCompareRoles' ) ) {
    // Display function of the admin bar by comparing the current user and the options in the database
    function hbrabCompareRoles() {
        
        global $wpdb;
        global $show_admin_bar;
        
        // User log verification
        if( is_user_logged_in() ) {
            
        // Variables current user, database and option display admin bar
            $user = wp_get_current_user();
            $role = ( array ) $user->roles;
            $role = $role[0]; 
            $table_name = $wpdb->prefix . 'hbrab_roles_list';
            $getRoleDisplay = $wpdb->get_results("SELECT * FROM $table_name WHERE $table_name.role LIKE '%$role%' AND $table_name.locale LIKE '%null%'");
            $roleDisplay = $getRoleDisplay[0]->display;
            
            // Comparison and application of the display status of the admin bar
            if($roleDisplay === '1'){
                show_admin_bar( TRUE );
            }else if ($roleDisplay === '0'){
                show_admin_bar( FALSE );
            }
        } else {
            return array();
        }
    }
    add_action ('plugins_loaded', 'hbrabCompareRoles');
}

if ( ! function_exists( 'hbrabAjaxScript' ) ) {

    add_action( 'admin_enqueue_scripts', 'hbrabAjaxScript' );
    add_action( 'wp_ajax_hbrabAjax', 'hbrabAjax' );
    function hbrabAjaxScript() {
        wp_enqueue_script('hbrabAjax', plugins_url( '/inc/HBRABAjax.js' , __FILE__ ) , array( 'jquery' ), false, true);
        wp_localize_script( 'hbrabAjax', 'hbrabAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php')));
    }
    do_action( 'hbrabAjax' . $_REQUEST['action'] );
}

if ( ! function_exists( 'hbrabAddNonce' ) ) {
    //Add a general nonce to requests
    function hbrabAddNonce() {
        $nonce = wp_create_nonce( 'hbrab_nonce' );
        echo "<meta name='hbrab-token' content='$nonce'>";
    }
    // To add to admin pages
    add_action( 'admin_head', 'hbrabAddNonce' );
}

if ( ! function_exists( 'hbrabGeneralNonce' ) ) {
    //Verify the submitted nonce
    function hbrabGeneralNonce() {
        $nonce = isset( $_SERVER['HTTP_X_HBRAB_TOKEN'] ) ? $_SERVER['HTTP_X_HBRAB_TOKEN']: '';
        if ( !wp_verify_nonce( $nonce, 'hbrab_nonce' ) ) {
            die();
        }
    }
}

if ( ! function_exists( 'hbrabAjax' ) ) {
    // Recording in database of the choices of plugin options via Ajax After verifing nonce
    function hbrabAjax() {
        
        // Verify nonce in admin page
        hbrabGeneralNonce();
        
        // Variables database, sanitize and validate $_POST
        global  $wpdb;
        $id = intval($_POST['id']);
        $role = sanitize_text_field($_POST['role']);
        $display = sanitize_text_field($_POST['roleDisplay']);
        $table_name = $wpdb->prefix . 'hbrab_roles_list';
        $wpdb->show_errors();
        $hbrabRoles = array("administrator","editor","author","contributor","subscriber");
        
        if(!in_array($role,$hbrabRoles)){
            $role = '';
        }
        if ( is_int($id) != true ) {
            $id = '';
        }
        if ( strlen( $display ) > 1 ) {
            $display = '';
        }
        
        //Comparison entry
        if( $id != '' && $role != '' &&  $display != '' ){ 
            // Select all id on table hbrab_roles_list per role
            $listIdRoles = $wpdb->get_results("SELECT id FROM $table_name WHERE $table_name.role LIKE '%$role%'");
            
            // Update of all co-entry on the role
            for ($i=0; $i< count($listIdRoles); $i++){
                $wpdb->update( $table_name, array( 'display' => $display ), array( 'id' => $listIdRoles[$i]->id ), array( '%d' ), array( '%d' ));  
            }
            die();
            return true;
        }   
}
    add_action ( 'hbrabAjax' , 'hbrabAjax' );
}

if ( ! function_exists( 'hbrabLoadTextdomain' ) ) {
    // Translation init
    function hbrabLoadTextdomain() {
        load_plugin_textdomain( 'hidden-by-roles-the-admin-bar', false, dirname( plugin_basename( __FILE__ )) . '/languages' );
    }
    add_action ('plugins_loaded', 'hbrabLoadTextdomain');
    add_action( 'init', 'hbrabLoadTextdomain' );
}

if ( ! function_exists( 'hbrabActionLinks' ) ) {
    // Add action links of plugin with translation
    add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'hbrabActionLinks' );
    function hbrabActionLinks( $links ) {
        $links['Settings'] = '<a href="'. esc_url( get_admin_url(null, 'options-general.php?page=HBRABinit') ) .'">' .esc_attr( __( 'Settings', 'hidden-by-roles-the-admin-bar' )) . '</a>';
        $links['More'] = '<a href="'.esc_url('https://kheloshop.com/nos-plugins/').'" target="_blank">' . esc_attr( __( 'More', 'hidden-by-roles-the-admin-bar' )) . '</a>';
        $links['Support'] = '<a href="'.esc_url('https://kheloshop.com/support/').'" target="_blank">' .esc_attr( __( 'Support', 'hidden-by-roles-the-admin-bar' )). '</a>';
        return $links;
    }
}



