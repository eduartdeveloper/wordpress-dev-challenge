<?php

function link_check_master_create_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'link_check_master';

    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id INT(11) NOT NULL AUTO_INCREMENT,
        URL VARCHAR(255) NOT NULL,
        status_error VARCHAR(255) NOT NULL,
        origin VARCHAR(255) NOT NULL,
        id_post INT(11) NOT NULL,
        latest_revision DATE NOT NULL,
        PRIMARY KEY (id)
    ) $charset_collate;";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql );
}
register_activation_hook( LINK_CHECK_MASTER_PLUGIN_FILE , 'link_check_master_create_table' );


function link_check_master_drop_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'link_check_master';

    $wpdb->query( "DROP TABLE IF EXISTS $table_name" );
}
register_uninstall_hook(LINK_CHECK_MASTER_PLUGIN_FILE, 'link_check_master_drop_table' );




