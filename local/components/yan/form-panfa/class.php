<?php
\Bitrix\Main\Loader::includeModule("iblock");

use Bitrix\Main\Localization\Loc;
class CYanFormComponent extends CBitrixComponent
{

    public function onPrepareComponentParams($arParams)
    {

        $arParams['AJAX_ID'] = CAjax::GetComponentID($this->__name, $this->__template->__name, $this->arParams['AJAX_OPTION_ADDITIONAL']);
        return $arParams;
    }

    public function executeComponent()
    {
        //Флаг успешной отправки
        $this->arResult["FORM_SUCCESS"]=false;

        $this->getFormFields();

        //Обработка формы ajax
        if ($_REQUEST["AJAX_CALL"] == "Y" && $_REQUEST["bxajaxid"] == $this->arParams['AJAX_ID']) {
            $this->ajaxAction();
        }

        $this->IncludeComponentTemplate();
    }

    protected function ajaxAction(){
        //Если нажата кнопка
        if($_REQUEST["ACTION"]=="ADD"){
             $this->addUsersIblockEvent();
        }

        global $APPLICATION;
        $APPLICATION->RestartBuffer();
        if($this->arResult["FORM_SUCCESS"]){
            $this->IncludeComponentTemplate("success");
        }
        die();
    }

    /**
     * Получаем поля формы из свойст инфоблока
     *
     * @return void
     */
    public function getFormFields()
    {
        if ($this->arParams["IBLOCK_ID"] > 0) {
            //IBLOCK_PROPS//
            $rsProps = CIBlock::GetProperties($this->arParams["IBLOCK_ID"], array("SORT" => "ASC", "NAME" => "ASC"), array("ACTIVE" => "Y", "PROPERTY_TYPE" => "S"));
            while ($arProp = $rsProps->fetch()) {
                //Если такие поля в настройках
                if(in_array($arProp["CODE"],$this->arParams["FORM_FIELDS"])){
                    //обязательные
                    if(in_array($arProp["CODE"],$this->arParams["FORM_FIELDS_REQUIRED"])){
                        $arProp["IS_REQUIRED"]="Y";
                    }
                    $this->arResult["IBLOCK"]["PROPERTIES"][] = $arProp;
                }
            }
            unset($arProp, $rsProps);
        }
    }

    /**
     * Добавляем запись в инфоблок
     *
     * @return void
     */
    protected function addUsersIblockEvent(){

        $arProps=[];
        foreach($this->arResult["IBLOCK"]["PROPERTIES"] as $arProp){
            $arProps[$arProp["CODE"]] = $_REQUEST[$arProp["CODE"]];
        }

        //NEW_ELEMENT//
       $el = new CIBlockElement;

        $arFields = array(
            "IBLOCK_ID" => $this->arParams["IBLOCK_ID"],
            "ACTIVE" => "Y",
            "NAME" => Loc::getMessage("IBLOCK_ELEMENT_NAME").ConvertTimeStamp(time(), "FULL"),
            "PROPERTY_VALUES" => !empty($arProps) ? $arProps : array(),
        );

        if($el->Add($arFields)) {

            $this->arResult["FORM_SUCCESS"]=true;
            //Если создан элемент то отправляем письмо Админу
          //  $this->sendEmail($arProps);
            //Если создан элемент то отправляем письмо Юзеру
           // $this->sendEmailUser($arProps);
        }
    }

