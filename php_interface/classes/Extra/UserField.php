<?php
namespace Extra;

class UserField{


    /**
     * Значение одного свойства
     *
     * @param $arUFieldCode  - символьный код
     */
    public function getField($arUFieldCode, $spisok=false)
    {
        if(self::getUserId()){
            $resUserFields = \CUser::GetList(
                $by = "ID",
                $order = "asc",
                array("ID_EQUAL_EXACT" =>self::getUserId()),
                array(
                    "SELECT" => array($arUFieldCode),
                    "FIELDS" => array("ID"),
                )
            )->fetch();

            if($spisok && $resUserFields[$arUFieldCode]>0){
                $arrRes = \CUserFieldEnum::GetList(array(), array(
                    "ID" =>$resUserFields[$arUFieldCode],
                ));
                if ($arrItem = $arrRes->GetNext()){
                    return $arrItem["VALUE"];
                }

                return false;
            }

            return $resUserFields[$arUFieldCode];
        }

        return false;

    }

    /**
     * Значение
     *
     * @param $arUFields  - массив пользовательских свойств
     * @param $arFields  - массив свойств
     */
    public function getFields($arUFields, $arFields)
    {
        if(self::getUserId()) {
            $resUserFields = \CUser::GetList(
                $by = "ID",
                $order = "asc",
                array("ID_EQUAL_EXACT" => self::getUserId()),
                array(
                    "SELECT" => $arUFields,
                    "FIELDS" => $arFields,
                )
            )->fetch();

            return $resUserFields;
        }

        return false;

    }


    public function getUserId()
    {
        global $USER;
        return  $USER->GetID();

    }
}