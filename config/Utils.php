<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Utils
 *
 * @author Maurcio Herrera
 */
class Utils {

    public static function get_path() {
        $path_ = implode("\\", array_slice(explode("\\", __DIR__), 0, 4));
        return $path_;
    }

    public static function get_root() {
        $url = str_replace("/", "\\", $_SERVER["REQUEST_SCHEME"] . "://" . $_SERVER["HTTP_HOST"] . "/" . explode("/", trim($_SERVER["REQUEST_URI"], "/"))[0]);
        return $url;
    }
    
    
    public static function get_rootParams($index) {
        $param = explode("/", trim($_SERVER["REQUEST_URI"], "/"));
        return $param[count($param)-$index];
    }

    public static function getUriParams($param) {
        $arrayParams = array();
        if ($param != '') {
            $params = explode("&", $param);
            for ($i = 0; $i < count($params); $i++) {
                $item = explode("=", $params[$i]);
                $arrayParams[$item[0]] = $item[1];
            }
        }
        return $arrayParams;
    }

    public static function validate($body) {
        $validate = true;
        foreach ($body as $key => $value) {
            if($value == ""){              
               $validate = false; 
            }
        }
        return $validate;
    }

}