    /**
     * Отправка письма юзеру
     *
     * @param $arProps
     * @return void
     */
    protected function sendEmailUser($arProps=array()){

        $siteId=SITE_ID;
        //Данные по инфоблоку
        $iblock=\Extra\Helper::GetIBlockFields($this->arParams["IBLOCK_ID"]);
        //Массив свойство для почты
        $arMailProps=$arProps;
        $arMailProps["FORM_NAME"] = $iblock["NAME"];
        $arMailProps["EMAIL_TO"] =$arProps["USER_EMAIL"];

        //MAIL_EVENT//
        $eventName = "YAN_FORM_FOR_USER_".$iblock["IBLOCK_TYPE_ID"]."_".$iblock["CODE"];

        $eventDesc = Loc::getMessage("MAIL_EVENT_DESCRIPTION");

        //MAIL_EVENT_TYPE//
        $arEvent = CEventType::GetByID($eventName, LANGUAGE_ID)->Fetch();

        if(empty($arEvent)) {
            $et = new CEventType;
            $arEventFields = array(
                "LID" => LANGUAGE_ID,
                "EVENT_NAME" => $eventName,
                "NAME" => Loc::getMessage("MAIL_EVENT_TYPE_NAME")." \"".$iblock["NAME"]."\"",
                "DESCRIPTION" => $eventDesc
            );
            $et->Add($arEventFields);
        }

        //MAIL_EVENT_MESSAGE//
        $arMess = CEventMessage::GetList($by = "site_id", $order = "desc", array("TYPE_ID" => $eventName))->Fetch();

        if(empty($arMess)) {
            $em = new CEventMessage;
            $arMess = array();
            $arMess["ID"] = $em->Add(
                array(
                    "ACTIVE" => "Y",
                    "EVENT_NAME" => $eventName,
                    "LID" => $siteId,
                    "EMAIL_FROM" => "#DEFAULT_EMAIL_FROM#",
                    "EMAIL_TO" => !empty($arMailProps["USER_EMAIL"]) ? "#USER_EMAIL#" : "#EMAIL_TO#",
                    "BCC" => "",
                    "SUBJECT" => Loc::getMessage("MAIL_EVENT_MESSAGE_SUBJECT"),
                    "BODY_TYPE" => "html",
                    "MESSAGE" => Loc::getMessage("MAIL_EVENT_MESSAGE_FOOTER")
                )
            );
        }

        //SEND_MAIL//
        Bitrix\Main\Mail\Event::send(array(
            "EVENT_NAME" => $eventName,
            "LID" => $siteId,
            "C_FIELDS" => !empty($arMailProps) ? $arMailProps : array(),
        ));



    }


    /**
     * Отправляем письмо Админу
     *
     * @param $arProps
     * @return void
     */
    protected function sendEmail($arProps=array()){

        $siteId=SITE_ID;
        //Данные по инфоблоку
        $iblock=\Extra\Helper::GetIBlockFields($this->arParams["IBLOCK_ID"]);
        //Массив свойство для почты
        $arMailProps=$arProps;
        $arMailProps["FORM_NAME"] = $iblock["NAME"];
        $arMailProps["EMAIL_TO"] =\Bitrix\Main\Config\Option::get("main", "email_from");

        //MAIL_EVENT//
        $eventName = "YAN_FORM_".$iblock["IBLOCK_TYPE_ID"]."_".$iblock["CODE"];

        $eventDesc = Loc::getMessage("MAIL_EVENT_DESCRIPTION");

        //MAIL_EVENT_TYPE//
        $arEvent = CEventType::GetByID($eventName, LANGUAGE_ID)->Fetch();

        if(empty($arEvent)) {
            $et = new CEventType;
            $arEventFields = array(
                "LID" => LANGUAGE_ID,
                "EVENT_NAME" => $eventName,
                "NAME" => Loc::getMessage("MAIL_EVENT_TYPE_NAME")." \"".$iblock["NAME"]."\"",
                "DESCRIPTION" => $eventDesc
            );
            $et->Add($arEventFields);
        }

        //MAIL_EVENT_MESSAGE//
        $arMess = CEventMessage::GetList($by = "site_id", $order = "desc", array("TYPE_ID" => $eventName))->Fetch();

        if(empty($arMess)) {
            $em = new CEventMessage;
            $arMess = array();
            $arMess["ID"] = $em->Add(
                array(
                    "ACTIVE" => "Y",
                    "EVENT_NAME" => $eventName,
                    "LID" => $siteId,
                    "EMAIL_FROM" => "#DEFAULT_EMAIL_FROM#",
                    "EMAIL_TO" => !empty($arMailProps["EMAIL_EMAIL"]) ? "#EMAIL_EMAIL#" : "#EMAIL_TO#",
                    "BCC" => "",
                    "SUBJECT" => Loc::getMessage("MAIL_EVENT_MESSAGE_SUBJECT"),
                    "BODY_TYPE" => "html",
                    "MESSAGE" => Loc::getMessage("MAIL_EVENT_MESSAGE_FOOTER")
                )
            );
        }

        //SEND_MAIL//
        Bitrix\Main\Mail\Event::send(array(
            "EVENT_NAME" => $eventName,
            "LID" => $siteId,
            "C_FIELDS" => !empty($arMailProps) ? $arMailProps : array(),
        ));



    }
}