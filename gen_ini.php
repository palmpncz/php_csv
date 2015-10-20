<?php
ini_set('memory_limit','2000M'); 
ini_set('max_execution_time', 0);
error_reporting(E_ALL);
$dbx = mysqli_connect('localhost','palmpcss','palmp4151') or die('Could not connect to server.' );
mysqli_select_db($dbx,'csv_proj' ) or die('Could not select database.');

$lang_list = array('uk'=>'en','de'=>'de','fr'=>'fr','es'=>'es','it'=>'it','mx'=>'mx','ca'=>'ca');
$dirx = dirname(__FILE__)."/csv_proto";
$target = dirname(__FILE__)."/ini_out";
$dir = array_diff(scandir($dirx,1), array('..', '.'));
$remove = true;
$sep = ';';

//$clean_sql = "truncate miss_log;";
//mysqli_query($dbx,$clean_sql);
//$clean_sql = "truncate complete_log;";
//mysqli_query($dbx,$clean_sql);
$opt = array('us'=>'Optional','uk'=>'Optional','fr'=>'Optionnel','it'=>'Obbligatorio','es'=>'Opcional','de'=>'Optional','mx'=>'Opcional','ca'=>'Optional');

if(!is_dir($target)){
    mkdir($target, 0777, true);
}
$alt_sql = "select * from alt_xsd_key";
$q = mysqli_query($dbx,$alt_sql);
$alt_list_xsd = array();
$alt_list_csv = array();
while($da = mysqli_fetch_array($q)){
    $alt_list_xsd[trim($da['alt_tkey'])] = trim($da['tkey']);
    $alt_list_csv[trim($da['tkey'])] = trim($da['alt_tkey']);
}
$sql = "select * from dict";
$l = mysqli_query($dbx,$sql);
$list_1 = array();
$list_2 = array();
$list_3_1 = array();
$list_3 = array();
$list_4 = array();
$list_5 = array();
while($d = mysqli_fetch_assoc($l)){
    $path = str_replace('C:xampphtdocsphp/','',$d['filepath']);
    $tmp = explode('.',$path);
    $dirq = $tmp[0];
    $lang = $d['lang'];
    $tkey = $d['tkey'];
    $file = $d['filename'];
    $dirq = str_replace('/'.$file, '', $dirq);
    $alt_key = $d['alt_tkey'];
    for($i=1;$i<=6;$i++){
        $col['col_'.$i] = $d['col_'.$i];
    }
    if(trim($d['col_6'])==''){
        $col['col_6'] = $opt[$lang];
    }
    $list_1["{$dirq}/{$file}"][$lang][$tkey] = $col;
//    if($alt_key=='producer'){echo 'ttttttttttttttttttttttt';
//    print_r($d);
//    }
    if($alt_key!=''){
        $list_2["{$dirq}/{$file}"][$lang][$alt_key] = $col;
        $list_5[$lang][$alt_key] = $col;
        $list_3_1["{$dirq}"][$lang][$alt_key] = $col;
//        echo '<br>'.$lang.' '.$alt_key.'<br>';
    }
    $list_3["{$dirq}"][$lang][$tkey] = $col;
    $list_4[$lang][$tkey] = $col;
    
}
//echo '<pre>';
//print_r($list_1);exit;

