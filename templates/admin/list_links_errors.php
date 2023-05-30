<?php
/**
 * Links Errors
 *
 * This class extends the WP_List_Errors class and provides functionality
 * for displaying a list of links with errors in WordPress admin area.
 */
require_once LINK_CHECK_MASTER_PATH . "/includes/admin/Table.php";

class Links_Errors extends WP_List_Errors {
    /**
     * Prepare the items to be displayed in the list.
     */
    public function prepare_items() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'link_check_master';

        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();

        $this->_column_headers = array($columns, $hidden, $sortable);

        // Retrieve records from the table
        $orderby = isset($_REQUEST['orderby']) ? sanitize_key($_REQUEST['orderby']) : 'URL';
        $order = isset($_REQUEST['order']) && in_array($_REQUEST['order'], array('asc', 'desc')) ? $_REQUEST['order'] : 'asc';

        $search = isset($_REQUEST['s']) ? sanitize_text_field($_REQUEST['s']) : '';
        $where = $search ? "WHERE URL LIKE '%$search%'" : '';

        $results = $wpdb->get_results("SELECT URL, status_error, origin, id_post FROM $table_name $where ORDER BY $orderby $order");

        // Pagination
        $current_page = $this->get_pagenum();
        $per_page = 20;
        $total_items = count($results);

         // Set pagination data
        $this->set_pagination_args(array(
            'total_items' => $total_items,
            'per_page' => $per_page,
        ));

        // Get the items to display on the current page
        $this->items = array_slice($results, ($current_page - 1) * $per_page, $per_page);
    }

    /**
     * Get the columns for the list table.
     *
     * @return array An associative array of columns.
     */
    public function get_columns() {
        $columns = array(
            'URL' => 'URL',
            'status_error' => 'Status',
            'origin' => 'Origin (link name)',
        );

        return $columns;
    }

    /**
     * Get the sortable columns for the list table.
     *
     * @return array An associative array of sortable columns.
     */
    public function get_sortable_columns() {
        $sortable_columns = array(
            'URL' => array('URL', false),
            'status_error' => array('status_error', false),
            'origin' => array('origin', false),
        );

        return $sortable_columns;
    }

    /**
     * Render the default column.
     *
     * @param object $item        The current item.
     * @param string $column_name The name of the column.
     * @return string             The HTML markup for the column.
     */
    public function column_default($item, $column_name) {
        switch ($column_name) {
            case 'URL':
            case 'status_error':
                return $item->$column_name;
            case 'origin':
                $linkedit = get_edit_post_link($item->id_post);
                return "Edit: <a href='{$linkedit}' target='_blank'>{$item->$column_name}</a>";
            default:
                return '';
        }
    }
    
    /**
     * Render the message when no items are found.
     */
    public function no_items() {
        echo 'No se encontraron elementos.';
    }
}

/**
 * Display the list of links with errors.
 */
function list_links_errors() {
    ?>
    <div class="wrap">
        <h1 class="wp-heading-inline">Links con errores</h1>
        <br><br>
        <form method="get">
            <input type="hidden" name="page" value="<?php echo $_REQUEST['page']; ?>">
            <label for="link-search-input">Buscar por URL:</label>
            <input type="search" id="link-search-input" name="s" value="<?php echo isset($_REQUEST['s']) ? esc_attr($_REQUEST['s']) : ''; ?>">
            <input type="submit" class="button" value="Buscar">
        </form>
        <br>
        <div style="float: right;">
            <a href="#" class="button" onclick="reloadPageWithValidator()">Hacer análisis Forzoso</a>
        </div>
        
        <?php
        $Links_errors = new Links_Errors();
        $Links_errors->prepare_items();
        $Links_errors->display();
        ?>
        <?php require_once LINK_CHECK_MASTER_PATH . "/includes/admin/validatorForce.php"; ?>
    </div>
    <?php
        if(isset($_GET['validator'])){
            if($_GET['validator'] == 'force'){
                force();
            }
        }
    ?>
    
    <script>
        function reloadPageWithValidator() {
            alert("Be patient, this will take a while!");
            var currentUrl = window.location.href;
            var url = new URL(currentUrl);
            url.searchParams.set('validator', 'force');
            var newUrl = url.href;
            window.location.href = newUrl;
        }
    </script>
    <?php
}
