<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;

if (!Loader::includeModule('iblock'))
    return;

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

//IBLOCK_TYPE//
$arIBlockType = CIBlockParameters::GetIBlockTypes();

//IBLOCK_ID//
$arIBlock = array();
$rsIBlock = CIBlock::GetList(array('sort' => 'asc'), array('TYPE' => $arCurrentValues['IBLOCK_TYPE'], 'ACTIVE' => 'Y'));
while ($arr = $rsIBlock->Fetch()) {
    $arIBlock[$arr['ID']] = '[' . $arr['ID'] . '] ' . $arr['NAME'];
}
//EVENT
$dbl = CEventType::GetList();
$eventName = [0 => "Не высылать"];
while ($res = $dbl->fetch()) {
    $eventName[$res["EVENT_NAME"]] = "[{$res["EVENT_NAME"]}] {$res["NAME"]}";
}

//PROPS
$fields = [];

if (!empty($arCurrentValues["IBLOCK_ID"])) {
    $propsInfo = [];
    $dbl = CIBlockProperty::GetList([], ["IBLOCK_ID" => $arCurrentValues["IBLOCK_ID"]]);
    while ($res = $dbl->Fetch()) {

        if (!in_array($res["PROPERTY_TYPE"], ["S", "N", "E", "F"])) {
            continue;
        }
        $fields[$res["CODE"]] = (!empty($res["NAME"]) ? $res["NAME"] :$res["CODE"] );
    }
}

$arComponentParameters = array(
    "GROUPS" => array(
        "ITEM_GROUP_1" => array(
            "NAME" => Loc::getMessage('ITEM_GROUP_1'),
            "SORT" => "300",
        ),
        "ITEM_GROUP_2" => array(
            "NAME" => Loc::getMessage('ITEM_GROUP_2'),
            "SORT" => "400",
        ),
        "ITEM_GROUP_3" => array(
            "NAME" => Loc::getMessage('ITEM_GROUP_3'),
            "SORT" => "500",
        ),
    ),

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
        'LEFT_TITLE' => array(
            'PARENT' => 'ITEM_GROUP_1',
            'NAME' => Loc::getMessage('LEFT_TITLE'),
            'TYPE' => 'STRING',
            'REFRESH' => 'Y',
        ),
        'LEFT_TEXT' => array(
            'PARENT' => 'ITEM_GROUP_1',
            'NAME' => Loc::getMessage('LEFT_TEXT'),
            'TYPE' => 'STRING',
            'REFRESH' => 'Y',
        ),
        'LEFT_BG_COLOR' => array(
            'PARENT' => 'ITEM_GROUP_1',
            'NAME' => Loc::getMessage('LEFT_BG_COLOR'),
            'TYPE' => 'STRING',
            'REFRESH' => 'Y',
        ),
        "LEFT_FILE" => array(
            "PARENT" => "ITEM_GROUP_1",
            "NAME" => Loc::getMessage('LEFT_FILE'),
            "TYPE" => "FILE",
            "DEFAULT" => "",
            "REFRESH" => "Y",
        ),
        'FORM_TITLE' => array(
            'PARENT' => 'ITEM_GROUP_2',
            'NAME' => Loc::getMessage('FORM_TITLE'),
            'TYPE' => 'STRING',
            'REFRESH' => 'Y',
        ),
        "FORM_EVENT_NAME" => array(
            "PARENT" => "ITEM_GROUP_2",
            "NAME" => Loc::getMessage('FORM_EVENT_NAME'),
            "TYPE" => "LIST",
            "VALUES" => $eventName,
            "DEFAULT" => "",
            "REFRESH" => "Y",
        ),
        "FORM_FIELDS"  => array(
            "PARENT"   => "ITEM_GROUP_2",
            "NAME"     =>  Loc::getMessage('FORM_FIELDS'),
            "TYPE"     => "LIST",
            "VALUES"   => $fields,
            "DEFAULT"  => "",
            "COLS"     => 50,
            "MULTIPLE" => "Y",
        ),
        "FORM_FIELDS_REQUIRED"  => array(
            "PARENT"   => "ITEM_GROUP_2",
            "NAME"     =>  Loc::getMessage('FORM_FIELDS_REQUIRED'),
            "TYPE"     => "LIST",
            "VALUES"   => $fields,
            "DEFAULT"  => "",
            "COLS"     => 50,
            "MULTIPLE" => "Y",
        ),
        'FORM_BTN_TITLE' => array(
            'PARENT' => 'ITEM_GROUP_2',
            'NAME' => Loc::getMessage('FORM_BTN_TITLE'),
            'TYPE' => 'STRING',
            'REFRESH' => 'Y',
        ),
        //answer
        'FORM_SUCCESS_TITLE' => array(
            'PARENT' => 'ITEM_GROUP_3',
            'NAME' => Loc::getMessage('FORM_SUCCESS_TITLE'),
            'TYPE' => 'STRING',
            'REFRESH' => 'Y',
            "DEFAULT"  =>  Loc::getMessage('FORM_SUCCESS_TITLE_DEF'),
        ),
        'FORM_SUCCESS_TEXT' => array(
            'PARENT' => 'ITEM_GROUP_3',
            'NAME' => Loc::getMessage('FORM_SUCCESS_TEXT'),
            'TYPE' => 'STRING',
            'REFRESH' => 'Y',
            "DEFAULT"=>Loc::getMessage('FORM_SUCCESS_TEXT_DEF'),
        ),
        'FORM_SUCCESS_WRITE' => array(
            'PARENT' => 'ITEM_GROUP_3',
            'NAME' => Loc::getMessage('FORM_SUCCESS_WRITE'),
            'TYPE' => 'STRING',
            'REFRESH' => 'Y',
            "DEFAULT"  =>  Loc::getMessage('FORM_SUCCESS_WRITE_DEF'),
        ),
        'FORM_SUCCESS_GO' => array(
            'PARENT' => 'ITEM_GROUP_3',
            'NAME' => Loc::getMessage('FORM_SUCCESS_GO'),
            'TYPE' => 'STRING',
            'REFRESH' => 'Y',
            "DEFAULT"  =>  Loc::getMessage('FORM_SUCCESS_GO_DEF'),
        ),
        'FORM_SUCCESS_CALL' => array(
            'PARENT' => 'ITEM_GROUP_3',
            'NAME' => Loc::getMessage('FORM_SUCCESS_CALL'),
            'TYPE' => 'STRING',
            'REFRESH' => 'Y',
            "DEFAULT"  =>  Loc::getMessage('FORM_SUCCESS_CALL_DEF'),
        ),

        'CACHE_TIME' => array(
            'DEFAULT' => 36000000
        )
    )
); ?>