$no_error = array();
$get_base = array();
$not_found_key=array();
$not_found_key_uk=array();
//$break = 10;
$ini_out = array();
foreach($dir as $k=>$d){
    
    $target_dir = $target.'/'.$d;
//    if(!is_dir($target_dir)){
//        mkdir($target_dir, 0777, true);
//    }
    $fd_name = $d; 
    $d_user = $dirx.'/'.$d; 
//    print_r($d_user);exit;
    if(!is_dir($d_user))continue;
    $list = array_diff(scandir($d_user), array('..', '.'));
    
    foreach($list as $i=>$file_name){
        
//        if($i==$break)exit;
        $path = $d_user.'/'.$file_name;
        $ext = pathinfo($path, PATHINFO_EXTENSION); 
        if($ext == 'csv'){
                $file = explode('.',  str_replace('.'.$ext, '', $file_name));
                $file_key = $file[0];
                $file_lang = $file[1]; 
                if(!in_array($file_lang,array('uk','us'))) continue;
                if($file_lang=='us'){
                    if(isset($get_base[$file_key])) continue;
                    if( file_exists(str_replace($file_lang.'.csv','uk.csv',$path))){
                        $file_lang='uk';
                    }
                }
                
                $get_base[$file_key]=$path;
                foreach($lang_list as $l=>$lt){
                    
                    $target_file = $target_dir.'.ini';
//                    echo 'File:'.$file_key.' Lang:'.$file_lang.'<br>';
//                    echo 'Read:'.$path.' Write:'.$target_file.'<br>';
                    
//                    if(!isset($no_error[$target_file])) $no_error[$target_file] = 0;
                    if($remove && file_exists($target_file)){
                        unlink($target_file);
                    }
//                    $sql =  "select tkey from dict where filename = '{$file_key}' and lang = '$file_lang' and filepath like '%csv/{$fd_name}/%'";
                    $sql =  "select tkey from xsd_key where lower(file) = '{$file_key}'  and path like '%xsd/{$fd_name}%'";
//                    echo $sql.'<br>';
                    $q = mysqli_query($dbx,$sql); 
                    $fp = null;
                    while ($data = mysqli_fetch_assoc($q)) {
                        $key =$data['tkey'];
                        $output = array();
                        
                        $out = find_lang_col($key,$l,$fd_name,$file_key);
                        if($out===false && isset($alt_list_xsd[$key])){
                            $out = find_lang_col($alt_list_xsd[$key],$l,$fd_name,$file_key);
                            if($out !== false){
                            $out[0] = $key;
                            }
                        }
                        if($out===false && $l == 'mx'){
                            $out = find_lang_col($key,'es',$fd_name,$file_key);
                            if($out===false && isset($alt_list_xsd[$key])){
                                $out = find_lang_col($alt_list_xsd[$key],'es',$fd_name,$file_key);
                                if($out !== false){
                                    $out[0] = $key;
                                }
                            } 
                            
                        }
                        
                        if($out === false){
                            $ini_out[$fd_name][$lt][$key]='';
//                            echo 'not found '.$key,' '.$l.' '.$fd_name.'<br>';
//                            $no_error[$target_file]++;
//                            put_miss_log($key,$l,$fd_name,$file_key,$file_lang);
//                            if(empty($fp))$fp = fopen($target_file, 'w');
//                            fputcsv($fp, array($key,'','','','',$opt[$l]),$sep);
//                            $not_found_key[$key]=true;
//                            if($l=='uk'){
//                                $not_found_key_uk[]=array($key,$fd_name,$file_key);
//                            }
                        }else{
                            if(isset($out[1])){
                            $ini_out[$fd_name][$lt][$out[0]]=$out[1];
                            }else{
                                print_r($out);
                            }
//                            if(empty($fp))$fp = fopen($target_file, 'w');
//                            print_r($out);echo '<br>';
//                            fputcsv($fp, $out,$sep);
                        }
                    } 
//                    if(!empty($fp)){
//                        fclose($fp); 
                        
//                    }else{
//                        echo '----No create file----<br>';
//                    }
                }
//            exit;   
        }
    }
}
echo '<pre>';

foreach($ini_out as $uni => $lang){
    $target_file = $target.'/'.$uni.'.ini';
    
    foreach($lang as $l=>$key_list){
        file_put_contents($target_file,"[".$l."]\n",FILE_APPEND);    
        foreach($key_list as $key=>$val){
            $val = str_replace('"', '', $val);
            $val=trim($val);
            if($val=='')continue;
            file_put_contents($target_file,$key ." = \"".$val."\"\n",FILE_APPEND);
        }
        file_put_contents($target_file,"\n",FILE_APPEND);   
    }
}
echo '<br>--------------END---------------<br>';

//foreach($no_error as $fname => $c){
//    
//    if($c==0){
//        $sql = "insert into complete_log (path) values ('{$fname}')";
//        mysqli_query($dbx,$sql); 
//    }
//}
//echo '<br>Result not found key :'.sizeof($not_found_key);
//echo '<br>Result UK not found key :'.sizeof($not_found_key_uk).'<br>-------------------------<br>';
//foreach($not_found_key_uk as $k=>$v){
//    echo '"'.implode('";"',$v).'"<br>';
//}
function put_miss_log($key,$lang,$fd_name,$file_key,$base_lang){
    global $dbx;
    $sql = "insert into miss_log (dir,filename,lang,tkey,timestamp,base_lang)values ('{$fd_name}','{$file_key}','{$lang}','{$key}',now(),'{$base_lang}')";
//    echo $sql.'<br>';
    mysqli_query($dbx,$sql); 
}





