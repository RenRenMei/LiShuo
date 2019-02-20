<?php
//文件篇
//递归删除文件夹
function delFile($dir,$file_type="")
{ 
    if(is_dir($dir)){
        // scandir  列出文件夹中的目录和列表
        $files = scandir($dir);
        //打开目录    列出目录中的所有文件并去掉 . 和 ..
        foreach($files as $filename){
            if($filename !='.' && $filename != ".."){
                if(!is_dir($dir.'/'.$filename)){
                    if(empty($file_type)){
                        unlink($dir."/".$filename);
                    }else{
                        if(is_array($file_type)){
                            if(preg_match($file_type[0],$filename)){
                                unlink($dir."/".$filename);
                            }
                        }else{
                            //制定包含某些字符串的文件
                            if(false != stristr($filename,$file_type)){
                                unlink($dir."/".$filename);
                            }
                        }
                    }
                }else{
                    delFile($dir."/".$filename);
                    rmdir($dir."/".$filename);
                }
            }
        }
    }else{
        if(file_exists($dir)) unlink($dir);
    }

}


//sql执行篇
//批量执行.sql中的sql语句    
function sql_split($sql, $tablepre) {

    if ($tablepre != "tp_")
    	$sql = str_replace("tp_", $tablepre, $sql);
          
    $sql = preg_replace("/TYPE=(InnoDB|MyISAM|MEMORY)( DEFAULT CHARSET=[^; ]+)?/", "ENGINE=\\1 DEFAULT CHARSET=utf8", $sql);
    
    $sql = str_replace("\r", "\n", $sql);
    $ret = array();
    $num = 0;
    $queriesarray = explode(";\n", trim($sql));
    unset($sql);
    foreach ($queriesarray as $query) {
        $ret[$num] = '';
        $queries = explode("\n", trim($query));
        $queries = array_filter($queries);
        foreach ($queries as $query) {
            $str1 = substr($query, 0, 1);
            if ($str1 != '#' && $str1 != '-')
                $ret[$num] .= $query;
        }
        $num++;
    }
    return $ret;
}




