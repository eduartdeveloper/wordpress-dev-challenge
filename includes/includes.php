<?php
/**
 *
 * It includes all the necessary files
 * for the plugin to function properly.
 */
if ( ! defined('ABSPATH') ) {
    die('Direct access not permitted.');
}

/**
 * Include hooks files
 *
 * Include all PHP files in the 'hooks' directory.
 */
foreach ( glob( (__DIR__) . '/hooks/*.php' ) as $filename ) {
    require_once $filename;
}

/**
 * Include functions files
 *
 * Include all PHP files in the 'functions' directory.
 */
foreach ( glob( (__DIR__) . '/functions/*.php' ) as $filename ) {
    require_once $filename;
}
