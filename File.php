<?php

namespace esa;

/**
 * sql语句生成类
 */
class File
{
    // 检查文件名
    public static function check_file_name($path){}
    
    // 取文件/目录列表
    public static function get_dir($dir){
        $file_arr = array();
        if(is_dir($dir)){
            //打开
            if($dh = @opendir($dir)){
                //读取
                while(($file = readdir($dh)) !== false){
                    if($file != '.' && $file != '..'){
                        $file_arr[] = $file;
                    }
                }
                //关闭
                closedir($dh);
            }
        }
        return $file_arr;
    }
    
    // 计算文件数量
    public static function get_files_count($path){}
    
    // 创建文件
    public static function create_file($path,$body){
        return file_put_contents($path,$body);
    }
    // 复制文件
    public static function copy_file($source,$target){}
    // 删除文件
    public static function delete_file($path){}
    // 创建目录
    public static function create_dir(){}
    // 复制目录
    public static function copy_dir(){}
    // 删除目录
    public static function delete_dir($path){
        if(is_dir($path)){
            File::empty_dir($path);
            @rmdir($path);
        }
    }
    // 清空目录
    public static function empty_dir($path){
        if(is_dir($path)){
            //扫描一个文件夹内的所有文件夹和文件并返回数组
           $p = scandir($path);
           foreach($p as $val){
               //排除目录中的.和..
               if($val !="." && $val !=".."){
                   //如果是目录则递归子目录，继续操作
                   if(is_dir($path.$val)){
                       //子目录中操作删除文件夹和文件
                       File::empty_dir($path.$val.'/');
                       //目录清空后删除空文件夹
                       @rmdir($path.$val.'/');
                   }else{
                       //如果是文件直接删除
                       unlink($path.$val);
                   }
               }
           }
       }
    }
    
    // 移动文件/文件加
    // 获取文件内容
    // 压缩
    // 解压
    // 重命名
}