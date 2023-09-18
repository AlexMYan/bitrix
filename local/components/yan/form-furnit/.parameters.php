<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;

if(!Loader::includeModule('iblock'))
    return;

use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

//IBLOCK_TYPE//
$arIBlockType = CIBlockParameters::GetIBlockTypes();

//IBLOCK_ID//
$arIBlock = array();
$rsIBlock = CIBlock::GetList(array('sort' => 'asc'), array('TYPE' => $arCurrentValues['IBLOCK_TYPE'], 'ACTIVE' => 'Y'));
while($arr = $rsIBlock->Fetch()) {
    $arIBlock[$arr['ID']] = '['.$arr['ID'].'] '.$arr['NAME'];
}

$arComponentParameters = array(
    'PARAMETERS' => array(
        'IBLOCK_TYPE' => array(
            'PARENT' => 'BASE',
            'NAME' => Loc::getMessage('FORMS_IBLOCK_TYPE'),
            'TYPE' => 'LIST',
            'VALUES' => $arIBlockType,
            'ADDITIONAL_VALUES' => 'N',
            'REFRESH' => 'Y',
            'MULTIPLE' => 'N',
        ),
        'IBLOCK_ID' => array(
            'PARENT' => 'BASE',
            'NAME' => Loc::getMessage('FORMS_IBLOCK_ID'),
            'TYPE' => 'LIST',
            'VALUES' => $arIBlock,
            'ADDITIONAL_VALUES' => 'Y',
            'REFRESH' => 'Y',
            'MULTIPLE' => 'N',
        ),
        'LINK_PP' => array(
            'PARENT' => 'BASE',
            'NAME' => Loc::getMessage('FORMS_LINK_PP'),
            'TYPE' => 'STRING',
            'VALUES' => "",
            'ADDITIONAL_VALUES' => 'Y',
            'REFRESH' => 'Y',
            'MULTIPLE' => 'N',
        ),
        'CACHE_TIME'  => array(
            'DEFAULT' => 36000000
        )
    )
);?>