<?php
/**
* Plugin Name: Airchat
* Version: 1.0.0
* Author: Airchat
* Author URI: https://airchat.us/
* Description: It will insert an airchat code snippet required to run the bot on your wordpress site
* License: GPL2
*/

/*  Copyright 2019 AIRCHAT.US

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/**
* Airchat Class
*/
class Airchat {
	/**
	* Constructor
	*/
	public function __construct() {

		// Plugin Details
        $this->plugin               = new stdClass;
        $this->plugin->name         = 'airchat';
        $this->plugin->displayName  = 'Airchat';
        $this->plugin->version      = '1.0.0';
        $this->plugin->folder       = plugin_dir_path( __FILE__ );
        $this->plugin->url          = plugin_dir_url( __FILE__ );
        $this->plugin->db_welcome_dismissed_key = $this->plugin->name . '_welcome_dismissed_key';

		// Hooks
		add_action( 'admin_init', array( &$this, 'registerSettings' ) );
        add_action( 'admin_menu', array( &$this, 'adminPanelsAndMetaBoxes' ) );
        add_action( 'admin_notices', array( &$this, 'dashboardNotices' ) );
        add_action( 'wp_ajax_' . $this->plugin->name . '_dismiss_dashboard_notices', array( &$this, 'dismissDashboardNotices' ) );

        // Frontend Hooks
        add_action( 'wp_head', array( &$this, 'frontendHeader' ) );

		// Filters
		add_filter( 'dashboard_secondary_items', array( &$this, 'dashboardSecondaryItems' ) );
	}

    /**
     * Number of Secondary feed items to show
     */
	function dashboardSecondaryItems() {
		return 6;
	}

    /**
     * Show relevant notices for the plugin
     */
    function dashboardNotices() {
        global $pagenow;

        if ( !get_option( $this->plugin->db_welcome_dismissed_key ) ) {
        	if ( ! ( $pagenow == 'options-general.php' && isset( $_GET['page'] ) && $_GET['page'] == 'airchat' ) ) {
	            $setting_page = admin_url( 'options-general.php?page=' . $this->plugin->name );
	            // load the notices view
                include_once( $this->plugin->folder . '/views/dashboard-notices.php' );
        	}
        }
    }

    /**
     * Dismiss the welcome notice for the plugin
     */
    function dismissDashboardNotices() {
    	check_ajax_referer( $this->plugin->name . '-nonce', 'nonce' );
        // user has dismissed the welcome notice
        update_option( $this->plugin->db_welcome_dismissed_key, 1 );
        exit;
    }

	/**
	* Register Settings
	*/
	function registerSettings() {
		register_setting( $this->plugin->name, 'ac-bot-id', 'trim' );
	}

	/**
    * Register the plugin settings panel
    */
    function adminPanelsAndMetaBoxes() {
    	add_submenu_page( 'options-general.php', $this->plugin->displayName, $this->plugin->displayName, 'manage_options', $this->plugin->name, array( &$this, 'adminPanel' ) );
	}

    /**
    * Output the Administration Panel
    * Save POSTed data from the Administration Panel into a WordPress option
    */
    function adminPanel() {
		// only admin user can access this page
		if ( !current_user_can( 'administrator' ) ) {
			echo '<p>' . __( 'Sorry, you are not allowed to access this page.', 'airchat' ) . '</p>';
			return;
		}

    	// Save Settings
        if ( isset( $_REQUEST['submit'] ) ) {
        	// Check nonce
			if ( !isset( $_REQUEST[$this->plugin->name.'_nonce'] ) ) {
	        	// Missing nonce
	        	$this->errorMessage = __( 'nonce field is missing. Settings NOT saved.', 'airchat' );
        	} elseif ( !wp_verify_nonce( $_REQUEST[$this->plugin->name.'_nonce'], $this->plugin->name ) ) {
	        	// Invalid nonce
	        	$this->errorMessage = __( 'Invalid nonce specified. Settings NOT saved.', 'airchat' );
        	} else {
	        	// Save
				// $_REQUEST has already been slashed by wp_magic_quotes in wp-settings
				// so do nothing before saving
	    		update_option( 'ac-bot-id', sanitize_text_field( $_REQUEST['ac-bot-id'] ) );
	    		update_option( $this->plugin->db_welcome_dismissed_key, 1 );
				$this->message = __( 'Settings Saved.', 'airchat' );
			}
        }

        // Get latest settings
        $this->settings = array(
			'ac-bot-id' => esc_html( wp_unslash( get_option( 'ac-bot-id' ) ) ),
        );

    	// Load Settings Form
        include_once( $this->plugin->folder . '/views/settings.php' );
    }

	/**
	* Outputs script / CSS to the frontend header
	*/
	function frontendHeader() {
		$this->output( 'ac-bot-id' );
	}

	/**
	* Outputs the given setting, if conditions are met
	*
	* @param string $setting Setting Name
	* @return output
	*/
	function output( $setting ) {
		// Ignore admin, feed, robots or trackbacks
		if ( is_admin() || is_feed() || is_robots() || is_trackback() ) {
			return;
		}
		
		// Get meta
		$meta = get_option( $setting );
		if ( empty( $meta ) ) {
			return;
		}
		if ( trim( $meta ) == '' ) {
			return;
		}
		
		$meta = '<script type="text/javascript">/*Airchat snippet*/ document.airchat = { botId: "' . $meta . '", widgetUrl: "https://widget.airchat.us" }; (function() { var s = document.createElement("script"); s.src = "https://widget.airchat.us/scripts/init.js"; document.head.appendChild(s); }());</script>';
		
		// Output
		echo wp_unslash( $meta );
	}
}

$airchat = new Airchat();