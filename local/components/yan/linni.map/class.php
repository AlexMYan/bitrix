<?php

use \Bitrix\Main\Loader;
use \Bitrix\Main\Application;
use \Bitrix\Main\Data\Cache;


if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

class CPickupYan extends CBitrixComponent
{


    /**
     * Проверка наличия модулей требуемых для работы компонента
     * @return bool
     * @throws Exception
     */
    private function checkModules()
    {

        // если модуль не подключен
        if (!Loader::includeModule('iblock'))
            // выводим сообщение в catch
            throw new SystemException(Loc::getMessage('IBLOCK_MODULE_NOT_INSTALLED'));
    }


    /**
     * Подготовка параметров компонента
     * @param $arParams
     * @return mixed
     */
    public function onPrepareComponentParams($arParams)
    {
        // тут пишем логику обработки параметров, дополнение параметрами по умолчанию
        // и прочие нужные вещи

        if (!isset($arParams["CACHE_TIME"]))
            $arParams["CACHE_TIME"] = 36000000;

        $arParams["IBLOCK_ID"] = intval($arParams["IBLOCK_ID"]);
        if ($arParams["IBLOCK_ID"] <= 0)
            return;

        if (!empty($arParams["YANDEX_API_KEY"])) {
            global $APPLICATION;
            $APPLICATION->AddHeadScript('https://api-maps.yandex.ru/2.1/?apikey=' . $arParams["YANDEX_API_KEY"] . '&lang=ru_RU');

        }

        if(empty($arParams["MAP_COORDS_CENTER"]))
            $arParams["MAP_COORDS_CENTER"]="60.612277, 87.891715";

        if(empty($arParams["MAP_ZOOM_CENTER"]))
            $arParams["MAP_ZOOM_CENTER"]=3;

        return $arParams;
    }


    /**
     * Выполняет основной код компонента, аналог конструктора (метод подключается автоматически)
     * Точка входа в компонент
     * Должна содержать только последовательность вызовов вспомогательых ф-ий и минимум логики
     * всю логику стараемся разносить по классам и методам
     */
    public function executeComponent()
    {

        try {
            // подключаем метод проверки подключения модуля «Информационные блоки»
            $this->checkModules();
            // подключаем метод подготовки массива $arResult
            $this->getResult();
        } catch (SystemException $e) {
            ShowError($e->getMessage());
        }
    }

    /**
     * подготовка массива $arResult (метод подключается внутри класса try...catch)
     * @return void
     */
    protected function getResult()
    {
        // если нет валидного кеша, получаем данные из БД
        if ($this->startResultCache()) {
            // Запрос к инфоблоку через класс ORM

            $arSelect = [
                "ID",
                "NAME",
                "SUB",
                "IBLOCK_SECTION_ID",
                "IBLOCK_ID",
                "COORDS_" => "COORDS",
                "METKA_" => "METKA",
                "ADRESS_" => "ADRESS",

            ];

            $arFilter = ["ACTIVE" => "Y"];

            $this->arResult["ITEMS"] = $this->getDataValues($arSelect, $arFilter);

            // кэш не затронет весь код ниже, он будут выполняться на каждом хите, здесь работаем с другим $arResult, будут доступны только те ключи массива, которые перечислены в вызове SetResultCacheKeys()
            if (isset($this->arResult)) {
                // ключи $arResult перечисленные при вызове этого метода, будут доступны в component_epilog.php и ниже по коду, обратите внимание там будет другой $arResult
                $this->SetResultCacheKeys(
                    array()
                );
                // подключаем шаблон и сохраняем кеш
                $this->IncludeComponentTemplate();
            } else { // если выяснилось что кешировать данные не требуется, прерываем кеширование и выдаем сообщение «Страница не найдена»
                $this->AbortResultCache();
                \Bitrix\Iblock\Component\Tools::process404(
                    Loc::getMessage('PAGE_NOT_FOUND'),
                    true,
                    true
                );
            }
        }
    }

    /**
     * Запрос к инфоблоку через класс ORM
     *
     * @return array
     */
    public function getDataValues($arSelect, $arFilter)
    {

        $arResult = [];

        $res = \Bitrix\Iblock\Elements\ElementWherebuyTable::getList([
            'select' => $arSelect,
            "filter" => $arFilter,
            "order" => ["SORT" => "ASC"],
            'runtime'=>[
                new \Bitrix\Main\Entity\ReferenceField(
                    'SUB',
                    \Bitrix\Iblock\Section::class,
                    ['=this.IBLOCK_SECTION_ID' => 'ref.ID'],
                    ['join_type'=>'left'],
                ),

            ]
        ])->fetchCollection();

        // Формируем массив arResult
        $arResult = [];
        foreach ($res as $element) {

            //NAME
            $strName=$element->getName();

             //ADRESS
             $strAdress="";
             if($element->getAdress()){
                 $strAdress=$element->getAdress()->getValue();
                 $strAdress=unserialize($strAdress)["TEXT"];
             }
            //COORFDINATES
             $strCoorfdinates="";
             if($element->getCoords()){
                 $strCoorfdinates=$element->getCoords()->getValue();
             }
             //METKA
             $strMetka=0;
             if($element->getMetka()){

                 $strMetka=$element->getMetka()->getValue();

                 if($strMetka>0){
                     $arFilter=[
                         "IBLOCK_ID" =>$element->get("IBLOCK_ID"),
                         "CODE" => "METKA"
                     ];
                     $strMetka= $this->getPropEnum($arFilter,$strMetka);

                 }
             }
            // Формируем массив arResult
            $arResult[] = [
                "ID" => $element->getId(),
                "METKA" => $strMetka,
                "ADRESS" => $strAdress,
                "COORDS" => $strCoorfdinates,
                "NAME"=>$strName,
                "COUNTRY"=>[$element->get("IBLOCK_SECTION_ID")=>$element->get("SUB")->getName()]
            ];
        }

        return $arResult;
    }

    public function getPropEnum($arFilter, $value)
    {
        $arrProp = [];
        $property_enums = CIBlockPropertyEnum::GetList(array("DEF" => "DESC", "SORT" => "ASC"), $arFilter);

        while ($enum_fields = $property_enums->GetNext()) {
            if ($enum_fields["ID"] == $value) {
                return $enum_fields["VALUE"];
            }
        }

        return false;
    }

}