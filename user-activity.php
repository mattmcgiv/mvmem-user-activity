<?php

/**
 * Plugin Name: MVMEM User Activity
 * Description: Track your users.
 * Version: 0.1
 * Author: Matt McGivney
 * Author URI: http://antym.com
 * Stable tag: 0.1
 * Tested up to: 3.9
 * License: GPL2
 */
 
 /*  Copyright 2014 Matt McGivney  (email : matt@antym.com)

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



global $mmm_db_version;
$mmm_db_version = '0.1';

function mmm_install() {
	global $wpdb;
	global $mmm_db_version;

	$table_name = $wpdb->prefix . 'user_activity';
	
	/*
	 * Set charset and collate
	 */
	$charset_collate = '';

	if ( ! empty( $wpdb->charset ) ) {
	  $charset_collate = "DEFAULT CHARACTER SET {$wpdb->charset}";
	}

	if ( ! empty( $wpdb->collate ) ) {
	  $charset_collate .= " COLLATE {$wpdb->collate}";
	}

	$sql = "CREATE TABLE $table_name (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		name text NOT NULL,		
		time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
		url VARCHAR(55) DEFAULT '' NOT NULL,
		UNIQUE KEY id (id)
	) $charset_collate;";

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta($sql);

	add_option( 'mmm_db_version', $mmm_db_version );
}

function mmm_install_data() {
	global $wpdb;
	
	$user_name = 'Matt McGivney';
	$activity_time = current_time('mysql');
	
	$table_name = $wpdb->prefix . 'user_activity';
	
	
	$wpdb->insert( 
		$table_name, 
		array( 
			'time' => current_time( 'mysql' ), 
			'name' => $user_name,
			'url' => 'http://antym.com' 
		) 
	);
}

register_activation_hook( __FILE__, 'mmm_install' );
register_activation_hook( __FILE__, 'mmm_install_data' );

add_action( 'get_header', 'mmm_log_activity');

function mmm_log_activity() {
	get_currentuserinfo();
	global $user_identity;
	global $wpdb;
	
	$activity_time = current_time('mysql');
	$current_user = $user_identity;
	$current_url = get_the_title();
	
	$table_name = $wpdb->prefix . 'user_activity';
	
	
	$wpdb->insert( 
		$table_name, 
		array( 
			'time' => $activity_time, 
			'name' => $current_user,
			'url' => $current_url 
		) 
	);
	
	
}