function find_lang_col($key,$lang,$fd_name,$file_key){
    global $dbx,$sep,$list_1,$list_2,$list_3,$list_3_1,$list_4,$list_5,$opt;
//    $sql = "select * from dict where  tkey='{$key}'  and lang = '{$lang}' and filepath like '%csv/{$fd_name}/{$file_key}%'";
//    
//    $q = mysqli_query($dbx,$sql); 
//    $num = mysqli_num_rows($q);
//    if($num>0){
    if(isset($list_1["csv/{$fd_name}/{$file_key}"][$lang][$key])){
//        $data = mysqli_fetch_assoc($q);
        $data = $list_1["csv/{$fd_name}/{$file_key}"][$lang][$key];
        foreach($data as $k=>$v){
            $data[$k] = str_replace($sep,',',$v);
        }
        return array($key,$data['col_2'],$data['col_3'],$data['col_4'],$data['col_5'],$data['col_6']);
    }else{
//        $sql = "select * from dict where  alt_tkey='{$key}'  and lang = '{$lang}' and filepath like '%csv/{$fd_name}/{$file_key}%'";
//    
//        $qa = mysqli_query($dbx,$sql); 
//        $num = mysqli_num_rows($qa);
//        if($num>0){
        if(isset($list_2["csv/{$fd_name}/{$file_key}"][$lang][$key])){
//            $data = mysqli_fetch_assoc($qa);
            $data = $list_2["csv/{$fd_name}/{$file_key}"][$lang][$key];
            foreach($data as $k=>$v){
                $data[$k] = str_replace($sep,',',$v);
            }
            return array($key,$data['col_2'],$data['col_3'],$data['col_4'],$data['col_5'],$data['col_6']  );
        }
//        echo $sql.'<br>';
//        $lang_sql = " lang = '{$lang}' ";
//        
//        $sql = "select * from dict where tkey='{$key}' and $lang_sql and filepath like '%csv/{$fd_name}/%' ";
//        $q = mysqli_query($dbx,$sql); 
//        $num = mysqli_num_rows($q);
//        $data = mysqli_fetch_assoc($q);
//        if($num==0){
        if(!isset($list_3["csv/{$fd_name}"][$lang][$key]) && !isset($list_3_1["csv/{$fd_name}"][$lang][$key])){
//            $lang_sql = " lang = '{$lang}' ";
            $num=0;
            if(in_array($lang, array('us','uk'))){
//                $lang_sql = " lang in ('us','uk') ";
                if(isset($list_4['uk'][$key])){
                    $data = $list_4['uk'][$key];
                    $num=1;
                }else{
                    if(isset($list_4['us'][$key])){
                        $data = $list_4['us'][$key];
                        $num=1;
                    }
                }
                if($num==0&& isset($list_5['uk'][$key])){
                    $data = $list_5['uk'][$key];
                    $num=1;
                }else{
                    if($num==0&&isset($list_5['us'][$key])){
                        $data = $list_5['us'][$key];
                        $num=1;
                    }
                }
            }else{
                if(isset($list_4[$lang][$key])){
                    $data = $list_4[$lang][$key];
                    $num=1;
                }else{
                    if(isset($list_5[$lang][$key])){
                        $data = $list_5[$lang][$key];
                        $num=1;
                    }
                }
                
            }
//            $sql = "select * from dict where tkey='{$key}' and $lang_sql  ";
//            $qx = mysqli_query($dbx,$sql); 
//            $num = mysqli_num_rows($qx);
//            $data = mysqli_fetch_assoc($qx);
            if($num==0){
                return false;
            }else{
                foreach($data as $k=>$v){
                    $data[$k] = str_replace($sep,',',$v);
                }
                $data['col_6'] = $opt[$lang];
                return array($key,$data['col_2'],$data['col_3'],$data['col_4'],$data['col_5'],$data['col_6']);
            }
        }else{
            if(isset($list_3["csv/{$fd_name}"][$lang][$key]))
            $data = $list_3["csv/{$fd_name}"][$lang][$key];
            if(isset($list_3_1["csv/{$fd_name}"][$lang][$key]))
            $data = $list_3_1["csv/{$fd_name}"][$lang][$key];
            foreach($data as $k=>$v){
                $data[$k] = str_replace($sep,',',$v);
            }
//            $data['col_6'] = '';
            return array($key,$data['col_2'],$data['col_3'],$data['col_4'],$data['col_5'],$data['col_6']  );
        }
    }
    
}