<?php
if (!defined('WP_UNINSTALL_PLUGIN')) {
    die;
}

    global $wpdb;
	global $hbrab_db_version;
    

    delete_option('hbrab_db_version',$hbrab_db_version);
    $table_name = $wpdb->prefix . 'hbrab_roles_list';
    $sql = "DROP TABLE IF EXISTS $table_name";
    $wpdb->query($sql);
