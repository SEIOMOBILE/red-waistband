<?php
    /*
    Plugin Name: 中国红腰带
    Plugin URI: http://code.google.com/p/red-waistband/
    Description: 通过AJAX(JSON)访问WordPress资源。 安装后，可以通过http://yoursite.com/?json来测试插件。
    Version: 2.0
    Author: SEIO MOBILE
    Author URI: http://mobile.seio.com/
    */

    /*  Copyright 2013 seio mobile (email: zhahc@seiosoft.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    4th floor, Ningxia RD. 777, Shanghai, China 
    */

    
    $red_waistband_db_version = '2.0';

    /**
    * Catches index.php/?json requests, stops further execution by WordPress
    * and handles the request depending on the request type.
    *
    * The are 3 types of treatment:
    * 1) 
    * 2) 
    * 3) 
    *    
    */
    function red_waistband_handle_request() {	
        // Look for the magic /?json string in the $_SERVER variable
        $json_found = false;
        $json_requested = false;
        foreach($_SERVER as $val) {
            if(strlen($val) >= 5 && substr($val, 0, 6) == "/?json") {
                $json_found = true;
                if(isset($_SERVER["QUERY_STRING"]) && strpos($_SERVER["QUERY_STRING"], "json") !== false) $json_requested = true;
                break;
            }
        }

        if($json_found) {
            // make sure the QUERY_STRING is correctly set to ?json so the instance delivers to right entry
            if($json_requested) {
                add_filter('template_include','template_include_filter');		
            }
        }
        // no wpws-request, go on with WordPress execution
    }
    
    // creates a customized install on plugin activation
    register_activation_hook(__FILE__, 'red_waistband_install');
    // creates a customized uninstall on plugin deactivation
    register_deactivation_hook(__FILE__, 'red_waistband_uninstall');
    // checks whether the request should be handled by AJAX
    add_action("parse_request", "red_waistband_handle_request");

    /**
    * template_include filter to index.php
    * http://yoursite.com/?json -> handle with index.php
    */
    function template_include_filter($template){
        //   return preg_replace('#([^/]+\.php)#','api/$1',$template);
        return dirname(__FILE__).'/index.php';
    }
    
    /**
    * install table for red-waistband plugin
    * 
    */
    function red_waistband_install() {
        global $wpdb;
        global $red_waistband_db_version;
                
        $table_name = $wpdb->prefix . "mobilesession";
        if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
            $sql = "CREATE TABLE " . $table_name . " (
                `session_id` VARCHAR(32) NOT NULL COMMENT 'sesson ID',
                `user_id` BIGINT(20) NULL DEFAULT NULL COMMENT 'user ID',
                `expire_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
                `device` VARCHAR(255) NULL DEFAULT NULL COMMENT ' describes the device\'s hardware and software',
                PRIMARY KEY (`session_id`)
            )
            COLLATE='utf8_general_ci'
            ENGINE=InnoDB;";
            
            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            dbDelta($sql);
        }
        
        $table_name = $wpdb->prefix . "mobileevents";
        if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
            $sql = "CREATE TABLE " . $table_name . " (
                `session_id` VARCHAR(32) NOT NULL COMMENT 'sesson ID',
                `event_id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
                `event_type` VARCHAR(20) NOT NULL,
                `event_date` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
                `event_date_gmt` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
                `event_content` VARCHAR(255) NOT NULL,
                `comment` VARCHAR(255) NULL DEFAULT NULL,
                PRIMARY KEY (`session_id`, `event_id`),
                INDEX `event_id` (`event_id`)
            )
            COLLATE='utf8_general_ci'
            ENGINE=InnoDB;";

            dbDelta($sql);
        }
                
        add_option("red_waistband_db_version", $red_waistband_db_version);
    }
    
    /**
    * uninstall table for red-waistband plugin
    * 
    */    
    function red_waistband_uninstall() {
        delete_option("red_waistband_db_version");
    }
    
?>