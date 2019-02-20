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

//判断文件或文件夹是否可写
function testwrite($d){
    if(is_file($d)){
        if(is_writable($d)){
            return true;
        }
        return false;
    }else{
        $tfile = "_test.txt";
        // “w”打开的文件只能向该文件写入。 
        //若打开的文件不存在，则以指定的文件名建立该文件，
        // 若打开的文件已经存在，则将该文件删去，重建一个新文件。
        $fp = @fopen($d."/".$tfile,"w");
        if(!$fp){
           return false;
        }
        fclose($fp);
        $rs = @unlink($d."/".$tfile);
        if($rs){
            return true;
        }else{
            return false;
        }
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

//批量执行sql
function sql_execute($sql,$tablepre)
{
   $sqls = sql_split($sql,$tablepre);
   if(is_array($sqls)){
       foreach($sqls as $sql){
           if(trim($sql) !=""){
               mysqli_query($sql);
           }
       }
   }else{
       mysqli_query($sqls);
   }
   return true;
}



//ip篇

//获取客户端ip地址
function get_client_ip()
{
    var_dump($_SERVER);
    static $ip = NULL;
    if($ip !== NULL){
        return $ip;
    }
    if(isset($_SERVER['HTTP_X_FORWARDED_FOR'])){
      //  var_dump($_SERVER['HTTP_X_FORWARDED_FOR']);
      $arr = explode(",",$_SERVER["HTTP_X_FORWARDED_FOR"]);
    //   在数组中搜索键值 "red"，并返回它的键名：
      $pos = array_search("unknown",$arr);
      if(false !== $pos){
           unset($arr[$pos]);
      }
      $ip = trim($arr[0]);
    }elseif (isset($_SERVER['HTTP_CLIENT_IP'])){
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    }elseif(isset($_SERVER['REMOTE_ADDR'])){
        $ip = $_SERVER["REMOTE_ADDR"];
    }
    //ip合法验证
    $ip = (false !== ip2long($ip)) ? $ip : "0.0.0.0";
    return $ip;
}


//随机字符串篇
    function sp_random_string($len = 8){
            $chars = array(
                "a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k",
			"l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v",
			"w", "x", "y", "z", "A", "B", "C", "D", "E", "F", "G",
			"H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R",
			"S", "T", "U", "V", "W", "X", "Y", "Z", "0", "1", "2",
			"3", "4", "5", "6", "7", "8", "9"
            );
            $charsLen = count($chars) - 1;
            shuffle($chars);  //将数组打乱
            $output = "";
            for($i=0;$i<$len;$i++){
                $output .= $chars[mt_rand(0,$charsLen)];
            }
            return $output;
    }


