<?php
use \Bitrix\Main\Loader;
use \Bitrix\Main\Application;
use \Bitrix\Main\Data\Cache;


if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

class CPickupYan extends CBitrixComponent {


    /**
     * Проверка наличия модулей требуемых для работы компонента
     * @return bool
     * @throws Exception
     */
    private function checkModules() {

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
    public function onPrepareComponentParams($arParams) {
        // тут пишем логику обработки параметров, дополнение параметрами по умолчанию
        // и прочие нужные вещи

        if(!isset($arParams["CACHE_TIME"]))
            $arParams["CACHE_TIME"] = 36000000;

        $arParams["IBLOCK_ID"] = intval($arParams["IBLOCK_ID"]);
        if($arParams["IBLOCK_ID"] <= 0)
            return;


       // $arParams["YANDEX_API_KEY"]="a9cbfea9-a798-466b-8ce4-ba72588c9dad";
        $arParams["YANDEX_API_KEY"]="3fbf19f4-1be9-4716-a272-fde43aca6efd";
        if(!empty($arParams["YANDEX_API_KEY"])){
            global $APPLICATION;
            $APPLICATION->AddHeadScript('https://api-maps.yandex.ru/2.1/?apikey='.$arParams["YANDEX_API_KEY"].'&lang=ru_RU');

        }


        return $arParams;
    }



    /**
     * Выполняет основной код компонента, аналог конструктора (метод подключается автоматически)
     * Точка входа в компонент
     * Должна содержать только последовательность вызовов вспомогательых ф-ий и минимум логики
     * всю логику стараемся разносить по классам и методам
     */
    public function executeComponent() {

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
    protected function getResult(){
         // если нет валидного кеша, получаем данные из БД
        if ($this->startResultCache()) {
            // Запрос к инфоблоку через класс ORM

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

            $arFilter=["ACTIVE" => "Y"];

            $this->arResult["ITEMS"]=  Extra\Helper::getDataValues($arSelect,$arFilter);

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

}