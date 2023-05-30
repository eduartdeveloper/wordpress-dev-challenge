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

        // Realizar la solicitud HEAD
        $response = wp_remote_head($link);
        
        // Verificar el código de respuesta
        $response_code = wp_remote_retrieve_response_code($response);
        if ($response_code >= 400) {
            $error_links[] = array(
                'link' => $link,
                'text' => $matches[2][$count],
                'error' => 'Error de respuesta HTTP: ' . $response_code
            );
        }

        $count++;
    }
    
    // Insertar en la base de datos
    global $wpdb;
    $table_name = $wpdb->prefix . 'link_check_master';
    
    if (!empty($error_links)) {
        foreach ($error_links as $error_link) {
            $existing_record = $wpdb->get_results(
                $wpdb->prepare(
                    "SELECT * FROM $table_name WHERE URL = %s",
                    $error_link['link']
                )
            );
            
            if (empty($existing_record)) {
                // No existe un registro con la misma URL, insertar uno nuevo
                $data = array(
                    'URL' => $error_link['link'],
                    'status_error' => $error_link['error'],
                    'origin' => $error_link['text'],
                    'id_post' => $post_id,
                    'latest_revision' => date('Y-m-d'),
                );
                
                $wpdb->insert($table_name, $data);
            } else {
                // Actualizar el campo latest_revision en el registro existente
                $wpdb->update(
                    $table_name,
                    array('latest_revision' => date('Y-m-d')),
                    array('URL' => $error_link['link'])
                );
            }
            
            update_post_meta($post_id, '_latest_revision_post', date('Y-m-d'));
        }
    }

     
     
     
}

function force(){

    $args = array(
        'post_type' => 'post',  // Tipo de post a recuperar
        'posts_per_page' => -1, // Número de posts por página (-1 para obtener todos)
    );
    
    $posts = get_posts($args);
    $totalIterations = 20000;
    $iterationsPerMinute = 15;
    $delaySeconds = 60 / $iterationsPerMinute;
    
    
    
    
    global $wpdb;
    $table_name = $wpdb->prefix . 'link_check_master';

    // Vaciar la tabla
    $wpdb->query("TRUNCATE TABLE $table_name");
    
    foreach ($posts as $post) {
        setup_postdata($post);
    
        
    
        validate_links_in_post_content($post->ID);
        echo($post->ID);
        sleep($delaySeconds);
        update_post_meta($post->ID, '_latest_revision_post', date('Y-m-d'));
    }
    
    wp_reset_postdata();
    
    
}


