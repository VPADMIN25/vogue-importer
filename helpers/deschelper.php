<?php
//$description = removeHTMLFormatting($description, false);
//  return array("status" => "success", "message" => $description);
// return array("status" => "fail", "message" => "RegEx not matched.");
function applyDescriptionFilter($description, $item_specific, $condition_note, $condition_val, $is_item_specific, $is_condition_note, $is_condition_val, $user_id)
{


    //echo "desc".json_encode($item_specific,true);


    

    if($user_id == 46190){ 
        echo "pankaj";
        $pattern1 = '/<font\s*rwr="1"\s*style="">(.*)<\/font><\/b><\/li>/sm';
        $res1 = preg_match_all($pattern1, $description, $result1);
        if ($res1) {
              print_r($result1);
              $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
              $description = removeHTMLFormatting($description, true);
              return array("status" => "success", "message" => $description); 
        }else{
            $pattern1 = '/<p\s*class="MsoNormal"\s*style="line-height:\s*normal;">(.*)<p\s*class="MsoNormal"\s*style="font-size:\s*14pt;\s*line-height:\s*normal;">/sm';
            $res1 = preg_match_all($pattern1, $description, $result1);
            if ($res1) {
                  print_r($result1);
                  $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                  $description = removeHTMLFormatting($description, true);
                  return array("status" => "success", "message" => $description); 
            }else{
                $pattern1 = '/<p\s*class="MsoNormal"\s*style="line-height:\s*normal;\s*font-size:\s*26.6667px;">(.*)<p\s*class="MsoNormal"\s*style="font-size:\s*14pt;\s*line-height:\s*normal;">/sm';
                $res1 = preg_match_all($pattern1, $description, $result1);
                if ($res1) {
                      print_r($result1);
                      $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                      $description = removeHTMLFormatting($description, true);
                      return array("status" => "success", "message" => $description); 
                }else{
                    $pattern1 = '/<font\s*rwr="1"\s*style="font-family:\s*Arial;"><font\s*rwr="1">(.*)<p\s*class="MsoNormal"\s*style="font-size:\s*14pt;\s*line-height:\s*normal;">/sm';
                    $res1 = preg_match_all($pattern1, $description, $result1);
                    if ($res1) {
                          print_r($result1);
                          $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                          $description = removeHTMLFormatting($description, true);
                          return array("status" => "success", "message" => $description); 
                    }else{
                        $pattern1 = '/<font\s*color="#ed7d31"\s*face="Arial">(.*)<\/font><\/font><\/li>/sm';
                        $res1 = preg_match_all($pattern1, $description, $result1);
                        if ($res1) {
                              print_r($result1);
                              $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                              $description = removeHTMLFormatting($description, true);
                              return array("status" => "success", "message" => $description); 
                        }
               
         else {
            $pattern1 = '/<p\s*class="MsoNormal"\s*style="line-height:\s*normal;">(.*)<b>Payment<\/b>/sm';
            $res1 = preg_match_all($pattern1, $description, $result1);
            if ($res1) {
                  print_r($result1);
                  $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                  $description = removeHTMLFormatting($description, true);
                  return array("status" => "success", "message" => $description); 
            }
        
            else {
                $pattern1 = '/<p\s*class="MsoNormal"\s*style="line-height:\s*normal;">(.*)<b><font color="#62bc5b">Payment<\/font>/sm';
                $res1 = preg_match_all($pattern1, $description, $result1);
                if ($res1) {
                      print_r($result1);
                      $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                      $description = removeHTMLFormatting($description, true);
                      return array("status" => "success", "message" => $description); 
                }
            
                else {
                    return array("status" => "fail", "message" => "RegEx not matched.");
    
                }

            }
           
        }
    }
        }
    }
        }
}

if($user_id == 48908 ){
    $item_specificObj = json_decode($item_specific, true);
$itemSpecificStr = '';

// Define the display order
$desiredOrder = [
    'Tipo',
    'Regione',
    'Gradazione alcolica',
    'Volume',
    'Sistema di valutazione',  // Optional
    'Valutazione'              // Optional
];

foreach ($desiredOrder as $key) {
    if (!empty($item_specificObj[$key])) {
        $itemSpecificStr .= '<p style="font-family: arial; font-size: large;"><strong>' . $key . ':</strong> ' . $item_specificObj[$key] . '</p>';
    }
}

echo $itemSpecificStr;

$description = preg_replace('/\s+/', ' ', $description);
$description = preg_replace('/<br\s*\/?>/i', '', $description);
$styleTag = '<style>p { margin: 20px 0 0; }</style>';

if (stripos(trim($description), '<p>') !== 0) {
    $description = $itemSpecificStr  . $styleTag . '<p>' . $description . '</p>';
} else {
    $description = $itemSpecificStr . $styleTag . $description;
}
  
    $description = preg_replace('/\s+/', ' ', $description);
   
    return array("status" => "success", "message" => $description); 
    
 }
if( $user_id == 49067){
    $conditionStr ="";
    //$description ="";
    if ($condition_val != '' ) {
        $conditionStr = $conditionStr . "<p style=\'word-wrap: break-word\'><strong>Condition:</strong>  ".$condition_val."</p>";
    }
    if ($condition_note != '' ) {
        $conditionStr = $conditionStr . "<p style=\'word-wrap: break-word\'><strong>Condition Notes:</strong> " . $condition_note . "</p>";
    }
    $item_specificObj = json_decode($item_specific, true);
    foreach ($item_specificObj as $k => $v) {
        $itemSpecificStr = $itemSpecificStr . '<p><strong>' . $k . ':</strong> ' . $v . '</p>';
    } 
    
    $description = $conditionStr.$itemSpecificStr.$description;
    return array("status" => "success", "message" => $description); 
    
 }
 if($user_id == 48805){
    //$description ="";
    $itemSpecificStr ='';
    if ($condition_val != '' ) {
        $conditionStr = $conditionStr . "<p style=\'word-wrap: break-word\'><strong>Condition:</strong>  ".$condition_val."</p>";
    }
    if ($condition_note != '' ) {
        $conditionStr = $conditionStr . "<p style=\'word-wrap: break-word\'><strong>Condition Notes:</strong> " . $condition_note . "</p>";
    }
    $item_specificObj = json_decode($item_specific, true);
    foreach ($item_specificObj as $k => $v) {
        $itemSpecificStr = $itemSpecificStr . '<p><strong>' . $k . ':</strong> ' . $v . '</p>';
    } 
    $description = removeHTMLFormatting($description, true);
    
    $pattern1 = '/<!--\?{6}[^>]*>(.*?)<!--\?\?-->/si';
    $res1 = preg_match_all($pattern1, $description, $result1);
    if ($res1) {
            print_r($result1);
            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
            $description = removeHTMLFormatting($description, true);
         
            $description = preg_replace('/<td[^>]*>(.*?)<\/td>/i', '<td>$1</td>', $description);
            $description = $itemSpecificStr.$description;
        return array("status" => "success", "message" => $description); 
      }
            
    else {
        $pattern1 = '/<div\s*class="description">(.*)<div\s*class="shipping">/s';
        $res1 = preg_match_all($pattern1, $description, $result1);
        if ($res1) {
                print_r($result1);
                $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                $description = removeHTMLFormatting($description, true);
             
                $description = preg_replace('/<td[^>]*>(.*?)<\/td>/i', '<td>$1</td>', $description);
                $description = $itemSpecificStr.$description;
            return array("status" => "success", "message" => $description); 
          }
                
        else {
                return array("status" => "fail", "message" => "RegEx not matched.");
        
            }
    
        }
 
 }
if($user_id == 46828){
    $itemSpecificStr ='<p class=\'sub_tit\'>Item Specifications</p><ul>';

    $item_specificObj = json_decode($item_specific, true);
    foreach ($item_specificObj as $k => $v) {
        $itemSpecificStr = $itemSpecificStr . '<p><strong>' . $k . ':</strong> ' . $v . '</p>';
    } 
    
    $description = removeHTMLFormatting($description, true);
    $description = $itemSpecificStr.$description;
    return array("status" => "success", "message" => $description); 
    
 }
if($user_id == 48747){
    $conditionStr ="";
    $description ="";
    if ($condition_val != '' ) {
        $conditionStr = $conditionStr . "<p style=\'word-wrap: break-word\'><strong>Condition:</strong>  ".$condition_val."</p>";
    }
    if ($condition_note != '' ) {
        $conditionStr = $conditionStr . "<p style=\'word-wrap: break-word\'><strong>Condition Notes:</strong> " . $condition_note . "</p>";
    }
    $item_specificObj = json_decode($item_specific, true);
    foreach ($item_specificObj as $k => $v) {
        $itemSpecificStr = $itemSpecificStr . '<p><strong>' . $k . ':</strong> ' . $v . '</p>';
    } 
    
    $description = $conditionStr.$itemSpecificStr.$description;
    return array("status" => "success", "message" => $description); 
}
 if ($user_id == 47369) { 
    echo "pankaj";
    $conditionStr="";
   
    $itemSpecificStr = "";
    if ($condition_val != '' ) {
        $conditionStr = $conditionStr . "<p style=\'word-wrap: break-word\'><strong>Condition:</strong>  ".$condition_val."</p>";
    }
    if ($condition_note != '' ) {
        $conditionStr = $conditionStr . "<p style=\'word-wrap: break-word\'><strong>Condition Notes:</strong> " . $condition_note . "</p>";
    }
    // Decode item specifics
    $item_specificObj = json_decode($item_specific, true);
    if (is_array($item_specificObj)) {
        foreach ($item_specificObj as $k => $v) {
            $itemSpecificStr .= '<p><strong>' . htmlspecialchars($k) . ':</strong> ' . htmlspecialchars($v) . '</p>';
        }
    }
    $pattern1 = '/<!--\s*Einzelheiten\s*-->(.*)<!--\s*Steuern\s*-->/si';
    $res1 = preg_match_all($pattern1, $description, $result1);
    if ($res1) {
                          
        $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
        $description = removeHTMLFormatting($description, true);
        $description = preg_replace("/<h1[^>]*>.*?<\/h1>/is", "", $description);
        $description = preg_replace("/<h2[^>]*>.*?<\/h2>/is", "", $description);
        $description = preg_replace("/<h3[^>]*>.*?<\/h3>/is", "", $description);
        $description = preg_replace("/<h4[^>]*>.*?<\/h4>/is", "", $description);
        $description =  $conditionStr.$itemSpecificStr . $description;
        echo $description;
        return array("status" => "success", "message" => $description); 
    }
    else {
        $description = removeHTMLFormatting($description, true);
        $pattern1 = '/<div\s*class="jumbotron">(.*)<div\s*class="col-lg-offset-0\s*col-lg-12\s*col-sm-12">/si';
        $res1 = preg_match_all($pattern1, $description, $result1);
        if ($res1) {
                              
            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
            $description = removeHTMLFormatting($description, true);
            $description = preg_replace("/<h1[^>]*>.*?<\/h1>/is", "", $description);
            $description = preg_replace("/<h2[^>]*>.*?<\/h2>/is", "", $description);
            $description = preg_replace("/<h3[^>]*>.*?<\/h3>/is", "", $description);
            $description = preg_replace("/<h4[^>]*>.*?<\/h4>/is", "", $description);
            $description =  $conditionStr.$itemSpecificStr . $description;
            echo $description;
            return array("status" => "success", "message" => $description); 
        }
        else {
            
            $pattern1 = '/<!--\s*Shop\s*speichern\s*-->(.*)<!--\s*Steuern\s*-->/s';
            $res1 = preg_match_all($pattern1, $description, $result1);
            if ($res1) {
                                  
                $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                $description = removeHTMLFormatting($description, true);
                $description = preg_replace("/<h1[^>]*>.*?<\/h1>/is", "", $description);
                $description = preg_replace("/<h2[^>]*>.*?<\/h2>/is", "", $description);
                $description = preg_replace("/<h3[^>]*>.*?<\/h3>/is", "", $description);
                $description = preg_replace("/<h4[^>]*>.*?<\/h4>/is", "", $description);
                $description =  $conditionStr.$itemSpecificStr . $description;
                echo $description;
                return array("status" => "success", "message" => $description); 
            }
            else {
               
                $pattern1 = '/<div\s*class="mail">(.*)<font\s*style=[^>]*>/s';
                $res1 = preg_match_all($pattern1, $description, $result1);
                if ($res1) {
                                      
                    $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                    $description = removeHTMLFormatting($description, true);
                    $description = preg_replace("/<h1[^>]*>.*?<\/h1>/is", "", $description);
                    $description = preg_replace("/<h2[^>]*>.*?<\/h2>/is", "", $description);
                    $description = preg_replace("/<h3[^>]*>.*?<\/h3>/is", "", $description);
                    $description = preg_replace("/<h4[^>]*>.*?<\/h4>/is", "", $description);
                    $description =  $conditionStr.$itemSpecificStr . $description;
                    echo $description;
                    return array("status" => "success", "message" => $description); 
                }
                else {
                    $description = removeHTMLFormatting($description, true);
                    $pattern1 = '/<!--\s*artikelbeschreibung begin -->(.*)<!--\s*Artikelbeschreibung Anfang\s*-->/s';
                    $res1 = preg_match_all($pattern1, $description, $result1);
                    if ($res1) {
                                          
                        $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                        $description = removeHTMLFormatting($description, true);
                        $description = preg_replace("/<h1[^>]*>.*?<\/h1>/is", "", $description);
                        $description = preg_replace("/<h2[^>]*>.*?<\/h2>/is", "", $description);
                        $description = preg_replace("/<h3[^>]*>.*?<\/h3>/is", "", $description);
                        $description = preg_replace("/<h4[^>]*>.*?<\/h4>/is", "", $description);
                        $description =  $conditionStr.$itemSpecificStr . $description;
                        echo $description;
                        return array("status" => "success", "message" => $description); 
                    }
                    else {
                        
                        $description = removeHTMLFormatting($description, true);
                        $pattern1 = '/<div\s*class="tab-content"\s*id="tab-content1">(.*)<div\s*class="tab-content"\s*id="tab-content2">/s';
                        $res1 = preg_match_all($pattern1, $description, $result1);
                        if ($res1) {
                                              
                            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                            $description = removeHTMLFormatting($description, true);
                            $description = preg_replace("/<h1[^>]*>.*?<\/h1>/is", "", $description);
                            $description = preg_replace("/<h2[^>]*>.*?<\/h2>/is", "", $description);
                            $description = preg_replace("/<h3[^>]*>.*?<\/h3>/is", "", $description);
                            $description = preg_replace("/<h4[^>]*>.*?<\/h4>/is", "", $description);
                            $description =  $conditionStr.$itemSpecificStr . $description;
                            echo $description;
                            return array("status" => "success", "message" => $description); 
                        }
                        else {
                            //$description = removeHTMLFormatting($description, true);
                        $pattern1 = '/Shop\s*speichern\s*<\/a>\s*<\/div>\s*<br>\s*<br>\s*<div\s*class="container">(.*)Bei\s*gebrauchten\s*Artikeln\s*ist\s*die\s*Ware/s';
                        $res1 = preg_match_all($pattern1, $description, $result1);
                        if ($res1) {
                                              
                            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                            $description = removeHTMLFormatting($description, true);
                            $description = preg_replace("/<h1[^>]*>.*?<\/h1>/is", "", $description);
                            $description = preg_replace("/<h2[^>]*>.*?<\/h2>/is", "", $description);
                            $description = preg_replace("/<h3[^>]*>.*?<\/h3>/is", "", $description);
                            $description = preg_replace("/<h4[^>]*>.*?<\/h4>/is", "", $description);
                            $description =  $conditionStr.$itemSpecificStr . $description;
                            echo $description;
                            return array("status" => "success", "message" => $description); 
                        }
                        else {
                            $pattern1 = '/Einzelheiten\s*zum\s*Artikel<\/h2>(.*)Bei\s*gebrauchten\s*Artikeln\s*ist/s';
                        $res1 = preg_match_all($pattern1, $description, $result1);
                        if ($res1) {
                                              
                            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                            $description = removeHTMLFormatting($description, true);
                            $description = preg_replace("/<h1[^>]*>.*?<\/h1>/is", "", $description);
                            $description = preg_replace("/<h2[^>]*>.*?<\/h2>/is", "", $description);
                            $description = preg_replace("/<h3[^>]*>.*?<\/h3>/is", "", $description);
                            $description = preg_replace("/<h4[^>]*>.*?<\/h4>/is", "", $description);
                            $description =  $conditionStr.$itemSpecificStr . $description;
                            echo $description;
                            return array("status" => "success", "message" => $description); 
                        }
                        else {
                            $description = removeHTMLFormatting($description, true);
                            echo "jhjg".$description;
                             //die();
                    
                         return array("status" => "fail", "message" => "RegEx not matched.");
                        }
                        }
                        }
                    }
                }
            }
        }
    }

 }

 if($user_id == 48551){
    $pattern1 = '/<!--DESCRIPTION Start-->(.*)<div\s*class="tabarea"[^>]*>/s';
    $res1 = preg_match_all($pattern1, $description, $result1);
    if ($res1) {
                          
        $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
        $description = removeHTMLFormatting($description, true);

        return array("status" => "success", "message" => $description); 
    }
    else {
     return array("status" => "fail", "message" => "RegEx not matched.");
    }
    
    
 }


 if($user_id == 48741){ 
    $conditionStr ="";
    $description ="";
    if ($condition_val != '' ) {
        $conditionStr = $conditionStr . "<p style=\'word-wrap: break-word\'><strong>Condition:</strong>  ".$condition_val."</p>";
    }
    if ($condition_note != '' ) {
        $conditionStr = $conditionStr . "<p style=\'word-wrap: break-word\'><strong>Condition Notes:</strong> " . $condition_note . "</p>";
    }
    
    $existingItemJson = json_decode($item_specific, true);
    foreach ($existingItemJson as $k => $v) {
        if ($k != "ItemCompatibilityList") {
            $conditionStr = $conditionStr . '<p style=\'word-wrap: break-word\'><strong>' . $k . ':</strong> ' . $v . '</p>';
        }
    }
    $description = $conditionStr . $description ;
    return array("status" => "success", "message" => $description);
}
if($user_id == 48687){ 
    $conditionStr ="";
   
    if ($condition_val != '' ) {
        $conditionStr = $conditionStr . "<p style=\'word-wrap: break-word\'><strong>Condition:</strong>  ".$condition_val."</p>";
    }
    $description = $conditionStr . $description ;
    return array("status" => "success", "message" => $description);
}

 if($user_id == 48599){ 

    //!--\s*description start\s*-->(<div>.*?<\/div>\s*)+<div>
    //<div\s*class="tds-gallery-specifics">(.*?)<table\s*id="u_content_divider_7"\s*[^>]+>
        
    $pattern1 = '/!--\s*description start\s*-->(<div>.*?<\/div>\s*)+<div>/sm';
    $res1 = preg_match_all($pattern1, $description, $result1);
    if ($res1) {
          //print_r($result1);
          $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
          $description = removeHTMLFormatting($description, true);
       
          return array("status" => "success", "message" => $description); 
        } else {
            $description =  removeHTMLFormatting($description,  true);
            $pattern1 = '/<!-- description start -->(.*)<!-- description end -->/s';
            $res1 = preg_match_all($pattern1, $description, $result1);
            if ($res1) {
          //print_r($result1);
            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
            $description =  removeHTMLFormatting($description,  true);
            $existingItemJson = json_decode($item_specific, true);
            $conditionStr = "";
            $i = 1;
            foreach ($existingItemJson as $k => $v) {
                if ($k != "ItemCompatibilityList") {
                    if($i == 1){
                       $conditionStr  =$conditionStr .'<tr><td width="50%"><strong>'.$k.':</strong> '.$v.'</td>';   
                        $i = 2;
                    } else if($i == 2){
                       $conditionStr  =$conditionStr .'<td width="50%"><strong>'.$k.':</strong> '.$v.'</td></tr>'; 
                        $i = 1;
                    }
                }
            }
            if($i == 2){
                $conditionStr  = $conditionStr .'<td width="50%"></td></tr>'; 
            }
            if($conditionStr != ''){
                $conditionStr  = '<table cellspacing="5" cellpadding="5" style="width:100%;word-break: break-word;margin-bottom:10px;">'. $conditionStr .'</table>';
                //$description = $itemSpecs.$description;
            }
            $description= $conditionStr .$description;
            return array("status" => "success", "message" => $description); 
            } else {
                //<div\s*class="ebaysummary">(.*)<div\s*id="inkfrog_crosspromo_bottom">
                //!--\s*description start\s*-->(<div>.*?<\/div>\s*)+<div>
                $description =  removeHTMLFormatting($description,  true);
                $pattern1 = '/!--\s*description start\s*-->(<div>.*?<\/div>\s*)+<div>/s';
                $res1 = preg_match_all($pattern1, $description, $result1);
                if ($res1) {
              //print_r($result1);
                $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                $description =  removeHTMLFormatting($description,  true);
                $existingItemJson = json_decode($item_specific, true);
                $conditionStr = "";
                $i = 1;
                foreach ($existingItemJson as $k => $v) {
                    if ($k != "ItemCompatibilityList") {
                        if($i == 1){
                           $conditionStr  =$conditionStr .'<tr><td width="50%"><strong>'.$k.':</strong> '.$v.'</td>';   
                            $i = 2;
                        } else if($i == 2){
                           $conditionStr  =$conditionStr .'<td width="50%"><strong>'.$k.':</strong> '.$v.'</td></tr>'; 
                            $i = 1;
                        }
                    }
                }
                if($i == 2){
                    $conditionStr  = $conditionStr .'<td width="50%"></td></tr>'; 
                }
                if($conditionStr != ''){
                    $conditionStr  = '<table cellspacing="5" cellpadding="5" style="width:100%;word-break: break-word;margin-bottom:10px;">'. $conditionStr .'</table>';
                    //$description = $itemSpecs.$description;
                }
                $description= $conditionStr .$description;
                return array("status" => "success", "message" => $description); 
                } else {
                    $description =  removeHTMLFormatting($description,  true);
                $existingItemJson = json_decode($item_specific, true);
                $conditionStr = "";
                $i = 1;
                foreach ($existingItemJson as $k => $v) {
                    if ($k != "ItemCompatibilityList") {
                        if($i == 1){
                           $conditionStr  =$conditionStr .'<tr><td width="50%"><strong>'.$k.':</strong> '.$v.'</td>';   
                            $i = 2;
                        } else if($i == 2){
                           $conditionStr  =$conditionStr .'<td width="50%"><strong>'.$k.':</strong> '.$v.'</td></tr>'; 
                            $i = 1;
                        }
                    }
                }
                if($i == 2){
                    $conditionStr  = $conditionStr .'<td width="50%"></td></tr>'; 
                }
                if($conditionStr != ''){
                    $conditionStr  = '<table cellspacing="5" cellpadding="5" style="width:100%;word-break: break-word;margin-bottom:10px;">'. $conditionStr .'</table>';
                    //$description = $itemSpecs.$description;
                }
                $description= $conditionStr .$description;
                return array("status" => "success", "message" => $description); 
                } 
            } 
    } 
        
       
 }

 if($user_id == 48964 || $user_id == 49053){
    
    $existingItemJson = json_decode($item_specific, true);
    $conditionStr = "";
    if ($condition_val != '' ) {
        $conditionStr = $conditionStr . "<tr><td width=\"50%\"><strong>Condition:</strong>".$condition_val."</td><td width=\"50%\"></td></tr>";
    }
    if ($condition_note != '' ) {
        $conditionStr = $conditionStr . "<tr><td width=\"50%\"><strong>Condition Notes:</strong> " . $condition_note . "</td><td width=\"50%\"></td></tr>";
    }
    $i = 1;
    foreach ($existingItemJson as $k => $v) {
        if ($k != "ItemCompatibilityList") {
            if($i == 1){
               $conditionStr  =$conditionStr .'<tr><td width="50%"><strong>'.$k.':</strong> '.$v.'</td>';   
                $i = 2;
            } else if($i == 2){
               $conditionStr  =$conditionStr .'<td width="50%"><strong>'.$k.':</strong> '.$v.'</td></tr>'; 
                $i = 1;
            }
        }
    }
    if($i == 2){
        $conditionStr  = $conditionStr .'<td width="50%"></td></tr>'; 
    }
    if($conditionStr != ''){
        $conditionStr  = '<table cellspacing="5" cellpadding="5" style="width:100%;word-break: break-word;margin-bottom:10px;border-top:1px;border-bottom:1px;">'. $conditionStr .'</table>';
        //$description = $itemSpecs.$description;
    }
    $str  = "";
    if($existingItemJson['ItemCompatibilityList']){
    $str = '<div class="comp"><table cellpadding="10" cellspacing="10" style="border-top:1px;border-bottom:1px;"><tr><td>Year</td><td>Make</td><td> Model</td><td>Trim</td><td>Engine</td><td>Notes</td></tr>';
    }
    foreach ($existingItemJson['ItemCompatibilityList'] as $v) {
        
        $str = $str . "<tr style=\'word-wrap: break-word\'> <td>" . $v["Year"] . "</td><td>" . $v["Make"] . "</td><td>" . $v["Model"] . "</td><td>" . $v["Trim"] . "</td><td>" . $v["Engine"] . "</td> <td>" . $v["CompatibilityNotes"] . "</td></tr>";
    }
    if( $str != ''){
       $str = $str . "</table></div>";
    }
    $description= "<div class=\"itemspec\">".$conditionStr."</div><div class=\"descnew\">" .$description."</div><div class=\"enddescnew\"></div>";
    return array("status" => "success", "message" => $description);
 }
if($user_id == 46751){ 
    echo "pankaj";
    $pattern1 = '/<div\s*id="description">(.*)<div\s*id="image-section">/sm';
    $res1 = preg_match_all($pattern1, $description, $result1);
    if ($res1) {
          print_r($result1);
          $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
          $description = removeHTMLFormatting($description, true);
          return array("status" => "success", "message" => $description); 
    } else {
        return array("status" => "fail", "message" => "RegEx not matched.");
       }
}
if($user_id == 47936){ 
    echo "pankaj";
    $pattern1 = '/<div\s*id="wpl_store_header">(.*)\S*<\/code><\/h4>/s';
    $res1 = preg_match_all($pattern1, $description, $result1);
    if ($res1) {
          print_r($result1);
          $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
          $description = removeHTMLFormatting($description, true);
          return array("status" => "success", "message" => $description); 
    } else{
        $pattern1 = '/<div\s+style="text-align:\s*center;">.*?<ul>.*?<\/ul>\s*<\/div>/sm';

        $res1 = preg_match_all($pattern1, $description, $result1);
        if ($res1) {
              print_r($result1);
              $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
              $description = removeHTMLFormatting($description, true);
              return array("status" => "success", "message" => $description); 
        }

        else{
            $pattern1 = '/<div\s*style="text-align:\s*center;">(.*)<p><br\s*\/><\/div>\s*<p>&nbsp;/sm';

        $res1 = preg_match_all($pattern1, $description, $result1);
        if ($res1) {
              print_r($result1);
              $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
              $description = removeHTMLFormatting($description, true);
              return array("status" => "success", "message" => $description); 
        }
        
    
    
    else {
        return array("status" => "fail", "message" => "RegEx not matched.");
       }
    }
    }
}

if($user_id == 48229){ 
    echo "pankaj";
    $itemSpecificStr = "";

    $item_specificObj = json_decode($item_specific, true);
    foreach ($item_specificObj as $k => $v) {
        $itemSpecificStr = $itemSpecificStr . '<p><strong>' . $k . ':</strong> ' . $v . '</p>';
    } 
    $description= "";
    $description=$itemSpecificStr.$description;
    print_r($description);

    return array("status" => "success", "message" => $description); 
    
}

if($user_id == 48043){ 
    echo "pankaj";

    $itemSpecificStr = "";

        $item_specificObj = json_decode($item_specific, true);
        foreach ($item_specificObj as $k => $v) {

            if ($k=='Manufacturer Part Number'){
                $itemSpecificStr = '<p><strong>Part Number : </strong> ' . $v . '</p>';
                break;
            }
            
        }  
        // $description = removeHTMLFormatting($description, true);
        $pattern1 = '/Product\s*Description<\/h3>(.*)<div\s*id="store-categories"/sm';
        $res1 = preg_match_all($pattern1, $description, $result1);
        if ($res1) {
              print_r($result1);
              $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
              $description = removeHTMLFormatting($description, true);
              $description  = $itemSpecificStr.$description;
              return array("status" => "success", "message" => $description); 
        }else{
            return array("status" => "fail", "message" => "RegEx not matched.");  
        }
        $description =$itemSpecificStr.$description;
         return array("status" => "success", "message" => $description); 
}
if($user_id == 46926){ 
    $itemSpecificStr = "";

        $item_specificObj = json_decode($item_specific, true);
        foreach ($item_specificObj as $k => $v) {
            $itemSpecificStr = $itemSpecificStr . '<p><strong>' . $k . ':</strong> ' . $v . '</p>';
        }        
    echo "pankaj";
    $pattern1 = '/<font\s*color="#000000"\s*style=""\s*size="4">(.*)<div\s*style="font-family:\s*Arial;\s*font-size:\s*large;"><br><\/div>/sm';
    $res1 = preg_match_all($pattern1, $description, $result1);
    if ($res1) {
          print_r($result1);
          $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
          $description = removeHTMLFormatting($description, true);
          $description  = $itemSpecificStr.$description;
          return array("status" => "success", "message" => $description); 
    } else {

    $pattern1 = '/<div\s*style="font-size:\s*14pt;\s*font-family:\s*Arial;"><b><font\s*size="4">(.*)<\/tbody><\/table>/sm';
    $res1 = preg_match_all($pattern1, $description, $result1);
    if ($res1) {
          print_r($result1);
          $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
          $description = removeHTMLFormatting($description, true);
          $description  = $itemSpecificStr.$description;
          return array("status" => "success", "message" => $description); 
    }
    else{
        $pattern1 = '/<font rwr="1" style="">(.*)<\/div><\/div><\/div><div[^>]+>/s';
    $res1 = preg_match_all($pattern1, $description, $result1);
    if ($res1) {
          print_r($result1);
          $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
          $description = removeHTMLFormatting($description, true);
          $description  = $itemSpecificStr.$description;
          return array("status" => "success", "message" => $description); 
    }
    else{
    $pattern1 = '/<font\s*rwr="1"\s*style="">(.*)<\/font><\/b><\/div><div[^>]+>/s';
    $res1 = preg_match_all($pattern1, $description, $result1);
    if ($res1) {
          print_r($result1);
          $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
          $description = removeHTMLFormatting($description, true);
          $description  = $itemSpecificStr.$description;
          return array("status" => "success", "message" => $description); 
    }

    else{
        $pattern1 = '/<b><br><\/b><\/span><\/font><\/div>(.*)<\/div><\/span><\/div>/s';
        $res1 = preg_match_all($pattern1, $description, $result1);
        if ($res1) {
              print_r($result1);
              $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
              $description = removeHTMLFormatting($description, true);
              $description  = $itemSpecificStr.$description;
              return array("status" => "success", "message" => $description); 
        }
    

       
    else{
        $pattern1 = '/<div style="text-align: center; ">(.*)<b>VAT\s*@/s';
        $res1 = preg_match_all($pattern1, $description, $result1);
        if ($res1) {
              print_r($result1);
              $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
              $description = removeHTMLFormatting($description, true);
              $description  = $itemSpecificStr.$description;
              return array("status" => "success", "message" => $description); 
        }
    

       
    else{
        $pattern1 = '/<font\s*rwr="1"\s*style="">(.*)<b>&nbsp;VAT\s*@/s';
        $res1 = preg_match_all($pattern1, $description, $result1);
        if ($res1) {
              print_r($result1);
              $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
              $description = removeHTMLFormatting($description, true);
              $description  = $itemSpecificStr.$description;
              return array("status" => "success", "message" => $description); 
        }

        else{
            $pattern1 = '/<font\s*color="#2663a6"\s*style=""\s*size="4">(.*)&nbsp;<\/font><\/div>/s';
            $res1 = preg_match_all($pattern1, $description, $result1);
            if ($res1) {
                  print_r($result1);
                  $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                  $description = removeHTMLFormatting($description, true);
                  $description  = $itemSpecificStr.$description;
                  return array("status" => "success", "message" => $description); 
            }
        else{
            $pattern1 = '/<p\s*align="left"\s*class="MsoNormal"\s*style="line-height:\s*normal;\s*text-align:\s*left;">(.*)<\/ul><\/div><\/div>/s';
            $res1 = preg_match_all($pattern1, $description, $result1);
            if ($res1) {
                  print_r($result1);
                  $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                  $description = removeHTMLFormatting($description, true);
                  $description  = $itemSpecificStr.$description;
                  return array("status" => "success", "message" => $description); 
            }

            else{
                $pattern1 = '/<font\s*size="6">(.*)<\/div><div><font\s*size="4">/s';
                $res1 = preg_match_all($pattern1, $description, $result1);
                if ($res1) {
                      print_r($result1);
                      $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                      $description = removeHTMLFormatting($description, true);
                      $description  = $itemSpecificStr.$description;
                      return array("status" => "success", "message" => $description); 
                }

                else{
                    $pattern1 = '/<\/a><\/div><font\s*rwr="1"\s*style="">(.*)<\/div><\/div><div>/s';
                    $res1 = preg_match_all($pattern1, $description, $result1);
                    if ($res1) {
                          print_r($result1);
                          $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                          $description = removeHTMLFormatting($description, true);
                          $description  = $itemSpecificStr.$description;
                          return array("status" => "success", "message" => $description); 
                    }
                
            

        
    

       
    else{
        return array("status" => "fail", "message" => "RegEx not matched.");

    


}
                }
            }
}
}

    }

    }

}
    }
    
    }
       }
}
//<div\s*style="font-size:\s*14pt;s*font-family:\s*Arial;"><b><font\s*size="4">(.*?)<\/table><\/div>
//<font\s*color="#000000"\s*style=""\s*size="4">(.*)<div\s*style="font-family:\s*Arial;\s*font-size:\s*large;"><br><\/div>

//<div\s*style="font-family:\s*Arial;"><b\s*style=""><font\s*size="4">(.*)<tbody\s*style="box-sizing:\s*inherit;">




if($user_id == 47876){ 
    echo "pankaj";
    $pattern1 = '/<div class="content__description mb0">(.*)<div id="gallery-viewer-component">/sm';
    $res1 = preg_match_all($pattern1, $description, $result1);
    if ($res1) {
          print_r($result1);
          $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
          $description = removeHTMLFormatting($description, true);
          return array("status" => "success", "message" => $description); 
    } else {
        return array("status" => "fail", "message" => "RegEx not matched.");
       }
}

//<h3><span class="glyphicon glyphicon-list-alt">(.*)<h3><span class="glyphicon glyphicon-time">



if($user_id == 48038){ 
    echo "pankaj";
    $description = removeHTMLFormatting($description, true);
    
    
    // Remove <h1> and <h2> tags with their content
    $description = str_replace('We have been selling on eBay for more than 10 years. Check our feedback with over 12,000 positive reviews.', '', $description);
    $description = preg_replace("/<h1[^>]*>.*?<\/h1>/is", "", $description);
    $description = preg_replace("/<h2[^>]*>.*?<\/h2>/is", "", $description);
    
    // Replace <ul> with <div> and </ul> with </div>
    $description = preg_replace("/<ul[^>]*>/i", "<div>", $description);
    $description = str_replace("</ul>", "</div>", $description);
    $description = preg_replace("/<table[^>]*>/i", "<div>", $description);
    $description = str_replace("</table>", "</div>", $description);
    $description = str_replace("<tbody>", "", $description);
    $description = str_replace("</tbody>", "", $description);
    
    // Replace <li> with <p> and </li> with </p>
    $description = str_replace("<li>", "<p>", $description);
    $description = str_replace("</li>", "</p><br>", $description);
    $description = str_replace("<tr>", "<p>", $description);
    $description = str_replace("</tr>", "</p><br>", $description);
    $description = str_replace("<td>", "<p>", $description);
    $description = str_replace("</td>", "</p>", $description);
    $description = str_replace("* All returns may include a 15% restocking fee.", "", $description);
    print_r("".$description);
    return array("status" => "success", "message" => $description); 
}




if($user_id == 47615){ 
    echo "pankaj";
    $pattern1 = '/<\/h3>(.*)<p\s*style="color:\s*red;">/sm';
    $res1 = preg_match_all($pattern1, $description, $result1);
    if ($res1) {
          print_r($result1);
          $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
          $description = removeHTMLFormatting($description, true);
          return array("status" => "success", "message" => $description); 
    } else {
        return array("status" => "fail", "message" => "RegEx not matched.");
       }
}

if($user_id == 47617){ 
    echo "pankaj";
    $pattern1 = '/<p\s*style="font-family:\s*Arial;">(.*)<\/span><\/p>/sm';
    $res1 = preg_match_all($pattern1, $description, $result1);
    if ($res1) {
          print_r($result1);
          $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
          $description = removeHTMLFormatting($description, true);
          return array("status" => "success", "message" => $description); 
    } else {
        $pattern1 = '/<h3>ITEM\s*DESCRIPTION<\/h3>(.*)<div\s*class="des_mobile">/sm';
    $res1 = preg_match_all($pattern1, $description, $result1);
    if ($res1) {
          print_r($result1);
          $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
          $description = removeHTMLFormatting($description, true);
          return array("status" => "success", "message" => $description); 
    } else {
        $pattern1 = '/<div\s*class="des"\s*[^>]+><h3>ITEM DESCRIPTION<\/h3>(.*)<div\s*class="des_mobile"\s*[^>]+>/sm';
        $res1 = preg_match_all($pattern1, $description, $result1);
        if ($res1) {
              print_r($result1);
              $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
              $description = removeHTMLFormatting($description, true);
              return array("status" => "success", "message" => $description); 
        } else {
            $pattern1 = '/<div\s*class="des"\s*[^>]+>\s*<h3\s*[^>]+>ITEM\s*DESCRIPTION<\/h3>(.*)<div\s*class="des_mobile"\s*[^>]+>/sm';
        $res1 = preg_match_all($pattern1, $description, $result1);
        if ($res1) {
              print_r($result1);
              $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
              $description = removeHTMLFormatting($description, true);
              return array("status" => "success", "message" => $description); 
        } else {
            $pattern1 = '/<h3>ITEM\s*DESCRIPTION<\/h3>\s*([\s\S]*?)\s*<div\s*class="des_mobile"[^>]*>/sm';
        $res1 = preg_match_all($pattern1, $description, $result1);
        if ($res1) {
              print_r($result1);
              $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
              $description = removeHTMLFormatting($description, true);
              return array("status" => "success", "message" => $description); 
        } else {
            return array("status" => "fail", "message" => "RegEx not matched.");
           }
           }
           }
       }
       }
}


if($user_id == 47377){ 
    echo "pankaj";
    $pattern1 = '/<title>(.*)\s*<table\s*class="table\s*table-responsive\s*table-borderless"\s*\s*[^>]+>/sm';
    $res1 = preg_match_all($pattern1, $description, $result1);
    if ($res1) {
          print_r($result1);
          $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
          $description = removeHTMLFormatting($description, true);
          return array("status" => "success", "message" => $description); 
    } else {
        return array("status" => "fail", "message" => "RegEx not matched.");
       }
}




    if($user_id == 43098){ 
            echo "pankaj";
            $pattern1 = '/<section id="content1">(.*)<\/section>\s<section id="content2">/sm';
            $res1 = preg_match_all($pattern1, $description, $result1);
            if ($res1) {
                  print_r($result1);
                  $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                  $description = removeHTMLFormatting($description, true);
                  return array("status" => "success", "message" => $description); 
            } else {
                return array("status" => "fail", "message" => "RegEx not matched.");
               }
    }
    if($user_id == 46695){ 
        
        $conditionStr ="";
       
        if($condition_val){
         $conditionStr = $conditionStr . "<p><strong>Condition:</strong> " . $condition_val . "</p>";
    
        }

        $description = $conditionStr . $description;
        // echo $description;
        return array("status" => "success", "message" => $description);
        
            
           
    }
    if($user_id == 46454){ 
        echo "pankaj";
        // $pattern1 = '/<font\s*rwr="1"\s*style="">(.*)<\/font><\/font><span\s*style="font-family:\s*Arial;">/sm';
        // $res1 = preg_match_all($pattern1, $description, $result1);
        // if ($res1) {
        //       print_r($result1);
        //       $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
              $description = removeHTMLFormatting($description, true);
              return array("status" => "success", "message" => $description); 
        // } else {
        //     return array("status" => "fail", "message" => "RegEx not matched.");
        //    }
}







    
    if($user_id == 46269){ 
        echo "pankaj";
        $pattern1 = '/<div id="element_553"\s*[^>]+>(.*)<\/div>\s*<div [^>]+><div id="elementz9ppf_563"\s*[^>]+>/s';
        $res1 = preg_match_all($pattern1, $description, $result1);
        if ($res1) {
              print_r($result1);
              $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
              $description = removeHTMLFormatting($description, true);
              return array("status" => "success", "message" => $description); 
        } else {
            $pattern1 = '/<div id="element_35"\s*[^>]+>(.*)<\/div>\s*<div [^>]+><div id="elm_15"\s*[^>]+>/s';
        $res1 = preg_match_all($pattern1, $description, $result1);
        if ($res1) {
              print_r($result1);
              $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
              $description = removeHTMLFormatting($description, true);
              return array("status" => "success", "message" => $description); 
        } else {
            return array("status" => "fail", "message" => "RegEx not matched.");
           }
           }
}

    
//     if($user_id == 46127){ 
//         echo "pankaj";
//         $pattern1 = '/<!--\s*CARD\s*DETAILS\s*BANNER\s*-->(.*)<\/tr><tr><td\s*class="orangebanner\s*orangebannerimage\s*fontbanner">/sm';
//         $res1 = preg_match_all($pattern1, $description, $result1);
//         if ($res1) {
//               print_r($result1);
//               $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
//               $description = removeHTMLFormatting($description, true);
//               return array("status" => "success", "message" => $description); 
//         } else {
//             return array("status" => "fail", "message" => "RegEx not matched.");
//            }
// }

    if($user_id == 45164){ 

        $pattern1 = '/<div\s*class="head-title">\s*(.*?)\s*<\/sd_description>/sm';
        $res1 = preg_match_all($pattern1, $description, $result1);
        if ($res1) {
              print_r($result1);
              $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
              $description = removeHTMLFormatting($description, true);
              return array("status" => "success", "message" => $description); 
        } else {
            $pattern1 = '/<div\s*class="container-ld-description">(.*)<\/div>\s*<\/div>\s*<\/sd_description>/sm';
                    $res1 = preg_match_all($pattern1, $description, $result1);
                    if ($res1) {
                          print_r($result1);
                          $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                          $description = removeHTMLFormatting($description, true);
                          return array("status" => "success", "message" => $description); 
                    } else {
                        return array("status" => "fail", "message" => "RegEx not matched.");
                       }
           }
    }


//     if($user_id == 45164){ 
//         echo "pankaj";
//         $pattern1 = '/<div\s*class="container-ld-description">(.*)<\/div>\s*<\/div>\s*<\/sd_description>/sm';
//         $res1 = preg_match_all($pattern1, $description, $result1);
//         if ($res1) {
//               print_r($result1);
//               $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
//               $description = removeHTMLFormatting($description, true);
//               return array("status" => "success", "message" => $description); 
//         } else {
//             return array("status" => "fail", "message" => "RegEx not matched.");
//            }
// }


    if($user_id == 44681){ 
            $pattern1 = '/<!--startcodistodescription\s*-\s*DO\s*NOT\s*REMOVE-->(.*)\s*<!--endcodistodescription\s*-\s*DO\s*NOT\s*REMOVE-->/s';
            $res1 = preg_match_all($pattern1, $description, $result1);
            if ($res1) {
                  print_r($result1);
                  $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                //   $description = removeHTMLFormatting($description, true);
                  return array("status" => "success", "message" => $description); 
            }
            
            else {
                
                $pattern1 = '/<div\s*style="display:\s*none;">(.*?)<\/div>/s';
                $res1 = preg_match_all($pattern1, $description, $result1);
                if ($res1) {
                      print_r($result1);
                      $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                    //   $description = removeHTMLFormatting($description, true);
                      return array("status" => "success", "message" => $description); 
                }
                else{
                return array("status" => "fail", "message" => "RegEx not matched.");
                }
            }
    }
        
    if($user_id == 45930){ 
        echo "sras...";
        $pattern1 = '/<div\s*id="productdesc"\s*class="section">(.*)<div\s*id="catgrid"\s*class="section">/s';
        $res1 = preg_match_all($pattern1, $description, $result1);
        if ($res1) {
              print_r($result1);
              $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
              $description = removeHTMLFormatting($description, true);
              return array("status" => "success", "message" => $description); 
        } else {
            $pattern1 = '/<div\s*id="productdesc"\s*class="section">(.*)/s';
            $res1 = preg_match_all($pattern1, $description, $result1);
            if ($res1) {
                  print_r($result1);
                  $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                  $description = removeHTMLFormatting($description, true);
                  return array("status" => "success", "message" => $description); 
            } else {
                return array("status" => "fail", "message" => "RegEx not matched.");
            }
        }
}

    if($user_id == 44584){ 
            echo "sras...";
            $pattern1 = '/<div\s*vocab[^>]+>(.*)<div\s*style=[^>]+>/s';
            $res1 = preg_match_all($pattern1, $description, $result1);
            if ($res1) {
                  print_r($result1);
                  $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                  $description = removeHTMLFormatting($description, true);
                  return array("status" => "success", "message" => $description); 
            } else {
                return array("status" => "fail", "message" => "RegEx not matched.");
            }
    }


    if($user_id == 44582){ 
       
                 

          $itemSpecificStr = "";
  
           $item_specificObj = json_decode($item_specific, true);
          foreach ($item_specificObj as $k => $v) {

               if($k == 'Brand'){

               }
                else{
                $itemSpecificStr = $itemSpecificStr . '<p><strong>' . $k . ':</strong> ' . $v . '</p>';
                }
          }  
          
          $pattern1 = '/<p\s*class="MsoNormal"\s*style="line-height: 250%;">(.*)<hr>/s';
          $res1 = preg_match_all($pattern1, $description, $result1);
          if ($res1) {
                print_r($result1);
                $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                $description = removeHTMLFormatting($description, true);
              
                $description =  $itemSpecificStr . $description;
          // echo $description;
          return array("status" => "success", "message" => $description);
                
          }
          
          else{
            //use this if regex failled
            //<p\s*class="MsoNormal"[^>]+>(.*)<div\s*class="MsoNormal"[^>]+>

            ///
            //regex for last 2 product use it according 
            //<div>(.*)<div>www.stefaniniarte.it<\/div>
            //<div\s*style="text-align:\s*left;">(.*)<div>www.stefaniniarte.it<\/div>


            
            
            $pattern1 = '/<\/div>\s*<br>(.*)<\/p>\s*<br>/s';
          $res1 = preg_match_all($pattern1, $description, $result1);
          if ($res1) {
                print_r($result1);
                $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                $description = removeHTMLFormatting($description, true);
              
                $description =  $itemSpecificStr . $description;
          // echo $description;
          return array("status" => "success", "message" => $description);   
          } 
          else{
            return array("status" => "fail", "message" => "RegEx not matched.");

          }

          }
  
       
          
        
       }

       if($user_id == 47855){ 
        //echo "sras...";
        $itemSpecificStr = "";
       if($item_specific){
        $item_specificObj = json_decode($item_specific, true);
       foreach ($item_specificObj as $k => $v) {

            $itemSpecificStr = $itemSpecificStr . '<p><strong>' . $k . ':</strong> ' . $v . '</p>';
        }  
        }
         $description = removeHTMLFormatting($description, true);
         $description = $itemSpecificStr . $description;
        return array("status" => "success", "message" => $description); 
        
        }
       
    if($user_id == 44979){ 
        echo "sras...";
        $pattern1 = '/<span\s*style="font-size:\s*16px;\s*font-family:\s*palatino;">(.*)\s*<\/span><\/span><\/li>/s';
        $res1 = preg_match_all($pattern1, $description, $result1);
        if ($res1) {
              print_r($result1);
              $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
              $description = removeHTMLFormatting($description, true);
              return array("status" => "success", "message" => $description); 
        } else {
            return array("status" => "fail", "message" => "RegEx not matched.");
        }
}


if( $user_id == 45199){
    $description = "";

    return array("status" => "success", "message" => $description); 
}

if( $user_id == 46127){
    $description = "";

    return array("status" => "success", "message" => $description); 
}


 
if($user_id == 44893){ 
    echo "sras...";
    $pattern1 = '/<section\s*class="panel\s*panel-default"\s*style="">(.*)<section\s*class="hidden-xs"\s*style="font-size:\s*14pt;\s*font-family:\s*Arial;">/s';
    $res1 = preg_match_all($pattern1, $description, $result1);
    if ($res1) {
          print_r($result1);
          $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
          $description = removeHTMLFormatting($description, true);
          return array("status" => "success", "message" => $description); 
        }else{
            $pattern1 = '/<!--\s*start\s*jar_description\s*-->(.*)<!--BULK\s*EDIT\s*ENDS\s*HERE-->/s';
            $res1 = preg_match_all($pattern1, $description, $result1);
            if ($res1) {
                  print_r($result1);
                  $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                  $description = removeHTMLFormatting($description, true);
                  return array("status" => "success", "message" => $description); 
            }else{
                $pattern1 = '/<section\s*class="panel\s*panel-default"\s*style="">(.*)<\/div><\/font><\/font>/s';
                $res1 = preg_match_all($pattern1, $description, $result1);
                if ($res1) {
                      print_r($result1);
                      $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                      $description = removeHTMLFormatting($description, true);
                      return array("status" => "success", "message" => $description);          
                }else{
                    $pattern1 = '/<!--BULK\s*EDIT\s*STARTS\s*HERE-->(.*)<!--\s*.flex-container\s*-->/s';
                    $res1 = preg_match_all($pattern1, $description, $result1);
                    if ($res1) {
                        print_r($result1);
                        $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                        $description = removeHTMLFormatting($description, true);
                        return array("status" => "success", "message" => $description);          
                    } else {
                        return array("status" => "fail", "message" => "RegEx not matched.");
                }
            }           
        }
    }
}

if($user_id == 45738){ 
    $conditionStr ="";

    if ($condition_note != '' && strlen($condition_note) > 0) {
        $conditionStr = $conditionStr . "<p><strong> " . $condition_note . "</strong></p>";
    }
            
    $pattern1 ='/Artikeldetails<\/h2>(.*)<!-- Artikelbeschreibung --><\/div>/sm';
    $res1 = preg_match_all($pattern1, $description, $result1);
   // echo $res1 ;
    print_r($result1);
    if ($res1) {
          print_r($result1);
          $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";

    }
    else{
     
        return array("status" => "fail", "message" => "RegEx not matched.");
    }
    $description = removeHTMLFormatting($description, true);
    $description = preg_replace('/<\/>/i', '', $description); 
     $description = $conditionStr . $description;
    // echo $description;
    return array("status" => "success", "message" => $description);

}


        if($user_id == 44361){ 
            $conditionStr ="";
            // if ($is_condition_val != '' && strlen($condition_val) > 0) {
                $conditionStr = $conditionStr . "<p><strong>Condition:</strong> " . $condition_val . "</p>";
        //  }
        if ($condition_note != '' && strlen($condition_note) > 0) {
                $conditionStr = $conditionStr . "<p><strong>Condition Description:</strong> " . $condition_note . "</p>";
        }
                    


            $description = $conditionStr . $description;
            // echo $description;
            return array("status" => "success", "message" => $description);

        }
        if($user_id == 48170){ 
           
            $conditionStr = "";
           
            $conditionStr = $conditionStr . "<p><strong>Condition:</strong> " . $condition_val . "</p>";
           
             $itemSpecificStr = "";
     
             $item_specificObj = json_decode($item_specific, true);
             foreach ($item_specificObj as $k => $v) {
                 $itemSpecificStr = $itemSpecificStr . '<p><strong>' . $k . ':</strong> ' . $v . '</p>';
             }        
             $description = removeHTMLFormatting($description, true);
             //print_r( $itemSpecificStr);
             $pattern1 = '/<div\s*id="layout__content">(.*)<div\s*id="gallery-viewer-component">/sm';
             $res1 = preg_match_all($pattern1, $description, $result1);
             if ($res1) {
                   //print_r($result1);
                   
                   $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                   $description = removeHTMLFormatting($description, true);
                 $description = $conditionStr . $itemSpecificStr . $description;

             // echo $description;
             return array("status" => "success", "message" => $description);
             }
             else{
                return array("status" => "fail", "message" => "RegEx not matched.");
             }

        }
        if($user_id == 47594){ 
           
            $conditionStr = "";
            // if ($is_condition_val != '' && strlen($condition_val) > 0) {
                 $conditionStr = $conditionStr . "<p><strong>Condition:</strong> " . $condition_val . "</p>";
           //  }
           if ($condition_note != '' && strlen($condition_note) > 0) {
                 $conditionStr = $conditionStr . "<p><strong>Condition Description:</strong> " . $condition_note . "</p>";
           }
                    
     
             $itemSpecificStr = "";
     
             $item_specificObj = json_decode($item_specific, true);
             foreach ($item_specificObj as $k => $v) {
                 $itemSpecificStr = $itemSpecificStr . '<p><strong>' . $k . ':</strong> ' . $v . '</p>';
             }        
     
             print_r( $itemSpecificStr);
     
             $description = $conditionStr . $itemSpecificStr . $description;
             // echo $description;
             return array("status" => "success", "message" => $description);

        }





    if($user_id == 45000){ 
        $conditionStr = "";
       // if ($is_condition_val != '' && strlen($condition_val) > 0) {
            $conditionStr = $conditionStr . "<p><strong>Condition:</strong> " . $condition_val . "</p>";
      //  }
      if ($condition_note != '' && strlen($condition_note) > 0) {
            $conditionStr = $conditionStr . "<p><strong>Condition Description:</strong> " . $condition_note . "</p>";
      }
               

        $itemSpecificStr = "";

        $item_specificObj = json_decode($item_specific, true);
        foreach ($item_specificObj as $k => $v) {
            $itemSpecificStr = $itemSpecificStr . '<p><strong>' . $k . ':</strong> ' . $v . '</p>';
        }        

        print_r( $itemSpecificStr);

        $description = $conditionStr . $itemSpecificStr . $description;
        // echo $description;
        return array("status" => "success", "message" => $description);

    }


    if($user_id == 46026){ 
        $conditionStr = "";
       // if ($is_condition_val != '' && strlen($condition_val) > 0) {
            $conditionStr = $conditionStr . "<p><strong>Condition:</strong> " . $condition_val . "</p>";
      //  }
      if ($condition_note != '' && strlen($condition_note) > 0) {
            $conditionStr = $conditionStr . "<p><strong>Condition Description:</strong> " . $condition_note . "</p>";
      }
               

        $itemSpecificStr = "";

        $item_specificObj = json_decode($item_specific, true);
        foreach ($item_specificObj as $k => $v) {
            $itemSpecificStr = $itemSpecificStr . '<p><strong>' . $k . ':</strong> ' . $v . '</p>';
        }        

        print_r( $itemSpecificStr);

        $description = $conditionStr . $itemSpecificStr . $description;
        // echo $description;
        return array("status" => "success", "message" => $description);

    }



    if($user_id == 44527){ 


        $conditionStr = "";
        
            $conditionStr = $conditionStr . "<p><strong>Condition:</strong> " . $condition_val . "</p>";
        
        
            //$conditionStr = $conditionStr . "<p><strong>Condition Description:</strong> " . $condition_note . "</p>";
        
        $itemSpecificStr = "";
        if ($is_item_specific) {
            $item_specificObj = json_decode($item_specific, true);
            foreach ($item_specificObj as $k => $v) {
                $itemSpecificStr = $itemSpecificStr . '<p><strong>' . $k . ':</strong> ' . $v . '</p>';
            }
        }
        echo "sras...";
        $pattern1 = '/(.*)<\/div><div><br><\/div><div><br><\/div><div>/s';
        $res1 = preg_match_all($pattern1, $description, $result1);
        if ($res1) {
              print_r($result1);
              $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
              $description = removeHTMLFormatting($description, true);
            //  return array("status" => "success", "message" => $description); 
        } else {

                
         $pattern1 ='/<a\s*name="_Hlk77240935">(.*)Please\s*do\s*your/s';
         $res1 = preg_match_all($pattern1, $description, $result1);
        // echo $res1 ;
         print_r($result1);
         if ($res1) {
              print_r($result1);
              $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
              $description = str_replace("<table>", "", $description);
              $description = str_replace("</table>", "", $description);
              $description = removeHTMLFormatting($description, true);
             //return array("status" => "success", "message" => $description); 
        }else{
            $pattern1 ='/<\/b><\/li><\/ul><div><b><br><\/b><\/div><div>(.*)<\/div><div><b><br><\/b><\/div><div><div style="">/s';
            $res1 = preg_match_all($pattern1, $description, $result1);
           // echo $res1 ;
            print_r($result1);
            if ($res1) {
                 print_r($result1);
                 $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                 $description = str_replace("<table>", "", $description);
                 $description = str_replace("</table>", "", $description);
                 $description = removeHTMLFormatting($description, true);
                //return array("status" => "success", "message" => $description); 
           }
        

        else{

            //
            return array("status" => "fail", "message" => "RegEx not matched.");

        }
    }
        }

        $description = $conditionStr . $itemSpecificStr . $description;
        echo $description;
        return array("status" => "success", "message" => $description);

    }

    
    if($user_id == 44664 ){ 

        $conditionStr = "";
        
            $conditionStr = $conditionStr . "<p><strong>Condition:</strong> " . $condition_val . "</p>";
        
            $itemSpecificStr = "";
            // if ($is_item_specific) {
                $item_specificObj = json_decode($item_specific, true);
                foreach ($item_specificObj as $k => $v) {
                    $itemSpecificStr = $itemSpecificStr . '<p><strong>' . $k . ':</strong> ' . $v . '</p>';
                }
            // }
            echo "sras...";      

            print_r( $itemSpecificStr);

        $description = $conditionStr . $itemSpecificStr . $description;

        // echo $description;

        return array("status" => "success", "message" => $description);

    }


    if($user_id == 44719 || $user_id == 45165 ){ 

        $conditionStr = "";
        if ($is_condition_val != '' && strlen($condition_val) > 0) {
            $conditionStr = $conditionStr . "<p><strong>Condition:</strong> " . $condition_val . "</p>";
        }
            $itemSpecificStr = "";
             if ($is_item_specific) {
                $item_specificObj = json_decode($item_specific, true);
                foreach ($item_specificObj as $k => $v) {
                    $itemSpecificStr = $itemSpecificStr . '<p><strong>' . $k . ':</strong> ' . $v . '</p>';
                }
             }
            echo "sras...";      

            print_r( $itemSpecificStr);

            $description = $conditionStr . $itemSpecificStr . $description;

        // echo $description;

        return array("status" => "success", "message" => $description);

    }



    

    if($user_id == 46021 ){
        
        $description ="";
        $conditionStr = "";
        if ( strlen($condition_val) > 0) {
            $conditionStr = $conditionStr . "<p><strong>Condition:</strong> " . $condition_val . "</p>";
        }
        if (strlen($condition_note) > 0) {
            $conditionStr = $conditionStr . "<p><strong>Condition Description:</strong> " . $condition_note . "</p>";
        }
        $item_specificObj = json_decode($item_specific, true);
        $itemSpecs = "";
        $i = 1;
        foreach($item_specificObj as $k => $v){
            if($i == 1){
                $itemSpecs = $itemSpecs.'<tr><td width="50%"><strong>'.$k.':</strong> '.$v.'</td>';   
                $i = 2;
            } else if($i == 2){
                $itemSpecs = $itemSpecs.'<td width="50%"><strong>'.$k.':</strong> '.$v.'</td></tr>'; 
                $i = 1;
            }
        }
        if($i == 2){
            $itemSpecs = $itemSpecs.'<td width="50%"></td></tr>'; 
        }
        if($itemSpecs != ''){
            $itemSpecs = '<table cellspacing="5" cellpadding="5" style="width:100%;word-break: break-word;">'.$itemSpecs.'</table>';
            //$description = $itemSpecs.$description;
        }
                
                
                
                
                // foreach ($item_specificObj as $k => $v) {
                //     $itemSpecificStr = $itemSpecificStr . '<p><strong>' . $k . ':</strong> ' . $v . '</p>';
                // }




            // }
            echo "sras...";      

            //print_r( $itemSpecificStr);

            $description = $conditionStr . $itemSpecs . $description;

        // echo $description;

        return array("status" => "success", "message" => $description);
    }
  
 


    if($user_id == 46873){ 
      
        $conditionStr = "";
        if (strlen($condition_val) > 0) {
            $conditionStr = $conditionStr . "<p><strong>Condition:</strong> " . $condition_val . "</p>";
        }
        if (strlen($condition_note) > 0) {
            $conditionStr = $conditionStr . "<p><strong>Condition Description:</strong> " . $condition_note . "</p>";
        }
      
       
        //<\/style>\s*<table>(.*)<img\ssrc[^>]+>
        $description = $conditionStr  . $description;
        echo $description;
        return array("status" => "success", "message" => $description);
      }

    if($user_id == 35540){ 
      
        $conditionStr = "";
        if ($is_condition_val && strlen($condition_val) > 0) {
            $conditionStr = $conditionStr . "<p><strong>Condition:</strong> " . $condition_val . "</p>";
        }
        if ($is_condition_note && strlen($condition_note) > 0) {
            $conditionStr = $conditionStr . "<p><strong>Condition Description:</strong> " . $condition_note . "</p>";
        }
        $itemSpecificStr = "";
        if ($is_item_specific) {
            $item_specificObj = json_decode($item_specific, true);
            foreach ($item_specificObj as $k => $v) {
                $itemSpecificStr = $itemSpecificStr . '<p><strong>' . $k . ':</strong> ' . $v . '</p>';
            }
        }
      
        $pattern1 ='/<\/style>\s*<table>(.*)<img\ssrc[^>]+>/sm';
        $res1 = preg_match_all($pattern1, $description, $result1);
       // echo $res1 ;
        print_r($result1);
        if ($res1) {
              print_r($result1);
              $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
              $description = str_replace("<table>", "", $description);
              $description = str_replace("</table>", "", $description);
              $description = removeHTMLFormatting($description, true);
             // return array("status" => "success", "message" => $description); 
        }else {


            
         $pattern1 ='/<span\sProperty="description">(.*)<\/div><\/span>/sm';
         $res1 = preg_match_all($pattern1, $description, $result1);
        // echo $res1 ;
         print_r($result1);
         if ($res1) {
              print_r($result1);
              $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
              $description = str_replace("<table>", "", $description);
              $description = str_replace("</table>", "", $description);
              $description = removeHTMLFormatting($description, true);
             //return array("status" => "success", "message" => $description); 
        }
         //  die(0);

         else{
            return array("status" => "fail", "message" => "RegEx not matched.");
        }
             
                 
         }


       
        //<\/style>\s*<table>(.*)<img\ssrc[^>]+>
        $description = $conditionStr . $itemSpecificStr . $description;
        echo $description;
        return array("status" => "success", "message" => $description);
      }





      if($user_id==42063){

      $desnew ='';
        $itemSpecificStr = "";
        if ($is_item_specific) {
            $item_specificObj = json_decode($item_specific, true);
            foreach ($item_specificObj as $k => $v) {
                $itemSpecificStr = $itemSpecificStr . '<p><strong>' . $k . ':</strong> ' . $v . '</p>';
            }
        }

        //print_r($description);
        $pattern1 ='/<p\s*class="MsoNormal"\s*style="">(.*)<br>/s';
        $res1 = preg_match_all($pattern1, $description, $result1);
       // echo $res1 ;
      //  print_r($result1);
        if ($res1) {
             // print_r($result1);

             echo"jhjhhhhjjhjh";
              $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
           
              $description  = removeHTMLFormatting($description, true);
             // return array("status" => "success", "message" => $description); 
        }else{
            $pattern = '/<p\s*style=[^>]*><span\s*style="font-size[^>]*>(.*?)<\/span><\/p>/s';

            // Perform the regex match
            if (preg_match($pattern, $description, $matches)) {
                // $matches[1] contains the captured content
                $description = $matches[1];
                
                // Output the captured content
               // echo $capturedContent;
            } 
        else{
            echo "not mathced";
            return array("status" => "fail", "message" => "RegEx not matched.");
        }
    }
    $desnew12  = $itemSpecificStr.$description ;
        //echo $desnew12;
      //die(0);

       
        return array("status" => "success", "message" => $description);


      


      }
      if($user_id == 42415 || $user_id == 43398){
        $itemSpecificStr = "";
        // if ($is_item_specific) {
            $item_specificObj = json_decode($item_specific, true);
            $itemSpecificStr ='<h2>Specifications</h2><ul>';
            foreach ($item_specificObj as $k => $v) {
                $itemSpecificStr = $itemSpecificStr . '<li><strong>' . $k . ':</strong> ' . $v . '</li>';
            }
        //}
        $itemSpecificStr = $itemSpecificStr . '</ul>';
        $descriptionnew ='<h2>Package Contents</h2>';
        $pattern1 ='/Package Contents<\/strong><\/p>(.*)<!--packaging,TAG_END-->/s';
        $res1 = preg_match_all($pattern1, $description, $result1);
       // echo $res1 ;
        print_r($result1);
        if ($res1) {
              print_r($result1);
              $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
              $pattern = '/(<ul>)(.*?)(<\/ul>)/s';
              $replacement = '<ol>$2</ol>';
              $description = preg_replace($pattern,'<ol>$2</ol>', $description);
              //$description = removeHTMLFormatting($description, true);
             // return array("status" => "success", "message" => $description); 
        }
        else{
            $pattern1 ='/Package\s*Contents(.*)<!--packaging,TAG_END-->/s';
            $res1 = preg_match_all($pattern1, $description, $result1);
           // echo $res1 ;
            print_r($result1);
            if ($res1) {
                  print_r($result1);
                  $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                  $pattern = '/(<ul>)(.*?)(<\/ul>)/s';
                  $replacement = '<ol>$2</ol>';
                  $description = preg_replace($pattern,'<ol>$2</ol>', $description);
                  //$description = removeHTMLFormatting($description, true);
                 // return array("status" => "success", "message" => $description); 
            }
            
            else{

                $pattern1 ='/<!--packaging,TAG_START-->(.*)<!--packaging,TAG_END-->/s';
            $res1 = preg_match_all($pattern1, $description, $result1);
           // echo $res1 ;
            print_r($result1);
            if ($res1) {
                  print_r($result1);
                  $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                  $pattern = '/(<ul>)(.*?)(<\/ul>)/s';
                  $replacement = '<ol>$2</ol>';
                  $description = preg_replace($pattern,'<ol>$2</ol>', $description);
                  //$description = removeHTMLFormatting($description, true);
                 // return array("status" => "success", "message" => $description); 
            }
            
            else{

                return array("status" => "fail", "message" => "RegEx not matched.");

            }

            }



            
        }

        $descriptionnew = $descriptionnew .$description;
        $description12 = $itemSpecificStr . $descriptionnew;
        echo $description;
        return array("status" => "success", "message" => $description12);


      


      }
      if($user_id == 42641){
        $itemSpecificStr = "";
         $conditionStr = "";
        //if ($is_condition_val && strlen($condition_val) > 0) {
            $conditionStr = $conditionStr . "<p><strong>Condition:</strong> " . $condition_val . "</p>";
           
        //}
        
        //if ($is_condition_note && strlen($condition_note) > 0) {
            $conditionStr = $conditionStr . "<p><strong>Condition Description:</strong> " . $condition_note . "</p>";
        //}
        //if ($is_item_specific) {
            $item_specificObj = json_decode($item_specific, true);
            //print_r(json_encode($item_specificObj));
            foreach ($item_specificObj as $k => $v) {
                $itemSpecificStr = $itemSpecificStr . '<p><strong>' . $k . ':</strong> ' . $v . '</p>';
            }
        //}


        $description12 = $conditionStr.$itemSpecificStr.$description;
           //$item_specificObj = json_decode($item_specific, true);
            //print_r(json_encode($description12));
        
        return array("status" => "success", "message" => $description12);


      


      }


      if($user_id == 42419){
        $itemSpecificStr = "";
         $conditionStr = "";
       
            $conditionStr = $conditionStr . "<p><strong>Condition:</strong> " . $condition_val . "</p>";
           
        
            $conditionStr = $conditionStr . "<p><strong>Condition Description:</strong> " . $condition_note . "</p>";
        
            $item_specificObj = json_decode($item_specific, true);
          
            foreach ($item_specificObj as $k => $v) {
                $itemSpecificStr = $itemSpecificStr . '<p><strong>' . $k . ':</strong> ' . $v . '</p>';
            }
       


       

        $description12 = $description.$itemSpecificStr;
          
        
        return array("status" => "success", "message" => $description12);


      


      }





      



      


      if($user_id == 43190){
        $itemSpecificStr = "";
         $conditionStr = "";
       
            $conditionStr = $conditionStr . "<p><strong>Condition:</strong> " . $condition_val . "</p>";
           
        
           $conditionStr = $conditionStr . "<p><strong>Condition Description:</strong> " . $condition_note . "</p>";
        
            $item_specificObj = json_decode($item_specific, true);
          
            foreach ($item_specificObj as $k => $v) {
                $itemSpecificStr = $itemSpecificStr . '<p><strong>' . $k . ':</strong> ' . $v . '</p>';
            }

        $description = $conditionStr.$itemSpecificStr;
          
        
        return array("status" => "success", "message" => $description);



      }


   


    




    if($user_id == 39593 ){ 
      
        $conditionStr = "";
        if ($is_condition_val && strlen($condition_val) > 0) {
            $conditionStr = $conditionStr . "<p><strong>Condition:</strong> " . $condition_val . "</p>";
        }
        if ($is_condition_note && strlen($condition_note) > 0) {
            $conditionStr = $conditionStr . "<p><strong>Condition Description:</strong> " . $condition_note . "</p>";
        }
        $itemSpecificStr = "";
        if ($is_item_specific) {
            $item_specificObj = json_decode($item_specific, true);
            foreach ($item_specificObj as $k => $v) {
                $itemSpecificStr = $itemSpecificStr . '<p><strong>' . $k . ':</strong> ' . $v . '</p>';
            }
        }
      
       
        $description = str_replace("size", "", $description);
        $description = str_replace("bold", "", $description);
        $description = str_replace("center", "", $description);
        $description = str_replace("<b>", "", $description);
        $description = str_replace("<b[^>]+>", "", $description);
        $description = str_replace("</b>", "", $description);
    
        
        
       
       
        $description = $conditionStr . $itemSpecificStr . $description;
        echo $description;
        return array("status" => "success", "message" => $description);

      
      
       
    
    }


    if($user_id == 43580 ){ 
      
        $conditionStr = "";
        //if ($is_condition_val && strlen($condition_val) > 0) {
            $conditionStr = $conditionStr . "<p><strong>Condition:</strong> " . $condition_val . "</p>";
        // }
        // if ($is_condition_note && strlen($condition_note) > 0) {
            $conditionStr = $conditionStr . "<p><strong>Condition Description:</strong> " . $condition_note . "</p>";
        //}
        $itemSpecificStr = "";
       // if ($is_item_specific) {
            $item_specificObj = json_decode($item_specific, true);
            foreach ($item_specificObj as $k => $v) {
                $itemSpecificStr = $itemSpecificStr . '<p><strong>' . $k . ':</strong> ' . $v . '</p>';
            }
        //}
      
       
        $description = str_replace("size", "", $description);
        $description = str_replace("bold", "", $description);
        $description = str_replace("center", "", $description);
        $description = str_replace("<b>", "", $description);
        $description = str_replace("<b[^>]+>", "", $description);
        $description = str_replace("</b>", "", $description);
    
        
        
       
       
        $description = $conditionStr . $itemSpecificStr . $description;
        echo $description;
        return array("status" => "success", "message" => $description);

      
      
       
    
    }


    // if($user_id == 42419){ 
      
    //     // $conditionStr = "";
    //     // //if ($is_condition_val && strlen($condition_val) > 0) {
    //     //     $conditionStr = $conditionStr . "<p><strong>Condition:</strong> " . $condition_val . "</p>";
    //     // //}
    //     // if ($is_condition_note && strlen($condition_note) > 0) {
    //     //     $conditionStr = $conditionStr . "<p><strong>Condition Description:</strong> " . $condition_note . "</p>";
    //     // }
    //     $itemSpecificStr = "";
    //     $itemSpecificJson = json_decode($item_specific, true);
        
    //     $i = 0;
    //     $itemSpecsStr = '<table class="desctable"><tr>';
    //     foreach ($itemSpecificJson as $k => $v) {
    //         $itemSpecsStr = $itemSpecsStr . "<td style='border: 2px solid'><strong>" . $k . "</strong>: " . $v . "</td>";
    //         $i++;
    //         if ($i == 2) {
    //             $itemSpecsStr = $itemSpecsStr . "</tr><tr>";
    //             $i = 0;
    //         }
    //     }
    //     if ($i == 0) {
    //         $itemSpecsStr = $itemSpecsStr . "<td></td><td></td>";
    //     }
    //     if ($i == 1) {
    //         $itemSpecsStr = $itemSpecsStr . "<td></td>";
    //     }
    //     $itemSpecsStr = $itemSpecsStr . "</tr></table>";
      
    //     $description12 = $itemSpecsStr.$description;
    //     // $description = str_replace("size", "", $description);
    //     // $description = str_replace("bold", "", $description);
    //     // $description = str_replace("center", "", $description);
    //     // $description = str_replace("<b>", "", $description);
    //     // $description = str_replace("<b[^>]+>", "", $description);
    //     // $description = str_replace("</b>", "", $description);
    
        
        
       
       
    //     // $description12 = $itemSpecificStr;
        
    //     //die()
    //     return array("status" => "success", "message" => $description12);

      
      
       
    
    // }

    if($user_id == 22571){ 
        $pattern1 = '/<font[^>]+><div[^>]+><font[^>]+>(.*)Shipping\sInformation/smU';
         $res1 = preg_match_all($pattern1, $description, $result1);
         if ($res1) {
               print_r($result1);
               $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
               $description = removeHTMLFormatting($description, true);
               return array("status" => "success", "message" => $description); 
         } else {


            $pattern1 ='/<font\s*rwr=[^>]*>(.*)<\/font><\/span><span\s*style=[^>]+>/sm';
            $res1 = preg_match_all($pattern1, $description, $result1);
            if ($res1) {
                  print_r($result1);
                  $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                  $description = removeHTMLFormatting($description, true);
                  return array("status" => "success", "message" => $description); 
            } else {
                return array("status" => "fail", "message" => "RegEx not matched.");
               }
            
           
          }
       
    }


    if($user_id == 38079){ 
        echo"helloooooo";
        echo $description;
        $pattern1 ='/<font\s*[^>]*>(.*)<font style="font-family:Arial">/sm';
         $res1 = preg_match_all($pattern1, $description, $result1);
        // echo $res1 ;
         print_r($result1);
         if ($res1) {
               print_r($result1);
               $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
               $description = removeHTMLFormatting($description, true);
               return array("status" => "success", "message" => $description); 
         } else {
          //  die(0);
            return array("status" => "fail", "message" => "RegEx not matched.");  
                  
          }
         
       
    }

    if($user_id == 43917){ 
        echo"helloooooo";
        echo $description;
        $pattern1 ='/<div\s*id="gs-designArea">(.*)<br><\/font><\/div><div>/sm';
         $res1 = preg_match_all($pattern1, $description, $result1);
        // echo $res1 ;
         print_r($result1);
         if ($res1) {
               print_r($result1);
               $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
               $description = removeHTMLFormatting($description, true);
               return array("status" => "success", "message" => $description); 
         }else{
            $pattern1 ='/<!--\s*title\s*field:\s*-->(.*)<div\s*style="box-sizing:\s*border-box;\s*font-size:\sinherit;">/sm';
            $res1 = preg_match_all($pattern1, $description, $result1);
           // echo $res1 ;
            print_r($result1);
            if ($res1) {
                  print_r($result1);
                  $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                  $description = removeHTMLFormatting($description, true);
                  return array("status" => "success", "message" => $description); 
            }else{
                $pattern1 ='/<div\s*id="gs-titleArea"\s*class="gs-titleFont">(.*)<div\s*style="font-size:\s*inherit;">/sm';
                $res1 = preg_match_all($pattern1, $description, $result1);
               // echo $res1 ;
                print_r($result1);
                if ($res1) {
                      print_r($result1);
                      $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                      $description = removeHTMLFormatting($description, true);
                      return array("status" => "success", "message" => $description); 
                }else{
                    $pattern1 ='/<div\s*style="display:\s*none">(.*)/sm';
                    $res1 = preg_match_all($pattern1, $description, $result1);
                   // echo $res1 ;
                    print_r($result1);
                    if ($res1) {
                          print_r($result1);
                          $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                          $description = removeHTMLFormatting($description, true);
                          return array("status" => "success", "message" => $description); 
                    }else{
                        $pattern1 ='/<!--\s*title\s*field:\s*-->(.*)<\/span><\/div><\/div><\/div><\/div>/sm';
                    $res1 = preg_match_all($pattern1, $description, $result1);
                   // echo $res1 ;
                    print_r($result1);
                    if ($res1) {
                          print_r($result1);
                          $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                          $description = removeHTMLFormatting($description, true);
                          return array("status" => "success", "message" => $description); 
                    }
                    
                
            
          else {
          //  die(0);
            return array("status" => "fail", "message" => "RegEx not matched.");  
               
            
          }
        }
        }
        }
        }
         
       
    }

    if($user_id == 42957){ 
        echo"helloooooo";
        echo $description;
        $pattern1 ='/<div\s*class="destitle">Product\s*Description<\/div>\s*(.*)\s*<\/div>\s*<\/div>\s*<div\s*class="tabbox">/sm';
         $res1 = preg_match_all($pattern1, $description, $result1);
        // echo $res1 ;
         print_r($result1);
         if ($res1) {
               print_r($result1);
               $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
               $description = removeHTMLFormatting($description, true);
               return array("status" => "success", "message" => $description); 
         } else {
          //  die(0);
            return array("status" => "fail", "message" => "RegEx not matched.");  
                  
          }
         
       
    }

    if($user_id == 37913){ 
        echo"helloooooo";
        echo $description;
        $pattern1 ='/<div\s*class="content__header">(.*)<p\s*align="center"\s*style=""><br><\/p>/sm';
         $res1 = preg_match_all($pattern1, $description, $result1);
        // echo $res1 ;
         print_r($result1);
         if ($res1) {
               print_r($result1);
               $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
               $description = removeHTMLFormatting($description, true);
               return array("status" => "success", "message" => $description); 
         } else{
            $pattern1 ='/<th\s*class="col\s*label\s*p-2\s*text-right\s*text-gray-700\s*font-normal\s*whitespace-nowrap"(.*)Theme<\/th>/sm';
            $res1 = preg_match_all($pattern1, $description, $result1);
           // echo $res1 ;
            print_r($result1);
            if ($res1) {
                  print_r($result1);
                  $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                  $description = removeHTMLFormatting($description, true);
                  return array("status" => "success", "message" => $description); 
            }else{
                $pattern1 ='/<font\s*style="vertical-align:\s*inherit;">(.*)<\/b><\/span><\/font><\/p>/sm';
                $res1 = preg_match_all($pattern1, $description, $result1);
               // echo $res1 ;
                print_r($result1);
                if ($res1) {
                      print_r($result1);
                      $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                      $description = removeHTMLFormatting($description, true);
                      return array("status" => "success", "message" => $description); 
                }
             
            
         
         
         else {
          //  die(0);
            return array("status" => "fail", "message" => "RegEx not matched.");  
                  
          }
        }
    }
       
    }

  

    

    if($user_id == 39373){ 
        // echo"helloooooo";
        // echo $description;
        $pattern1 ='/<div\s*id="description">(.*)<div\s*id="descriptionbase">/sm';
         $res1 = preg_match_all($pattern1, $description, $result1);
        // echo $res1 ;
         print_r($result1);
         if ($res1) {
               print_r($result1);
               $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
               $description = removeHTMLFormatting($description, true);
               return array("status" => "success", "message" => $description); 
         }else{
            $pattern1 ='/<div\sid="descriptioncontent">(.*)<\/span><\/li>/sm';
            $res1 = preg_match_all($pattern1, $description, $result1);
           // echo $res1 ;
            print_r($result1);
            if ($res1) {
                  print_r($result1);
                  $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                  $description = removeHTMLFormatting($description, true);
                  return array("status" => "success", "message" => $description); 
            }
         
         
         else {
          //  die(0);
            return array("status" => "fail", "message" => "RegEx not matched.");  
                  
          }
         } 
       
    }





    if($user_id == 40460){ 
        echo"helloooooo";
        echo $description;
        $pattern1 ='/<div class="section1">\s*(.*)<\/p>\s*<ul>\s*<li\s*class="x-hide">/sm';
         $res1 = preg_match_all($pattern1, $description, $result1);
        // echo $res1 ;
         print_r($result1);
         if ($res1) {
               print_r($result1);
               $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
               $description = removeHTMLFormatting($description, true);
               return array("status" => "success", "message" => $description); 
         } else {
          //  die(0);
            return array("status" => "fail", "message" => "RegEx not matched.");  
                  
          }
         
       
    }

    if($user_id == 40170){ 
        echo"helloooooo";
        //echo $description;
        $pattern1 ='/<u><span\s*style[^>]*>(.*)<\/span><\/font>/sm';
         $res1 = preg_match_all($pattern1, $description, $result1);
        // echo $res1 ;
         print_r($result1);
         if ($res1) {
               print_r($result1);
               $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
               $description = removeHTMLFormatting($description, true);
               return array("status" => "success", "message" => $description); 
         }else{
            $pattern1 ='/<span\s*style="font-size:18px;">(.*)<\/span><\/span><\/font>/sm';
            $res1 = preg_match_all($pattern1, $description, $result1);
           // echo $res1 ;
            print_r($result1);
            if ($res1) {
                  print_r($result1);
                  $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                  $description = removeHTMLFormatting($description, true);
                  return array("status" => "success", "message" => $description); 
            }else{
                
                    $pattern1 ='/<span\s*style="font-size:16px;"><\/span><\/strong><\/font>(.*)<\/font><\/span>\s*<\/p>/sm';
                    $res1 = preg_match_all($pattern1, $description, $result1);
                   // echo $res1 ;
                    print_r($result1);
                    if ($res1) {
                          print_r($result1);
                          $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                          $description = removeHTMLFormatting($description, true);
                          return array("status" => "success", "message" => $description); 
                    }else{
                        
                
                            $pattern1 ='/<span\s*style="font-size:16px;">(.*)<\/font><\/span>\s*<\/p>/sm';
                            $res1 = preg_match_all($pattern1, $description, $result1);
                           // echo $res1 ;
                            print_r($result1);
                            if ($res1) {
                                  print_r($result1);
                                  $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                                  $description = removeHTMLFormatting($description, true);
                                  return array("status" => "success", "message" => $description); 
                            }else{
                        
                
                                $pattern1 ='/<h2\s*style="font-family: Arial; font-size:\s*14pt;">(.*)<\/font><\/p>/sm';
                                $res1 = preg_match_all($pattern1, $description, $result1);
                               // echo $res1 ;
                                print_r($result1);
                                if ($res1) {
                                      print_r($result1);
                                      $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                                      $description = removeHTMLFormatting($description, true);
                                      return array("status" => "success", "message" => $description); 
                                }else{
                        
                
                                    $pattern1 ='/<h2\s*style="font-family: Arial; font-size: 14pt;">(.*)<div\s*class="container-fluid\s*services"\s* style="font-family: Arial;\s*font-size: 14pt;">/sm';
                                    $res1 = preg_match_all($pattern1, $description, $result1);
                                   // echo $res1 ;
                                    print_r($result1);
                                    if ($res1) {
                                          print_r($result1);
                                          $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                                          $description = removeHTMLFormatting($description, true);
                                          return array("status" => "success", "message" => $description); 
                                    }else{
                        
                
                                        $pattern1 ='/<div\s*class="title_type2_after bgcolor">&nbsp;<\/div>(.*)<\/span><\/span><\/font><\/font><\/font><\/p>/sm';
                                        $res1 = preg_match_all($pattern1, $description, $result1);
                                       // echo $res1 ;
                                        print_r($result1);
                                        if ($res1) {
                                              print_r($result1);
                                              $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                                              $description = removeHTMLFormatting($description, true);
                                              return array("status" => "success", "message" => $description); 
                                        }
                  
            
         
         
         
         else {
          //  die(0);
            return array("status" => "fail", "message" => "RegEx not matched.");  
                  
                }
            }
            }
            }
          }

            }
      }
       
    }



    

    






    
    if($user_id == 312){ 
        echo"helloooooo";
       // echo $description;
        $description = removeHTMLFormatting($description, true);
        $pattern1 ='/<div\sclass="discription">(.*)<\/p>\s<ul>/Us';
        $res1 = preg_match_all($pattern1, $description, $result1);
        // echo $res1 ;
         print_r($result1);
         if ($res1) {
               print_r($result1);
               $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
               $description = removeHTMLFormatting($description, true);
               return array("status" => "success", "message" => $description); 
         } else {
          //  die(0);
            return array("status" => "fail", "message" => "RegEx not matched.");  
                  
          }
         
       
    }

    if($user_id == 38981){ 
        echo"helloooooo";
       // echo $description;
      
        $pattern1 ='/<div\sstyle=[^>]*>(.*)<\/div>/Us';
        $res1 = preg_match_all($pattern1, $description, $result1);
        // echo $res1 ;
         print_r($result1);
         if ($res1) {
               print_r($result1);
               $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
               $description = removeHTMLFormatting($description, true);
               return array("status" => "success", "message" => $description); 
         } else {
          //  die(0);
            return array("status" => "fail", "message" => "RegEx not matched.");  
                  
          }
         
       
    }





    


    if($user_id == 39050)  {


        $pattern1 ='/<h3\sid="desc">(.*)<\/li><\/ul>/Us';
        $res1 = preg_match_all($pattern1, $description, $result1);
        // echo $res1 ;
         print_r($result1);
         if ($res1) {
               print_r($result1);
               $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
               $description = removeHTMLFormatting($description, true);
               return array("status" => "success", "message" => $description); 
         } else {
        
            return array("status" => "fail", "message" => "RegEx not matched.");  
                  
          }
    }

   







     
    if($user_id == 38981){ 
        echo"helloooooo";
    
        $pattern1 ='/<div\sstyle[^>]*>(.*)<\/table>\s<\/div>/s';
        $res1 = preg_match_all($pattern1, $description, $result1);
        // echo $res1 ;
         print_r($result1);
         if ($res1) {
               print_r($result1);
               $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
               $description = removeHTMLFormatting($description, true);
               return array("status" => "success", "message" => $description); 
         }
         
         else {
          //  die(0);
            return array("status" => "fail", "message" => "RegEx not matched.");  
                  
          }
         
       
    }


    if($user_id == 38055){ 
        echo"helloooooo";
    
        $pattern1 ='/<font\s*face="Arial"\s*size="4">(.*)<\/font><\/p>/s';
        $res1 = preg_match_all($pattern1, $description, $result1);
        // echo $res1 ;
         print_r($result1);
         if ($res1) {
               print_r($result1);
               $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
               $description = removeHTMLFormatting($description, true);
               return array("status" => "success", "message" => $description); 
         }else{

            $pattern1 ='/<div\s*class="item-details">(.*)<span\s*style="box-sizing:\s*border-box;">Material:&nbsp;<\/span>/s';
            $res1 = preg_match_all($pattern1, $description, $result1);
            // echo $res1 ;
             print_r($result1);
             if ($res1) {
                   print_r($result1);
                   $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                   $description = removeHTMLFormatting($description, true);
                   return array("status" => "success", "message" => $description); 
             }
             
         
         
         else {
          //  die(0);
            return array("status" => "fail", "message" => "RegEx not matched.");  
                  
          }
        }
         
       
    }

    


    
     
    if($user_id == 38302){ 
        echo"helloooooo";
    
        $pattern1 ='/<summary\s*style=""\s*property="description(.*)<\/span><\/div><\/div>/s';
        $res1 = preg_match_all($pattern1, $description, $result1);
        // echo $res1 ;
         print_r($result1);
         if ($res1) {
               print_r($result1);
               $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
               $description = removeHTMLFormatting($description, true);
               return array("status" => "success", "message" => $description); 
         }else{
            $pattern1 ='/<div\s*class="details-big\s*section">(.*)<\/p>\s*<\/div>\s*<\/aside>/s';
            $res1 = preg_match_all($pattern1, $description, $result1);
            // echo $res1 ;
             print_r($result1);
             if ($res1) {
                   print_r($result1);
                   $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                   $description = removeHTMLFormatting($description, true);
                   return array("status" => "success", "message" => $description); 
             }else{
                $pattern1 ='/<header\s*class="container-fluid"\s*id="gallery-and-summary"\s*style="">(.*)\s*<\/font>/s';
                $res1 = preg_match_all($pattern1, $description, $result1);
                // echo $res1 ;
                 print_r($result1);
                 if ($res1) {
                       print_r($result1);
                       $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                       $description = removeHTMLFormatting($description, true);
                       return array("status" => "success", "message" => $description); 
                 }
             
         
         
         else {
          //  die(0);
            return array("status" => "fail", "message" => "RegEx not matched.");  
                  
          }
        }
    }
       
    }



    





    if($user_id == 23330){ 
        echo"helloooooo";
       // echo $description;
        
        $pattern1 ='/<div\sclass="cpcm_part-info">(.*)<\/div>/Us';
        
         $res1 = preg_match_all($pattern1, $description, $result1);
        // echo $res1 ;
         print_r($result1);
         if ($res1) {
               print_r($result1);
               $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
               $description = removeHTMLFormatting($description, true);
              
         }  else {
            

            $pattern1 ='/<div\sclass="cpcm_part-info">(.*)<br><\/span><\/li>\s<\/ul>/m';
        
            $res1 = preg_match_all($pattern1, $description, $result1);
           // echo $res1 ;
            print_r($result1);
            if ($res1) {
                  print_r($result1);
                  $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                  $description = removeHTMLFormatting($description, true);
                 
            } 
            else {

                return array("status" => "fail", "message" => "RegEx not matched.");  
            }

          //  die(0);
                  
          }

          
				// if (substr(trim($item_specific), -1) === '{') {
				// 	// Add '}' character to complete the JSON data
				// 	$item_specific .= '}]}';
				// }
				// // //echo "item spec".json_encode($item_specific);
				
				// // //print_r(json_encode($item_specific,true));
				// $existingItemJson = json_decode($item_specific,true);
				// // //print_r("compa".json_encode($existingItemJson["ItemCompatibilityList"]));
				// if (!isset($existingItemJson["ItemCompatibilityList"])) {
				//    echo "desc not updated product_id".$row2['title'];
				// }
				// $itemCompatibilityListObj = $existingItemJson["ItemCompatibilityList"];
				// $str = '<table cellpadding="4" cellspacing="4"><tr><td>Year</td><td>Make</td><td>Model</td><td>Trim</td><td>Engine</td></tr>';
				// foreach ($itemCompatibilityListObj as $v) {
				// 	$str = $str . "<tr style=\'word-wrap: break-word\'><td>" . $v["Year"] . "</td><td>" . $v["Make"] . "</td><td>" . $v["Model"] . "</td> <td>" . $v["Trim"] . "</td><td>" . $v["Engine"] . "</td></tr>";
				// }
				// $str = $str . "</table>";


				// //print_r($str);
				
				
				// $description = $description . "<p><strong>Compatibility</strong></p>" . $str;

                // return array("status" => "success", "message" => $description); 

        
         
       
    }



    if($user_id ==  39709){ 
        echo"helloooooo";
        echo $description;
        $pattern1 ='/<div\sid="x-main-desc">(.*)<p\sclass[^>]*>/m';
         $res1 = preg_match_all($pattern1, $description, $result1);
        // echo $res1 ;
         print_r($result1);
         if ($res1) {
               print_r($result1);
               $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
               $description = removeHTMLFormatting($description, true);
               return array("status" => "success", "message" => $description); 
         } else {
          //  die(0);
            return array("status" => "fail", "message" => "RegEx not matched.");  
                 
        }
         
       
    }


   



    

    // if($user_id == 39373){ 
    //     echo"helloooooo";
    //     echo $description;
    //     $pattern1 ='/<div\sid="descriptioncontent">(.*)<\/span><\/li>/sm';
    //      $res1 = preg_match_all($pattern1, $description, $result1);
    //     // echo $res1 ;
    //      print_r($result1);
    //      if ($res1) {
    //            print_r($result1);
    //            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
    //            $description = removeHTMLFormatting($description, true);
    //            return array("status" => "success", "message" => $description); 
    //      } else {
    //       //  die(0);
    //         return array("status" => "fail", "message" => "RegEx not matched.");  
                  
    //       }
         
       
    // }



 
    if($user_id == 37913){ 
     
        $pattern1 ='/<div\s*class="description_title">(.*?)(?=<div\s*id="listing_wrap">)/sm';
        $res1 = preg_match_all($pattern1, $description, $result1);
        if ($res1) {
            echo"helloooooo";
            
            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";

            $description = removeHTMLFormatting($description, true);     
       } 
      else{
  
          $pattern1 ='/<div\s*class="details_title">(.*)<\/p><\/span>/sm';
          $res1 = preg_match_all($pattern1, $description, $result1);
         if ($res1) {
          //echo"helloooooo";
          
          $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";

          $description = removeHTMLFormatting($description, true);

          
          } 
         else{
             $pattern1 ='/<div\s*class="details_title">(.*)<\/font><\/span><\/p>/sm';
            $res1 = preg_match_all($pattern1, $description1, $result1);

            if ($res1) {
          //echo"helloooooo";
          
          $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";

          $description = removeHTMLFormatting($description, true);    
          } 
         else{
   
  	     return array("status" => "fail", "message" => "RegEx not matched.");  
         }
     
        
        }
       
   
        }
        $existingItemJson = json_decode($item_specific,true);

        // print_r("compa".json_encode($item_specific));
        // print_r("compa".json_encode($existingItemJso));
        // die();
        if ($existingItemJson === null) {
            // JSON data is invalid
            //die("Invalid JSON data");
        }
        
        // Step 2: Detect and correct the improperly closed last child
        $lastCommaPosition = strrpos($item_specific, ',');
        if ($lastCommaPosition !== false) {
            // Remove everything after the last comma to fix the JSON structure
            $fixedJsonData = substr($item_specific, 0, $lastCommaPosition) . '}]}';
            
            // Step 3: Remove the corrected last child
            $existingItemJson = json_decode($fixedJsonData, true);
            //print_r(json_encode($fixedJsonData));
            
            if ($existingItemJson === null && $item_specific != '' ) {
                return array("status" => "fail", "message" => "RegEx not matched.");  
            }
            
            // Now $existingItemJson contains the JSON object with the last child removed
            //print_r($existingItemJson);
        } 
        if (!isset($existingItemJson["ItemCompatibilityList"])) {
           echo "ItemCompatibilityList is empty updated product_id";
        }
        $itemCompatibilityListObj = $existingItemJson["ItemCompatibilityList"];
        $str = '<table cellpadding="10" cellspacing="10"><tr><td>Make</td><td>Model</td><td> Year</td><td>Variant</td><td>Chessis</td><td>Type</td><td>Engine</td></tr>';
        foreach ($itemCompatibilityListObj as $v) {
            $str = $str . "<tr style=\'word-wrap: break-word\'> <td>" . $v["Car Make"] . "</td><td>" . $v["Model"] . "</td><td>" . $v["Cars Year"] . "</td><td>" . $v["Variant"] . "</td><td>" . $v["BodyStyle"] . "</td> <td>" . $v["Cars Type"] . "</td><td>" . $v["Engine"] . "</td></tr>";
        }
        $str = $str . "</table>";


   
        // //$description ='';
        
        $description = $description . "<p><strong>Compatibility</strong></p>" . $str;

        return array("status" => "success", "message" => $description); 
    }


   

    if($user_id == 39221){ 
        $str ='';
      
        $pattern1 = '/<div\s*class="description_title">(.*?)(?=<div\s*id="listing_wrap">)/sm';
        $res1 = preg_match_all($pattern1, $description, $result1);
        if ($res1) {
              print_r($result1);
              $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
              $description = removeHTMLFormatting($description, true);
              return array("status" => "success", "message" => $description); 
        } else {

            $pattern1 = '/<div\s*class="details_title">(.*)<\/p><\/span>/sm';
            $res1 = preg_match_all($pattern1, $description, $result1);
            if ($res1) {
                  print_r($result1);
                  $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                  $description = removeHTMLFormatting($description, true);
                  return array("status" => "success", "message" => $description); 
            } else {
    
                $pattern1 = '/<div\s*class="details_title">(.*)<\/font><\/span><\/p>/sm';
                $res1 = preg_match_all($pattern1, $description, $result1);
                if ($res1) {
                      print_r($result1);
                      $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                      $description = removeHTMLFormatting($description, true);
                      return array("status" => "success", "message" => $description); 
                } else {
                 
                    $pattern1 = '/<div\s*id="left">(.*)<\/u><\/font>/sm';
                    $res1 = preg_match_all($pattern1, $description, $result1);
                    if ($res1) {
                          print_r($result1);
                          $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                          $description = removeHTMLFormatting($description, true);
                          return array("status" => "success", "message" => $description); 
                    } else {
              
                        $pattern1 = '/<div\s*class="details_title">(.*)<div\s*class="descr">/sm';
                        $res1 = preg_match_all($pattern1, $description, $result1);
                        if ($res1) {
                              print_r($result1);
                              $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                              $description = removeHTMLFormatting($description, true);
                              return array("status" => "success", "message" => $description); 
                        } else {
                
                            $pattern1 = '/<span\s*style=[^>]*>VW CRAFTER(.*)\s*<\/span><\/span><\/font>/sm';
                            $res1 = preg_match_all($pattern1, $description, $result1);
                            if ($res1) {
                                  print_r($result1);
                                  $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                                  $description = removeHTMLFormatting($description, true);
                                  return array("status" => "success", "message" => $description); 
                            } else {
                    
                                $pattern1 = '/<div\s*id="left">(.*)\s*<tbody>/sm';
                                $res1 = preg_match_all($pattern1, $description, $result1);
                                if ($res1) {
                                      print_r($result1);
                                      $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                                      $description = removeHTMLFormatting($description, true);
                                      return array("status" => "success", "message" => $description); 
                                } else {
                        
                                    $pattern1 = '/<div\s*class="description_title"\s*style=[^>]*>(.*)<div\s*id="listing_wrap">/sm';
                                    $res1 = preg_match_all($pattern1, $description, $result1);
                                    if ($res1) {
                                          print_r($result1);
                                          $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                                          $description = removeHTMLFormatting($description, true);
                                          return array("status" => "success", "message" => $description); 
                                    } else {
                            
                                        $pattern1 = '/<div\s*id="content">(.*)<\/strong><\/font><\/span>/sm';
                                        $res1 = preg_match_all($pattern1, $description, $result1);
                                        if ($res1) {
                                              print_r($result1);
                                              $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                                              $description = removeHTMLFormatting($description, true);
                                              return array("status" => "success", "message" => $description); 
                                        } else {

                                            $pattern1 = '/div align="center">(.*)<div>\s<\/div><p align="center"[^>]+>/sm';
                                            $res1 = preg_match_all($pattern1, $description, $result1);
                                            if ($res1) {
                                                  print_r($result1);
                                                  $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                                                  $description = removeHTMLFormatting($description, true);
                                                  return array("status" => "success", "message" => $description); 
                                            } else {
                                                 //<div align="center">(.*)<div>\s<\/div><p align="center"[^>]+>
                                                 return array("status" => "fail", "message" => "RegEx not matched."); 
                                            }

                                            
                                        }
                                    }
                                }
                            }
                        }
            
                    }
        
                }
            }
        }
        
        $existingItemJson = json_decode($item_specific,true);

        // print_r("compa".json_encode($item_specific));
        // print_r("compa".json_encode($existingItemJso));
        // die();
        if ($existingItemJson === null) {
            // JSON data is invalid
            //die("Invalid JSON data");
        }
        
        // Step 2: Detect and correct the improperly closed last child
        $lastCommaPosition = strrpos($item_specific, ',');
        if ($lastCommaPosition !== false) {
            // Remove everything after the last comma to fix the JSON structure
            $fixedJsonData = substr($item_specific, 0, $lastCommaPosition) . '}]}';
            
            // Step 3: Remove the corrected last child
            $existingItemJson = json_decode($fixedJsonData, true);
            //print_r(json_encode($fixedJsonData));
            
            if ($existingItemJson === null && $item_specific != '' ) {
               $fixedJsonData = substr($item_specific, 0, $lastCommaPosition) . '"}]}';
            
               // Step 3: Remove the corrected last child
               $existingItemJson = json_decode($fixedJsonData, true);
               // print_r(json_encode($fixedJsonData));
               // die();
               if ($existingItemJson === null && $item_specific != '' ) {
                return array("status" => "fail", "message" => "RegEx notcompatibility.");  
                }
            }
            
            // Now $existingItemJson contains the JSON object with the last child removed
            //print_r($existingItemJson);
        } 
        if (!isset($existingItemJson["ItemCompatibilityList"])) {
            return array("status" => "fail", "message" => "RegEx notcompatibility.");  
        }
        $itemCompatibilityListObj = $existingItemJson["ItemCompatibilityList"];
        $str='';
        if($itemCompatibilityListObj){
        $str = '<p><strong>Compatibility</strong></p><table cellpadding="10" cellspacing="10"><tr><td>Make</td><td>Model</td><td> Year</td><td>Variant</td><td>Chessis</td><td>Type</td><td>Engine</td></tr>';
        foreach ($itemCompatibilityListObj as $v) {
            $str = $str . "<tr style=\'word-wrap: break-word\'> <td>" . $v["Car Make"] . "</td><td>" . $v["Model"] . "</td><td>" . $v["Cars Year"] . "</td><td>" . $v["Variant"] . "</td><td>" . $v["BodyStyle"] . "</td> <td>" . $v["Cars Type"] . "</td><td>" . $v["Engine"] . "</td></tr>";
        }
        $str = $str . "</table>";
       
       }
      
       $description = $description. $str;
                  
       return array("status" => "success", "message" => $description);     
       
    }

    

   


    if($user_id == 37913){ 
        $pattern1 = '/<div class="col col-md-12 order-1">(.*)<div class="content__bulletpoints">/m';
         $res1 = preg_match_all($pattern1, $description, $result1);
         if ($res1) {
               print_r($result1);
               $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
               $description = removeHTMLFormatting($description, true);
               return array("status" => "success", "message" => $description); 
         } else {

            $pattern1 = '/<div class="item_desc"[^>]+>(.*)Delivery:/m';
         $res1 = preg_match_all($pattern1, $description, $result1);
         if ($res1) {
               print_r($result1);
               $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
               $description = removeHTMLFormatting($description, true);
               return array("status" => "success", "message" => $description); 
         } else {
            $pattern1 = '/<div class="col col-md-12 order-1">(.*)<\/p>/m';
            $res1 = preg_match_all($pattern1, $description, $result1);
            if ($res1) {
                  print_r($result1);
                  $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                  $description = removeHTMLFormatting($description, true);
                  return array("status" => "success", "message" => $description); 
            } else {
                $pattern1 = '/<div>(.*)<\/div><div><br><\/div><div>/m';
                $res1 = preg_match_all($pattern1, $description, $result1);
                if ($res1) {
                      print_r($result1);
                      $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                      $description = removeHTMLFormatting($description, true);
                      return array("status" => "success", "message" => $description); 
                } 
                else{


                return array("status" => "fail", "message" => "RegEx not matched.");  
                }
            }



            
         }


            //<div class="item_desc"[^>]+>(.*)Delivery:
           
                  
          }
       
    }




    if( $user_id == 38901){
        $description = "";

        return array("status" => "success", "message" => $description); 
    }

   
    if( $user_id == 38972){
        $description = "";

        return array("status" => "success", "message" => $description); 
    }

    

  
   

    if($user_id == 38161){ 
        $pattern1 = '/<div><font\sface=[^>]*>(.*)<\/font><\/b><\/div>/m';
         $res1 = preg_match_all($pattern1, $description, $result1);
         if ($res1) {
               print_r($result1);
               $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
               $description = removeHTMLFormatting($description, true);
               return array("status" => "success", "message" => $description); 
         } else {
            return array("status" => "fail", "message" => "RegEx not matched.");  
                  
           
          }
       
    }



    if($user_id == 37755){
        $description = removeHTMLFormatting($description, true);

        return array("status" => "success", "message" => $description); 
    }


    if($user_id == 42){
        $description = removeHTMLFormatting($description, true);

        return array("status" => "success", "message" => $description); 
    }



    
    if($user_id == 36873){
        $description = removeHTMLFormatting($description, true);

        return array("status" => "success", "message" => $description); 
    }





  
    
    if($user_id == 22548){
        $description = removeHTMLFormatting($description, true);

        return array("status" => "success", "message" => $description); 
    }
    
    // if($user_id == 22548){ 
    //     $pattern1 = '/<font rwr="1" style="">(.*)Decklist:<\/span>/sm';
    //      $res1 = preg_match_all($pattern1, $description, $result1);
    //      if ($res1) {
    //            print_r($result1);
    //            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
    //            $description = removeHTMLFormatting($description, true);
    //            return array("status" => "success", "message" => $description); 
    //      } else {
    //         return array("status" => "fail", "message" => "RegEx not matched.");  
                     
    //       }
       
    // }

    if ($user_id == 23595 ) {
        
		
		
        $conditionStr = "";
      echo " hello ";
       

      
        if (strlen($condition_val) > 0) {
            
            $conditionStr = $conditionStr . "<p><strong>Condition:</strong> " . $condition_val . "</p>";

            echo   $conditionStr ;
            
           
        }

        $itemSpecificStr = "";
        if ($is_item_specific) {
            $item_specificObj = json_decode($item_specific, true);
            foreach ($item_specificObj as $k => $v) {
                $itemSpecificStr = $itemSpecificStr . '<p><strong>' . $k . ':</strong> ' . $v . '</p>';
            }
        }

       

        $description = $conditionStr . $itemSpecificStr . $description;
        echo $description;
        
        return array("status" => "success", "message" => $description);


    }


    

    


     if ($user_id == 15132) {
        $itemSpecificJson = json_decode($item_specific, true);
        $itemSpecStr = "";
        if (strlen($condition_val) > 0) {
            $itemSpecStr = $itemSpecStr . "<p><strong>Condition:</strong> " . $condition_val . "</p>";
        }
        if (strlen($condition_note) > 0) {
            $itemSpecStr = $itemSpecStr . "<p><strong>Condition Description:</strong> " . $condition_note . "</p>";
        }
        foreach ($itemSpecificJson as $k => $v) {
            $itemSpecStr = $itemSpecStr . "<p><strong>" . $k . "</strong> " . $v . "</p>";
        }
        $description = $itemSpecStr;
        return array("status" => "success", "message" => $description);
    }

    if ($user_id == 15132) {
        $itemSpecStr = "";
        if (strlen($condition_val) > 0) {
            $itemSpecStr = $itemSpecStr . "<p><strong>Condition:</strong> " . $condition_val . "</p>";
        }

        if (strlen($condition_note) > 0) {
            $itemSpecStr = $itemSpecStr . "<p><strong>Condition Description:</strong> " . $condition_note . "</p>";
        }

        $description = $itemSpecStr;
        return array("status" => "success", "message" => $description);

    }


    if($user_id == 38727){ 
        $pattern1 = '/<div\s*class="desc-container">(.*)<div\s*class="pst-tab">/smU';
         $res1 = preg_match_all($pattern1, $description, $result1);
         if ($res1) {
               print_r($result1);
               $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
               $description = removeHTMLFormatting($description, true);
               return array("status" => "success", "message" => $description); 
         } else {
            return array("status" => "fail", "message" => "RegEx not matched.");  
                  
           
          }
       
    }
    if($user_id == 38144){ 
        $pattern1 = '/<div\s*data-element="productDescription">(.*)<div\s*id="Column_1"[^>]+>/smu';
         $res1 = preg_match_all($pattern1, $description, $result1);
         if ($res1) {
               print_r($result1);
               $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
               $description = removeHTMLFormatting($description, true);
               return array("status" => "success", "message" => $description); 
         } else {
            return array("status" => "fail", "message" => "RegEx not matched.");  
                  
           
          }
       
    }
    if($user_id == 18919){ 
        $pattern1 = '/<div class="listing_listingarea-box" id="right_box">(.*)<div class="home_banner">/smU';
         $res1 = preg_match_all($pattern1, $description, $result1);
         if ($res1) {
               print_r($result1);
               $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
               $description = removeHTMLFormatting($description, true);
               return array("status" => "success", "message" => $description); 
         } else {
            return array("status" => "fail", "message" => "RegEx not matched.");  
                  
           
          }
       
    }
    if($user_id == 20435){ 
        $description = removeHTMLFormatting($description, true);

        $pattern1 = '/<!--end-header-3dsellers.com-ld-html-->(.*?)<!--start-footer-3dsellers.com-ld-html-->/sm';
        $res1 = preg_match_all($pattern1, $description, $result1);
        if ($res1) {
            print_r($result1);
            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
            $description = removeHTMLFormatting($description, true);
            $description = preg_replace('/<div><\/div><div><br><\/div><div><br><\/div>/', ' ', $description);
            $description = preg_replace('/<div><font><br><\/div><div><font><br><\/div><div><font><br>/', ' ', $description);
            $description = preg_replace('/<div><font><br><\/div>/', ' ', $description);
            $description = preg_replace('/<div><font><br><\/div>/', ' ', $description);
            echo $description;
           
            return array("status" => "success", "message" => $description); 
        } else {
            return array("status" => "fail", "message" => "RegEx not matched.");  
        }
        
       
    }




    if($user_id == 37622){ 
        $pattern1 = '/<span\sproperty="description">([\s\S]*?)<\/p>\s*<\/span>/m';
        $res1 = preg_match_all($pattern1, $description, $result1);
          if($res1){
             
               $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
               $description = removeHTMLFormatting($description, true);
               return array("status" => "success", "message" => $description); 
         } else {
                  $pattern1 ='/<div id="tab-content1" class="tab-content">(.*)<div id="tab-content2" class="tab-content">/sm';
            $res1 = preg_match_all($pattern1, $description, $result1);
            if ($res1) {
                  print_r($result1);
                  $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                  $description = removeHTMLFormatting($description, true);
                  return array("status" => "success", "message" => $description); 
            } else {
            
                $pattern1 = '/<font rwr="1" style="">(.*)<\/ul>/ms';
                $res1 = preg_match_all($pattern1, $description, $result1);
                if ($res1) {
                      print_r($result1);
                      $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                } else {
                  
                     return array("status" => "fail", "message" => "RegEx not matched.");
                }
           }
            
           
          }
       
    }


   








    if($user_id == 18558){ 
        $pattern1 = '/<!--\s*START\s*DESCRIPTION\s*-->(.*)<!--\s*END\s*DESCRIPTION\s*-->/sm';
         $res1 = preg_match_all($pattern1, $description, $result1);
         if ($res1) {
               print_r($result1);
               $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
               $description = removeHTMLFormatting($description, true);
               return array("status" => "success", "message" => $description); 
         } else {



            $pattern1 = '/<section id="content1">(.*)<\/section><section id="content2">/sm';
            $res1 = preg_match_all($pattern1, $description, $result1);
            if ($res1) {
                  print_r($result1);
                  $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                  $description = removeHTMLFormatting($description, true);
                  return array("status" => "success", "message" => $description); 
            } else {
                return array("status" => "fail", "message" => "RegEx not matched.");
               }
            
           
          }
       
    }


    // if($user_id == 36620){ 
    //     $pattern1 = '/<body>(.*)<!-- MAIN DESCRIPTION -->/smiU';
    //      $res1 = preg_match_all($pattern1, $description, $result1);
    //      if ($res1) {
    //            print_r($result1);
    //            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
    //            $description = removeHTMLFormatting($description, true);
    //            return array("status" => "success", "message" => $description); 
    //      } else {

    //         $pattern1 = '/<div class="desboxmar">(.*)<\/div>/smiU';
    //         $res1 = preg_match_all($pattern1, $description, $result1);
    //         if ($res1) {
    //               print_r($result1);
    //               $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
    //               $description = removeHTMLFormatting($description, true);
    //               return array("status" => "success", "message" => $description); 
    //         } else {
    //             return array("status" => "fail", "message" => "RegEx not matched.");    
    //         }


            
           
    //       }
       
    // }


    if($user_id == 22571){ 
        $pattern1 = '/<b>Description:<\/b>(.*)shipping/smiU';
         $res1 = preg_match_all($pattern1, $description, $result1);
         if ($res1) {
               print_r($result1);
               $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
               $description = removeHTMLFormatting($description, true);
               return array("status" => "success", "message" => $description); 
         } else {
            return array("status" => "fail", "message" => "RegEx not matched.");
           
          }
       
    }


    if($user_id == 36167){ 
        $pattern1 = '/<div\sclass="short-desc-container"[^>]+>(.*)<section\sclass="product-description"\sstyle="">/sm';
         $res1 = preg_match_all($pattern1, $description, $result1);
         if ($res1) {
               print_r($result1);
               $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
               $description = removeHTMLFormatting($description, true);
               return array("status" => "success", "message" => $description); 
         } else {
            return array("status" => "fail", "message" => "RegEx not matched.");
           
          }
       
    }
    

    if($user_id == 38333){ 
        $pattern1 = '/<div\s*style=[^>]*>(.*)<\/div><span\s*style=[^>]*>/smU';
         $res1 = preg_match_all($pattern1, $description, $result1);
         if ($res1) {
               print_r($result1);
               $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
               $description = removeHTMLFormatting($description, true);
               return array("status" => "success", "message" => $description); 
         } else {
            return array("status" => "fail", "message" => "RegEx not matched.");
           
          }
       
    }


    

    if($user_id == 38301){ 
        $pattern1 = '/<td\s*class="price_bg_black">(.*)<!--\s*tab\s*contect\s*start\s*here\s*-->/smu';
         $res1 = preg_match_all($pattern1, $description, $result1);
         if ($res1) {
               print_r($result1);
               $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
               $description = removeHTMLFormatting($description, true);
               return array("status" => "success", "message" => $description); 
         } else {
            return array("status" => "fail", "message" => "RegEx not matched.");
           
          }
       
    }

    if($user_id == 37879){ 
        $pattern1 = '/<div id="element_58" [^>]*>(.*)<\/strong><\/p>/smU';
         $res1 = preg_match_all($pattern1, $description, $result1);
         if ($res1) {
               print_r($result1);
               $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
               $description = removeHTMLFormatting($description, true);
               return array("status" => "success", "message" => $description); 
         }else{
            $pattern1 = '/<div\s*style="text-align:\s*center;"><font\s*face="Arial"\s*size="4">(.*)<\/font><\/b><\/div><div\s*style="text-align:\s*center;">/smU';
            $res1 = preg_match_all($pattern1, $description, $result1);
            if ($res1) {
                  print_r($result1);
                  $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                  $description = removeHTMLFormatting($description, true);
                  return array("status" => "success", "message" => $description); 
            }else{
                $pattern1 = '/<div\s*id="cl-raw-description"\s*data-exclude-css-removal="true"\s*style="display:\s*none;"><div(.*)/smU';
                $res1 = preg_match_all($pattern1, $description, $result1);
                if ($res1) {
                      print_r($result1);
                      $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                      $description = removeHTMLFormatting($description, true);
                      return array("status" => "success", "message" => $description); 
                }
            
         
         
         else {
            return array("status" => "fail", "message" => "RegEx not matched.");
           
          }
        }
    }
       
    }


   



    
    if($user_id == 17047){ 
        $pattern1 = '/<p\sclass=\"MsoNormal\"[^>]+>(.*)<\/tbody>/sm';
         $res1 = preg_match_all($pattern1, $description, $result1);
         if ($res1) {
               print_r($result1);
               $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
               $description = removeHTMLFormatting($description, true);
               return array("status" => "success", "message" => $description); 
         } else {
            return array("status" => "fail", "message" => "RegEx not matched.");
           
          }
       
    }


    if($user_id == 22582){ 
        echo $is_item_specific;   
       
           $item_specificObj = json_decode($item_specific, true);
           foreach ($item_specificObj as $k => $v) {
               $itemSpecificStr = $itemSpecificStr . '<p><strong>' . $k . ':</strong> ' . $v . '</p>';
           }
     
       $description = $itemSpecificStr ;
    }


    if($user_id == 36428){ 
        echo $is_item_specific;   
       
           $item_specificObj = json_decode($item_specific, true);
           foreach ($item_specificObj as $k => $v) {
               $itemSpecificStr = $itemSpecificStr . '<p><strong>' . $k . ':</strong> ' . $v . '</p>';
           }
     
       $description = $itemSpecificStr ;
    }






    if($user_id == 37566 || $user_id == 40013 || $user_id == 45915  ){ 
        
        $description ="";

        return array("status" => "success", "message" => $description);  
    }

    if($user_id == 17540){ 
        echo $is_item_specific;   
       
           $item_specificObj = json_decode($item_specific, true);
           foreach ($item_specificObj as $k => $v) {
               $itemSpecificStr = $itemSpecificStr . '<p><strong>' . $k . ':</strong> ' . $v . '</p>';
           }
           $description ="";
       $description = $itemSpecificStr.$description ;
       return array("status" => "success", "message" => $description);  
       
    }


    if($user_id == 35283){ 
        $pattern1 = '/<div class="col-lg-12\stitle"[^>]+>(.*)Package\sIncludes/sm';
         $res1 = preg_match_all($pattern1, $description, $result1);
         if ($res1) {
               print_r($result1);
               $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
         } else {
            return array("status" => "fail", "message" => "RegEx not matched.");
           
          }
       
    }

    if($user_id == 16761){ 
        $pattern1 = '/<div id="descriptioncontent">(.*)<\/froodescription>/sm';
         $res1 = preg_match_all($pattern1, $description, $result1);
         if ($res1) {
               print_r($result1);
               $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
               $description = removeHTMLFormatting($description, true);
         } else {

            $pattern1 = '/<span\sstyle[^>]*>(.*)<\/div><\/font>/sm';
            $res1 = preg_match_all($pattern1, $description, $result1);
            if ($res1) {
                  print_r($result1);
                  $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                  $description = removeHTMLFormatting($description, true);
            } else {
  

            $pattern1 = '/<div\s*style[^>]*>(.*)<\/div><\/font>/sm';
             $res1 = preg_match_all($pattern1, $description, $result1);
            if ($res1){
                print_r($result1);
                $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                $description = removeHTMLFormatting($description, true);

            }else{

                $pattern1 = '/<div [^>]*>(.*)<font [^>]*>Also, we will not declare the value of purchased merchandise to be below what the buyer paid, nor will we declare merchandise to be a gift to avoid duties.<\/font><\/div>/sm';
                $res1 = preg_match_all($pattern1, $description, $result1);
               if ($res1){
                   print_r($result1);
                   $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                   $description = removeHTMLFormatting($description, true);
   
               }else{
            

                $pattern1 = '/<div>(.*)<\/div>/sm';
                $res1 = preg_match_all($pattern1, $description, $result1);
               if ($res1){
                   print_r($result1);
                   $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                   $description = removeHTMLFormatting($description, true);
   
               }else{
                return array("status" => "fail", "message" => "RegEx not matched.");
               }

   
                   
               }
            }

            }



            // <div id="description">(.*)<\/froodescription>
           
           
          }
       
    }


    
    if($user_id == 13881){ 
        $pattern1 = '/<!--\sfroo\sdescription\s-->(.*)<\/froodescription>/sm';
         $res1 = preg_match_all($pattern1, $description, $result1);
         if ($res1) {
               print_r($result1);
               $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
         } else {

            $pattern1 = '/<div\stypeof="Product"[^>]+>(.*)Further\sDetails/ms';
            $res1 = preg_match_all($pattern1, $description, $result1);
            if ($res1) {
                  print_r($result1);
                  $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
            }
            
    
            else {
                 //<!--CSG INDICATOR END--> (.*)<!-- Begin: AuctivaCounter\s-->
                 $pattern1 = '/div id="descriptioncontent">(.*)/ms';
                 $res1 = preg_match_all($pattern1, $description, $result1);
                 if ($res1) {
                       print_r($result1);
                       $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                 } else {
                     $pattern1='/<div><b>(.*)<\/div>/ms';
                     $res1 = preg_match_all($pattern1, $description, $result1);
                     if ($res1) {
                        print_r($result1);
                        $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                  }
                  else{
                   
                    $pattern1='/<font\s[^>]*>(.*)<\/font>/ms';
                    $res1 = preg_match_all($pattern1, $description, $result1);
                    if ($res1) {
                       print_r($result1);
                       $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                 }
                 else{
                    return array("status" => "fail", "message" => "RegEx not matched.");
                 }
                     }
                
                 }
          
            }             

          }
    
     $description = removeHTMLFormatting($description, true);
     //$description = str_replace("<br>", "", $description);
     //$description = str_replace("</br>", "", $description);
      return array("status" => "success", "message" => $description);  
    }


    if($user_id == 21929  ){ 
     $pattern1 = '/<\/header>(.*)<footer>/sm';
        $res1 = preg_match_all($pattern1, $description, $result1);
        if ($res1) {
              print_r($result1);
              $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
        } else {
            $pattern1 = '/<\/header>(.*)<footer[^>]+>/sm';
            $res1 = preg_match_all($pattern1, $description, $result1);
        if ($res1) {
              print_r($result1);
              $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
        } else {
            $pattern1 = '/<p\sstyle=[^>](.*)<\/p>/sm';
            $res1 = preg_match_all($pattern1, $description, $result1);
        if ($res1) {
              print_r($result1);
              $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
        }
       else{
        return array("status" => "fail", "message" => "RegEx not matched.");
       }    

         
        }
           
          }
        echo $is_item_specific;   
        print_r ($item_specific);
       // exit(0);
         
            $item_specificObj = json_decode($item_specific, true);
            foreach ($item_specificObj as $k => $v) {
                $itemSpecificStr = $itemSpecificStr . '<p><strong>' . $k . ':</strong> ' . $v . '</p>';
            }
    
            $str = "";
            $existingItemJson = json_decode($item_specific, true);
            if (isset($existingItemJson["ItemCompatibilityList"])) {
                $itemCompatibilityListObj = $existingItemJson["ItemCompatibilityList"];
               
            }

             $description = $itemSpecificStr .$str. $description;
             return array("status" => "success", "message" => $description);  
    
    }
    




    
   
   
    if($user_id == 35708){ 
        $pattern1 = '/<!--CSG\s*INDICATOR\s*END-->(.*)<!--\s*Begin:\s*AuctivaCounter\s*-->/sm';
         $res1 = preg_match_all($pattern1, $description, $result1);
         if ($res1) {
               print_r($result1);
               $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
         }
         
         else {

            
            $pattern1 = '/<!--DESCRIPTION\s*Start-->(.*)<!--Decription\s*Body-->/sm';
            $res1 = preg_match_all($pattern1, $description, $result1);
            if ($res1) {
                  print_r($result1);
                  $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
            } else {
   
               
                $pattern1 = '/<div\s*id="bg"\s*style="">(.*)<\/span><\/strong><br><br>/sm';
                $res1 = preg_match_all($pattern1, $description, $result1);
                if ($res1) {
                      print_r($result1);
                      $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                } else {
       
                   
                    $pattern1 = '/<div\s*id="csgPlatformLinkDiv">(.*)<!--\s*Begin:\s*AuctivaCounter\s*-->/sm';
                    $res1 = preg_match_all($pattern1, $description, $result1);
                    if ($res1) {
                          print_r($result1);
                          $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                    } else {
           
                       
                                 //<!--CSG INDICATOR END--> (.*)<!-- Begin: AuctivaCounter\s-->
                                 return array("status" => "fail", "message" => "RegEx not matched.");
                            }
                   
                        }
               
                    }
                 }
           
     $description = removeHTMLFormatting($description, true);
     //$description = str_replace("<br>", "", $description);
     //$description = str_replace("</br>", "", $description);
      return array("status" => "success", "message" => $description);  
    }


    if($user_id == 23028 || $user_id == 35606 ){ 
    //     $pattern1 = '/<div[^>]+typeof="Product"[^>]+>(.*)<h2[^>]+>/sm';
    //      $res1 = preg_match_all($pattern1, $description, $result1);
    //      if ($res1) {
    //            print_r($result1);
    //            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
    //      } else {

    //         $pattern1 = '/<div\stypeof="Product"[^>]+>(.*)Further\sDetails/ms';
    //         $res1 = preg_match_all($pattern1, $description, $result1);
    //         if ($res1) {
    //               print_r($result1);
    //               $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
    //         } else {
    //              //<!--CSG INDICATOR END--> (.*)<!-- Begin: AuctivaCounter\s-->
    //              return array("status" => "fail", "message" => "RegEx not matched.");
    //         }
    //       }
    //  $description = removeHTMLFormatting($description, true);
    //  //$description = str_replace("<br>", "", $description);
    //  //$description = str_replace("</br>", "", $description);
    $description = "";
    //return array("status" => "success", "message" => $description);
      return array("status" => "success", "message" => $description);  
    
    }

    if ($user_id == 13434) {
        echo "pankaj";
       // exit; 
        echo $description;
        echo "                ";
        $pattern1 = '/(.*)<p[^>]*>\s*<font[^>]*>\s*------------------------------------------/ismU';
        $res1 = preg_match_all($pattern1, $description, $result1);
        if ($res1) {
            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
        } else {
            // return array("status" => "fail", "message" => "RegEx not matched.");
        }
        $description = removeHTMLFormatting($description, true);
        echo $description;
        $pattern1 = '/(.*)<p[^>]*>\s*<em>\s*<u>\s*Remise\s*en\s*mains\s*propres/smU';
        $res1 = preg_match_all($pattern1, $description, $result1);
        if ($res1) {
            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
        }
        $conditionStr = "";
        if (strlen($condition_val) > 0) {
            $conditionStr = $conditionStr . "<p><strong>Condition:</strong> " . $condition_val . "</p>";
        }
        if (strlen($condition_note) > 0) {
            $conditionStr = $conditionStr . "<p>" . $condition_note . "</p>";
        }
        $description = $conditionStr . $description;
        return array("status" => "success", "message" => $description);
    }

   


        
   
 


    if($user_id == 22876){ 
        $pattern1 = '/<!--\s*Start Description AucLister\s*-->(.*)<!--\s*End Description AucLister --\s*>/sm';
         $res1 = preg_match_all($pattern1, $description, $result1);
         if ($res1) {
               print_r($result1);
               $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
         } else {

            $pattern1 = '/<!--CSG\s*INDICATOR\s*END-->(.*)<!--\s*Begin:\s*AuctivaCounter\s-->/ms';
            $res1 = preg_match_all($pattern1, $description, $result1);
            if ($res1) {
                  print_r($result1);
                  $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
            } else {
                 //<!--CSG INDICATOR END--> (.*)<!-- Begin: AuctivaCounter\s-->
                 return array("status" => "fail", "message" => "RegEx not matched.");
            }
          }
      return array("status" => "success", "message" => $description);  
    }
    if($user_id == 21221){ 
        $pattern1 = '/<h2>(.*)<div\s*class=\"tab_container.+\">/sm';
         $res1 = preg_match_all($pattern1, $description, $result1);
         if ($res1) {
               print_r($result1);
               $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
         } else {

          
                 //<!--CSG INDICATOR END--> (.*)<!-- Begin: AuctivaCounter\s-->
                 return array("status" => "fail", "message" => "RegEx not matched.");
            
          }
      return array("status" => "success", "message" => $description);  
    }

    if($user_id == 22933){ 
       

        echo "pankaj".$is_condition_val;
        echo "narang ".$condition_val;
        $conditionStr = "";
        if ($is_condition_val && strlen($condition_val) > 0) {
            echo "pankaj".$condition_val;
            $conditionStr = $conditionStr . "<p><strong>Condition:</strong> " . $condition_val . "</p>";
        }
        
        
        
        $description =  $conditionStr.removeHTMLFormatting($description, false); 
        print_r($description);
        //exit(0);
        return array("status" => "success", "message" => $description);  
      }

   /* if($user_id == 22476){ 
       
      //$description = str_replace("white", "grey", $description);
      return array("status" => "success", "message" => $description);  
    }
   
   */
   
    if($user_id == 19563 || $user_id == 22840   ) {
        // applied filter based on DB settings
        $conditionStr = "";
        echo "pankaj";
        echo $condition_note;
        echo $condition_val;
        echo $is_condition_note;
        echo $is_condition_val;
        //exit(0);

        if (strlen($condition_val) > 0) {
            $conditionStr = $conditionStr . "<p><strong>Condition:</strong> " . $condition_val . "</p>";
            echo " condition found    ".$conditionStr;
        }
        if (strlen($condition_note) > 0) {
            $conditionStr = $conditionStr . "<p><strong>Condition Description:</strong> " . $condition_note . "</p>";
        }
        $itemSpecificStr = "";
        if ($is_item_specific) {
            $item_specificObj = json_decode($item_specific, true);
            foreach ($item_specificObj as $k => $v) {
                $itemSpecificStr = $itemSpecificStr . '<p><strong>' . $k . ':</strong> ' . $v . '</p>';
            }
        }
        $description =  $description.$conditionStr . $itemSpecificStr ;
        echo "no condition matched";
        return array("status" => "success", "message" => $description);
    } 
    if($user_id == 22481  ) {
        
              
        $pattern1 = '/(.*)<o:p><\/o:p><\/font>/sm';
        $res1 = preg_match_all($pattern1, $description, $result1);
              
        
        if ($res1) {
              print_r($result1);
              $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
              $description =  $description.$itemSpecificStr ;
         
               } else {


                $pattern1 = '/(.*)<o:p><\/o:p>.*SHIPPING:/sm';
        $res1 = preg_match_all($pattern1, $description, $result1);
        if ($res1) {
              print_r($result1);
              $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
              $description =  $description.$itemSpecificStr ;
         
               } else {

                $pattern1 = '/(.*)<o:p><\/o:p><\/span>/sm';
                $res1 = preg_match_all($pattern1, $description, $result1);
              
        
        if ($res1) {
              print_r($result1);
              $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
              $description =  $description.$itemSpecificStr ;
         
               } else {

                $pattern1 = '/<sd_description>(.*)<\/sd_description>/sm';
                $res1 = preg_match_all($pattern1, $description, $result1);
                      
        if ($res1) {
              print_r($result1);
              $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
              $description =  $description.$itemSpecificStr ;
         
               } else {

                $pattern1 = '/<sd_description>(.*)<\/div>/sm';
                $res1 = preg_match_all($pattern1, $description, $result1);
                      
        if ($res1) {
              print_r($result1);
              $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
              $description =  $description.$itemSpecificStr ;
         
               } else {
                $pattern1 = '/<div\s*class="mb3"\s*[^>]*>(.*)/sm';
                $res1 = preg_match_all($pattern1, $description, $result1);                  
        if ($res1) {
              print_r($result1);
              $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
              $description =  $description.$itemSpecificStr ;
         
               } else {
                $pattern1 = '/<span\sstyle[^>]*>(.*)<\/span>/sm';
                $res1 = preg_match_all($pattern1, $description, $result1);                  
        if ($res1) {
              print_r($result1);
              $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
              $description =  $description.$itemSpecificStr ;
         
               }
             else{
                $pattern1 = '/Fuchsia\sEdition(.*)<\/em><\/p>/sm';
                $res1 = preg_match_all($pattern1, $description, $result1);                  
        if ($res1) {
              print_r($result1);
              $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
              $description =  $description.$itemSpecificStr ;
         
               }

                else{

               
                    return array("status" => "fail", "message" => "RegEx not matched.");
                }
            }

        }
               }


              
               }


                //<sd_description>(.*)<\/div>
               
               }


               }

        
                //<!--CSG INDICATOR END--> (.*)<!-- Begin: AuctivaCounter\s-->
                
           
         }
       
        $itemSpecificStr = "";
        if ($is_item_specific) {
            $item_specificObj = json_decode($item_specific, true);
            foreach ($item_specificObj as $k => $v) {
                $itemSpecificStr = $itemSpecificStr . '<p><strong>' . $k . ':</strong> ' . $v . '</p>';
            }
        }
        $description = removeHTMLFormatting($description, false); 
        $description =  $description. $itemSpecificStr ;
        echo "no condition matched";
        return array("status" => "success", "message" => $description);
    } 


    if($user_id == 22192 || $user_id == 21596 ){ 
        $conditionStr = "";
        if (strlen($condition_val) > 0) {
            $conditionStr = $conditionStr . "<p><strong>Condition:</strong> " . $condition_val . "</p>";
        }
        if (strlen($condition_note) > 0) {
            $conditionStr = $conditionStr . "<p><strong>Condition Description:</strong> " . $condition_note . "</p>";
        }
        $description = $conditionStr . $description;
        
        if($user_id == 21596)
        { 
        $description = removeHTMLFormatting($description, false); 
        }
        return array("status" => "success", "message" => $description);
    }

    if($user_id == 21489){ 
        echo "pankaj 21489";
        $conditionStr = "";
        if (strlen($condition_val) > 0) {
            $conditionStr = $conditionStr . "<p><strong>Condition:</strong> " . $condition_val . "</p>";
        }
        if (strlen($condition_note) > 0) {
            $conditionStr = $conditionStr . "<p><strong>Condition Description:</strong> " . $condition_note . "</p>";
        }
       
        $pattern1 = '/<span\s[^>]*>(.*)<\/span>/sm';
        $res1 = preg_match_all($pattern1, $description, $result1);
        if ($res1) {
              // print_r($result1);
              $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
              $description = $conditionStr . $description;
              return array("status" => "success", "message" => $description);
        } else {

            $pattern1 = '/<\/div>(.*)<br><center>/sm';
            $res1 = preg_match_all($pattern1, $description, $result1);
            if ($res1) {
                  // print_r($result1);
                  $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                  $description = $conditionStr . $description;
                  return array("status" => "success", "message" => $description);
            } else {

                $pattern1 = '/<\/div>(.*)<\/center>/sm';
                $res1 = preg_match_all($pattern1, $description, $result1);
                if ($res1) {
                      // print_r($result1);
                      $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                      $description = $conditionStr . $description;
                      return array("status" => "success", "message" => $description);
                } else {
                    return array("status" => "fail", "message" => $description);
                }


                
            }


            //
             
        } 
        
       
    }


if($user_id == 17611){ 
    $pattern1 = '/<table class="desc_table"[^>]+>(.*)<\/table>/sm';
     $res1 = preg_match_all($pattern1, $description, $result1);
     if ($res1) {
           print_r($result1);
           $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
     } else {
         return array("status" => "fail", "message" => "RegEx not matched.");
      }
  return array("status" => "success", "message" => $description);  
}



if($user_id == 21506){ 
 $pattern1 = '/Item Description<\/h2>\s<div class="block_content" style="">(.*)<div\sclass="block"\sstyle="font-family:\sArial;\sfont-size: 14pt;">\s<h2\sclass="block_title">Shipping\s<\/h2>/sm';
        $res1 = preg_match_all($pattern1, $description, $result1);
        if ($res1) {
              print_r($result1);
              $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
        } else {

           $pattern1 = '/Item Description(.*)Shipping/sm';
        $res1 = preg_match_all($pattern1, $description, $result1);
        if ($res1) {
              print_r($result1);
              $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
        } else {
              return array("status" => "fail", "message" => "RegEx not matched.");
             }

            //Item Description(.*)Shipping
           
         }
     return array("status" => "success", "message" => $description);  
}


if($user_id == 20835){ 
   // print_r($result1);
                   // $description = removeHTMLFormatting($description1, false); 
              //print_r($description); 
             
     $pattern1 = '/<!-- froo item title -->(.*)About\sUs/sm';
        $res1 = preg_match_all($pattern1, $description, $result1);
        if ($res1) {
              //print_r($result1);
              $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
             
        } else {
            $pattern1 = '/<div\s*class="creditlink-wrapper">(.*)About\sUs/sm';
            $res1 = preg_match_all($pattern1, $description, $result1);
            if ($res1) {
                  //print_r($result1);
                  $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                 
            } else {
                $pattern1 = '/<div\s*class="creditlink-wrapper" [^>]*>(.*)About\sUs/sm';
                $res1 = preg_match_all($pattern1, $description, $result1);
                if ($res1) {
                      //print_r($result1);
                      $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                     
                } else {
                    $pattern1 = '/<div\s*class="creditlink-wrapper">(.*)About/sm';
                    $res1 = preg_match_all($pattern1, $description, $result1);
                    if ($res1) {
                          //print_r($result1);
                          $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                         
                    } else {
                        return array("status" => "fail", "message" => "RegEx not matched.");
                     }
                 }
             }
         }
        // exit(0); 
     return array("status" => "success", "message" => $description); 
     //exit(0); 
}
if($user_id == 20417 ){ 
    $itemSpecificStr = "";
      if ($is_item_specific) {
          $item_specificObj = json_decode($item_specific, true);
          foreach ($item_specificObj as $k => $v) {
              $itemSpecificStr = $itemSpecificStr . '<p><strong>' . $k . ':</strong> ' . $v . '</p>';
          }
      }
      $description = $itemSpecificStr;
      return array("status" => "success", "message" => $description);
}


if( $user_id == 21582 ){ 

    $pattern1 = '/Product\sDescription(.*)<\/i><\/p>\s<\/div>\s<div[^>]*>/smi';
    $res1 = preg_match_all($pattern1, $description, $result1);
    if ($res1) {
          print_r($result1);
          $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
    } else {
        
        $pattern1 = '/<div\sclass="textimgl-START\stextimgbg-START\stextimgl_intab">(.*)<\/p>\s<\/div>/U';
        $res1 = preg_match_all($pattern1, $description, $result1);
        if ($res1) {
              print_r($result1);
              $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
        }else{
                $pattern1 = '/<div\sclass="colleft\scol-xs-12">(.*)<\/div>\s<br>/sU';
        $res1 = preg_match_all($pattern1, $description, $result1);
        if ($res1) {
              print_r($result1);
              $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
        }

            
            else{
            return array("status" => "fail", "message" => "RegEx not matched.");
            }
        }

      
     }


    
    $itemSpecificStr = "";
      if ($is_item_specific) {
          $item_specificObj = json_decode($item_specific, true);
          foreach ($item_specificObj as $k => $v) {
              $itemSpecificStr = $itemSpecificStr . '<p><strong>' . $k . ':</strong> ' . $v . '</p>';
          }
      }



      $description = $itemSpecificStr.$description;
      return array("status" => "success", "message" => $description);
}


if( $user_id == 23396){ 

    $description = removeHTMLFormatting($description, false);
    $itemSpecificStr = "";
      if ($is_item_specific) {
           
          $item_specificObj = json_decode($item_specific, true);
          foreach ($item_specificObj as $k => $v) {
              $itemSpecificStr = $itemSpecificStr . '<p><strong>' . $k . ':</strong> ' . $v . '</p>';
          }
      }
      
      $description = $itemSpecs.$description;
            
      
      return array("status" => "success", "message" => $description);
}





if($user_id == 20252){ 
       $pattern1 = '/<div\sstyle="font-family:Arial;\sfont-size:14pt;\spadding:1px;">(.*)<div\sstyle="font-family:Arial;\sfont-size:14pt; padding:5px;">/sm';
        $res1 = preg_match_all($pattern1, $description, $result1);
        if ($res1) {
              print_r($result1);
              $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
        } else {
            $pattern1 = '/<div\s*class="element"\s*[^>]*>(.*)<div\s*class="col-12\s*col-md-12 \s*element"\s*[^>]*>/sm';
        $res1 = preg_match_all($pattern1, $description, $result1);
        if ($res1) {
              print_r($result1);
              $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
        } else {
              $pattern1 = '/(.*)About\sUs/sm';
        $res1 = preg_match_all($pattern1, $description, $result1);
        if ($res1) {
              print_r($result1);
              $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
        } else {
                //
            return array("status" => "fail", "message" => "RegEx not matched.");
            }
                   


         
         }
         }
     //exit(0);
     return array("status" => "success", "message" => $description);  
}




if($user_id == 20698){ 
       $pattern1 = '/<!-- GALLERY BOX END -->(.*)<!-- ITEM DESCRIPTION END -->/sm';
        $res1 = preg_match_all($pattern1, $description, $result1);
        if ($res1) {
              print_r($result1);
              $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
        } else {
            $pattern1 = '/<!--\s*ITEM\s*DESCRIPTION\s*START\s*-->(.*)<!--\s*ITEM\s*DESCRIPTION\s*END\s*-->/sm';
            $res1 = preg_match_all($pattern1, $description, $result1);
            if ($res1) {
                  print_r($result1);
                  $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
            } else {
                return array("status" => "fail", "message" => "RegEx not matched.");
             }
         }
     return array("status" => "success", "message" => $description);  
}



if($user_id == 20554){ 
      $itemSpecificStr = "";
        if ($is_item_specific) {
            $item_specificObj = json_decode($item_specific, true);
            foreach ($item_specificObj as $k => $v) {
                $itemSpecificStr = $itemSpecificStr . '<p><strong>' . $k . ':</strong> ' . $v . '</p>';
            }
        }
        $description = $itemSpecificStr;
        return array("status" => "success", "message" => $description);
}

if($user_id == 20190){ 
       $pattern1 = '/<div class="right-panel">(.*)<div\sclass="box\sanimate\sscale\scontent"\sid="content2">/sm';
        $res1 = preg_match_all($pattern1, $description, $result1);
        if ($res1) {
              print_r($result1);
              $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
        } else {
           $pattern1 = '/<div\sclass=\'right-panel\'>(.*)<div\sstyle="text-align:\scenter;">/sm';
        $res1 = preg_match_all($pattern1, $description, $result1);
        if ($res1) {
              print_r($result1);
              $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
        } else {
               return array("status" => "fail", "message" => "RegEx not matched.");
            } 
 
         }
       return array("status" => "success", "message" => $description);  
}

if($user_id == 20128){ 
       $pattern1 = '/<sd_description>(.*)<\/sd_description>/sm';
        $res1 = preg_match_all($pattern1, $description, $result1);
        if ($res1) {
              print_r($result1);
              $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
        } else {
             $pattern1 = '/<!--startcodistodescription - DO NOT REMOVE-->(.*)<!--endcodistodescription - DO NOT REMOVE-->/sm';
        $res1 = preg_match_all($pattern1, $description, $result1);
        if ($res1) {
              print_r($result1);
              $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
        } else {
                return array("status" => "fail", "message" => "RegEx not matched.");
               }       

            
         }
     return array("status" => "success", "message" => $description);  
}


if($user_id == 20243){ 
       $pattern1 = '/<p\sclass="MsoNormal"\sstyle="text-align:justify">&nbsp;<o:p><\/o:p><\/p>(.*)PAYMENT:/sm';
        $res1 = preg_match_all($pattern1, $description, $result1);
        if ($res1) {
              print_r($result1);
              $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
        } else {
            $pattern1 = '/<span>(.*)<\/span>/sm';
            $res1 = preg_match_all($pattern1, $description, $result1);
            if ($res1) {
                  print_r($result1);
                  $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
            } else {
               
                $pattern1 = '/<p>(.*)<\/p>/sm';
                $res1 = preg_match_all($pattern1, $description, $result1);
                if ($res1) {
                      print_r($result1);
                      $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                }else{

                
                return array("status" => "fail", "message" => "RegEx not matched.");
                }
             }
         }
     return array("status" => "success", "message" => $description);  
}



if($user_id == 20101 || $user_id == 20803 || $user_id == 22569 || $user_id == 23291 ) {
        $description = " ";        
        return array("status" => "success", "message" => $description);
    }

if($user_id == 19995  )
 {
        $itemSpecs = "";
        $itemSpecificObj = json_decode($item_specific, true);		
        foreach($itemSpecificObj as $k => $v){
        	$itemSpecs = $itemSpecs.'<p><strong>'.$k.':</strong> '.$v.'</p>';
        }
        $description = $itemSpecs.$description;        
        return array("status" => "success", "message" => $description);
}

 if($user_id == 36606 )
    {
           $itemSpecs = "";
           $itemSpecificObj = json_decode($item_specific, true);		
           foreach($itemSpecificObj as $k => $v){
               $itemSpecs = $itemSpecs.'<p><strong>'.$k.':</strong> '.$v.'</p>';
           }$description ="";
           $description = $itemSpecs.$description;        
           return array("status" => "success", "message" => $description);
 }

 
   if( $user_id == 38382 ){

    $itemSpecs = "";
           $itemSpecificObj = json_decode($item_specific, true);		
           foreach($itemSpecificObj as $k => $v){
               $itemSpecs = $itemSpecs.'<p><strong>'.$k.':</strong> '.$v.'</p>';
           }
    $description ="";
    $description = $itemSpecs.$description;        
       return array("status" => "success", "message" => $description);
   }






if(  $user_id ==  42198 ||    $user_id ==  35390 || $user_id == 20152  ||  $user_id == 20356 ||  $user_id == 20611 ||  $user_id == 20828  || $user_id ==  18428 || $user_id ==  17609 || $user_id ==  21123 || $user_id ==   21176  || $user_id == 23537 || $user_id == 22476 || $user_id == 38696 || $user_id == 39844 || $user_id == 43700 ) 

 {
  print_r($description);  
  //exit(0);
$item_specificObj = json_decode($item_specific, true);
        $itemSpecs = '<tr style=\'word-wrap: break-word;\' ><td width="50%"><strong>Condition:</strong> '.$condition_val.'</td>';
        $i = 2;
        foreach($item_specificObj as $k => $v){
            if($i == 1){
                $itemSpecs = $itemSpecs.'<tr style=\'word-wrap: break-word;\'><td width="50%"><strong>'.$k.':</strong> '.$v.'</td>';   
                $i = 2;
            } else if($i == 2){
                $itemSpecs = $itemSpecs.'<td width="50%"><strong>'.$k.':</strong> '.$v.'</td></tr>'; 
                $i = 1;
            }
        }
        if($i == 2){
            $itemSpecs = $itemSpecs.'<td width="50%"></td></tr>'; 
        }
        if($itemSpecs != ''){
            $itemSpecs = '<table style=\'width: 100%;
            word-wrap: break-word;\' cellspacing="5" width="100%" cellpadding="5" >'.$itemSpecs.'</table><br>';
            
           
             $description =  preg_replace("/\<h1\sstyle=\"\">(.*)\<\/h1\>/","",  $description,1);
             $description =  preg_replace("/\<h1>(.*)\<\/h1\>/","",  $description,1);
            

             

             if( $user_id ==  43700  ){
           
                // $description =  preg_replace("/<li>\s*<h3\sclass=\"pBottom-10\">(.*)<\/h3>\s*<\/li>/U","<li><b>$1</b></li>",  $description,1);
                // $description =  preg_replace("/<h3\s*class=\"pBottom-10\">(.*)<\/h3>/U","<h2>$1</h2>",  $description,1);
                // $description = str_replace('<div class="main1"><h1><br></h1>', "", $description);   
                // $description = str_replace("<h2>Description</h2>", "", $description);   
                // $description = str_replace('<h3 class="pBottom-10">Appearance</h3>', "<h2 >Appearance</h2>", $description);   
                // $description = str_replace('<h3 class="pBottom-10">Included Items</h3>', "<h2 >Included Items</h2>", $description);  
                $description = $itemSpecs; 
                

                
                
            } 
            else {
                $description = $itemSpecs.$description;
            }
            
            
            $description = removeHTMLFormatting($description, false);

            if( $user_id ==  35390  ){
                $description = str_replace("<h1>", "", $description);
                $description = str_replace("</h1>", "", $description);
            } 

           



        }
        print_r($description);
        return array("status" => "success", "message" => $description);
    }




if($user_id == 20008){ 
       $pattern1 = '/<span property="description">(.*)<style\stype="text\/css">/smU';
        $res1 = preg_match_all($pattern1, $description, $result1);
        if ($res1) {
            print_r($result1);
              $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
              return array("status" => "success", "message" => $description);  
        } else {
            $pattern1 = '/<meta name="description" content="">(.*)<style\stype="text\/css">/smU';
            $res1 = preg_match_all($pattern1, $description, $result1);
            if ($res1) {
                print_r($result1);
                  $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                  $description = removeHTMLFormatting($description, false);
                  return array("status" => "success", "message" => $description);  

            } else {

                  $pattern1 = '/<span class="card-title-decoration">(.*)<style\stype="text\/css">/smU';
            $res1 = preg_match_all($pattern1, $description, $result1);
            if ($res1) {
                print_r($result1);
                  $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                  $description = removeHTMLFormatting($description, false);
                  return array("status" => "success", "message" => $description);  

            } else {
                 
                $pattern1 = '/<span class="card-title-decoration">(.*)<!-- Footer\sstart\s\/\/ -->/smU';
            $res1 = preg_match_all($pattern1, $description, $result1);
            if ($res1) {
                print_r($result1);
                  $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                  $description = removeHTMLFormatting($description, false);
                  return array("status" => "success", "message" => $description);  

            } else {     
            $pattern1 = '/<span\sstyle=[^>]+>Up\sfor\sSale:(.*)<\/strong><\/div>/sm';
            $res1 = preg_match_all($pattern1, $description, $result1);
            if ($res1) {
                print_r($result1);
                  $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                  $description = removeHTMLFormatting($description, false);
                  return array("status" => "success", "message" => $description);  

            }
            else{
                
                  return array("status" => "fail", "message" => "RegEx not matched.");
            }

              }


                  
                 //
               
                }   



                
             }
         }
 
}





//

if($user_id == 10399){ 
       
$description = '<p>- Customise the front with any wording.</p><br><p>- 6x6 inch 300gsm Luxury Textured Square Card.</p><br><p>- Inside is left BLANK for you to write your own message.</p><br><p>- Card is 100% plastic free and recyclable</p>';
return array("status" => "success", "message" => $description);  
}

if($user_id == 23515){ 
    $pattern1 = '/<!--\s*Beschreibung\s*-->(.*)<!--\s*Preis\s*-->/ismU';
     $res1 = preg_match_all($pattern1, $description, $result1);
     if ($res1) {
           $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
     } else {
         return array("status" => "fail", "message" => "RegEx not matched.");
      }
      $description = removeHTMLFormatting($description, false);
  return array("status" => "success", "message" => $description);  
}




if($user_id == 38825){ 
    $description = preg_replace('/<a[^>]*>(.*)<\/a>/m', ' ', $description);
        return array("status" => "success", "message" => $description);

}


if($user_id == 18791){ 
       $pattern1 = '/<sd_description[^>]*>(.*)<\/sd_description>/ismU';
        $res1 = preg_match_all($pattern1, $description, $result1);
        if ($res1) {
              $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
        } else {
            return array("status" => "fail", "message" => "RegEx not matched.");
         }
         $description = removeHTMLFormatting($description, false);
     return array("status" => "success", "message" => $description);  
}



 if($user_id == 4527){ 
       $pattern1 = '/<froodescription >(.*)<\/froodescription>/ismU';
        $res1 = preg_match_all($pattern1, $description, $result1);
        if ($res1) {
            print_r($result1);
              $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
        } else {
            $pattern1 = '/<!-- froo description -->(.*)<!-- end froo description -->/ismU';
            $res1 = preg_match_all($pattern1, $description, $result1);
            if ($res1) {
                print_r($result1);
                  $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
            } else {
                $pattern1 = '/<froodescription>(.*)<\/froodescription>/ismU';
                $res1 = preg_match_all($pattern1, $description, $result1);
                if ($res1) {
                    print_r($result1);
                      $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                } else {
                        if($description = '' || strlen($description) <=200 ){

                        }
                        else{
                            $pattern1 = '/<p><CENTER><H4>(.*)<\/P>/ismU';
                            $res1 = preg_match_all($pattern1, $description, $result1);
                            if ($res1) {
                                print_r($result1);
                                  $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                            } else {
                                    if($description = '' || strlen($description) <=200 ){
            
                                    }
                                    else{
                                    return array("status" => "fail", "message" => "RegEx not matched.".$description);
                                       }
                                   }
                           }
                       }


                
             }
         }
     return array("status" => "success", "message" => $description);  
}

// if($user_id == 13881){ 
//     $pattern1 = '/<froodescription [^>]*>(.*)<\/froodescription>/ismU';
//      $res1 = preg_match_all($pattern1, $description, $result1);
//      if ($res1) {
//          print_r($result1);
//            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
//      } else {
//         return array("status" => "fail", "message" => "RegEx not matched.");
//       }
//   return array("status" => "success", "message" => $description);  
// }

if($user_id == 19410){ 
    $pattern1 = '/(.*)PLEASE NOTE/ismU';
    $res1 = preg_match_all($pattern1, $description, $result1);
    if ($res1) {
        $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
    } else {
        $pattern1 = '/<div[^>]+>(.*)PLEASE NOTE/ismU';
        $res1 = preg_match_all($pattern1, $description, $result1);
    if ($res1) {
        $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
    } else {
        return array("status" => "fail", "message" => "RegEx not matched.");
          }          
        
    }
    if($user_id == 20717){ 
        $pattern1 = '/<h3 [^>]*>PHILIPS - HUE WHITE AND COLOR AMBIANCE E12 BULB - WHITE(.*)<span class="aucCounterBlueText">Auctivas\s*Counter<\/span>/sm';
         $res1 = preg_match_all($pattern1, $description, $result1);
         if ($res1) {
               print_r($result1);
               $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
         } else {
             return array("status" => "fail", "message" => "RegEx not matched.");
          }
      return array("status" => "success", "message" => $description);  
 }

    

    $description = preg_replace("/<body[^>]+>/", "", $description);
    $description = str_replace("</body>", "", $description);
    $description = preg_replace('/(<[^>]+) style=".*?"/i', '$1', $description);
    $description = preg_replace('/\s*style=\\"[^\\"]*\\"/', '', $description);
    $description = preg_replace("/<font[^>]+>/", "", $description);
    $description = str_replace("</font>", "", $description);
    $description = preg_replace("/<FONT[^>]+>/", "", $description);
    $description = str_replace("</FONT>", "", $description);
    $description = preg_replace('#&nbsp;#is', ' ', $description);
    $description = preg_replace('#<p>\s*<br[^>]*>\s*</p>#is', '', $description);
    $description = preg_replace('#<p>\s*<br[^>]*>\s*<br[^>]*>\s*</p>#is', '', $description);
    $description = preg_replace('#<p>\s*&nbsp;\s*&nbsp;\s*</p>#is', '', $description);
    $description = preg_replace('#<p>\s*&nbsp;\s*</p>#is', '', $description);
    $description = preg_replace('#<p[^>]*>\s*</p>#is', '', $description);
        $description = str_replace("<b>", "", $description);
        $description = str_replace("</b>", "", $description);
        $description = str_replace("<strong>", "", $description);
        $description = str_replace("</strong>", "", $description);
       // $description = str_replace("<p>", "", $description);
       // $description = str_replace("</p>", "", $description);
        $description = str_replace("<b>", "", $description);
        $description = str_replace("</b>", "", $description);
        $description = str_replace("<i>", "", $description);
        $description = str_replace("</i>", "", $description);
        $description = str_replace("<div>", "", $description);
        $description = preg_replace("/<div[^>]+>/", "", $description);
        $description = str_replace("</div>", "", $description);
        $description = str_replace("<table>", "", $description);
        $description = preg_replace("/<table[^>]+>/", "", $description);
        $description = str_replace("<tbody>", "", $description);
        $description = str_replace("<tr>", "", $description);
        $description = str_replace("<td>", "", $description);
        $description = str_replace("<html>", "", $description);
        $description = str_replace("<body>", "", $description); 
        $description = str_replace("**", "", $description); 
        $description = preg_replace("/<meta[^>]+>/", "", $description);
        $description = preg_replace("/<span[^>]+>/", "", $description);
        
    $description = str_replace("<span>", "", $description);
    $description = str_replace("</span>", "", $description);
    $description = str_replace("<center>", "", $description);
    $description = str_replace("</center>", "", $description);
    $description = str_ireplace('align="center"', "", $description);
    $description = preg_replace('#<style[^>]*>.*?</style>#is', '', $description);
    $description = preg_replace('#<script[^>]*>.*?</script>#is', '', $description);
    $description = preg_replace("/<link[^>]+>/", "", $description);
    $description = preg_replace("/<img[^>]+>/", "", $description);
    $description = preg_replace('#<audio[^>]*>.*?</audio>#is', '', $description);
       $description = preg_replace("/<p[^>]*>/", "<div>", $description);
    $description = str_replace("</p>", "</div><br/>", $description);
 

  return array("status" => "success", "message" => $description);
}


  if($user_id == 19562){ 
      $pattern1 = '/<div class="col col-xs-12 col-sm-6">(.*)<\/div>.*<div\sclass="row store_desc softshadow">/ismU';
        $res1 = preg_match_all($pattern1, $description, $result1);
        if ($res1) {
            print_r($result1);
            $description = isset($result1[0][0]) ? trim($result1[0][0]) : "";
        } else {
            return array("status" => "fail", "message" => "RegEx not matched.");
            
        }
     return array("status" => "success", "message" => $description);  
}

    if($user_id == 19488){ 
        $pattern1 = '/<\/style><div\sclass="cl-rich-text-el"><p><span\sstyle="color:#FFFFFF"><strong><span\sstyle="font-size:22px">(.*)<span\sstyle="color:#FFF0F5"><strong><span style="font-size:48px">/ismU';
        $res1 = preg_match_all($pattern1, $description, $result1);
        if ($res1) {
            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
        }else {
            return array("status" => "fail", "message" => "RegEx not matched.");
             }
          return array("status" => "success", "message" => $description);  
        }
    


/*
    if($user_id == 19443){ 
        $pattern1 = '/ <!--BEGIN DESCBOX-->(.*)<!--END DESCBOX-->/ismU';
        $res1 = preg_match_all($pattern1, $description, $result1);
        if ($res1) {
            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
        } else {
            $pattern1 = '/<font[^>]*>(.*)<\/font>/ismU';
            $res1 = preg_match_all($pattern1, $description, $result1);
        if ($res1) {
            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
        } else {
            $pattern1 = '/<span[^>]*>(.*)<\/span>/ismU';
            $res1 = preg_match_all($pattern1, $description, $result1);
        if ($res1) {
            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
        } else {
            return array("status" => "fail", "message" => "RegEx not matched.");
             }

              }          
            
        }
      return array("status" => "success", "message" => $description);
      
      
    }*/



if($user_id == 19321){ 
    $pattern1 = '/<div\sdata-element="productDescription">(.*)<p\sclass="MsoNormal">/ismU';
    $res1 = preg_match_all($pattern1, $description, $result1);
    if ($res1) {
        $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
    } else {
        $pattern1 = '/<div\sdata-element="productDescription">(.*)<div\sclass="row">/ismU';
        $res1 = preg_match_all($pattern1, $description, $result1);
    if ($res1) {
        $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
    } else {
        return array("status" => "fail", "message" => "RegEx not matched.");
          }          
        
    }
  return array("status" => "success", "message" => $description);
  
  
}

if($user_id == 17346){ 
  if( strpos($description, '<![CDATA[' ) !== false )  
  {
    $description = str_replace("<![CDATA[", "", $description);
    echo "match found";
  }
  return array("status" => "success", "message" => $description);               
}

    if($user_id == 11739){    
        $pattern1 = '/<hr \/>\s*<p>(.*)<hr \/>.*Payment:/ismU';
        $res1 = preg_match_all($pattern1, $description, $result1);
        if ($res1) {
            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
        } else {
            $pattern1 = '/<p [^>]*>(.*)Payment:/ismU';
            $res1 = preg_match_all($pattern1, $description, $result1);
        if ($res1) {
            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
        } else {

            $pattern1 = '/<\/div><div>(.*)Payment:/ismU';
            $res1 = preg_match_all($pattern1, $description, $result1);
        if ($res1) {
            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
        } else {

                $pattern1 = '/<\/div><\/div><\/div>(.*)<hr>.*Payment:/ismU';
            $res1 = preg_match_all($pattern1, $description, $result1);
        if ($res1) {
            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
        } else {
               $pattern1 = '/<hr \/>(.*)Payment/ismU';
               $res1 = preg_match_all($pattern1, $description, $result1);
        if ($res1) {
            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
        } else {

            $pattern1 = '/<\/div><\/div><\/div>(.*)>Payment:/ismU';
            $res1 = preg_match_all($pattern1, $description, $result1);
     if ($res1) {
         $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
     } else {


        $pattern1 = '/<p[^>]+><strong><span[^>]+>(.*)<hr>.*Payment:/ismU';
            $res1 = preg_match_all($pattern1, $description, $result1);
     if ($res1) {
         $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
     } else {

        
        $pattern1 = '/<div><span\sstyle=[^>](.*)<\/span><\/div>/sm';
            $res1 = preg_match_all($pattern1, $description, $result1);
     if ($res1) {
         $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
     }
        else{

            $pattern1 = '/<strong><span style="background-color: #008000;">(.*)<\/span><\/span><\/div>/sm';
            $res1 = preg_match_all($pattern1, $description, $result1);
     if ($res1) {
         $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
     }
        else{

        return array("status" => "fail", "message" => "RegEx not matched.");
        }
        }
     }


    
     }



            //<\/div><\/div><\/div>(.*)>Payment:
             
       }
   
          
        }
       
             //
             
             } 
         
             }          
        }
      return array("status" => "success", "message" => $description);
    }




    if($user_id == 17195){    
        $pattern1 = '/Description<\/h2>(.*)Payment<\/h2>/ismU';
        $res1 = preg_match_all($pattern1, $description, $result1);
        if ($res1) {
            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
           
        } else {
              return array("status" => "fail", "message" => "RegEx not matched.");
        }
      return array("status" => "success", "message" => $description);
    }



     if($user_id == 9273){    
        $pattern1 = '/<div>(.*)<\/div><div><br><\/div><div><br><\/div><div>*/ismU';
        $res1 = preg_match_all($pattern1, $description, $result1);
        if ($res1) {
            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
            print_r($description);
            //print_r("pankaj");
            //exit(0);
        } else {
            
         $pattern1 = '/<p[^>]*>(.*)<\/p>/ismU';
         $res1 = preg_match_all($pattern1, $description, $result1);
        if ($res1) {
            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
            print_r($description);
            //print_r("pankaj");
            //exit(0);
        } else {
                
         $pattern1 = '/<div>(.*)<\/div>/ismU';
         $res1 = preg_match_all($pattern1, $description, $result1);
        if ($res1) {
            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
            print_r($description);
            //print_r("pankaj");
            //exit(0);
        } else {
            
              $pattern1 = '/<font[^>]*>(.*)<\/font>/ismU';
         $res1 = preg_match_all($pattern1, $description, $result1);
        if ($res1) {
            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
            print_r($description);
            //print_r("pankaj");
            //exit(0);
        } else {
  
              
            $pattern1 = '/(.*)<br>.*SHIPPING/ismU';
            $res1 = preg_match_all($pattern1, $description, $result1);
           if ($res1) {
               $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
               print_r($description);
               //print_r("pankaj");
               //exit(0);
           } else {
                 return array("status" => "fail", "message" => "RegEx not matched.");
           }




            //* SHIPPING 
             
        }
                
              
              }
        }
        
        //<div>(.*)<\/div>
      return array("status" => "success", "message" => $description);
    }
     }
    

    if($user_id == 10474){    
        $pattern1 = '/Product\sDescription(.*)<h3>ABOUT\sUS<\/h3>/ismU';
        $res1 = preg_match_all($pattern1, $description, $result1);
        if ($res1) {
            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
            //print_r($description);
            //print_r("pankaj");
            //exit(0);
        } else {
            $pattern1 = '/<span[^>]*><span[^>]*>Product\s*Key \s*Features(.*)/ismU';
            $res1 = preg_match_all($pattern1, $description, $result1);
            if ($res1) {
                $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                //print_r($description);
                //print_r("pankaj");
                //exit(0);
            } else {
                  return array("status" => "fail", "message" => "RegEx not matched.");
            }
        }
      $description = removeHTMLFormatting($description, false);
      return array("status" => "success", "message" => $description);
    }
    
    if($user_id == 19083){
        $description = removeHTMLFormatting($description, false);
        $pattern1 = '/(.*)<p[^>]*>\s*<strong>\s*<u>\*\*\s*Shipping\s*\/\s*Combined/ismU';
        $res1 = preg_match_all($pattern1, $description, $result1);
        if ($res1) {
            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
        } else {
            $pattern1 = '/(.*)<p[^>]*>\s*<b>\s*<u>\*\*\s*Shipping\s*\/\s*Combined/ismU';
            $res1 = preg_match_all($pattern1, $description, $result1);
            if ($res1) {
                $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
            } else {
                $pattern1 = '/(.*)<p[^>]*>\s*\*\*\s*Shipping\s*\/\s*Combined/ismU';
                $res1 = preg_match_all($pattern1, $description, $result1);
                if ($res1) {
                    $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                } else {
                    $pattern1 = '/(.*)<p[^>]*>\s*<b>\s*[\*]+\s*Check\s*out\s*my\s*other\s*listings/ismU';
                    $res1 = preg_match_all($pattern1, $description, $result1);
                    if ($res1) {
                        $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                    } else {
                        $pattern1 = '/(.*)<p[^>]*>\s*I\s*ship\s*within\s*24\s*hours\s*of\s*receiving\s*/ismU';
                        $res1 = preg_match_all($pattern1, $description, $result1);
                        if ($res1) {
                            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                        } else {
                            return array("status" => "fail", "message" => "RegEx not matched."); 
                        }
                    }
                }
            }
        }
        return array("status" => "success", "message" => $description);
    } else if($user_id == 18995){
        $description = removeHTMLFormatting($description, false);
        $pattern1 = '/Item\s*Description(.*)<\/td>/ismU';
        $res1 = preg_match_all($pattern1, $description, $result1);
        if ($res1) {
            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
        } else {
            return array("status" => "fail", "message" => "RegEx not matched.");
        }
        return array("status" => "success", "message" => $description);
    } else if($user_id == 18899){
        $itemSpecs = "";
        $itemSpecificObj = json_decode($item_specific, true);		
        foreach($itemSpecificObj as $k => $v){
        	$itemSpecs = $itemSpecs.'<p><strong>'.$k.':</strong> '.$v.'</p>';
        }
        $description = $description.$itemSpecs;        
        return array("status" => "success", "message" => $description);
    } else if($user_id == 17609 || $user_id == 38696 || $user_id == 39844){
        $itemSpecs = "";        
        $item_specificObj = json_decode($item_specific, true);
        foreach($item_specificObj as $k => $v){
            $itemSpecs = $itemSpecs.'<p><strong>'.$k.':</strong> '.$v.'</p>';
        }
        $description = $itemSpecs.$description;
        return array("status" => "success", "message" => $description);
    } else if($user_id == 18345){        
        $description = "<p><strong>Condition: </strong> ".$condition_val."</p>".$description;
        return array("status" => "success", "message" => $description);
    } else if($user_id == 18723){        
        $itemSpecificStr = "";
        $item_specificObj = json_decode($item_specific, true);
        foreach($item_specificObj as $k => $v){
            $itemSpecificStr = $itemSpecificStr.'<p><strong>'.$k.':</strong> '.$v.'</p>';
        }
        $description = $description.$itemSpecificStr;
        return array("status" => "success", "message" => $description);
    } else if($user_id == 17349){
        $description = removeHTMLFormatting($description, true);
        return array("status" => "success", "message" => $description);
    } else if( $user_id == 18772  ){
        $pattern1 = '/h1\s*,\s*h2\s*,\s*h3/ismU';	
        $res1 = preg_match_all($pattern1, $description, $result1);				
        if($res1){	
            $description = preg_replace('#<style[^>]*>.*?</style>#is', '<style type="text/css">.product__description h1,.product__description h2,.product__description h3{font-family: "Lato", sans-serif;} p,ul,li,ol,span{font-family: "Open Sans", sans-serif;} .product__description h1{ padding:1em 1em 0 1em; color:rgb(51, 51, 51); } .product__description h2,.product__description h3{ position:relative; padding:0.5em 1em 0.5em 1.2em; margin:0 1em 1em; border:2px solid #095fe1; border-radius: 0.4em; background: rgb(255, 255, 255); color:#095fe1; font-size:100%; line-height:1.2; text-align:left; } .product__description  h2:before, .product__description h3:before{ content:\'\'; position:absolute; left:0.5em; top:0; bottom:0; margin:6px auto; border:3px solid#095fe1; border-radius:3px; color:#095fe1; } p, .product_dec div{ margin:0.5em 0; padding:0 2em 2em 2em; color:rgb(51, 51, 51); text-align:left; } #main{ max-width:96%; background:rgb(249, 249, 249); border-radius:0.5em; text-align:center; } @media screen and (min-width: 768px) { #main{ margin:0 auto; max-width:65%; } .product__description h2,.product__description h3{ font-size:125%; } }</style>', $description);
        }
        else {
            $pattern1 = '/(.*?)Shipping:/ismU';	
            $res1 = preg_match_all($pattern1, $description, $result1);				
            if($res1){	
                $description = preg_replace('#<style[^>]*>.*?</style>#is', '<style type="text/css">.product__description h1,.product__description h2,.product__description h3{font-family: "Lato", sans-serif;} p,ul,li,ol,span{font-family: "Open Sans", sans-serif;} .product__description h1{ padding:1em 1em 0 1em; color:rgb(51, 51, 51); } .product__description h2,.product__description h3{ position:relative; padding:0.5em 1em 0.5em 1.2em; margin:0 1em 1em; border:2px solid #095fe1; border-radius: 0.4em; background: rgb(255, 255, 255); color:#095fe1; font-size:100%; line-height:1.2; text-align:left; } .product__description  h2:before, .product__description h3:before{ content:\'\'; position:absolute; left:0.5em; top:0; bottom:0; margin:6px auto; border:3px solid#095fe1; border-radius:3px; color:#095fe1; } p, .product_dec div{ margin:0.5em 0; padding:0 2em 2em 2em; color:rgb(51, 51, 51); text-align:left; } #main{ max-width:96%; background:rgb(249, 249, 249); border-radius:0.5em; text-align:center; } @media screen and (min-width: 768px) { #main{ margin:0 auto; max-width:65%; } .product__description h2,.product__description h3{ font-size:125%; } }</style>', $description);
            }
           
        }


        $item_specificObj = json_decode($item_specific, true);
        $itemSpecs = "";
        $i = 1;
        foreach($item_specificObj as $k => $v){
            if($i == 1){
                $itemSpecs = $itemSpecs.'<tr><td width="50%"><strong>'.$k.':</strong> '.$v.'</td>';   
                $i = 2;
            } else if($i == 2){
                $itemSpecs = $itemSpecs.'<td width="50%"><strong>'.$k.':</strong> '.$v.'</td></tr>'; 
                $i = 1;
            }
        }
        if($i == 2){
            $itemSpecs = $itemSpecs.'<td width="50%"></td></tr>'; 
        }
        if($itemSpecs != ''){
            $itemSpecs = '<table cellspacing="5" cellpadding="5" style="width:100%;">'.$itemSpecs.'</table>';
            $description = $itemSpecs.$description;
        }
        return array("status" => "success", "message" => $description);
    } else if($user_id == 18555){        
        $itemSpecificStr = "";
        $item_specificObj = json_decode($item_specific, true);
        foreach($item_specificObj as $k => $v){
            $itemSpecificStr = $itemSpecificStr.'<p><strong>'.$k.':</strong> '.$v.'</p>';
        }
        $description = $itemSpecificStr.$description;
        return array("status" => "success", "message" => $description);
    
    } else if($user_id == 18407){
        $pattern1 = '/<!--\s*Short\s*Description\s*Start\s*-->(.*)<\/div>\s*<\/div>\s*<\/div>\s*<div[^>]*>\s*<\/div>\s*<!--\s*Tabs\s*Start/ismU';	
        $res1 = preg_match_all($pattern1, $description, $result1);				
        if($res1){						
            $description = isset($result1[1][0])?trim($result1[1][0]):"";				
        }
        $description = removeHTMLFormatting($description, false);  
        return array("status" => "success", "message" => $description);
    } else if($user_id == 13053){
        $pattern1 = '/(<div\s*class="hd-content"[^>]*>.*)<div\s*class="hd-wrapcens"[^>]*>\s*<div\s*id="hd-footer"/ismU';	
        $res1 = preg_match_all($pattern1, $description, $result1);				
        if($res1){						
            $description = isset($result1[1][0])?trim($result1[1][0]):"";				
        } 
        $description = removeHTMLFormatting($description, false);  
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 18647) {
        $pattern1 = '/<div\s*id="description">(.*)<\/div>\s*<\/div>\s*<div\s*class="section"/ismU';
        $res1 = preg_match_all($pattern1, $description, $result1);
        if ($res1) {
            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
        } else {
            $pattern1 = '/<div\s*typeof="Product"\s*vocab="https:\/\/schema\.org\/">\s*<br>\s*<\/div>(.*<\/table>)/ismU';
            $res1 = preg_match_all($pattern1, $description, $result1);
            if ($res1) {
                $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
            } else {
                $pattern1 = '/Product Details<\/span>(.*)<\/table>/ismU';
                $res1 = preg_match_all($pattern1, $description, $result1);
                if ($res1) {
                    $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                } else {
                    $pattern1 = '/<table>(.*)<\/table>/ismU';
                    $res1 = preg_match_all($pattern1, $description, $result1);
                    if ($res1) {
                        $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                    } else {
                        
                    $pattern1 = '/.*<table[^>]*>(.*)<\/table>/ismU';
                    $res1 = preg_match_all($pattern1, $description, $result1);
                    if ($res1) {
                        $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                    } else { 
                         return array("status" => "fail", "message" => "RegEx not matched.");   
                          }
                    }

                    //<table>(.*)<\/table>
                   
                }


                //
               
            }
        }
        $description = removeHTMLFormatting($description, false);
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 212) {
        $pattern = '/<div\s*class="description"\s*style="font-family:Arial;font-size:14px;color:#646464;font-weight:normal;">(.*)<div\s*class="speclist">.*<ul\s*class="specs">\s*<li>(.*)<\/li>\s*<\/ul>.*<div\s*class="banner2">/sm';
        $res = preg_match_all($pattern, $description, $result);
        if ($res) {
            $part1 = isset($result[1][0]) ? trim($result[1][0]) : "";
            $part2 = isset($result[2][0]) ? trim($result[2][0]) : "";
            $description = $part1 . $part2;
        } else {
            $pattern1 = '/(<div\s*class="panel panel-default">.*<\/ul>\s*<\/div>\s*<\/div>)/sm';
            $res1 = preg_match_all($pattern1, $description, $result1);
            if ($res1) {
                $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
            } else {
                $pattern1 = '/(<ul\s*class="specs">.*)<!--\s*HERE\s*GOES\s*THE\s*PRODUCT\s*DESCRIPTION\s*-->/sm';
                $res1 = preg_match_all($pattern1, $description, $result1);
                if ($res1) {
                    $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                } else {
                    $pattern1 = '/<div\s*class="panel-heading">Description<\/div>(.*)<\/div>\s*<div\s*class="panel\s*panel-default">\s*<div\s*class="panel-heading">Features/sm';
                    $res1 = preg_match_all($pattern1, $description, $result1);
                    if ($res1) {
                        $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                    } else {
                        return array("status" => "fail", "message" => "RegEx not matched.");
                    }
                }
            }
        }
        $description = str_replace("Â", "", $description);
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 5686) {
        $pattern = '/<div\s*id="detail_info"\s*class="widget">(.*)<\/div>\s*<div\s*class="module\s*module_offset_fix">\s*<div\s*id="storeDescriptionTab"/smi';
        $res1 = preg_match_all($pattern, $description, $result1);
        if ($res1) {
            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
        } else {
            $pattern = '/<div\s*class="widget"\s*id="detail_info">(.*)<\/div>\s*<div\s*class="module\s*module_offset_fix">\s*<div\s*class="widget\s*widget_tab"\s*id="storeDescriptionTab">/smi';
            $res1 = preg_match_all($pattern, $description, $result1);
            if ($res1) {
                $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
            }
        }
        $description = str_replace("&nbsp;", " ", $description);
        $description = str_replace(' ', "", $description);
        $description = str_replace('Â', "", $description);
        $description = preg_replace("/<font[^>]+>/", "", $description);
        $description = str_replace("</font>", "", $description);
        $description = preg_replace("/<h1[^>]+>/", "", $description);
        $description = str_replace("</h1>", "", $description);
        $description = preg_replace("/<meta[^>]+>/", "", $description);
        $description = str_replace("</meta>", "", $description);
        $description = preg_replace("/<strong>/", "", $description);
        $description = str_replace("</strong>", "", $description);
        $description = preg_replace("/<\b>/", "", $description);
        $description = str_replace("</b>", "", $description);
        $description = preg_replace('/(<[^>]+) style=".*?"/i', '$1', $description);
        $description = str_replace('align="center"', "", $description);
        $description = preg_replace('/\s*style=\\"[^\\"]*\\"/', '', $description);
        $description = preg_replace('/<title>.*<\/title>/smU', '', $description);
        $description = preg_replace("/<link[^>]+>/", "", $description);
        $description = preg_replace("/<img[^>]+>/", "", $description);
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 4059) {
        $pattern = '/<span\s*class="header-title-decoration"\s*>(.*)<\/span>\s*<\/h1>/sm';
        $res = preg_match_all($pattern, $description, $result);
        if ($res) {
            $description = isset($result[1][0]) ? trim($result[1][0]) : "";
        } else {
            $pattern = '/<!--\s*Start\s*Description\s*AucLister\s*-->(.*)<!--\s*End\s*Description\s*AucLister/sm';
            $res = preg_match_all($pattern, $description, $result);
            if ($res) {
                $description = isset($result[1][0]) ? trim($result[1][0]) : "";
            } else {
                $pattern = '/(.*)<!--CSG\s*INDICATOR\s*START\s*-->/sm';
                $res = preg_match_all($pattern, $description, $result);
                if ($res) {
                    $description = isset($result[1][0]) ? trim($result[1][0]) : "";
                } else {
                    return array("status" => "fail", "message" => "RegEx not matched.");
                }
            }
        }
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 4187) {
        $pattern = '/<div\s*id="description"\s*>(.*)<\/div>/sm';
        $res = preg_match_all($pattern, $description, $result);
        if ($res) {
            $description = isset($result[1][0]) ? trim($result[1][0]) : "";
        } else {
            return array("status" => "fail", "message" => "RegEx not matched.");
        }
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 2213) {
        $pattern = '/<etbdescription[^>]*>(.*)<\/etbdescription>/sm';
        $res = preg_match_all($pattern, $description, $result);
        if ($res) {
            $description = isset($result[1][0]) ? trim($result[1][0]) : "";
        } else {
            return array("status" => "fail", "message" => "RegEx not matched.");
        }
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 18445) {
        echo "1";
        $pattern = '/<!--\s*DESCRIPTION\s*Start-->(.*)<\/div>\s*<\/div>\s*<\/div>\s*<\/div>\s*<\/div>\s*<!--\s*Footer\s*Start/ismU';
        $res = preg_match_all($pattern, $description, $result1);
        echo "2";
        if ($res) {
            echo " 3i ";
            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
        } else {
            echo " 3 ";    
        $pattern = '/(.*?)<p\s*class="MsoNormal"\s*style="margin-bottom:\s*0.0001pt;\s*line-height: normal;">\s*<font face="Trebuchet MS">\s*<b>\s*Shipping:/ms';
        $res = preg_match_all($pattern, $description, $result1);
        if ($res) {
            echo " 4 ";
            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
            print_r($description);
            //exit(0);
        } else {
            
            echo " 5 ";
             $pattern = '/(.*)<div\sstyle=""><b style="font-family: &quot;Trebuchet MS&quot;;">Shipping:/ms';
        $res = preg_match_all($pattern, $description, $result1);
        if ($res) {
            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
            print_r($description);
            //exit(0);
        } else {
            echo " 6 ";
               $pattern = '/(.*)<div><b>Shipping:/ms';
        $res = preg_match_all($pattern, $description, $result1);
        if ($res) {
            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
            print_r($description);
            //exit(0);
        } else {
            echo " 7 ";
            $pattern = '/(.*?)<div style=""><b>\s*Shipping:/ms';
            $res = preg_match_all($pattern, $description, $result1);
            if ($res) {
                $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                print_r($description);
                //exit(0);
            } else {
                //(.*)<div\sstyle=""><b>Shipping:
           //(.*?)Shipping
           //(.*)<b>Shipping:
           echo " 8 ";
           $pattern = '/(.*)\s*<b>\s*Shipping:\s*<\/b>/ms';
           $res = preg_match_all($pattern, $description, $result1);
           if ($res) {
               $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
               print_r($description);
               //exit(0);
           } else {
            echo " 9 ";
               $pattern = '/(.*)<font size="4" face="Trebuchet MS">Shipping/ms';
           $res = preg_match_all($pattern, $description, $result1);
           if ($res) {
               $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
               print_r($description);
               //exit(0);
           } else {
                echo " 1u ";
                //return array("status" => "fail", "message" => "RegEx not matched."); 
                 // $pattern = '/(.*)<div>.*Quick\sshipping:/ms';
                 //$pattern = '/(.*)<div>(.*)Shipping:/ms';
                 $pattern = '/(.*)shipping/msi';
                 
                 
           $res = preg_match_all($pattern, $description, $result1);
           if ($res) {
              echo " 10  "; 
               $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
               print_r($description);
               //exit(0);
           } else {

           $pattern = '/(.*)<font [^>]*>Fast Shipping/ms';
           $res = preg_match_all($pattern, $description, $result1);
           if ($res) {
            echo " 11 ";
               $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
               print_r($description);
               //exit(0);
           } else {
            $pattern = '/(.)*<span [^>].*Ships from California/ms';
            $res = preg_match_all($pattern, $description, $result1);
            if ($res) {
                echo " 10 ";
                $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                print_r($description);
                //exit(0);
            } else {
                echo " 11 "; 
               $pattern = '/(.)*<span [^>].*FAST SHIPPING /ms';
            $res = preg_match_all($pattern, $description, $result1);
            if ($res) {
                echo " 12 "; 
                $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                print_r($description);
                //exit(0);
            } else {
                echo " 13 "; 
                $pattern = '/(.)*[^>].*Ships from California/ms';
                $res = preg_match_all($pattern, $description, $result1);
                if ($res) {
                    echo " 14 "; 
                    $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                    print_r($description);
                    //exit(0);
                } else {
                    echo " 15 "; 
                    $pattern = '/(.)*<font [^>].*Shipped /ms';
                    $res = preg_match_all($pattern, $description, $result1);
                    if ($res) {
                        echo " 16 "; 
                        $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                        print_r($description);
                        //exit(0);
                    } else {
                        echo " 17 "; 
                        $pattern = '/(.*)<font face="Arial">Shipping/ms';
                        $res = preg_match_all($pattern, $description, $result1);
                        if ($res) {
                            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                            print_r($description);
                            //exit(0);
                        } else {
                            echo " 18 "; 
                            $pattern = '/(.)*[^>].*Ships\sfrom\sCalifornia/ms';
                            $res = preg_match_all($pattern, $description, $result1);
                            if ($res) {
                                echo " 18 "; 
                                $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                                print_r($description);
                                //exit(0);
                            } else {
                              echo "dfdfdfdfdf";
                              $pattern = '/(.*)\sShips1\sfrom\sCalifornia,\sUSA/ms';
                              $res = preg_match_all($pattern, $description, $result1);
                               echo "43434dfdfdfdfdfdfd"; 
                            if ($res) {
                                 echo "dfdfdfdfdsdsdsdsdskkkkf";
                                $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                                print_r($description);
                                //exit(0);
                            } else {
                                echo " 189 "; 
                              $pattern = '/DESCRIPTION(.*)Fast/ms';
                              $res = preg_match_all($pattern, $description, $result1);
                               echo "pankaj na"; 
                            if ($res) {
                                 echo "dfdfdfdfdsdsdsdsdskkkksdsdsdsd3434343f";
                                $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                                print_r($description);
                                //exit(0);
                            } else {
                                $pattern = '/.*<span[^>]*>.*Fast\s*Shipping\s*\*\*\*<\/span>/ms';
                                $res = preg_match_all($pattern, $description, $result1);
                                 echo "pankaj na1234"; 
                              if ($res) {
                                   echo "anjali";
                                  $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                                  print_r($description);
                                  //exit(0);
                              } else {

                                $pattern = '/<!--DESCRIPTION Start-->(.*)<!--Footer Start-->/ms';
                                $res = preg_match_all($pattern, $description, $result1);
                                 echo "pankaj na1234"; 
                              if ($res) {
                                   echo "anjali";
                                  $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                                  print_r($description);
                                  //exit(0);
                              } else {
                              
                                   //   return array("status" => "fail", "message" => "RegEx not matched."); 
                            
                                   }
                                    



                                       //<!--DESCRIPTION Start-->(.*)<!--Footer Start-->

                                        
                                           }
                                         }



                                 //
                                 
                               }  



                               
                                
                            }
                            
                        }
                        
                    }
                    
                }
                
            }
                
            }
                  
              }
    
                
                }



                 
                   }  
                  



                 
           }
           
            }




           
        }
            
            //(.*)<div><b>Shipping:
            
             
        }
        
            
        }  
            
           
        }
        $description = removeHTMLFormatting($description, false);
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 18461) {
        $pattern = '/<div\s*class="col-md-12\s*col-sm-12\s*col-xs-12"[^>]*>(.*)<\/div>/ismU';
        $res = preg_match_all($pattern, $description, $result1);
        if ($res) {
            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
        } else {
            $pattern = '/<div\s*class="econtent">(.*)<\/div>\s*<!--\s*\/\/\s*Inhalt\s*--/ismU';
            $res = preg_match_all($pattern, $description, $result1);
            if ($res) {
                $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
            } else {
                return array("status" => "fail", "message" => "RegEx not matched.");
            }
        }
        $description = removeHTMLFormatting($description, false);
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 12522) {
        $pattern1 = '/<main[^>]*>(.*)<\/main>/smUi';
        $res1 = preg_match_all($pattern1, $description, $result1);
        if ($res1) {
            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
        } else {

            $pattern1 = '/<main[^>]*>(.*)/smUi';
        $res1 = preg_match_all($pattern1, $description, $result1);
        if ($res1) {
            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
        } else {
             return array("status" => "fail", "message" => "RegEx not matched.");  
             }
         
        }
        $description = removeHTMLFormatting($description, false);
        $description = str_ireplace('#231b10', "#FFFFFF", $description);
        return array("status" => "success", "message" => $description);
    } 


     else if ($user_id == 7073) {
        $pattern = '/<!--\s*Block\s*Description\s*\/\/\s*-->(.*)<!--\s*\/\/\s*Block\s*Description/ismU';
        $res = preg_match_all($pattern, $description, $result);
        if ($res) {
            $description = isset($result[1][0]) ? trim($result[1][0]) : "";
        } else {
            $pattern = '/<!--\s*Start\s*Description\s*AucLister\s*-->(.*)<!--\s*End\s*Description\s*AucLister/ismU';
            $res = preg_match_all($pattern, $description, $result);
            if ($res) {
                $description = isset($result[1][0]) ? trim($result[1][0]) : "";
            } else {
                $pattern = '/<!--\s*Begin\s*Description\s*-->(.*)<!--\s*End\s*Description/ismU';
                $res = preg_match_all($pattern, $description, $result);
                if ($res) {
                    $description = isset($result[1][0]) ? trim($result[1][0]) : "";
                } else {
                    //return array("status" => "fail", "message" => "RegEx not matched.");
                }
            }
        }
        $description = removeHTMLFormatting($description, false);
         
        $conditionStr = "";
        echo "pankaj";
        echo $condition_note;
        echo $condition_val;
        echo $is_condition_note;
        echo $is_condition_val;
        //exit(0);

        if (strlen($condition_val) > 0) {
            $conditionStr = $conditionStr . "<p><strong>Condition:</strong> " . $condition_val . "</p>";
            echo " condition found    ".$conditionStr;
        }
        if (strlen($condition_note) > 0) {
            $conditionStr = $conditionStr . "<p><strong>Condition Description:</strong> " . $condition_note . "</p>";
        }
        $itemSpecificStr = "";
        if ($is_item_specific) {
            $item_specificObj = json_decode($item_specific, true);
            foreach ($item_specificObj as $k => $v) {
                $itemSpecificStr = $itemSpecificStr . '<p><strong>' . $k . ':</strong> ' . $v . '</p>';
            }
        }
        $description =  $conditionStr . $itemSpecificStr.$description ;
        $description = str_replace("<br>", "", $description);
        $description = str_replace("</br>", "", $description);
        echo "no condition matched";




        return array("status" => "success", "message" => $description);
    } else if ($user_id == 18076) {
        $pattern1 = '/<!--\$@Beschreibung\$-->(.*)<!--\$end-->/smUi';
        $res1 = preg_match_all($pattern1, $description, $result1);
        if ($res1) {
            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
        } else {
            return array("status" => "fail", "message" => "RegEx not matched.");
        }
        $description = removeHTMLFormatting($description, false);
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 17803) {
        $pattern1 = '/<div\s*id="tab-content1"\s*class="tab-content"[^>]*>(.*)<\/div>\s*<div\s*id="tab-content2"\s*class="tab-content"/smUi';
        $res1 = preg_match_all($pattern1, $description, $result1);
        if ($res1) {
            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
        } else {
            return array("status" => "fail", "message" => "RegEx not matched.");
        }
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 17185) {
        $pattern1 = '/<!--\s*Start\s*Description\s*AucLister\s*-->(.*)<!--\s*End\s*Description\s*AucLister/ismU';	
       	$res1 = preg_match_all($pattern1, $description, $result1);				
       	if($res1){						
       		$description = isset($result1[1][0])?trim($result1[1][0]):"";				
        } else{
            $pattern1 = '/desc\.gif">\s*<\/div>(.*)<\/td>/ismU';	
           	$res1 = preg_match_all($pattern1, $description, $result1);				
           	if($res1){						
           		$description = isset($result1[1][0])?trim($result1[1][0]):"";				
            } else{
                $pattern1 = '/<!--\s*CSG\s*INDICATOR\s*END\s*-->(.*)<!--\s*Begin:\s*AuctivaCounter/ismU';	
               	$res1 = preg_match_all($pattern1, $description, $result1);				
               	if($res1){						
               		$description = isset($result1[1][0])?trim($result1[1][0]):"";				
                }
            }
        }
        $description = removeHTMLFormatting($description, false);
        $itemSpecificStr = "";
        $item_specificObj = json_decode($item_specific, true);
        foreach ($item_specificObj as $k => $v) {
            $itemSpecificStr = $itemSpecificStr . '<p><strong>' . $k . ':</strong> ' . $v . '</p>';
        }
        $description = $itemSpecificStr . $description;
        return array("status" => "success", "message" => $description);
    }else if ($user_id == 17997) {
        $pattern = '/<froodescription>(.*)<\/froodescription>/ismU';
        $res = preg_match_all($pattern, $description, $result);
        if ($res) {
            $description = isset($result[1][0]) ? trim($result[1][0]) : "";
        } else {
            $pattern = '/<froodescription>(.*)<\/froodescription>/ismU';
            $res = preg_match_all($pattern, $description, $result);
            if ($res) {
                $description = isset($result[1][0]) ? trim($result[1][0]) : "";
            } else {
                $pattern = '/<!--\s*Begin\s*Description\s*-->(.*)<!--\s*End\s*Description/ismU';
                $res = preg_match_all($pattern, $description, $result);
                if ($res) {
                    $description = isset($result[1][0]) ? trim($result[1][0]) : "";
                } else {
                    //return array("status" => "fail", "message" => "RegEx not matched.");
                }
            }
        }
        $description = removeHTMLFormatting($description, false);
         
        $itemSpecificStr = "";
        if ($is_item_specific) {
            $item_specificObj = json_decode($item_specific, true);
            foreach ($item_specificObj as $k => $v) {
                $itemSpecificStr = $itemSpecificStr . '<p><strong>' . $k . ':</strong> ' . $v . '</p>';
            }
        }
        $description =  $itemSpecificStr.$description ;
       
    




        return array("status" => "success", "message" => $description);
    }  else if ($user_id == 16216) {
        $pattern1 = '/<!--\s*INIZIO\s*DESCRIZIONE\s*OGGETTO\s*-->\s*<table[^>]*>.*<table[^>]*>.*<td[^>]*>(.*)<\/td>/smUi';
        $res1 = preg_match_all($pattern1, $description, $result1);
        if ($res1) {
            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
        } else {
            return array("status" => "fail", "message" => "RegEx not matched.");
        }
        $description = removeHTMLFormatting($description, false);
        $description = str_ireplace("<h1>", "<p>", $description);
        $description = str_ireplace("</h1>", "</p>", $description);
        $description = str_ireplace("<h2>", "<p>", $description);
        $description = str_ireplace("</h2>", "</p>", $description);
        $description = str_ireplace("<h3>", "<p>", $description);
        $description = str_ireplace("</h3>", "</p>", $description);
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 17678) {
        $pattern = '/description-banner\.jpg"[^>]*>\s*<\/td>\s*<\/tr>\s*<tr>\s*<td[^>]*>\s*\&nbsp;\s*<\/td>\s*<td[^>]*>(.*)<\/td>/ismU';
        $res = preg_match_all($pattern, $description, $result);
        if ($res) {
            $description = isset($result[1][0]) ? trim($result[1][0]) : "";
        } else {
            return array("status" => "fail", "message" => "RegEx not matched.");
        }
        $description = removeHTMLFormatting($description, false);
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 17450) {
        $description = preg_replace('/<!--\s*Header\s*\/\/\s*start\s*-->.*<!--\s*Header\s*\/\/\s*end-->/smUi', '', $description);
        $description = preg_replace('/<!-- Footer Start -->.*<!--End Footer -->/smUi', '', $description);
        $description = preg_replace('/<div\s*class="bar warrantyBar">.*<div\s*class="estorepower-clear">/smUi', '', $description);
        $description = preg_replace('/<div\s*class="desc-hedtitle2">\s*WARRANTY.*<div\s*class="estorepower-clear">/smUi', '', $description);
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 17682) {
        $description = preg_replace('/<!--\s*Header\s*\/\/\s*start\s*-->.*<!--\s*Header\s*\/\/\s*end-->/smUi', '', $description);
        $description = preg_replace('/<!-- Footer Start -->.*<!--End Footer -->/smUi', '', $description);
        $description = preg_replace('/<div\s*class="bar warrantyBar">.*<div\s*class="estorepower-clear">/smUi', '', $description);
        $description = preg_replace('/<div\s*class="desc-hedtitle2">\s*WARRANTY.*<div\s*class="estorepower-clear">/smUi', '', $description);
        $description = preg_replace('#<style[^>]*>.*?</style>#is', '', $description);
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 17503) {
        $description = removeHTMLFormatting($description, false);
        $pattern1 = '/(.*)eBay Member since 2001/smUi';
        $res1 = preg_match_all($pattern1, $description, $result1);
        if ($res1) {
            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
        } else {
            $pattern1 = '/(.*)No fakes!/smUi';
            $res1 = preg_match_all($pattern1, $description, $result1);
            if ($res1) {
                $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
            } else {
                return array("status" => "fail", "message" => "RegEx not matched.");
            }
        }
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 16846) {
        $pattern = '/<div\s*class="colleft col-sm-7">(.*)<\/div>\s*<div\s*class="colright col-sm-5"/smiU';
        $res = preg_match_all($pattern, $description, $result);
        if ($res) {
            $description = isset($result[1][0]) ? trim($result[1][0]) : "";
        } else {
            $pattern = '/<section\s*class="tab-content"\s*id="content1"\s*style="overflow:hidden"\s*>(.*)<\/section>/smiU';
            $res = preg_match_all($pattern, $description, $result);
            if ($res) {
                $description = isset($result[1][0]) ? trim($result[1][0]) : "";
            } else {
                return array("status" => "fail", "message" => "RegEx not matched.");
            }
        }
        $description = removeHTMLFormatting($description, false);
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 17594) {
        $pattern = '/<sd_description>(.*)<\/sd_description>/smiU';
        $res = preg_match_all($pattern, $description, $result);
        if ($res) {
            $description = isset($result[1][0]) ? trim($result[1][0]) : "";
        } else {
            return array("status" => "fail", "message" => "RegEx not matched.");
        }
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 17582) {
        $pattern = '/<div\s*class="content__description mb0">(.*)<\/div>\s*<\/div>\s*<div\s*class="col\s*col-md-12 order-2"/ismU';
        $res = preg_match_all($pattern, $description, $result);
        if ($res) {
            $description = isset($result[1][0]) ? trim($result[1][0]) : "";
        } else {
            $pattern = '/<!--\s*Block\s*Description\s*\/\/\s*-->(.*)<!--\s*\/\/\s*Block\s*Description/ismU';
            $res = preg_match_all($pattern, $description, $result);
            if ($res) {
                $description = isset($result[1][0]) ? trim($result[1][0]) : "";
            } else {
                $pattern = '/<!--\s*Start\s*Description\s*AucLister\s*-->(.*)<!--\s*End\s*Description\s*AucLister/ismU';
                $res = preg_match_all($pattern, $description, $result);
                if ($res) {
                    $description = isset($result[1][0]) ? trim($result[1][0]) : "";
                } else {
                    return array("status" => "fail", "message" => "RegEx not matched.");
                }
            }
        }
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 13924) {
        $pattern1 = '/<div\s*class="tds-row tds-row-2\s*tds-product-description"[^>]*>(.*)<div\s*class="tds-row tds-row-4\s*tds-section-title-wrapper"/smUi';
        $res1 = preg_match_all($pattern1, $description, $result1);
        if ($res1) {
            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
        } else {
            //	return array("status" => "fail", "message" => "RegEx not matched.");         	    					
        }
        $description = removeHTMLFormatting($description, false);
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 17567) {
        $pattern = '/<summary\s*property="description">(.*)<\/summary>/ismU';
        $res = preg_match_all($pattern, $description, $result);
        if ($res) {
            $description = isset($result[1][0]) ? trim($result[1][0]) : "";
        } else {
            return array("status" => "fail", "message" => "RegEx not matched.");
        }
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 17308) {
        $pattern1 = '/<!--\s*Block\s*Description\s*\/\/\s*-->(.*)<!--\s*\/\/\s*Block\s*Description/smUi';
        $res1 = preg_match_all($pattern1, $description, $result1);
        if ($res1) {
            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
        } else {
            $pattern1 = '/<!--\s*CSG\s*INDICATOR\s*END\s*-->(.*)<!--\s*Begin:\s*AuctivaCounter/smUi';
            $res1 = preg_match_all($pattern1, $description, $result1);
            if ($res1) {
                $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
            } else {
                $pattern1 = '/<!--\s*Start\s*Description\s*AucLister\s*-->(.*)<!--\s*End\s*Description\s*AucLister/smUi';
                $res1 = preg_match_all($pattern1, $description, $result1);
                if ($res1) {
                    $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                } else {
                    return array("status" => "fail", "message" => "RegEx not matched.");
                }
            }
        }
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 17144) {
        $pattern1 = '/<div\s*id="inkfrog_crosspromo_top"[^>]*>\s*<\/div>\s*<div\s*id="inkfrog_crosspromo_top"[^>]*>\s*<\/div>(.*)<div\s*id="inkfrog_crosspromo_bottom"/smUi';
        $res1 = preg_match_all($pattern1, $description, $result1);
        if ($res1) {
            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
        } else {
            $pattern1 = '/<div\s*id="inkfrog_crosspromo_top"[^>]*>\s*<\/div>\s*<div\s*id="inkfrog_crosspromo_top"[^>]*>\s*<\/div>(.*)/smUi';
            $res1 = preg_match_all($pattern1, $description, $result1);
            if ($res1) {
                $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
            }
        }
        $description = removeHTMLFormatting($description, false);
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 16687 || $user_id ==  23188) {
        $description = "";
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 2961) {
        $conditionStr = "";
        if (strlen($condition_val) > 0) {
            $conditionStr = $conditionStr . "<p><strong>Condition:</strong> " . $condition_val . "</p>";
        }
        if (strlen($condition_note) > 0) {
            $conditionStr = $conditionStr . "<p><strong>Condition Description:</strong> " . $condition_note . "</p>";
        }
        $description = $conditionStr . $description;
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 17173  || $user_id == 21129  ) {
        echo "here i am tedting";
        $itemSpecificJson = json_decode($item_specific, true);
        $i = 0;
        $itemSpecsStr = '<table class="desctable"><tr>';
        foreach ($itemSpecificJson as $k => $v) {
            $itemSpecsStr = $itemSpecsStr . "<td><strong>" . $k . "</strong>: " . $v . "</td>";
            $i++;
            if ($i == 2) {
                $itemSpecsStr = $itemSpecsStr . "</tr><tr>";
                $i = 0;
            }
        }
        if ($i == 0) {
            $itemSpecsStr = $itemSpecsStr . "<td></td><td></td>";
        }
        if ($i == 1) {
            $itemSpecsStr = $itemSpecsStr . "<td></td>";
        }
        $itemSpecsStr = $itemSpecsStr . "</tr></table>";
        //print_r($description);
        $description = removeHTMLFormatting($description, false);
        //$cnt = substr_count($description, "<div");
        //$description = str_replace_n("<div", '<div class="descdiv' . $i . '"', $description, $i);
        
        /*for ($i = 1; $i <= $cnt; $i++) {
             
            if( $i ==1 ){
              $description = str_replace('<div ', '<div class = "prodhead" ', $description);
             }
            if( $i ==3 ){
              $description = str_replace('<div ', '<div class = "prodpara" ', $description);
             }
            if( $i ==4 ){
              $description = str_replace('<div ', '<div class = "proddet" ', $description);
             }
           if( $i == 5 ){
              $description = str_replace('<div ', '<div class = "desctable" ', $description);
             }      
            else {
              $description = str_replace_n("<div", '<div class="descdiv' . $i . '"', $description, $i);
             }   
        }*/


        
          

        $description = $itemSpecsStr . $description;
       // print_r($description);
        //exit(0);
        return array("status" => "success", "message" => $description);
    }else if ($user_id == 16834) {
        $str = "";
        $existingItemJson = json_decode($item_specific, true);
        if (isset($existingItemJson["ItemCompatibilityList"])) {
            $itemCompatibilityListObj = $existingItemJson["ItemCompatibilityList"];
            $str = '<p><strong>Vehicle Compatibility</strong></p><table cellpadding="4" cellspacing="4">';
            foreach ($itemCompatibilityListObj as $v) {
                $str = $str . "<tr><td>" . $v["Year"] . "</td><td>" . $v["Make"] . "</td><td>" . $v["Model"] . "</td><td>" . $v["Trim"] . "</td><td>" . $v["Engine"] . "</td></tr>";
            }
            $str = $str . "</table>";
        }
    
        $pattern1 = '/<\/table>\s*<table[^>]*>\s*<tbody>\s*<tr>\s*<td[^>]*>(.*)<\/td>/smUi';
        $res1 = preg_match_all($pattern1, $description, $result1);
        if ($res1) {
            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
        } else {
            $pattern1 = '/(.*)<p>\s*<strong>\s*<span[^>]*>\s*Our\s*process/smUi';
            $res1 = preg_match_all($pattern1, $description, $result1);
            if ($res1) {
                $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
            } else {//<p>(.*)We\sship\sfast
              $pattern1 = '/(.*)We\sship\sfast/smUi';
              $res1 = preg_match_all($pattern1, $description, $result1);
            if ($res1) {
                $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
            } else {
                 $pattern1 = '/(.*)<p[^>]*>.*Our\s*process/smUi';
              $res1 = preg_match_all($pattern1, $description, $result1);
            if ($res1) {
                $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
            } else {
                return array("status" => "fail", "message" => "RegEx not matched.");
             }


                  //(.*)<p[^>]*>.*Our\s*process

                 
             }    
                

                
            }
        }
        $description = removeHTMLFormatting($description, true);
        $description = $description . $str;
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 16968) {
        $description = removeHTMLFormatting($description, true);
        $pattern1 = '/erhalten\s*hier\s*[<br>]*\s*<\/p>(.*)<p\s*class="MsoNormal"[^>]*>\s*Versand\s*Info/smUi';
        $res1 = preg_match_all($pattern1, $description, $result1);
        if ($res1) {
            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
        } else {
            $pattern1 = '/erhalten\s*hier\s*ein[e]*[n]*\s*[<br>]*\s*<\/p>(.*)<p\s*class="MsoNormal"[^>]*>\s*Versand\s*Info/smUi';
            $res1 = preg_match_all($pattern1, $description, $result1);
            if ($res1) {
                $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
            } else {
                //   return array("status" => "fail", "message" => "RegEx not matched.");
            }
        }
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 17221) {
        $pattern = '/<!--\s*CSG\s*INDICATOR\s*END\s*-->(.*)<!--\s*Begin:\s*AuctivaCounter/sm';
        $res = preg_match_all($pattern, $description, $result);
        if ($res) {
            $description = isset($result[1][0]) ? trim($result[1][0]) : "";
        } else {
            return array("status" => "fail", "message" => "RegEx not matched.");
        }
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 16810) {
        $pattern1 = '/<div\s*id="sidebar">(.*)<\/div>\s*<div\s*id="content"\s*class="btcf"/smUi';
        $res1 = preg_match_all($pattern1, $description, $result1);
        if ($res1) {
            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
        } else {
            return array("status" => "fail", "message" => "RegEx not matched.");
        }
        $description = removeHTMLFormatting($description, false);
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 16763) {
        $pattern = '/<section\s*class="panel panel-default"\s*id="JarMainDesc">(.*)<\/section>/ismU';
        $res = preg_match_all($pattern, $description, $result);
        if ($res) {
            $description = isset($result[1][0]) ? trim($result[1][0]) : "";
        } else {
            // return array("status" => "fail", "message" => "RegEx not matched."); 
        }
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 16873) {
        // $title = str_ireplace("Paparazzi Jewelry", "", $title);
        //  $title = str_ireplace("New Paparazzi Jewelry", "", $title);
        // $title = trim($title);
        //return array("status" => "success", "message" => $description);
    } else if ($user_id == 17019) {
        $pattern1 = '/<div\s*id="element_569"[^>]*>(.*)<div[^>]*>\s*<div\s*id="elm_544"/smUi';
        $res1 = preg_match_all($pattern1, $description, $result1);
        if ($res1) {
            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
        } else {
            return array("status" => "fail", "message" => "RegEx not matched.");
        }
        $description = removeHTMLFormatting($description, false);
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 17012) {
        $pattern = '/<ebdescription>(.*)<\/ebdescription>/smUi';
        $res = preg_match_all($pattern, $description, $result);
        if ($res) {
            $description = isset($result[1][0]) ? trim($result[1][0]) : "";
        } else {
            // return array("status" => "fail", "message" => "RegEx not matched."); 
        }
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 16993) {
        $pattern = '/<sd_description>(.*)<\/sd_description>/smUi';
        $res = preg_match_all($pattern, $description, $result);
        if ($res) {
            $description = isset($result[1][0]) ? trim($result[1][0]) : "";
        } else {
            $pattern = '/<etbdescription>(.*)<\/etbdescription>/smUi';
            $res = preg_match_all($pattern, $description, $result);
            if ($res) {
                $description = isset($result[1][0]) ? trim($result[1][0]) : "";
            } else {
                return array("status" => "fail", "message" => "RegEx not matched.");
            }
        }
        $description = removeHTMLFormatting($description, false);
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 16728) {
        $pattern1 = '/<!--\s*Block\s*Description\s*\/\/\s*-->(.*)<!--\s*\/\/\s*Block\s*Description/smUi';
        $res1 = preg_match_all($pattern1, $description, $result1);
        if ($res1) {
            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
        } else {
            $pattern1 = '/<h2\s*class="sectionHeader"\s*id="descHeader">\s*<span>\s*Description\s*<\/span>\s*<\/h2>(.*)<div\s*class="section"\s*id="payment"/smUi';
            $res1 = preg_match_all($pattern1, $description, $result1);
            if ($res1) {
                $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
            } else {
                $pattern1 = '/<froodescription>(.*)<\/froodescription>/smUi';
                $res1 = preg_match_all($pattern1, $description, $result1);
                if ($res1) {
                    $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                } else {
                    return array("status" => "fail", "message" => "RegEx not matched.");
                }
            }
        }
        $description = removeHTMLFormatting($description, false);
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 16685) {
        $pattern1 = '/<div\s*class="panel\s*panel-description">.*<body>(.*)<\/body>\s*<\/html>/smUi';
        $res1 = preg_match_all($pattern1, $description, $result1);
        if ($res1) {
            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
        } else {
            $pattern1 = '/(<div\s*class="panel\s*panel-description">.*)<div\s*class="tabarea"/smUi';
            $res1 = preg_match_all($pattern1, $description, $result1);
            if ($res1) {
                $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
            } else {
               // return array("status" => "fail", "message" => "RegEx not matched.");
            }
        }
        $description = removeHTMLFormatting($description, false);
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 16650) {
        $desc1 = "";
        $desc2 = "";
        $pattern1 = '/(<ul\s*class="specs">.*<\/ul>)/smUi';
        $res1 = preg_match_all($pattern1, $description, $result1);
        if ($res1) {
            $desc1 = isset($result1[1][0]) ? trim($result1[1][0]) : "";
            $pattern1 = '/<div\s*class="data-table">\s*<h2>\s*Item\s*Description\s*<\/h2>\s*<\/div>(.*<\/ul>\s*<\/div>\s*<\/div>)/smUi';
            $res1 = preg_match_all($pattern1, $description, $result1);
            if ($res1) {
                $desc2 = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                $description = $desc1 . $desc2;
            }
        }
        $itemSpecificStr = "";
        $item_specificObj = json_decode($item_specific, true);
        foreach ($item_specificObj as $k => $v) {
            $itemSpecificStr = $itemSpecificStr . '<p><strong>' . $k . ':</strong> ' . $v . '</p>';
        }
        $description = removeHTMLFormatting($description, false);
        $description = $itemSpecificStr . $description;
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 10305) {
        $pattern1 = '/<!--\s*Block\s*Description\s*\/\/\s*-->(.*)<!--\s*\/\/\s*Block\s*Description/ismU';
        $res1 = preg_match_all($pattern1, $description, $result1);
        if ($res1) {
            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
        } else {
            $pattern1 = '/<div\s*class="content__description\s*mb0">(.*)<\/div>\s*<\/div>\s*<div\s*class="col\s*col-md-12\s*order-2"/ismU';
            $res1 = preg_match_all($pattern1, $description, $result1);
            if ($res1) {
                $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
            } else {
                //	return array("status" => "fail", "message" => "RegEx not matched.");      
            }
        }
        $description = removeHTMLFormatting($description, false);
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 16647) {
        $pattern = '/<div\s*class="desc-rd\s*description_text"[^>]*>(.*)<\/div>\s*<!--\s*Tabs/smUi';
        $res = preg_match_all($pattern, $description, $result);
        if ($res) {
            $description = isset($result[1][0]) ? trim($result[1][0]) : "";
        } else {
            $pattern = '/<div class="desc-rd\s*description_text"[^>]*>(.*)<!--\s*Tabs/smUi';
            $res = preg_match_all($pattern, $description, $result);
            if ($res) {
                $description = isset($result[1][0]) ? trim($result[1][0]) : "";
            } else {

                  $pattern = '/<table\s*[^>]*>(.*)<!--STARTFROOGALLERY-->/smUi';
            $res = preg_match_all($pattern, $description, $result);
            if ($res) {
                $description = isset($result[1][0]) ? trim($result[1][0]) : "";
            } else {
                return array("status" => "fail", "message" => "RegEx not matched.");
                 }



               
            }
        }
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 48427) {
        $description = removeHTMLFormatting($description, false);

    
        $pattern1 = '/(.*)<p[^>]*>\s*<i[^>]*>\s*<span[^>]*>\s*Please\s*view\s*our\s*other\s*listings\s*for\s*more\s*fine/ismU';
        $res1 = preg_match_all($pattern1, $description, $result1);
        if ($res1) {
            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
        } else {
            $pattern1 = '/<!-- Product Details -->.*<\/h1>(.*)<[p|div][^>]*>\s*Please\s*view\s*our\s*other\s*listings/ismU';
            $res1 = preg_match_all($pattern1, $description, $result1);
            if ($res1) {
                $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
            } else {
                $pattern1 = '/<!-- Product Details -->.*<\/h1>(.*)<p[^>]*>\s*<span[^>]*>\s*All\s*our\s*jewellery\s*is\s*posted\s*safely/ismU';
                $res1 = preg_match_all($pattern1, $description, $result1);
                if ($res1) {
                    $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                } else {
                    $pattern1 = '/(.*)<p[^>]*>\s*<i[^>]*>\s*Please\s*view\s*our\s*other\s*listings\s*for\s*more\s*fine/ismU';
                    $res1 = preg_match_all($pattern1, $description, $result1);
                    if ($res1) {
                        $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                    } else {
                   $pattern1 = '/<!-- Product Details -->.*<\/h1>(.*)[<[p|div][^>]*>\s*All\s*our\s*jewellery\s*is\s*posted\s*safely/ismU';
                        $res1 = preg_match_all($pattern1, $description, $result1);
                        if ($res1) {
                            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                        } else {
                            $pattern1 = '/(.*)<p[^>]*>\s*Our\s*stock\s*is\s*stored\s*offsite/ismU';
                            $res1 = preg_match_all($pattern1, $description, $result1);
                            if ($res1) {
                                $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                            } else {

            $pattern1 = '/<!-- Product Details -->.*<\/title>(.*)<[p|div][^>]*>\s*Please\s*view\s*our\s*other\s*listings/ismU';
            $res1 = preg_match_all($pattern1, $description, $result1);
            if ($res1) {
                $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
            } else {
                     
                     $pattern1 = '/(.*)<[p|div][^>]*>\s*Please\s*view\s*our\s*other\s*listings/ismU';
            $res1 = preg_match_all($pattern1, $description, $result1);
            if ($res1) {
                $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                //echo "matched here";
                //exit(0);  
            } else {

                 $pattern1 = '/<!-- Product Details -->(.*)<!-- Footer -->/ismU';
            $res1 = preg_match_all($pattern1, $description, $result1);
            if ($res1) {
                $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                //echo "matched here";
                //exit(0);  
            } else {
                   //print_r ($description);
                   $pattern1 = '/(.*)<font[^>]*>\s*Please\s*view\s*our\s*other\s*listings\s*for\s*more\s*fi/ismU';
                   $res1 = preg_match_all($pattern1, $description, $result1);
                   if ($res1) {
                       $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                       //echo "matched here";
                       //exit(0);  
                   } else {
                    //print_r ($description);
                    $pattern1 = '/(.*)<div[^>]*>\s*All\s*our\s*jewellery\s*is\s*posted\s*safely\s*and \s*securely\s*in\s*leatherette\s*boxes/ismU';
                    $res1 = preg_match_all($pattern1, $description, $result1);
                    if ($res1) {
                        $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                        //echo "matched here";
                        //exit(0);  
                    } else {
                        $pattern1 = '/<div\sstyle="padding: 0 16px;"><div\sdata-element="productDescription">(.*)<\/span><\/p><p\sstyle="">/mU';
                        $res1 = preg_match_all($pattern1, $description, $result1);
                        if ($res1) {
                            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                            //echo "matched here";
                            //exit(0);  
                        }
                        else{
                            $pattern1 = '/<div>(.*)<\/div>/m';
                            $res1 = preg_match_all($pattern1, $description, $result1);
                            if ($res1) {
                                $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                                //echo "matched here";
                                //exit(0);  
                            }
                            else{

                            return array("status" => "fail", "message" => "RegEx not matched.");
                            }
                        }
                   
                      }
                     }
              }



             //
             
                }
  
                    
                    }


                               
                            }
                        }
                    }
                }
            }
        }
        return array("status" => "success", "message" => $description);
        /*
        //$description = removeHTMLFormatting($description, false);
        
		$pattern1 = '/(.*)<p[^>]*>\s*<i[^>]*>\s*<span[^>]*>\s*Please\s*view\s*our\s*other\s*listings\s*for\s*more\s*fine/ismU';	
       	$res1 = preg_match_all($pattern1, $description, $result1);				
       	if($res1) {						
       		$description = isset($result1[1][0])?trim($result1[1][0]):"";				
        } else {
            $pattern1 = '/(.*)<p[^>]*>\s*Please\s*view\s*our\s*other\s*listings\s*for\s*more\s*fine/ismU';	
           	$res1 = preg_match_all($pattern1, $description, $result1);				
           	if($res1) {						
           	    $description = isset($result1[1][0])?trim($result1[1][0]):"";				
            } else {
               	$pattern1 = '/(.*)<p[^>]*>\s*<span[^>]*>\s*All\s*our\s*jewellery\s*is\s*posted\s*safely/ismU';	
               	$res1 = preg_match_all($pattern1, $description, $result1);				
               	if($res1){						
               	    $description = isset($result1[1][0])?trim($result1[1][0]):"";				
                } else{
                    $pattern1 = '/(.*)<p[^>]*>\s*<i[^>]*>\s*Please\s*view\s*our\s*other\s*listings\s*for\s*more\s*fine/ismU';		
                   	$res1 = preg_match_all($pattern1, $description, $result1);				
                   	if($res1){						
                   		$description = isset($result1[1][0])?trim($result1[1][0]):"";				
                    } else{
                        $pattern1 = '/(.*)<p[^>]*>\s*All\s*our\s*jewellery\s*is\s*posted\s*safely/ismU';	
                    	$res1 = preg_match_all($pattern1, $description, $result1);				
                        if($res1){						
                       		$description = isset($result1[1][0])?trim($result1[1][0]):"";				
                        } else{
                            $pattern1 = '/(.*)<p[^>]*>\s*<span[^>]*>\s*Please\s*view\s*our\s*other\s*listings\s*for\s*more\s*fine/ismU';	
                        	$res1 = preg_match_all($pattern1, $description, $result1);				
                        	if($res1) {						
                        		$description = isset($result1[1][0])?trim($result1[1][0]):"";				
                           	} else{
                          	    $pattern1 = '/(.*)<p[^>]*>\s*All\s*our\s*products\s*are\s*tested/ismU';	
                               	$res1 = preg_match_all($pattern1, $description, $result1);				
                               	if($res1){						
                               	    $description = isset($result1[1][0])?trim($result1[1][0]):"";				
                                } else {
                                    $pattern1 = '/(.*)<div[^>]*>\s*All\s*our\s*products\s*are\s*tested/ismU';	
                                   	$res1 = preg_match_all($pattern1, $description, $result1);				
                                   	if($res1){						
                                   		$description = isset($result1[1][0])?trim($result1[1][0]):"";				
                                    } else{

                                        $pattern1 = '/(.*)span[^>]*>\s*Please\s*view\s*our\s*other\s*listings\s*for\s*more\s*fine/ismU';	
                                   	$res1 = preg_match_all($pattern1, $description, $result1);				
                                   	if($res1){						
                                   		$description = isset($result1[1][0])?trim($result1[1][0]):"";				
                                    } else{
                                          return array("status" => "fail", "message" => "RegEx not matched.");
                                       }  



                                       //

                                        
                                    }
                                }
                            }
                        }
               	    }
              	}
            }
       	}
       	$description = preg_replace('#<p>\s*<br[^>]*>\s*</p>#is', '', $description);
        $description = preg_replace('#<p>\s*<br[^>]*>\s*<br[^>]*>\s*</p>#is', '', $description);
        $description = preg_replace('#<p>\s*&nbsp;\s*&nbsp;\s*</p>#is', '', $description);
        $description = preg_replace('#<p>\s*&nbsp;\s*</p>#is', '', $description);
        $description = preg_replace('#<p[^>]*>\s*</p>#is', '', $description); 
        $description = removeHTMLFormatting($description, false);
        $description = preg_replace("/<div[^>]*>/", "<p>", $description);
        $description = str_ireplace("</div>", "</p>", $description);
        return array("status" => "success", "message" => $description);*/
    }
    
    else if ($user_id == 9188) {
        $description = removeHTMLFormatting($description, false);
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 15126) {
        $pattern1 = '/<div\s*class="content__description\s*mb0"[^>]*>(.*)<\/div>\s*<\/div>\s*<div\s*class="col\s*col-md-12\s*order-2/ismU';
        $res1 = preg_match_all($pattern1, $description, $result1);
        if ($res1) {
            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
        } else {
            return array("status" => "fail", "message" => "RegEx not matched.");
        }
        $description = removeHTMLFormatting($description, false);
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 16283) {
        $conditionStr = "";
        if (strlen($condition_val) > 0) {
            $conditionStr = $conditionStr . "<p><strong>Condition:</strong> " . $condition_val . "</p>";
        }
        $description = $conditionStr . $description;
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 10537) {
        $description = "<p>Same Day Dispatch</p><p>Fast & Free Shipping</p><p>1st Class Option available</p><p>Trusted Seller</p>";
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 11433) {
        $description = removeHTMLFormatting($description, false);
        $pattern = '/Description:<\/p>(.*)<\/table>/iUms';
        $res1 = preg_match_all($pattern, $description, $result1);
        if ($res1) {
            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
        } else {
            return array("status" => "fail", "message" => "RegEx not matched.");
        }
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 16420) {
        $pattern1 = '/<div\s*class="r_title"\s*id="des"[^>]*>\s*Description\s*<\/div>(.*)<main/ismU';
        $res1 = preg_match_all($pattern1, $description, $result1);
        if ($res1) {
            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
        } else {
            return array("status" => "fail", "message" => "RegEx not matched.");
        }
        $description = removeHTMLFormatting($description, false);
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 14530) {
        $description = removeHTMLFormatting($description, false);
        $pattern1 = '/(.*)<div><div>_____________________/ismU';
        $res1 = preg_match_all($pattern1, $description, $result1);
        if ($res1) {
            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
        } else {
            $pattern1 = '/(.*)<div>_____________________/ismU';
            $res1 = preg_match_all($pattern1, $description, $result1);
            if ($res1) {
                $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
            } else {
                return array("status" => "fail", "message" => "RegEx not matched.");
            }
        }
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 16434) {
        $description = "<p>Please see all photos as this is the exact bundle for sale, all images show the items that will be included.</p><p>The items are used but in good condition.</p><p>Delivery on this bundle is FREE OF CHARGE.</p>";
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 16333) {
        $pattern1 = '/mini-title-technical\.gif"[^>]+>\s*<\/p>(.*)<\/td>/ismU';
        $res1 = preg_match_all($pattern1, $description, $result1);
        if ($res1) {
            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
        } else {
            $pattern1 = '/<table\s*class="product-text-desc"[^>]+>\s*<tbody>\s*<tr>\s*<td[^>]+>(.*)<\/td>/ismU';
            $res1 = preg_match_all($pattern1, $description, $result1);
            if ($res1) {
                $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
            } else {
                //	return array("status" => "fail", "message" => "RegEx not matched.");			
            }
        }
        $description = removeHTMLFormatting($description, false);
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 16320) {
        $description = removeHTMLFormatting($description, true);
        $pattern = '/(.*)<p>\s*CONTACT\s*US/Usmi';
        $res1 = preg_match_all($pattern, $description, $result1);
        if ($res1) {
            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
        } else {
            return array("status" => "fail", "message" => "RegEx not matched.");
        }
        $description = removeHTMLFormatting($description, false);
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 10564) {
        $conditionStr = "";
        if (strlen($condition_val) > 0) {
            $conditionStr = $conditionStr . "<p><strong>Condition:</strong> " . $condition_val . "</p>";
        }
        if (strlen($condition_note) > 0) {
            $conditionStr = $conditionStr . "<p><strong>Condition Description:</strong> " . $condition_note . "</p>";
        }
        $description = $conditionStr . $description;
        $item_specificObj = json_decode($item_specific, true);
        foreach ($item_specificObj as $k => $v) {
            if ($k == "Manufacturer Part Number" && $v != 'Does Not Apply') {
                $description = $description . '<p><strong>' . $k . ':</strong> ' . $v . '</p>';
            }
        }
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 16055) {
        $pattern = '/<!--\s*Begin\s*Description\s*-->(.*)<!--\s*End\s*Description\s*-->/sm';
        $res = preg_match_all($pattern, $description, $result);
        if ($res) {
            $description = isset($result[1][0]) ? trim($result[1][0]) : "";
        } else {
            $pattern = '/<!--\s*Begin\s*Description\s*-->(.*)<div[^>]+>/sm';
            $res = preg_match_all($pattern, $description, $result);
        if ($res) {
            $description = isset($result[1][0]) ? trim($result[1][0]) : "";
        } else {
            return array("status" => "fail", "message" => "RegEx not matched.");
            }
         // <!--\s*Begin\s*Description\s*-->(.*)<div[^>]+>  
           
        }
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 3149) {
        $pattern = '/<!--\s*Begin\s*Description\s*-->\s*<!--\s*Begin\s*Description\s*-->(.*)<\/div>\s*<\/div>\s*<\/div>\s*<div\s*class="essTabs\s*mdl-Box"[^>]*>/sm';
        $res1 = preg_match_all($pattern, $description, $result1);
        if ($res1) {
            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
            $description = preg_replace("/<img[^>]+\>/i", "", $description);
        } else {
            $pattern = '/<!--\s*Begin\s*Description\s*-->\s*<!--\s*Begin\s*Description\s*-->(.*)<\/div>\s*<\/div>\s*<\/div>/sm';
            $res1 = preg_match_all($pattern, $description, $result1);
            if ($res1) {
                $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
            } else {
                $pattern = '/<div\s*id="inkfrog_crosspromo_top"[^>]*>\s*<\/div>\s*<link[^>]+>(.*)<div\s*id="inkfrog_crosspromo_bottom"/sm';
                $res1 = preg_match_all($pattern, $description, $result1);
                if ($res1) {
                    $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                } else {
                    return array("status" => "fail", "message" => "RegEx not matched.");
                }
            }
        }
        $description = removeHTMLFormatting($description, false);
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 15970) {
        $conditionStr = "";
        if (strlen($condition_note) > 0) {
            $conditionStr = '<div class="slrmsg">' . $condition_note . "</div>";
        }
        $description = $conditionStr . '<div class="ebaydesc">' . $description . '</div>';
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 177) {
        $pattern1 = '/data-cl-template-tag="description">(.*)<\/div>\s*<\/div>\s*<div[^>]*>\s*<div.*data-element-type="editor.elements.ImageElement">/ismU';
        $res1 = preg_match_all($pattern1, $description, $result1);
        if ($res1) {
            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
        } else {
            return array("status" => "fail", "message" => "RegEx not matched.");
        }
        $description = removeHTMLFormatting($description, false);
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 7021) {
        $description = '';
        $item_specificObj = json_decode($item_specific, true);
        foreach ($item_specificObj as $k => $v) {
            $description = $description . '<p><strong>' . $k . ':</strong> ' . $v . '</p>';
        }
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 14590) {
        $pattern1 = '/<div\s*id="tab-content1"\s*class="tab-content"[^>]+>(.*)<\/div>\s*<div\s*id="tab-content2"\s*class="tab-content"/ismU';
        $res1 = preg_match_all($pattern1, $description, $result1);
        if ($res1) {
            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
        } else {
            return array("status" => "fail", "message" => "RegEx not matched.");
        }
        $description = removeHTMLFormatting($description, false);
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 15750) {
        $description = removeHTMLFormatting($description, false);
        $pattern = '/(.*)<div>\s*<p\s*class="MsoNormal"\s*>\s*<b>\s*<u>\s*Thank\s*you\s*for\s*stopping\s*by\s*and\s*taking/sm';
        $res = preg_match_all($pattern, $description, $result);
        if ($res) {
            $description = isset($result[1][0]) ? trim($result[1][0]) . "</div>" : "";
        } else {
            //   return array("status" => "fail", "message" => "RegEx not matched.");
        }
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 11771 || $user_id == 18281) {
        $pattern = '/<froodescription[^>]*>(.*)<\/froodescription>/sm';
        $res = preg_match_all($pattern, $description, $result);
        if ($res) {
            $description = isset($result[1][0]) ? trim($result[1][0]) : "";
        } else {
            // return array("status" => "fail", "message" => "RegEx not matched.");
        }
        $description = removeHTMLFormatting($description, true);
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 13915 || $user_id == 12655 || $user_id == 17065 || $user_id == 16763 || $user_id == 15057 || $user_id == 15631 || $user_id == 16623) {
        $description = removeHTMLFormatting($description, true);
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 15600) {
        $pattern1 = '/<div\s*class="description[^>]*>(.*)<\/div>/ismU';
        $res1 = preg_match_all($pattern1, $description, $result1);
        if ($res1) {
            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
        } else {

            $pattern1 = '/<div class="main_section">(.*)Shipping/ismU';
            $res1 = preg_match_all($pattern1, $description, $result1);
            if ($res1) {
                $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
            } else {


                $pattern1 = '/<span property="description">(.*)<\/div>/ismU';
                $res1 = preg_match_all($pattern1, $description, $result1);
                if ($res1) {
                    $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                } else {

                    
                $pattern1 = '/<div class="description col-xs-12">(.*)Payment<\/h2>/ismU';
                $res1 = preg_match_all($pattern1, $description, $result1);
                if ($res1) {
                    $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                } else {
                    return array("status" => "fail", "message" => "RegEx not matched."); 
                }
                   
                }
                



                //
               }



           
            
        }
        $description = removeHTMLFormatting($description, true);
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 15236) {
        $desc1 = "";
        $desc2 = "";
        $pattern1 = '/<div\s*class="top-right">(.*)<\/div>/ismU';
        $res1 = preg_match_all($pattern1, $description, $result1);
        if ($res1) {
            $desc1 = isset($result1[1][0]) ? trim($result1[1][0]) : "";
        } else {
            return array("status" => "fail", "message" => "RegEx not matched.");
        }
        $pattern1 = '/<div\s*class="section">(.*)<\/div>/ismU';
        $res1 = preg_match_all($pattern1, $description, $result1);
        if ($res1) {
            $desc2 = isset($result1[1][0]) ? trim($result1[1][0]) : "";
        } else {
            return array("status" => "fail", "message" => "RegEx not matched.");
        }
        $description = $desc1 . $desc2;
        $description = removeHTMLFormatting($description, true);
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 15459) {
        $description = removeHTMLFormatting($description, true);
        $pattern1 = '/<!--\s*Start\s*Description\s*AucLister\s*-->(.*)<!--\s*End\s*Description\s*AucLister\s*-->/ismU';
        $res1 = preg_match_all($pattern1, $description, $result1);
        if ($res1) {
            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
        } else {
            $pattern1 = '/<!--\s*Block\s*Description\s*\/\/\s*-->(.*)<!--\s*\/\/\s*Block\s*Description\s*-->/ismU';
            $res1 = preg_match_all($pattern1, $description, $result1);
            if ($res1) {
                $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
            } else {
                $pattern1 = '/<div[^>]*>\s*Description\s*<\/div>(.*)Thankyou/ismU';
                $res1 = preg_match_all($pattern1, $description, $result1);
                if ($res1) {
                    $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                } else {
                    $pattern1 = '/desc\.gif"[^>]*>(.*)Thankyou/ismU';
                    $res1 = preg_match_all($pattern1, $description, $result1);
                    if ($res1) {
                        $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                    } else {
                        $pattern1 = '/<main[^>]*>(.*)<\/main>/ismU';
                        $res1 = preg_match_all($pattern1, $description, $result1);
                        if ($res1) {
                            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                        } else {
                            $pattern1 = '/(.*)<!--\s*Begin:\s*AuctivaCounter\s*-->/ismU';
                            $res1 = preg_match_all($pattern1, $description, $result1);
                            if ($res1) {
                                $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                            } else {
                                $pattern1 = '/(.*)<!--\s*CSG\s*INDICATOR\s*START\s*-->/ismU';
                                $res1 = preg_match_all($pattern1, $description, $result1);
                                if ($res1) {
                                    $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                                } else {
                                    return array("status" => "fail", "message" => "RegEx not matched.");
                                }
                            }
                        }
                    }
                }
            }
        }
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 15332) {
        $pattern1 = '/<description>(.*)<\/description>/ismU';
        $res1 = preg_match_all($pattern1, $description, $result1);
        if ($res1) {
            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
        } else {
            return array("status" => "fail", "message" => "RegEx not matched.");
        }
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 15336) {
        $pattern1 = '/<div\s*class="content__description\s*mb0"[^>]*>(.*)<\/div>\s*<\/div>\s*<div\s*class="col\s*col-md-12\s*order-2"/ismU';
        $res1 = preg_match_all($pattern1, $description, $result1);
        if ($res1) {
            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
        } else {
            return array("status" => "fail", "message" => "RegEx not matched.");
        }
        $description = removeHTMLFormatting($description, true);
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 15153) {
        $pattern1 = "/<div\s*class='right-panel'>(.*)<div\s*class='price-box'>/ismU";
        $res1 = preg_match_all($pattern1, $description, $result1);
        if ($res1) {
            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
        } else {
            $pattern1 = '/<div\s*class="right-panel">(.*)<div\s*class="price-box">/ismU';
            $res1 = preg_match_all($pattern1, $description, $result1);
            if ($res1) {
                $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
            } else {
                return array("status" => "fail", "message" => "RegEx not matched.");
            }
        }
        $description = removeHTMLFormatting($description, true);
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 9266) {
        $description = preg_replace("/<!--\s*VISIT\s*OUR\s*EBAY\s*STORE\s*LINK\s*-->.*<!--\s*FOOTER\s*-->/ismU", "", $description);
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 4437) {
        $pattern1 = '/<!--\s*Description\s*Starts\s*-->(.*)<!--\s*Description\s*Ends\s*-->/smU';
        $res1 = preg_match_all($pattern1, $description, $result1);
        if ($res1) {
            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
        } else {
            return array("status" => "fail", "message" => "RegEx not matched.");
        }
        $description = removeHTMLFormatting($description, true);
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 14950) {
        $description = removeHTMLFormatting($description, true);
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 15006) {
        $description = removeHTMLFormatting($description, true);
        $pattern1 = '/<h2\s*data-ux="SectionHeading"\s*data-aid="CONTACT_SECTION_TITLE_REND"[^>]+>(.*)<\/h2>/smU';
        $res1 = preg_match_all($pattern1, $description, $result1);
        if ($res1) {
            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
        } else {
            return array("status" => "fail", "message" => "RegEx not matched.");
        }
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 14611) {
        $pattern1 = '/<!--\s*qui\s*descrizione\s*-->(.*)<\/td>/smU';
        $res1 = preg_match_all($pattern1, $description, $result1);
        if ($res1) {
            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
        } else {
            $pattern1 = '/<div\s*id="description">(.*)<div\s*class="areabasedes">/smU';
            $res1 = preg_match_all($pattern1, $description, $result1);
            if ($res1) {
                $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
            } else {
                //	return array("status" => "fail", "message" => "RegEx not matched.");          
            }
        }
        $description = removeHTMLFormatting($description, true);
        $description = preg_replace("/SI\s*AVVISA\s*LA\s*GENTILE\s*CLIENTELA.*la\s*Gomera,\s*el\s*Hierro/smiU", "", $description);
        $description = preg_replace("/SI\s*AVVERTE\s*LA\s*GENTILE.*EFFETTUATA\s*CON\s*POSTE\s*ITALIANE/smiU", "", $description);
        $description = preg_replace('#(<br */?>\s*)+#i', '<br />', $description);
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 14614) {
        $conditionStr = "";
        if (strlen($condition_val) > 0) {
            $conditionStr = $conditionStr . "<p><strong>Condition:</strong> " . $condition_val . "</p>";
        }
        if (strlen($condition_note) > 0) {
            $conditionStr = $conditionStr . "<p><strong>Condition Description:</strong> " . $condition_note . "</p>";
        }
        $itemSpecArr = json_decode($item_specific, true);
        foreach ($itemSpecArr as $k => $v) {
            $conditionStr = $conditionStr . '<p><strong>' . $k . ':</strong> ' . $v . '</p>';
        }
        $description = $conditionStr;
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 14494) {
        $pattern1 = '/<!--\s*DESCRIPTION\s*-->(.*)<!--\s*DESCRIPTION\s*-->/smU';
        $res1 = preg_match_all($pattern1, $description, $result1);
        if ($res1) {
            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
        } else {
            $pattern1 = '/<!--\s*DESCRIPTION\s*-->(.*)<\/span>\s*<\/td>/smU';
            $res1 = preg_match_all($pattern1, $description, $result1);
            if ($res1) {
                $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
            } else {

                $pattern1 = '/<br><p>(.*)<\/tbody><\/table>/smU';
                $res1 = preg_match_all($pattern1, $description, $result1);
                if ($res1) {
                    $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                } else {

                    $pattern1 = '/<div>(.*)<\/p><br>/smU';
                $res1 = preg_match_all($pattern1, $description, $result1);
                if ($res1) {
                    $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                } else {
                    return array("status" => "fail", "message" => "RegEx not matched.");

                }




                    
                }



                //<br><p>(.*)<\/tbody><\/table>
                
            }
        }
        $description = removeHTMLFormatting($description, true);
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 14473) {
        $description = removeHTMLFormatting($description, true);
        $description = preg_replace('/<a[^>]+>([^<]+)<\/a>/i', '\1', $description);
        $description = preg_replace("/\http[^,]+/", "", $description);
        $description = strip_tags($description, "<br><p><div><span><ul><li><ol>");
        $pattern1 = '/(.*)[<p[^>]*>]*\s*Free\s*Shipping/smU';
        $res1 = preg_match_all($pattern1, $description, $result1);
        if ($res1) {
            $description = isset($result1[1][0]) ? trim($result1[1][0]) . "</div>" : "";
        } else {
            $pattern1 = '/(.*)[<p[^>]*>]*\s*USPS\s*Priority\s*Mail\s*with\s*tracking\s*Shipping\s*is\s*FREE/smU';
            $res1 = preg_match_all($pattern1, $description, $result1);
            if ($res1) {
                $description = isset($result1[1][0]) ? trim($result1[1][0]) . "</div>" : "";
            } else {
                $pattern1 = '/(.*)[<p[^>]*>]*\s*USPS\s*First\s*Class\s*Mail\s*with\s*tracking\s*Shipping\s*is\s*FREE/smU';
                $res1 = preg_match_all($pattern1, $description, $result1);
                if ($res1) {
                    $description = isset($result1[1][0]) ? trim($result1[1][0]) . "</div>" : "";
                } else {
                    $pattern1 = '/(.*)[<p[^>]*>]*\s*Shipping\s*with\s*tracking\s*is\s*FREE/smU';
                    $res1 = preg_match_all($pattern1, $description, $result1);
                    if ($res1) {
                        $description = isset($result1[1][0]) ? trim($result1[1][0]) . "</div>" : "";
                    } else {
                        $pattern1 = '/(.*)[<p[^>]*>]*\s*Paypal\s*only/smU';
                        $res1 = preg_match_all($pattern1, $description, $result1);
                        if ($res1) {
                            $description = isset($result1[1][0]) ? trim($result1[1][0]) . "</div>" : "";
                        } else {

                            $pattern1 = '/(.*)[<p[^>]*>]*\s*FREE/smU';
                            $res1 = preg_match_all($pattern1, $description, $result1);
                            if ($res1) {
                                $description = isset($result1[1][0]) ? trim($result1[1][0]) . "</div>" : "";
                            } else {

                                $pattern1 = '/(.*)Must\spay/smU';
                                $res1 = preg_match_all($pattern1, $description, $result1);
                            if ($res1) {
                                $description = isset($result1[1][0]) ? trim($result1[1][0]) . "</div>" : "";
                            } else {
                                 
                                $pattern1 = '/(.*)[<p[^>]*>]*\s*Free\s*Shipping/smU';
                                $res1 = preg_match_all($pattern1, $description, $result1);
                            if ($res1) {
                                $description = isset($result1[1][0]) ? trim($result1[1][0]) . "</div>" : "";
                            } else {
                                  return array("status" => "fail", "message" => "RegEx not matched.");
                               }

                               }

                                
                            }
                            }



                            
                    }
                }
            }
        }
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 14440) {
        $description = removeHTMLFormatting($description, true);
        $description = preg_replace('/<a[^>]+>([^<]+)<\/a>/i', '\1', $description);
        $description = preg_replace("/\http[^,]+/", "", $description);
        $description = strip_tags($description, "<br><p><div><span>");
        $pattern1 = '/(Standard\s*Features.*)\*\*Please\s*see\s*each\s*picture\s*before\s*purchase.\s*If\s*not\s*pictured,\s*it\s*is\s*not/smU';
        $res1 = preg_match_all($pattern1, $description, $result1);
        if ($res1) {
            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
        } else {
            $pattern1 = '/(.*)<div>\s*About\s*Us\s*<\/div>/smU';
            $res1 = preg_match_all($pattern1, $description, $result1);
            if ($res1) {
                $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
            } else {
                $pattern1 = '/(.*)About\s*Us/smU';
                $res1 = preg_match_all($pattern1, $description, $result1);
                if ($res1) {
                    $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                } else {
                    return array("status" => "fail", "message" => "RegEx not matched.");
                }
            }
        }
        $description = preg_replace('/<div\s*class="mattblacktabs">.*<\/div>/smU', "", $description);
        print_r($description); 
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 14449) {
        $pattern1 = '/<!--\s*::::::::::::::::::::::::::::::::::::\s*item\s*description\s*starts\s*here\s*::::::::::::::::::::::::::::::::::::::::\s*-->(.*)<!--\s*::::::::::::::::::::::::::::::::::::\s*item\s*description\s*ends\s*here/smU';
        $res1 = preg_match_all($pattern1, $description, $result1);
        if ($res1) {
            $description = isset($result1[1][0]) ? trim($result1[1][0]) . "</div>" : "";
        } else {
            return array("status" => "fail", "message" => "RegEx not matched.");
        }
        $description = removeHTMLFormatting($description, false);
        $description = strip_tags($description, "<br><p><div><span>");
        $description = preg_replace('#(<br */?>\s*)+#i', '<br />', $description);
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 5001) {
        if (stripos($description, 'In order to access combined shipping') !== false) {
            $description = '';
        }
        echo "here is description ".$description;
        return array("status" => "success", "message" => $description);
    }



    
    
    
    
    
    
    
    
    else if ($user_id == 18969 ) {
        $str = "";
        $existingItemJson = json_decode($item_specific, true);
        if (isset($existingItemJson["ItemCompatibilityList"])) {
            $itemCompatibilityListObj = $existingItemJson["ItemCompatibilityList"];
            $str = '<p><strong>Vehicle Compatibility</strong></p><table cellpadding="4" cellspacing="4">';
            $str = $str . "<tr><td><strong>Year</strong></td><td><strong>Make</strong></td><td><strong>Model</strong></td><td><strong>Submodel</strong></td></tr>";
            foreach ($itemCompatibilityListObj as $v) {
                $str = $str . "<tr><td>" . $v["Year"] . "</td><td>" . $v["Make"] . "</td><td>" . $v["Model"] . "</td><td>" . $v["Submodel"] . "</td></tr>";
            }
            $str = $str . "</table>";
        }
        $conditionStr = "";
        if (strlen($condition_val) > 0) {
            $conditionStr = $conditionStr . "<p><strong>Condition:</strong> " . $condition_val . "</p>";
        }
        if (strlen($condition_note) > 0) {
            $conditionStr = $conditionStr . "<p>" . $condition_note . "</p>";
        }
        foreach ($existingItemJson as $k => $v) {
            if ($k != "ItemCompatibilityList") {
                $conditionStr = $conditionStr . '<p><strong>' . $k . ':</strong> ' . $v . '</p>';
            }
        }
        print_r($string);
        $description = $conditionStr . $str . $description;
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 14386) {
        $str = "";
        $existingItemJson = json_decode($item_specific, true);
        if (isset($existingItemJson["ItemCompatibilityList"])) {
            $itemCompatibilityListObj = $existingItemJson["ItemCompatibilityList"];
            $str = '<p><strong>Vehicle Compatibility</strong></p><table cellpadding="4" cellspacing="4">';
            foreach ($itemCompatibilityListObj as $v) {
                $str = $str . "<tr><td>" . $v["Year"] . "</td><td>" . $v["Make"] . "</td><td>" . $v["Model"] . "</td><td>" . $v["Trim"] . "</td><td>" . $v["Engine"] . "</td></tr>";
            }
            $str = $str . "</table>";
        }

        $conditionStr = "";
        if (strlen($condition_val) > 0) {
            $conditionStr = $conditionStr . "<p><strong>Condition:</strong> " . $condition_val . "</p>";
        }
        if (strlen($condition_note) > 0) {
            $conditionStr = $conditionStr . "<p>" . $condition_note . "</p>";
        }
        foreach ($existingItemJson as $k => $v) {
            if ($k != "ItemCompatibilityList") {
                $conditionStr = $conditionStr . '<p><strong>' . $k . ':</strong> ' . $v . '</p>';
            }
        }
        $description = $conditionStr . $str . $description;
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 8678) {
        $str = "";
        $existingItemJson = json_decode($item_specific, true);
        if (isset($existingItemJson["ItemCompatibilityList"])) {
            $itemCompatibilityListObj = $existingItemJson["ItemCompatibilityList"];
            $str = '<p><strong>Vehicle Compatibility</strong></p><table cellpadding="4" cellspacing="4">';
            foreach ($itemCompatibilityListObj as $v) {
                $str = $str . "<tr><td>" . $v["Year"] . "</td><td>" . $v["Make"] . "</td><td>" . $v["Model"] . "</td><td>" . $v["Trim"] . "</td><td>" . $v["Engine"] . "</td></tr>";
            }
            $str = $str . "</table>";
        }

        $conditionStr = "";
        if (strlen($condition_val) > 0) {
            $conditionStr = $conditionStr . "<p><strong>Condition:</strong> " . $condition_val . "</p>";
        }
        if (strlen($condition_note) > 0) {
            $conditionStr = $conditionStr . "<p>" . $condition_note . "</p>";
        }
        foreach ($existingItemJson as $k => $v) {
            if ($k != "ItemCompatibilityList") {
                $conditionStr = $conditionStr . '<p><strong>' . $k . ':</strong> ' . $v . '</p>';
            }
        }
        $description = $conditionStr . $str;
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 22738) {

      
        if (substr(trim($item_specific), -1) === '{') {
            // Add '}' character to complete the JSON data
            $item_specific .= '}]}';
        }
        echo "item spec".json_encode($item_specific);
        
        //print_r(json_encode($item_specific,true));
        $existingItemJson = json_decode($item_specific,true);
        //print_r("compa".json_encode($existingItemJson["ItemCompatibilityList"]));
        if (!isset($existingItemJson["ItemCompatibilityList"])) {
            return array("status" => "fail", "message" => "RegEx not matched.");
        }
        $itemCompatibilityListObj = $existingItemJson["ItemCompatibilityList"];
        $str = '<table cellpadding="4" cellspacing="4"><tr><td>Make</td><td>Model</td><td>Year</td><td>Variant</td><td>Type</td><td>Chassis</td><td>Engine</td></tr>';
        foreach ($itemCompatibilityListObj as $v) {
            $str = $str . "<tr style=\'word-wrap: break-word\'><td>" . $v["Car Make"] . "</td><td>" . $v["Model"] . "</td><td>" . $v["Cars Year"] . "</td><td>" . $v["Variant"] . "</td><td>" . $v["BodyStyle"] . "</td><td>" . $v["Cars Type"] . "</td><td>" . $v["Engine"] . "</td></tr>";
        }
        $str = $str . "</table>";
        $conditionStr = "";
        foreach ($existingItemJson as $k => $v) {
            if ($k != "ItemCompatibilityList") {
                $conditionStr = $conditionStr . '<p style=\'word-wrap: break-word\'><strong>' . $k . ':</strong> ' . $v . '</p>';
            }
        }
        $description = $conditionStr . $description . "<p><strong>Vehicle Compatibility</strong></p>" . $str;
        
        return array("status" => "success", "message" => $description);
    
    } 


    else if ($user_id == 44188) {
   //     <div\s*id="ds_div">(.*)\s*<p\s*class="MsoNormal"[^>]*><b>

   

   $pattern1 = '/<div\s*id="ds_div">(.*)\s*<p\s*class="MsoNormal"[^>]*><b>/s';
   $res1 = preg_match_all($pattern1, $description, $result1);
   if ($res1) {
         print_r($result1);
         $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
         $description = removeHTMLFormatting($description, true);
        // return array("status" => "success", "message" => $description); 
   }  else {

    $pattern1 = '/<div\s*id="ds_div">(.*)\s*<p\s*class="MsoNormal"[^>]*><img[^>]+>/sU';
    $res1 = preg_match_all($pattern1, $description, $result1);
    if ($res1) {
          print_r($result1);
          $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
          $description = removeHTMLFormatting($description, true);
         // return array("status" => "success", "message" => $description); 
    }  else {
        return array("status" => "fail", "message" => "RegEx not matched.");    
          } 

         
           //<div\s*id="ds_div">(.*)\s*<p\s*class="MsoNormal"[^>]*><img[^>]+>
          
      }


      
     
    

        $str = "";
        $existingItemJson = json_decode($item_specific, true);
        if (isset($existingItemJson["ItemCompatibilityList"])) {
            $itemCompatibilityListObj = $existingItemJson["ItemCompatibilityList"];
            $str = '<p><strong> Compatibility</strong></p><table cellpadding="4" cellspacing="4"><tr><td>Year</td><td>Make</td><td>Model</td><td>Trim</td><td>Engine</td></tr>';
            foreach ($itemCompatibilityListObj as $v) {
                $str = $str . "<tr><td>" . $v["Year"] . "</td><td>" . $v["Make"] . "</td><td>" . $v["Model"] . "</td><td>" . $v["Trim"] . "</td><td>" . $v["Engine"] . "</td></tr>";
            }
            $str = $str . "</table>";
        }

        $conditionStr = "";
        if (strlen($condition_val) > 0) {
            $conditionStr = $conditionStr . "<p><strong>Condition:</strong> " . $condition_val . "</p>";
        }
        if (strlen($condition_note) > 0) {
            $conditionStr = $conditionStr . "<p>" . $condition_note . "</p>";
        }
        foreach ($existingItemJson as $k => $v) {
            if ($k != "ItemCompatibilityList") {
                $conditionStr = $conditionStr . '<p><strong>' . $k . ':</strong> ' . $v . '</p>';
            }
        }
        $description = $conditionStr . $str .$description;

        print_r($description);
       return array("status" => "success", "message" => $description);
    }





    // else if ($user_id == 23330) {

      
    //     if (substr(trim($item_specific), -1) === '{') {
    //         // Add '}' character to complete the JSON data
    //         $item_specific .= '}]}';
    //     }
    //     echo "item spec".json_encode($item_specific);
        
    //     //print_r(json_encode($item_specific,true));
    //     $existingItemJson = json_decode($item_specific,true);
    //     //print_r("compa".json_encode($existingItemJson["ItemCompatibilityList"]));
    //     if (!isset($existingItemJson["ItemCompatibilityList"])) {
    //         return array("status" => "fail", "message" => "RegEx not matched.");
    //     }
    //     $itemCompatibilityListObj = $existingItemJson["ItemCompatibilityList"];
    //     $str = '<table cellpadding="4" cellspacing="4"><tr><td>Make</td><td>Model</td><td>Year</td><td>Variant</td><td>Type</td><td>Chassis</td><td>Engine</td></tr>';
    //     foreach ($itemCompatibilityListObj as $v) {
    //         $str = $str . "<tr style=\'word-wrap: break-word\'><td>" . $v["Year"] . "</td><td>" . $v["Make"] . "</td><td>" . $v["Model"] . "</td><td>" . $v["Trim"] . "</td><td>" . $v["Engine"] . "</td></tr>";
    //     }
    //     $str = $str . "</table>";
    //     $conditionStr = "";
    //     foreach ($existingItemJson as $k => $v) {
    //         if ($k != "ItemCompatibilityList") {
    //             $conditionStr = $conditionStr . '<p style=\'word-wrap: break-word\'><strong>' . $k . ':</strong> ' . $v . '</p>';
    //         }
    //     }
    //     $description = $conditionStr . $description . "<p><strong>Vehicle Compatibility</strong></p>" . $str;
        
    //     return array("status" => "success", "message" => $description);
    
    // } 
    







    else if ($user_id == 36620) {

        $pattern1 = '/<body>(.*)<!-- MAIN DESCRIPTION -->/smiU';
             $res1 = preg_match_all($pattern1, $description, $result1);
             if ($res1) {
                   print_r($result1);
                   $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                   $description = removeHTMLFormatting($description, true);
                  // return array("status" => "success", "message" => $description); 
             } else {
    
                $pattern1 = '/<div class="desboxmar">(.*)<\/div>/smiU';
                $res1 = preg_match_all($pattern1, $description, $result1);
                if ($res1) {
                      print_r($result1);
                      $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                      $description = removeHTMLFormatting($description, true);
                     // return array("status" => "success", "message" => $description); 
                } else {
                    return array("status" => "fail", "message" => "RegEx not matched.");    
                }
    
    
                
               
              }

      
    
        echo "item spec".json_encode($item_specific);
        

        $existingItemJson = json_decode($item_specific,true);
        //print_r("compa".json_encode($existingItemJson["ItemCompatibilityList"]));
        if (!isset($existingItemJson["ItemCompatibilityList"])) {
            return array("status" => "fail", "message" => "RegEx not matched.");
        }
        $itemCompatibilityListObj = $existingItemJson["ItemCompatibilityList"];
        $str = '<table cellpadding="4" cellspacing="4"><tr><td>Year</td><td>Make</td><td>Model</td><td>Trim</td><td>Engine</td></tr>';
        foreach ($itemCompatibilityListObj as $v) {
            $str = $str . "<tr style=\'word-wrap: break-word\'><td>" . $v["Year"] . "</td><td>" . $v["Make"] . "</td><td>" . $v["Model"] . "</td><td>" . $v["Trim"] . "</td><td>" . $v["Engine"] . "</td></tr>";

          //  print($str);
                }
        $str = $str . "</table>";
        $conditionStr = "";
        foreach ($existingItemJson as $k => $v) {
            if ($k != "ItemCompatibilityList") {
                $conditionStr = $conditionStr . '<p style=\'word-wrap: break-word\'><strong>' . $k . ':</strong> ' . $v . '</p>';
            }
        }
        $description = $conditionStr . $description . "<p><strong>Compatibility</strong></p>" . $str;

      //  print_r($description);
       // die(0);
      
        return array("status" => "success", "message" => $description);
    
    } 









    else if ($user_id == 14178) {
        $pattern1 = "/(.*)<div\s*class='row\s*panel-container'>\s*<div\s*class='col\s*col-sm-4'>\s*<div\s*class='panel\s*panel-success'>\s*<div\s*class='panel-heading'>\s*Fast\s*and\s*Free\s*Shipping/smU";
        $res1 = preg_match_all($pattern1, $description, $result1);
        if ($res1) {
            $description = isset($result1[1][0]) ? trim($result1[1][0]) . "</div>" : "";
        } else {
            $pattern1 = '/(.*)<div\s*class="row\s*panel-container">\s*<div\s*class="col\s*col-sm-4">\s*<div\s*class="panel\s*panel-success">\s*<div\s*class="panel-heading">\s*Fast\s*and\s*Free\s*Shipping/smU';
            $res1 = preg_match_all($pattern1, $description, $result1);
            if ($res1) {
                $description = isset($result1[1][0]) ? trim($result1[1][0]) . "</div>" : "";
            } else {
                $pattern1 = '/(.*)<span[^>]+>\s*CUSTOMER\s*SATISFACTION\s*GUARANTEE/smU';
                $res1 = preg_match_all($pattern1, $description, $result1);
                if ($res1) {
                    $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                } else {
                    $pattern1 = '/(.*)<div[^>]+>\s*CUSTOMER\s*SATISFACTION\s*GUARANTEE/smU';
                    $res1 = preg_match_all($pattern1, $description, $result1);
                    if ($res1) {
                        $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                    } else {
                        return array("status" => "fail", "message" => "RegEx not matched.");
                    }
                }
            }
        }
        $description = removeHTMLFormatting($description, false);
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 10773) {
        $pattern1 = '/<!--\s*CSG\s*INDICATOR\s*END\s*-->(.*)<!--\s*Begin:\s*AuctivaCounter/smU';
        $res1 = preg_match_all($pattern1, $description, $result1);
        if ($res1) {
            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
        } else {
            $pattern1 = '/<!--\s*CSG\s*INDICATOR\s*END\s*-->(.*)<div\s*align="center"\s*class="aucCounter"/smU';
            $res1 = preg_match_all($pattern1, $description, $result1);
            if ($res1) {
                $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
            } else {
                //	return array("status" => "fail", "message" => "RegEx not matched.");           
            }
        }
        $description = removeHTMLFormatting($description, false);
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 14105) {
        $pattern1 = '/<!--\s*Block\s*Description\s*\/\/\s*-->(.*)<ol>.*<!--\s*\/\/\s*Block\s*Description\s*-->/smU';
        $res1 = preg_match_all($pattern1, $description, $result1);
        if ($res1) {
            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
        } else {
            $pattern1 = '/<!--\s*CSG\s*INDICATOR\s*END-->(.*)<!--\s*Begin:\s*AuctivaCounter\s*-->/smU';
            $res1 = preg_match_all($pattern1, $description, $result1);
            if ($res1) {
                $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
            } else {
                return array("status" => "fail", "message" => "RegEx not matched.");
            }
        }
        $description = removeHTMLFormatting($description, false);
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 13716) {
        $pattern1 = '/<div\s*class="col-md-6 desc">(.*)<\/div>\s*<div\s*class="col-md-6"/smU';
        $res1 = preg_match_all($pattern1, $description, $result1);
        if ($res1) {
            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
        } else {
            $pattern1 = '/<div\s*class="right-part\s*col-md-6">(.*)<\/div>\s*<\/div>\s*<div\s*class="part">\s*<p\s*class="p-part">\s*<font[^>]*>\s*Shipping/smU';
            $res1 = preg_match_all($pattern1, $description, $result1);
            if ($res1) {
                $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
            } else {
                $pattern1 = '/<div\s*id="detail_info"\s*class="widget">(.*)<\/div>\s*<\/div>\s*<div\s*class="module\s*module_offset_fix">\s*<div\s*id="storeDescriptionTab"/smU';
                $res1 = preg_match_all($pattern1, $description, $result1);
                if ($res1) {
                    $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                } else {
                    $pattern1 = '/<div\s*id="detail_info"\s*class="widget">(.*)<\/div>\s*<\/div>\s*<\/div>\s*<\/div>\s*<\/div>\s*<\/div>\s*<div\s*class="layout footer_content"/smU';
                    $res1 = preg_match_all($pattern1, $description, $result1);
                    if ($res1) {
                        $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                    } else {
                        $pattern1 = '/<div\s*class="descp2">(.*)<\/div>/smU';
                        $res1 = preg_match_all($pattern1, $description, $result1);
                        if ($res1) {
                            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                        } else {
                            return array("status" => "fail", "message" => "RegEx not matched.");
                        }
                    }
                }
            }
        }
        $description = removeHTMLFormatting($description, true);
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 10474) {
        $pattern1 = '/Product\s*Description\s*<\/p>\s*<\/td>\s*<\/tr>\s*<tr>\s*<td\s*class="bld">(.*)<\/td>/smU';
        $res1 = preg_match_all($pattern1, $description, $result1);
        if ($res1) {
            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
        } else {
            
                return array("status" => "fail", "message" => "RegEx not matched.");
            
        }
        $description = removeHTMLFormatting($description, true);
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 13048) {
        $pattern1 = '/<div\s*class="well"\s*align="center">\s*Product\s*Description\s*<\/div>(.*)<ul\s*class="tabs"/smU';
        $res1 = preg_match_all($pattern1, $description, $result1);
        if ($res1) {
            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
        } else {
            return array("status" => "fail", "message" => "RegEx not matched.");
        }
        $description = removeHTMLFormatting($description, true);
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 13603) {
        $pattern1 = '/(<div\s*id="tab-content1"\s*class="tab-content">.*)<div\s*id="tab-content3"\s*class="tab-content"/smU';
        $res1 = preg_match_all($pattern1, $description, $result1);
        if ($res1) {
            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
        } else {
            return array("status" => "fail", "message" => "RegEx not matched.");
        }
        $description = removeHTMLFormatting($description, true);
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 13656) {
        $pattern1 = '/<div\s*id="itemdetails">(.*)<\/div>\s*<div\s*class="clear">\s*<\/div>\s*<\/div>\s*<div\s*class="breaker">\s*<\/div>\s*<!--\s*About\s*Seller/smU';
        $res1 = preg_match_all($pattern1, $description, $result1);
        if ($res1) {
            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
        } else {
            //	return array("status" => "fail", "message" => "RegEx not matched.");		
        }
        $description = removeHTMLFormatting($description, false);
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 13657) {
        $pattern1 = '/<div\s*class="single-description\s*fix"[^>]*>(.*)<\/div>\s*<\/div>\s*<\/div>\s*<br[^>]*>\s*<br[^>]*>\s*<div\s*class="description-sce\s*fix"[^>]*>/smU';
        $res1 = preg_match_all($pattern1, $description, $result1);
        if ($res1) {
            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
        } else {
            //    			return array("status" => "fail", "message" => "RegEx not matched.");		
        }
        $description = removeHTMLFormatting($description, false);
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 13434) {
        echo "pankaj";
        exit(0);
        echo $description;
        echo "                ";
        $pattern1 = '/(.*)<p[^>]*>\s*<font[^>]*>\s*------------------------------------------/ismU';
        $res1 = preg_match_all($pattern1, $description, $result1);
        if ($res1) {
            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
        } else {
            // return array("status" => "fail", "message" => "RegEx not matched.");
        }
        $description = removeHTMLFormatting($description, true);
        echo $description;
        $pattern1 = '/(.*)<p[^>]*>\s*<em>\s*<u>\s*Remise\s*en\s*mains\s*propres/smU';
        $res1 = preg_match_all($pattern1, $description, $result1);
        if ($res1) {
            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
        }
        $conditionStr = "";
        if (strlen($condition_val) > 0) {
            $conditionStr = $conditionStr . "<p><strong>Condition:</strong> " . $condition_val . "</p>";
        }
        if (strlen($condition_note) > 0) {
            $conditionStr = $conditionStr . "<p>" . $condition_note . "</p>";
        }
        $description = $conditionStr . $description;
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 13526) {
        $description = removeHTMLFormatting($description, true);
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 13280) {
        $pattern1 = '/<froodescription>(.*)<\/froodescription>/smU';
        $res1 = preg_match_all($pattern1, $description, $result1);
        if ($res1) {
            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
        }
        $description = removeHTMLFormatting($description, true);
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 3747) {
        $pattern1 = '/<div\s*id="listingarea"[^>]*>(.*)<div\s*class="hd-listingarea-box"[^>]*>\s*<div\s*class="desc-headtitle"[^>]*>\s*<span>\s*Delivery/smU';
        $res1 = preg_match_all($pattern1, $description, $result1);
        if ($res1) {
            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
        }
        $description = removeHTMLFormatting($description, true);
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 13394) {
        $existingItemJson = json_decode($item_specific, true);
        $conditionStr = "";
        $allowedArr = array("Brand", "Metal", "Metal Purity", "Main Stone", "Secondary Stone", "Weight", "Length", "Pendant Weight", "Pendant Length (inches)", "Chain Length (inches)");
        foreach ($existingItemJson as $k => $v) {
            if (in_array($k, $allowedArr)) {
                $conditionStr = $conditionStr . '<p><strong>' . $k . ':</strong> ' . $v . '</p>';
            }
        }
        $description = $conditionStr;
    } else if ($user_id == 13380) {
        $description = removeHTMLFormatting($description, true);
        $description = strip_tags($description, "<div><p><ul><li>");
        return array("status" => "success", "message" => $description);
    }else if ($user_id == 13298000000) {
        //$description = removeHTMLFormatting($description, false);
         //$description = str_replace('class="container"', 'class="container" style="backgroud-color:white"', $description);
         return array("status" => "success", "message" => $description);
    } 
    else if ($user_id == 1329800000) {
        $description1 = $description;
        $pattern1 = '/<div\s*class="product_description"[^>]*>(.*)<div\s*class="icons_section"/smU';
        $res1 = preg_match_all($pattern1, $description, $result1);
        if ($res1) {
            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
        } else {
            //	return array("status" => "fail", "message" => "RegEx not matched.");		
        }

        $pattern1 = '/<div\s*id="first_tab"\s*class="content_container"[^>]*>(.*)<input\s*type="radio"\s*name="css_tabs_handler"\s*id="tab2">/smU';
        $res1 = preg_match_all($pattern1, $description1, $result1);
        if ($res1) {
            $description .= isset($result1[1][0]) ? trim($result1[1][0]) : "";
        } else {
            $pattern1 = '/<div\s*id="first_tab"\s*class="content_container"[^>]*>(.*)<\/div>\s*<\/div>\s*<!--css_tabs_contents ends/smU';
            $res1 = preg_match_all($pattern1, $description1, $result1);
            if ($res1) {
                $description .= isset($result1[1][0]) ? trim($result1[1][0]) : "";
            } else {
                $pattern1 = '/id="first_tab"[^>]*>(.*)<input\s*type="radio"\s*name="css_tabs_handler"\s*id="tab2">/smU';
                $res1 = preg_match_all($pattern1, $description1, $result1);
                if ($res1) {
                    $description .= isset($result1[1][0]) ? trim($result1[1][0]) : "";
                } else {
                    //		return array("status" => "fail", "message" => "RegEx not matched.");						
                }
            }
        }
        $description = removeHTMLFormatting($description, false);
        $description = preg_replace('#<h1[^>]*>\s*<b>\s*FEATURES:\s*</b>\s*</h1>#is', '', $description);
        $description = preg_replace('#<h1[^>]*>\s*<b>\s*PRODUCT\s*HIGHLIGHTS\s*</b>\s*</h1>#is', '', $description);
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 12592) {
        $pattern = '/<ebdescription[^>]*>(.*)<\/ebdescription>/sm';
        $res = preg_match_all($pattern, $description, $result);
        if ($res) {
            $description = isset($result[1][0]) ? trim($result[1][0]) : "";
        } else {
            //  return array("status" => "fail", "message" => "RegEx not matched.");
        }
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 13300) {
        $pattern1 = '/<!--\s*Begin\s*Description\s*-->(.*)<!--\s*End\s*Description\s*-->/smU';
        $res1 = preg_match_all($pattern1, $description, $result1);
        if ($res1) {
            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
        } else {
            return array("status" => "fail", "message" => "RegEx not matched.");
        }
        $description = removeHTMLFormatting($description, false);
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 13242) {
        $descriptiondup = $description;
        $pattern1 = '/(.*)<p\s*dir="ltr">\s*Sollten\s*Sie\s*bei\s*Erhalt\s*des/smU';
        $res1 = preg_match_all($pattern1, $description, $result1);
        if ($res1) {
            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
        } else {
            $pattern1 = '/(.*)Sollten\s*Sie\s*bei\s*Erhalt\s*des/smU';
            $res1 = preg_match_all($pattern1, $description, $result1);
            if ($res1) {
                $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                $description = $description . "</p>";
            }
        }
        $description1 = "";
        $pattern1 = '/<p\s*dir="ltr">Artikelnummer(.*)<\/p>/smU';
        $res1 = preg_match_all($pattern1, $descriptiondup, $result1);
        if ($res1) {
            $description1 = isset($result1[0][0]) ? trim($result1[0][0]) : "";
        }
        $description = $description . $description1;
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 12154) {
        $pattern = '/<div\s*class="item-description">(.*)<\/div>/smU';
        $res = preg_match_all($pattern, $description, $result);
        if ($res) {
            $description = isset($result[1][0]) ? trim($result[1][0]) : "";
        } else {
            return array("status" => "fail", "message" => "RegEx not matched.");
        }
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 13064) {
        $description = '<h4>Frame Depth</h4><p>We use a depth of 18 mm for sizes up to 36 in / 91 cm. For sizes 40 inches and above it is 38 mm.</p><h4>Media</h4><p>Canvas 260 gsm</p><h4>Free Shipping<br></h4><p>On all UK orders. Charges apply to other countries.</p><h4 class="no-margin">Delivery times</h4><p>We aim to deliver shop products within:</p><p class="no-margin">UK – 3-5 working days</p><p class="no-margin">Europe – 5-7 working days</p><p class="no-margin">Rest of world – 7-10 working days</p><p class="no-margin"><i>Custom Made In The UK.&nbsp;</i>We don\'t mass produce, so each art print you order is&nbsp;<strong>made just for you</strong>!</p>';
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 13138) {
        $pattern1 = '/<div\s*id="inkfrog_crosspromo_top">\s*<\/div>\s*<div\s*id="inkfrog_crosspromo_top">\s*<\/div>(.*)<div\s*id="inkfrog_crosspromo_bottom">/smU';
        $res1 = preg_match_all($pattern1, $description, $result1);
        if ($res1) {
            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
        } else {
            //	return array("status" => "fail", "message" => "RegEx not matched.");		
        }
        $description = removeHTMLFormatting($description, false);
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 12965 || $user_id == 13163 ) {
        $existingItemJson = json_decode($item_specific, true);
        if (!isset($existingItemJson["ItemCompatibilityList"])) {
            return array("status" => "fail", "message" => "RegEx not matched.");
        }
        $itemCompatibilityListObj = $existingItemJson["ItemCompatibilityList"];
        $str = '<table cellpadding="4" cellspacing="4">';
        foreach ($itemCompatibilityListObj as $v) {
            $str = $str . "<tr><td>" . $v["Year"] . "</td><td>" . $v["Make"] . "</td><td>" . $v["Model"] . "</td><td>" . $v["Trim"] . "</td><td>" . $v["Engine"] . "</td></tr>";
        }
        $str = $str . "</table>";
        $conditionStr = "";
        foreach ($existingItemJson as $k => $v) {
            if ($k != "ItemCompatibilityList") {
                $conditionStr = $conditionStr . '<p><strong>' . $k . ':</strong> ' . $v . '</p>';
            }
        }
        $description = $conditionStr . $description . "<p><strong>Vehicle Compatibility</strong></p>" . $str;
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 12221 || $user_id == 7112 || $user_id == 15164 || $user_id == 14146) {
        $description = removeHTMLFormatting($description, false);
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 12949) {
        $pattern1 = '/<froodescription>(.*)<\/froodescription>/smU';
        $res1 = preg_match_all($pattern1, $description, $result1);
        if ($res1) {
            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
        } else {
              // $description = removeHTMLFormatting($description, false); 
               //return array("status" => "fail", "message" => "RegEx not matched.");
        }
        $description = removeHTMLFormatting($description, false);
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 12815) {
        $pattern1 = '/<div\s*class="right-part col-md-6">(.*)<\/div>\s*<\/div>\s*<div\s*class="part">\s*<p\s*class="p-part">/smU';
        $res1 = preg_match_all($pattern1, $description, $result1);
        if ($res1) {
            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
        } else {
            //	return array("status" => "fail", "message" => "RegEx not matched.");		
        }
        $description = removeHTMLFormatting($description, false);
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 12738) {
        $pattern1 = '/<!--\s*Block\s*Description\s*\/\/\s*-->(.*)<!--\s*\/\/\s*Block\s*Description\s*-->/smU';
        $res1 = preg_match_all($pattern1, $description, $result1);
        if ($res1) {
            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
        } else {
            $pattern1 = '/<!--\s*Start\s*Description\s*AucLister\s*-->(.*)<!--\s*End\s*Description\s*AucLister\s*-->/smU';
            $res1 = preg_match_all($pattern1, $description, $result1);
            if ($res1) {
                $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
            } else {
                $pattern1 = '/https\:\/\/ti2\.auctiva\.com\/tia\/E1E38\/sassybeads\/desc\.png">\s*<\/div>(.*)<font[^>]*>\s*<br>\s*<div[^>]*>\s*<img\s*src="https\:\/\/ti2\.auctiva\.com\/tia\/E1E38\/sassybeads\/pay\.png"/smU';
                $res1 = preg_match_all($pattern1, $description, $result1);
                if ($res1) {
                    $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                } else {
                    $pattern1 = '/victorian_desc\.gif">\s*<\/div>(.*)<\/td>/smU';
                    $res1 = preg_match_all($pattern1, $description, $result1);
                    if ($res1) {
                        $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                    } else {
                        $pattern1 = '/desc\.png">\s*<\/div>(.*)<\/td>/smU';
                        $res1 = preg_match_all($pattern1, $description, $result1);
                        if ($res1) {
                            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                        } else {
                             $pattern1 = '/<div\s*class="card-block">(.*)<div\sstyle="text-align:left;"><br>\s<font face="Arial">&nbsp;<\/font><\/div>/smU';
                        $res1 = preg_match_all($pattern1, $description, $result1);
                        if ($res1) {
                            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                        } else {
                                  
                            $pattern1 = '/<div\s*class="card-block">(.*)<font\sface="Arial">/smU';
                        $res1 = preg_match_all($pattern1, $description, $result1);
                        if ($res1) {
                            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                        } else {

                          $pattern1 = '/<div\s*class="card-block">(.*)<br>&nbsp;<\/p><\/div><\/div>/smU';
                          $res1 = preg_match_all($pattern1, $description, $result1);
                        if ($res1) {
                            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                        } else {

                          $pattern1 = '/<div class="card-header" id="section-heading-1">(.*)<span>Payment<\/span>/smU';
                          $res1 = preg_match_all($pattern1, $description, $result1);
                        if ($res1) {
                            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                            print_r($description);
                            
                        } else {

                          $pattern1 = '/<span class="description" id="cte_descriptiontext">(.*)Remember.*unsure/smU';
                          $res1 = preg_match_all($pattern1, $description, $result1);
                        if ($res1) {
                            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                            print_r($description);
                            
                        } else {
                          $pattern1 = '/<span\sclass="card-title-decoration">Description<\/span>\s*<\/h3>\s*(.*)<h3>\s*<span\sclass="card-title-decoration">Payment/smU';
                          $res1 = preg_match_all($pattern1, $description, $result1);
                        if ($res1) {
                            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                            print_r($description);
                            
                        } else {

                       $pattern1 = '/<div class="card card-bordered has-mb-12 md-mb-16"><div class="card-block">(.*)<div class="card-header"\sid="section-heading-2"><h3><span>Payment/smU';
                          $res1 = preg_match_all($pattern1, $description, $result1);
                        if ($res1) {
                            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                            print_r($description);
                            
                        } else {

                       $pattern1 = '/<span\sclass="card-title-decoration">Description<\/span>\s* <\/h3>(.*)<h3\sclass="card-title">/smU';
                       $res1 = preg_match_all($pattern1, $description, $result1);
                        if ($res1) {
                            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                            print_r($description);
                            
                        } else {
                    $pattern1 = '/<div class="card card-bordered has-mb-12 md-mb-16"><br><div class="card-block">(.*)<div\sclass="card card-bordered has-mb-12 md-mb-16"><div\sclass="card-header"\sid="section-heading-2"><h3><span>Payment/smU';
                       $res1 = preg_match_all($pattern1, $description, $result1);
                        if ($res1) {
                            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                            print_r($description);
                            
                        } else {
                               return array("status" => "fail", "message" => "RegEx not matched."); 
                  } 
                             
                            } 
                         
   
                             } 


                          } 
        
                             }  

                              }
                           
                         }
                         }
                    

                            
                        }
                            
                            
                            
                            
                            
                            
                            
                            
                        }
                    }
                }
            }
        }
        $description = removeHTMLFormatting($description, false);
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 12513) {
        $pattern1 = '/<div\s*data-element="productDescription">(.*)<\/div>\s*<div\s*class="row">\s*<div\s*id="Column_6"/smU';
        $res1 = preg_match_all($pattern1, $description, $result1);
        if ($res1) {
            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
        } else {
            return array("status" => "fail", "message" => "RegEx not matched.");
        }
        $description = removeHTMLFormatting($description, false);
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 12455) {
        $pattern1 = '/(<div id="tab1">.*)<div\s*id="tab2">/smU';
        $res1 = preg_match_all($pattern1, $description, $result1);
        if ($res1) {
            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
        } else {
            return array("status" => "fail", "message" => "RegEx not matched.");
        }
        $description = removeHTMLFormatting($description, false);
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 12460) {
        $pattern1 = '/<div\s*id="cl-raw-description"\s*data-exclude-css-removal="true"[^>]*>(.*)<\/div>\s*<\/div>\s*<\/div>\s*<\/body>/smU';
        $res1 = preg_match_all($pattern1, $description, $result1);
        if ($res1) {
            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
        } else {
            $pattern1 = '/<div\s*id="cl-raw-description"\s*data-exclude-css-removal="true"[^>]*>(.*)<\/div>\s*<\/font>/smU';
            $res1 = preg_match_all($pattern1, $description, $result1);
            if ($res1) {
                $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
            } else {
                $pattern1 = '/<div\s*id="cl-raw-description"\s*data-exclude-css-removal="true"[^>]*>(.*)<\/div>\s*<\/body>/smU';
                $res1 = preg_match_all($pattern1, $description, $result1);
                if ($res1) {
                    $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                } else {
                    return array("status" => "fail", "message" => "RegEx not matched.");
                }
            }
        }
        $description = removeHTMLFormatting($description, false);
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 12482) {
        $pattern1 = '/(.*)<!--ENDFROOGALLERY-->/smU';
        $res1 = preg_match_all($pattern1, $description, $result1);
        if ($res1) {
            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
        } else {
            //return array("status" => "fail", "message" => "RegEx not matched.");		
        }
        $description = removeHTMLFormatting($description, false);
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 12180) {
        $pattern1 = '/<div\s*class="contentdv1"[^>]*>(.*)<\/div>\s*<div\s*class="contentdv2"/smU';
        $res1 = preg_match_all($pattern1, $description, $result1);
        if ($res1) {
            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
        } else {
            //	return array("status" => "fail", "message" => "RegEx not matched.");		
        }
        $description = removeHTMLFormatting($description, false);
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 12311) {
        $description = removeHTMLFormatting($description, false);
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 12289) {
        $pattern1 = '/name="sale_r3_c6"[^>]*>\s*<\/td>\s*<\/tr>(.*)<\/table>/smU';
        $res1 = preg_match_all($pattern1, $description, $result1);
        if ($res1) {
            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
            $description = "<table><tbody>" . $description;
        } else {
            //return array("status" => "fail", "message" => "RegEx not matched.");		
        }
        $description = removeHTMLFormatting($description, false);
        return array("status" => "success", "message" => $description);
        $description = strip_tags($description, "<b><strong><p><ul><li><br>");
    } else if ($user_id == 12145) {
        $pattern1 = '/<!--\s*DESCRIPTION\s*-->(.*)<!--\s*DESCRIPTION\s*-->/smU';
        $res1 = preg_match_all($pattern1, $description, $result1);
        if ($res1) {
            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
        } else {
            $pattern1 = '/<!--\s*DESCRIPTION\s*-->(.*)<a[^>]+>\s*See\s*my\s*other\s*listings/smU';
            $res1 = preg_match_all($pattern1, $description, $result1);
            if ($res1) {
                $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
            } else {
                $pattern1 = '/tplimages\/osvg\/des\.gif"\s*border="0"[^>]*>(.*)<!--\s*CONTACTTERMS/smU';
                $res1 = preg_match_all($pattern1, $description, $result1);
                if ($res1) {
                    $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                } else {
                    $pattern1 = '/<span\s*property="description">(.*)<\/div>/smU';
                    $res1 = preg_match_all($pattern1, $description, $result1);
                    if ($res1) {
                        $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                    } else {
                       $pattern1 = '/<br><table[^>]*>(.*)<\/table>\s<br>\s<br>\s<img.*>\s<br>/smU';
                       $res1 = preg_match_all($pattern1, $description, $result1);
                    if ($res1) {
                        $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                    } else {

                        $pattern1 = '/<img [^>]+>(.*)<!-- CONTACTTERMS -->/sm';
                       $res1 = preg_match_all($pattern1, $description, $result1);
                    if ($res1) {
                        $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                    } else {
                        $pattern1 = '/<span\sstyle[^>]*>(.*)<\/p>/sm';
                        $res1 = preg_match_all($pattern1, $description, $result1);
                     if ($res1) {
                         $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                     }
                     else{
                        return array("status" => "fail", "message" => "RegEx not matched."); 
                     }

                    }


                       

                        
                      } 
                       //

                        
                    }
                }
            }
        }
        $description = removeHTMLFormatting($description, false);
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 12104) {
        $pattern = '/<div\s*class="description-cel-mnd"[^>]*>\s*<div\s*class="col-sm-8"[^>]*>(.*)<\/div>\s*<div\s*class="col-sm-4"/iUms';
        $res1 = preg_match_all($pattern, $description, $result1);
        if ($res1) {
            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
        } else {
            return array("status" => "fail", "message" => "RegEx not matched.");
        }
        $description = removeHTMLFormatting($description, false);
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 11993) {
        $conditionStr = "";
        if (strlen($condition_val) > 0) {
            $conditionStr = $conditionStr . "<p><strong>Condition:</strong> " . $condition_val . "</p>";
        }
        if (strlen($condition_note) > 0) {
            $conditionStr = $conditionStr . "<p>" . $condition_note . "</p>";
        }
        $description = removeHTMLFormatting($description, false);
        $description = $conditionStr . $description;
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 11839) {
        $pattern = '/<h3[^>]*>\s*<span\s*class="glyphicon\s*glyphicon-list-alt">\s*<\/span>\s*Description\s*<\/h3>(.*)<ul\s*class="list-unstyled"[^>]*>/iUms';
        $res1 = preg_match_all($pattern, $description, $result1);
        if ($res1) {
            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
        } else {
            return array("status" => "fail", "message" => "RegEx not matched.");
        }
        $description = removeHTMLFormatting($description, false);
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 5326) {
        $pattern = '/<div\s*class="descriptiongg">(.*)<\/div>\s*<\/div>\s*<div\s*class="container">\s*<div\s*class="main">/iUms';
        $res1 = preg_match_all($pattern, $description, $result1);
        if ($res1) {
            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
        } else {
            return array("status" => "fail", "message" => "RegEx not matched.");
        }
        $description = removeHTMLFormatting($description, false);
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 11852) {
        $pattern = '/data-cl-template-tag="description">(.*)<\/div>\s*<\/div><div[^>]*>\s*<div\s*id="elm_539"\s*data-element-type="editor\.elements\.ImageElement"/iUms';
        $res1 = preg_match_all($pattern, $description, $result1);
        if ($res1) {
            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
        } else {
            return array("status" => "fail", "message" => "RegEx not matched.");
        }
        $description = removeHTMLFormatting($description, false);
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 11658) {
        $description = removeHTMLFormatting($description, false);
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 8517) {
        $pattern = '/<sd_description\s*id="ld_itemDescription"\s*class="descriptionWrap container">(.*)<\/sd_description>/iUms';
        $res1 = preg_match_all($pattern, $description, $result1);
        if ($res1) {
            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
        } else {
            return array("status" => "fail", "message" => "RegEx not matched.");
        }
        $description = removeHTMLFormatting($description, false);
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 11203) {
        $pattern = '/<span\s*property="description">(.*)<p>\s*Shop\s*our\s*range/iUms';
        $res1 = preg_match_all($pattern, $description, $result1);
        if ($res1) {
            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
        }
        $description = removeHTMLFormatting($description, false);
        $pattern = '/(.*)<div[^>]*>\s*<div[^>]*>\s*<div[^>]*>\s*---\s*<\/div>/iUms';
        $res1 = preg_match_all($pattern, $description, $result1);
        if ($res1) {
            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
        } else {
            $pattern = '/(.*)<div[^>]*>\s*<div[^>]*>\s*---\s*<\/div>/iUms';
            $res1 = preg_match_all($pattern, $description, $result1);
            if ($res1) {
                $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
            } else {
                //    return array("status" => "fail", "message" => "RegEx not matched.");
            }
        }
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 11012) {
        $pattern = '/<froodescription>(.*)<\/froodescription>/iUms';
        $res1 = preg_match_all($pattern, $description, $result1);
        if ($res1) {
            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
        } else {
            return array("status" => "fail", "message" => "RegEx not matched.");
        }
        $description = removeHTMLFormatting($description, false);
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 10933) {
        $description1 = "";
        $pattern = '/<section\s*id="content1"\s*class="tabs__content">(.*)<\/section>/iUms';
        $res1 = preg_match_all($pattern, $description, $result1);
        if ($res1) {
            $description1 = isset($result1[1][0]) ? trim($result1[1][0]) : "";
        }
        $pattern = '/<div\s*itemprop="description"\s*class="listing__info-description">(.*)<\/div>\s*<\/div>\s*<div\s*class="question">/iUms';
        $res1 = preg_match_all($pattern, $description, $result1);
        if ($res1) {
            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
        } else {
            return array("status" => "fail", "message" => "RegEx not matched.");
        }
        $description = $description1 . $description;
        $description = removeHTMLFormatting($description, false);
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 10836) {
        $description = "Item is supplied in the condition shown in the photographs.";
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 10550) {
        $pattern = '/<div\s*id="description">(.*)<\/div>\s*<\/div>\s*<div\s*id="image-section">/iUms';
        $res1 = preg_match_all($pattern, $description, $result1);
        if ($res1) {
            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
        } else {
            return array("status" => "fail", "message" => "RegEx not matched.");
        }
        $description = removeHTMLFormatting($description, false);
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 10455) {
        $pattern = '/<ul\s*type="disc"[^>]*>(.*)<\/ul>/iUms';
        $res1 = preg_match_all($pattern, $description, $result1);
        if ($res1) {
            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
        } else {
            return array("status" => "fail", "message" => "RegEx not matched.");
        }
        $description = removeHTMLFormatting($description, false);
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 10362) {
        $pattern = '/(.*)<hr>\s*<h4[^>]*>\s*<strong>\s*Welcome/iUms';
        $res1 = preg_match_all($pattern, $description, $result1);
        if ($res1) {
            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
        } else {
            $pattern = '/(.*)<p[^>]*>\s*We,\s*at\s*Absolute\s*Opals\s*and\s*Gems\s*Pty\s*Ltd/iUms';
            $res1 = preg_match_all($pattern, $description, $result1);
            if ($res1) {
                $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
            } else {
                $pattern = '/(.*)<p[^>]*>\s*We\s*hope\s*we\s*can\s*welcome\s*you/iUms';
                $res1 = preg_match_all($pattern, $description, $result1);
                if ($res1) {
                    $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                } else {
                    return array("status" => "fail", "message" => "RegEx not matched.");
                }
            }
        }
    } else if ($user_id == 10318) {
        $pattern = '/<!--end-header-3dsellers\.com-ld-html-->(.*)<!--start-footer-3dsellers\.com-ld-html-->/iUms';
        $res = preg_match_all($pattern, $description, $result);
        if ($res) {
            $description = isset($result[1][0]) ? trim($result[1][0]) : "";
        } else {
            return array("status" => "fail", "message" => "RegEx not matched.");
        }
        $description = removeHTMLFormatting($description, false);
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 10043) {
        $pattern = '/<!--VGMVAGRVKGEVRGSND-->(.*)<!--\s*Begin:\s*AuctivaCounter\s*-->/iUms';
        $res1 = preg_match_all($pattern, $description, $result1);
        if ($res1) {
            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
        } else {
            $pattern = '/<!--VGMVAGRVKGEVRGSND-->(.*)<!--\s*End:\s*AuctivaCounter\s*-->/iUms';
            $res1 = preg_match_all($pattern, $description, $result1);
            if ($res1) {
                $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
            } else {
                $pattern = '/<!--VGMVAGRVKGEVRGSND-->(.*)/iUms';
                $res1 = preg_match_all($pattern, $description, $result1);
                if ($res1) {
                    $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                } else {
                    $pattern = '/<div\s*class="product-short-description short-description"[^>]*>(.*)<\/div>/iUms';
                    $res1 = preg_match_all($pattern, $description, $result1);
                    if ($res1) {
                        $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                    } else {
                        $pattern = '/(.*)<!--\s*Begin:\s*AuctivaCounter/iUms';
                        $res1 = preg_match_all($pattern, $description, $result1);
                        if ($res1) {
                            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                        } else {
                            return array("status" => "fail", "message" => "RegEx not matched.");
                        }
                    }
                }
            }
        }
        $description = removeHTMLFormatting($description, false);
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 10122) {
        $pattern = '/<froodescription>(.*)<\/froodescription>/iUms';
        $res = preg_match_all($pattern, $description, $result);
        if ($res) {
            $description = isset($result[1][0]) ? trim($result[1][0]) : "";
        } else {
            return array("status" => "fail", "message" => "RegEx not matched.");
        }
        $description = removeHTMLFormatting($description, false);
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 10275) {
        $pattern = '/<div\s*id="sma_description">(.*)<!--\s*End\s*SMA\s*Description/iUms';
        $res = preg_match_all($pattern, $description, $result);
        if ($res) {
            $description = isset($result[1][0]) ? trim($result[1][0]) : "";
        } else {
            return array("status" => "fail", "message" => "RegEx not matched.");
        }
        $description = removeHTMLFormatting($description, false);
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 9992) {
        $pattern = '/(<div\s*class="container-ld-description">\s*<div\s*class="ld-description-text">.*)<\/div>\s*<div\s*class="component-container"[^>]*>\s*<!--\s*print\s*components\s*-->/iUms';
        $res = preg_match_all($pattern, $description, $result);
        if ($res) {
            $description = isset($result[1][0]) ? trim($result[1][0]) : "";
        } else {
            return array("status" => "fail", "message" => "RegEx not matched.");
        }
        $description = removeHTMLFormatting($description, false);
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 9784) {
        $pattern = '/<h3\s*class="info__title"[^>]*>\s*Product\s*Details\s*<\/h3>\s*<div\s*class="info__text"[^>]*>(.*)<\/div>\s*<\/div>\s*<\/div>\s*<\/div>\s*<\/section><section\s*class="section details"/iUms';
        $res = preg_match_all($pattern, $description, $result);
        if ($res) {
            $description = isset($result[1][0]) ? trim($result[1][0]) : "";
            $pattern = '/<div\s*class="styled">(.*)<\/div>/iUms';
            $res = preg_match_all($pattern, $description, $result);
            if ($res) {
                $description = isset($result[1][0]) ? trim($result[1][0]) : "";
            } else {
                return array("status" => "fail", "message" => "RegEx not matched.");
            }
        } else {
            $featureStr = "";
            $pattern = '/(<ul class="specs">.*<\/ul>)/iUms';
            $res = preg_match_all($pattern, $description, $result);
            if ($res) {
                $featureStr = isset($result[1][0]) ? trim($result[1][0]) : "";
            }
            $pattern = '/<ul\s*class="specs">(.*)<\/ul>/iUms';
            $res = preg_match_all($pattern, $description, $result);
            if ($res) {
                $pattern = '/<div\s*class="styled">(.*)<\/div>/iUms';
                $res = preg_match_all($pattern, $description, $result);
                if ($res) {
                    $description = isset($result[1][0]) ? trim($result[1][0]) : "";
                    $description = $description . $featureStr;
                } else {
                    return array("status" => "fail", "message" => "RegEx not matched.");
                }
            } else {
                return array("status" => "fail", "message" => "RegEx not matched.");
            }
        }
        $description = removeHTMLFormatting($description, false);
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 9750) {
        echo "1";
        $pattern = '/2508\.jpg"\s*[^>]*>\s*<\/td>\s*<\/tr>\s*<tr>\s*<td[^>]*>(.*)<\/td>/iUms';
        $res = preg_match_all($pattern, $description, $result);
        if ($res) {
              echo "2";
              $description = isset($result[1][0]) ? trim($result[1][0]) : "";
        } else {
            echo "3";
            $pattern = '/3733\.jpg"\s*[^>]*>\s*<\/p>\s*<\/td>\s*<\/tr>\s*<tr>\s*<td[^>]*>(.*)<\/td>/iUms';
            $res = preg_match_all($pattern, $description, $result);
            if ($res) {
                   echo "4";
                   $description = isset($result[1][0]) ? trim($result[1][0]) : "";
            } else {
                echo "5";
                $pattern = '/3733\.jpg"\s*[^>]*>\s*<\/td>\s*<\/tr>\s*<tr>\s*<td[^>]*>(.*)<\/td>/ims';//U
                $res = preg_match_all($pattern, $description, $result);
                if ($res) {
                    echo "6";
                    $description = isset($result[1][0]) ? trim($result[1][0]) : "";
                } else {
                    echo "7";
                    $pattern = '/2508\.jpg"\s*[^>]*>\s*<\/td>\s*<\/tr>\s*<tr>\s*<td[^>]*>(.*)<\/td>/iUms';
                    $res = preg_match_all($pattern, $description, $result);
                    if ($res) {
                        echo "8";
                        $description = isset($result[1][0]) ? trim($result[1][0]) : "";
                    }else {

                        echo "7";
                        $pattern = '/<font\s*size="6"\s*face="Comic\s*Sans\s*MS">(.*)<\/p><\/font><\/font>/s';
                        $res = preg_match_all($pattern, $description, $result);
                        if ($res) {
                            echo "8";
                            $description = isset($result[1][0]) ? trim($result[1][0]) : "";
                        }else{
                        
                        echo "7";
                        $pattern = '/<span\s*style=[^>]*>(.*)<\/strong><\/font><\/span><\/p>/s';
                        $res = preg_match_all($pattern, $description, $result);
                        if ($res) {
                            echo "8";
                            $description = isset($result[1][0]) ? trim($result[1][0]) : "";
                        }
                        else{
                           echo "9";
                           return array("status" => "fail", "message" => "RegEx not matched.");
                        }
                    
                    }

                }
            }
        }
    }
        echo "10";
        $description = removeHTMLFormatting($description, false);
        
        return array("status" => "success", "message" => $description);
        echo "11";          

    } else if ($user_id == 9829) {
        $pattern = '/<section\s*class="panel\s*panel-default">\s*<div\s*class="panel-heading">\s*Main\s*Description\s*<\/div>(.*)<\/section>/iUms';
        $res = preg_match_all($pattern, $description, $result);
        if ($res) {
            $description = isset($result[1][0]) ? trim($result[1][0]) : "";
        } else {
            return array("status" => "fail", "message" => "RegEx not matched.");
        }
        $description = removeHTMLFormatting($description, false);
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 9534) {
        $pattern = '/<div\s*class="product-des">(.*)<\/div>\s*<div\s*class="essTabs">/sm';
        $res = preg_match_all($pattern, $description, $result);
        if ($res) {
            $description = isset($result[1][0]) ? trim($result[1][0]) : "";
        } else {
            return array("status" => "fail", "message" => "RegEx not matched.");
        }
    } else if ($user_id == 9421) {
        $pattern = '/<sd_description>(.*)<\/sd_description>/iUms';
        $res = preg_match_all($pattern, $description, $result);
        if ($res) {
            $description = isset($result[1][0]) ? trim($result[1][0]) : "";
        } else {
            //  return array("status" => "fail", "message" => "RegEx not matched.");
        }
        $description = removeHTMLFormatting($description, false);
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 9549) {
        $pattern = '/<div\s*class="description">(.*)<\/div>\s*<\/div>\s*<\/div>\s*<button\s*class="accordion">\s*Livraison/iUms';
        $res = preg_match_all($pattern, $description, $result);
        if ($res) {
            $description = isset($result[1][0]) ? trim($result[1][0]) : "";
        } else {
            $pattern = '/<div\s*class="description">(.*)<\/div>\s*<\/div>\s*<\/div>\s*<button\s*class="accordion">.*Lieferung/iUms';
            $res = preg_match_all($pattern, $description, $result);
            if ($res) {
                $description = isset($result[1][0]) ? trim($result[1][0]) : "";
            } else {
                return array("status" => "fail", "message" => "RegEx not matched.");
            }
        }
        $description = removeHTMLFormatting($description, false);
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 9162 || $user_id == 12640) {
        $conditionStr = "";
        $itemSpecArr = json_decode($item_specific, true);
        foreach ($itemSpecArr as $k => $v) {
            $conditionStr = $conditionStr . '<p><strong>' . $k . ':</strong> ' . $v . '</p>';
        }
        $description = $description . $conditionStr;
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 8509) {
        $conditionStr = "";
        if (strlen($condition_val) > 0) {
            $conditionStr = $conditionStr . "<p><strong>Condition:</strong> " . $condition_val . "</p>";
        }
        if (strlen($condition_note) > 0) {
            $conditionStr = $conditionStr . "<p><strong>Condition Description:</strong> " . $condition_note . "</p>";
        }
        $itemSpecArr = json_decode($item_specific, true);
        foreach ($itemSpecArr as $k => $v) {
            $conditionStr = $conditionStr . '<p><strong>' . $k . ':</strong> ' . $v . '</p>';
        }
        $description = $conditionStr . $description;
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 9309) {
        $conditionStr = "";
        if (strlen($condition_val) > 0) {
            $conditionStr = $conditionStr . "<p><strong>Condition:</strong> " . $condition_val . "</p>";
        }
        if (strlen($condition_note) > 0) {
            $conditionStr = $conditionStr . "<p><strong>Condition Description:</strong> " . $condition_note . "</p>";
        }
        $itemSpecArr = json_decode($item_specific, true);
        foreach ($itemSpecArr as $k => $v) {
            $conditionStr = $conditionStr . '<p><strong>' . $k . ':</strong> ' . $v . '</p>';
        }
        $description = $description . $conditionStr;
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 9018) {
        $pattern = '/<!--\s*Start\s*Description\s*AucLister\s*-->(.*)<!--\s*End\s*Description\s*AucLister\s*-->/iUms';
        $res = preg_match_all($pattern, $description, $result);
        if ($res) {
            $description = isset($result[1][0]) ? trim($result[1][0]) : "";
        } else {
            return array("status" => "fail", "message" => "RegEx not matched.");
        }
        $description = removeHTMLFormatting($description, false);
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 8923) {
        $pattern = '/(.*)<!--VGMVAGRVKGEVRGSND-->/sm';
        $res = preg_match_all($pattern, $description, $result);
        if ($res) {
            $description = isset($result[1][0]) ? trim($result[1][0]) : "";
        } else {
            return array("status" => "fail", "message" => "RegEx not matched.");
        }
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 631) {
        $pattern = '/<div\s*class="desc_con">(.*)<\/div>\s*<div\s*class="tabsmain">/Usmi';
        $res = preg_match_all($pattern, $description, $result);
        if ($res) {
            $description = isset($result[1][0]) ? trim($result[1][0]) : "";
        } else {
            //    return array("status" => "fail", "message" => "RegEx not matched.");
        }
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 7904) {
        $pattern1 = '/<!--\s*Block\s*Description\s*\/\/\s*-->(.*)<!--\s*\/\/\s*Block\s*Description\s*-->/Usmi';
        $res1 = preg_match_all($pattern1, $description, $result1);
        if ($res1) {
            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
        } else {
            $pattern1 = '/<!--\s*Start\s*Description\s*AucLister\s*-->(.*)<!--\s*End\s*Description\s*AucLister\s*-->/Usmi';
            $res1 = preg_match_all($pattern1, $description, $result1);
            if ($res1) {
                $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
            } else {
                return array("status" => "fail", "message" => "RegEx not matched.");
            }
        }
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 8831) {
        $pattern = '/<!--description-->(.*)<!--description\s*end-->/sm';
        $res = preg_match_all($pattern, $description, $result);
        if ($res) {
            $description = isset($result[1][0]) ? trim($result[1][0]) : "";
        } else {
            $pattern = '/<!--item_features-->(.*)<!--item_features end-->/sm';
            $res = preg_match_all($pattern, $description, $result);
            if ($res) {
                $description = isset($result[1][0]) ? trim($result[1][0]) : "";
            } else {
                return array("status" => "fail", "message" => "RegEx not matched.");
            }
        }
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 8822) {
        $pattern = '/<span\s*property="description">(.*)<\/span>/smU';
        $res = preg_match_all($pattern, $description, $result);
        if ($res) {
            $description = isset($result[1][0]) ? trim($result[1][0]) : "";
        } else {
            $pattern = '/<div.*data-cl-template-tag="description">(.*)<\/div>/smU';
            $res = preg_match_all($pattern, $description, $result);
            if ($res) {
                $description = isset($result[1][0]) ? trim($result[1][0]) : "";
            } else {
                return array("status" => "fail", "message" => "RegEx not matched.");
            }
        }
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 8779) {        
        $pattern = '/<summary\s*property="description"[^>]*>(.*)<\/summary>/Usmi';
        $res = preg_match_all($pattern, $description, $result);
        if ($res) {
            $description = isset($result[1][0]) ? trim($result[1][0]) : "";
        } else {
            return array("status" => "fail", "message" => "RegEx not matched.");
        }
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 8778) {
        $description = removeHTMLFormatting($description, true);
        $description = strip_tags($description, "<br>");
        $pattern1 = '/(.*)Shipping/Usmi';
        $res1 = preg_match_all($pattern1, $description, $result1);
        if ($res1) {
            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
        } else {
            return array("status" => "fail", "message" => "RegEx not matched.");
        }
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 8683) {
     
        $pattern1 = '/<img\s*src="https:\/\/ti2\.auctiva\.com\/tia\/13213F\/sparkle\/s_desc\.gif">\s*<\/div>(.*)<\/table>/smU';
        $res1 = preg_match_all($pattern1, $description, $result1);
        if ($res1) {
            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
        } else {
            $pattern1 = '/<img\s*src="https:\/\/ti2\.auctiva\.com\/tia\/13213F\/sparkle\/s_desc\.gif">\s*<\/font>\s*<\/div>(.*)<\/table>/smU';
            $res1 = preg_match_all($pattern1, $description, $result1);
            if ($res1) {
                $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
            } else {
                $pattern1 = '/<!--\s*CSG\s*INDICATOR\s*END\s*-->(.*)<!--\s*Begin:\s*AuctivaCounter\s*-->/smU';
                $res1 = preg_match_all($pattern1, $description, $result1);
                if ($res1) {
                    $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                } else {
                    $pattern1 = '/(.*)<!--\s*STARTFROOGALLERY\s*-->/smU';
                    $res1 = preg_match_all($pattern1, $description, $result1);
                    if ($res1) {
                        $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                    } else {
                        
                    $pattern1 = '/<div\s*id="ds_div"><table\s*align="center"\s*style="font-family:\s&quot;Times New Roman&quot;; border-spacing: 0px; width: 1124px;">(.*)<\/table><br><br><div\sstyle="font-size:\s14pt;\stext-align: center;"><font\sface="Times New Roman"><font size="6"><b><u>RETURN POLICY/smU';
                    $res1 = preg_match_all($pattern1, $description, $result1);
                    if ($res1) {
                        $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                        print_r($description);
                        //exit(0);
                        
                    } else {
                    
                    
                     $pattern1 = '/<div\s*id="ds_div"><table\s*align="center"\s*style="font-family:\s&quot;Times New Roman&quot;; border-spacing: 0px; width: 1124px;">(.*)<\/table><br><br><div\sstyle="font-size:\s14pt;\stext-align: center;"><font\sface="Times New Roman"><font size="6"><b><u>RETURN POLICY/smU';
                    $res1 = preg_match_all($pattern1, $description, $result1);
                    if ($res1) {
                        $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                        print_r($description);
                        //exit(0);
                        
                    } else {

                        $pattern1 = '/<table\s*align="center"\s*style="[^>]*">(.*)<\/table><div style="font-size: 14pt; text-align: center;"><font size="6"><b><u><br><\/u><\/b><\/font><\/div><div style="font-size: 14pt; text-align: center;"><font size="6"><b><u>RETURN POLICY/smU';
                        $res1 = preg_match_all($pattern1, $description, $result1);
                        if ($res1) {
                            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                            print_r($description);
                            //exit(0);
                            
                        } else {
                              
                            $pattern1 = '/<div\s*id="ds_div">(.*)<\/table><div\sstyle="font-family:\s&quot;Times New Roman&quot;;\sfont-size: 14pt;\stext-align: center;"><font face="Times New Roman"><font size="6"><b><u>RETURN POLICY/smU';
                            $res1 = preg_match_all($pattern1, $description, $result1);
                            if ($res1) {
                                $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                                print_r($description);
                                //exit(0);
                                
                            } else {

                                $pattern1 = '/<div\s*id="ds_div">(.*)<\/table><\/div><div\sstyle="font-size:\s14pt;\stext-align: center;"><font\sface="Times New Roman"><font size="6"><b><u>RETURN POLICY/smU';
                                $res1 = preg_match_all($pattern1, $description, $result1);
                                if ($res1) {
                                    $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                                    print_r($description);
                                    //exit(0);
                                    
                                } else {

                                    $pattern1 = '/<div id="ds_div"><font rwr="1"[^>]*>(.*)RETURN POLICY/smU';
                                    $res1 = preg_match_all($pattern1, $description, $result1);
                                    if ($res1) {
                                        $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                                        print_r($description);
                                        //exit(0);
                                        
                                    } else {


                                    $pattern1 = '/<div id="ds_div">(.*)RETURN POLICY/smU';
                                    $res1 = preg_match_all($pattern1, $description, $result1);
                                    if ($res1) {
                                        $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                                        print_r($description);
                                        //exit(0);
                                        
                                    } else {

                                    $pattern1 = '/(.*)RETURN/smUi';
                                    $res1 = preg_match_all($pattern1, $description, $result1);
                                    if ($res1) {
                                        $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                                        print_r($description);
                                        //exit(0);
                                        
                                    } else {
                                        return array("status" => "fail", "message" => "RegEx not matched.");
                                    }
                                       

                                       }

                                        //<div id="ds_div">(.*)RETURN POLICY
                                        
                                    
                                      }




                                   
                                }

                              //<div id="ds_div"><font rwr="1"[^>]*>(.*)RETURN POLICY
                                //<div\s*id="ds_div">(.*)<\/table><\/div><div\sstyle="font-size:\s14pt;\stext-align: center;"><font\sface="Times New Roman"><font size="6"><b><u>RETURN POLICY
                               

                            }




                           
                        }
                       //

                        //<table\s*align="center"\s*style="[^>]*">(.*)<\/table><div style="font-size: 14pt; text-align: center;"><font size="6"><b><u><br><\/u><\/b><\/font><\/div><div style="font-size: 14pt; text-align: center;"><font size="6"><b><u>RETURN POLICY
                    
                        
                           }
                    }
                        
                        
                        
                        
                        
                        
                    }
                }
            }
        }

        $description = removeHTMLFormatting($description, true);
        $itemSpecificJson = json_decode($item_specific, true);

        $i = 0;
        $itemSpecsStr = "<table><tr>";
        foreach ($itemSpecificJson as $k => $v) {
            $itemSpecsStr = $itemSpecsStr . "<td><strong>" . $k . "</strong>: " . $v . "</td>";
            $i++;
            if ($i == 2) {
                $itemSpecsStr = $itemSpecsStr . "</tr><tr>";
                $i = 0;
            }
        }
        if ($i == 0) {
            $itemSpecsStr = $itemSpecsStr . "<td></td><td></td>";
        }
        if ($i == 1) {
            $itemSpecsStr = $itemSpecsStr . "<td></td>";
        }
        $itemSpecsStr = $itemSpecsStr . "</tr></table>";
        $description = $itemSpecsStr . $description;
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 8439) {
        $pattern = '/<div\s*id="etb-description"[^>]*>(.*)<\/div>/Usmi';
        $res = preg_match_all($pattern, $description, $result);
        if ($res) {
            $description = isset($result[1][0]) ? trim($result[1][0]) : "";
        } else {
            return array("status" => "fail", "message" => "RegEx not matched.");
        }
        $description = removeHTMLFormatting($description, true);
        $description = $description . "<p><strong><u>Item Specs</u></strong></p>";
        $itemSpecArr = json_decode($item_specific, true);
        foreach ($itemSpecArr as $k => $v) {
            $description = $description . '<p><strong>' . $k . ':</strong> ' . $v . '</p>';
        }
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 8333) {
        $description = preg_replace('#<style[^>]*>.*?</style>#is', '', $description);
        $pattern = '/data-cl-template-tag="description">(.*)<\/div>\s*<\/div>\s*<div[^>]*>\s*<\/div>\s*<\/div>\s*<\/div>\s*<\/div>\s*<div\s*vocab="https:\/\/schema\.org\/"\s*typeof="Product"\s*id="mobile-description"/Usmi';
        $res1 = preg_match_all($pattern, $description, $result1);
        if ($res1) {
            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
        } else {
            $pattern = '/data-cl-template-tag="description">(.*)<\/div>\s*<\/div>\s*<span[^>]*>\s*<div[^>]*>\s*<\/div>\s*<\/span>\s*<\/div>\s*<\/div>\s*<\/div>\s*<div\s*vocab="https:\/\/schema\.org\/"\s*typeof="Product"\s*id="mobile-description"/Usmi';
            $res1 = preg_match_all($pattern, $description, $result1);
            if ($res1) {
                $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
            } else {
                return array("status" => "fail", "message" => "RegEx not matched.");
            }
        }
        $description = removeHTMLFormatting($description, true);
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 8042) {
        $pattern = '/<!--PA4_HTMLTop_End-->(.*)<!--PA4_HTMLBottom_Begin-->/Usmi';
        $res1 = preg_match_all($pattern, $description, $result1);
        if ($res1) {
            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
        }
        $description = removeHTMLFormatting($description, true);
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 8190 || $user_id == 8175) {
        $pattern = '/<!--\s*LISTING\s*DESCRIPTION\s*GOES\s*HERE\s*-->(.*)<div\s*id="nt-main-mail"\s*class="nt-prom nt-prot">/smi';
        $res1 = preg_match_all($pattern, $description, $result1);
        if ($res1) {
            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
        } else {
            //	return array("status" => "fail", "message" => "RegEx not matched.");
        }
        $description = removeHTMLFormatting($description, true);
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 5347) {
        $pattern = '/<span\s*class="title\s*titletmpl".*<div[^>]*>\s*<img[^>]+>\s*<\/div>(.*)<\/div>\s*<div[^>]*>\s*<h3[^>]*>\s*<img\s*src="https:\/\/ebay\.spar-king\.com\/ebay\/image\/footer\.jpg">\s*<\/h3>\s*<\/div>/Usmi';
        $res1 = preg_match_all($pattern, $description, $result1);
        if ($res1) {
            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
        } else {
            $pattern = '/<div[^>]*>\s*<img[^>]+>\s*<\/div>(.*)<\/div>\s*<div[^>]*>\s*<h3[^>]*>\s*<img\s*src="https:\/\/ebay\.spar-king\.com\/ebay\/image\/footer\.jpg">\s*<\/h3>\s*<\/div>/Usmi';
            $res1 = preg_match_all($pattern, $description, $result1);
            if ($res1) {
                $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
            } else {
                $pattern = '/<div[^>]*>\s*<img[^>]+>\s*<\/div>(.*)<\/div>\s*<div[^>]*>\s*<ul>\s*<li>\s*<h3[^>]*>\s*<img\s*src="https:\/\/ebay\.spar-king\.com\/ebay\/image\/footer\.jpg">\s*<\/h3>\s*<\/li>\s*<\/ul>\s*<\/div>/Usmi';
                $res1 = preg_match_all($pattern, $description, $result1);
                if ($res1) {
                    $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                } else {
                    echo "Regex not matched.";
                    //	return array("status" => "fail", "message" => "RegEx not matched.");
                }
            }
        }
        $description = removeHTMLFormatting($description, false);
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 17961 || $user_id == 9294 || $user_id == 9163 || $user_id == 8077 || $user_id == 8212) {
        $description = removeHTMLFormatting($description, true);
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 5053 || $user_id == 7835) {
        $tempArr = array("Band Size", "Bottoms Size (Men's)", "Bottoms Size (Women's)", "Dress Shirt Size", "Intimates & Sleep Size (Women's)", "Size", "Size (Men's)", "Size (Women's)", "Size Type", "US Shoe Size (Women's)", "Waist Size");
        $conditionStr = "";
        $itemSpecArr = json_decode($item_specific, true);
        foreach ($itemSpecArr as $k => $v) {
            if ($k != 'Country/Region of Manufacture') {
                if (in_array($k, $tempArr)) {
                    $k = "Size";
                }
                $conditionStr = $conditionStr . '<p><strong>' . $k . ':</strong> ' . $v . '</p>';
            }
        }
        $description = $description . $conditionStr;
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 5053) {
        $description = str_ireplace("iced out", "", $description);
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 7897) {
        $pattern = '/<td\s*class="tab-header">\s*<p>\s*Description\s*<\/p>\s*<\/td>\s*<\/tr>\s*<tr>\s*<td>(.*)<\/td>/iUms';
        $res1 = preg_match_all($pattern, $description, $result1);
        if ($res1) {
            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
        } else {
            $pattern = '/<td\s*class="tab-header">\s*<p>\s*<font[^>]*>\s*<span[^>]*>\s*Description\s*<\/span>\s*<\/font>\s*<\/p>\s*<\/td>\s*<\/tr>\s*<tr>\s*<td>(.*)<\/td>/iUms';
            $res1 = preg_match_all($pattern, $description, $result1);
            if ($res1) {
                $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
            } else {
                return array("status" => "fail", "message" => "RegEx not matched.");
            }
        }
        $description = removeHTMLFormatting($description, true);
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 7863) {
        $pattern = '/<div\s*data-element="productDescription">(.*)<\/div>\s*<\/div>\s*<div\s*class="row">\s*<div\s*id="Column_14"/iUms';
        $res1 = preg_match_all($pattern, $description, $result1);
        if ($res1) {
            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
        } else {
            $pattern = '/<div\s*class="col-lg-8\s*up3"\s*id="descttl">\s*<p>\s*<b>\s*DESCRIPTION\s*<\/b>\s*<\/p>\s*<\/div>(.*<ul\s*class="specs">.*<\/ul>\s*<\/div>)/iUms';
            $res1 = preg_match_all($pattern, $description, $result1);
            if ($res1) {
                $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
            } else {
                $pattern = '/<div\s*class="row-fluid section"\s*id="description-section">(.*)<\/div>\s*<div\s*id="image-section">/iUms';
                $res1 = preg_match_all($pattern, $description, $result1);
                if ($res1) {
                    $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                } else {
                    $pattern = '/<div\s*class="col-lg-8\s*up3"\s*id="descttl">\s*<p>\s*<strong>\s*DESCRIPTION\s*<\/strong>\s*<\/p>\s*<\/div>(.*<ul\s*class="specs">.*<\/ul>\s*<\/div>)/iUms';
                    $res1 = preg_match_all($pattern, $description, $result1);
                    if ($res1) {
                        $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                    } else {
                        return array("status" => "fail", "message" => "RegEx not matched.");
                    }
                }
            }
        }
        $description = removeHTMLFormatting($description, true);
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 7834) {
        $description = removeHTMLFormatting($description, true);
        $pattern = '/<h3>\s*Standard\s*Features\s*<\/h3>(.*)<h3>\s*About\s*Us\s*<\/h3>/iUms';
        $res1 = preg_match_all($pattern, $description, $result1);
        if ($res1) {
            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
        } else {
            $pattern = '/<h3>\s*<i>\s*Standard\s*Features\s*<\/i>\s*<\/h3>(.*)<h3>\s*About\s*Us\s*<\/h3>/iUms';
            $res1 = preg_match_all($pattern, $description, $result1);
            if ($res1) {
                $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
            } else {
                $pattern = '/<h1>.*<\/h1>(.*)<h3>\s*About\s*Us\s*<\/h3>/iUms';
                $res1 = preg_match_all($pattern, $description, $result1);
                if ($res1) {
                    $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                } else {
                    $pattern = '/<h3>\s*Standard\s*Features\s*<\/h3>(.*)<h3>\s*Contact\s*Us\s*<\/h3>/iUms';
                    $res1 = preg_match_all($pattern, $description, $result1);
                    if ($res1) {
                        $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                    } else {
                        return array("status" => "fail", "message" => "RegEx not matched.");
                    }
                }
            }
        }
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 6383) {
        $pattern = '/<!--\s*Block\s*Description\s*\/\/\s*-->(.*)<!--\s*\/\/\s*Block\s*Description\s*-->/iUms';
        $res = preg_match_all($pattern, $description, $result);
        if ($res) {
            $description = isset($result[1][0]) ? trim($result[1][0]) : "";
        } else {
            $pattern = '/<!--\s*Start\s*Description\s*AucLister\s*-->(.*)<!--\s*End\s*Description\s*AucLister\s*-->/iUms';
            $res = preg_match_all($pattern, $description, $result);
            if ($res) {
                $description = isset($result[1][0]) ? trim($result[1][0]) : "";
            } else {
                $pattern = '/<!--\s*CSG\s*INDICATOR\s*END\s*-->(.*)<center>\s*<img\s*src="HTTPS:\/\/pics\.ebay\.com\/aw\/pics\/sell\/templates\/images\/k2\/tagline\.gif/iUms';
                $res = preg_match_all($pattern, $description, $result);
                if ($res) {
                    $description = isset($result[1][0]) ? trim($result[1][0]) : "";
                } else {
                    return array("status" => "fail", "message" => "RegEx not matched.");
                }
            }
        }
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 7514) {
        $description = removeHTMLFormatting($description, false);
        $description = preg_replace('#<ul[^>]*>.*?</ul>#is', '', $description);
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 4201) {
        $pattern = '/<!--\s*Start\s*Description\s*AucLister\s*-->(.*)<!--\s*End\s*Description\s*AucLister\s*-->/smi';
        $res1 = preg_match_all($pattern, $description, $result1);
        if ($res1) {
            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
        } else {
            $pattern = '/<!--\s*Block\s*Description\s*\/\/\s*-->(.*)<!--\s*\/\/\s*Block\s*Description\s*-->/smi';
            $res1 = preg_match_all($pattern, $description, $result1);
            if ($res1) {
                $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
            }
        }
        $pattern = '/(.*)<p>\s*<strong>\s*ABOUT\s*US/smi';
        $res1 = preg_match_all($pattern, $description, $result1);
        if ($res1) {
            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
        }
        $description = removeHTMLFormatting($description, true);
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 4143) {
        $pattern = '/<div\s*id="templatedesctxt">(.*)<\/div>\s*<\/div>\s*<\/div>\s*<!--FOOT\s*CLOSE-->/smi';
        $res1 = preg_match_all($pattern, $description, $result1);
        if ($res1) {
            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
        }
        $description = removeHTMLFormatting($description, true);
        $pattern = '/(.*)<p\s*class="MsoNormal"\s*>\s*PLEASE\s*READ/smi';
        $res1 = preg_match_all($pattern, $description, $result1);
        if ($res1) {
            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
        }
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 6936) {
        $pattern = '/<div\s*class="row-fluid section"\s*id="description-section">(.*)<\/div>\s*<div\s*id="image-section">/smi';
        $res1 = preg_match_all($pattern, $description, $result1);
        if ($res1) {
            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
            $description = $description . "</div></div>";
        } else {
            return array("status" => "fail", "message" => "RegEx not matched.");
        }
        $description = removeHTMLFormatting($description, true);
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 6943) {
        $pattern = '/(<div\s*class="banner3_5"[^>]*>.*)<div\s*class="banner3"[^>]*>\s*<div\s*class="banner3_dv1"[^>]*>/smi';
        $res = preg_match_all($pattern, $description, $result);
        if ($res) {
            $description = isset($result[1][0]) ? trim($result[1][0]) : "";
            $description = str_replace('<font color="#00429a">&nbsp; &nbsp; &nbsp; &nbsp;SAVE 5% AT CHECKOUT WITH EBAY DISCOUNT CODE&nbsp;</font><font color="#ff0010">PLANET</font><font color="#00429a">&nbsp;(T&amp;Cs Apply)</font>', '', $description);
            $description = str_replace('<font color="#00429a">SAVE 5% AT CHECKOUT WITH EBAY DISCOUNT CODE&nbsp;</font><font color="#ff0010">PLANET</font><font color="#00429a">&nbsp;(T&amp;Cs Apply)</font>', '', $description);
        } else {
            // return array("status" => "fail", "message" => "RegEx not matched.");
        }
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 6962) {
        $pattern = '/<p\s*ng-bind="description"\s*class="ng-binding"[^>]*>(.*)<div\s*class="disclaimer-fda/smi';
        $res = preg_match_all($pattern, $description, $result);
        if ($res) {
            $description = isset($result[1][0]) ? trim($result[1][0]) : "";
        } else {
            return array("status" => "fail", "message" => "RegEx not matched.");
        }
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 6931) {
        $pattern = '/<etbdescription[^>]*>(.*)<\/etbdescription>/sm';
        $res = preg_match_all($pattern, $description, $result);
        if ($res) {
            $description = isset($result[1][0]) ? trim($result[1][0]) : "";
        } else {
            return array("status" => "fail", "message" => "RegEx not matched.");
        }
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 1809) {
        $pattern = '/<div\s*class="row-fluid\s*section"\s*id="description-section"[^>]*>(.*)<\/div>\s*<div\s*id="image-section"/smi';
        $res1 = preg_match_all($pattern, $description, $result1);
        if ($res1) {
            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
            $description = $description;
        } else {
            $pattern = '/<img\s*[^>]+>\s*<\/p>\s*(<h1\s*class="it-ttl"\s*id="itemTitle"\s*itemprop="name"\s*align="center">.*)<p>\s*<span[^>]*>\s*<span[^>]*>\s*When\s*ordering\s*outside\s*of/smi';
            $res1 = preg_match_all($pattern, $description, $result1);
            if ($res1) {
                $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                $description = $description;
            } else {
                $pattern = '/<img\s*[^>]+>\s*<\/p>\s*(<h1\s*class="it-ttl"\s*id="itemTitle"\s*itemprop="name"\s*align="center">.*)<p>\s*<font[^>]*>\s*<span[^>]*>\s*<span[^>]*>\s*When\s*ordering\s*outside\s*of/smi';
                $res1 = preg_match_all($pattern, $description, $result1);
                if ($res1) {
                    $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                    $description = $description;
                } else {
                    return array("status" => "fail", "message" => "RegEx not matched.");
                }
            }
        }
        $description = removeHTMLFormatting($description, true);
        $description = preg_replace('#(<br */?>\s*)+#i', '<br />', $description);
        $description = preg_replace('#(<div>\s*<br */?>\s*<\/div>\s*)+#i', '<br />', $description);
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 6884) {
        $pattern = '/<div\s*class="corpo_descrizione_container">(.*)<\/div>\s*<div\s*class="footer_descrizione_prodotto_container">/smi';
        $res1 = preg_match_all($pattern, $description, $result1);
        if ($res1) {
            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
            $description = $description . "</div></div>";
        } else {
            return array("status" => "fail", "message" => "RegEx not matched.");
        }
        $description = removeHTMLFormatting($description, true);
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 6793) {
        $description = removeHTMLFormatting($description, true);
        $description = str_replace("<h1>", "", $description);
        $description = str_replace("</h1>", "", $description);
        $description = str_replace("<h2>", "", $description);
        $description = str_replace("</h2", "", $description);
        $description = preg_replace('#(<br */?>\s*)+#i', '<br />', $description);
        $description = preg_replace('#(<div>\s*<br */?>\s*<\/div>\s*)+#i', '<br />', $description);
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 6876) {
        $pattern = '/<div\s*class="product-description\s*no-subscribe">(.*)<\/div>\s*<div\s*class="row"[^>]*>\s*<div\s*id="Column_1"[^>]*>\s*<div\s*id="elm-3"/smi';
        $res1 = preg_match_all($pattern, $description, $result1);
        if ($res1) {
            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
            $description = $description . "</div></div>";
        } else {
            return array("status" => "fail", "message" => "RegEx not matched.");
        }
        $description = removeHTMLFormatting($description, true);
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 6094) {
        $description = removeHTMLFormatting($description, true);
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 5925) {
        $description = removeHTMLFormatting($description, true);
        $description = iconv('windows-1250', 'utf-8', $description);
        $conditionStr = "";
        if (strlen($condition_val) > 0) {
            $conditionStr = $conditionStr . "<p><strong>Condition:</strong> " . $condition_val . "</p>";
        }
        if (strlen($condition_note) > 0) {
            $conditionStr = $conditionStr . "<p><strong>Condition Description:</strong> " . $condition_note . "</p>";
        }
        $itemSpecArr = json_decode($item_specific, true);
        foreach ($itemSpecArr as $k => $v) {
            $conditionStr = $conditionStr . '<p><strong>' . $k . ':</strong> ' . $v . '</p>';
        }
        $description = $description . $conditionStr;
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 5539) {
        $pattern = '/<div\s*id="efusion-cathead1">\s*Product\s*Description\s*<\/div>(.*)<main\s*class="mainnn">/sm';
        $res = preg_match_all($pattern, $description, $result);
        if ($res) {
            $description = isset($result[1][0]) ? trim($result[1][0]) : "";
        } else {
            return array("status" => "fail", "message" => "RegEx not matched.");
        }
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 6453 || $user_id == 7032) {
        $pattern = '/<froodescription>(.*)<\/froodescription>/sm';
        $res = preg_match_all($pattern, $description, $result);
        if ($res) {
            $description = isset($result[1][0]) ? trim($result[1][0]) : "";
        } else {
            return array("status" => "fail", "message" => "RegEx not matched.");
        }
        $description = preg_replace('/(<[^>]+) style=".*?"/i', '$1', $description);
        $description = preg_replace('/\s*style=\\"[^\\"]*\\"/', '', $description);
        $description = iconv('windows-1250', 'utf-8', $description);
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 6065) {
        $pattern = '/<etbdescription[^>]*>(.*)<\/etbdescription>/sm';
        $res = preg_match_all($pattern, $description, $result);
        if ($res) {
            $description = isset($result[1][0]) ? trim($result[1][0]) : "";
        } else {
            return array("status" => "fail", "message" => "RegEx not matched.");
        }
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 6133) {
        $pattern = '/<!--\s*::::::::::::::::::::::::::::::::::::\s*item\s*description\s*starts\s*here\s*::::::::::::::::::::::::::::::::::::::::\s*-->(.*)<!--\s*::::::::::::::::::::::::::::::::::::\s*item\s*description\s*ends\s*here/sm';
        $res1 = preg_match_all($pattern, $description, $result1);
        if ($res1) {
            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
        } else {
            $pattern = '/<!--\s*::::::::::::::::::::::::::::::::::::\s*layout\s*and\s*description\s*::::::::::::::::::::::::::::::::::::::\s*-->(.*)<table/sm';
            $res1 = preg_match_all($pattern, $description, $result1);
            if ($res1) {
                $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
            } else {
                $pattern = '/<!--\s*::::::::::::::::::::::::::::::::::::\s*item\s*description\s*starts\s*here\s*::::::::::::::::::::::::::::::::::::::::\s*-->(.*)<table/sm';
                $res1 = preg_match_all($pattern, $description, $result1);
                if ($res1) {
                    $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                } else {
                    return array("status" => "fail", "message" => "RegEx not matched.");
                }
            }
        }
        $description = iconv('windows-1250', 'utf-8', $description);
        $description = str_replace('”', '"', $description);
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 6067) {
        echo "anjali 6067 in             ";
        $itemSpecArr = json_decode($item_specific, true);
        $pattern = '/<!--\s*Begin\s*Description\s*-->(.*)<!--\s*End\s*Description\s*-->/sm';
        $res1 = preg_match_all($pattern, $description, $result1);
        if ($res1) {
            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
        } else {
            $pattern = '/<!--\s*::::::::::::::::::::::::::::::::::::\s*item\s*description\s*starts\s*here\s*::::::::::::::::::::::::::::::::::::::::\s*-->(.*)<!--\s*::::::::::::::::::::::::::::::::::::\s*item\s*description\s*ends\s*here/sm';
            $res1 = preg_match_all($pattern, $description, $result1);
            if ($res1) {
                $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
            } else {
                $pattern = '/<!--\s*::::::::::::::::::::::::::::::::::::\s*layout\s*and\s*description\s*::::::::::::::::::::::::::::::::::::::\s*-->(.*)<!--\s*::::::::::::::::::::::::::::::::::::\s*item\s*description\s*ends\s*here/sm';
                $res1 = preg_match_all($pattern, $description, $result1);
                if ($res1) {
                    $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                } else {
                    $pattern = '/<div id="gs-description">(.*)<div class="gs-clearFloatDiv"><\/div>/sm';
                    $res1 = preg_match_all($pattern, $description, $result1);
                    if ($res1) {
                        $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                    } else {
                        $pattern = '/<!-- Begin Description -->(.*)<!-- End Description -->/sm';
                        $res1 = preg_match_all($pattern, $description, $result1);
                        if ($res1) {
                            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                        } else {
                            $pattern = '/<div\s*align="center"\s*[^>](.*)/sm';
                            $res1 = preg_match_all($pattern, $description, $result1);
                            if ($res1) {
                                $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                            } else {
                                $pattern = '/<span [^>]*>SKU(.*)<\/span><\/div>/sm';
                            $res1 = preg_match_all($pattern, $description, $result1);
                            if ($res1) {
                                $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                            } else {
                                $pattern = '/(.*)/sm';
                                $res1 = preg_match_all($pattern, $description, $result1);
                                if ($res1) {
                                    $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                                } else {
                                    return array("status" => "fail", "message" => "RegEx not matched.");
        
                                } 
    
                            }  
    
                            }  

                        }  


                        //
                        
                    }
                }
            }
        }
        $sdescription = '';
        $i = 0;
        $tags = array();
        foreach ($itemSpecArr as $k => $v) {
            if ($i === 0) {
                $sdescription = $sdescription . '<tr>';
            }
            $i++;
            $sdescription = $sdescription . '<td><strong>' . $k . ':</strong> ' . $v . '</td>';
            if ($v != "=") {
                $tags[] = $v;
            }
            if ($i == 2) {
                $i = 0;
                $sdescription = $sdescription . '</tr>';
            }
        }
        if ($i == 1) {
            $sdescription = $sdescription . '<td></td></tr>';
        }

        $description = iconv('windows-1250', 'utf-8', $description . '<br /><table cellspacing="10" cellpadding="0" align="left" style="width:100%"><tbody>' . $sdescription . '</table>');
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 5738) {
        $pattern = '/<!--\s*Block Description\s*\/\/\s*-->.*<u>\s*Details\s*<\/u>\s*<\/span>\s*<\/strong>\s*<\/span>\s*<\/span>\s*<\/p>(.*)<\/div>\s*<\/div>.*<!--\s*\/\/\s*Block\s*Description\s*-->/sm';
        $res = preg_match_all($pattern, $description, $result);
        if ($res) {
            $description = isset($result[1][0]) ? trim($result[1][0]) : "";
        } else {
            $pattern = '/<!--\s*Block Description\s*\/\/\s*-->.*\s*Details\s*<\/span>\s*<\/strong>\s*<\/span>\s*<\/span>\s*<\/p>(.*)<\/div>\s*<\/div>.*<!--\s*\/\/\s*Block\s*Description\s*-->/sm';
            $res = preg_match_all($pattern, $description, $result);
            if ($res) {
                $description = isset($result[1][0]) ? trim($result[1][0]) : "";
            } else {
                $pattern = '/<div\s*class="product-description\s*no-subscribe">\s*<div\s*data-element="productDescription">(.*)<\/div>\s*<\/div>\s*<div\s*class="row">\s*<div\s*id="Column_1"/sm';
                $res = preg_match_all($pattern, $description, $result);
                if ($res) {
                    $description = isset($result[1][0]) ? trim($result[1][0]) : "";
                } else {
                    $pattern = '/<!--\s*Block Description\s*\/\/\s*-->.*Details\s*<\/span>\s*<\/strong>\s*<\/span>\s*<\/span>\s*<\/u>\s*<\/p>(.*)<\/div>\s*<\/div>.*<!--\s*\/\/\s*Block\s*Description\s*-->/sm';
                    $res = preg_match_all($pattern, $description, $result);
                    if ($res) {
                        $description = isset($result[1][0]) ? trim($result[1][0]) : "";
                    } else {
                        $pattern = '/<!--\s*Block Description\s*\/\/\s*-->.*Details\s*<\/strong>\s*<\/span>\s*<\/span>\s*<\/span>(.*)<\/div>\s*<\/div>.*<!--\s*\/\/\s*Block\s*Description\s*-->/sm';
                        $res = preg_match_all($pattern, $description, $result);
                        if ($res) {
                            $description = isset($result[1][0]) ? trim($result[1][0]) : "";
                        } else {
                            $pattern = '/<p>\s*<b[^>]+>\s*Details\s*<\/b>\s*<\/p>(.*)<p>\s*<span[^>]*>\s*<span[^>]*>\s*<b[^>]*>\s*Guaranteed Authentic/sm';
                            $res = preg_match_all($pattern, $description, $result);
                            if ($res) {
                                $description = isset($result[1][0]) ? trim($result[1][0]) : "";
                            } else {
                                return array("status" => "fail", "message" => "RegEx not matched.");
                            }
                        }
                    }
                }
            }
        }
        $description = preg_replace("/<font[^>]+>/i", "", $description);
        $description = str_ireplace("</font>", "", $description);
        $description = preg_replace('/(<[^>]+) style=".*?"/i', '$1', $description);
        $description = str_ireplace('align="center"', "", $description);
        $description = preg_replace('/\s*style=\\"[^\\"]*\\"/i', '', $description);
        $description = str_replace("Â", "", $description);
        $description = iconv('windows-1250', 'utf-8', $description);
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 4188) {
        $pattern = '/<div\s*id="description"\s*>(.*)<\/div>\s*<\/div>\s*<div\s*id="image-section"/sm';
        $res = preg_match_all($pattern, $description, $result);
        if ($res) {
            $description = isset($result[1][0]) ? trim($result[1][0]) : "";
        } else {
            $pattern = '/<div\s*id="description"\s*>(.*)<\/div>\s*<div\s*id="image-section">/sm';
            $res = preg_match_all($pattern, $description, $result);
            if ($res) {
                $description = isset($result[1][0]) ? trim($result[1][0]) : "";
            } else {
                //			return array("status" => "fail", "message" => "RegEx not matched.");
            }
        }
        $description = str_replace("Â", "", $description);
        $description = iconv('windows-1250', 'utf-8', $description);
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 1714) {
        $pattern = '/<h1\s*style=".*">\s*<font\s*size="5">DESCRIPTION:<\/font>\s*<\/h1>(.*)<p\s*style=".*">\s*<font\s*size="6"\s*face="Times New Roman">\s*<b>\*\s*&nbsp;\s*&nbsp;\*\s*&nbsp;\s*&nbsp;\*<\/b>\s*<\/font>\s*<\/p>.*PLEASE\s*CONTACT\s*US/sm';
        $res = preg_match_all($pattern, $description, $result);
        if ($res) {
            $description = isset($result[1][0]) ? trim($result[1][0]) : "";
            $description = str_replace("Â", "", $description);
            $description = iconv('windows-1250', 'utf-8', $description);
        } else {
            return array("status" => "fail", "message" => "RegEx not matched.");
        }
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 6569) {
        $pattern1 = '/<htmlbox\s*class="htmlBox"[^>]*>(.*)<\/htmlbox>/smU';
        $res1 = preg_match_all($pattern1, $description, $result1);
        if ($res1) {
            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
        } else {
            //	return array("status" => "fail", "message" => "RegEx not matched.");					
        }
        $description = removeHTMLFormatting($description, false);
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 1639) {
        $description = preg_replace('#<script[^>]*>.*?</script>#is', '', $description);
        $description = preg_replace('#<p>\s*<span\s*style="color:\s*\#0000ff;[^>]*>.*?</p>#is', '', $description);
        $description = preg_replace('#&nbsp;#is', ' ', $description);
        $description = preg_replace('#<p>\s*<br[^>]*>\s*</p>#is', '', $description);
        $description = preg_replace('#<p>\s*<br[^>]*>\s*<br[^>]*>\s*</p>#is', '', $description);
        $description = preg_replace('#<p>\s*</p>#is', '', $description);
        $pattern = '/<body>(.*)<\/body>/sm';
        $res = preg_match_all($pattern, $description, $result1);
        if ($res) {
            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
        }
        $description = str_replace("Â", "", $description);
        $description = iconv('windows-1250', 'utf-8', $description);
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 32) {
        $description = '<p><strong>- Official Licensed&nbsp;Product<br>-&nbsp;Genuine &amp; Authentic&nbsp;Merchandise</strong><br><strong>- Brand New With Tags</strong><br></p>';
        return array("status" => "success", "message" => $description);//
    } else if ( $user_id == 23128 ||  $user_id == 20030 || $user_id == 20019 || $user_id == 17998 || $user_id == 18977 || $user_id == 18793 || $user_id == 14743 || $user_id == 13109 || $user_id == 9950 || $user_id == 9381 || $user_id == 8480 || $user_id == 5411 || $user_id == 8387 || $user_id == 8147 || $user_id == 7764 || $user_id == 410 || $user_id == 5595  || $user_id == 5773  || $user_id == 6602     ) {
        $description = '';
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 4084) {
        $pattern = '/<font\s*rwr="1"\s*size="4"><div>(.*)<\/div><div><font\s*size="4"><br><\/font><\/div><div>(.*)<\/div><div><br>/sm';
        $res = preg_match_all($pattern, $description, $result);
        if ($res) {
            $part1 = isset($result[1][0]) ? trim($result[1][0]) : "";
            $part2 = isset($result[2][0]) ? trim($result[2][0]) : "";
            $description = $part1 . '<br/><br/>' . $part2;
        }
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 57 || $user_id == 2275) { //|| $user_id == 2206
        $itemSpecArr = json_decode($item_specific, true);
        $description = '';
        foreach ($itemSpecArr as $k => $v) {
            $description = $description . '<p><strong>' . $k . ':</strong> ' . $v . '</p>';
        }
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 4933 || $user_id == 4500) {
        $itemSpecArr = json_decode($item_specific, true);
        $specs = "<ul>";
        foreach ($itemSpecArr as $k => $v) {
            $specs = $specs . '<li><strong>' . $k . ':</strong> ' . $v . '</li>';
        }
        $specs = $specs . "</ul>";
        $description = $specs . $description;
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 654) {
        $pattern = '/<div\s*class="col-xs-12\s*ebay-description">(.*<\/p>\s*<\/div>)/sm';
        $res = preg_match_all($pattern, $description, $result1);
        if ($res) {
            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
        } else {
            return array("status" => "fail", "message" => "RegEx not matched.");
        }
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 5999) {
        $pattern = '/<!--startcodistodescription\s*-\s*DO\s*NOT\s*REMOVE-->(.*)<!--endcodistodescription\s*-\s*DO\s*NOT REMOVE-->/sm';
        $res = preg_match_all($pattern, $description, $result1);
        if ($res) {
            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
            $description = iconv('windows-1250', 'utf-8', $description);
        }
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 5783) {
        $pattern = '/<!--\s*Block Description\s*\/\/\s*-->(.*)<!--\s*\/\/\s*Block\s*Description\s*-->/sm';
        $res = preg_match_all($pattern, $description, $result1);
        if ($res) {
            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
            $description = iconv('windows-1250', 'utf-8', $description);
        } else {
            $pattern = '/(.*)<div\s*class="inner_block"[^>]*><div\s*class="title"[^>]*><a\s*name="Guarantee"/sm';
            $res = preg_match_all($pattern, $description, $result1);
            if ($res) {
                $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                $description = $description . "</div>";
                //$description = iconv('windows-1250', 'utf-8', $description);
            } else {
                return array("status" => "fail", "message" => "RegEx not matched.");
            }
        }
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 5780) {
        $pattern = '/ZYKU\s*POLSKIM\s*<\/span>\s*<\/td>\s*<\/tr>\s*<tr>\s*<td[^>]*>(.*)<\/td>/sU';
        $res1 = preg_match_all($pattern, $description, $result1);
        if ($res1) {
            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
        } else {
            $pattern = '/ZYKU\s*POLSKIM\s*<\/span>\s*<\/td>\s*<\/tr>\s*<tr>\s*<td[^>]*>(.*)<p[^>]*>\s*<br>\s*<br>\s*<o:p>\s*<\/o:p>\s*<\/p>\s*<\/td>/sm';
            $res1 = preg_match_all($pattern, $description, $result1);
            if ($res1) {
                $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
            } else {
                //	return array("status" => "fail", "message" => "RegEx not matched.");
            }
        }
        //$description = iconv('windows-1250', 'utf-8', $description); 
        //	$description = str_replace('”', '"', $description);
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 714) {
        $itemSpecArr = json_decode($item_specific, true);
        $extraDesc = '';
        if (count($itemSpecArr) > 0) {
            $extraDesc = '<table border = "1" cellspacing="4" cellpadding = "4"  style = "text:center;width:90%"><tr><td><strong>Year</strong></td><td><strong>Make</strong></td><td><strong>Model</strong></td><td><strong>Trim</strong></td></tr>';
            foreach ($itemSpecArr as $obj) {
                $year = '';
                $make = '';
                $model = '';
                $trim = '';
                foreach ($obj as $k => $v) {
                    if ($k == 'Year') {
                        $year = $v;
                    } else if ($k == 'Make') {
                        $make = $v;
                    } else if ($k == 'Model') {
                        $model = $v;
                    } else if ($k == 'Trim') {
                        $trim = $v;
                    }
                }
                $extraDesc =  $extraDesc . '<tr><td><strong>' . $year . '</strong></td><td><strong>' . $make . '</strong></td><td><strong>' . $model . '</strong></td><td><strong>' . $trim . '</strong></td></tr>';
            }
            $extraDesc = $extraDesc . '</table>';
            $description = $description . $extraDesc;
        }
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 796) {
        $pattern = '/<div\s*id="template_content"[^>]*>(.*)<div\s*id="image-section">/sm';
        $res = preg_match_all($pattern, $description, $result1);
        if ($res) {
            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
            $description = iconv('windows-1250', 'utf-8', $description);
        } else {
            return array("status" => "fail", "message" => "RegEx not matched.");
        }
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 1280) {
        $description = preg_replace("/<font[^>]+>/", "", $description);
        $description = str_replace("</font>", "", $description);
        $description = preg_replace("/<i[^>]*>/", "", $description);
        $description = str_replace("</i>", "", $description);
        $description = preg_replace("/<em[^>]*>/", "", $description);
        $description = str_replace("</em>", "", $description);
        $description = preg_replace('/(<[^>]+) style=".*?"/i', '$1', $description);
        $description = str_replace("<b>", "", $description);
        $description = str_replace("</b>", "", $description);
        $description = preg_replace('/\s*style=\\"[^\\"]*\\"/', '', $description);

        $pattern = "/<td[^>]*>(.*)Please\s*don't\s*hesitate\s*to.*<hr.*hr/sm";
        $res1 = preg_match_all($pattern, $description, $result1);
        if ($res1) {
            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
            $description = removeHTMLFormatting($description, true);
            
        } else {
            $pattern = "/<!--\s*::::::::::::::::::::::::::::::::::::\s*item\s*description\s*starts\s*here\s*::::::::::::::::::::::::::::::::::::::::\s*-->\s*<div>\s*<div>\s*(.*)<\/div>\s*<\/div>\s*<div.*Please\s*let\s*me\s*know\s*if\s*you\s*have\s*any\s*questions!/sm";
            $res1 = preg_match_all($pattern, $description, $result1);
            if ($res1) {
                $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                $description = removeHTMLFormatting($description, true);
            } else {
                $pattern = "/<td[^>]*>(.*)Please\s*don't\s*hesitate\s*to/smU";
                $res1 = preg_match_all($pattern, $description, $result1);
                if ($res1) {
                    $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                    $description = removeHTMLFormatting($description, true);
                } else {
                    return array("status" => "fail", "message" => "RegEx not matched.");
                }
            }
        }
        $description = $string = preg_replace('/<h1\b[^>]*>(.*?)<\/h1>/', '<div>$1</div>', $description);
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 1447) {
        $pattern = '/<div\s*id="ds_div">(.*)<div\s*class="vi-iw"/sm';
        $res = preg_match_all($pattern, $description, $result1);
        if ($res) {
            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
        } else {
            $pattern = '/<table[^>]+>.*<table[^>]+>.*<\/table>.*<\/table>/sm';
            $res = preg_match_all($pattern, $description, $result1);
            if ($res) {
                $description = preg_replace($pattern, "", $description);
            } else {
                $pattern = '/<table[^>]+>.*<\/table>/sm';
                $res = preg_match_all($pattern, $description, $result1);
                if ($res) {
                    $description = preg_replace($pattern, "", $description);
                } else {
                    //return array("status" => "fail", "message" => "RegEx not matched.");
                }
            }
        }
        $description = strip_tags($description);
        $description = str_replace(".", ". ", $description);
        $description = str_replace("!", "! ", $description);
        $description = str_replace("&nbsp;", "", $description);
        $description = str_replace(". 925", ".925", $description);
        $tempStr = "";
        if (strlen($item_specific) > 0) {
            $item_specificObj = json_decode($item_specific, true);
            $i = 0;
            foreach ($item_specificObj as $k => $v) {
                if ($i == 0) {
                    $tempStr = $tempStr . '<tr><td width="50%"><strong>' . $k . '</strong>: ' . $v . '</td>';
                    $i = 1;
                } else if ($i == 1) {
                    $tempStr = $tempStr . '<td width="50%"><strong>' . $k . '</strong>: ' . $v . '</td></tr>';
                    $i = 0;
                }
            }
            if ($i == 1) {
                $tempStr = $tempStr . '<td width="50%"></td></tr>';
            }
        }
        if (strlen($tempStr) > 0) {
            $tempStr = '<h3>More Details:</h3><table cellspacing="5" cellpadding="5" width="100%" style="border-collapse: collapse;" border="1">' . $tempStr . '<table>';
        }
        $description = '<p><span style="font-size: x-large;"><strong><span size="5">' . $description . '</span></strong></span></p>' . $tempStr;
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 5987 || $user_id == 2063 || $user_id == 1873 || $user_id == 3596) {
        $description = '';
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 2056) {
        $pattern = '/<div\s*class="row"\s*id="description">\s*<div\s*class="desc-text">(.*)<\/div>\s*<\/div>\s*<div\s*class="tabs\s*row"\s*id="payment"/sm';
        $res1 = preg_match_all($pattern, $description, $result1);
        if ($res1) {
            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
            $description = iconv('windows-1250', 'utf-8', $description);
            $description = str_replace('”', '"', $description);
        }
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 1690) {
        $pattern = '/<div\s*class="description\s*col-md-6\s*col-sm-6\s*col-xs-12">\s*<div\s*class="title\s*col-md-12\s*col-sm-12\s*col-xs-12">.*<\/div>\s*(<div.*)<\/div>\s*<\/div>\s*<div\s*class="promotion\s*col-md-12\s*col-sm-12\s*col-xs-12">/sm';
        $res1 = preg_match_all($pattern, $description, $result1);
        if ($res1) {
            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
            $description = iconv('windows-1250', 'utf-8', $description);
            $description = str_replace('”', '"', $description);
            $description = str_replace('Â', '', $description);
        } else {
            $pattern = '/<div\s*class="description\s*col-md-6\s*col-sm-6\s*col-xs-12">(.*)<\/div>\s*<div\s*class="promotion col-md-12 col-sm-12 col-xs-12">/sm';
            $res1 = preg_match_all($pattern, $description, $result1);
            if ($res1) {
                $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                $description = iconv('windows-1250', 'utf-8', $description);
                $description = str_replace('”', '"', $description);
                $description = str_replace('Â', '', $description);
            }
        }
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 2171) {
        $pattern = '/<!--\s*froo\s*description\s*-->(.*)<!--\s*end\s*froo\s*description\s*-->/sm';
        $res1 = preg_match_all($pattern, $description, $result1);
        if ($res1) {
            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
        } else {
            $pattern = '/<froodescription\s*style="">(.*)<\/froodescription>/sm';
            $res1 = preg_match_all($pattern, $description, $result1);
            if ($res1) {
                $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
            }
        }
        $description = preg_replace("/<font[^>]+>/", "", $description);
        $description = str_replace("</font>", "", $description);
        $description = preg_replace('/(<[^>]+) style=".*?"/i', '$1', $description);
        $description = iconv('windows-1250', 'utf-8', $description);
        $description = str_replace('”', '"', $description);
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 3856  || $user_id == 2564) {
        $description =   "<p>Condition " . $condition_val . "</p><p>" . $description;
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 6519) {
        $pattern = '/<div\s*class="proinfowraper">\s*<h2>\s*Description\s*<\/h2>\s*<p[^>]*>(.*)<\/p>\s*<\/div>\s*<div\s*class="clear">\s*<br\s*\/>\s*<\/div>\s*<div\s*class="footer">/sm';
        $res1 = preg_match_all($pattern, $description, $result1);
        if ($res1) {
            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
            $description = preg_replace('/(<[^>]+) style=".*?"/i', '$1', $description);
            $description = preg_replace('/\s*style=\\"[^\\"]*\\"/', '', $description);
            $description = str_replace("&nbsp;", ' ', $description);
            $pattern = '/(.*)<p[^>]*>\s*<span[^>]*>\s*We\s*are\s*not\s*an\s*Authorized\s*Dealer/smi';
            $res1 = preg_match_all($pattern, $description, $result1);
            if ($res1) {
                $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
            } else {
                $pattern = '/(.*)<p[^>]*>\s*We\s*are\s*not\s*an\s*Authorized\s*Dealer/smi';
                $res1 = preg_match_all($pattern, $description, $result1);
                if ($res1) {
                    $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                } else {
                    $pattern = '/(.*)<div>\s*<span>\s*<strong>\s*FREE\s*FIRST CLASS\s*<\/strong>\s*<strong>\s*SHIPPING\s*WITH\s*TRACKING/smi';
                    $res1 = preg_match_all($pattern, $description, $result1);
                    if ($res1) {
                        $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                    } else {
                        $pattern = '/(.*)<div>\s*<span>\s*<strong>\s*FREE\s*EXPEDITED\s*<\/strong>\s*<strong>\s*SHIPPING\s*WITH\s*TRACKING/smi';
                        $res1 = preg_match_all($pattern, $description, $result1);
                        if ($res1) {
                            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                        } else {
                            //return array("status" => "fail", "message" => "RegEx not matched.");
                        }
                    }
                }
            }
        } else {
            $pattern = '/<!--\s*startcodistodescription\s*-\s*DO\s*NOT\s*REMOVE\s*-->(.*)<!--\s*endcodistodescription\s*-\s*DO\s*NOT\s*REMOVE\s*-->/sm';
            $res1 = preg_match_all($pattern, $description, $result1);
            if ($res1) {
                $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
            } else {
                $pattern = '/<div\s*class="proinfowraper">\s*<h2>\s*Description\s*<\/h2>(.*)<\/div>\s*<div\s*class="footer">/sm';
                $res1 = preg_match_all($pattern, $description, $result1);
                if ($res1) {
                    $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                } else {
                    //return array("status" => "fail", "message" => "RegEx not matched.");
                }
            }
        }
        $description = preg_replace('/(<[^>]+) style=".*?"/i', '$1', $description);
        $description = preg_replace('/\s*style=\\"[^\\"]*\\"/', '', $description);
        $description = iconv('windows-1250', 'utf-8', $description);
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 6412) {
        $pattern = '/<div\s*id="element_584"[^>]*>(.*)<\/div>\s*<\/div>\s*<div[^>]*>\s*<div\s*id="element_62"/sm';
        $res1 = preg_match_all($pattern, $description, $result1);
        if ($res1) {
            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
        } else {
            $pattern = '/<div\s*id="element_577"[^>]*>(.*)<\/div>\s*<\/div>\s*<div[^>]*>\s*<div\s*id="element_62"/sm';
            $res1 = preg_match_all($pattern, $description, $result1);
            if ($res1) {
                $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
            } else {
                //	return array("status" => "fail", "message" => "RegEx not matched.");
            }
        }
        $description = preg_replace('/(<[^>]+) style=".*?"/i', '$1', $description);
        $description = preg_replace('/\s*style=\\"[^\\"]*\\"/', '', $description);
        $description = iconv('windows-1250', 'utf-8', $description);
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 2422) {
        $pattern = '/(<p[^>]+>\s*<font[^>]+><span[^>]*>\s*<b[^>]*>Condition.*)<p[^>]+>\s*<u>\s*<sub>\s*<font[^>]+>\s*<b>Terms\s*\&amp;\s*Conditions<\/b>/sm';
        $res1 = preg_match_all($pattern, $description, $result1);
        if ($res1) {
            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
        } else {
            $pattern = '/(<p[^>]+>\s*<font[^>]+>\s*<span[^>]+>\s*<b[^>]*>Condition.*)<p[^>]+>\s*<u>\s*<sub>\s*<font[^>]+>\s*<b>Terms\s*\&amp;\s*Conditions<\/b>/sm';
            $res = preg_match_all($pattern, $description, $result1);
            if ($res) {
                $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
            } else {
                $pattern = '/(<p[^>]+>\s*<span[^>]+>\s*<b[^>]*>Condition.*)<p[^>]+>\s*<u>\s*<sub>\s*<font[^>]+>\s*<b>\s*Terms\s*\&amp;\s*Conditions/sm';
                $res = preg_match_all($pattern, $description, $result1);
                if ($res) {
                    $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                } else {
                    $pattern = '/(<p[^>]+>\s*<font[^>]+>\s*<span[^>]+>\s*<b[^>]*>\s*Condition.*)<p[^>]+>\s*<u>\s*<sub>\s*<font[^>]+>\s*<b>\s*Terms\s*\&amp;\s*Conditions<\/b>/sm';
                    $res = preg_match_all($pattern, $description, $result1);
                    if ($res) {
                        $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                    } else {
                        $pattern = '/(<p[^>]+>\s*<b>\s*<span[^>]+>\s*Fitting\s*<\/span>\s*<\/b>.*)<p[^>]+>\s*<u>\s*<sub>\s*<font[^>]+>\s*<b>Terms\s*\&amp;\s*Conditions<\/b>/sm';
                        $res = preg_match_all($pattern, $description, $result1);
                        if ($res) {
                            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                        } else {
                            $pattern = '/(<p[^>]+>\s*<font[^>]+>\s*<font[^>]+>\s*<span[^>]+>\s*<b[^>]*>\s*Condition.*)<p[^>]+>\s*<u>\s*<sub>\s*<font[^>]+>\s*<b>\s*Terms\s*\&amp;\s*Conditions/sm';
                            $res = preg_match_all($pattern, $description, $result1);
                            if ($res) {
                                $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                            } else {
                                $pattern = '/(<p[^>]+>\s*<font[^>]+>\s*<b[^>]*>Condition.*)<p[^>]+>\s*<u>\s*<sub>\s*<font[^>]+>\s*<b>Terms\s*\&amp;\s*Conditions<\/b>/sm';
                                $res = preg_match_all($pattern, $description, $result1);
                                if ($res) {
                                    $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                                } else {
                                    $pattern = '/(<p[^>]+>\s*<span[^>]+>\s*<font[^>]+>\s*<b[^>]*>\s*Condition.*)<p[^>]+>\s*<u>\s*<sub>\s*<font[^>]+>\s*<b>\s*Terms\s*\&amp;\s*Conditions<\/b>/sm';
                                    $res = preg_match_all($pattern, $description, $result1);
                                    if ($res) {
                                        $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                                    } else {
                                        $pattern = '/(<p[^>]+>\s*<font[^>]+>\s*<span[^>]+>\s*<font[^>]+>\s*<span[^>]+>\s*<b[^>]*>\s*Condition.*)<p[^>]+>\s*<u>\s*<sub>\s*<font[^>]+>\s*<b>\s*Terms\s*\&amp;\s*Conditions<\/b>/sm';
                                        $res = preg_match_all($pattern, $description, $result1);
                                        if ($res) {
                                            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                                        } else {
                                            $pattern = '/(<div[^>]+>\s*<span[^>]+>\s*<b[^>]*>\s*Condition.*)<p[^>]+>\s*<u>\s*<sub>\s*<font[^>]+>\s*<b>\s*Terms\s*\&amp;\s*Conditions<\/b>/sm';
                                            $res = preg_match_all($pattern, $description, $result1);
                                            if ($res) {
                                                $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                                            } else {
                                                $pattern = '/(<p[^>]+>\s*<font[^>]+>\s*<span[^>]+>\s*<font[^>]+>\s*<b[^>]*>\s*Condition.*)<p[^>]+>\s*<u>\s*<sub>\s*<font[^>]+>\s*<b>\s*Terms\s*\&amp;\s*Conditions<\/b>/sm';
                                                $res = preg_match_all($pattern, $description, $result1);
                                                if ($res) {
                                                    $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                                                } else {
                                                    $pattern = '/(<p[^>]+>\s*<span[^>]+>\s*<u>\s*<b>\s*<font[^>]+>\s*Condition.*)<p[^>]+>\s*<u>\s*<sub>\s*<font[^>]+>\s*<b>\s*Terms\s*\&amp;\s*Conditions<\/b>/sm';
                                                    $res = preg_match_all($pattern, $description, $result1);
                                                    if ($res) {
                                                        $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                                                    } else {
                                                        $pattern = '/(<p[^>]+>\s*<span[^>]+>\s*<b[^>]*>\s*<font[^>]+>\s*Condition.*)<p[^>]+>\s*<u>\s*<sub>\s*<font[^>]+>\s*<b>\s*Terms\s*\&amp;\s*Conditions<\/b>/sm';
                                                        $res = preg_match_all($pattern, $description, $result1);
                                                        if ($res) {
                                                            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                                                        } else {
                                                            return array("status" => "fail", "message" => "RegEx not matched.");
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        $description = preg_replace("/<font[^>]+>/", "", $description);
        $description = str_replace("</font>", "", $description);
        $description = preg_replace('/(<[^>]+) style=".*?"/i', '$1', $description);
        $description = str_replace('align="center"', "", $description);
        $description = preg_replace('/\s*style=\\"[^\\"]*\\"/', '', $description);
        $description = iconv('windows-1250', 'utf-8', $description);
        $description = str_replace('”', '"', $description);
        $itemSpecArr = json_decode($item_specific, true);
        $itemSpecStr = '';
        $is_brand = '';
        $is_theme = '';
        $is_character = '';
        $is_size = '';
        $is_type = '';
        $is_year = '';
        $is_packaging = '';
        $is_recommended_age = '';
        foreach ($itemSpecArr as $k => $v) {
            if ($k == 'Brand') {
                $is_brand = $v;
            } else if ($k == 'Theme') {
                $is_theme = $v;
            }
            if ($k == 'Character Family' || $k == 'Character') {
                $is_character = $v;
            }
            if ($k == 'Size') {
                $is_size = $v;
            }
            if ($k == 'Type') {
                $is_type = $v;
            }
            if ($k == 'Year') {
                $is_year = $v;
            }
            if ($k == 'Packaging') {
                $is_packaging = $v;
            }
            if ($k == 'Recommended Age Range') {
                $is_recommended_age = $v;
            }
            //$itemSpecStr = $itemSpecStr.'<p><strong>'.$k.':</strong> '.$v.'</p>';
        }
        if (strlen($is_brand) > 0) {
            $itemSpecStr = $itemSpecStr . '<p><strong>Brand:</strong> ' . $is_brand . '</p>';
        }
        if (strlen($is_theme) > 0) {
            $itemSpecStr = $itemSpecStr . '<p><strong>Theme:</strong> ' . $is_theme . '</p>';
        }
        if (strlen($is_character) > 0) {
            $itemSpecStr = $itemSpecStr . '<p><strong>Character:</strong> ' . $is_character . '</p>';
        }
        if (strlen($is_size) > 0) {
            $itemSpecStr = $itemSpecStr . '<p><strong>Size:</strong> ' . $is_size . '</p>';
        }
        if (strlen($is_type) > 0) {
            $itemSpecStr = $itemSpecStr . '<p><strong>Type:</strong> ' . $is_type . '</p>';
        }
        if (strlen($is_year) > 0) {
            $itemSpecStr = $itemSpecStr . '<p><strong>Year:</strong> ' . $is_year . '</p>';
        }
        if (strlen($is_packaging) > 0) {
            $itemSpecStr = $itemSpecStr . '<p><strong>Packaging:</strong> ' . $is_packaging . '</p>';
        }
        if (strlen($is_recommended_age) > 0) {
            $itemSpecStr = $itemSpecStr . '<p><strong>Recommended Age Range:</strong> ' . $is_recommended_age . '</p>';
        }
        $description = $itemSpecStr . $description;
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 2562 || $user_id == 2581) {
        $pattern = '/<!--\s*ETB:MOBILE:DESC:START\s*-\s*DO\s*NOT\s*EDIT\s*ABOVE\s*THIS\s*LINE\s*-->(.*)<!--\s*ETB:MOBILE:DESC:END\s*-\s*DO\s*NOT\s*EDIT\s*BELOW\s*THIS\s*LINE\s*-->/sm';
        $res1 = preg_match_all($pattern, $description, $result1);
        if ($res1) {
            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
        } else {
            return array("status" => "fail", "message" => "RegEx not matched.");
        }
        $description = iconv('windows-1250', 'utf-8', $description);
        $description = str_replace('”', '"', $description);
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 2730) {
        $pattern = '/<div\s*class="row-fluid section"\s*id="description-section">(.*)<\/div>\s*<div\s*id="image-section">/sm';
        $res1 = preg_match_all($pattern, $description, $result1);
        if ($res1) {
            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
        } else {
            return array("status" => "fail", "message" => "RegEx not matched.");
        }
        $description = iconv('windows-1250', 'utf-8', $description);
        $description = str_replace('”', '"', $description);
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 721) {
        $pattern = '/<div\s*class="discription disc-product">\s*<div\s*class="section1">\s*<div\s*class="titel2">Product\s*description<\/div>\s*<div\s*class="section">\s*<!--\s*Product\s*description\s*TEXT\s*GOES\s*HERE\s*-->(.*)<\/div>\s*<\/div>\s*<\/div>\s*<div\s*class="discription">/sm';
        $res1 = preg_match_all($pattern, $description, $result1);
        if ($res1) {
            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
        }
        $description = iconv('windows-1250', 'utf-8', $description);
        $description = str_replace('”', '"', $description);
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 18278 || $user_id == 2904 ) {
        $description = '';
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 4545) {
        $pattern = '/<div\s*style="[^>]*text-align:\s*center;">(.*)\s*Questions:/sm';
        $res = preg_match_all($pattern, $description, $result1);

        if ($res) {
            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
            $description = str_replace('Ã‚', "", $description);
            $description = preg_replace("/<font[^>]+>/", "", $description);
            $description = str_replace("</font>", "", $description);
            $description = preg_replace("/<b>/", "", $description);
            $description = str_replace("</b>", "", $description);
            $description = preg_replace("/<b[^>]+>/", "", $description);
            $description = preg_replace("/<span[^>]+>/", "", $description);
            $description = str_replace("</span>", "", $description);
            $description = preg_replace("/<p[^>]+>/", "<p>", $description);
            $description = str_replace("</p>", "</p>", $description);
            $description = iconv('windows-1250', 'utf-8', $description);
            $description = str_replace('â€', '"', $description);
            $description = '<div>' . $description . '</p></div></div>';
        } else {
            $pattern = '/<p\s*style="[^>]*text-align:\s*center;">(.*)\s*Questions:/sm';
            $res1 = preg_match_all($pattern, $description, $result1);
            if ($res1) {
                $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                $description = str_replace('Ã‚', "", $description);
                $description = preg_replace("/<font[^>]+>/", "", $description);
                $description = str_replace("</font>", "", $description);
                $description = preg_replace("/<b>/", "", $description);
                $description = str_replace("</b>", "", $description);
                $description = preg_replace("/<b[^>]+>/", "", $description);
                $description = preg_replace("/<span[^>]+>/", "", $description);
                $description = str_replace("</span>", "", $description);
                $description = preg_replace("/<p[^>]+>/", "<p>", $description);
                $description = str_replace("</p>", "</p>", $description);
                $description = iconv('windows-1250', 'utf-8', $description);
                $description = str_replace('â€<9d>', '"', $description);
                $description = '<div>' . $description . '</p></div></div>';
            }
        }
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 4656) {
        $pattern = '/<div\s*id="description">(.*) <div\s*id="image-section">.|\s*<section\s*class=\'tabbed\'>(.*)\s*<\/section>.*/sm';
        $res = preg_match_all($pattern, $description, $result);
        if ($res) {
            $part1 = isset($result[1][0]) ? trim($result[1][0]) : "";
            $part2 = isset($result[2][0]) ? trim($result[2][0]) : "";
            $description = $part1;
        } else {
            $pattern = '/<div\s*class="disclaim"[^>]*>(.*)<\/div>\s*<\/div>\s*<br>\s*<\/div>\s*<\/div>\s*<ul\s*class="nav nav-tabs">/sm';
            $res1 = preg_match_all($pattern, $description, $result1);
            if ($res1) {
                $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
            } else {
                return array("status" => "fail", "message" => "RegEx not matched.");
            }
        }
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 4607) {
        $pattern = '/<body>(.*)<\/body>/sm';
        $res1 = preg_match_all($pattern, $description, $result1);
        if ($res1) {
            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
        }
        $description = removeHTMLFormatting($description, true);
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 5057) {
        $pattern = '/<div\s*class="columns-wrapper">(.*)<!----------------------DESCRIPTION---END------------------------->/sm';
        $res1 = preg_match_all($pattern, $description, $result1);
        if ($res1) {
            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
            $description = $description . "</div></div>";
        }
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 5117) {
        $pattern = '/<div\s*class="panel panel-default desc-box" style="">(.*)<section\s*class="footer"\s*style="font-family: Arial; font-size: 14pt;">/sm';
        $res1 = preg_match_all($pattern, $description, $result1);
        if ($res1) {
            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
        }
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 5423) {
        $pattern = '/<!--\s*Block Description\s*\/\/\s*-->(.*)<!--\s*\/\/ Block Description\s*-->\s*/sm';
        $res1 = preg_match_all($pattern, $description, $result1);
        if ($res1) {
            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
            $description = $description;
        }
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 5244) {
        $pattern = '/<span property="description">(.*)<\/span>/U';
        $res1 = preg_match_all($pattern, $description, $result1);
        if ($res1) {
            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
            $description = $description;
        }
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 5597) {
        $pattern1 = '/<div\s*class="itemDesc">(.*)<\/div>/sm';
        $res1 = preg_match_all($pattern1, $description, $result1);
        if ($res1) {
            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
        }
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 3383) {
        $description = preg_replace("/Important.*/sm", "", $description);
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 6015) {
        $pattern = '/<div class="panel-heading">Description<\/div>\s*<div\s*class="panel-body">(.*)<\/div>\s*<\/section>\s*<section class="mobileonlypromos visible-xs">/sm';
        $res1 = preg_match_all($pattern, $description, $result1);
        if ($res1) {
            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
        }
        $description = str_replace('<p><strong>OPENING TIMES</strong></p>', "", $description);
        $description = str_replace('<p>MONDAY - FRIDAY 09.00 - 17.00</p>', "", $description);
        $description = str_replace('<p>SATURDAY 09.00 - 13.00</p>', "", $description);
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 3020) {
        $description = str_replace("&nbsp;", " ", $description);
        $description = str_replace(' ', "", $description);
        $description = str_replace('Â', "", $description);
        $description = preg_replace("/<font[^>]+>/", "", $description);
        $description = str_replace("</font>", "", $description);
        $description = preg_replace("/<b>/", "", $description);
        $description = str_replace("</b>", "", $description);
        $description = preg_replace("/<strong>/", "", $description);
        $description = str_replace("</strong>", "", $description);
        $description = preg_replace('/(<[^>]+) style=".*?"/i', '$1', $description);
        $description = str_replace('align="center"', "", $description);
        $description = preg_replace('/\s*style=\\"[^\\"]*\\"/', '', $description);
        $pattern = '/<div\s*id="templatedesc"\s*class="tempwidget"[^>]*>(.*)<div\s*id="templatedesc"\s*class="tempwidget"[^>]*>\s*<b[^>]*>\s*<font[^>]*>[^L]*Lowest\s*price\s*on/sm';
        $res1 = preg_match_all($pattern, $description, $result1);
        if ($res1) {
            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
        } else {
            $pattern = '/<div\s*id="templatedesc"\s*class="tempwidget"[^>]*>(.*)<center>\s*<div\s*id="templateship"\s*class="tempwidget"[^>]*>\s*<img\s*src="https:\/\/freeauctiondesigns\.com\/ebay\/templates\/(?:colorful_flowers|pink_red_hearts|roses_background)\/shipping\.gif"/sm';
            $res1 = preg_match_all($pattern, $description, $result1);
            if ($res1) {
                $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
            } else {
                $pattern = '/(<div\s*id="templatedesc"\s*class="tempwidget"[^>]*>\s*<div\s*id="templatedesc"\s*class="tempwidget"[^>]*>.*)<\/center>\s*<center>\s*<div\s*id="templateship"\s*class="tempwidget"[^>]*>\s*<img\s*src="https:\/\/freeauctiondesigns\.com\/ebay\/templates\/(?:colorful_flowers|pink_red_hearts|roses_background)\/shipping\.gif"/sm';
                $res1 = preg_match_all($pattern, $description, $result1);
                if ($res1) {
                    $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                } else {
                    $pattern = '/(<div\s*id="templatedesc"\s*class="tempwidget"[^>]*>.*)<\/center>\s*<center>\s*<div\s*id="templateship"\s*class="tempwidget">\s*<br>\s*<\/div>\s*<div\s*id="templateship"\s*class="tempwidget"[^>]*>\s*<img\s*src="https:\/\/freeauctiondesigns\.com\/ebay\/templates\/(?:colorful_flowers|pink_red_hearts|roses_background)\/shipping\.gif"/sm';
                    $res1 = preg_match_all($pattern, $description, $result1);
                    if ($res1) {
                        $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                    } else {
                        $pattern = '/(<div\s*id="templatedesc"\s*class="tempwidget"[^>]*>\s*<div\s*id="templatedesc"\s*class="tempwidget"[^>]*>.*)<\/center>\s*<center>\s*<div\s*id="templateship"\s*class="tempwidget"[^>]*>\s*<img\s*border="0"\s*src="https:\/\/freeauctiondesigns\.com\/ebay\/templates\/(?:colorful_flowers|pink_red_hearts|roses_background)\/shipping\.gif"/sm';
                        $res1 = preg_match_all($pattern, $description, $result1);
                        if ($res1) {
                            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                        } else {
                            $pattern = '/<div\s*id="templatedesc"\s*class="tempwidget"[^>]*>(.*)<div\s*id="templateship"\s*class="tempwidget"[^>]*>\s*<img\s*src="https:\/\/freeauctiondesigns\.com\/ebay\/templates\/(?:colorful_flowers|pink_red_hearts|roses_background)\/shipping\.gif"/sm';
                            $res1 = preg_match_all($pattern, $description, $result1);
                            if ($res1) {
                                $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                            } else {
                                $pattern = '/start\s*auction\s*content\s*below[^>]*>(.*)<!--\s*end\s*of\s*image\s*-->/sm';
                                $res1 = preg_match_all($pattern, $description, $result1);
                                if ($res1) {
                                    $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                                    $description = preg_replace("/<img[^>]+\>/i", "", $description);
                                } else {
                                    $pattern = '/Item\s*Description\s*<\/div>\s*<\/div>(.*)<\/div>/sm';
                                    $res1 = preg_match_all($pattern, $description, $result1);
                                    if ($res1) {
                                        $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                                    } else {

                                        $pattern = '/<div\s*id="templatedesc"\s*class="tempwidget"[^>]*>(.*)<div\s*id="templatedesc"\s*class="tempwidget"[^>]*>\s*<b[^>]*>\s*/sm';
                                        $res1 = preg_match_all($pattern, $description, $result1);
                                        if ($res1) {
                                            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                                        } else {
                                            return array("status" => "fail", "message" => "RegEx not matched.");
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        $description = iconv('windows-1250', 'utf-8', $description);
        $description = str_replace('”', '"', $description);
        $description = preg_replace('/<div\s*id="templatedesc"\s*class="tempwidget"[^>]*>\s*You\s*can\s*find\s*more.*eBay\s*Store\s*<\/a>/', "", $description);
        $description = str_replace('You can find more of my holy cards by looking in my eBay Store', '', $description);
        $description = str_replace('Lowest price on eBay. If I can’t beat it, I will match it!', '', $description);
        $description = str_replace('You can find more of my Vatican items by looking in my &nbsp;</b></font><a href="http://stores.ebay.com/" target="_blank"><img src="https://pics.ebaystatic.com/aw/pics/storefronts/hub/subcat/storesLogoNW_107x55.gif" border="0" align="absmiddle" alt="From collectibles to electronics, buy and sell all kinds of items on eBay Stores"></a>', '"', $description);
        $description = str_replace("Please look at the pictures and don't hesitate to contact me for more info", '"', $description);
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 1986) {
        $itemSpecArr = json_decode($item_specific, true);
        $itemSpecStr = '';
        foreach ($itemSpecArr as $k => $v) {
            $itemSpecStr = $itemSpecStr . '<p><strong>' . $k . ':</strong> ' . $v . '</p>';
        }
        $description = $itemSpecStr;
        if (strlen($condition_val) > 0) {
            $description = '<p><strong>Condition:</strong> ' . $condition_val . '</p>' . $description;
        }
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 5505) {
        $itemSpecStr = "";
        if (strlen($condition_val) > 0) {
            $itemSpecStr = $itemSpecStr . "<p><strong>Condition:</strong> " . $condition_val . "</p>";
        }
        if (strlen($condition_note) > 0) {
            $itemSpecStr = $itemSpecStr . "<p><strong>Condition Description:</strong> " . $condition_note . "</p>";
        }
        $description = $itemSpecStr . $description;
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 3365) {
        if (strlen($condition_val) > 0) {
            $description = '<p><strong>Condition:</strong> ' . $condition_val . '</p>' . $description;
        }
        return array("status" => "success", "message" => $description);
    } else if ( $user_id == 18206 || $user_id == 18199 || $user_id == 17718 || $user_id == 16897 || $user_id == 16857 || $user_id == 15192 || $user_id == 16012 || $user_id == 15578 || $user_id == 15029 || $user_id == 14943 || $user_id == 14695 || $user_id == 11974 || $user_id == 13233 || $user_id == 13111 || $user_id == 13094 || $user_id == 11853 || $user_id == 11385 || $user_id == 10743 || $user_id == 11053 || $user_id == 10783 || $user_id == 10755 || $user_id == 4740 || $user_id == 3360 || $user_id == 3143 || $user_id == 4057 || $user_id == 5003 || $user_id == 5779 || $user_id == 6036 || $user_id == 6424) {
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 3629) {
        $pattern = '/<!--\s*Start\s*Description\s*AucLister\s*-->(.*)<!--\s*End\s*Description\s*AucLister\s*-->/sm';
        $res1 = preg_match_all($pattern, $description, $result1);
        if ($res1) {
            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
        } else {
            return array("status" => "fail", "message" => "RegEx not matched.");
        }
        $description = iconv('windows-1250', 'utf-8', $description);
        $description = str_replace('”', '"', $description);
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 1468) {
        $description = preg_replace('/(<[^>]+) style=".*?"/i', '$1', $description);
        $description = preg_replace('/\s*style=\\"[^\\"]*\\"/', '', $description);
        $description = preg_replace("/<font[^>]+>/", "", $description);
        $description = str_replace("</font>", "", $description);
        $description = preg_replace('#&nbsp;#is', ' ', $description);
        $description = preg_replace('#<p>\s*<br[^>]*>\s*</p>#is', '', $description);
        $description = preg_replace('#<p>\s*<br[^>]*>\s*<br[^>]*>\s*</p>#is', '', $description);
        $description = preg_replace('#<p>\s*&nbsp;\s*&nbsp;\s*</p>#is', '', $description);
        $description = preg_replace('#<p>\s*&nbsp;\s*</p>#is', '', $description);
        $description = preg_replace('#<p[^>]*>\s*</p>#is', '', $description);
        $description = str_replace('align="center"', "", $description);
        $description = str_replace("<span>", "", $description);
        $description = str_replace("</span>", "", $description);
        $description = str_replace("<center>", "", $description);
        $description = str_replace("</center>", "", $description);
        $description = preg_replace('#<style[^>]*>.*?</style>#is', '', $description);
        $description = preg_replace('#<script[^>]*>.*?</script>#is', '', $description);
        $description = preg_replace('/(<[^>]+) style=".*?"/i', '$1', $description);
        $description = preg_replace('/\s*style=\\"[^\\"]*\\"/', '', $description);
        //$description = preg_replace('/<!--(.|\s)*?-->/', '', $description);
        $description = preg_replace('/<a.*?a>/', '', $description);
        $description = preg_replace('/<p[^>]*>\s*You\s*are\s*Bidding\s*on:\s*<\/p>/iU', '', $description);
        $description = preg_replace('/<p[^>]*>\s*You\s*are\s*Bidding\s*on:\s*<br>\s*<\/p>/iU', '', $description);
        $description = preg_replace('/<span[^>]*>\s*You\s*are\s*Bidding\s*on:\s*<\/span>/iU', '', $description);
        $description = str_ireplace('You are Bidding on:', '', $description);
        $description = preg_replace('/<p[^>]*>\s*You\s*are\s*Bidding\s*on\s*<\/p>/iU', '', $description);
        $description = preg_replace('/<p[^>]*>\s*You\s*are\s*Bidding\s*on\s*<br>\s*<\/p>/iU', '', $description);
        $description = preg_replace('/<span[^>]*>\s*You\s*are\s*Bidding\s*on\s*<\/span>/iU', '', $description);
        $description = str_ireplace('You are Bidding on', '', $description);
        $description = iconv('windows-1250', 'utf-8', $description);
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 2852) {
        $pattern = '/<div\s*id="description">(.*)<\/div>\s*<div\s*id="image-section"/sm';
        $res1 = preg_match_all($pattern, $description, $result1);
        if ($res1) {
            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
        } else {
            return array("status" => "fail", "message" => "RegEx not matched.");
        }
        $description = iconv('windows-1250', 'utf-8', $description);
        $description = str_replace('”', '"', $description);
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 4604) {
        $pattern1 = '/<div\s*class="itemdescription">(.*)<div\s*class="itemdescription">/sm';
        $res1 = preg_match_all($pattern1, $description, $result1);
        if ($res1) {
            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
        } else {
            return array("status" => "fail", "message" => "RegEx not matched.");
        }
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 3874) {
        $pattern = '/<!--\s*Start\s*Description\s*AucLister\s*-->(.*)<!--\s*End\s*Description\s*AucLister\s*-->/sm';
        $res1 = preg_match_all($pattern, $description, $result1);
        if ($res1) {
            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
        } else {
            $pattern = '/<!--\s*Block\s*Description\s*\/\/\s*-->(.*)<!--\s*\/\/\s*Block\s*Description\s*-->/sm';
            $res1 = preg_match_all($pattern, $description, $result1);
            if ($res1) {
                $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
            } else {
                return array("status" => "fail", "message" => "RegEx not matched.");
            }
        }
        $description = iconv('windows-1250', 'utf-8', $description);
        $description = str_replace('”', '"', $description);
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 6236) {
        $pattern = '/<div\s*id="element_558".*data-element-type="editor.elements.TitleElement"\s*data-exclude-css-removal="true"\s*data-cl-template-tag="description">(.*)<\/div>\s*<div\s*id="elementbzqovg_559"/sm';
        $res1 = preg_match_all($pattern, $description, $result1);
        if ($res1) {
            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
        } else {
            $description = "";
        }
        $description = preg_replace('/(<[^>]+) style=".*?"/i', '$1', $description);
        $description = preg_replace('/\s*style=\\"[^\\"]*\\"/', '', $description);
        $description = iconv('windows-1250', 'utf-8', $description);
        $description = str_replace('”', '"', $description);
        return array("status" => "success", "message" => $description);
    } else if ($user_id ==  6226) {
        $description = preg_replace("/<meta[^>]+>/", "", $description);
        $description = preg_replace("/<font[^>]+>/", "", $description);
        $description = str_replace("</font>", "", $description);
        $description = preg_replace("/<FONT[^>]+>/", "", $description);
        $description = str_replace("</FONT>", "", $description);
        return array("status" => "success", "message" => $description);
    } else if ($user_id ==  6216) {
        $pattern = '/<div\s*class="description"[^>]*>(.*<ul\s*class="specs">.*<\/ul>).*<div\s*class="zdzTabs\s*mdl-Box">/sm';
        $res1 = preg_match_all($pattern, $description, $result1);
        if ($res1) {
            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
        } else {
            $pattern = '/<div\s*class="description"[^>]*>(.*<div\s*class="speclist">.*<\/div>)\s*<\/div>\s*<\/div>\s*<\/div>\s*<\/div>\s*<\/div>\s*\&nbsp;\&nbsp;\s*<\/div>.*<div\s*class="zdzTabs\s*mdl-Box">/sm';
            $res1 = preg_match_all($pattern, $description, $result1);
            if ($res1) {
                $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
            } else {
                $res1 = preg_match_all($pattern, $description, $result1);
                if ($res1) {
                    $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                } else {
                    $pattern = '/<div\s*class="data-table\s*spec">\s*<h2>\s*ITEM\s*Description\s*<\/h2>(.*)<div\s*class="row"[^>]*>\s*<div\s*class="col-md-12\s*col-sm-12\s*col-xs-12">\s*<div\s*class="zdzTabs\s*mdl-Box">/sm';
                    $res1 = preg_match_all($pattern, $description, $result1);
                    if ($res1) {
                        $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
                    } else {
                        return array("status" => "fail", "message" => "RegEx not matched.");
                    }
                }
            }
        }
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 4186) {
        $description = "";
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 3766) {
        $itemSpecificJson = json_decode($item_specific, true);
        $itemSpecsStr = "";
        if (isset($itemSpecificJson[0]["_1"])) {
            foreach ($itemSpecificJson as $obj) {
                $itemSpecsStr = $itemSpecsStr . '<p><strong>' . $obj["_1"] . ':</strong> ' . $obj["_2"][0] . '</p>';
            }
        } else {
            foreach ($itemSpecificJson as $k => $v) {
                $itemSpecsStr = $itemSpecsStr . '<p><strong>' . $k . ':</strong> ' . $v . '</p>';
            }
        }
        $description = $description . $itemSpecsStr;
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 4946) {
        $description = str_replace("&nbsp;", " ", $description);
        $description = str_replace('Â ', "", $description);
        $description = str_replace('Ã‚', "", $description);
        $description = preg_replace("/<font[^>]+>/", "", $description);
        $description = str_replace("</font>", "", $description);
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 4481) {
        $pattern = '/<div\s*class="discription\s*disc-product">\s*<h1\s*class="tittle">\s*Product\s*description<\/h1>(.*)<\/div>\s*<div\s*class="discription">/sm';
        $res1 = preg_match_all($pattern, $description, $result1);
        if ($res1) {
            $description = isset($result1[1][0]) ? trim($result1[1][0]) : "";
        } else {
            return array("status" => "fail", "message" => "RegEx not matched.");
        }
        $description = iconv('windows-1250', 'utf-8', $description);
        $description = str_replace('”', '"', $description);
        return array("status" => "success", "message" => $description);
    } else if ($user_id == 6122) {
        $itemSpecificJson = json_decode($item_specific, true);
        $itemSpecStr = "";
        if (strlen($condition_val) > 0) {
            $itemSpecStr = $itemSpecStr . "<p><strong>Condition:</strong> " . $condition_val . "</p>";
        }
        if (strlen($condition_note) > 0) {
            $itemSpecStr = $itemSpecStr . "<p><strong>Condition Description:</strong> " . $condition_note . "</p>";
        }
        foreach ($itemSpecificJson as $k => $v) {
            $itemSpecStr = $itemSpecStr . "<p><strong>" . $k . "</strong> " . $v . "</p>";
        }
        $description = $itemSpecStr;
        return array("status" => "success", "message" => $description);
    }
    else if($user_id == 21489){
        echo "21489 description condition ";
        $conditionStr = "";
        if ($is_condition_val && strlen($condition_val) > 0) {
            $conditionStr = $conditionStr . "<p><strong>Condition:</strong> " . $condition_val . "</p>";
        }
        if ($is_condition_note && strlen($condition_note) > 0) {
            $conditionStr = $conditionStr . "<p><strong>Condition Description:</strong> " . $condition_note . "</p>";
        }
        $description = $conditionStr ;
        echo "21489 description condition ".$description;
        return array("status" => "success", "message" => $description);
    }
    
    
    else {
        echo " condition enabled for ".$user_id;
        //sleep(3);
        // applied filter based on DB settings
        $conditionStr = "";
        if ($is_condition_val && strlen($condition_val) > 0) {
            $conditionStr = $conditionStr . "<p><strong>Condition:</strong> " . $condition_val . "</p>";
        }
        if ($is_condition_note && strlen($condition_note) > 0) {
            $conditionStr = $conditionStr . "<p><strong>Condition Description:</strong> " . $condition_note . "</p>";
        }
        $itemSpecificStr = "";
        if ($is_item_specific) {
            $item_specificObj = json_decode($item_specific, true);
            foreach ($item_specificObj as $k => $v) {
                $itemSpecificStr = $itemSpecificStr . '<p><strong>' . $k . ':</strong> ' . $v . '</p>';
            }
        }
        $description = $conditionStr . $itemSpecificStr . $description;
        echo "no condition matched";
        return array("status" => "success", "message" => $description);
    }
    return array("status" => "fail", "message" => "RegEx not matched.");       
}

function removeHTMLFormatting($description, $boldremoval = true)
{
    $description = preg_replace("/<body[^>]+>/", "", $description);
    $description = str_replace("</body>", "", $description);
    $description = preg_replace('/(<[^>]+) style=".*?"/i', '$1', $description);
    $description = preg_replace('/\s*style=\\"[^\\"]*\\"/', '', $description);
    $description = preg_replace("/<font[^>]+>/", "", $description);
    $description = str_replace("</font>", "", $description);
    $description = preg_replace("/<FONT[^>]+>/", "", $description);
    $description = str_replace("</FONT>", "", $description);
    $description = preg_replace('#&nbsp;#is', ' ', $description);
    $description = preg_replace('#<p>\s*<br[^>]*>\s*</p>#is', '', $description);
    $description = preg_replace('#<p>\s*<br[^>]*>\s*<br[^>]*>\s*</p>#is', '', $description);
    $description = preg_replace('#<p>\s*&nbsp;\s*&nbsp;\s*</p>#is', '', $description);
    $description = preg_replace('#<p>\s*&nbsp;\s*</p>#is', '', $description);
    $description = preg_replace('#<p[^>]*>\s*</p>#is', '', $description);
    if ($boldremoval) {
        $description = str_replace("<b>", "", $description);
        $description = str_replace("</b>", "", $description);
        $description = str_replace("<strong>", "", $description);
        $description = str_replace("</strong>", "", $description);
    }
    $description = str_replace("<span>", "", $description);
    $description = str_replace("</span>", "", $description);
    $description = str_replace("<center>", "", $description);
    $description = str_replace("</center>", "", $description);
    $description = str_ireplace('align="center"', "", $description);
    $description = preg_replace('#<style[^>]*>.*?</style>#is', '', $description);
    $description = preg_replace('#<script[^>]*>.*?</script>#is', '', $description);
    $description = preg_replace("/<link[^>]+>/", "", $description);
    $description = preg_replace("/<img[^>]+>/", "", $description);
    $description = preg_replace('#<audio[^>]*>.*?</audio>#is', '', $description);
    $description = preg_replace('/<meta[^>]+>/', '', $description);

    return $description;
}
?>
