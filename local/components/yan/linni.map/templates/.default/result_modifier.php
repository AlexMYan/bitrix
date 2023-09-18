<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

/*
echo "<pre>";
print_r($arResult["ITEMS"]);
echo "</pre>";*/

if($arResult["ITEMS"]){

    $arResult["COUNTRY"]=[];
    $arResult["COUNTRY_NAME"]=[];
    foreach ($arResult["ITEMS"] as $key => &$item){

        if(empty($item["COORDS"])){
            unset($arResult["ITEMS"][$key]);
        }else{
            //COORFDINATES
            $pieces = explode(",", $item["COORDS"]);

            if(count($pieces)==2){
                $item["position"]=$pieces;
            }
            $arResult["COUNTRY"][key($item["COUNTRY"])][]=$item;
            $arResult["COUNTRY_NAME"][key($item["COUNTRY"])]=$item["COUNTRY"][key($item["COUNTRY"])];
        }
    }
}

