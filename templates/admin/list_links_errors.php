<?php

require_once LINK_CHECK_MASTER_PATH."/includes/admin/Table.php";
class Links_Errors extends WP_List_Errors {
    public $errors = array(
        array(
            'URL' => 'https://example.com/page1',
            'status' => '404',
            'origin' => 'https://example.com',
        ),
        array(
            'URL' => 'https://example.com/page2',
            'status' => '500',
            'origin' => 'https://example.com',
        ),
    );

    public function get_columns(){
        $columns = array (
            'URL' => 'URL',
            'status' => 'Status',
            'origin' => 'Origin',
        ); 
    
        return $columns; 
    }
    
    public function prepare_items(){
        $columns = $this->get_columns(); 
        $this->_column_headers = array( $columns ); 
        $this->items = $this->errors;
    }

    public function column_default( $item, $column_name ){
        switch( $column_name ) { 
            case 'URL':
            case 'status':
            return  'cy';
          default:
            return  'no';
        }
    }


}




function list_links_errors(){
    ?>
    <div class="wrap">
        <h1 class="wp-heading-inline">Links con errores</h1>
        <br><br>
            <!-- fecha. nombre, estado btn -->
            <?php 
                $Links_errors = new Links_Errors();
                $Links_errors->prepare_items();
                $Links_errors->display();
            ?>
            <p>si</p>
            <?php
                function validate_links_in_post_content($post_id) {
                    // Obtener el contenido del post
                    $post_content = get_post_field('post_content', $post_id);
                    
                    // Encontrar todos los enlaces en el contenido del post
                    preg_match_all('/<a\s[^>]*href=["\'](.*?)["\'][^>]*>(.*?)<\/a>/', $post_content, $matches);
                    
                    // Array para almacenar los enlaces con errores
                    $error_links = array();
                    
                    // Recorrer los enlaces encontrados
                    $count = 0;
                    foreach ($matches[1] as $link) {
                        // Validar el enlace
                        
                        // Enlace inseguro
                        if (strpos($link, 'http://') === 0) {
                            $error_links[] = array(
                                'link' => $link,
                                'text' => $matches[2][$count],
                                'error' => 'Enlace inseguro'
                            );
                        }
                        
                        // Protocolo no especificado
                        if (strpos($link, '://') === false || strpos($link, '//') > strpos($link, '/')) {
                            $error_links[] = array(
                                'link' => $link,
                                'text' => $matches[2][$count],
                                'error' => 'Protocolo no especificado'
                            );
                        }
                        // Validar si solo se proporcionó una ruta relativa a partir del dominio
                        $parsed_link = parse_url($link);
                        if (isset($parsed_link['host']) && !isset($parsed_link['path'])) {
                            $error_links[] = array(
                                'link' => $link,
                                'text' => $matches[2][$count],
                                'error' => 'Protocolo no especificado'
                            );
                        }
                        
                        // Enlace malformado
                        $url_parts = parse_url($link);
                        if (!$url_parts || !isset($url_parts['scheme']) || !isset($url_parts['host'])) {
                            $error_links[] = array(
                                'link' => $link,
                                'text' => $matches[2][$count],
                                'error' => 'Enlace malformado'
                            );
                        }
                        // Enlace malformado
                        if (strpos($link, '://') === false && strpos($link, '//') !== 0 && strpos($link, '/') !== 0) {
                            $error_links[] = array(
                                'link' => $link,
                                'text' => $matches[2][$count],
                                'error' => 'Enlace malformado'
                            );
                        }
                        $count++;
                    }
                    
                    // Imprimir los enlaces con errores
                    if (!empty($error_links)) {
                        echo '<h2>Enlaces con errores:</h2>';
                        echo '<ul>';
                        foreach ($error_links as $error_link) {
                            echo '<li>Enlace: ' . $error_link['link'] . ' - Error: ' . $error_link['error'] . ' - Nombre: ' . $error_link['text'] . '</li>';
                        }
                        echo '</ul>';
                    } else {
                        echo '<p>No se encontraron enlaces con errores.</p>';
                    }
                }
                
                // Llamar a la función pasando el ID del post deseado (en este caso, 81)
                validate_links_in_post_content(54);
                
            ?>
    </div>
    <?php
}