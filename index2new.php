<?php
// ✅ Database Connection (Using DigitalOcean Environment Variables - VPC Default Mode)
$host = getenv('DB_HOST');
$username = getenv('DB_USER');
$password = getenv('DB_PASS');
$dbname = getenv('DB_NAME');
$port = (int)getenv('DB_PORT');
$sslmode = getenv('DB_SSLMODE'); // 'REQUIRED'

// Létrehozzuk az objektumot
$conn = mysqli_init();

// NEM állítunk be semmilyen kézi SSL opciót
// A rendszerre bízzuk a belső hálózati kapcsolat kezelését

// Csatlakozás a mysqli_real_connect segítségével, SSL flag NÉLKÜL
// Az sslmode='require' miatt a hostnév fogja kikényszeríteni az SSL-t, ha kell.
if (!mysqli_real_connect($conn, $host, $username, $password, $dbname, $port)) {
    // Ha a kapcsolat sikertelen, írjuk ki a hibát és álljunk le
    die("❌ Connection failed (VPC Default Mode Failed): " . mysqli_connect_error());
}

// Ha a kapcsolat sikeres, állítsuk be a karakterkódolást
mysqli_set_charset($conn, "utf8");
echo "✅ Database Connected Successfully<br>";
 
require_once("helpers/deschelper.php");
require_once("helpers/shopifyGraphQL.php");
require_once("helpers/general.php");

	$logfile = fopen("importToShopifynew23newsomay_new".date('Y-m-d').".txt", "a+") or die("Unable to open log file!");
	addlog("Execution Started", "INFO");
	addlog("Execution Started user ".json_encode($_GET), "INFO");

	
	$categoryLogicArr = array(35027,23749,19563,21651,19477,21596,18278,21221,18965,18177,19321,19300,19083,2119,18647,18646,18593,16333,16873,2961,1280,17595,17422,11930,16968,16997,17001,16993,16969,16283,16480,15434,16055,15346,15410,15221,15295,15153,15057,14669,13821,13915,13979,13884,13434,13526,13233,13174,12409,12343,11993,11668,5736,2865,9276,9431,9388,9441,9332,9294,9188,8442,7863,7512,7682,7375,6943,5925,113,490,997,1056,1193,1447,2653,2904,37236,46190,44582,48551);
    

	
    $user_id;
	if($_GET['user_id']){
	  $user_id= $_GET['user_id']; 
	}
	addlog("Execution Started user ".$user_id, "INFO");
	$result ;
    print_r($user_id);
	if($user_id != '' ){
	

		$result = $conn->query("select distinct user_id from products where status = 'Import in progress' and user_id in (select id from users where membershiptype = 'free' )   and user_id !=48427  and user_id = $user_id ");
        print_r($result);
	}
	else{
		 
		$result = $conn->query("select distinct user_id from products where status = 'Import in progress' and user_id in (select id from users where membershiptype = 'free' )   and user_id  and user_id !=48427  group by user_id  ");
	}
	
   
