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
// get device info
$device = $_REQUEST['device'];
// get token : session_id
$token  = $_REQUEST['token'];
// get user by token
$user   = getUserByToken();    
    
/**
* session token 
* get valid user from token
* 
* @param mixed $token
*/
function getUserCookie() {
    global $secure, $user;
    
    if ( !user ) return false;
    
    if ( $secure ) {
        $scheme = 'secure_auth';
    } else {
        $scheme = 'auth';
    }
    
    // default not to remember
    if ( $remember ) {
        $expiration = $expire = time() + apply_filters('auth_cookie_expiration', 14 * DAY_IN_SECONDS, $user_id, $remember);
    } else {
        $expiration = time() + apply_filters('auth_cookie_expiration', 2 * DAY_IN_SECONDS, $user_id, $remember);
        $expire = 0;
    }
    $cookie = wp_generate_auth_cookie($user_id, $expiration, $scheme);
    
    return $cookie;
}

/**
* register 
* POST method necessary
* 
*/
function register() {
    global $wpdb, $token, $secure, $user, $jsonObj;
    
    $username = $_POST['username'];
    $password = $_POST['password'];
    $repassword = $_POST['re-password'];
    $email  = $_POST['email']; 
    
    // valid
    if (!$username) {
        $invalid['into']    = 'span.wpcf7-form-control-wrap.username';
        $invalid['message'] = '请您输入用户名。';
        $invalids[] = $invalid;
    }
    
    if (!$password) {
        $invalid['into']    = 'span.wpcf7-form-control-wrap.password';
        $invalid['message'] = '请您输入密码。';
        $invalids[] = $invalid;
    }
    
    if (!$repassword) {
        $invalid['into']    = 'span.wpcf7-form-control-wrap.re-password';
        $invalid['message'] = '请您输入确认密码。';
        $invalids[] = $invalid;
    }
    
    if ($password !== $repassword) {
        $invalid['into']    = 'span.wpcf7-form-control-wrap.password';
        $invalid['message'] = '两次密码必须一致。';
        $invalids[] = $invalid;
    }
    
    if (!$email) {
        $invalid['into']    = 'span.wpcf7-form-control-wrap.email';
        $invalid['message'] = '请您输入您的邮箱。';
        $invalids[] = $invalid;
    }
    
    // auth 
    if (username_exists( $username )) {
        $invalid['into']    = 'span.wpcf7-form-control-wrap.username';
        $invalid['message'] = '已有用户名，请选择其他名称。';
        $invalids[] = $invalid;  
    }
    
    if ($invalids) {        
        $jsonObj['invalids']    = $invalids;
        $jsonObj['message']     = '请根据提示完成必要输入项。';
        $jsonObj['into']        = '#wpcf7-f241-p240-o1';
        return false;
    }
    
    $user['user_login'] = $username;
    $user['user_pass'] = $password;
    $user['user_email'] = $email;
    $user_id = wp_insert_user( $user );
    
    $user = get_user_by('login', $username );
    
    // update session  
    $table_name = $wpdb->prefix . "mobilesession";                    
    $sql = "UPDATE $table_name 
            SET user_id=%d 
            WHERE session_id=%s";
    $wpdb->query( $wpdb->prepare($sql, $user->ID, $token) );
    
    // return cookie to keep session  
    $content = '';
    $content .= '<h3>'.$user->data->display_name.'</h3>';    
    $content .= '<div>';
    $content .= '   <span>email：'.$user->data->user_email.'</span>';    
    $content .= '</div>';
    $jsonObj['user']['content'] = $content;
    
    // log mobile events
    logSessionEvents('register');
}

/**
* login 
* POST method necessary
* 
*/
function login() {
    global $wpdb, $token, $secure, $user, $jsonObj;
    
    $username = $_POST['username'];
    $password = $_POST['password'];
     
    // valid
    if (!$username) {
        $invalid['into']    = 'span.wpcf7-form-control-wrap.username';
        $invalid['message'] = '请您输入用户名。';
        $invalids[] = $invalid;
    }
    
    if (!$password) {
        $invalid['into']    = 'span.wpcf7-form-control-wrap.password';
        $invalid['message'] = '请您输入密码。';
        $invalids[] = $invalid;
    }
        
    if ($invalids) {        
        $jsonObj['invalids']    = $invalids;
        $jsonObj['message']     = '请根据提示完成必要输入项。';
        $jsonObj['into']        = '#wpcf7-f241-p240-o1';
    }
       
    // auth 
    $user = wp_authenticate($username, $password);
    
    if ( is_wp_error( $user ) ) {
        if  ($jsonObj['user'])
            unset($jsonObj['user']);
        $jsonObj['message']     = '请输入正确的用户名和密码。';
        $jsonObj['into']        = '#wpcf7-f241-p240-o1';
        
        return false;
    }
    
    // update session  
    $table_name = $wpdb->prefix . "mobilesession";                    
    $sql = "UPDATE $table_name 
            SET user_id=%d 
            WHERE session_id=%s";
    $wpdb->query( $wpdb->prepare($sql, $user->ID, $token) );
    
    // return cookie to keep session  
    $content = '';
    $content .= '<h3>'.$user->data->display_name.'</h3>';    
    $content .= '<div>';
    $content .= '   <span>email：'.$user->data->user_email.'</span>';    
    $content .= '</div>';
    $jsonObj['user']['content'] = $content;
    
    // log mobile events
    logSessionEvents('login');
}

