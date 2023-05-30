<?php
/**
 * Link validation in post content
 *
 * This code verifies the links in the content of a post and performs various validations.
 */

/**
 * Function to validate links in post content
 *
 * @param int $post_id Post ID
 */
function validate_links_in_post_content($post_id) {
    // Get the post content
    $post_content = get_post_field('post_content', $post_id);
    
    // Find all the links in the post content
    preg_match_all('/<a\s[^>]*href=["\'](.*?)["\'][^>]*>(.*?)<\/a>/', $post_content, $matches);
    
    // Array to store links with errors
    $error_links = array();
    
    // Iterate through the found links
    $count = 0;
    foreach ($matches[1] as $link) {
        // Validate the link
        
        // Insecure link
        if (strpos($link, 'http://') === 0) {
            $error_links[] = array(
                'link' => $link,
                'text' => $matches[2][$count],
                'error' => 'Insecure link'
            );
        }
        
        // Protocol not specified
        if (strpos($link, '://') === false || strpos($link, '//') > strpos($link, '/')) {
            $error_links[] = array(
                'link' => $link,
                'text' => $matches[2][$count],
                'error' => 'Protocol not specified'
            );
        }
        // Check if only a relative path was provided starting from the domain
        $parsed_link = parse_url($link);
        if (isset($parsed_link['host']) && !isset($parsed_link['path'])) {
            $error_links[] = array(
                'link' => $link,
                'text' => $matches[2][$count],
                'error' => 'Protocol not specified'
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
        // Malformed link
        if (strpos($link, '://') === false && strpos($link, '//') !== 0 && strpos($link, '/') !== 0) {
            $error_links[] = array(
                'link' => $link,
                'text' => $matches[2][$count],
                'error' => 'Malformed link'
            );
        }

        // Perform the HEAD request
        $response = wp_remote_head($link);
        
        // Check the response code
        $response_code = wp_remote_retrieve_response_code($response);
        if ($response_code >= 400) {
            $error_links[] = array(
                'link' => $link,
                'text' => $matches[2][$count],
                'error' => 'HTTP response error: ' . $response_code
            );
        }

        $count++;
    }
    
    // Insert into the database
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
                //  No record with the same URL exists, insert a new one
                $data = array(
                    'URL' => $error_link['link'],
                    'status_error' => $error_link['error'],
                    'origin' => $error_link['text'],
                    'id_post' => $post_id,
                    'latest_revision' => date('Y-m-d'),
                );
                
                $wpdb->insert($table_name, $data);
            } else {
                // Update the latest_revision field in the existing record
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



$args = array(
    'post_type' => 'post',  //Post type to retrieve
    'posts_per_page' => -1, // Number of posts per page (-1 to retrieve all)>
);

$posts = get_posts($args);
$totalIterations = 20000;
$iterationsPerMinute = 15;
$delaySeconds = 60 / $iterationsPerMinute;



foreach ($posts as $post) {
    setup_postdata($post);

    $latest_revision_post = get_post_meta($post->ID, '_latest_revision_post', true);
    // Check if the date difference is less than 4 days
    $diff = strtotime(date('Y-m-d')) - strtotime($latest_revision_post);
    $days_diff = floor($diff / (60 * 60 * 24)); //Difference in days
    if ($days_diff < 4) {
        continue; // Skip to the next iteration of the loop
    }

    validate_links_in_post_content($post->ID);
    sleep($delaySeconds);
    update_post_meta($post->ID, '_latest_revision_post', date('Y-m-d'));
}

wp_reset_postdata();


