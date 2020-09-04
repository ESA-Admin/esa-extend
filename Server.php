<?php

namespace esa;
use esa\Http;

/**
 * esa 服务器通讯类
 */
class Server
{
    public static $base_url = "https://www.bug-maker.com/addons/";
    public static $base_addon = "demo";
    
    // 拼接url
    public static function url($purl="api/index/index"){
        $url = explode("/",$purl);
        if(count($url) != 3){
            throw Exception('ESA::Server()->url() 参数错误!');
        }
        return self::$base_url.self::$base_addon.".".$url[0].".".$url[1]."/".$url[2].".html";
    }
    
    // 请求处理
    public static function post($url,$data){
        return Http::post(self::url($url),$data);
    }
    
    // 测试
    public static function test(){
        $data = [
            "type"  => "get_list",
        ];
        return self::post("api/index/index",$data);
    }
}