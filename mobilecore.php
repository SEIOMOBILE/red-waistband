<?php
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
class MobileCore {
    var $post_name;
    var $page_title;
    var $page_content;
        
    var $bgImages;
    
    function __construct($post_name) {
        $this->post_name = $post_name;
        
        $this->getPage();
    } 
    
    function getContent() {
        
    }
        
    /**
    * page
    * 
    * @param mixed $post_type
    * @param mixed $post_status
    */
    function getPage($post_type='page', $post_status='publish') {
        global $wpdb; 
        // page title       
        $post = $wpdb->get_row( $wpdb->prepare( "
            SELECT *
            FROM $wpdb->posts
            WHERE post_type = %s AND post_status = %s AND post_name = %s
        ", $post_type, $post_status, $this->post_name.'-title') );
               
        if ( $post ) {
            $this->page_title = $post->post_content;   
        }
        
        // page content
        $post = $wpdb->get_row( $wpdb->prepare( "
            SELECT *
            FROM $wpdb->posts
            WHERE post_type = %s AND post_status = %s AND post_name = %s
        ", $post_type, $post_status, $this->post_name) );
               
        if ( $post ) {
            $this->page_content = $post->post_content;   
        }
    } 
    
    /**
    * background 
    * 
    * @param mixed $post_type
    * @param mixed $post_status
    * @return array
    */
    function getbgImages($post_type='attachment', $post_status='inherit') {
        global $wpdb;        
        $posts = $wpdb->get_col( $wpdb->prepare( "
            SELECT guid
            FROM $wpdb->posts
            WHERE post_type = %s AND post_status = %s AND post_title = %s
        ", $post_type, $post_status, $this->post_name.'-bg') );
                                       
        $this->bgImages = $posts;   
    }
    
}                       
    