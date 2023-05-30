<?php
/**
 * Database Table Creation and Deletion
 *
 * This code handles the creation and deletion of the database table
 * required by the plugin.
 */

/**
 * Create Link Check Master table
 *
 * This function is called during plugin activation and creates the
 * necessary database table for storing link check data.
 */
function link_check_master_create_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'link_check_master';
    // Get the character set collate for creating the table
    $charset_collate = $wpdb->get_charset_collate();
    // Get the character set collate for creating the table
    $sql = "CREATE TABLE $table_name (
        id INT(11) NOT NULL AUTO_INCREMENT,
        URL VARCHAR(255) NOT NULL,
        status_error VARCHAR(255) NOT NULL,
        origin VARCHAR(255) NOT NULL,
        id_post INT(11) NOT NULL,
        latest_revision DATE NOT NULL,
        PRIMARY KEY (id)
    ) $charset_collate;";
    // Include the necessary WordPress upgrade script
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    // Create the table using the defined SQL query
    dbDelta( $sql );
}
// Register the table creation function to be executed during plugin activation
register_activation_hook( LINK_CHECK_MASTER_PLUGIN_FILE , 'link_check_master_create_table' );

/**
 * Drop Link Check Master table
 *
 * This function is called during plugin uninstallation and deletes the
 * database table created by the plugin.
 */
function link_check_master_drop_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'link_check_master';
    // Delete the table if it exists
    $wpdb->query( "DROP TABLE IF EXISTS $table_name" );
}
register_uninstall_hook(LINK_CHECK_MASTER_PLUGIN_FILE, 'link_check_master_drop_table' );




