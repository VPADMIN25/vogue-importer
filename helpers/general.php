<?php
// helpers/general.php

function applyPriceMarkupSettingsRow19443($price, $settingsRow){
    $newprice = $price;
    $markupenabled = $settingsRow['markupenabled'];
    $markuptype = $settingsRow['markuptype'];
    $markupval = $settingsRow['markupval'];
    $markupround = $settingsRow['markupround'];
    if($markupenabled == 1){
        if ($markuptype == "FIXED") {
            $newprice = $price + $markupval;
        } else {
            $newprice = $price + $price * $markupval / 100;
        }		
        $newprice = round($newprice,1);
    }	
    return $newprice;
}

function applyPriceMarkup($price, $markupenabled, $markuptype, $markupval, $markupround){
    $newprice = $price;
    if($markupenabled == 1){
        if ($markuptype == "FIXED") {
            $newprice = $price + $markupval;
        } else {
            $newprice = $price + $price * $markupval / 100;
        }		
        if ($markupround) {
            $newprice = round($newprice) - 0.01;
        }
    }	
    return $newprice;
}

function applyPriceMarkupSettingsRow($price, $settingsRow){
    $newprice = $price;
    $markupenabled = $settingsRow['markupenabled'];
    $markuptype = $settingsRow['markuptype'];
    $markupval = $settingsRow['markupval'];
    $markupround = $settingsRow['markupround'];
    if($markupenabled == 1){
        if ($markuptype == "FIXED") {
            $newprice = $price + $markupval;
        } else {
            $newprice = $price + $price * $markupval / 100;
        }		
        if ($markupround) {
            $newprice = round($newprice) - 0.01;
        }
    }	
    return $newprice;
}
?>
