<?php

namespace esa;

/**
 * sql语句生成类
 */
class Sql
{
    public static function parse($sql='',$limit=0,$prefix=[])
    {
        // 被替换的前缀
        $from = '';
        // 要替换的前缀
        $to = '';
    
        // 替换表前缀
        if (!empty($prefix)) {
            $to   = current($prefix);
            $from = current(array_flip($prefix));
        }
    
        if ($sql != '') {
            // 纯sql内容
            $pure_sql = [];
    
            // 多行注释标记
            $comment = false;
    
            // 按行分割，兼容多个平台
            $sql = str_replace(["\r\n", "\r"], "\n", $sql);
            $sql = explode("\n", trim($sql));
    
            // 循环处理每一行
            foreach ($sql as $key => $line) {
                // 跳过空行
                if ($line == '') {
                    continue;
                }
    
                // 跳过以#或者--开头的单行注释
                if (preg_match("/^(#|--)/", $line)) {
                    continue;
                }
    
                // 跳过以/**/包裹起来的单行注释
                if (preg_match("/^\/\*(.*?)\*\//", $line)) {
                    continue;
                }
    
                // 多行注释开始
                if (substr($line, 0, 2) == '/*') {
                    $comment = true;
                    continue;
                }
    
                // 多行注释结束
                if (substr($line, -2) == '*/') {
                    $comment = false;
                    continue;
                }
    
                // 多行注释没有结束，继续跳过
                if ($comment) {
                    continue;
                }
    
                // 替换表前缀
                if ($from != '') {
                    $line = str_replace('`'.$from, '`'.$to, $line);
                }
                if ($line == 'BEGIN;' || $line =='COMMIT;') {
                    continue;
                }
                // sql语句
                array_push($pure_sql, $line);
            }
    
            // 只返回一条语句
            if ($limit == 1) {
                return implode($pure_sql, "");
            }
    
            // 以数组形式返回sql语句
            $pure_sql = implode($pure_sql, "\n");
            $pure_sql = explode(";\n", $pure_sql);
            return $pure_sql;
        } else {
            return $limit == 1 ? '' : [];
        }
    }
}