<?php

namespace Custom;

class QuantitativeAccounting
{
    static $producIBlockId =2;

    ########[ HANDLERS ]###################################################################

    /**
     * ОБрабатываем изменение кол-ва товаров
     *
     * @param \Bitrix\Main\Event $event
     * @return \Bitrix\Main\Entity\EventResult
     */
    function OnBeforeProductUpdateHandler(\Bitrix\Main\Event $event)
    {

        $arParams = $event->getParameters();
        $obResult = new \Bitrix\Main\Entity\EventResult();
        $productQuantity = $arParams['fields']['QUANTITY'];

        if($productQuantity > 0){
            if(!empty($productId = $arParams['id'])){

                $arrPropValue=\Extra\Helper::getElementPropertyMulti(self::$producIBlockId, $productId, "CML2_TRAITS");
                $resultQuantity=0;
                if($arrPropValue){
                    foreach($arrPropValue as $value){
                        if($value["DESCRIPTION"]=="Не снижаемый остаток"){
                            if($value["VALUE"]>0){
                                $storeSum=self::getCatalogStoreProductAmountSum($productId);
                                $resultQuantity=$storeSum-$value["VALUE"];
                            }
                        }
                    }
                }

                $arFields['fields']['QUANTITY'] = $resultQuantity;
                $obResult->modifyFields($arFields);
            }
        }

        return $obResult;
    }


    ########[ HELPERS ]###################################################################


    /**
     * Достаем кол-во товара
     *
     * @param $productIds
     * @return array [productId => quantity]
     */
    function getCatalogProductsQuantity($productIds): array
    {
        $result = [];

        if(!empty($productIds)){
            $o = \Bitrix\Sale\ProductTable::getList([
                'filter' => ['ID' => $productIds],
                'select' => ['ID', 'QUANTITY'],
                'limit' => count(array_unique($productIds))
            ]);
            while ($r = $o->fetch()){
                $result[$r['ID']] = $r['QUANTITY'];
            }
        }

        return $result;
    }


    /**
     * Достаем сумму со всех складов
     *
     * @param $productIds
     * @return int
     */
    function getCatalogStoreProductAmountSum($productIds):int
    {
        $result = 0;
        if(!empty($productIds)){
            $rsStoreProduct = \Bitrix\Catalog\StoreProductTable::getList(array(
                'filter' => ['=PRODUCT_ID'=>$productIds,'STORE.ACTIVE'=>'Y'],
                'select' => ['ID', 'AMOUNT'],

            ));
            while($arStoreProduct=$rsStoreProduct->fetch()){
                if($arStoreProduct>0){
                    $result+=$arStoreProduct["AMOUNT"];
                }
            }
        }

        return $result;
    }

    /**
     * @param $productIds
     * @param $propertyCode
     * @return int
     */
    function getProductProperty($productIds,$propertyCode){

        $catalogIblockId = \Extra\Helper::getElementPropertyMulti(self::$producIBlockId, $productIds, $propertyCode);

        return $catalogIblockId;
    }

}