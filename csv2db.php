<?php
$dbx = mysqli_connect('localhost','palmpcss','palmp4151') or die('Could not connect to server.' );
mysqli_select_db($dbx,'csv_proj' ) or die('Could not select database.');
mb_internal_encoding("UTF-8");
mb_regex_encoding("UTF-8");
$lang_list = array('uk','us','fr','es','de','it','mx','ca');
$dirx = dirname(__FILE__)."/csv";
$target = dirname(__FILE__)."/csv_out";;
$dir = array_diff(scandir($dirx,1), array('..', '.'));

$clean_sql = "truncate dict;";
mysqli_query($dbx,$clean_sql);

$def_text =array();
$def_text['uk'] = array('Optional','Required','Preferred','Desired');			
$def_text['us'] = array('Optional','Required','Preferred','Desired');	
$def_text['fr'] = array('Optionnel','Obligatoire','Souhaité','Souhaité');
$def_text['it'] = array('Facoltativo','Obbligatorio','Consigliato','Consigliato');			
$def_text['es'] = array('Opcional','Obligatorio','Recomendado','Recomendado');
$def_text['de'] = array('Optional','Erforderlich','Empfohlen','Erwünscht');
$def_text['mx'] = array('Opcional','Obligatorio','Recomendado','Recomendado');
$def_text['ca'] = array('Optional','Required','Preferred','Desired');	
$def_text['def']= array('optional'=>0,'required'=>1,'preferred'=>2,'desired'=>3);	

$replace_text =array(
    'Al menos 1 valor obligatorio'=>'Opcional',
    'Obligatorio para ordenadores de sobremesa y portátiles'=>'Obligatorio',
    'Obligatorio para ordenadores de sobremesa y portÃ¡tiles'=>'Obligatorio',
    'Obligatorio si se indica el tamaÃ±o del bÃºfer'=>'Obligatorio',
    'Obligatorio si se indica la capacidad mÃ¡xima de almacenamiento'=>'Obligatorio',
    'Souhaité'=>'Souhaité',
    'Required if Brightness is provided'=>'Optional',
    'Required if LampLife is provided'=>'Optional',
    'Required if ScreenTrigger is provided'=>'Optional',  
    'Facoltativo

Obbligatoriose viene fornito il diametro delle ruote.'=>'Facoltativo'
);

if(!is_dir($target)){
    mkdir($target, 0777, true);
}
$alt_sql = "select * from alt_xsd_key";
$q = mysqli_query($dbx,$alt_sql);
$alt_list = array();
$alt_list_all = array();
while($da = mysqli_fetch_array($q)){
//    $alt_list[strtolower($da['universe'])][str_replace('.uk.csv','',strtolower($da['file']))][$da['alt_tkey']] = $da['tkey'];
    $alt_list[$da['alt_tkey']] = $da['tkey'];
    $alt_list_all[$da['tkey']] = $da['alt_tkey'];
}

