<?
use Bitrix\Main\EventManager;

Bitrix\Main\Loader::registerAutoLoadClasses(null, [
    'Extra\Helper' => '/local/php_interface/classes/Extra/Helper.php',
    'Extra\UserField' => '/local/php_interface/classes/Extra/UserField.php',
    'Extra\ProductPrice' => '/local/php_interface/classes/Extra/ProductPrice.php',
    'Extra\BitrixUser' => '/local/php_interface/classes/Extra/BitrixUser.php',
    'Custom\CrmCompany' => '/local/php_interface/classes/Custom/CrmCompany.php',
    'Custom\QuantitativeAccounting' => '/local/php_interface/classes/Custom/QuantitativeAccounting.php',

    "ElementWithDescription" => "/local/props/elementWithDescription/ElementWithDescription.php",
    "CUserTypeIBlockElementList" => "/local/props/CUserTypeIBlockElementList/CUserTypeIBlockElementList.php",

]);


?>