$totalcount =0;

	addlog("Execution Started -5678 ", "INFO");
	$userArr = array();
	$biguser = false;
    print_r($result);
	if ($result->num_rows > 0) {
		while($row = $result->fetch_assoc()) {
			$user_id = $row['user_id'];
			$importeditems = $conn->query("select count(*) as cnt from products where user_id =".$user_id." and status='imported' ");
			$importeditemsrow = $importeditems->fetch_assoc();
			// $conn->query("insert into importqueue (user_id, dateofmodification) values(".$user_id.", now())")or die("error in queryb,svx");
			// addlog("inserted user  id in q".$user_id, "INFO");
			$userArr[] = $user_id;
			
			
		}
	}
	addlog("Execution Started -678888888 ", "INFO");
	print_r($userArr);
	echo json_encode($biguser). " is true or false";
	//exit(0);
    $masterRegExArr = array();
	foreach($userArr as $user_id){
        echo "dddddddddddddddddddddd";
		addlog("Execution Started -12345666 ", "INFO");
	//	$user_id = $row['user_id'];
    addlog("process start for  user  id in q ".$user_id, "INFO");
		echo $user_id."--";
		$userresult = $conn->query("select * from users where installationstatus = 1 and id = ".$user_id);
		if ($userresult->num_rows > 0) {
			while($userrow = $userresult->fetch_assoc()) {
				echo "2";
				
				addlog("Execution Started -rtrrrrr ", "INFO");
				$shopurl = $userrow['shopurl'];
                $ownername = $userrow['ownername'];
			    $email = $userrow['email'];
				$token = $userrow['token'];
				$multi_location_enabled = $userrow['multi_location_enabled'];
				$published = 1;
				$fulfilment_service = 'manual';
				$inventory_policy = 'shopify';
				$includeoutofstock = 0;
				$location_id = "";
				$settingsResult = $conn->query("select * from settings where user_id = ".$user_id);
				if($settingsResult->num_rows < 1){
					// Settings not defined for user
					echo "no setting";
					continue;
				}
				$settingsRow = $settingsResult->fetch_assoc();
				$published = $settingsRow['published'];
				$fulfilment_service = $settingsRow['fulfilment_service'];
				$inventory_policy = $settingsRow['inventory_policy'];
				$location_id = $settingsRow['shopifylocationid'];				
				if($location_id == ""){
					$location_id = getMainLocation($user_id, $shopurl, $token);
					print_r($location_id);
					if(!$location_id){	
						echo "no location";					
						continue;
					}
					$conn->query("update settings set shopifylocationid = '".mysqli_real_escape_string($conn, $location_id)."' where user_id = ".$user_id);
				}
				$existingSKUs = getExistingSKUs($user_id);
				$ebKeysResult = $conn->query("select * from eb_keys where valid = 1 and user_id = ".$user_id);
				if($ebKeysResult->num_rows < 1){
				    echo "key missing";
				//	continue;
				}
				$ebKeysRow = $ebKeysResult->fetch_assoc();
				$ebtoken = $ebKeysRow['token'];
				$siteid = $ebKeysRow['siteid'];
                echo "hello";
             
				addlog("Execution Started -12weweeeetrt456 ".$user_id, "INFO");
				processProducts($user_id, $token, $shopurl, $existingSKUs, $includeoutofstock, $published, $fulfilment_service, $inventory_policy, $location_id, $ebtoken, $siteid, $settingsRow,$totalcount,$ownername,$email);					
			}
		}
		// $conn->query("delete from processimports where user_id = ".$user_id);
		$conn->query("delete from importqueue where user_id = ".$user_id);
	}	
	//$conn->query("update crons set isrunning = 0 where crontype = 'importtoshopifynew'");
	echo "update crons set isrunning = 0 where crontype = 'importtoshopifynew'".json_encode($_GET);

    function applyOptions47855(){
        global $conn;
        $result = $conn->query("SELECT * FROM `products` WHERE user_id = 47855 and option1name = '' and status = 'Import in progress'");
        if($result->num_rows > 0){
    	    while($row = $result->fetch_assoc()){
    	        $item_specific = $row['item_specific'];
    			if(strlen($item_specific) > 0){
    				$itemSpecArr = json_decode($item_specific, true);
    				if(isset($itemSpecArr['Handedness'])){
    				    $size = $itemSpecArr['Handedness'];
    				    $conn->query("update products set option1name = 'Handedness' where user_id = 47855 and product_id = ".$row['product_id']);
    	                $conn->query("update product_variants set option1val = '".mysqli_real_escape_string($conn, $size)."' where user_id = 47855 and product_id = ".$row['product_id']);
    	                return true;
    				}
    			}
    			//$conn->query("update products set block = 1 where user_id = 14033 and product_id = ".$row['product_id']);
    	    }    
    	}
    }
    
    
    function filterTitle16968($title){
        $titleArr = array("!", "“", "Top", "Rar", "kg", "Sammlung", "selten", "konvolut");
        foreach($titleArr as $filter){
            $title = str_ireplace($filter, "", $title);
        }
        $title = trim($title);
        return $title;
    }
    
    
	function applyRegEx($user_id, $description){
		global $conn, $masterRegExArr;
		$regExArr = array();	
		if(!array_key_exists($user_id, $masterRegExArr)){
			$result = $conn->query("select * from regular_expressions where user_id = ".$user_id." order by created_at asc");			
			if($result->num_rows > 0){
				while($row = $result->fetch_assoc()){
					$regExArr[] = $row['regex'];
				}
			}
			$masterRegExArr[$user_id] = $regExArr;
		} else {
			$regExArr = $masterRegExArr[$user_id];
		}
		$matched = 0;
		$newdesc = $description;
		foreach($regExArr as $regex){
			$matched = 1;
			$res = preg_match_all($regex, $description, $result1);
			if($res){
				$matched = 2;
				$newdesc = isset($result1[1][0])?trim($result1[1][0]):"";
				break;
			}
		}	
		$newdesc = iconv('windows-1250', 'utf-8', $newdesc); 
		$newdesc = str_replace('”', '"', $newdesc);
		$newdesc = str_replace("Â","",$newdesc);
		return array("matched" => $matched, "newdesc" => $newdesc);
	}
    
    function generateTagsFromTitle($title){
        $filters = array("&","/","-","+","|");
        $tags = array();
        $temp = explode(" ", $title);
        $temp = array_map('trim', $temp);
        foreach($temp as $v){
            if(!in_array($v, $filters)){
                $tags[] = $v;
            }
        }
        return $tags;
    }
    
	function processProducts($user_id, $token, $shopurl, $existingSKUs, $includeoutofstock, $published, $fulfilment_service, $inventory_policy, $location_id, $ebtoken, $siteid, $settingsRow,$totalcount,$ownername,$email) {
	    echo $user_id."--importprocessproducts";
		addlog("import  Started - ".$user_id, "INFO");

		global $conn,$biguser;
		$productQuery;
		echo $biguser;
		
		addlog("import  Started small user- ".$user_id, "INFO");
	    echo $biguser;
		//exit(0);
		// if(!$biguser){
			//echo "all users less then 300 ".json_encode($biguser). " is true or false";
			addlog("import  Started small user- ".$user_id, "INFO");
	     	$productQuery = "select * from products where title != '' and status = 'Import in progress' and shopifyproductid = '' and  duplicate = 0 and block = 0 and deleted = 0 and user_id = ".$user_id."";
     	//}
		addlog("import  Started and product found- ".$user_id, "INFO");
		$productResult = $conn->query($productQuery);
		echo  "ALl record".json_encode($productResult);
		if ($productResult->num_rows > 0 ) {
	             echo $productResult->num_rows;	
           	while($productRow = $productResult->fetch_assoc()) {
           	   
				   
				$product_id = $productRow['product_id'];
				if ($user_id == 47855 && strpos($productRow['product_type'], 'Golf Clubs') === false) {
                  $conn->query("update products set block = 1 where user_id =47855 and product_id = ".$product_id);
					continue;
					// Your code here
				}
				echo "productid-------".$product_id;
				$option1name = $productRow['option1name'];
                echo "select * from product_variants where product_id = ".$product_id." and user_id = ".$user_id." and block = 0 and duplicate = 0 and shopifyvariantid = ''";
                
				$variantResult = $conn->query("select * from product_variants where product_id = ".$product_id." and user_id = ".$user_id." and block = 0 and duplicate = 0 and shopifyvariantid = ''");
				$noOfVariants = $variantResult->num_rows;
                 
    				if($noOfVariants == 1 && $option1name === ''){
    					echo "in456";
						handleProductWithoutVariants($user_id, $token, $shopurl, $existingSKUs, $includeoutofstock, $published, $fulfilment_service, $inventory_policy, $productRow, $location_id, $ebtoken, $siteid, $settingsRow,$totalcount);
    				} else {
						echo "567";
    					handleProductsWithVariants($user_id, $token, $shopurl, $existingSKUs, $includeoutofstock, $published, $fulfilment_service, $inventory_policy, $productRow, $location_id, $ebtoken, $siteid, $settingsRow,$totalcount);
    				
    				    
    					
    				}   
				
			}
		}
		else{

			//$conn->query("delete from processimports  where user_id =".$user_id);
		}
	}
	
	function removeDescriptionTemplate($user_id, $description){
		$newDescription = $description;
		$pattern = '/<div\s*class="desc-hedtitle">Product\s*Description<\/div>(.*)<\/div>\s*<!--\s*front\s*view\s*tab\s*-->/sm';
		$res1 = preg_match_all($pattern, $description, $result1);
		if($res1){			
			$newDescription = isset($result1[1][0])?trim($result1[1][0]):"";
		} else {
			$pattern = '/<div\s*class="desc-hedtitle"\s*style=".*">Product\s*Description<\/div>(.*)<\/div>\s*<!--\s*front\s*view\s*tab\s*-->/sm';
			$res1 = preg_match_all($pattern, $description, $result1);
			if($res1){			
				$newDescription = isset($result1[1][0])?trim($result1[1][0]):"";
			} else {
				$pattern = "/(<div\s*class='description'\s*style='.*'>.*)<div\s*class='speclist'>/sm";
				$res1 = preg_match_all($pattern, $description, $result1);
				if($res1){			
					$newDescription = isset($result1[1][0])?trim($result1[1][0]):"";
				} else {
					$pattern = '/<div\s*class="desc-hedtitle">Product\s*Description<\/div>(.*)<\/div>\s*<font\s*size="3">\s*<!--\s*front\s*view\s*tab\s*-->/sm';
					$res1 = preg_match_all($pattern, $description, $result1);
					if($res1){			
						$newDescription = isset($result1[1][0])?trim($result1[1][0]):"";
					} else {
						$pattern = '/<div\s*class="desc-hedtitle"><font\s*face="Arial">Product\s*Description<\/font><\/div>(.*)<\/div>\s*<!--\s*front\s*view\s*tab\s*-->/sm';
						$res1 = preg_match_all($pattern, $description, $result1);
						if($res1){			
							$newDescription = isset($result1[1][0])?trim($result1[1][0]):"";
						} else {
							$pattern = '/(<div\s*class="description"\s*style="font-family:Arial;font-size:14px;color:#646464;font-weight:normal;">.*<p\s*class="description-item-number">.*)<\/div>\s*<div\s*class="col-lg-4"\s*id="hydraimg">/sm';
							$res1 = preg_match_all($pattern, $description, $result1);
							if($res1){			
								$newDescription = isset($result1[1][0])?trim($result1[1][0]):"";
							} else {
								return false;							
							}
						}
					}
				}
			}
		}	
		return $newDescription;
	}

	function handleProductWithoutVariants($user_id, $token, $shopurl, $existingSKUs, $includeoutofstock, $published, $fulfilment_service, $inventory_policy, $productRow, $location_id, $ebtoken, $siteid, $settingsRow,$totalcount) {
	    echo "handleProductWithoutVariants  count".$totalcount;
		addlog("handleProductWithoutVariants - ", "INFO");
		global $conn, $skipRegx, $categoryLogicArr;
		$catArr = array();
		if(in_array($user_id, $categoryLogicArr)){
			$result = $conn->query("select * from storecategories where user_id = ".$user_id);
			addlog("import  Started and product found- ".$user_id, "INFO");
			if($result->num_rows > 0){
				while($row = $result->fetch_assoc()){
					
					$category_id = $row['category_id'];
					$category_name = $row['category_name'];
					$parent_id = $row['parent_id'];
					$catArr[$category_id] = array("category_name" => $category_name, "parent_id" => $parent_id);
				}
			}
		}
   
		$shippingCostArr = array();
    	
		$product_id = $productRow['product_id'];
		$ebayitemid = $productRow['ebayitemid'];
            echo $ebayitemid; 
		$title = $productRow['title'];
       
		$condition_note = $productRow['condition_note'];
		$condition_val = $productRow['condition_val'];		
		$brand = $productRow['brand'];
		$product_type = $productRow['product_type'];
	// $item_specific = $productRow['item_specific'];
		$searchstring12 = $productRow['searchstring'];
		$category_idv = $productRow['storecategoryid'];
		$itemSpecificObj = json_decode($item_specific, true);
		$tags = array();
            echo $user_id." in progress";
		$variantResult = $conn->query("select * from product_variants where product_id = ".$product_id." and user_id = ".$user_id." and block = 0 and duplicate = 0 and shopifyvariantid = ''");
		
		if($variantResult->num_rows < 1){
		    return true;
		}
		$variantRow = $variantResult->fetch_assoc();

		$variant_id = $variantRow['id'];
		$sku = $variantRow['sku'];
		$price = $variantRow['price'];
		$orig_price = $variantRow['orig_price'];
		$quantity = $variantRow['quantity'];
		$weight = $variantRow['weight'];
		$weight_unit = $variantRow['weight_unit'];
		$productid = $variantRow['productid'];



		if($user_id == 46190){

			$tags[] =$searchstring12;
		}
		if($user_id == 44719 || $user_id == 45165) {            
    		$tags[] = "eb_".$searchstring12;
			$tags[]=$ebayitemid;
        }

		if($user_id == 45000   &&  $searchstring12 !='UK'){

		$conn->query("update products set block = 1 where user_id = 45000 and ebayitemid =".$ebayitemid );
		return false;

		}
		


		if($user_id == 45738) {            
    		$sku =  $sku;
        }

		if($user_id == 46021) {            
    		$sku =  $sku;
        }

        if(strlen($sku) > 255  ){
		   $str = wordwrap($sku, 254);
			$str = explode(",", $str);
			$sku = $str[0];
		}
		

		if($user_id == 48043){
		    if(is_array($itemSpecificObj)){
			    foreach($itemSpecificObj as $k => $v){
                    if($k == "MPN" || $k == "Manufacturer Part Number"){
                        $sku = $v;   
					    break;
                    }
			    }
            }
		}



		if( $user_id == 46674){
		    if(is_array($itemSpecificObj)){
			    foreach($itemSpecificObj as $k => $v){
                    if($k == "MPN" || $k == "Manufacturer Part Number"){
                        $sku = $v;   
					    break;
                    }
			    }
            }
		}
		
       if($user_id == 19443){
			$price = applyPriceMarkupSettingsRow19443($price, $settingsRow);
		}
		else {
			$price = applyPriceMarkupSettingsRow($price, $settingsRow);
		} 


		if ($user_id == 47617){
			//GBP TO EURO
			$price =$variantRow['orig_price'];
			if($price > 0){
			  $price = $price*1.20;
			}
			else{
				$price = $variantRow['price'];
				$price= $price*1.20;
			}
			//$price = round($price);
		}


		if($user_id == 44582 ){
		    $shippingcost = 0;

			$tags = array();

			echo "select * from shippingcost where user_id = ".$user_id." and product_id=".$product_id;
			$result = $conn->query("select * from shippingcost where user_id = ".$user_id." and product_id=".$product_id);
    	    if ($result->num_rows > 0) {
    		    while($shippingCost = $result->fetch_assoc()) {	
    		        $shippingcost = $shippingCost['cost'];    
    		        $tags[] ="Ship_".$shippingcost ;
    		    }
    	    }

			// 
			
			echo"     valuee tagess        "."\n";
			print_r($tags);

		}


		if($user_id == 44582){
			
			$temp = explode(":", $product_type);
		    $product_type = trim(end($temp));
				$tags[] =$product_type ;

				print_r($tags);
		}

		if($user_id == 47582){
			
	
	
				$temp = explode(":", $product_type);
	
				$segment1 = $temp[0]; 
				$tags[] = $segment1;
				$segment2 = $temp[1]; 
				$tags[] = $segment2;
				$segment3 = $temp[2];
				$tags[] = $segment3; 
				$segment4 = $temp[3]; 
				$tags[] = $segment4; 
			//}
	
		}



		if($user_id == 44582){			
			$storecategoryid = $productRow['storecategoryid'];
			
			$product_type = "";
			if(array_key_exists($storecategoryid, $catArr)){
				$temp = $catArr[$storecategoryid];
				$category_name = $temp['category_name'];
				$parent_id = $temp['parent_id'];
				$tags[] = $category_name;
				//$product_type = $category_name;
				if($parent_id != ""){
					if(array_key_exists($parent_id, $catArr)){
						$temp1 = $catArr[$parent_id];
						$category_name1 = $temp1['category_name'];
						$tags[] = $category_name1;
						//$product_type = $category_name1;
					}
				}
			}
		}



			print_r($tags);


		
		if($user_id == 44841 && $weight_unit =='lb'){
		    $weight = $weight*0.453592;
		    $weight_unit = "kg";
		}
		
		

		if(strlen($title) > 255){
			$str = wordwrap($title, 254);
			$str = explode("\n", $str);
			$title = $str[0];
		}
		
		$description = "";
		if($user_id != 16702 ){
            $description = getProdDescription($user_id, $product_id);
		     
		}							
		
		$is_item_specific = false;
        $is_condition_note = false;
     
		if($settingsRow["desc_cond "] == 1){
			$is_condition_note = true;
			$is_condition_val =  true;
		}
		if($settingsRow["desc_itemspecs"] == 1){
			$is_item_specific = true;			
		}
		//error can be

		echo"description start";
		echo "anjali".json_encode($is_item_specific,true);
		//sleep(3);
		$descFilterResp = applyDescriptionFilter($description, $item_specific, $condition_note, $condition_val, $is_item_specific, $is_condition_note, $is_condition_val, $user_id);
        print_r($descFilterResp);
		if($descFilterResp["status"] == "fail"){
			echo "error may be here ".$user_id;
		    if(!$skipRegx){

				echo "hgghhghghghghghghghghghg";
		        return false;   
		    }
		} else {
		    $description = $descFilterResp["message"];
        }
		if(strlen($description) == 0){
			$description = $title;
		}
		
		
		     
			if($user_id == 43580) {
				$result = $conn->query("select * from storecategories where category_id ='".$category_idv."' and user_id = " . $user_id);
				if ($result->num_rows > 0) {
					while ($row = $result->fetch_assoc()) {
						$tags = $row['category_name'];
						// $shopifycategoryid = $row['shopifycategoryid'];
						// $catArr[$category_id] = $shopifycategoryid;
					}
				}
			}  

			if($user_id == 44401) {
				$result = $conn->query("select * from storecategories where category_id ='".$category_idv."' and user_id = " . $user_id);
				if ($result->num_rows > 0) {
					while ($row = $result->fetch_assoc()) {
						$tags[] = $row['category_name'];
						// $shopifycategoryid = $row['shopifycategoryid'];
						// $catArr[$category_id] = $shopifycategoryid;
					}
				}
			} 
       

		if($user_id == 46926){
		    $temp = explode(":", $product_type);
		    $product_type = trim(end($temp));
		}



		//46926
       
		if($user_id == 44893){
			$temp = explode(":", $product_type);
			$product_type = end($temp);
            $tags[]= $product_type;
			echo $product_type."                        " ;
			
		}
       


        if(strlen($sku) > 255  ){
		   $str = wordwrap($sku, 254);
			$str = explode("\n", $str);
			$sku = $str[0];
		}
     
        echo "17346 in progress imags";
		$imageArr = array();
		$position = 1;		
		$imageResult = $conn->query("select * from product_images where user_id = ".$user_id." and variant_id = ".$variant_id);
		if($imageResult->num_rows > 0){
			while($imageRow = $imageResult->fetch_assoc()){
				$imgurl = $imageRow['imgurl'];
				if($user_id != 15715){
				    $imgurl = str_replace("_12.JPG", "_57.JPG", $imgurl);
    				$imgurl = str_replace("_1.JPG", "_57.JPG", $imgurl);
    				$imgurl = str_replace("_0.JPG", "_57.JPG", $imgurl);
    				$imgurl = str_replace("_6.JPG", "_57.JPG", $imgurl);
				} else {
				    if (strpos($imgurl, 'ebayimg') !== false) {
                        $imgurl = str_replace("_12.JPG", "_57.JPG", $imgurl);
        				$imgurl = str_replace("_1.JPG", "_57.JPG", $imgurl);
        				$imgurl = str_replace("_0.JPG", "_57.JPG", $imgurl);
        				$imgurl = str_replace("_6.JPG", "_57.JPG", $imgurl);
                    }   
				}
				if($user_id == 38128){
				    $imgurl = str_replace("_12.PNG", "_57.PNG", $imgurl);
    				$imgurl = str_replace("_1.PNG", "_57.PNG", $imgurl);
    				$imgurl = str_replace("_0.PNG", "_57.PNG", $imgurl);
    				$imgurl = str_replace("_6.PNG", "_57.PNG", $imgurl);
                    
				}
               
				 if($user_id == 43059){
				    $imageArr[] = array("src" => trim($imgurl), "position" => $position++,"width" => 1200,"height" => 1600);    
					
					print_r($imageArr);

					//exit(0);
			    }
				 else {
					
			        $imageArr[] = array("src" => trim($imgurl), "position" => $position++);
					
					
					
			    }      
			}
		}
		
print_r(   $imageArr);
		if(in_array($user_id, $categoryLogicArr)){
			$storecategoryid = $productRow['storecategoryid'];
			$storecategoryid2 = $productRow['storecategoryid2'];
			if(array_key_exists($storecategoryid, $catArr)){
				$temp = $catArr[$storecategoryid];
				$category_name = $temp['category_name'];
				$parent_id = $temp['parent_id'];
				$tags[] = $category_name;
				$product_type = $category_name;
				if($parent_id != ""){
					if(array_key_exists($parent_id, $catArr)){
						$temp1 = $catArr[$parent_id];
						$category_name1 = $temp1['category_name'];
						$parent_id = $temp1['parent_id'];
						$tags[] = $category_name1;
						if($user_id != 19321 && $user_id != 19563 ){
						   $product_type = $category_name1;
						  }
						if($parent_id != ""){
        					if(array_key_exists($parent_id, $catArr)){
        						$temp2 = $catArr[$parent_id];
        						$category_name2 = $temp2['category_name'];
        						$tags[] = $category_name2;
								if($user_id != 19321 && $user_id != 19563){
        						   $product_type = $category_name2;
							   }
        					}
        				}
					}
				}
			}
			if(array_key_exists($storecategoryid2, $catArr)){
				$temp = $catArr[$storecategoryid2];
				$category_name = $temp['category_name'];
				$parent_id = $temp['parent_id'];
				$tags[] = $category_name;
				if($parent_id != ""){
					if(array_key_exists($parent_id, $catArr)){
						$temp1 = $catArr[$parent_id];
						$category_name1 = $temp1['category_name'];
						$tags[] = $category_name1;
					}
				}
			}
			if($user_id == 15057){
			    if($storecategoryid == "27421515014"){ // LAMBSKIN LEATHER
			        $tags[] = "retail_lambskins";
			    } else if($storecategoryid == "27421516014"){ // GOATSKIN LEATHER
			        $tags[] = "retail_goatskins";
			    } else if($storecategoryid == "27421517014"){ // METALLIC LEATHER
			        $tags[] = "retail_metallics";
			    } else if($storecategoryid == "1095742014"){ // CALF/COW LEATHER
			        $tags[] = "retail_calf/cow";
			    } else {
			        $conn->query("update products set block = 1, status = 'Ready to Import' where user_id = 15057 and product_id = ".$productRow['product_id']);
			        return false;
			    }
			}




		}
	


		   if($user_id == 45000){
		
			$item_specific = $productRow['item_specific'];
			if(strlen($item_specific) > 0){
				$jsonObj = json_decode($item_specific, true);
				
				if(isset($jsonObj['Size'])){
    	            $tags[] = trim($jsonObj['Size']);
    	        }
				
				
				
			}	
	   	} 
		

		

		if($user_id == 46026  &&   $condition_val =="New other (see details)" ){
			$tags[]="take off";
		
	   	}

		$comparePrice = 0;
	



		
		// Add settings from DB
		$dbtags = trim($settingsRow['tags']);
		$dbvendor = trim($settingsRow['vendor']);
		$dbproduct_type = trim($settingsRow['product_type']);
		$dbtaxable = $settingsRow['taxable'];
		if($dbvendor != ""){
			if($user_id == 18969){
				 echo "1st vendor $dbvendor";
                if($dbvendor == "HG Garage" && $productRow['brand'] == 'KTM'){
					$brand = "KTM";
					echo "anjali from db with HGgrage $brand";
				}
				else {
					$brand = $dbvendor;
					echo "anjali from db without HGgrage $brand";
				}
			}
			else{
			$brand = $dbvendor;
			 echo "anjali other  $brand";
			}
		}
		if($dbproduct_type != ""){
			$product_type = $dbproduct_type;
		}
		if($dbtags != ""){
			$tempTags = explode(",", $dbtags);
			foreach($tempTags as $tempTag){
				$tags[] = trim($tempTag);
			}
		}
		
		$taxable = true;
		if($dbtaxable == 0){
			$taxable = false;
		}
		
		$pubstatus = "active";
        
		if(strlen($sku) > 255  ){
			$str = wordwrap($sku, 254);
			 $str = explode(",", $str);
			 $sku = $str[0];
		 }


		 if($user_id == 41563){
			//die();

			$p_category_name ="";
			$product_id = $productRow['product_id'];

			echo"select category_name from storecategories where shopifycategoryid != '' and user_id = '" . $user_id."'  and category_id in (select storecategoryid from products where user_id= '".$user_id."'  and  product_id = '".$product_id."')";

			$result = $conn->query("select category_name,category_id from storecategories where shopifycategoryid != '' and user_id = '" . $user_id."'  and category_id in (select storecategoryid from products where user_id= '".$user_id."'  and  product_id = '".$product_id."') ");
			if($result->num_rows > 0){


				// while (
					$row = $result->fetch_assoc();
					//) {
					$child_category_name =$row['category_name'];
					$child_category_id  =$row['category_id'];
                    
					echo"hkjhhghkhkjhkjh".$child_category_id;

					$tags[]=$child_category_name;

				
                   
					$newcategory = $conn->query("select * from storecategories where user_id = 41563");

					//echo "select * from storecategories where user_id = 41563";
			       
		     	if($newcategory->num_rows > 0){

				
				     while($row = $newcategory->fetch_assoc()){
					
					$category_id = $row['category_id'];

					
					$category_name = $row['category_name'];
					$parent_id = $row['parent_id'];
					$catArr[$category_id] = array("category_name" => $category_name, "parent_id" => $parent_id);
				
				}

				foreach ($catArr as $categoryId => $categoryData) {
					if ($categoryData['category_name'] == $child_category_name && $categoryId == $child_category_id) {
						$parent_id = $categoryData['parent_id'];
						break; 
					}
				}
				if ($parent_id != ' ') {

					foreach ($catArr as $categoryId => $categoryData) {
						if ($categoryId == $parent_id) {
							$p_category_name = $categoryData['category_name'];
							break; 
						}
					}
					
				} 

				if ($p_category_name != ' ') {

					echo"herrrrrr isss        ";

					echo "Parent id  ".$parent_id;

					echo"<br></br>";
					echo"                   ";

					echo "Parent Category name ". $p_category_name;

					$tags[]=$p_category_name;
					
				} else {
					echo "Category not found";
				}


				echo"here is tags ";
                print_r($tags);
				
				
			}

			
		}

		
		}


		if($user_id == 47617){
			//die();

			$p_category_name ="";
			$product_id = $productRow['product_id'];

			echo"select category_name from storecategories where shopifycategoryid != '' and user_id = '" . $user_id."'  and category_id in (select storecategoryid from products where user_id= '".$user_id."'  and  product_id = '".$product_id."')";

			$result = $conn->query("select category_name,category_id from storecategories where shopifycategoryid != '' and user_id = '" . $user_id."'  and category_id in (select storecategoryid from products where user_id= '".$user_id."'  and  product_id = '".$product_id."') ");
			if($result->num_rows > 0){


				// while (
					$row = $result->fetch_assoc();
					//) {
					$child_category_name =$row['category_name'];
					$child_category_id  =$row['category_id'];
                    
					echo"hkjhhghkhkjhkjh".$child_category_id;

					$tags[]=$child_category_name;

				
                   
					$newcategory = $conn->query("select * from storecategories where user_id = 47617");

					//echo "select * from storecategories where user_id = 41563";
			       
		     	if($newcategory->num_rows > 0){

				
				     while($row = $newcategory->fetch_assoc()){
					
					$category_id = $row['category_id'];

					
					$category_name = $row['category_name'];
					$parent_id = $row['parent_id'];
					$catArr[$category_id] = array("category_name" => $category_name, "parent_id" => $parent_id);
				
				}

				foreach ($catArr as $categoryId => $categoryData) {
					if ($categoryData['category_name'] == $child_category_name && $categoryId == $child_category_id) {
						$parent_id = $categoryData['parent_id'];
						break; 
					}
				}
				if ($parent_id != ' ') {

					foreach ($catArr as $categoryId => $categoryData) {
						if ($categoryId == $parent_id) {
							$p_category_name = $categoryData['category_name'];
							break; 
						}
					}
					
				} 

				if ($p_category_name != ' ') {

					echo"herrrrrr isss        ";

					echo "Parent id  ".$parent_id;

					echo"<br></br>";
					echo"                   ";

					echo "Parent Category name ". $p_category_name;

					$tags[]=$p_category_name;
					
				} else {
					echo "Category not found";
				}


				echo"here is tags ";
                print_r($tags);
				
				//print_r($catArr);
				
				//exit(0);
				
			}

			// TODO: find child_category_name in catArr with child_category_nameand find parent_id
			//find pparent_id in catArr and get category_name and add in tag

			// 




				//}		

			

				// echo ".>>>>>soosmayyyrjhkjhkjhgkjh";
				// print_r($tags);
						
						
		
			
		}

			

			
		



		}
		



		



		if($user_id == 44893 || $user_id == 48551){
			//die();

			$p_category_name ="";
			$product_id = $productRow['product_id'];

			echo"select category_name from storecategories where shopifycategoryid = '' and user_id = '" . $user_id."'  and category_id in (select storecategoryid from products where user_id= '".$user_id."'  and  product_id = '".$product_id."')";

			$result = $conn->query("select category_name,category_id from storecategories where shopifycategoryid = '' and user_id = '" . $user_id."'  and category_id in (select storecategoryid from products where user_id= '".$user_id."'  and  product_id = '".$product_id."') ");
			if($result->num_rows > 0){


				// while (
					$row = $result->fetch_assoc();
					//) {
					$child_category_name =$row['category_name'];
					$child_category_id  =$row['category_id'];
                    
					echo"hkjhhghkhkjhkjh".$child_category_id;

					$tags[]=$child_category_name;

				
                   
					$newcategory = $conn->query("select * from storecategories where user_id = ".$user_id."'");

					//echo "select * from storecategories where user_id = 44893";
			       
		     	if($newcategory->num_rows > 0){

				
				     while($row = $newcategory->fetch_assoc()){
					
					$category_id = $row['category_id'];

					
					$category_name = $row['category_name'];
					$parent_id = $row['parent_id'];
					$catArr[$category_id] = array("category_name" => $category_name, "parent_id" => $parent_id);
				
				}

				foreach ($catArr as $categoryId => $categoryData) {
					if ($categoryData['category_name'] == $child_category_name && $categoryId == $child_category_id) {
						$parent_id = $categoryData['parent_id'];
						break; 
					}
				}
				if ($parent_id != ' ') {

					foreach ($catArr as $categoryId => $categoryData) {
						if ($categoryId == $parent_id) {
							$p_category_name = $categoryData['category_name'];
							break; 
						}
					}
					
				} 

				if ($p_category_name != ' ') {

					echo"herrrrrr isss        ";

					echo "Parent id  ".$parent_id;

					echo"<br></br>";
					echo"                   ";

					echo "Parent Category name ". $p_category_name;

					$tags[]=$p_category_name;
					
				} else {
					echo "Category not found";
				}


				echo"here is tags ";
                print_r($tags);
				
				//print_r($catArr);
				
				//exit(0);
				
			}

	
			
		}

			

		}

		if($user_id == 46190){
			//die();

			$p_category_name ="";
			$product_id = $productRow['product_id'];

			echo"select category_name from storecategories where shopifycategoryid = '' and user_id = '" . $user_id."'  and category_id in (select storecategoryid from products where user_id= '".$user_id."'  and  product_id = '".$product_id."')";

			$result = $conn->query("select category_name,category_id from storecategories where shopifycategoryid = '' and user_id = '" . $user_id."'  and category_id in (select storecategoryid from products where user_id= '".$user_id."'  and  product_id = '".$product_id."') ");
			if($result->num_rows > 0){


				// while (
					$row = $result->fetch_assoc();
					//) {
					$child_category_name =$row['category_name'];
					$child_category_id  =$row['category_id'];
                    
					echo"hkjhhghkhkjhkjh".$child_category_id;

					$tags[]=$child_category_name;

				
                   
					$newcategory = $conn->query("select * from storecategories where user_id = 46190");

					//echo "select * from storecategories where user_id = 46190";
			       
		     	if($newcategory->num_rows > 0){

				
				     while($row = $newcategory->fetch_assoc()){
					
					$category_id = $row['category_id'];

					
					$category_name = $row['category_name'];
					$parent_id = $row['parent_id'];
					$catArr[$category_id] = array("category_name" => $category_name, "parent_id" => $parent_id);
				
				}

				foreach ($catArr as $categoryId => $categoryData) {
					if ($categoryData['category_name'] == $child_category_name && $categoryId == $child_category_id) {
						$parent_id = $categoryData['parent_id'];
						break; 
					}
				}
				if ($parent_id != ' ') {

					foreach ($catArr as $categoryId => $categoryData) {
						if ($categoryId == $parent_id) {
							$p_category_name = $categoryData['category_name'];
							break; 
						}
					}
					
				} 

				if ($p_category_name != ' ') {

					echo"herrrrrr isss        ";

					echo "Parent id  ".$parent_id;

					echo"<br></br>";
					echo"                   ";

					echo "Parent Category name ". $p_category_name;

					$tags[]=$p_category_name;
					
				} else {
					echo "Category not found";
				}


				echo"here is tags ";
                print_r($tags);
				
				//print_r($catArr);
				
				//exit(0);
				
			}

		

			// 




				//}		

			

				// echo ".>>>>>soosmayyyrjhkjhkjhgkjh";
				// print_r($tags);
						
						
		
			
		}

			

			
		



		}

		

		if($user_id == 43398){
			$tags = explode(":", $product_type);
		    $product_type = end($tags);
		}

		if($user_id == 45000){
			$productid = $sku;
		}
		if($user_id == 44841 ){
			$productid ="";
			$taxable = false;
		}
	    if($user_id == 46127){

			$description ="";
		}

		if($user_id == 46021){
			$tags = array();
			if (strpos($title, 'Mens') !== false ) {
				$tags[] = "gender_mens";
			}
			if(strpos($title, 'Womens') !== false){
            
				$tags[] = "gender_womens";
			}
		}
		// if($user_id == 46021){
		// 	$tags[] =array();
		// }
		if($user_id == 46668 ){
                
			$sku = $ebayitemid;
	   }
	   if($user_id == 46910){

		$tags[] = $sku;
	   }

	   if($user_id == 47309){
		$description ="";
		$item_specific = $productRow['item_specific'];
		$condition_val = $productRow['condition_val'];
		$description = setdescriptiontemplate47309($item_specific,$title,$condition_val);
	  }

	  if($user_id == 47855){ 
		if(isset($itemSpecificObj['Brand'])){
			$brand = $itemSpecificObj['Brand'];
		}
		if(isset($itemSpecificObj['Golf Club Type'])){
			$product_type = $itemSpecificObj['Golf Club Type'];
		}
		if(isset($itemSpecificObj['Flex'])){
			$tags[] = $itemSpecificObj['Flex'];
		}
		
		
		
	   }

	 

		$data = array(
					"product"=>array(
						"title"=> $title,
						"body_html"=> $description,
						"vendor"=> $brand,
						"product_type"=> $product_type,
						"status"=> strtoupper($pubstatus),
						"tags"=> $tags,
						"published_scope" => "global",
						"images"=>$imageArr,
						"variants"=>array(
							array(
								"sku"=>$sku,
								"position"=>1,
								"price"=>number_format($price, 2, '.', ''),
								"inventory_policy"=>"deny",
								"fulfillment_service"=> $fulfilment_service,
								"inventory_management"=> $inventory_policy,
								"taxable"=>$taxable,
								/*"inventory_quantity"=>$quantity,*/
								"weight" => $weight,
								"weight_unit" => $weight_unit,
								"barcode" => $productid,
								"requires_shipping"=> true,
                                "quantity" => $quantity
							)
						)
					)
				);	
		
		if (count($imageArr) === 1) {
					$data["product"]["variants"][0]["imgurl"] = $imageArr[0]['src'];
		}
		if($user_id == 46926){
			
			$metafieldsvv =array();
			$conditionarray = array();
			$conditionarray[0] =  "Pre-owned";
			$metafieldsvv[] = array("key" => "condition_", "value" => json_encode($conditionarray), "type" => "list.single_line_text_field", "namespace" => "custom");
			$data["product"]["metafields"] = $metafieldsvv;
		}
		
		if($user_id == 47617){
			$price =$variantRow['orig_price'];
				if($price > 0){
				  $price = $price;
				}
				else{
					$price = $variantRow['price'];
					
				}
			$metafieldsvv =array();
			$metafieldsvv[] = array("key" => "ebay_item_price", "value" =>  $price, "type" => "single_line_text_field", "namespace" => "custom");
			$data["product"]["variants"][0]["metafields"] = $metafieldsvv;
		}
		if ($user_id == 44495){
			$product_type = $productRow['product_type'];
			if(strpos($product_type, 'Clothes') === false) {
				$data["product"]["variants"][0]["requires_shipping"] = false;
			}
          }
    

		if($user_id == 46454){
			if($brand == 'Oliver People' || $brand == 'Saint Laurent'){
			   $comparePrice = $price*0.35;
			   $singleVariant["compare_at_price"] = number_format($comparePrice, 2, '.', '');
			}
			else if($brand == 'PUMA'){
				$comparePrice = $price*0.60;
				$singleVariant["compare_at_price"] = number_format($comparePrice, 2, '.', '');
			}
		}
		
		if( $user_id == 43398 ){
		    if($orig_price > $price){
		        $data["product"]["variants"][0]["compare_at_price"] = number_format($orig_price, 2, '.', '');
		    }
		}
		
		if($user_id == 46021){
		   
            $metafields = array();
		   
    		$item_specific = $productRow['item_specific'];
            print_r("here are the metafileds ".$item_specific);
            // exit(0);
    		$itemSpecificJson = json_decode($item_specific, true);
    		foreach($itemSpecificJson as $k => $v){
              print_r($k);
    		   
    		    if($k == "Dress Length"){
    		           $key = "dress_length";
                       $value_type = "single_line_text_field";
                       $metafields[] = array("key" => $key, "value" => $v, "type" => $value_type, "namespace" => "custom");   
    		    }
				if($k == "Theme"){
    		           $key = "theme";
                       $value_type = "single_line_text_field";
                       $metafields[] = array("key" => $key, "value" => $v, "type" => $value_type, "namespace" => "custom");   
    		    }
				if($k == "Fit"){
    		           $key = "fit";
                       $value_type = "single_line_text_field";
                       $metafields[] = array("key" => $key, "value" => $v, "type" => $value_type, "namespace" => "custom");   
    		    }
				if($k == "Rise"){
    		           $key = "rise";
                       $value_type = "single_line_text_field";
                       $metafields[] = array("key" => $key, "value" => $v, "type" => $value_type, "namespace" => "custom");   
    		    }
				if($k == "Waist Size"){
    		           $key = "waist_size";
                       $value_type = "single_line_text_field";
                       $metafields[] = array("key" => $key, "value" => $v, "type" => $value_type, "namespace" => "custom");   
    		    }
				if($k == "Colour"){
    		           $key = "colour";
                       $value_type = "single_line_text_field";
                       $metafields[] = array("key" => $key, "value" => $v, "type" => $value_type, "namespace" => "custom");   
    		    }
				if($k == "Brand"){
    		           $key = "brand_";
                       $value_type = "single_line_text_field";
                       $metafields[] = array("key" => $key, "value" => $v, "type" => $value_type, "namespace" => "sc_attributes");   
    		    }
				if($k == "Material"){
    		           $key = "material_";
                       $value_type = "single_line_text_field";
                       $metafields[] = array("key" => $key, "value" => $v, "type" => $value_type, "namespace" => "sc_attributes");   
    		    }
				if($k == "Closure"){
    		           $key = "closure_";
                       $value_type = "single_line_text_field";
                       $metafields[] = array("key" => $key, "value" => $v, "type" => $value_type, "namespace" => "sc_attributes");   
    		    }
				if($k == "Condition"){
    		           $key = "condition_";
                       $value_type = "single_line_text_field";
                       $metafields[] = array("key" =>$key, "value" => $condition_val, "type" => $value_type, "namespace" => "sc_attributes");   
    		    }
				if($k == "Department"){
    		           $key = "department_";
                       $value_type = "single_line_text_field";
                       $metafields[] = array("key" => $key, "value" => $v, "type" => $value_type, "namespace" => "sc_attributes");   
    		    }
				if($k == "Style"){
    		           $key = "style";
                       $value_type = "single_line_text_field";
                       $metafields[] = array("key" => $key, "value" => $v, "type" => $value_type, "namespace" => "custom");   
    		    }
				if($k == "Type"){
    		           $key = "type_";
                       $value_type = "single_line_text_field";
                       $metafields[] = array("key" => $key, "value" => $v, "type" => $value_type, "namespace" => "sc_attributes");   
    		    }
				if($k == "Size Type"){
    		           $key = "size_type_";
                       $value_type = "single_line_text_field";
                       $metafields[] = array("key" => $key, "value" => $v, "type" => $value_type, "namespace" => "sc_attributes");   
    		    }
				if($k == "Size"){
    		           $key = "size_";
                       $value_type = "single_line_text_field";
                       $metafields[] = array("key" => $key, "value" => $v, "type" => $value_type, "namespace" => "sc_attributes");   
    		    }
				if($k == "Sleeve Length"){
    		           $key = "sleeve_length_";
                       $value_type = "single_line_text_field";
                       $metafields[] = array("key" => $key, "value" => $v, "type" => $value_type, "namespace" => "sc_attributes");   
    		    }
				if($k == "Fabric Type"){
    		           $key = "fabric_type";
                       $value_type = "single_line_text_field";
                       $metafields[] = array("key" => $key, "value" => $v, "type" => $value_type, "namespace" => "custom");   
    		    }
				if($k == "Pattern"){
    		           $key = "pattern_";
                       $value_type = "single_line_text_field";
                       $metafields[] = array("key" => $key, "value" => $v, "type" => $value_type, "namespace" => "sc_attributes");   
    		    }
				if($k == "Neckline"){
    		           $key = "neckline_";
                       $value_type = "single_line_text_field";
                       $metafields[] = array("key" => $key, "value" => $v, "type" => $value_type, "namespace" => "sc_attributes");   
    		    } 
				if($k == "Graphic Print"){
    		           $key = "graphic_print_";
                       $value_type = "single_line_text_field";
                       $metafields[] = array("key" => $key, "value" => $v, "type" => $value_type, "namespace" => "sc_attributes");   
    		    }
				if($k == "Front Type"){
    		           $key = "front_type";
                       $value_type = "single_line_text_field";
                       $metafields[] = array("key" => $key, "value" => $v, "type" => $value_type, "namespace" => "custom");   
    		    }
				if($k == "Chest size"){
    		           $key = "chest_size_";
                       $value_type = "single_line_text_field";
                       $metafields[] = array("key" => $key, "value" => $v, "type" => $value_type, "namespace" => "custom");   
    		    }
				if($k == "Pocket Type"){
    		           $key = "pocket_type";
                       $value_type = "single_line_text_field";
                       $metafields[] = array("key" => $key, "value" => $v, "type" => $value_type, "namespace" => "custom");   
    		    }
				if($k == "Strap Type"){
    		           $key = "strap_type";
                       $value_type = "single_line_text_field";
                       $metafields[] = array("key" => $key, "value" => $v, "type" => $value_type, "namespace" => "custom");   
    		    }
				if($k == "Swim Bottom Style"){
    		           $key = "swim_bottom_style";
                       $value_type = "single_line_text_field";
                       $metafields[] = array("key" => $key, "value" => $v, "type" => $value_type, "namespace" => "custom");   
    		    }
				if($k == "Garment Care"){
    		           $key = "garment_care";
                       $value_type = "single_line_text_field";
                       $metafields[] = array("key" => $key, "value" => $v, "type" => $value_type, "namespace" => "custom");   
    		    }
				if($k == "Accent"){
    		           $key = "accent";
                       $value_type = "single_line_text_field";
                       $metafields[] = array("key" => $key, "value" => $v, "type" => $value_type, "namespace" => "custom");   
    		    }
				if($k == "Character"){
    		           $key = "character";
                       $value_type = "single_line_text_field";
                       $metafields[] = array("key" => $key, "value" => $v, "type" => $value_type, "namespace" => "custom");   
    		    }
				if($k == "Season"){
    		           $key = "season";
                       $value_type = "single_line_text_field";
                       $metafields[] = array("key" => $key, "value" => $v, "type" => $value_type, "namespace" => "custom");   
    		    }
				if($k == "Skirt Length"){
    		           $key = "skirt_length";
                       $value_type = "single_line_text_field";
                       $metafields[] = array("key" => $key, "value" => $v, "type" => $value_type, "namespace" => "custom");   
    		    }
				if($k == "Handle/Strap Colour"){
    		           $key = "handle_strap_colour";
                       $value_type = "single_line_text_field";
                       $metafields[] = array("key" => $key, "value" => $v, "type" => $value_type, "namespace" => "custom");   
    		    }
				if($k == "Handle Style"){
    		           $key = "handle_style";
                       $value_type = "single_line_text_field";
                       $metafields[] = array("key" => $key, "value" => $v, "type" => $value_type, "namespace" => "custom");   
    		    }
				if($k == "Exterior Material"){
    		           $key = "exterior_material";
                       $value_type = "single_line_text_field";
                       $metafields[] = array("key" => $key, "value" => $v, "type" => $value_type, "namespace" => "custom");   
    		    }
				if($k == "Exterior Colour"){
    		           $key = "exterior_colour";
                       $value_type = "single_line_text_field";
                       $metafields[] = array("key" => $key, "value" => $v, "type" => $value_type, "namespace" => "custom");   
    		    }
                if($k == "Bag Width"){
    		           $key = "bag_width";
                       $value_type = "single_line_text_field";
                       $metafields[] = array("key" => $key, "value" => $v, "type" => $value_type, "namespace" => "custom");   
    		    }
				if($k == "Bag Heigh"){
    		           $key = "bag_heigh";
                       $value_type = "single_line_text_field";
                       $metafields[] = array("key" => $key, "value" => $v, "type" => $value_type, "namespace" => "custom");   
    		    }
				if($k == "Upper Material"){
    		           $key = "upper_material";
                       $value_type = "single_line_text_field";
                       $metafields[] = array("key" => $key, "value" => $v, "type" => $value_type, "namespace" => "custom");   
    		    }
				if($k == "UK Shoe Size"){
    		           $key = "uk_shoe_size";
                       $value_type = "single_line_text_field";
                       $metafields[] = array("key" => $key, "value" => $v, "type" => $value_type, "namespace" => "custom");   
    		    }
				if($k == "Toe Shape"){
    		           $key = "toe_shape";
                       $value_type = "single_line_text_field";
                       $metafields[] = array("key" => $key, "value" => $v, "type" => $value_type, "namespace" => "custom");   
    		    }
				if($k == "Outsole Material"){
    		           $key = "outsole_material";
                       $value_type = "single_line_text_field";
                       $metafields[] = array("key" => $key, "value" => $v, "type" => $value_type, "namespace" => "custom");   
    		    }
				if($k == "Outer Shell Material"){
    		           $key = "outer_shell_material";
                       $value_type = "single_line_text_field";
                       $metafields[] = array("key" => $key, "value" => $v, "type" => $value_type, "namespace" => "custom");   
    		    }
				if($k == "Lining Material"){
    		           $key = "lining_material";
                       $value_type = "single_line_text_field";
                       $metafields[] = array("key" => $key, "value" => $v, "type" => $value_type, "namespace" => "custom");   
    		    }
				if($k == "Jacket/Coat Length"){
    		           $key = "jacket_coat_length";
                       $value_type = "single_line_text_field";
                       $metafields[] = array("key" => $key, "value" => $v, "type" => $value_type, "namespace" => "custom");   
    		    }
				if($k == "Leg Style"){
    		           $key = "leg_style";
                       $value_type = "single_line_text_field";
                       $metafields[] = array("key" => $key, "value" => $v, "type" => $value_type, "namespace" => "custom");   
    		    }
				if($k == "Inside Leg"){
    		           $key = "inside_leg";
                       $value_type = "single_line_text_field";
                       $metafields[] = array("key" => $key, "value" => $v, "type" => $value_type, "namespace" => "custom");   
    		    }
				if($k == "Distressed"){
    		           $key = "distressed";
                       $value_type = "single_line_text_field";
                       $metafields[] = array("key" => $key, "value" => $v, "type" => $value_type, "namespace" => "custom");   
    		    }
				if($k == "Other Material"){
    		           $key = "other_material";
                       $value_type = "single_line_text_field";
                       $metafields[] = array("key" => $key, "value" => $v, "type" => $value_type, "namespace" => "custom");   
    		    }
				if($k == "Collar To Hem"){
    		           $key = "collar_to_hem_";
                       $value_type = "single_line_text_field";
                       $metafields[] = array("key" => $key, "value" => $v, "type" => $value_type, "namespace" => "custom");   
    		    }
				
			} 
    		$data["product"]["metafields"] = $metafields;
            print_r($data["product"]);
		}
		
	
		if($user_id == 46026){
			$metafields = array();
		    $condition_note = $productRow['condition_note'];
		    $condition_val = $productRow['condition_val'];
		    if($condition_val != ''){
		        $metafields[] = array("key" => "condition", "value" => $condition_val, "type" => "single_line_text_field", "namespace" => "my_fields"); 
		    }
		    if($condition_note != ''){
		        $metafields[] = array("key" => "condition_description", "value" => $condition_note, "type" => "single_line_text_field", "namespace" => "my_fields");    
		    }
			$data["product"]["metafields"] = $metafields;


		}



		if($user_id == 44664 || $user_id == 43040 ){
            $metafields = array(); 	
              
			$result = $conn->query("select * from product_dimensions where  user_id = ".$user_id." and ebayitemid = ". $productRow['ebayitemid']." limit 1"  );
			
			   if($result->num_rows > 0){
				while($row = $result->fetch_assoc()){
                   echo  $productRow['ebayitemid'];
				   echo  $shopifyproductid;

					$len = $row['length'];
                    $width = $row['width'];
                    $depth = $row['depth'];

					echo "length $len";
					echo "width $width";
					echo "depth $depth";

					$metafields[] = array("key" => "length", "value" => $len, "type" => "single_line_text_field", "namespace" => "custom");
					$metafields[] = array("key" => "width", "value" => $width, "type" => "single_line_text_field", "namespace" => "custom");
					if($user_id == 43040){
						$metafields[] = array("key" => "depth", "value" =>$depth, "type" => "single_line_text_field", "namespace" => "custom");
					}
					else{
					$metafields[] = array("key" => "height", "value" =>$depth, "type" => "single_line_text_field", "namespace" => "custom");
					}
					echo "set meta field ";

				}
				$data["product"]["metafields"] = $metafields;
			}
		}  

		if($user_id == 47379 ){

			$metafields = array(); 	
              
			$result = $conn->query("select * from product_dimensions where  user_id = ".$user_id." and ebayitemid = ". $productRow['ebayitemid']." limit 1"  );
			
			   if($result->num_rows > 0){
				while($row = $result->fetch_assoc()){
                   echo  $productRow['ebayitemid'];
				   echo  $shopifyproductid;

					$len = $row['length'];
                    $width = $row['width'];
                    $depth = $row['depth'];

					echo "length $len";
					echo "width $width";
					echo "depth $depth";

					$metafields[] = array("key" => "length", "value" => $len, "type" => "single_line_text_field", "namespace" => "custom");
					$metafields[] = array("key" => "width", "value" => $width, "type" => "single_line_text_field", "namespace" => "custom");
					$metafields[] = array("key" => "height", "value" =>$depth, "type" => "single_line_text_field", "namespace" => "custom");
					
					echo "set meta field ";

				}

				$metafields[] = array("key" => "Weight", "value" =>$weight, "type" => "single_line_text_field", "namespace" => "custom");
				$data["product"]["metafields"] = $metafields;
			}

		}	






		if($user_id == 44401 ){

			$metafields[] = array("key" => "listing_condition", "value" =>$condition_val, "type" => "single_line_text_field", "namespace" => "custom");
			$data["product"]["metafields"] = $metafields;	
		}
		
		if(($user_id == 41578 || $user_id == 43633 || $user_id == 42894 || $user_id == 44890   ) && $inventory_policy == "NO"){
		    $data['product']['variants'][0]['inventory_management'] = "";
		}
		if($user_id == 45000){
			$productid = $sku;
		}
		if($user_id == 48043){
			$data['product']['variants'][0]["inventory_policy"] = "continue";
			
		}
		

		if($user_id == 44719 || $user_id == 45165 ){
			$data['product']['variants'][0]['barcode'] = $ebayitemid;
           
		}
   
		if( $user_id == 46021){
			if(is_array($itemSpecificObj)){
				foreach($itemSpecificObj as $k => $v){
					if($k == "EAN"){
						$data['product']['variants'][0]['barcode']  = $v;   
						break;
					}
				}
			}
		}

		if($user_id == 48102){
		   
			$metafields = array();
			$metafields[] = array("key" => "snowboard_length", "value" => "{\"value\":1.0,\"unit\":\"mm\"}", "type" => "dimension", "namespace" => "test_data");
	    	$data["product"]["metafields"] = $metafields;    
		}
		
       


		if($user_id == 47617){//
		
            $metafields = array();
    		$item_specific = $productRow['item_specific'];
            $ebayitemid =  $productRow['ebayitemid'];
			$searchstring12=$productRow['searchstring'];
			echo $searchstring12 ."\n";
            $category =    $productRow['product_type'];
            $condition_val = $productRow['condition_val'];
			$condition_note = $productRow['condition_note'];
            $title = $productRow['title'];
    	      $itemSpecificJson = json_decode($item_specific, true);
         
    	      if(isset($itemSpecificJson['Non-Domestic Product']) ){
    		    $NDP = $itemSpecificJson['Non-Domestic Product'];
    		    $metafields[] = array("key" => "ebay_non_domestic_product", "value" => $NDP, "type" => "single_line_text_field", "namespace" => "custom");
    		 }
           

             if(isset($itemSpecificJson['Brand']) ){
    		    $brand = $itemSpecificJson['Brand'];
    		    $metafields[] = array("key" => "ebay_brand", "value" => $brand, "type" => "single_line_text_field", "namespace" => "custom");
    		 }

			 
             if(isset($itemSpecificJson['Type']) ){
    		    $type = $itemSpecificJson['Type'];
    		    $metafields[] = array("key" => "ebay_type", "value" => $type, "type" => "single_line_text_field", "namespace" => "custom");
    		 }

			 if(isset($itemSpecificJson['Custom Bundle']) ){
    		    $Custom_Bundle = $itemSpecificJson['Custom Bundle'];
    		    $metafields[] = array("key" => "ebay_custom_bundle", "value" => $Custom_Bundle, "type" => "single_line_text_field", "namespace" => "custom");
    		 }
			 if(isset($itemSpecificJson['Modified Item']) ){
    		    $modified_item = $itemSpecificJson['Modified Item'];
    		    $metafields[] = array("key" => "ebay_modified_item", "value" => $modified_item, "type" => "single_line_text_field", "namespace" => "custom");
    		 }
			 if(isset($itemSpecificJson['Battery Size']) ){
    		    $battery_size = $itemSpecificJson['Battery Size'];
    		    $metafields[] = array("key" => "ebay_battery_size", "value" => $battery_size, "type" => "single_line_text_field", "namespace" => "custom");
    		 }
			 if(isset($itemSpecificJson['MPN']) ){
    		    $mpn = $itemSpecificJson['MPN'];
    		    $metafields[] = array("key" => "ebay_mpn", "value" => $mpn, "type" => "single_line_text_field", "namespace" => "custom");
    		 }
			 if(isset($itemSpecificJson['Voltage']) ){
    		    $voltage = $itemSpecificJson['Voltage'];
    		    $metafields[] = array("key" => "ebay_voltage", "value" => $voltage, "type" => "single_line_text_field", "namespace" => "custom");
    		 }
			 if(isset($itemSpecificJson['EAN']) ){
    		    $ean = $itemSpecificJson['EAN'];
    		    $metafields[] = array("key" => "ebay_ean", "value" => $ean, "type" => "single_line_text_field", "namespace" => "custom");
    		 }

			 if(isset($itemSpecificJson['Chemical Composition']) ){
    		    $chemical_composition = $itemSpecificJson['Chemical Composition'];
    		    $metafields[] = array("key" => "ebay_chemical_composition", "value" => $chemical_composition, "type" => "single_line_text_field", "namespace" => "custom");
    		 }
			 if(isset($itemSpecificJson['Expiry Date']) ){
    		    $expiry_date = $itemSpecificJson['Expiry Date'];
    		    $metafields[] = array("key" => "ebay_expiry_date", "value" => $expiry_date, "type" => "single_line_text_field", "namespace" => "custom");
    		 }

			 if(isset($itemSpecificJson['Coin\/Button Cell Sub-Type']) ){
    		    $coin_button_cell_sub_type = $itemSpecificJson['Coin\/Button Cell Sub-Type'];
    		    $metafields[] = array("key" => "ebay_coin_button_cell_sub_type", "value" => $coin_button_cell_sub_type, "type" => "single_line_text_field", "namespace" => "custom");
    		 }

			 if(isset($searchstring12)){
				
    		    $metafields[] = array("key" => "ebay_shipping_profile_name", "value" => $searchstring12, "type" => "single_line_text_field", "namespace" => "custom");

			 }

			 if(isset($ebayitemid)){
				$ebay_link='https://www.ebay.co.uk/itm/'.$ebayitemid;
    		    $metafields[] = array("key" => "ebay_original_ebay_link", "value" => $ebay_link, "type" => "single_line_text_field", "namespace" => "custom");

			 }
			 if(isset($condition_note)){
				
    		    $metafields[] = array("key" => "ebay_condition", "value" =>  $condition_note, "type" => "single_line_text_field", "namespace" => "custom");

			 }
			 if(isset($condition_val)){
				
    		    $metafields[] = array("key" => "ebay_condition", "value" =>$condition_val, "type" => "single_line_text_field", "namespace" => "custom");

			 }
			 if(isset($itemSpecificJson["Ideale Per:"]) ){
    		    $Ideale = $itemSpecificJson["Ideale Per:"];
    		    $metafields[] = array("key" => "ebay_ideale_per", "value" => $Ideale, "type" => "single_line_text_field", "namespace" => "custom");
    		 }
			 if(isset($itemSpecificJson["Carte Opzione"]) ){
    		    $Carte = $itemSpecificJson["Carte Opzione"];
    		    $metafields[] = array("key" => "ebay_carte_opzione", "value" => $Carte , "type" => "single_line_text_field", "namespace" => "custom");
    		 }
			 if(isset($itemSpecificJson["Marca"]) ){
    		    $Marca = $itemSpecificJson["Marca"];
    		    $metafields[] = array("key" => "ebay_marca", "value" => $Marca , "type" => "single_line_text_field", "namespace" => "custom");
    		 }
			 if(isset($itemSpecificJson["Tipo."]) ){
    		    $Tipo = $itemSpecificJson["Tipo."];
    		    $metafields[] = array("key" => "ebay_tipo", "value" => $Tipo , "type" => "single_line_text_field", "namespace" => "custom");
    		 }
			 if(isset($itemSpecificJson["Paese\/Regione di Produzione"]) ){
    		    $Paese = $itemSpecificJson["Paese\/Regione di Produzione"];
    		    $metafields[] = array("key" => "ebay_paese_regione_di_produzione", "value" => $Paese , "type" => "single_line_text_field", "namespace" => "custom");
    		 }
			 if(isset($itemSpecificJson["Number of Piece"]) ){
    		    $Piece11 = $itemSpecificJson["Number of Piece"];
    		    $metafields[] = array("key" => "ebay_number_of_piecese", "value" => $Piece11 , "type" => "single_line_text_field", "namespace" => "custom");
    		 }
			 if(isset($itemSpecificJson["Material"]) ){
    		    $Material = $itemSpecificJson["Material"];
    		    $metafields[] = array("key" => "ebay_material", "value" => $Material  , "type" => "single_line_text_field", "namespace" => "custom");
    		 }
			 if(isset($itemSpecificJson["Age Level"]) ){
    		    $Level = $itemSpecificJson["Age Level"];
    		    $metafields[] = array("key" => "ebay_age_level", "value" => $Level  , "type" => "single_line_text_field", "namespace" => "custom");
    		 }
			 if(isset($itemSpecificJson["Personalisation Instructions"]) ){
    		    $Instructions = $itemSpecificJson["Personalisation Instructions"];
    		    $metafields[] = array("key" => "ebay_personalise", "value" => $Instructions   , "type" => "single_line_text_field", "namespace" => "custom");
    		 }
			 
			 if(isset($itemSpecificJson["Colour"]) ){
    		    $Colour = $itemSpecificJson["Colour"];
    		    $metafields[] = array("key" => "ebay_colour", "value" =>$Colour  , "type" => "single_line_text_field", "namespace" => "custom");
    		 }
			 if(isset($itemSpecificJson["Award"]) ){
    		    $Award = $itemSpecificJson["Award"];
    		    $metafields[] = array("key" => "ebay_award", "value" =>$Award  , "type" => "single_line_text_field", "namespace" => "custom");
    		 }
			
			 if(isset($itemSpecificJson["Theme"]) ){
    		    $Theme = $itemSpecificJson["Theme"];
    		    $metafields[] = array("key" => "ebay_theme", "value" =>$Theme  , "type" => "single_line_text_field", "namespace" => "custom");
    		 }
           
			 if(isset($itemSpecificJson["Features"]) ){
    		    $Features = $itemSpecificJson["Features"];
    		    $metafields[] = array("key" => "ebay_features", "value" =>$Features  , "type" => "single_line_text_field", "namespace" => "custom");
    		 }
			 if(isset($itemSpecificJson["Recommended Age Range"]) ){
    		    $Range = $itemSpecificJson["Recommended Age Range"];
    		    $metafields[] = array("key" => "ebay_recommended_age_range", "value" =>$Range  , "type" => "single_line_text_field", "namespace" => "custom");
    		 }
			 if(isset($itemSpecificJson["Country\/Region of Manufacture"]) ){
    		    $Country = $itemSpecificJson["Country\/Region of Manufacture"];
    		    $metafields[] = array("key" => "ebay_country_region_of_manufacture", "value" =>$Country  , "type" => "single_line_text_field", "namespace" => "custom");
    		 }
			 if(isset($itemSpecificJson["Character Family"]) ){
    		    $Family = $itemSpecificJson["Character Family"];
    		    $metafields[] = array("key" => "ebay_character_family", "value" =>$Family  , "type" => "single_line_text_field", "namespace" => "custom");
    		 }
			 if(isset($itemSpecificJson["Personalise"]) ){
    		    $Personalise = $itemSpecificJson["Personalise"];
    		    $metafields[] = array("key" => "ebay_personalise", "value" =>$Personalise  , "type" => "single_line_text_field", "namespace" => "custom");
    		 }
   
                  
    		$data["product"]["metafields"] = $metafields;
            print_r($data);
            //exit(0);
		}


        $imageArr_new = array();
		foreach($data["product"]['images'] as $v){
			$image_url = preg_replace('/\?.*/', '', $v['src']);
			$imageArr_new[] = array("alt"=>"$product_id","mediaContentType" => "IMAGE","originalSource" => trim($image_url));			
		}
 
        echo "---------------------------------";
        print_r($imageArr_new);
        echo "---------------------------------";
          
          $jsonstring = [
			"input" => [
			  "title" => $data["product"]['title'],
			  'descriptionHtml' =>$data["product"]['body_html'],
			   'vendor' => $data["product"]['vendor'],
			   "tags"=>$data["product"]['tags'],
			   "status" => strtoupper($data["product"]['status']),
			   'productType'=>$data["product"]['product_type'],
			   'published'=> true,   
            ],
			"media" => $imageArr_new
        ];
        if (isset($data["product"]['metafields'])) {
            $jsonstring["input"]['metafields'] =$data["product"]['metafields'];
        }
        print_r($jsonstring);
		addlog("variable addShopifyProduct_graphql: ","INFO");
		addlog(json_encode($jsonstring,true),"INFO");
            $response = addShopifyProduct_graphql($token, $shopurl, $jsonstring);
            echo "////*//////////////////////////////////////////";
			print_r($response );
            echo "////*//////////////////////////////////////////";

			$responseDecoded = $response ;
			// return false;
            addlog("check response addShopifyProduct: ","INFO");
            addlog("productcreate response ".json_encode($response,true),"INFO");
			//  print_r($response);
             print_r( $responseDecoded);
             echo "sdskfdjnsfj";
		

			 if (empty($responseDecoded['data']['productCreate']['userErrors'])) {
                echo 'in if for updated variant';
                print_r($responseDecoded['data']['productCreate']['product']['id']);
               $gid_shopifyproductid = $responseDecoded['data']['productCreate']['product']['id'];
                print_r($gid_shopifyproductid);
				//die();
                $shopifyproductid = str_replace("gid://shopify/Product/", "",$gid_shopifyproductid);
                print_r($gid_shopifyproductid);
                $shopifyvariantidG = $responseDecoded['data']['productCreate']['product']['variants']['nodes'][0]['id'];
                $shopifyvariantid = str_replace("gid://shopify/ProductVariant/", "", $shopifyvariantidG);
                $shopifyinventoryidG = $responseDecoded['data']['productCreate']['product']['variants']['nodes'][0]['inventoryItem']['id'];
                $shopifyinventoryid = str_replace("gid://shopify/InventoryItem/", "", $shopifyinventoryidG);
                $conn->query("update products set shopifyproductid = '".mysqli_real_escape_string($conn, $shopifyproductid)."', status = 'Imported', newflag = 0, updated_at = now() where user_id = '".$user_id."' and product_id = ".$product_id);
                $conn->query("update product_variants set shopifyproductid = '".mysqli_real_escape_string($conn, $shopifyproductid)."', shopifyvariantid = '".mysqli_real_escape_string($conn, $shopifyvariantid)."', shopifyinventoryid = '".mysqli_real_escape_string($conn, $shopifyinventoryid)."', shopifylocationid = '".mysqli_real_escape_string($conn, $location_id)."', status = 'Imported', newflag = 0, updated_at = now() where user_id = '".$user_id."' and id = ".$variant_id);
                $conn->query("insert into shopifyproducts(user_id, productid, variantid, sku, dateofmodification) values ('".$user_id."', '".mysqli_real_escape_string($conn, $shopifyproductid)."', '".mysqli_real_escape_string($conn, $shopifyvariantid)."', '".mysqli_real_escape_string($conn, $sku)."', now())");
                addlog("productid ".$shopifyproductid, "INFO");
                 $weight_unitnew = $data['product']['variants'][0]['weight_unit'];
                if(isset( $weight_unitnew) && $weight > 0){
                     $weight_unitnew = convertWeightUnitToFullForm( $weight_unitnew);
                }else{
                    $weight = 0;
                     $weight_unitnew = "POUNDS";	
                }
				$track =  true;
				if($data['product']['variants'][0]['inventory_policy'] == "continue"){
                  $track = false;
				}
				$variantVariables ='';
				if($data['product']['variants'][0]['imgurl'] !='' ){
					$image_url = preg_replace('/\?.*/', '', $data['product']['variants'][0]['imgurl']);
        $variantVariables = [
            "productId" => "gid://shopify/Product/$shopifyproductid",
			"media" =>[
				"alt" => "epi".$data['product']['variants'][0]['barcode'],
                        "mediaContentType" => "IMAGE",
                        "originalSource" => $image_url,
			],
            "variants" => [
                [
                    "id" => "gid://shopify/ProductVariant/$shopifyvariantid",
    
                    "barcode" => $data['product']['variants'][0]['barcode'],
                    "inventoryItem" => [
                        "measurement" => [
                            "weight" => [
                                "unit" =>  $weight_unitnew,
                                "value" => (float)$data['product']['variants'][0]['weight'],
                            ],
                        ],
                        "requiresShipping" => true,
                        "sku" => $data['product']['variants'][0]['sku'],
                        "tracked" =>true,
                    ],
                    "inventoryPolicy" => strtoupper($data['product']['variants'][0]['inventory_policy']),
                    "price" => $data['product']['variants'][0]['price'],
                    "taxable" =>  $data['product']['variants'][0]['taxable'],
					"mediaSrc" =>$image_url,
                ],
            ],
        ];
	   }
	   else{
		$variantVariables = [
            "productId" => "gid://shopify/Product/$shopifyproductid",
			
            "variants" => [
                [
                    "id" => "gid://shopify/ProductVariant/$shopifyvariantid",
    
                    "barcode" => $data['product']['variants'][0]['barcode'],
                    "inventoryItem" => [
                        "measurement" => [
                            "weight" => [
                                "unit" =>  $weight_unitnew,
                                "value" => (float)$data['product']['variants'][0]['weight'],
                            ],
                        ],
                        "requiresShipping" => true,
                        "sku" => $data['product']['variants'][0]['sku'],
                        "tracked" => true,
                    ],
                    "inventoryPolicy" => strtoupper($data['product']['variants'][0]['inventory_policy']),
                    "price" => $data['product']['variants'][0]['price'],
                    "taxable" =>  $data['product']['variants'][0]['taxable'],
                ],
            ],
        ];
	   }
                addlog("processto uplocat v data".json_encode($variantVariables,true),"INFO");
			$res = updateShopifyVariant_graphql($token, $shopurl, $variantVariables);
			print_r($res);
			addlog("processto uplocat v data default response".json_encode($res,true),"INFO");
			if($res){
           

                addlog("updateShopifyVariant id true ","INFO");
				$variables = [
					"input" => [
						"ignoreCompareQuantity" => true,
						"name" => "available",
						"reason" => "correction",
						"quantities" => [
							[
								"inventoryItemId" =>  "gid://shopify/InventoryItem/$shopifyinventoryid",
								"locationId" => "gid://shopify/Location/$location_id",
								"quantity" => (int)$data['product']['variants'][0]['quantity']
							]
						]
					]
				];
                     echo "****************************";
                print_r(	$variables);
                echo "****************************";
				addlog("updateShopifyInventory_graphql ","INFO");
				addlog("updateinve data".json_encode($variables,true),"INFO");
				sleep(2);
				$USI = updateShopifyInventory_graphql($token, $shopurl,$variables);
				print_r($USI);
				if($USI){
					addlog("processto uplocat v data default response inventory".json_encode($USI,true),"INFO");
					addlog("product imported successfully :".$product_id,"INFO");
					echo "product imported successfully :".$product_id;
					$totalcount = $totalcount - 1;
					if($totalcount == 0 ){
						$conn->query("delete from processimports  where user_id =".$user_id);
					}
					else{
						
						$conn->query("update processimports set count ='".$totalcount."' where user_id =".$user_id."");
					}
					$totalcount--;
				
				}
              }
             }
			 
			 else {
                
				echo "please contact support to import this products ".$user_id;
			 }
         
		return true;
	}
    function convertWeightUnitToFullForm($abbreviation) {
		$units = [
			'kg' => 'KILOGRAMS',
			'lb' => 'POUNDS',
			'g' => 'GRAMS',
			'oz' => 'OUNCES'
		];
		
		$abbreviation = strtolower((string)$abbreviation);
		
		return $units[$abbreviation] ?? '';
	}
	function handleProductsWithVariants($user_id, $token, $shopurl, $existingSKUs, $includeoutofstock, $published, $fulfilment_service, $inventory_policy, $productRow, $location_id, $ebtoken, $siteid, $settingsRow,$totalcount){
		global $conn, $skipRegx, $categoryLogicArr;	
		$catArr = array();
		echo "total count".$totalcount;
		if(in_array($user_id, $categoryLogicArr)){
			$result = $conn->query("select * from storecategories where user_id = ".$user_id);
			addlog("import  Started and product found- ".$user_id, "INFO");
			if($result->num_rows > 0){
				while($row = $result->fetch_assoc()){
					$category_id = $row['category_id'];
					$category_name = $row['category_name'];
					$parent_id = $row['parent_id'];
					$catArr[$category_id] = array("category_name" => $category_name, "parent_id" => $parent_id);
				}
			}
		}

		$shippingCostArr = array();
    	
		$product_id = $productRow['product_id'];
		$ebayitemid = $productRow['ebayitemid'];
		$title = $productRow['title'];
		$condition_note = $productRow['condition_note'];
		$condition_val = $productRow['condition_val'];		
		$brand = $productRow['brand'];
		$category_idv = $productRow['storecategoryid'];
		$product_type = $productRow['product_type'];
		$item_specific = $productRow['item_specific'];
		$searchstring12 = $productRow['searchstring'];
		$itemSpecificObj = json_decode($item_specific, true);		
		$option1name = $productRow['option1name'];
		$option2name = $productRow['option2name'];
		$option3name = $productRow['option3name'];
		$option4name = $productRow['option4name'];
		$option5name = $productRow['option5name'];
		$tags = array();

		$option1name = str_replace("/", "-", $option1name);
		$option2name = str_replace("/", "-", $option2name);
		$option3name = str_replace("/", "-", $option3name);
		$option4name = str_replace("/", "-", $option4name);
		$option5name = str_replace("/", "-", $option5name);

		$isSizeType = false;
		

		if($user_id == 46190){

			$tags[] =$searchstring12;
		}
        if($user_id == 44719 || $user_id == 45165 ) {            
    		$tags[] = "eb_".$searchstring12;
			$tags[]=$ebayitemid;
        }

		if($user_id == 41867 ){
			$option4name = "";
			$option5name = "" ;

		}

		if($user_id == 45556 ){
			$option4name = "";
			$option5name = "" ;

		}

		if($user_id == 41867 ){
			$option4name = "";
			$option5name = "" ;

		}
		if($user_id == 43398 ){
			$option4name = "";
			$option5name = "" ;

		}


		$weight = 0;
		$weight_unit = "lb";
		if(strlen($option4name) > 0 || strlen($option5name) > 0){
		    $conn->query("update products set status = 'error', errdetails = 'More than 3 variant options.' where product_id = ".$product_id." and user_id = ".$user_id);
			echo "more than three variant options.";
			return false;
		}


			if($user_id == 45000   &&  $searchstring12 !='UK'){

			$conn->query("update products set block = 1 where user_id = 45000 and ebayitemid =".$ebayitemid );
			return false;
	
			}

		$description = "";
		if( $user_id != 18645){
            $description = getProdDescription($user_id, $product_id);
		    
		}
        
        $is_item_specific = false;
        $is_condition_note = false;
        $is_condition_val =  false;
		if($settingsRow["desc_cond "] == 1){
			$is_condition_note = true;
			$is_condition_val =  true;
		}
		if($settingsRow["desc_itemspecs"] == 1){
			$is_item_specific = true;			
		}

		print_r($is_item_specific);
		echo "  gggggggggggggggggggggggg          ";

		$descFilterResp = applyDescriptionFilter($description, $item_specific, $condition_note, $condition_val, $is_item_specific, $is_condition_note, $is_condition_val, $user_id);
		print_r($descFilterResp);
		if($descFilterResp["status"] == "fail"){
		    if(!$skipRegx){
		        return false;   
		    }
		} else {
		    $description = $descFilterResp["message"];
        }

		if(strlen($title) > 255){
			$str = wordwrap($title, 254);
			$str = explode("\n", $str);
			$title = $str[0];
		}
		if(strlen($description) == 0 ){
			$description = $title;
		}
        

		if($user_id == 46926){
		    $temp = explode(":", $product_type);
		    $product_type = trim(end($temp));
		}
        

		if($user_id == 44582){
			
			$temp = explode(":", $product_type);
		    $product_type = trim(end($temp));
				$tags[] =$product_type ;

				print_r($tags);
		}


		if($user_id == 47582){
			
			// $temp = explode(":", $product_type);
		    // $product_type = trim(end($temp));
			// 	$tags[] =$product_type ;

			// 	print_r($tags);


			$temp = explode(":", $product_type);

			$segment1 = $temp[0]; 
			$tags[] = $segment1;
			$segment2 = $temp[1]; 
			$tags[] = $segment2;
			$segment3 = $temp[2];
			$tags[] = $segment3; 
			$segment4 = $temp[3]; 
			$tags[] = $segment4; 
		}



		if($user_id == 44582 ){
		    $shippingcost = 0;

			$tags = array();

			echo "select * from shippingcost where user_id = ".$user_id." and product_id=".$product_id;
			$result = $conn->query("select * from shippingcost where user_id = ".$user_id." and product_id=".$product_id);
    	    if ($result->num_rows > 0) {
    		    while($shippingCost = $result->fetch_assoc()) {	
    		        $shippingcost = $shippingCost['cost'];    
    		        $tags[] ="Ship_".$shippingcost ;
    		    }
    	    }

			// 
			
			echo"     valuee tagess        "."\n";
			print_r($tags);

		}


		if($user_id == 44893){
			$temp = explode(":", $product_type);
			$product_type = end($temp);
            $tags[]= $product_type;
			echo $product_type."                        " ;
			
		}

		if($user_id == 44401){            
	        $tempArr = explode(":", $product_type);
			$product_type = trim($tempArr[0]);
			$tags[] =$product_type;
        }

		if(   $user_id == 42198 ){      

	      $searchstring = $productRow['searchstring'];        
	      $tags[] = $searchstring;
            echo "$user_id tags $tags";
           
        }
       
        if(  $user_id == 42198   ) {          
            $tags[] = $condition_val;
        }

        if($user_id == 43580) {
			$result = $conn->query("select * from storecategories where category_id ='".$category_idv."' and user_id = " . $user_id);
			if ($result->num_rows > 0) {
				while ($row = $result->fetch_assoc()) {
					$tags = $row['category_name'];
					// $shopifycategoryid = $row['shopifycategoryid'];
					// $catArr[$category_id] = $shopifycategoryid;
				}
			}
		}  

		if($user_id == 44401) {
			$result = $conn->query("select * from storecategories where category_id ='".$category_idv."' and user_id = " . $user_id);
			if ($result->num_rows > 0) {
				while ($row = $result->fetch_assoc()) {
					$tags[] = $row['category_name'];
					// $shopifycategoryid = $row['shopifycategoryid'];
					// $catArr[$category_id] = $shopifycategoryid;
				}
			}
		}  	        
		$shopifyproductid = $productRow['shopifyproductid'];
		
		if(in_array($user_id, $categoryLogicArr)){
			$storecategoryid = $productRow['storecategoryid'];
			$storecategoryid2 = $productRow['storecategoryid2'];
			if(array_key_exists($storecategoryid, $catArr)){
				$temp = $catArr[$storecategoryid];
				$category_name = $temp['category_name'];
				$parent_id = $temp['parent_id'];
				$tags[] = $category_name;
				$product_type = $category_name;
				if($parent_id != ""){
					if(array_key_exists($parent_id, $catArr)){
						$temp1 = $catArr[$parent_id];
						$category_name1 = $temp1['category_name'];
						$parent_id = $temp1['parent_id'];
						$tags[] = $category_name1;
						if($user_id != 19321 && $user_id != 19563 ){
						   $product_type = $category_name1;
						}
						if($parent_id != ""){
        					if(array_key_exists($parent_id, $catArr)){
        						$temp2 = $catArr[$parent_id];
        						$category_name2 = $temp2['category_name'];
        						$tags[] = $category_name2;
								if($user_id != 19321 && $user_id != 19563 ){
        						   $product_type = $category_name2; 
								}
        					}
        				}
					}
				}
			}
			if(array_key_exists($storecategoryid2, $catArr)){
				$temp = $catArr[$storecategoryid2];
				$category_name = $temp['category_name'];
				$parent_id = $temp['parent_id'];
				$tags[] = $category_name;
				if($parent_id != ""){
					if(array_key_exists($parent_id, $catArr)){
						$temp1 = $catArr[$parent_id];
						$category_name1 = $temp1['category_name'];
						$tags[] = $category_name1;
					}
				}
			}
			if($user_id == 15057){
			    if($storecategoryid == "27421515014"){ // LAMBSKIN LEATHER
			        $tags[] = "retail_lambskins";
			    } else if($storecategoryid == "27421516014"){ // GOATSKIN LEATHER
			        $tags[] = "retail_goatskins";
			    } else if($storecategoryid == "27421517014"){ // METALLIC LEATHER
			        $tags[] = "retail_metallics";
			    } else if($storecategoryid == "1095742014"){ // CALF/COW LEATHER
			        $tags[] = "retail_calf/cow";
			    } else {
			        $conn->query("update products set block = 1, status = 'Ready to Import' where user_id = 15057 and product_id = ".$productRow['product_id']);
			        return false;
			    }
			}
		}
		

		$variantResult = $conn->query("select * from product_variants where product_id = ".$product_id." and user_id = ".$user_id." and block = 0 and duplicate = 0 and shopifyvariantid = '' ");
		
		$variants = array();
		$options = array();
		$imageArr = array();
		$invArr = array();
		$imgSecondaryArr = array();
		if($option1name != ''){
			$options[] = array("name"=> $option1name, "position"=> 1);
		}
		if($option2name != ''){
			$options[] = array("name"=> $option2name, "position"=> 2);
		}
		if($option3name != ''){
			$options[] = array("name"=> $option3name, "position"=> 3);
		}				
				
		$variantPosition = 1;
		while($variantRow = $variantResult->fetch_assoc()){
			$variant_id = $variantRow['id'];
			$sku = $variantRow['sku'];
			$price = $variantRow['price'];
			$orig_price = $variantRow['orig_price'];
			 if($user_id == 19443){
				$price = applyPriceMarkupSettingsRow19443($price, $settingsRow);
			} 
			else {
				
				$price = applyPriceMarkupSettingsRow($price, $settingsRow);
			}  

            if($user_id == 4881){
                $searchstring = $productRow['searchstring'];
                if($searchstring == "eBayMotors"){
                    $price = $price;   
                } else if($searchstring == "Australia"){
                    $price = $price*0.76;
                } else if($searchstring == "France"){
                    $price = $price*1.19;
                } else if($searchstring == "Spain"){
                    $price = $price*1.19;
                } else if($searchstring == "Germany"){
                    $price = $price*1.19;
                } else if($searchstring == "Italy"){
                    $price = $price*1.19;
                } else  if($searchstring == "UK"){
                    $price = $price*1.39;
                } else  if($searchstring == "Canada"){
                    $price = $price*0.80;
                } else  if($searchstring == "Ireland"){
                    $price = $price*1.14;
                } else {
                    return false;
                }
            }


            if(strlen($sku) == 0 ){
			$sku = $ebayitemid."-".$variantRow['id'];
		     }
            
			if($user_id == 44057 && array_key_exists($product_id, $shippingCostArr)){
				$shippingcost = 0;
				foreach($shippingCostArr[$product_id] as $k => $v){
					$shippingcost = $v;
					break;
				}
				$tags ="Ship_" .$shippingcost;

				print_r($tags);
			}

			$quantity = $variantRow['quantity'];	
			if($user_id != 1690){
				$weight = $variantRow['weight'];
				$weight_unit = $variantRow['weight_unit'];
			}

			if($user_id == 44841){
    		    $weight = 0.02;
    		    $weight_unit = "kg";
    		}

			if($user_id == 12522){
    		    $weight = 0.02;
    		    $weight_unit = "kg";
    		}
			$productid = $variantRow['productid'];
			$option1val = $variantRow['option1val'];
			$option2val = $variantRow['option2val'];
			$option3val = $variantRow['option3val'];
			if($isSizeType && $user_id == 2481){
				$option1val = $variantRow['option1val'];
				$option2val = $variantRow['option3val'];
				$option3val = $variantRow['option4val'];
			}
			if($isSizeType && $user_id == 6364){
				$option1val = $variantRow['option2val'];
				$option2val = $variantRow['option3val'];
				$option3val = $variantRow['option4val'];
			}
			if($isSizeType && $user_id == 32){
				$option1val = $variantRow['option1val'];
				$option2val = $variantRow['option3val'];
				$option3val = $variantRow['option4val'];
			}
			if($isSizeType && $user_id == 4188){
				$option1val = $variantRow['option2val'];
				$option2val = $variantRow['option3val'];
				$option3val = $variantRow['option4val'];
			}
		
			if($user_id == 45738) {            
				$sku =  $sku;
			}

			if($user_id == 46021) {            
				$sku =  $sku;
			}

			$taxable = true;
			if($settingsRow['taxable'] == 0){
				$taxable = false;
			}
			if($user_id == 45000){
				$productid = $sku;
			}
			if($user_id == 44841 ){
				$productid ="";
				$taxable = false;
			}
            if($user_id == 46910){

                 $tags[] = $sku;
            }
			if($user_id == 46668 && $sku == '' ){
                
				 $sku = $ebayitemid;
			}
			if($user_id == 47309){
				$description ="";
				$item_specific = $productRow['item_specific'];
				$condition_val = $productRow['condition_val'];
				$description = setdescriptiontemplate47309($item_specific,$title,$condition_val);
			  }
			  if ($user_id == 47617){
				//GBP TO EURO
				$price =$variantRow['orig_price'];
				if($price > 0){
				  $price = $price*1.20;
				}
				else{
					$price = $variantRow['price'];
					$price= $price*1.20;
				}
				//$price = round($price);
			}
            
			$singleVariant = array(
								"sku"=>$sku,
								"position"=>$variantPosition++,
								"price"=>number_format($price, 2, '.', ''),
								"inventory_policy"=>"deny",								
								"fulfillment_service"=> $fulfilment_service,
								"inventory_management"=> $inventory_policy,		
								"taxable"=>$taxable,								
								"weight" => $weight,
								"weight_unit" => $weight_unit,
								"barcode" => $productid,
								"requires_shipping"=> true,
								"quantity" => $quantity
							);
            if( ( $user_id == 41578 || $user_id == 43633 || $user_id == 42894 || $user_id == 44890) && $inventory_policy == "NO"){
    		    $singleVariant['inventory_management'] = "";
    		}   
			
			if($user_id == 48043){
        		$singleVariant["inventory_policy"] = "continue";
        		
        	}
		if ($user_id == 44495){
			$product_type = $productRow['product_type'];
			if(strpos($product_type, 'Clothes') === false) {
             $singleVariant["requires_shipping"] = false;
			}
          }

        	if($user_id == 46454){
				if($brand == 'Oliver People' || $brand == 'Saint Laurent'){
        	       $comparePrice = $price*0.35;
        	       $singleVariant["compare_at_price"] = number_format($comparePrice, 2, '.', '');
				}
				else if($brand == 'PUMA'){
					$comparePrice = $price*0.60;
					$singleVariant["compare_at_price"] = number_format($comparePrice, 2, '.', '');
				}
        	}
			
			
			
			if($user_id == 44719 || $user_id == 45165 ){
				$singleVariant["barcode"]  = $ebayitemid;
			   
			}
			if($user_id == 47617){
				$price =$variantRow['orig_price'];
				if($price > 0){
				  $price = $price;
				}
				else{
					$price = $variantRow['price'];
					
				}
				$metafieldsvvv = array();
			 $metafieldsvvv[] = array("key" => "ebay_item_price", "value" =>  $price, "type" => "single_line_text_field", "namespace" => "custom");
			 $singleVariant["metafields"] = $metafieldsvvv;
			}
			
			if( $user_id == 46021){
				if(is_array($itemSpecificObj)){
					foreach($itemSpecificObj as $k => $v){
						if($k == "EAN"){
							$singleVariant["barcode"] = $v;   
							break;
						}
					}
				}
			}





			if($user_id == 45000){
				$productid = $sku;
			}
						
			$imgskuArr = array();
			if($option1val != ''){
				$singleVariant['option1'] = trim($option1val);
				$imgskuArr[] = trim($option1val);
			}
			if($option2val != ''){
				$singleVariant['option2'] = trim($option2val);
				$imgskuArr[] = trim($option2val);
			}
			if($option3val != ''){
				$singleVariant['option3'] = trim($option3val);
				$imgskuArr[] = trim($option3val);
			}
			if( count($variants) > 99){
			    continue;
			}
					
			$imgsku = implode("::", $imgskuArr);
			$invArr[$imgsku] = $quantity;
			$imageResult = $conn->query("select * from product_images where user_id = ".$user_id." and variant_id = ".$variant_id);
			if($imageResult->num_rows > 0){
				$isFirstImage = true;
				while($imageRow = $imageResult->fetch_assoc()){
					$imgurl = $imageRow['imgurl'];
					if($user_id != 15715){
    				    $imgurl = str_replace("_12.JPG", "_57.JPG", $imgurl);
        				$imgurl = str_replace("_1.JPG", "_57.JPG", $imgurl);
        				$imgurl = str_replace("_0.JPG", "_57.JPG", $imgurl);
        				$imgurl = str_replace("_6.JPG", "_57.JPG", $imgurl);
    				} 
					
					else {
    				    if (strpos($imgurl, 'ebayimg') !== false) {
                            $imgurl = str_replace("_12.JPG", "_57.JPG", $imgurl);
            				$imgurl = str_replace("_1.JPG", "_57.JPG", $imgurl);
            				$imgurl = str_replace("_0.JPG", "_57.JPG", $imgurl);
            				$imgurl = str_replace("_6.JPG", "_57.JPG", $imgurl);
                        }   
    				}
					if($user_id == 38128){
    				    $imgurl = str_replace("_12.PNG", "_57.PNG", $imgurl);
        				$imgurl = str_replace("_1.PNG", "_57.PNG", $imgurl);
        				$imgurl = str_replace("_0.PNG", "_57.PNG", $imgurl);
        				$imgurl = str_replace("_6.PNG", "_57.PNG", $imgurl);
					
    				} 
					if($isFirstImage){
						$imageArr[] = $imgurl;
                        echo "fdfgdgfgfgfgf";
                        $singleVariant['imgurl'] = trim(preg_replace('/\?.*/', '', $imgurl));
					} else {
						$imgSecondaryArr[] = trim(preg_replace('/\?.*/', '', $imgurl));
					}
					$isFirstImage  = false;	
		            /*if($user_id == 4752){
			            $imageArr[] = array("src" => trim($imgurl), "position" => $position++,"width" => 1200,"height" => 1600);
                    }*/			
                }
			}
            $variants[] = $singleVariant;	
		}
		$imgSecondaryArr = array_unique($imgSecondaryArr);
		if(count($variants) == 0){
			echo "product without variants";
			return false;
		}//
		if(( $user_id == 41867 || $user_id == 45245 || $user_id == 45204 || $user_id == 46444 || $user_id == 45549) && count($variants) > 100){
		    $variants = array_slice($variants, 0, 100, TRUE);
		}

		if(count($variants) > 100){
		    $conn->query("update products set status = 'error', errdetails = 'More than 100 variants.' where product_id = ".$product_id." and user_id = ".$user_id);
			echo "product having more than 100 variants";
			return false;
		}
		



		if($user_id == 44582){			
			$storecategoryid = $productRow['storecategoryid'];
			// $tags = array();
			$product_type = "";
			if(array_key_exists($storecategoryid, $catArr)){
				$temp = $catArr[$storecategoryid];
				$category_name = $temp['category_name'];
				$parent_id = $temp['parent_id'];
				$tags[] = $category_name;
				$product_type = $category_name;
				if($parent_id != ""){
					if(array_key_exists($parent_id, $catArr)){
						$temp1 = $catArr[$parent_id];
						$category_name1 = $temp1['category_name'];
						$tags[] = $category_name1;
						$product_type = $category_name1;
					}
				}
			}
		}
		
		if( $user_id == 43700){
			$tags[] = $condition_val;
			
			$tags[] =$productRow['searchstring'];
		}
        
		// Add settings from DB
		$dbtags = trim($settingsRow['tags']);
		$dbvendor = trim($settingsRow['vendor']);
		$dbproduct_type = trim($settingsRow['product_type']);		
		if($dbvendor != ""){
			$brand = $dbvendor;
			echo "anjali12 $brand";
		}
		
		if($dbproduct_type != ""){
			$product_type = $dbproduct_type;
		}
		if($dbtags != ""){
			$tempTags = explode(",", $dbtags);
			foreach($tempTags as $tempTag){
				$tags[] = trim($tempTag);
			}
		}

        $pubstatus = "active";
       
		if(strlen($sku) > 255 ){
			$str = wordwrap($sku, 254);
			 $str = explode("\n", $str);
			 $sku = $str[0];
		 }
		 if($user_id == 43398){
			$tags = explode(":", $product_type);
		    $product_type = end($tags);
		}




		if($user_id == 44893 || $user_id == 48551){
			//die();

			$p_category_name ="";
			$product_id = $productRow['product_id'];

			echo"select category_name from storecategories where shopifycategoryid = '' and user_id = '" . $user_id."'  and category_id in (select storecategoryid from products where user_id= '".$user_id."'  and  product_id = '".$product_id."')";

			$result = $conn->query("select category_name,category_id from storecategories where shopifycategoryid = '' and user_id = '" . $user_id."'  and category_id in (select storecategoryid from products where user_id= '".$user_id."'  and  product_id = '".$product_id."') ");
			if($result->num_rows > 0){


				// while (
					$row = $result->fetch_assoc();
					//) {
					$child_category_name =$row['category_name'];
					$child_category_id  =$row['category_id'];
                    
					echo"hkjhhghkhkjhkjh".$child_category_id;

					$tags[]=$child_category_name;

				
                   
					$newcategory = $conn->query("select * from storecategories where user_id ='".$user_id."'");

					//echo "select * from storecategories where user_id = 44893";
			       
		     	if($newcategory->num_rows > 0){

				
				     while($row = $newcategory->fetch_assoc()){
					
					$category_id = $row['category_id'];

					
					$category_name = $row['category_name'];
					$parent_id = $row['parent_id'];
					$catArr[$category_id] = array("category_name" => $category_name, "parent_id" => $parent_id);
				
				}

				foreach ($catArr as $categoryId => $categoryData) {
					if ($categoryData['category_name'] == $child_category_name && $categoryId == $child_category_id) {
						$parent_id = $categoryData['parent_id'];
						break; 
					}
				}
				if ($parent_id != ' ') {

					foreach ($catArr as $categoryId => $categoryData) {
						if ($categoryId == $parent_id) {
							$p_category_name = $categoryData['category_name'];
							break; 
						}
					}
					
				} 

				if ($p_category_name != ' ') {

					echo"herrrrrr isss        ";

					echo "Parent id  ".$parent_id;

					echo"<br></br>";
					echo"                   ";

					echo "Parent Category name ". $p_category_name;

					$tags[]=$p_category_name;
					
				} else {
					echo "Category not found";
				}


				echo"here is tags ";
                print_r($tags);
				
				//print_r($catArr);
				
				//exit(0);
				
			}

		

			// 




				//}		

			

				// echo ".>>>>>soosmayyyrjhkjhkjhgkjh";
				// print_r($tags);
						
						
		
			
		}

			

			
		
		if($user_id == 46021){
			$tags = array();
			if (strpos($title, 'Mens') !== false ) {
				$tags[] = "gender_mens";
			}
			if(strpos($title, 'Womens') !== false){
            
				$tags[] = "gender_womens";
			}
		}


		}


		if($user_id == 47617){
			//die();

			$p_category_name ="";
			$product_id = $productRow['product_id'];

			echo"select category_name from storecategories where shopifycategoryid != '' and user_id = '" . $user_id."'  and category_id in (select storecategoryid from products where user_id= '".$user_id."'  and  product_id = '".$product_id."')";

			$result = $conn->query("select category_name,category_id from storecategories where shopifycategoryid != '' and user_id = '" . $user_id."'  and category_id in (select storecategoryid from products where user_id= '".$user_id."'  and  product_id = '".$product_id."') ");
			if($result->num_rows > 0){


				// while (
					$row = $result->fetch_assoc();
					//) {
					$child_category_name =$row['category_name'];
					$child_category_id  =$row['category_id'];
                    
					echo"hkjhhghkhkjhkjh".$child_category_id;

					$tags[]=$child_category_name;

				
                   
					$newcategory = $conn->query("select * from storecategories where user_id = 47617");

					//echo "select * from storecategories where user_id = 41563";
			       
		     	if($newcategory->num_rows > 0){

				
				     while($row = $newcategory->fetch_assoc()){
					
					$category_id = $row['category_id'];

					
					$category_name = $row['category_name'];
					$parent_id = $row['parent_id'];
					$catArr[$category_id] = array("category_name" => $category_name, "parent_id" => $parent_id);
				
				}

				foreach ($catArr as $categoryId => $categoryData) {
					if ($categoryData['category_name'] == $child_category_name && $categoryId == $child_category_id) {
						$parent_id = $categoryData['parent_id'];
						break; 
					}
				}
				if ($parent_id != ' ') {

					foreach ($catArr as $categoryId => $categoryData) {
						if ($categoryId == $parent_id) {
							$p_category_name = $categoryData['category_name'];
							break; 
						}
					}
					
				} 

				if ($p_category_name != ' ') {

					echo"herrrrrr isss        ";

					echo "Parent id  ".$parent_id;

					echo"<br></br>";
					echo"                   ";

					echo "Parent Category name ". $p_category_name;

					$tags[]=$p_category_name;
					
				} else {
					echo "Category not found";
				}


				echo"here is tags ";
                print_r($tags);
				
				//print_r($catArr);
				
				//exit(0);
				
			}

			// TODO: find child_category_name in catArr with child_category_nameand find parent_id
			//find pparent_id in catArr and get category_name and add in tag

			// 




				//}		

			

				// echo ".>>>>>soosmayyyrjhkjhkjhgkjh";
				// print_r($tags);
						
						
		
			
		}

			

			
		



		}



		if($user_id == 46190){
			//die();

			$p_category_name ="";
			$product_id = $productRow['product_id'];

			echo"select category_name from storecategories where shopifycategoryid = '' and user_id = '" . $user_id."'  and category_id in (select storecategoryid from products where user_id= '".$user_id."'  and  product_id = '".$product_id."')";

			$result = $conn->query("select category_name,category_id from storecategories where shopifycategoryid = '' and user_id = '" . $user_id."'  and category_id in (select storecategoryid from products where user_id= '".$user_id."'  and  product_id = '".$product_id."') ");
			if($result->num_rows > 0){


				// while (
					$row = $result->fetch_assoc();
					//) {
					$child_category_name =$row['category_name'];
					$child_category_id  =$row['category_id'];
                    
					echo"hkjhsomayhghkhkjhkjh".$child_category_id;

					$tags[]=$child_category_name;

				
                   
					$newcategory = $conn->query("select * from storecategories where user_id = 46190");

					//echo "select * from storecategories where user_id = 46190";
			       
		     	if($newcategory->num_rows > 0){

				
				     while($row = $newcategory->fetch_assoc()){
					
					$category_id = $row['category_id'];

					
					$category_name = $row['category_name'];
					$parent_id = $row['parent_id'];
					$catArr[$category_id] = array("category_name" => $category_name, "parent_id" => $parent_id);
				
				}

				foreach ($catArr as $categoryId => $categoryData) {
					if ($categoryData['category_name'] == $child_category_name && $categoryId == $child_category_id) {
						$parent_id = $categoryData['parent_id'];
						break; 
					}
				}
				if ($parent_id != ' ') {

					foreach ($catArr as $categoryId => $categoryData) {
						if ($categoryId == $parent_id) {
							$p_category_name = $categoryData['category_name'];
							break; 
						}
					}
					
				} 

				if ($p_category_name != ' ') {

					echo"herrrrrr isss        ";

					echo "Parent id  ".$parent_id;

					echo"<br></br>";
					echo"                   ";

					echo "Parent Category name ". $p_category_name;

					$tags[]=$p_category_name;
					
				} else {
					echo "Category not found";
				}


				echo"here is tags ";
                print_r($tags);
				
				
			}

			
		}

			

			
		



		}
		if($user_id == 46127){

			$description ="";
		}
		if($user_id == 47855){ 
			$item_specific = $productRow['item_specific'];
			$itemSpecArr = json_decode($item_specific, true);	
			if(isset($itemSpecArr['Brand'])){
				$brand = $itemSpecArr['Brand'];
			}
			if(isset($itemSpecArr['Golf Club Type'])){
				$product_type = $itemSpecArr['Golf Club Type'];
			}
			if(isset($itemSpecArr['Flex'])){
				$tags[] = $itemSpecArr['Flex'];
			}
			
			
			
		   }
		$data = array(
					"product"=>array(
						"title"=> $title,
						"body_html"=> $description,
						"vendor"=> $brand,
						"product_type"=> $product_type,
						"status"=> strtoupper($pubstatus),
						"tags"=> $tags,
						"published_scope" => "global",
						"variants" => $variants,
						"options" => $options
					)				
				);



                echo "++++++++++++++++++***++++++++++++";
                print_r(	$data);
                echo "++++++++++++++++++***++++++++++++";

		


		
	
        $imageArr_new = array();
		print_r("img".json_encode(array_unique($imageArr)));
        $ismultiimage = array_unique($imageArr);
        if(count($ismultiimage) == 1){
            $imageArr_new[] = array("alt"=>"ep01","mediaContentType" => "IMAGE","originalSource" => trim( $ismultiimage[0]));	
        }

        echo "---------------------------------------------=dsf-s=d-f=sd-f=s-df=s-df\n";
        print_r(   $ismultiimage);
        
        echo "---------------------------------------------=dsf-s=d-f=sd-f=s-df=s-df\n";

		$imgSecondaryArr = array_unique($imgSecondaryArr);
       
        foreach($imgSecondaryArr as $key => $v){
			
            $imageArr_new[] = array("alt"=>"$key","mediaContentType" => "IMAGE","originalSource" => trim( $v));				
        }
       
        $option1valall = array();
        $option2valall = array();
        $option3valall = array();
        echo "++++++++++++++++++++++++++++++";
        print_r($data);
        echo "++++++++++++++++++++++++++++++";

        if (!empty($data["product"]['options'][0])) {
            
            $option1valall = [
                "name" =>$data["product"]['options'][0]['name'] ,
                "position" => 1,
                "values" =>["name"=>$data["product"]['variants'][0]['option1']] ,
            ];
        }
        if (!empty($data["product"]['options'][1])) {
           
            $option2valall = [
                "name" =>$data["product"]['options'][1]['name'],
                "position" => 2,
               "values" =>["name"=>$data["product"]['variants'][0]['option2']],
            ];
        }
        if (!empty($data["product"]['options'][2])) {
            
            $option3valall = [
                "name" =>$data["product"]['options'][2]['name'],
                "position" => 3,
               "values" =>["name"=>$data["product"]['variants'][0]['option3']],
            ];
        }
          
          $jsonstring = [
			"input" => [
			  "title" => $data["product"]['title'],
			  'descriptionHtml' =>utf8_encode($data["product"]['body_html']),
			   'vendor' => $data["product"]['vendor'],
			   "tags"=>$data["product"]['tags'],
			   "status" => strtoupper($data["product"]['status']),
			   'productType'=>$data["product"]['product_type'],
			   'published'=> true,  
			    
            ],
			'media' =>  $imageArr_new
        ];
        if (isset($data["product"]['metafields'])) {
            $jsonstring["input"]['metafields'] =$data["product"]['metafields'];
        }
        $productOptions = [];
        if (!empty($option1valall)) $productOptions[] = $option1valall;
        if (!empty($option2valall)) $productOptions[] = $option2valall;
        if (!empty($option3valall)) $productOptions[] = $option3valall;
        if (!empty($productOptions)) {
            $jsonstring['input']['productOptions'] = $productOptions;
        }
        
       print_r( $jsonstring);
       $response = addShopifyProduct_graphql($token, $shopurl, $jsonstring);
       $responseDecoded = $response;
       addlog("check response addShopifyProduct: ","INFO");
       addlog("productcreate response".json_encode($response,true),"INFO");
       print_r( $response);
       if (empty($responseDecoded['data']['productCreate']['userErrors'])) {
        echo 'in if for updated variant';
        print_r($responseDecoded['data']['productCreate']['product']['id']);
       $gid_shopifyproductid = $responseDecoded['data']['productCreate']['product']['id'];
       
        $shopifyproductid = str_replace("gid://shopify/Product/", "",$gid_shopifyproductid);
    
        $shopifyvariantidG = $responseDecoded['data']['productCreate']['product']['variants']['nodes'][0]['id'];
        $shopifyvariantid = str_replace("gid://shopify/ProductVariant/", "", $shopifyvariantidG);
        $shopifyinventoryidG = $responseDecoded['data']['productCreate']['product']['variants']['nodes'][0]['inventoryItem']['id'];
        $shopifyinventoryid = str_replace("gid://shopify/InventoryItem/", "", $shopifyinventoryidG);
            $option1 = "";
            $option2 = "";
            $option3 = "";
            $whereQuery = "";
				if(isset($data["product"]['variants'][0]['option1'])){
					$option1 = $data["product"]['variants'][0]['option1'];
					$imgskuArr[] = trim($option1);
					$whereQuery .= " and option1val = '".mysqli_real_escape_string($conn, $option1)."' ";
				}
				if(isset($data["product"]['variants'][0]['option2'])){
					$option2 =$data["product"]['variants'][0]['option2'];
					$imgskuArr[] = trim($option2);
					$whereQuery .= " and option2val = '".mysqli_real_escape_string($conn, $option2)."' ";
				}
				if(isset($data["product"]['variants'][0]['option3'])){
					$option3 = $data["product"]['variants'][0]['option3'];
					$imgskuArr[] = trim($option3);
					$whereQuery .= " and option3val = '".mysqli_real_escape_string($conn, $option3)."' ";
				}
            $conn->query("update products set shopifyproductid = '".mysqli_real_escape_string($conn, $shopifyproductid)."', status = 'Imported', newflag = 0, updated_at = now() where user_id = '".$user_id."' and product_id = ".$product_id);

           $conn->query("update product_variants set shopifyproductid = '".mysqli_real_escape_string($conn, $shopifyproductid)."', shopifyvariantid = '".mysqli_real_escape_string($conn, $shopifyvariantid)."', shopifyinventoryid = '".mysqli_real_escape_string($conn, $shopifyinventoryid)."', shopifylocationid = '".mysqli_real_escape_string($conn, $location_id)."', status = 'Imported', newflag = 0, updated_at = now() where user_id = '".$user_id."' and product_id = ".$product_id." ".$whereQuery); 

          $conn->query("insert into shopifyproducts(user_id, productid, variantid, dateofmodification) values ('".$user_id."', '".mysqli_real_escape_string($conn, $shopifyproductid)."', '".mysqli_real_escape_string($conn, $shopifyvariantid)."', now())");
        addlog("productid ".$shopifyproductid, "INFO");
         $weight_unitnew = $data['product']['variants'][0]['weight_unit'];
        if(isset( $weight_unitnew) && $weight > 0){
             $weight_unitnew = convertWeightUnitToFullForm( $weight_unitnew);
        }else{
            $weight = 0;
             $weight_unitnew = "POUNDS";	
        }
		     $track =  true;
				if($data['product']['variants'][0]['inventory_policy'] == "continue"){
                  $track = false;
				}
				$variantVariables ='';
                echo "dddddddddddddddddddd\n";
                print_r($data['product']['variants']);
                echo "\n";

				if($data['product']['variants'][0]['imgurl'] !=''  ){
					$image_url = $data['product']['variants'][0]['imgurl'];
					
        $variantVariables = [
            "productId" => "gid://shopify/Product/$shopifyproductid",
			"media" =>[
				"alt" => "epi".$data['product']['variants'][0]['barcode'],
                        "mediaContentType" => "IMAGE",
                        "originalSource" =>  $image_url,
			],
            "variants" => [
                [
                    "id" => "gid://shopify/ProductVariant/$shopifyvariantid",
    
                    "barcode" => $data['product']['variants'][0]['barcode'],
                    "inventoryItem" => [
                        "measurement" => [
                            "weight" => [
                                "unit" =>  $weight_unitnew,
                                "value" => (float)$data['product']['variants'][0]['weight'],
                            ],
                        ],
                        "requiresShipping" => true,
                        "sku" => $data['product']['variants'][0]['sku'],
                        "tracked" => true,
                    ],
                    "inventoryPolicy" => strtoupper($data['product']['variants'][0]['inventory_policy']),
                    "price" => $data['product']['variants'][0]['price'],
                    "taxable" =>  $data['product']['variants'][0]['taxable'],
					"mediaSrc" =>$image_url,
                ],
            ],
        ];
	   }
	   else{
		$variantVariables = [
            "productId" => "gid://shopify/Product/$shopifyproductid",
			
            "variants" => [
                [
                    "id" => "gid://shopify/ProductVariant/$shopifyvariantid",
    
                    "barcode" => $data['product']['variants'][0]['barcode'],
                    "inventoryItem" => [
                        "measurement" => [
                            "weight" => [
                                "unit" =>  $weight_unitnew,
                                "value" => (float)$data['product']['variants'][0]['weight'],
                            ],
                        ],
                        "requiresShipping" => true,
                        "sku" => $data['product']['variants'][0]['sku'],
                        "tracked" => true,
                    ],
                    "inventoryPolicy" => strtoupper($data['product']['variants'][0]['inventory_policy']),
                    "price" => $data['product']['variants'][0]['price'],
                    "taxable" =>  $data['product']['variants'][0]['taxable'],
					
                ],
            ],
        ];
	   }
        addlog("processto uplocat v data".json_encode($variantVariables,true),"INFO");
    $res = updateShopifyVariant_graphql($token, $shopurl, $variantVariables);
	print_r($res);
	addlog("processto uplocat v data response".json_encode( $res,true),"INFO");
    if($res){
        addlog("updateShopifyVariant id true ","INFO");
        $variables = [
            "input" => [
                "ignoreCompareQuantity" => true,
                "name" => "available",
                "reason" => "correction",
                "quantities" => [
                    [
                        "inventoryItemId" =>  "gid://shopify/InventoryItem/$shopifyinventoryid",
                        "locationId" => "gid://shopify/Location/$location_id",
                        "quantity" => (int)$data['product']['variants'][0]['quantity']
                    ]
                ]
            ]
        ];
        addlog("updateShopifyInventory_graphql ","INFO");
        addlog("updateinve data".json_encode($variables,true),"INFO");
        sleep(2);
        $USI = updateShopifyInventory_graphql($token, $shopurl,$variables);
		print_r($USI);
		addlog("processto uplocat invdata response".json_encode( $USI,true),"INFO");
        if ($USI) {
			// Remove the first variant and re-index the array
			unset($data["product"]['variants'][0]);
			$data["product"]['variants'] = array_values($data["product"]['variants']);
		print_r($data);
			$variantArray = [];
			$media =[];
			foreach ($data["product"]['variants'] as $key => $variant) {
				$productOptions = [];
		
				// Handle Option 1
				if (!empty($data["product"]['options'][0])) {
					$productOptions[] = [
						"name" =>$variant['option1'],
						"optionName" =>$data["product"]['options'][0]['name'] ,
					];
				}
				
				// Handle Option 2
				if (!empty($data["product"]['options'][1])) {
					$productOptions[] = [
						"name" =>$variant['option2'] ,
						"optionName" =>$data["product"]['options'][1]['name'] ,
					];
				}
				
				// Handle Option 3
				if (!empty($data["product"]['options'][2])) {
					$productOptions[] = [
						"name" =>$variant['option3'] ,
						"optionName" =>$data["product"]['options'][2]['name'] ,
					];
				}
				
				$track =  true;
				if($variant['inventory_policy'] == "continue"){
                  $track = false;
				}
				// Convert weight unit to full form or set defaults
				$weight = isset($variant['weight']) ? (float)$variant['weight'] : 0;
				$weightUnit = !empty($variant['weight_unit']) && $weight > 0
					? convertWeightUnitToFullForm($variant['weight_unit'])
					: "POUNDS";
		
				// Build variant array
				if($variant['imgurl'] !=""  && count($ismultiimage) != 1 ){
					$image_url = $variant['imgurl'];
					
					$variantArray[] = [
						'optionValues' => $productOptions,
						'price' => (float)$variant['price'],
						'inventoryQuantities' => [
							'availableQuantity' => (int)$variant['quantity'],
							'locationId' => 'gid://shopify/Location/' . $location_id,
						],
						
						'barcode' => $variant['barcode'],
						'inventoryItem' => [
							'measurement' => [
								'weight' => [
									'unit' => $weightUnit,
									'value' => $weight,
								],
							],
							'sku' => $variant['sku'],
							'tracked' => true,
							'requiresShipping' => true,
						],
						'inventoryPolicy' => "DENY",
						'mediaSrc' => $image_url,
					];
	
					$media[] =[
						   "alt" => "epi".$key,
							"mediaContentType" => "IMAGE",
							"originalSource" => $image_url,
					];
					  }
					else{
						$variantArray[] = [
							'optionValues' => $productOptions,
							'price' => (float)$variant['price'],
							'inventoryQuantities' => [
								'availableQuantity' => (int)$variant['quantity'],
								'locationId' => 'gid://shopify/Location/' . $location_id,
							],
							
							'barcode' => $variant['barcode'],
							'inventoryItem' => [
								'measurement' => [
									'weight' => [
										'unit' => $weightUnit,
										'value' => $weight,
									],
								],
								'sku' => $variant['sku'],
								'tracked' => true,
								'requiresShipping' => true,
							],
							'inventoryPolicy' => "DENY",
							
						];
		
						
	
					}
			}
			$jsonVariantsPayload ='';
			// Prepare the JSON payload for Shopify variants
			if(isset($media)){
			$jsonVariantsPayload = [
				"media" => $media,
				"productId" =>$gid_shopifyproductid,
				"variants" => $variantArray,

			];}else{
				$jsonVariantsPayload = [
				"productId" =>$gid_shopifyproductid,
				"variants" => $variantArray,
				];
			}
			
		    print_r($jsonVariantsPayload);
			// Create Shopify variants
			$allRemainingVariants = createShopifyVaraint_graphql($shopurl, $token, $jsonVariantsPayload);
			print_r($allRemainingVariants);
			addlog("bulkcreatevariants response  ".json_encode($allRemainingVariants),"INFO");
			$productVariants = $allRemainingVariants['data']['productVariantsBulkCreate']['productVariants'];
			addlog("bulkcreatevariants productVariants response  ".json_encode($productVariants),"INFO");
			foreach ($productVariants as $variantnew) {
				print_r($variantnew);
				addlog("bulkcreatevariantssingle productVariants response  ".json_encode($variantnew),"INFO");
				$gid_inventoryItemId = $variantnew['inventoryItem']['id'];
				$gid_shopifyvariantid = $variantnew['id']; 
				$shopifyvariantid = str_replace("gid://shopify/ProductVariant/", "", $gid_shopifyvariantid);
				$shopifyinventoryid = str_replace("gid://shopify/InventoryItem/", "", $gid_inventoryItemId);
				
				$option1 = "";
				$option2 = "";
				$option3 = "";
				$whereQuery = "";
				$selectedOptions = $variantnew['selectedOptions']; 
				if (isset($variantnew['selectedOptions'][0]['value'])) {
					$option1 = $variantnew['selectedOptions'][0]['value'];
					$whereQuery .= " and option1val = '" . mysqli_real_escape_string($conn, $option1) . "' ";
				}
				if (isset($variantnew['selectedOptions'][1]['value'])) {
					$option2 = $variantnew['selectedOptions'][1]['value'];
					$whereQuery .= " and option2val = '" . mysqli_real_escape_string($conn, $option2) . "' ";
				}
				if (isset($variantnew['selectedOptions'][2]['value'])) {
					$option3 = $variantnew['selectedOptions'][2]['value'];
					$whereQuery .= " and option3val = '" . mysqli_real_escape_string($conn, $option3) . "' ";
				}
				 ///
				 echo "UPDATE product_variants SET 
										shopifyproductid = '" . mysqli_real_escape_string($conn, $shopifyproductid) . "', 
										shopifyvariantid = '" . mysqli_real_escape_string($conn, $shopifyvariantid) . "', 
										shopifyinventoryid = '" . mysqli_real_escape_string($conn, $shopifyinventoryid) . "', 
										shopifylocationid = '" . mysqli_real_escape_string($conn,$shopifylocationid) . "', 
										status = 'Imported', 
										newflag = 0, 
										updated_at = now() 
									WHERE user_id = '" . $user_id . "' AND product_id = " . $product_id . " " . $whereQuery;
				    // Update the product variants
					$conn->query("UPDATE product_variants SET 
					shopifyproductid = '" . mysqli_real_escape_string($conn, $shopifyproductid) . "', 
					shopifyvariantid = '" . mysqli_real_escape_string($conn, $shopifyvariantid) . "', 
					shopifyinventoryid = '" . mysqli_real_escape_string($conn, $shopifyinventoryid) . "', 
					shopifylocationid = '" . mysqli_real_escape_string($conn,$location_id) . "', 
					status = 'Imported', 
					newflag = 0, 
					updated_at = now() 
				  WHERE user_id = '" . $user_id . "' AND product_id = " . $product_id . " " . $whereQuery);
					
						// Insert into shopifyproducts
						// EZ A JAVÍTOTT KÓD
						$conn->query("INSERT INTO shopifyproducts(user_id, productid, variantid, dateofmodification) 
                            		VALUES ('" . $user_id . "', '" . mysqli_real_escape_string($conn, $shopifyproductid) . "', '" . mysqli_real_escape_string($conn, $shopifyvariantid) . "', NOW())");
											addlog("bulkcreatevariantssingle productVariants response comleted  ","INFO");
			}
		}
		
    }

    }
    
	}

	function getExistingSKUs($user_id){
		global $conn;
		$existingSKUs = array();
		/*$result = $conn->query("select * from shopifyproducts where user_id = ".$user_id);
		if ($result->num_rows > 0) {
			while($row = $result->fetch_assoc()) {	
				$existingSKUs[] = $row['sku'];
			}
		}*/
		return $existingSKUs;
	}
	
	function getProdDescription($user_id, $product_id){
		global $conn;
		$description = '';
		//return $description;
		/*if($user_id == 8236){
		    return $description;
		}*/
		$result = $conn->query("select description from product_description where product_id = ".$product_id." and user_id = ".$user_id);
		if ($result->num_rows > 0) {
			while($row = $result->fetch_assoc()) {	
				$description = $row['description'];
			}
		}
		return $description;
	}
	
	function getProdDescriptionNew($user_id, $ebayitemid, $ebtoken, $siteid){
		global $conn, $ebConfig;
		$description = '';		
		try {
    		$service = new Services\TradingService([
    					'credentials' => $ebConfig['production']['credentials'],
    					'sandbox'     => false,
    					'siteId'      => $siteid
    				]);		
    		$itemRequest = new Types\GetItemRequestType();
    		$itemRequest->RequesterCredentials = new Types\CustomSecurityHeaderType();
    		$itemRequest->RequesterCredentials->eBayAuthToken = $ebtoken;
    		$itemRequest->ItemID = $ebayitemid;
    		$itemRequest->DetailLevel = ['ReturnAll'];
    		$itemRequest->IncludeItemCompatibilityList = true;
    		$itemRequest->IncludeItemSpecifics = true;
    		$itemResponse = $service->getItem($itemRequest);		
    		if($itemResponse->Ack !== 'Failure') {
    			if(!isset($itemResponse->Item)){				
    				$description = '';
    			}
    			$item = $itemResponse->Item;
    			$description = $item->Description;
    		}	
		} catch(Exception $e){
		
		}
		return $description;
	}
	
	// Start adding functions to handle multiple location
	function getMainLocation($user_id, $shopurl, $token){
		global $conn;
		$result = $conn->query("select * from locations where legacy = 0 and user_id = ".$user_id." order by shopifylocationid * 1");
		if($result->num_rows > 0){
			$row = $result->fetch_assoc();
			$location_id = $row['shopifylocationid'];
			return $location_id;
		} else{
			// Try to fetch all possible locations
			$location_id = fetchLocations($user_id, $shopurl, $token);
			if($location_id){
				return $location_id;
			}
		}
		return false;
	}
	
	function fetchLocations($user_id, $shopurl, $token) {
		global $conn;		
		$existingLocations = array();
		$result = $conn->query("select * from locations where user_id = ".$user_id);
		if($result->num_rows > 0){
			while($row = $result->fetch_assoc()){
				$existingLocations[] = $row['shopifylocationid'];
			}
		}
		$apiUrl = "https://$shopurl/admin/api/2024-04/graphql.json";
	
	
			$query = <<<QUERY
			{
			locations(first: 10) {
				edges {
				node {
					id
					name
					legacyResourceId
				}
				}
			}
			}
			QUERY;
			
		
			$headers = [
				"Content-Type: application/json",
				"X-Shopify-Access-Token: $token"
			];
		
			$postData = json_encode(['query' => $query]);
		
			$ch = curl_init($apiUrl);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
		
			$response = curl_exec($ch);
			$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			curl_close($ch);
		
			if ($httpCode !== 200) {
				return "Error: Unable to fetch data. HTTP Status Code: $httpCode";
			}
		
			$data = json_decode($response, true);
	
			print_r($data);
			$locations = $data['data']['locations']['edges'] ?? [];
	
		foreach ($locations as $location) {
		$name = $location['node']['name'];
	
		
	
		preg_match('/\d+$/', $location['node']['id'], $matches);
	
		$locationId = $matches[0];
		
		echo $locationId;
	
		$shopifylocationid = $locationId;
	
		echo $shopifylocationid;
	
		if(in_array($shopifylocationid, $existingLocations)){
			continue;
		}
		$legacy = 0;
		if($location['node']['legacyResourceId']){
			$legacy = 1;
		
		}
		$conn->query("insert into locations(name, legacy, status, shopifylocationid, user_id, created_at, updated_at) values('".mysqli_real_escape_string($conn, $name)."', ".$legacy.", 'active', ".mysqli_real_escape_string($conn, $shopifylocationid).", ".$user_id.", now(), now())");
		$existingLocations[] = $shopifylocationid;
		}
		if(count($existingLocations) > 0){
	
			sort($existingLocations, SORT_NUMERIC); 
			return $existingLocations[0];
	
		} else {
			return false;
		}
	
		
	}
	function applyMarkup($price) {
		$newprice = $price;
		if($price < 50){
			$newprice = $price*0.904;
		} else if($price < 100){
			$newprice = $price*0.906;
		} else if($price < 250){
			$newprice = $price*0.911;
		} else if($price < 500){
			$newprice = $price*0.925;
		} else if($price < 1000){
			$newprice = $price*0.928;
		} else if($price < 2000){
			$newprice = $price*0.9425;
		} else if($price < 3000){
			$newprice = $price*0.949;
		} else if($price < 6000){
			$newprice = $price*0.9545;
		} else if($price < 999999){
			$newprice = $price*0.959;
		}
		if($price > 100){
			$newprice = ceil($newprice);
		}
		return number_format($newprice, 2, ".","");
	}

	function processConditionText($inputText){
		$outputArr = array();
		$titlesuffix = "";
		$conditiontag = "";
		if($inputText == "Used"){
			$titlesuffix = "Used";
			$conditiontag = "cond-used";
		} else if($inputText == "Seller refurbished"){
			$titlesuffix = "Refurbished";
			$conditiontag = "cond-refurbished";
		} else if($inputText == "New other (see details)"){
			$titlesuffix = "New";
			$conditiontag = "cond-new";
		} else if($inputText == "Pre-owned"){
			$titlesuffix = "Used";
			$conditiontag = "cond-used";
		} else if($inputText == "New"){
			$titlesuffix = "New";
			$conditiontag = "cond-new";
		} else if($inputText == "For parts or not working"){
			$titlesuffix = "For Parts";
			$conditiontag = "cond-parts";
		} else if($inputText == "Manufacturer refurbished"){
			$titlesuffix = "Refurbished";
			$conditiontag = "cond-refurbished";
		} 
		if($titlesuffix != ""){
			$outputArr['titlesuffix'] = $titlesuffix;
		}
		if($conditiontag != ""){
			$outputArr['conditiontag'] = $conditiontag;
		}
		return $outputArr;	
	}

	function utf8_fopen_read($fileName) { 
		$fc = iconv('windows-1250', 'utf-8', file_get_contents($fileName)); 
		$handle=fopen("php://memory", "rw"); 
		fwrite($handle, $fc); 
		fseek($handle, 0); 
		return $handle; 
	}
    
    function str_replace_n($search, $replace, $subject, $occurrence) {
    	$search = preg_quote($search);
        return preg_replace("/^((?:(?:.*?$search){".--$occurrence."}.*?))$search/", "$1$replace", $subject);
    }
    
    function slugify($text)
    {
        // Strip html tags
        $text=strip_tags($text);
        // Replace non letter or digits by -
        $text = preg_replace('~[^\pL\d]+~u', '_', $text);
        // Transliterate
        setlocale(LC_ALL, 'en_US.utf8');
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
        // Remove unwanted characters
        $text = preg_replace('~[^-\w]+~', '', $text);
        // Trim
        $text = trim($text, '-');
        // Remove duplicate -
        $text = preg_replace('~-+~', '-', $text);
        // Lowercase
        $text = strtolower($text);
        // Check if it is empty
        if (empty($text)) { return 'n-a'; }
        // Return result
        return $text;
    }
    

    function RemoveSpecialChar($str) {
 
      // Using str_replace() function
      // to replace the word
      $res = str_replace( array( '\'', '"',
      ',' , ';', '<', '/' ), ' ', $str);
 
      // Returning the result
      return $res;
      }

	  
      function addlog($message, $type="INFO"){
		global $logfile;
		$txt = date("Y-m-d H:i:s")." [".$type."]: ".$message."\n";
		fwrite($logfile, $txt);
	}
	addlog("Execution Finished", "INFO");
	fclose($logfile);
?>







