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
    // include config
    include(dirname(__FILE__).'/config.php');    
    // handle session token
    include(dirname(__FILE__).'/session.php');        
    
    // handle action    
    $action = $_REQUEST['json'];
    
    // decode action
    // ...
    //$timespan
    //$action
    
    if ($mobilepage && file_exists(dirname(__FILE__ ).'/'.$mobilepage.'.php')) {
        // include mobile page class 
        require_once(dirname(__FILE__).'/mobilepage.php');
        // include request page
        include(dirname(__FILE__ ).'/'.$mobilepage.'.php');
        
        $mobilepage = new $mobilepage();                
        if ($action && method_exists($mobilepage, $action)) {
            $mobilepage->$action();
        } else {
            $mobilepage->getContent();
            
        }
        
        // wrap sections and extra
        if ($jsonObj['content']) {
            $jsonObj['content'] = '<div id="content-host">' . $jsonObj['content'] . '</div>';   
        }
        if ($jsonObj['extra']) {
            $jsonObj['extra'] = '<div id="extra">' . $jsonObj['extra'] . '</div>';
        }
                   
    } else {
        if ($action && function_exists($action))
            $action(); 
        else
            sampleAction();        
    }
            
    // return json to caller
    if(isset($_GET['debug'])) {
        debug_back($jsonObj);
    }
    else {              
        json_back($jsonObj);
    } 
    
    /**
    * return debug info
    * 
    * @param mixed $expression
    */
    function debug_back($expression) {
        header('Content-type: application/json; charset=' . get_option('blog_charset'));    
        print_r($expression); 
    }
    
    /**
    * array to json string
    * if 'callback', jsonp script response
    * else 'text-json'  response   
    * 
    * @param mixed $json_array
    */
    function json_back($jsonObj) {
        $jsonStr = json_encode($jsonObj);
        
        $callback = $_REQUEST['callback'];
        if ($callback) {
            header('Content-Type: text/javascript; charset=' . get_option('blog_charset'));
            echo $callback . '(' . $jsonStr . ')';
        } else {
            // just for ajax form plugin, sometimes 'success' method can't handled
            header('HTTP/1.1 200 OK');
            header('Content-Type: text/json; charset=' . get_option('blog_charset'));
            header('Access-Control-Allow-Origin: *');
            echo $jsonStr;
        }                      
    }
    
    /**
    * error obj
    * 
    * @param mixed $action
    */
    function getErrorObj($action) {
        return false;    
    }    
    
    /**
    * sampleAction
    * 
    */
    function sampleAction() { 
        global $jsonObj;
              
        $item['topic']      = '手机特惠';
        $item['subtopic']   = '总有你要的低价';
        $sampleData[]       = $item;
        
        $item['topic']      = '酒店';
        $item['subtopic']   = 'HOTEL';
        $sampleData[]       = $item;
        
        $item['topic']      = '团购';
        $item['subtopic']   = '低价旅行精选';
        $sampleData[]       = $item;
        
        $item['topic']      = '接送机';
        $item['subtopic']   = '';
        $sampleData[]       = $item;
        
        $item['topic']      = '车车';
        $item['subtopic']   = '出租车打车平台';
        $sampleData[]       = $item;
        
        $item['topic']      = '火车票';
        $item['subtopic']   = '';
        $sampleData[]       = $item;
        
        $item['topic']      = '夜宵酒店';
        $item['subtopic']   = '倒数 05：03：04';
        $sampleData[]       = $item;
        
        $item['topic']      = '机票';
        $item['subtopic']   = '';
        $sampleData[]       = $item;
        
        $item['topic']      = '门票';
        $item['subtopic']   = '';
        $sampleData[]       = $item;
        
        $item['topic']      = '长线游周边游';
        $item['subtopic']   = '';
        $sampleData[]       = $item;
        
        $item['topic']      = '身边';
        $item['subtopic']   = '';
        $sampleData[]       = $item;                       
        
        $jsonObj['sample'] = $sampleData;        
        return $sampleData;
    } 