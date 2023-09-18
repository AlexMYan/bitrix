<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

$arResult["CITIES"]=[];
$arResult["POINTS"]=[];


if($arResult["ITEMS"]){
   foreach ($arResult["ITEMS"] as $key => &$item){

       if(empty($item["COORFDINATES"])){
           unset($arResult["ITEMS"][$key]);
       }else{
           if(!empty($item["CITY"])){
               //CITIES
               $arResult["CITIES"][]=$item["CITY"];
               //ADRESS
               $item["ADRESS"]=$item["CITY"].", ".$item["ADRESS"];
           }
           //DESCRIPTION
           $item["DESCRIPTION_TEXT"]="";
           if($item["DESCRIPTION"]){
               $arDescTemp=unserialize($item["DESCRIPTION"]);
               if($arDescTemp){
                   $item["DESCRIPTION_TEXT"]=$arDescTemp["TEXT"];
               }
           }
          //COORFDINATES
           $pieces = explode(",", $item["COORFDINATES"]);

           if(count($pieces)==2){
               $item["position"]=$pieces;
           }
       }
   }
}
$arResult["CITIES"]=array_values(array_unique($arResult["CITIES"]));
