<?php
ini_set('memory_limit','2000M'); 
ini_set('max_execution_time', 0);
error_reporting(E_ALL);
ini_set('display_errors',1);
$insert_sql = "replace into xsd_key (file,okey,tkey,path,line,md) values ";
$insert_list = array();
$ref_file = array();
$dbx = mysqli_connect('localhost','palmpcss','palmp4151') or die('Could not connect to server.' );
mysqli_select_db($dbx,'csv_proj' ) or die('Could not select database.');

$lang_list = array('uk','us','fr','es','de','it','mx','ca');
$dirx = dirname(__FILE__)."/xsd"; 
$dir = array_diff(scandir($dirx,1), array('..', '.'));

$clean_sql = "truncate xsd_key;";
mysqli_query($dbx,$clean_sql);
$value=array();
echo '<pre>';
//foreach($dir as $k=>$d){
//    $target_dir = $target.'/'.$d;
    
     
    $d_user = $dirx;
//    if(!is_dir($d_user))continue;
    $list = array_diff(scandir($d_user), array('..', '.'));
    
    foreach($list as $file_name){
        $path = $d_user.'/'.$file_name;
        $ext = pathinfo($path, PATHINFO_EXTENSION); 
        echo $path.'<br>';
        if($ext == 'xsd'){
 
                $xsd_txt = file_get_contents($path);
                $xmlarray = array();
                $xml_parser = xml_parser_create();
                xml_parse_into_struct($xml_parser, $xsd_txt, $xmlarray);
                
                
                $found_product_type = false;
                $open_tag = false;
                $found_tag = '';
                
                $open_sub_tag = false;
                $found_sub_tag = '';
                $start_sub_tag = 0;
                $value=array(); 
                if($file_name=='ClothingAccessories.xsd'){
                    foreach($xmlarray as $l => $t){
                    //    print_r($t);
                        $lv = isset($t['level'])?$t['level']:0;
                        $type = isset($t['type'])?$t['type']:'';
                        $tag = isset($t['tag'])?$t['tag']:'';
                        $name = isset($t['attributes']['NAME'])?$t['attributes']['NAME']:'';
                        $ref = isset($t['attributes']['REF'])?$t['attributes']['REF']:'';
                        $val = isset($t['attributes']['VALUE'])?$t['attributes']['VALUE']:''; 
                        if($type=='open'&&$lv==2 && $tag == 'XSD:ELEMENT'  ){
                            $found_product_type=true;
                            $found_tag = $name;
                            $open_tag=true;
                        }
                        if($found_product_type &&$lv==8&& $tag = 'XSD:ELEMENT' && $name=='VariationTheme'){
                            $open_sub_tag=true;
                            $start_sub_tag=$lv;
                        }
                        if($open_tag && $open_sub_tag &&  $type=='close'&& $lv == $start_sub_tag    ){
                            $open_sub_tag=false; $found_sub_tag='';
                        }
                        if($open_sub_tag&&$found_product_type&&$lv==$start_sub_tag+3&& $tag = 'XSD:ELEMENT'  ){
                                if(!empty($val))
                                $value[$found_tag][$val] = $val;
                                if(!empty($ref))
                                $value[$found_tag][$ref] = $ref;
                                if(!empty($name))
                                $value[$found_tag][$name] = $name;
                        }
                        if($found_product_type&&$type=='open'&&$lv==5&& $tag = 'XSD:ELEMENT' && $name=='ClassificationData'){
                            $open_sub_tag=true;
                            $start_sub_tag=$lv;
                        }
                         
                    } 
                }elseif($file_name=='EUCompliance.xsd'){
//                    print_r($xmlarray);
                    foreach($xmlarray as $l => $t){
//                         print_r($t);
                        $lv = isset($t['level'])?$t['level']:0;
                        $type = isset($t['type'])?$t['type']:'';
                        $tag = isset($t['tag'])?$t['tag']:'';
                        $name = isset($t['attributes']['NAME'])?$t['attributes']['NAME']:'';
                        $ref = isset($t['attributes']['REF'])?$t['attributes']['REF']:'';
                        $val = isset($t['attributes']['VALUE'])?$t['attributes']['VALUE']:''; 
                        if($type=='open'&&$lv==2 && $tag == 'XSD:ELEMENT'   ){
                            $found_product_type=true;
                            $found_tag = $name;
                            $open_tag=true;
                            $start_sub_tag=$lv;
                            
                        } 
                        if($open_tag  &&  $type=='close'&& $lv == $start_sub_tag    ){
                            $open_tag=false; $found_sub_tag='';
                            $found_product_type =false;
                        }
                        if($open_tag&&$found_product_type&&$lv==$start_sub_tag+3&& $tag = 'XSD:ELEMENT'  ){
                                if(!empty($val))
                                $value[$found_tag][$val] = $val;
                                if(!empty($ref))
                                $value[$found_tag][$ref] = $ref;
                                if(!empty($name))
                                $value[$found_tag][$name] = $name;
                        }
                     
                      
                    }   
//                    print_r($value);exit; 
                }elseif($file_name=='FoodServiceAndJanSan.xsd'){
//                    print_r($xmlarray);
                    foreach($xmlarray as $l => $t){
                    //    print_r($t);
                        $lv = isset($t['level'])?$t['level']:0;
                        $type = isset($t['type'])?$t['type']:'';
                        $tag = isset($t['tag'])?$t['tag']:'';
                        $name = isset($t['attributes']['NAME'])?$t['attributes']['NAME']:'';
                        $ref = isset($t['attributes']['REF'])?$t['attributes']['REF']:'';
                        $val = isset($t['attributes']['VALUE'])?$t['attributes']['VALUE']:''; 
                        if($type=='open'&&$lv==8 && $tag == 'XSD:ELEMENT'    ){
                            $found_product_type=true;
                            $found_tag = $name;
                            $open_tag=true;
                            $start_sub_tag=$lv;
//                            echo $name;exit;
                        }
                         
                        if($open_tag  &&  $type=='close'&& $lv == $start_sub_tag    ){
                            $open_tag=false; $found_sub_tag='';
                            $found_product_type =false;
                        }
                        if($open_tag&&$found_product_type&&$lv==$start_sub_tag+3&& $tag = 'XSD:ELEMENT'  ){
                                if(!empty($val))
                                $value[$found_tag][$val] = $val;
                                if(!empty($ref))
                                $value[$found_tag][$ref] = $ref;
                                if(!empty($name))
                                $value[$found_tag][$name] = $name;
                        }
                     
                         
                    }
//                     print_r($value);exit;
                }elseif($file_name=='GiftCards.xsd'){
//                    print_r($xmlarray);
                    foreach($xmlarray as $l => $t){
                    //    print_r($t);
                        $lv = isset($t['level'])?$t['level']:0;
                        $type = isset($t['type'])?$t['type']:'';
                        $tag = isset($t['tag'])?$t['tag']:'';
                        $name = isset($t['attributes']['NAME'])?$t['attributes']['NAME']:'';
                        $ref = isset($t['attributes']['REF'])?$t['attributes']['REF']:'';
                        $val = isset($t['attributes']['VALUE'])?$t['attributes']['VALUE']:''; 
                        if($type=='open'&&$lv==5 && $tag == 'XSD:ELEMENT'    ){
                            $found_product_type=true;
                            
//                            echo $name;exit;
                        }
                        if($found_product_type&&$type=='open'&&$lv==8&&$name!=''){
                            $found_tag = $name;
                            $open_tag=true;
                            $start_sub_tag=$lv;
                            $value[$found_tag]=array();
                        }elseif($found_product_type&&$type=='complete'&&$lv==8&&$name!=''){
                            $value[$name]=array();
                        }
                         
                        if($open_tag  &&  $type=='close'&& $lv == $start_sub_tag    ){
                          $found_sub_tag='';
                             $start_sub_tag=0;
                        }
                        if($open_tag &&$found_product_type &&  $type=='close'&& $lv == 5    ){
                          $found_product_type=false; 
                        }
                        if($open_tag&&$found_product_type&&$lv==$start_sub_tag+3&& $tag = 'XSD:ELEMENT'  ){
                            
                                if(!empty($val))
                                $value[$found_tag][$val] = $val;
                                if(!empty($ref))
                                $value[$found_tag][$ref] = $ref;
                                if(!empty($name))
                                $value[$found_tag][$name] = $name;
                        }
                        if($name=='VariationData'){
                                 
                                break; 
                            }
                        if($open_tag&&!$found_product_type && $lv==5 ){
                                
                                if(!empty($name)){
                                    foreach($value as $k=>$v){
                                        $value[$k][$name] = $name;
                                    }
                                }
                                
                        }
                     
                         
                    }
//                     print_r($value);exit;
                }elseif(in_array($file_name,array('Luggage.xsd','PowerTransmission.xsd','RawMaterials.xsd','SportsMemorabilia.xsd')) ){
//                    print_r($xmlarray);
                    foreach($xmlarray as $l => $t){
                    //    print_r($t);
                        $lv = isset($t['level'])?$t['level']:0;
                        $type = isset($t['type'])?$t['type']:'';
                        $tag = isset($t['tag'])?$t['tag']:'';
                        $name = isset($t['attributes']['NAME'])?$t['attributes']['NAME']:'';
                        $ref = isset($t['attributes']['REF'])?$t['attributes']['REF']:'';
                        $val = isset($t['attributes']['VALUE'])?$t['attributes']['VALUE']:''; 
                        if($type=='open'&&$lv==5 && $tag == 'XSD:ELEMENT'  && $name == 'ProductType'  ){
                            $found_product_type=true;
                            $open_tag=true;
//                            echo $name;exit;
                        }
                        if($found_product_type&&$type=='complete'&&$lv==8 ){
                            $found_tag=$val;
                            $value[$found_tag]=array(); 
                            
                        }
//                        if($name=='VariationTheme'){
//                            print_r(array($lv,(int)$open_sub_tag ,$start_sub_tag,$tag,$t));exit;
//                        }
                        if($found_product_type&&$type=='open'&&$lv==8 && $tag == 'XSD:ELEMENT'  && $name == 'VariationTheme'  ){
                            
                            $start_sub_tag=$lv;
                            $open_sub_tag=true;
                        }
                         
                        if($open_tag  &&  $type=='close'&& $lv == $start_sub_tag    ){
                            $found_sub_tag='';
                             $start_sub_tag=0;
                             $open_sub_tag=false;
                        }
//                        if($open_tag &&$found_product_type &&  $type=='close'&& $lv == 5    ){
//                          $found_product_type=false; 
//                        }
//                        if($val=='SizeName'){
//                            print_r(array($lv,(int)$open_sub_tag ,$start_sub_tag,$tag,$t));exit;
//                        }
                        if($open_sub_tag &$lv==$start_sub_tag+3&& $tag = 'XSD:ENUMERATION'  ){
                            
                                if(!empty($val))
                                $value[$found_tag][$val] = $val;
                                if(!empty($ref))
                                $value[$found_tag][$ref] = $ref;
                                if(!empty($name))
                                $value[$found_tag][$name] = $name;
                        }
                         
                        if($open_tag&& $found_product_type && $lv==5 ){
                                if($name!='VariationData')
                                if(!empty($name)){
                                    foreach($value as $k=>$v){
                                        $value[$k][$name] = $name;
                                    }
                                }
                                
                        }
                     
                         
                    }
                    if($file_name=='SportsMemorabilia.xsd'){
//                     print_r($value);exit;
                    }
                }elseif($file_name=='MechanicalFasteners.xsd'){
//                    print_r($xmlarray);
                    foreach($xmlarray as $l => $t){
                    //    print_r($t);
                        $lv = isset($t['level'])?$t['level']:0;
                        $type = isset($t['type'])?$t['type']:'';
                        $tag = isset($t['tag'])?$t['tag']:'';
                        $name = isset($t['attributes']['NAME'])?$t['attributes']['NAME']:'';
                        $ref = isset($t['attributes']['REF'])?$t['attributes']['REF']:'';
                        $val = isset($t['attributes']['VALUE'])?$t['attributes']['VALUE']:''; 
                        if($type=='open'&&$lv==5 && $tag == 'XSD:ELEMENT'  && $name == 'ProductType'  ){
                            $found_product_type=true;
                            $open_tag=true;
//                            echo $name;exit;
                        }
                        if($found_product_type&&$type=='open'&&$lv==8 ){
                            $found_tag=$name;
                            $value[$found_tag]=array(); 
                            $start_sub_tag=$lv;
                            $open_sub_tag=$open_tag=true;
                            
                            
                        } 
                         
                        if($open_tag  &&  $type=='close'&& $lv == $start_sub_tag    ){
                            $found_sub_tag='';
                             $start_sub_tag=0;
                             $open_sub_tag=$open_tag=false;
                             
                        }
//                        if($open_tag &&$found_product_type &&  $type=='close'&& $lv == 5    ){
//                          $found_product_type=false; 
//                        }
//                        if($val=='SizeName'){
//                            print_r(array($lv,(int)$open_sub_tag ,$start_sub_tag,$tag,$t));exit;
//                        }
                        if($open_sub_tag &$lv==$start_sub_tag+3   ){
                            
                                if(!empty($val))
                                $value[$found_tag][$val] = $val;
                                if(!empty($ref))
                                $value[$found_tag][$ref] = $ref;
                                if(!empty($name))
                                $value[$found_tag][$name] = $name;
                        }
                          
                     
                         
                    }
//                     print_r($value);exit;
                }elseif($file_name=='ProductClothing.xsd'){
//                    print_r($xmlarray);
                    foreach($xmlarray as $l => $t){
                    //    print_r($t);
                        $lv = isset($t['level'])?$t['level']:0;
                        $type = isset($t['type'])?$t['type']:'';
                        $tag = isset($t['tag'])?$t['tag']:'';
                        $name = isset($t['attributes']['NAME'])?$t['attributes']['NAME']:'';
                        $ref = isset($t['attributes']['REF'])?$t['attributes']['REF']:'';
                        $val = isset($t['attributes']['VALUE'])?$t['attributes']['VALUE']:''; 
                        if($type=='open'&&$lv==5&&$name=='ProductType' && $tag == 'XSD:ELEMENT'   ){
                            $found_product_type=true;
                            $open_tag=true; 
//                             $found_tag=$name;
//                            $value[$found_tag]=array();  
                            $start_sub_tag = $lv;
                        }
                        if($found_product_type &&$lv==8 && $tag=='XSD:ENUMERATION'){
                            $open_tag=true; 
                            $found_tag=$val;
                            $value[$found_tag]=array();  
                        }
                        
                        if($found_product_type&&$type=='open'&&$lv==8 && $tag == 'XSD:ELEMENT'  && $name == 'VariationTheme'  ){
                            
                            $start_sub_tag=$lv;
                            $open_sub_tag=true;
                        }
                        if($found_product_type&&$type=='open'&&$lv==5 && $tag == 'XSD:ELEMENT'  && $name == 'ClassificationData'  ){
                            
                            $start_sub_tag=$lv;
                            $open_sub_tag=true;
                            $open_tag=true;
                        }
                        if($open_tag  &&  $type=='close'&& $lv == $start_sub_tag    ){
                            $found_sub_tag='';
                             $start_sub_tag=0;
                             $open_sub_tag=$open_tag=false;
                             
                        }
//                        if($open_tag &&$found_product_type &&  $type=='close'&& $lv == 5    ){
//                          $found_product_type=false; 
//                        }
//                        if($val=='SizeName'){
//                            print_r(array($lv,(int)$open_sub_tag ,$start_sub_tag,$tag,$t));exit;
//                        }
                        if(!$open_tag && $lv == '5' && !($name == 'ClassificationData'||$name == 'VariationData')){
                             $dat = '';
                                if(!empty($val))
                                $dat = $val;
                                if(!empty($ref))
                                $dat= $ref;
                                if(!empty($name))
                                $dat = $name;
                                if(!empty($dat)){
                                    foreach($value as $k=>$v){
                                        $value[$k][$dat] = $dat;
                                    }
                                } 
                        }
                        if($open_tag&&$open_sub_tag &$lv==$start_sub_tag+3   ){
                            
                                $dat = '';
                                if(!empty($val))
                                $dat = $val;
                                if(!empty($ref))
                                $dat= $ref;
                                if(!empty($name))
                                $dat = $name;
                                if(!empty($dat)){
                                    foreach($value as $k=>$v){
                                        $value[$k][$dat] = $dat;
                                    }
                                } 
                        }
                          
                     
                         
                    }
//                     print_r($value);exit;
                }elseif($file_name=='Shoes.xsd'){
//                    print_r($xmlarray);
                    foreach($xmlarray as $l => $t){
                    //    print_r($t);
                        $lv = isset($t['level'])?$t['level']:0;
                        $type = isset($t['type'])?$t['type']:'';
                        $tag = isset($t['tag'])?$t['tag']:'';
                        $name = isset($t['attributes']['NAME'])?$t['attributes']['NAME']:'';
                        $ref = isset($t['attributes']['REF'])?$t['attributes']['REF']:'';
                        $val = isset($t['attributes']['VALUE'])?$t['attributes']['VALUE']:''; 
                        if($type=='open'&&$lv==5 && $tag == 'XSD:ELEMENT' && $name=='ClothingType'){
                            $found_product_type=true;
                            
                        }
                        if($found_product_type &&$lv==8 && $tag=='XSD:ENUMERATION'){
                            $open_tag=true; 
                            $found_tag=$val;
                            $value[$found_tag]=array();  
                        }
                            
                        if($found_product_type&&$type=='open'&&$lv==8 && $tag == 'XSD:ELEMENT'  && $name == 'VariationTheme'  ){
                            $open_tag=true;
                            $start_sub_tag=$lv;
                            $open_sub_tag=true;
                        }
                        if($found_product_type&&$type=='open'&&$lv==5 && $tag == 'XSD:ELEMENT'  && $name == 'ClassificationData'  ){
                            
                            $start_sub_tag=$lv;
                            $open_sub_tag=true;
                            $open_tag=true;
                        }
                        if($open_tag  &&  $type=='close'&& $lv == $start_sub_tag    ){
                            $found_sub_tag='';
                             $start_sub_tag=0;
                             $open_sub_tag=$open_tag=false;
                             
                        }
//                        if($open_tag &&$found_product_type &&  $type=='close'&& $lv == 5    ){
//                          $found_product_type=false; 
//                        }
//                        if($val=='SizeName'){
//                            print_r(array($lv,(int)$open_sub_tag ,$start_sub_tag,$tag,$t));exit;
//                        }
                        
                        if($open_tag&&$open_sub_tag &$lv==$start_sub_tag+3   ){
                                $dat = '';
                                if(!empty($val))
                                $dat = $val;
                                if(!empty($ref))
                                $dat= $ref;
                                if(!empty($name))
                                $dat = $name;
                                if(!empty($dat)){
                                    foreach($value as $k=>$v){
                                        $value[$k][$dat] = $dat;
                                    }
                                } 
                        }
                          
                     
                         
                    }
//                     print_r($value);exit;
                }elseif($file_name=='Sports.xsd'){
//                    print_r($xmlarray);
                    foreach($xmlarray as $l => $t){
                    //    print_r($t);
                        $lv = isset($t['level'])?$t['level']:0;
                        $type = isset($t['type'])?$t['type']:'';
                        $tag = isset($t['tag'])?$t['tag']:'';
                        $name = isset($t['attributes']['NAME'])?$t['attributes']['NAME']:'';
                        $ref = isset($t['attributes']['REF'])?$t['attributes']['REF']:'';
                        $val = isset($t['attributes']['VALUE'])?$t['attributes']['VALUE']:''; 
                        if($type=='open'&&$lv==5 && $tag == 'XSD:ELEMENT' && $name=='ProductType'){
                            $found_product_type=true;
                            
                        }
                        if($found_product_type &&$lv==8 && $tag=='XSD:ENUMERATION'){
                            $open_tag=true; 
                            $found_tag=$val;
                            $value[$found_tag]=array();  
                        }
                            
//                        if(!$found_product_type&&$type=='open'&&$lv==8 && $tag == 'XSD:ELEMENT'  && $name == 'VariationTheme'  ){
//                            $open_tag=true;
//                            $start_sub_tag=$lv;
//                            $open_sub_tag=true;
//                        }
                        if(!$found_product_type&&$type=='open'&&$lv==5 && $tag == 'XSD:ELEMENT'  && $name == 'ClassificationData'  ){
                            
                            $start_sub_tag=$lv;
                            $open_sub_tag=true;
                            $open_tag=true;
                        }
                        if($open_tag  &&  $type=='close'&& $lv == $start_sub_tag    ){
                            $found_sub_tag='';
                             $start_sub_tag=0;
                             $open_sub_tag=$open_tag=false;
                             
                        }
                        if($open_tag &&$found_product_type &&  $type=='close'&& $lv == 5    ){
                          $found_product_type=false; 
                        }
//                        if($val=='SizeName'){
//                            print_r(array($lv,(int)$open_sub_tag ,$start_sub_tag,$tag,$t));exit;
//                        }
                        
                        if(!$found_product_type&&$open_tag&&$open_sub_tag &$lv==$start_sub_tag+3   ){
                                $dat = '';
                                if(!empty($val))
                                $dat = $val;
                                if(!empty($ref))
                                $dat= $ref;
                                if(!empty($name))
                                $dat = $name;
                                if(!empty($dat)){
                                    foreach($value as $k=>$v){
                                        $value[$k][$dat] = $dat;
                                    }
                                } 
                        }
                        
                        if(!$found_product_type&&$open_tag&& $type=='complete'&&$lv==8 && $tag == 'XSD:ELEMENT'   ){
                                $dat = '';
                                if(!empty($val))
                                $dat = $val;
                                if(!empty($ref))
                                $dat= $ref;
                                if(!empty($name))
                                $dat = $name;
                                if(!empty($dat)){
                                    foreach($value as $k=>$v){
                                        $value[$k][$dat] = $dat;
                                    }
                                } 
                        }
                        if(!$found_product_type&&$open_tag &&$lv==5 && $tag == 'XSD:ELEMENT' && $name!='ProductType' && $name!='VariationData'  ){
                                $dat = '';
                                if(!empty($val))
                                $dat = $val;
                                if(!empty($ref))
                                $dat= $ref;
                                if(!empty($name))
                                $dat = $name;
                                if(!empty($dat)){
                                    foreach($value as $k=>$v){
                                        $value[$k][$dat] = $dat;
                                    }
                                } 
                        }
                          
                     
                         
                    }
//                     print_r($value);exit;
                }elseif($file_name=='WineAndAlcohol.xsd'){
//                    print_r($xmlarray);
                    foreach($xmlarray as $l => $t){
                    //    print_r($t);
                        $lv = isset($t['level'])?$t['level']:0;
                        $type = isset($t['type'])?$t['type']:'';
                        $tag = isset($t['tag'])?$t['tag']:'';
                        $name = isset($t['attributes']['NAME'])?$t['attributes']['NAME']:'';
                        $ref = isset($t['attributes']['REF'])?$t['attributes']['REF']:'';
                        $val = isset($t['attributes']['VALUE'])?$t['attributes']['VALUE']:''; 
                        if($type=='open'&&$lv==5 && $tag == 'XSD:ELEMENT'  && $name == 'ProductType'  ){
                            $found_product_type=true;
                            $open_tag=true;
//                            echo $name;exit;
                        }
                        if($found_product_type&&$type=='open'&&$lv==8 ){
                            $found_tag=$name;
                            $value[$found_tag]=array(); 
                            $start_sub_tag=$lv;
                            $open_sub_tag=$open_tag=true; 
                        }else if($found_product_type&&$type=='complete'&&$lv==8 ){
                            $found_tag=$name;
                            $value[$found_tag]=array();
                        }
                        if(!$found_product_type&&$type=='open'&&$lv==8 && $tag == 'XSD:ELEMENT'  && $name == 'VariationTheme'  ){
                            $open_tag=true;
                            $start_sub_tag=$lv;
                            $open_sub_tag=true;
                            
                        }
                        if($open_tag  &&  $type=='close'&& $lv == $start_sub_tag    ){
                            $found_sub_tag='';
                             $start_sub_tag=0;
                             $open_sub_tag=$open_tag=false;
                             
                        }
                        if( $found_product_type  && $lv == 5 && $name=='VariationData'   ){
                          $found_product_type=false; 
                        }
//                        if($val=='SizeName'){
//                            print_r(array($lv,(int)$open_sub_tag ,$start_sub_tag,$tag,$t));exit;
//                        }
                        if( $open_sub_tag &$lv==$start_sub_tag+3   ){
                            $dat = '';
                                if(!empty($val))
                                $dat = $val;
                                if(!empty($ref))
                                $dat= $ref;
                                if(!empty($name))
                                $dat = $name;
                                if(!empty($dat)){
                                    foreach($value as $k=>$v){
                                        $value[$k][$dat] = $dat;
                                    }
                                } 
                        }
                        
                        if(!$found_product_type&&!$open_tag &&$lv==5 && $tag == 'XSD:ELEMENT' && $name!='ProductType' && $name!='VariationData'  ){
                                $dat = '';
                                if(!empty($val))
                                $dat = $val;
                                if(!empty($ref))
                                $dat= $ref;
                                if(!empty($name))
                                $dat = $name;
                                if(!empty($dat)){
                                    foreach($value as $k=>$v){
                                        $value[$k][$dat] = $dat;
                                    }
                                } 
                        }
                          
                     
                         
                    }
//                     print_r($value);exit;
                }else{
//                    if($file_name=='Luggage.xsd'){
//                        print_r($xmlarray);
//                    }
                    foreach($xmlarray as $l => $t){
                    //    print_r($t);
                        $lv = isset($t['level'])?$t['level']:0;
                        $type = isset($t['type'])?$t['type']:'';
                        $tag = isset($t['tag'])?$t['tag']:'';
                        $name = isset($t['attributes']['NAME'])?$t['attributes']['NAME']:'';
                        $ref = isset($t['attributes']['REF'])?$t['attributes']['REF']:'';
                        $val = isset($t['attributes']['VALUE'])?$t['attributes']['VALUE']:''; 
                        if($type=='open'&&$lv==5 && $tag == 'XSD:ELEMENT' && $name == 'ProductType'){
                            $start_lv = $lv;
                            $found_product_type = true;
                    //        echo 'x1<br>';
                        }

                    //    print_r(array($found_product_type,$type,$lv,$tag,$ref,$start_lv+3));
                        if($found_product_type==true && $type=='complete'&& $lv == $start_lv+3 && $tag == 'XSD:ELEMENT' && !empty($ref)  ){
                            $ref_file[] = $ref;
                    //        echo 'x2<br>';
                        }
//                        if($found_product_type==true && $type=='complete'&& $lv == $start_lv+3 && $tag == 'XSD:ELEMENT' && !empty($val)  ){
//                            $ref_file[] = $val;
//                    //        echo 'x2<br>';
//                        }
                        if($found_product_type==true&&$type=='close'&&$lv==$start_lv && $tag = 'XSD:ELEMENT'  ){

                            $found_product_type = false;
                    //        echo 'x3<br>';
                    //        break;
                        }
//                        if($file_name=='Luggage.xsd'){
//                            print_r($ref_file);
//                        }

                        if(!empty($ref_file) && !$found_product_type){
                            if($lv==2&& $tag = 'XSD:ELEMENT' && $type=='open' && in_array($name,$ref_file)){
                                $open_tag = true;
                                $found_tag=$name;
                                $start_lv=$lv;
                            }
                            if($lv==2&& $tag = 'XSD:ELEMENT' && $type=='close'  ){
                                $open_tag = false;  
                            }
                    //        if($open_tag &&  $type=='complete'&& $lv == $start_lv+3 && $tag == 'XSD:ELEMENT' && !empty($ref)){
                    //            $value[$found_tag][$ref] = $ref;
                    //        }
                            if($open_tag &&  $type=='complete'&& $lv == $start_lv+3 && $tag == 'XSD:ELEMENT' ){
                                if(!empty($ref))
                                $value[$found_tag][$ref] = $ref;
                                if(!empty($name))
                                $value[$found_tag][$name] = $name;
                            }

                            if($open_tag &&  $type=='open'&& $lv == $start_lv+3 && $tag == 'XSD:ELEMENT'  ){
                                if($name == 'VariationData'){continue;}
                                if($ref == 'VariationData'){continue;}
                                if(!empty($name))
                    //            $value[$found_tag][$name] = $name;
                                $open_sub_tag=true; 
                                $found_sub_tag=$name;
                                $start_sub_tag = $lv;
                            }
                            if($open_sub_tag  && !empty($found_sub_tag) &&  $type=='complete'&& $lv == $start_sub_tag+3 && $tag == 'XSD:ELEMENT' ){
                    //            print_r(array($t,$start_sub_tag));
                            }
                            if($open_tag && $open_sub_tag &&  $type=='close'&& $lv == $start_sub_tag+3   ){
                                $open_sub_tag=false; $found_sub_tag='';
                            }

                            if($open_tag && $open_sub_tag && !empty($found_sub_tag) &&  $type=='complete'&& $lv == $start_sub_tag+3 && $tag == 'XSD:ELEMENT'  ){
                                $value[$found_tag][$found_sub_tag][] = $name;
                    //            echo 'xxxx1'.$found_sub_tag.' '.$name.'<br>';
                            }

                            if($open_tag && $open_sub_tag && !empty($found_sub_tag) &&  $type=='complete'&& $lv == $start_sub_tag+3 && $tag == 'XSD:ENUMERATION'  ){
                                $value[$found_tag][$found_sub_tag][] = $val;
                    //            echo 'xxxx1'.$found_sub_tag.' '.$val.'<br>';
                            }

                            if($open_tag &&  $type=='open'&& $lv == $start_lv+6 && $tag == 'XSD:ELEMENT' && $name == 'VariationTheme' ){
                    //            $value[$found_tag][$name] = $name;
                                $open_sub_tag=true; 
                                $found_sub_tag=$name;
                                $start_sub_tag = $lv;
                    //            echo 'xxxx0'.$name.'<br>'; 
                            }

                        }
                    }
//                    if($file_name=='Luggage.xsd'){
//                    print_r($value);exit;
//                    }
                    
                }
                foreach($value as $file=>$v){
                        if(is_array($v)){
                            foreach($v as $key => $val){
                                if(is_array($val)){
                                    foreach($val as $vv){

                                        if($key == 'VariationTheme' && $vv != 'Size-Color'&& $vv != 'SizeColor'){
                                            if(strpos($vv,'-') !==false){
                                                continue;
                                            }
                                            insert_key_into_db($file,$vv,$path,__LINE__ );
                                        }elseif($key == 'ColorSpecification' ){
                                            insert_key_into_db($file,$vv,$path,__LINE__);
                                        }else{
                                            insert_key_into_db($file,$key,$path,__LINE__); 
                                        }
                                    }

                                }else{
                                    insert_key_into_db($file,$key,$path,__LINE__); 
                                }
                            }
                        }else{
                            insert_key_into_db($file,$v,$path,__LINE__); 
                        }
                    }
        }
//    }
}
//print_r($ref_file);
if(sizeof($insert_list)>0){
    $sql = $insert_sql . implode(',',$insert_list);
        mysqli_query($dbx,$sql) or die($sql.'<br>'.mysqli_error($dbx)); 
//        echo $sql.'<br>';
}
//print_r($value);



function insert_key_into_db($file,$key,$path,$line ){
    global $insert_list,$insert_sql,$dbx;
    if(sizeof($insert_list)>600){
        $sql = $insert_sql . implode(',',$insert_list);
//        echo $sql.'<br>';exit;
        mysqli_query($dbx,$sql) or die($sql.'<br>'.mysqli_error($dbx)); 
        $insert_list=array();
    }
//    echo " ('$file','$key') <br>";
    if(is_array($key)){
        echo $line.' '.$path.' '.$file.'<br>';
        print_r($key);exit;
    }
    if(hasUpperCaseLetter($key)){
    $keywords =   explode(",",substr(preg_replace("/([A-Z])/",',\\1',$key),1));//preg_match_all('/[A-Z][^A-Z]*/',$str,$results);
    $keywords = strtolower(implode('_',$keywords));
    }else{
        $keywords = $key;
    }
     
    if(in_array($keywords,array('variation_theme', 'variation_data','nit_of_measure','parent','child','clothing_type'))){return;}
    
    $insert_list[] = " ('$file','$key','$keywords','$path','{$line}','".md5($path)."') ";
    
}
function hasUpperCaseLetter($string)
{
return strtolower($string) !== $string;
}