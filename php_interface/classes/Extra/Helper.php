<?php


namespace Extra;

class Helper
{

    static $cacheIblockIds = []; // [iblockCode => iblockId]

    /**
     * Получение свойства элемента по CODE
     *
     *
     * @param int $orderID
     * @param string $propertyCode
     *
     * @return false|propertyValue
     */
    public function getElementProperty(int $ID, string $propertyCode)
    {
        $resElement = \CIBlockElement::GetList([], ['ID' => $ID], false, false, ["PROPERTY_" . $propertyCode]);

        if ($element = $resElement->getNext()) {
            return $element["PROPERTY_" . $propertyCode . "_VALUE"];
        }

        return false;
    }


    /**
     * Получение множественного свойства элемента по CODE
     *
     *
     * @param int $orderID
     * @param string $propertyCode
     *
     * @return false|propertyValues
     */
    public function getElementPropertyMulti(int $IBlockID, int $ID, string $propertyCode)
    {
        $result = [];

        $resElement = \CIBlockElement::GetProperty($IBlockID, $ID, "sort", "asc", array("CODE" => $propertyCode));
        while ($ob = $resElement->GetNext()) {
            $result[] = $ob;
        }

        if (!empty($result)) {
            return $result;
        }

        return false;
    }

    /**
     * Обновляет свойства элемента по CODE => VALUE
     *
     *
     * @param int $orderID
     * @param array $properties => $value
     *
     * @return true|false
     */
    public function updateElementProperty(int $ID, $properties)
    {
        if (!empty($ID) && !empty($properties)) {
            \CIBlockElement::SetPropertyValuesEx($ID, false, $properties);
            return true;
        }

        return false;
    }

    /**
     * Получение свойства заказа по CODE
     *
     *
     * @param int $orderID
     * @param string $propertyCode
     *
     * @return false|propertyValue
     */
    public function getOrderProperty(int $orderID, string $propertyCode)
    {
        if ($arOrderProps = \CSaleOrderProps::GetList(array(), array("CODE" => $propertyCode))->Fetch()) {
            $db_vals = \CSaleOrderPropsValue::GetList(array(), array("ORDER_ID" => $orderID, "ORDER_PROPS_ID" => $arOrderProps["ID"]));

            if ($arVals = $db_vals->Fetch()) {
                return $arVals["VALUE"];
            }
        }

        return false;
    }


    /**
     * Возвращает ID инфоблока по символьному коду
     *
     * При первом обращении получает все ID инфоюлоков
     * (для того что бы уменьшить кол-во обращений к БД)
     *
     * @param string $iblockCode
     * @return false|mixed
     */
    public function getIblockId(string $iblockCode)
    {
        $result = false;

        if (!empty($id = self::$cacheIblockIds[$iblockCode])) {
            $result = $id;
        } else if (\Bitrix\Main\Loader::includeModule('iblock')) {
            $o = \Bitrix\Iblock\IblockTable::getList(['select' => ['ID', 'CODE']]);
            while ($r = $o->fetch()) {
                if (!empty($code = $r['CODE'])) {
                    self::$cacheIblockIds[$code] = $r['ID'];
                }
            }

            if (!empty($id = self::$cacheIblockIds[$iblockCode])) {
                $result = $id;
            }
        }

        return $result;
    }

    /**
     * Возвращает поле по символьному коду
     *
     *
     * @param int $ID
     * @param string $fieldCode
     *
     * @return false|sectionID
     */
    public function getFieldElement(int $ID, $fieldCode = '')
    {
        $arSelect = array('ID', 'IBLOCK_ID');
        if (!empty($fieldCode)) {
            array_push($arSelect, $fieldCode);
        }
        $arItem = \CIBlockElement::GetList(array(), array('ID' => $ID), false, false, $arSelect)->Fetch();
        if ($arItem) {
            return $arItem[$fieldCode];
        }

        return false;
    }


    /**
     * Возвращает поля  раздел , либо если указать символьные кода то только их
     *
     *
     * @param int $sectionID
     * @param array $arPropertyCode
     *
     * @return false|section
     */
    public function getSection(int $sectionID, array $arPropertyCode = array())
    {
        $res = \CIBlockSection::GetByID($sectionID);
        if ($arRes = $res->Fetch()) {
            if (!empty($arPropertyCode)) {
                $result = [];
                $result["ID"] = $arRes["ID"];
                foreach ($arPropertyCode as $propCode)
                    if (array_key_exists($propCode, $arRes))
                        $result[$propCode] = $arRes[$propCode];


                return $result;
            } else {
                return $arRes;
            }
        }
        return false;
    }

    /**
     * Возвращает кол-во элементов по фильтру
     *
     *
     * @param array $arFilter
     *
     * @return int
     */
    public function getCountElemetsInFilter($arFilter)
    {
        return \CIBlockElement::GetList(array(), $arFilter, array());

    }


    /**
     * Возвращает элементы по фильтру
     *
     *
     * @param array $arFilter
     *
     * @return int
     */
    public function getElementsID($arFilter,$arPagination)
    {
        $arrID=[];
        $res= \CIBlockElement::GetList(array(), $arFilter, false,$arPagination);
        while($ar_fields = $res->GetNext()){
            $arrID[]=$ar_fields["ID"];
        }

        return $arrID;
    }

    /**
     * @param $IBlockы
     * @param $arrIds
     * @param $arSelect
     * @return array
     */
    public function getElementsPropertys($IBlock,$arrIds,$arSelect){
        $arProps=[];

        $arSelect=!empty($arSelect)?$arSelect:Array("IBLOCK_ID", "ID", "NAME");

        $arFilter = Array("IBLOCK_ID"=>$IBlock, "ID"=>$arrIds, "ACTIVE"=>"Y");
        $res = \CIBlockElement::GetList(Array(), $arFilter, false, Array(), $arSelect);
        while($ob = $res->GetNext())
        {
            $arProps[] = $ob;

        }
        return $arProps;
    }

    /**
     * По символьному коду ID раздела
     *
     * @param $IDIblock
     * @param $code
     * @return false|mixed
     */
    public function getSectionID($IDIblock, $code) {
        $rsSections = CIBlockSection::GetList(array(), array('IBLOCK_ID' => $IDIblock, '=CODE' => $code, false, array("ID")));
        if ($arSection = $rsSections->Fetch()) {
            return $arSection['ID'];
        }

        return false;
    }

    /**
     * Обновляем свойсвто типа список
     *
     * @param $productId
     * @param $IDIblock
     * @param $propertyCode
     * @param $propetyId
     */
    public function updateElementPropertyTypeList($productId,$IDIblock,$propertyCode, $propetyId ){
        \CIblockElement::SetPropertyValuesEx($productId,$IDIblock, [$propertyCode => $propetyId]);
    }


    /**
     * Возвращает  элементы по фильтру
     *
     *
     * @param array $arFilter
     *
     * @return int
     */
    public static function getElements($arFilter,$arSelect)
    {
        $arr=[];
        $result= \CIBlockElement::GetList(array(), $arFilter, false,array(),$arSelect);
        while ($res = $result->Fetch()) {

            $arr=$res;
        }

        return $arr;
    }

    /**
     * Доступное кол-во
     *
     * @param $id
     * @return false|mixed
     */
    public function getCatalogProduct($id){
        //Доступное количество
        $arQuantity = Bitrix\Catalog\Model\Product::getList([
            'filter' => array('ID' => $id),
        ])->Fetch();
        $value = "";
        if (!is_null($arQuantity['QUANTITY']) && $arQuantity['QUANTITY'] != 0) {
           return  $arQuantity['QUANTITY'];
        }

        return false;
    }

}