$error_file=array();
echo '<pre>';
print_r($alt_list_all);
$insert_list=array();
$lang_list = array();
foreach($dir as $k=>$d){
    $target_dir = $target.'/'.$d;
    if(!is_dir($target_dir)){
        mkdir($target_dir, 0777, true);
    }
    $universe=$d;
    $d = $dirx.'/'.$d; 
    $d_user = $d;
    
    if(!is_dir($d_user))continue;
    $list = array_diff(scandir($d_user), array('..', '.'));
    
    foreach($list as $file_name){
        $path = $d_user.'/'.$file_name;
        $ext = pathinfo($path, PATHINFO_EXTENSION); 
        if($ext == 'csv'){
                $file = explode('.',  str_replace('.'.$ext, '', $file_name));
                $file_key = $file[0];
                $file_lang = $file[1];  
//                foreach($lang_list as $l){
//                    $target_file = $target_dir.'/'.$file_key.'.'.$l.'.'.$ext;
//                    echo '------------------------------------<br>';
                    echo $path.'<br>';
                    
                    
                    $fp = fopen($path, 'r');
                    $at_line =0;
                    $sep =  ';';
                    if(in_array($file_name,array('fashionnecklacebraceletanklet.fr.csv','fashionother.fr.csv','fashionring.fr.csv','fineother.fr.csv','finering.fr.csv','watch.fr.csv', 'fashionearring.fr.csv', 'autoaccessorymisc.fr.csv', 'labsupply.uk.csv', 'petsuppliesmisc.fr.csv', 'shoes.uk.csv', 'sportinggoods.it.csv',  'wine.uk.csv','sportinggoods.fr.csv','petsuppliesmisc.uk.csv','rawmaterials.uk.csv','golfclubiron.uk.csv','sportinggoods.de.csv')))$sep="\t";
                    if(in_array($file_name,array('securityelectronics.uk.csv','gourmet.de.csv','camerabagsandcases.uk.csv')))$sep=":";
                    if(in_array($file_name,array('cebinocular.uk.csv','digitalpictureframe.uk.csv')))$sep=",";
                    while (($line = fgetcsv($fp,99999,$sep,'"')) !== false) {
                        
                        $at_line++;
                        $key ='';
                        $output = array();
                        $data =$line ;
                         foreach($data as $k => $v){
//                            echo $v.'x'.strpos($v,'"').'y'.strpos($v,'"',strlen($v)-1).'<br>';
//                            $v = strpos($v,'"') === 0 && strpos($v,'"',strlen($v)-1) === strlen($v) -1 ?  substr($v,1,strlen($v)-2):$v;
                             $v=str_replace('""','"',$v);
                             $v=str_replace("'","\'",$v);
                             $v=trim($v);
                            if($k==0){  
                                $key=$v;
                            }
                            else{
                                $data[$k]=$v;
                            }
                        } 
                        put_line($file_key,$path,$key,$file_lang,$data,$line,$at_line,$universe); 
                        if($key=='hole_count'){
                            $key = 'number_of_holes';
                            put_line($file_key,$path,$key,$file_lang,$data,$line,$at_line,$universe); 
                        }
                    }
                    fclose($fp);
                            
                    
//                }
                 
        }
    }
}

if(sizeof($insert_list) >0){
        $sql = "replace into dict (tkey,lang,filename,filepath,filemd,col_1,col_2,col_3,col_4,col_5,col_6,alt_tkey) values ".implode(',',$insert_list);
         mysqli_query($dbx,$sql) or die($sql.'<br>'.mysqli_error($dbx)); 
        $insert_list = array();
    }
foreach($error_file as $v){
    echo $v.'<br>';
}


$sql = "update dict d, update_key_csv up set d.tkey = up.new_key where up.old_key = d.tkey";
mysqli_query($dbx,$sql);
echo '<pre>';

