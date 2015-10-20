<?php
ini_set('memory_limit','2000M'); 
ini_set('max_execution_time', 0);
error_reporting(E_ALL);
$dbx = mysqli_connect('localhost','palmpcss','palmp4151') or die('Could not connect to server.' );
mysqli_select_db($dbx,'csv_proj' ) or die('Could not select database.');

$lang_list = array('uk','us','fr','es','de','it','mx','ca');
$dirx = dirname(__FILE__)."/csv";
$target = dirname(__FILE__)."/csv_proto";;
$dir = array_diff(scandir($dirx,1), array('..', '.'));
$remove = true;
$sep = ';';

//$clean_sql = "truncate miss_log;";
//mysqli_query($dbx,$clean_sql);
//$clean_sql = "truncate complete_log;";
//mysqli_query($dbx,$clean_sql);

if(!is_dir($target)){
    mkdir($target, 0777, true);
}
$sql = "SELECT xsd_key.file, xsd_key.tkey,dict.col_1,dict.col_2,dict.col_3,dict.col_4,dict.col_5,dict.col_6,xsd_key.okey,dict.lang,xsd_key.path 
    FROM xsd_key 
    LEFT JOIN dict ON xsd_key.tkey = dict.tkey AND  LOWER(xsd_key.file)    like dict.filename and (lang = 'us' or lang='uk')
    group by LOWER(xsd_key.file),xsd_key.tkey 
    order by  xsd_key.path asc,file asc ";
$q = mysqli_query($dbx, $sql);
$del_list=array();
while($d = mysqli_fetch_assoc($q)){
    $tkey = $d['tkey'];
    $file = strtolower($d['file']);
    $dir = str_replace(array('C:xampphtdocsphp/xsd/','.xsd'), '', $d['path']);
    $dir = strtolower($dir);
    $target_dir = $target.'/'.$dir;
//    echo $target_dir;exit;
    if(!is_dir($target_dir)){
        mkdir($target_dir, 0777, true);
    }
    if(empty($d['col_1'])){
        $sql = "SELECT xsd_key.file, xsd_key.tkey,dict.col_1,dict.col_2,dict.col_3,dict.col_4,dict.col_5,dict.col_6,xsd_key.okey,dict.lang,xsd_key.path 
            FROM xsd_key 
            LEFT JOIN dict ON xsd_key.tkey = dict.tkey  and (lang = 'us' or lang='uk')
            where xsd_key.tkey = '$tkey'
            group by  xsd_key.tkey 
            order by  xsd_key.path asc,file asc limit 1";
        $qb = mysqli_query($dbx, $sql);
        if(mysqli_num_rows($qb)>0){
            $d = mysqli_fetch_assoc($qb);
            $d['col_6'] = '';
        }
    }
    
    
    $target_file= $target_dir.'/'.$file.'.uk.csv';
    $out = array($tkey,$d['col_2'],$d['col_3'],$d['col_4'],$d['col_5'],$d['col_6']);
    if(!isset($del_list[$target_file]) && file_exists($target_file) ){ 
            unlink($target_file);
    }
    $del_list[$target_file] = 1;
    file_put_contents($target_file, '"'.implode('";"',$out).'"'."\n",FILE_APPEND);
}
echo '<br>--------------END---------------<br>';