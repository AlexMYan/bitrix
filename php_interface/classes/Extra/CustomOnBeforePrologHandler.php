<?php
namespace Extra;
/**
 * Подмена url c фильтром
 */
class CustomOnBeforePrologHandler{

    public function ReinitPath(){

        $context = \Bitrix\Main\Application::getInstance()->getContext();
        //Забирать get- и post-переменные
        $request = $context->getRequest();

        if (strpos($request->getRequestUri(), "/bitrix") === false && (!$request->isAjaxRequest() || $request->get
                ("bxajaxid"))) {
            //id инфоблока с подменой url
            $SMART_FILTER_SEO_ID=IBLOCK_FILTER_SEO_ID;

            if($SMART_FILTER_SEO_ID>0){
                //массив url
                $arUrl = \CHTTP::ParseURL($request->getRequestUri());
                //Декодирование URL-кодированной строки
                $arUrl["path"] = urldecode($arUrl["path"]);

                $arFilter = array("ACTIVE" => "Y", "IBLOCK_ID" => $SMART_FILTER_SEO_ID, array("LOGIC" => "OR", array("LOGIC" => "OR", array("PROPERTY_DEFAULT_URL" => $arUrl["path"]), array("PROPERTY_DEFAULT_URL" => $arUrl["path_query"])), array("CODE" => basename($arUrl["path"]))));

                $isCacheManager = defined("BX_COMP_MANAGED_CACHE") && is_object($GLOBALS["CACHE_MANAGER"]);

                $obCache = new \CPHPCache();
                if($obCache->InitCache(36000000, serialize($arFilter), "/iblock/catalog")) {
                    $arResult = $obCache->GetVars();
                } elseif(\Bitrix\Main\Loader::includeModule("iblock") && $obCache->StartDataCache()) {
                    $arResult = array();
                    $rsElement = \CIBlockElement::GetList(array(), $arFilter, false, false, array("ID", "CODE", "IBLOCK_ID", "DETAIL_PAGE_URL", "PROPERTY_DEFAULT_URL",));

                    if($isCacheManager)
                        $GLOBALS["CACHE_MANAGER"]->StartTagCache("/iblock/catalog");

                    if($arElement = $rsElement->GetNext()) {
                        if($isCacheManager)
                            $GLOBALS["CACHE_MANAGER"]->RegisterTag("iblock_id_".$SMART_FILTER_SEO_ID);

                        $arResult["DEFAULT_URL"] = $arElement["PROPERTY_DEFAULT_URL_VALUE"];

                        if(!empty($arElement["CODE"]))
                            $arResult["NEW_URL"] = $arElement["DETAIL_PAGE_URL"];
                    }

                    if($isCacheManager)
                        $GLOBALS["CACHE_MANAGER"]->EndTagCache();

                    $obCache->EndDataCache($arResult);
                } else {
                    $arResult = array();
                }
                unset($arFilter);

                if(!empty($arResult["DEFAULT_URL"]) && !empty($arResult["NEW_URL"]) && $arResult["DEFAULT_URL"] !=
                    $arResult["NEW_URL"]) {

                    if($arUrl["path"] == $arResult["DEFAULT_URL"] || $arUrl["path_query"] == $arResult["DEFAULT_URL"]) {
                        LocalRedirect($arResult["NEW_URL"].(!empty($arUrl["query"]) ? "?".$arUrl["query"] : ""), false, "301 Moved Permanently");
                    } elseif($arUrl["path"] == $arResult["NEW_URL"]) {
                        $server = $context->getServer();
                        $server_array = $server->toArray();

                        $arUrlNew = \CHTTP::ParseURL($arResult["DEFAULT_URL"]);

                        if(!empty($arUrlNew["query"])) {
                            $getList = explode("&", $arUrlNew["query"]);
                            foreach($getList as $getItem) {
                                $get = explode("=", $getItem);
                                $_GET[$get[0]] = $get[1];
                            }
                            unset($get, $getItem, $getList);
                        }
                        unset($arUrlNew);


                        $_SERVER["REQUEST_URI"] = $arResult["DEFAULT_URL"];
                        $server_array["REQUEST_URI"] = $_SERVER["REQUEST_URI"];

                        $server->set($server_array);

                        $context->initialize(new \Bitrix\Main\HttpRequest($server, $_GET, array(), array(), $_COOKIE)
                            , $context->getResponse(), $server);
                        $GLOBALS["APPLICATION"]->sDocPath2 = GetPagePath(false, true);
                        $GLOBALS["APPLICATION"]->sDirPath = GetDirPath($GLOBALS["APPLICATION"]->sDocPath2);
                        $GLOBALS["APPLICATION"]->SetCurPage($arResult["NEW_URL"]);
                    }

                }
            }
        }

    }
}