function hasUpperCaseLetter($string)
{
return strtolower($string) !== $string;
}
//print_r($lang_list );
function put_line($file_key,$path,$key,$lang,$data,$line,$at_line,$universe){
    global $dbx,$error_file,$insert_list,$lang_list,$alt_list,$alt_list_all,$def_text,$replace_text;
    if(sizeof($data)==0&&empty($data) || empty($data)){return;}
    if($key == 'av_output' && $file_key == 'digitalcamera'){return;}
    if(sizeof($data)==5){
       array_splice($data, 1, 0, ''); 
    }
//    if($file_key=='autopart'&&$lang=='es'){
//        print_r($data);
//        
//    }
    
    $alt_key = '';
//    if(isset($alt_list[strtolower($universe)][$file_key][$key])){ 
//        $alt_key = $alt_list[$universe][$file_key][$key];
//    }
    
    if($alt_key==''&& isset($alt_list[$key])){ 
        $alt_key = $alt_list[$key];
    }
    if($alt_key==''&& isset($alt_list_all[$key])){ 
        $alt_key = $alt_list_all[$key];
    }
    
    $jdata = json_encode($data);
    $md = md5($jdata);
    $fmd = md5($path);
    if(isset($lang_list[$path][$key][$lang]) && $lang_list[$path][$key][$lang] == $md){
        return;
    } 
        $lang_list[$path][$key][$lang] = $md;
     
    if(sizeof($data)==1){
        if(strpos($data[0],'":"')){
            $data = explode(':',$data[0]);
            $key = $data[0];
        }elseif(strpos($data[0],'";"')){
            $data = explode(';',$data[0]);
            $key = $data[0];
        }elseif(strpos($data[0],';')){
            $data = explode(';',$data[0]);
            $key = $data[0];
        }else{
        echo '^^^'.$path.'';
        echo ' #line#'.$at_line.'#'.'<br>';
        echo 'data is wrong<pre>'.print_r($data,true).'</pre><br>';
        exit;
        }
            foreach($data as $k=>$v){
                $data[$k] = str_replace(array("\'", "'"),"\'",$v);
                $data[$k] = str_replace(array("\\'"),"\'",$data[$k]);
                $data[$k] = str_replace(array('"'),"",$data[$k]);
            }
    } 
    if(sizeof($data)>20){
        $error_file[$path]= $path;return;
    }else if(sizeof($data)>6){
//        $error_file[$path]= $path;
        echo '^^^'.$path.'';
        echo ' #line#'.$at_line.'#'.'<br>';
        echo 'data is wrong<pre>'.print_r($data,true).'</pre><br>';
        return;}
        elseif(sizeof($data[1]) < 30 && empty($data[1])&&empty($data[2])&&empty($data[3])&& empty($data[4])&&empty($data[5])){
            return;
        }
    elseif(  empty($data[3])&&empty($data[5])){
//        echo '^^^'.$path.'';
//        echo ' #line#'.$at_line.'#'.'<br>';
//        echo 'data is wrong<pre>'.print_r($data,true).'</pre><br>';
//        return;
        if(!isset($data[4])){
            print_r($data);
        }
        $data[5] =$data[4];
        $data[4]='';
    }
    
    if($data[5]==''&&$data[4]==''&&$data[3]==''&&$data[2]=='' ){
        echo 'skip empty info line<br>';return;
    }
//    
    
    
    if(hasUpperCaseLetter($key)){
        $keywords =   explode(",",substr(preg_replace("/([A-Z])/",',\\1',$key),1));//preg_match_all('/[A-Z][^A-Z]*/',$str,$results); 
        $keywords = str_replace(' ', '', $keywords);
        $key = strtolower(implode('_',$keywords));
    }
    $t = htmlentities($data[5], ENT_COMPAT, 'UTF-8');
    if(isset($replace_text[$t])){
        $data[5] = $replace_text[$t];
    }
    if(strpos($data[5],'Souhait') !==false){
        $data[5] = 'Souhaité';
    }
    if(!in_array($lang,array('us','uk')) && isset($def_text['def'][strtolower($data[5])])){
        
        if(isset($def_text[$lang]) && isset($def_text[$lang][$def_text['def'][strtolower($data[5])]]))
            $data[5] = $def_text[$lang][$def_text['def'][strtolower($data[5])]];
    }
    if(trim($data[5])==''){
        $data[5]=$def_text[$lang][0];
    }
    
    foreach($data as $k=>$v){
        $data[$k] = str_replace(array('‘','’',"‘","’", chr(145), chr(146)),"\'",$data[$k]);
        if(in_array($lang,array('us','uk'))){
            $data[$k] = mb_convert_encoding ($data[$k], 'UTF-8','ISO-8859-1'  );
        }
    }
    $pos = strpos($key,'1 - ');
    if($pos!==false){
        $key = substr($key,0,$pos);
    }
    
    $pos = strpos($data[1],'1 - ');
    if($pos!==false){
        $data[1] = substr($data[1],0,$pos);
    }
    if($key=='is_adult_product'){
        $data[5] = $def_text[$lang][$def_text['def']['preferred']];
    }
 
    $insert_list[] = " ('$key','$lang','$file_key','$path','$fmd','{$data[0]}','{$data[1]}','{$data[2]}','{$data[3]}','{$data[4]}','{$data[5]}','$alt_key') ";
//    if($md=='14a6ed8a4ccab9023a0255a234ac19ca'){
//        echo 'data is wrong<pre>'.print_r($insert_list,true).'</pre><br>';
//         $sql = "replace into dict (tkey,lang,filename,filepath,col_1,col_2,col_3,col_4,col_5,col_6) values ".implode(',',$insert_list);
//         mysqli_query($dbx,$sql) or die($sql.'<br>'.mysqli_error($dbx)); 
//         echo $sql;
//        $insert_list = array();
//        exit;
//    }
    if(sizeof($insert_list) >100){
        $sql = "replace into dict (tkey,lang,filename,filepath,filemd,col_1,col_2,col_3,col_4,col_5,col_6,alt_tkey) values ".implode(',',$insert_list);
         mysqli_query($dbx,$sql) or die($sql.'<br>'.mysqli_error($dbx)); 
        $insert_list = array();
    }
//    echo $sql.'<br>'; 
}