<?php

namespace esa;
use esa\Http;

/**
 * esa 服务器通讯类
 */
class Server
{
    public static $base_url = "http://api.esaadmin.com/";
    
    // 拼接url
    public static function url($purl="Sys/get_version"){
        $url = ucfirst($purl);
        return self::$base_url.$url.".html";
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
    
    // 本地信息
    public static function my_info($m = ""){
        $info_path = APP_PATH."/version";
        $info = explode(":",@file_get_contents($info_path));
        if(count($info) != 2){
            throw new Exception("ESA Admin 版本信息不正确");
        }
        $tv = explode("_",$info[1]);
        if(count($tv) != 2){
            throw new Exception("ESA Admin 版本信息不正确2");
        }
        $res = [
            "type"      => $tv[1],
            "version"   => $tv[0],
        ];
        return empty($m) ? $res : $res[$m];
    }
    
    // 获取线上版本
    public static function get_version(){
        $data = self::my_info();
        return self::post("sys/get_version",$data);
    }
    
    // 获取线上版本列表
    public static function get_upgrade($page = 0){
        $data = [
            "limit" => 5,
            "page"  => $page
        ];
        return @json_decode(Http::get(self::url("sys/upgrade"),$data),true);
    }

}