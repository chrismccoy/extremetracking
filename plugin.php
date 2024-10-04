<?php
/**
 * Plugin Name: Extreme Tracking
 * Plugin URI: http://github.com/chrismccoy/extremetracking
 * Description: Add Extreme Tracking to your Site
 * Version: 1.0
 * Author: Chris McCoy
 * Author URI: http://github.com/chrismccoy

 * @copyright 2024
 * @author Chris McCoy
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @package Extreme_Tracking
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Settings Class for the Extreme Tracking Options
 *
 * @since 1.0
 */

require_once dirname( __FILE__ ) . '/inc/class.settings-api.php';

/**
 * Initiate Extreme Tracking Class on plugins_loaded
 *
 * @since 1.0
 */

if ( !function_exists( 'extreme_tracking' ) ) {

	function extreme_tracking() {
		$extreme_tracking = new Extreme_Tracking();
	}

	add_action( 'plugins_loaded', 'extreme_tracking' );
}

/**
 * Extreme Tracking Class for options and scripts
 *
 * @since 1.0
 */

if( !class_exists( 'Extreme_Tracking' ) ) {

	class Extreme_Tracking {

    		private $settings_api;

		/**
 		* Hooks for options and scripts
 		*
 		* @since 1.0
 		*/

		function __construct() {
        		$this->settings_api = new Extreme_Tracking_Settings_API;

        		add_action( 'admin_init', array($this, 'admin_init') );
        		add_action( 'admin_menu', array($this, 'admin_menu') );
			add_filter( 'script_loader_tag', array($this, 'scripts_data_tag'), 10, 3);
			add_filter( 'script_loader_src', array($this, 'remove_extremetracking_version'), 9999, 2 );
			add_action( 'wp_enqueue_scripts', array( $this, 'scripts' ) );
		}

		/**
		 * get extreme tracking options sections and fields
		 *
		 * @since 1.0
		 */

    		function admin_init() {
        		$this->settings_api->set_sections( $this->get_settings_sections() );
        		$this->settings_api->set_fields( $this->get_settings_fields() );
        		$this->settings_api->admin_init();
    		}

		/**
		 * add extreme tracking options page
		 *
		 * @since 1.0
		 */

    		function admin_menu() {
        		add_options_page( 'Extreme Tracking', 'Extreme Tracking', 'delete_posts', 'extreme_tracking_settings', array($this, 'plugin_page') );
    		}

		/**
		 * extreme tracking options page markup
		 *
		 * @since 1.0
		 */

    		function plugin_page() {
        		echo '<div class="wrap">';
        		$this->settings_api->show_navigation();
        		$this->settings_api->show_forms();
        		echo '</div>';
    		}

		/**
		 * extreme tracking options sections
		 *
		 * @since 1.0
		 */

    		function get_settings_sections() {
        		$sections = array(
            			array(
                			'id'    => 'extreme_tracking_settings',
                			'title' => __( 'Extreme Tracking Settings', 'extremetracking' )
            			)
        		);
        		return $sections;
    		}

		/**
		 * extreme tracking option fields
		 *
		 * @since 1.0
		 */

    		function get_settings_fields() {
        		$settings_fields = array(
            			'extreme_tracking_settings' => array(
                			array(
                    				'name'    => 'extreme_tracking_id',
                    				'label'   => __( 'ID', 'extremetracking' ),
                    				'desc'    => __( 'Your Extreme Tracking Site ID', 'extremetracking' ),
                    				'type'    => 'number',
                			),
            			)
        		);
        		return $settings_fields;
    		}

		/**
		 * enqueue extreme tracking javascript
		 *
		 * @since 1.0
		 */

		function scripts() {
			wp_enqueue_script('extremetracking_js', 'https://eprocode.com/js.js', null, false, true);
        	}

		/**
		 * add async and data id to javascript
		 *
		 * @since 1.0
		 */

		function scripts_data_tag( $tag, $handle, $src ) {
    			if ( 'extremetracking_js' != $handle ) {
        			return $tag;
    			}

			$id = get_option('extreme_tracking_settings')['extreme_tracking_id'];

    			return str_replace( 'id="extremetracking_js-js"></script>', 'id="'. $id .'" async defer></script>', $tag );
		}

		/**
		 * remove version string from extreme tracking script tag
		 *
		 * @since 1.0
		 */

		function remove_extremetracking_version( $src, $handle )  {
    			if ( $handle == 'extremetracking_js' )
        			$src = remove_query_arg( 'ver', $src );

    			return $src;
		}
   	}
}
