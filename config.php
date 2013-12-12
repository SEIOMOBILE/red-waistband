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
    // json object 
    $jsonObj = array();
    // Determine if SSL is used.  
    $secure = is_ssl();
    // page difinition        
   
    // action map
    define('MOBILE_ACTION_THUMBS',      'lrio.');
    define('MOBILE_ACTION_MAP',         array (
        'latest', 'register', 'signin', 'signout',       
    ));        
    
    // terms name
    define('MOBILE_POST_TAG',           'APP');