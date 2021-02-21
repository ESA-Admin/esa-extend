<?php

namespace esa;

/**
 * sql语句生成类
 */
class File
{
    // 检查文件名
    public static function check_file_name($path){}
    
    // 文件夹对比存在哪些更新
    public static function diff($source,$target){
        $source_paths = File::get_file($source);
        $target_paths = File::get_file($target);
        // 寻找添加文件
        $add_files = array_diff($target_paths,$source_paths);
        // 寻找删除文件
        $delete_files = array_diff($source_paths,$target_paths);
        // 寻找修改文件/删除文件
        $edit_files = [];
        $eq_files = array_intersect($source_paths,$target_paths);
        foreach($eq_files as $key => $value){
            if(md5_file($source.$value) != md5_file($target.$value)){
                $edit_files[] = $value;
            }
        }
        return ["add"=>$add_files,"edit"=>$edit_files,"delete"=>$delete_files];
    }
    
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
    
    // 获取文件夹内所有文件
    public static function get_file($source_dir,$true_path=false,$target=""){
        $file_arr = array();
        $dir = $source_dir.$target;
        if(is_dir($dir)){
            //打开
            if($dh = @opendir($dir)){
                //读取
                while(($file = readdir($dh)) !== false){
                    if($file != '.' && $file != '..'){
                        if(is_dir($dir."/".$file)){
                            $file_arr = array_merge($file_arr,self::get_file($source_dir,$true_path,$target."/".$file));
                        }else{
                            $file_arr[] = ($true_path ? $source_dir : "").$target."/".$file;
                        }
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
    public static function copy_file($source,$target){
        if(!is_file($source)){
            return false;
        }
        $target_path = pathinfo($target,PATHINFO_DIRNAME);
        if(!is_dir($target_path)){
            mkdir($target_path);
        }
        return copy($source,$target);
    }
    // 删除文件
    public static function delete_file($path){}
    // 复制目录
    public static function copy_dir($source,$target,$retain=true,$cove=true){
        // retain 是否保留源文件
        // cove 是否符覆盖目标文件
        $source_arr = File::get_file($source);
        foreach($source_arr as $v){
            $path_info = pathinfo($target.$v);
            if(!is_dir($path_info['dirname'])){
                @mkdir($path_info['dirname']);
            }
            if(!$cove && is_file($target.$v)){
                continue;
            }
            if(!@copy($source.$v,$target.$v)){
                return false;
            }
            if(!$retain){
                @unlink($srouce.$v);
            }
        }
        return true;
    }
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
    public static function zip_encode($path,$name,$save_path=__DIR__,$is_dir=false){
        // dump($path);
        $zip=new \ZipArchive();
        $result = $zip->open($save_path . '/' . $name . ".zip", \ZipArchive::CREATE);
        if($result !== true){
            return false;
        }
        if(is_array($path)){
            if(!isset($path['base']) || !isset($path['data'])){
                return false;
            }
            foreach($path['data'] as $file){
                $target = $is_dir ? $name.$file : $file;
                $zip->addFile($path['base'].$file,$target);
            }
        }else{
            foreach(self::get_file($path) as $file){
                $target = $is_dir ? $name.$file : $file;
                $zip->addFile($path.$file,$target);
            }
        }
        
        $zip->close();
        return true;
    }
    // 解压
    public static function zip_decode($source,$target){
        if(!is_file($source) || !is_dir($target)){
            return false;
        }
        $zip=new \ZipArchive();
        if ($zip->open($source) === true) {
            $zip->extractTo($target);
            $zip->close();
            return true;
        } else {
            return false;
        }
    }
    // 重命名
}