/**
* logout 
* 
*/
function logout() {
    global $token, $user, $jsonObj;
    // log mobile events
    logSessionEvents('logout');
    
    // return false to erase session 
    session_start();
        
    if ($token == session_id()) {
        session_regenerate_id();   
    }
    $token = session_id();
    $jsonObj['token'] = $token;
    
    unset($jsonObj['user']);
} 

/**
* update token
* 
*/
function getUserByToken() {
    global $wpdb, $token, $device, $jsonObj;
    
    // if token then check expiration
    $table_name = $wpdb->prefix . "mobilesession";
    if ( $token ) {        
        $sql = "SELECT users.user_login 
                FROM $table_name mobilesession 
                LEFT OUTER JOIN $wpdb->users users ON mobilesession.user_id=users.ID
                WHERE mobilesession.session_id=%s
                AND mobilesession.expire_time>SYSDATE()";
        
        $mobilesession = $wpdb->get_row( $wpdb->prepare($sql, $token) );        
    }
    
    // already login or not expire, update mobile session
    if ( $mobilesession ) {
        $sql = "UPDATE $table_name 
                SET expire_time=DATE_ADD(SYSDATE(), INTERVAL 2 HOUR) 
                WHERE session_id=%s";
        $wpdb->query( $wpdb->prepare($sql, $token) );
        
        $user = get_user_by('login', $mobilesession->user_login);
        
        // return cookie to keep session      
        // return cookie to keep session  
        $content = '';
        $content .= '<h3>'.$user->data->display_name.'</h3>';    
        $content .= '<div>';
        $content .= '   <span>email：'.$user->data->user_email.'</span>';    
        $content .= '</div>';
        $jsonObj['user']['content'] = $content;  
    } else {
        session_start();
        
        if ($token == session_id()) {
            session_regenerate_id();   
        }
        $token = session_id();
        
        $sql = "INSERT INTO $table_name( session_id, user_id, expire_time, device ) 
                VALUES ( %s, 0, DATE_ADD(SYSDATE(), INTERVAL 2 HOUR ), %s);";
        $wpdb->query($wpdb->prepare($sql, $token, $device));  
    } 
    
    $jsonObj['token'] = $token;            
    return $user;       
}

/**
* upload files
* 
*/
function uploadFiles() {
    global $wpdb, $user, $jsonObj;
    
    // if login and can upload files
    if ( $user && user_can($user, 'upload_files') ) {
        $post_id = 0;
        if ( isset( $_REQUEST['post_id'] ) ) {
            $post_id = absint( $_REQUEST['post_id'] );
            if ( ! get_post( $post_id ) || ! user_can( $user, 'edit_post' ) )
                $post_id = 0;
        }
        
        // position
        $event_content['position'] = $_POST['ajax-position'];
        
        // photo
        if ($_FILES['ajax-photo']) {
            $event_content['photo']     = media_handle_upload('ajax-photo', $post_id); 
        }
        // bar code
        $event_content['barcode']   = $_POST['ajax-position']; 
        
        $event_content = json_encode($event_content);
        
        // write evnet log 
        logSessionEvents('upload', $event_content);
    }  
}

/**
* write events log
* 
* @param mixed $event_type
* @param mixed $event_content
* @param mixed $comment
*/
function logSessionEvents($event_type, $event_content='', $comment='') {
    global $wpdb, $token;
    $table_name = $wpdb->prefix . "mobileevents";
    $sql = "INSERT INTO $table_name (session_id, event_type, event_date, event_date_gmt, event_content, comment) 
            VALUES (%s, %s, SYSDATE(), UTC_TIMESTAMP(), %s, %s)";
    $wpdb->query($wpdb->prepare($sql, $token, $event_type, $event_content, $comment));    
}        