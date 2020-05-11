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
    public static function create_file($path,$body){}
    // 复制文件
    public static function copy_file($source,$target){}
    // 删除文件
    public static function delete_file($path){}
    // 创建目录
    public static function create_dir(){}
    // 复制目录
    public static function copy_dir(){}
    // 删除目录
    public static function delete_dir(){}
    
    // 移动文件/文件加
    // 获取文件内容
    // 压缩
    // 解压
    // 重命名
}