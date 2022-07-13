<?php
namespace Extra;

class ProductPrice{
     function getOptimalPrice($productID,$quantity){
         global $USER;
         $renewal="N";
         $arPrice = \CCatalogProduct::GetOptimalPrice($productID, $quantity, $USER->GetUserGroupArray(), $renewal);
         if (!$arPrice || count($arPrice) <= 0)
         {
             if ($nearestQuantity = \CCatalogProduct::GetNearestQuantityPrice($productID, $quantity, $USER->GetUserGroupArray()))
             {
                 $quantity = $nearestQuantity;
                 $arPrice = \CCatalogProduct::GetOptimalPrice($productID, $quantity, $USER->GetUserGroupArray(), $renewal);
             }
         }

         return $arPrice;
     }
}