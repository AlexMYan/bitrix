<?php
use \Bitrix\Main\Loader;
use \Bitrix\Main\Application;
use \Bitrix\Main\Data\Cache;
use Bitrix\Main\File;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

class CGaleryYan extends CBitrixComponent {
    private $_request;

    /**
     * Проверка наличия модулей требуемых для работы компонента
     * @return bool
     * @throws Exception
     */
    private function _checkModules() {


        return true;
    }

    /**
     * Обертка над глобальной переменной
     * @return CAllMain|CMain
     */
    private function _app() {
        global $APPLICATION;
        return $APPLICATION;
    }

    /**
     * Обертка над глобальной переменной
     * @return CAllUser|CUser
     */
    private function _user() {
        global $USER;
        return $USER;
    }

    /**
     * Подготовка параметров компонента
     * @param $arParams
     * @return mixed
     */
    public function onPrepareComponentParams($arParams) {
        // тут пишем логику обработки параметров, дополнение параметрами по умолчанию
        // и прочие нужные вещи

        if (!isset($arParams["CACHE_TIME"])) {
            $arParams["CACHE_TIME"] = 36000000;
        }

        return $arParams;
    }

    public function ResizeImg($url,$width,$height,$flag){

        $pieces = explode("/", $url);

        $file_name=urldecode(end($pieces));

        $origImgPath = $_SERVER['DOCUMENT_ROOT'].urldecode($url);
        $tempFile = $_SERVER['DOCUMENT_ROOT']."/upload/galery/".$flag.$file_name;
        $imgResultPreviewBool=CFile::ResizeImageFile(
            $origImgPath,
            $tempFile,
            array('width'=>$width,'height'=>$height),
            BX_RESIZE_IMAGE_PROPORTIONAL,
            array(),
            false,
            false
        );


        if($imgResultPreviewBool){
            return "/upload/galery/".$flag.$file_name;
        }

        return "";
    }

    /**
     * Точка входа в компонент
     * Должна содержать только последовательность вызовов вспомогательых ф-ий и минимум логики
     * всю логику стараемся разносить по классам и методам
     */
    public function executeComponent() {

        $arrPath=$this->arParams["IMAGE_PATH"];


        //Добавленные картинки
        if($arrPath){
            foreach ($arrPath as $key => $pathUrl){

                if(!empty($pathUrl["SRC"])){
                    $this->arResult["IMAGE_PATH"][$key]=[
                        "PREVIEW"=>$this->ResizeImg($pathUrl["SRC"],56,56,"small_"),
                        "DETAIL"=>$this->ResizeImg($pathUrl["SRC"],1024,1024,"main_")
                    ];
                }
            }
        }

        $this->includeComponentTemplate();

    }
}