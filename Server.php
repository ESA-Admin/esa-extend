<?php

namespace esa;
use esa\Http;

/**
 * esa 云服务通讯类
 */

class Server
{
    protected $server_url = "https://www.esaadmin.com/esaserver/";
    protected $_user_info = "";
    protected $_user_file = __DIR__."/cache/user";
    protected $_token = "";
    protected $_token_file = __DIR__."/cache/token";
    protected $_error = "";
    protected $_errcode = 0;
    
    public $err_data = null;
    
    public function __construct()
    {
        $this->_user_info = @json_decode(@base64_decode(@file_get_contents($this->_user_file)),true);
        $this->_token = @file_get_contents($this->_token_file);
    }
    
    public static function instance($options = [])
    {
        if (is_null(self::$instance)) {
            self::$instance = new static($options);
        }
        return self::$instance;
    }
    
    public function url($url = "user/token")
    {
        return $this->server_url . $url . ".html?__TOKEN__=".$this->_token;
    }
    
    public function seterr($error="",$errcode = 110){
        if($this->_error > 0){
            return false;
        }
        $this->_error = $error;
        $this->_errcode = $errcode;
        return true;
    }
    
    public function geterr($type=null){
        if(!empty($type) && $type == "msg"){
            return $this->_error;
        }
        if(!empty($type) && $type == "code"){
            return $this->_errcode;
        }
        return ["msg"=>$this->_error,"code"=>$this->_errcode];
    }
    
    public function post($url_base="user/token",$data=[],$deep=0){
        $url = $this->url($url_base);
        $res = @json_decode(Http::post($url,$data,[CURLOPT_REFERER=>$_SERVER['HTTP_HOST']]),true);
        if(is_array($res)){
            if($deep > 1){
                $this->seterr("出现史诗级错误！");
                return false;
            }
            if(intval($res['code']) === 101001 || intval($res['code']) === 101004 || intval($res['code']) === 10004){
                $user = $this->post("user/token",$this->_user_info,$deep++);
                if(!empty($user['token'])){
                    $this->_token = $user['token'];
                    @file_put_contents($this->_token_file,$user['token']);
                    return $this->post($url_base,$data);
                }else{
                    $this->seterr($user['msg'],$user['code']);
                    return false;
                }
            }
            if(intval($res['code']) > 0){
                $this->err_data = $res['data'];
                $this->seterr($res['msg'],$res['code']);
                return false;
            }else{
                return $res['data'];
            }
        }else{
            $this->seterr("请求出现错误");
            return false;
        }
    }

    public function login($username,$password){
        $user = ["username"=>$username,"password"=>$password];
        $data = $this->post("user/token",$user);
        if($data){
            if(!isset($data['token'])){
                $this->seterr($data['msg'],$data['code']);
                return false;
            }else{
                @file_put_contents($this->_user_file,base64_encode(json_encode($user)));
                @file_put_contents($this->_token_file,$data['token']);
                return true;
            }
        }else{
            return false;
        }
    }
    
    public function isLogin(){
        $user = @json_decode(@base64_decode(@file_get_contents($this->_user_file)),true);
        if(!empty($user['username']) && !empty($user['password'])){
            return true;
        }else{
            return false;
        }
    }
}
