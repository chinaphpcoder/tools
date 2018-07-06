<?php

if( !function_exists("check_mobile") ) {
    function check_mobile($mobile) {
        if(preg_match("/^1[23456789]{1}\d{9}$/",$mobile)){
            return $mobile;  
        }else{  
            return false; 
        }
    }
}