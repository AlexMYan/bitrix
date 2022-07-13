<?php
use \Bitrix\Main\EventManager;
use \Bitrix\Sale\Internals\Input\Manager;
use \Bitrix\Main\Localization\Loc;

// Обработка изменения кол-ва товара в каталоге
EventManager::getInstance()->addEventHandler(
    'catalog', 'Bitrix\Catalog\Model\Product::OnBeforeUpdate',
    ['\Custom\QuantitativeAccounting', 'OnBeforeProductUpdateHandler']
);