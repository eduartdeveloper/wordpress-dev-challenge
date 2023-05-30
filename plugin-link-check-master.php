<?php

/**
 *
 * The plugin bootstrap file
 *
 * This file is responsible for starting the plugin using the main plugin class file.
 *
 * @since 1.0.0
 * @package Link_Check_Master
 *
 * @wordpress-plugin
 * Plugin Name:     Link Check Master
 * Description:     Ensure flawless website performance with Link Check Master, a powerful plugin that analyzes and detects broken links in your WordPress posts. Save time and maintain a seamless user experience by easily identifying and fixing any broken or dead links. Keep your website error-free and boost your SEO ranking with Link Check Master.
 * Version:         1.0.0
 * Author:          Jose Eduardo Rendón Valencia
 * Author URI:      https://joseeduardo.com.co
 * License:         GPL-2.0+
 * License URI:     http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:     link-check-master
 * Domain Path:     /lang
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Direct access not permitted.' );
}

if ( ! class_exists( 'Link_Check_Master' ) ) {

	/*
	 * main Link_Check_Master class
	 *
	 * @class Link_Check_Master
	 * @since 1.0.0
	 */
	class Link_Check_Master {

		/*
		 * Link_Check_Master plugin version
		 *
		 * @var string
		 */
		public $version = '1.0.0';

		/**
		 * The single instance of the class.
		 *
		 * @var Link_Check_Master
		 * @since 1.0.0
		 */
		protected static $instance = null;

		/**
		 * Main Link_Check_Master instance.
		 *
		 * @since 1.0.0
		 * @static
		 * @return Link_Check_Master - main instance.
		 */
		public static function instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}
			return self::$instance;
		}

		/**
		 * Link_Check_Master class constructor.
		 */
		public function __construct() {
			$this->load_plugin_textdomain();
			$this->define_constants();
			$this->includes();
			$this->define_actions();
			$this->define_menus();
		}

		public function load_plugin_textdomain() {
			load_plugin_textdomain( 'link-check-master', false, basename( dirname( __FILE__ ) ) . '/lang/' );
		}

		/**
		 * Include required core files
		 */
		public function includes() {
            
			// Load custom functions and hooks
			require_once __DIR__ . '/includes/includes.php';


			require_once __DIR__ . '/templates/admin/list_links_errors.php';
		}

		/**
		 * Get the plugin path.
		 *
		 * @return string
		 */
		public function plugin_path() {
			return untrailingslashit( plugin_dir_path( __FILE__ ) );
		}


		/**
		 * Define Link_Check_Master constants
		 */
		private function define_constants() {
			define( 'LINK_CHECK_MASTER_PLUGIN_FILE', __FILE__ );
			define( 'LINK_CHECK_MASTER_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
			define( 'LINK_CHECK_MASTER_VERSION', $this->version );
			define( 'LINK_CHECK_MASTER_PATH', $this->plugin_path() );
			define( 'LINK_CHECK_MASTER_DIR_URL', plugin_dir_url(__FILE__) );
		}

		/**
		 * Define Link_Check_Master actions
		 */
		public function define_actions() {
			//
		}

		/**
		 * Define Link_Check_Master menus
		 */
		public function define_menus() {
            
			function LCK__register_menu(){
				add_menu_page('Errores de links','Errores de links', 'manage_options', 'link-check-master' , 'list_links_errors', 'dashicons-editor-unlink', 2);
			}
			add_action('admin_menu', 'LCK__register_menu');
		
		}
	}

	$Link_check_master = new Link_Check_Master();
}
