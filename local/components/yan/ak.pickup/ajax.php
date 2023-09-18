<?define("NOT_CHECK_PERMISSIONS", true);
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

use Bitrix\Main\Localization\Loc,
    Bitrix\Main\Web\Json;

if(!Bitrix\Main\Loader::includeModule("iblock"))
    return;

Loc::loadMessages(__FILE__);

global $APPLICATION;

$context = Bitrix\Main\Application::getInstance()->getContext();

$response = $context->getResponse();
$response->addHeader("Content-Type", "application/json");

$request = $context->getRequest();

if($request->isAjaxRequest()) {

    $action = $request->get("action");

    //GET_DATA//
    if($action == "getData") {

        $arFilter=["ACTIVE" => "Y"];

        $arSelect=[
            "ID",
            "NAME",
            "IBLOCK_ID",
            "ADRESS_" => "ADRESS",
            "CITY_" => "CITY",
            "TIME_WORK_" => "TIME_WORK",
            "PHONE_" => "PHONE",
            "PAY_" => "PAY",
            "DESCRIPTION_" => "DESCRIPTION",
            "COORFDINATES_"=>"COORFDINATES",
        ];

        $getCity = $request->get("city");
        //Достаем все города
        if($getCity=="all"){
        //Нужный город
        }elseif(!empty($getCity)){
            $arFilter=[
                "ACTIVE" => "Y",
                "@CITY.VALUE" => $getCity,
            ];
        }

        $result =Extra\Helper::getDataValues($arSelect,$arFilter);

        if($result){
            foreach ($result as $key => &$item){

                if(empty($item["COORFDINATES"])){
                    unset($result[$key]);
                }else{
                    if(!empty($item["CITY"])){

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

            $response->flush(Json::encode(array(
                "status" => true,
                "result" => $result
            )));
        }else{
            $response->flush(Json::encode(array(
                "status" => false,
            )));
        }

    }

}