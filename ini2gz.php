<?php
ini_set('memory_limit','2000M'); 
ini_set('max_execution_time', 0);
error_reporting(E_ALL); 
 
$dirx = dirname(__FILE__)."/ini_out";
$target = dirname(__FILE__)."/gz_ini_out"; 
$dir = array_diff(scandir($dirx,1), array('..', '.'));
$remove = true;
$sep = ';';
 
if(!is_dir($target)){
    mkdir($target, 0777, true);
}
$no_error = array();
$get_base = array();;
//$break = 10;
//foreach($dir as $k=>$d){
    $target_dir = $target ;
//    if(!is_dir($target_dir)){
//        mkdir($target_dir, 0777, true);
//    }
//    $fd_name = $d; 
    $d_user = $dirx ;
//    if(!is_dir($d_user))continue;
    $list = array_diff(scandir($d_user), array('..', '.'));
    
    foreach($list as $i=>$file_name){
//        if($i==$break)exit;
        $path = $d_user.'/'.$file_name;
        $ext = pathinfo($path, PATHINFO_EXTENSION); 
        $target_path = $target_dir.'/'.$file_name;
        if($ext == 'ini'){
                $file = $path; 
                $gzfile = $target_path.".gz"; 
                if(file_exists($gzfile))unlink($gzfile);
                $fp = gzopen ($gzfile, 'w9'); 
                gzwrite ($fp, file_get_contents($file)); 
                gzclose($fp); 
        }
    }
//} 
    echo '<br>--------------END---------------<br>';