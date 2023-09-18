<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

if (!Loader::includeModule('iblock'))
    return;

$arrTempatesCode = [
    "block1",
    "block2",
    "block3",
    "block4",
    "block5",
    "block6",
    "block7",
    "block8",
];

//IBLOCK_TYPE//
$arIBlockType = CIBlockParameters::GetIBlockTypes();

$groupList = [];

$dbl = \Bitrix\Main\GroupTable::query()
    ->setFilter(array("!=ID" => [1, 2]))
    ->setSelect(["ID", "NAME"])
    ->exec();

while ($res = $dbl->fetch()) {
    $groupList[$res["ID"]] = $res["NAME"];
}

//EVENT
$dbl = CEventType::GetList();
$eventName = [0 => "Не высылать"];
while ($res = $dbl->fetch()) {
    $eventName[$res["EVENT_NAME"]] = "[{$res["EVENT_NAME"]}] {$res["NAME"]}";
}


$arComponentParameters = [
    "GROUPS" => [
        "SETTINGS" => [
            "NAME" => Loc::getMessage('GROUPS_TITLE'),
            "SORT" => 10,
        ],
    ],
    "PARAMETERS" => [
        "ITEM_LIST" => [
            'NAME' => Loc::getMessage('NAME_POSITION'),
            'TYPE' => 'TEXT',
            "PARENT" => 'SETTINGS',
            'MULTIPLE' => 'Y',
            'REFRESH' => 'Y',
        ],
    ],
];

$arCurrentValues["ITEM_LIST"] = array_filter($arCurrentValues["ITEM_LIST"], function ($test) {
    return strlen(trim($test));
});

foreach ($arCurrentValues["ITEM_LIST"] as $k => $adv) {

    $arComponentParameters['GROUPS']["ITEM_GROUP_$k"] = ['NAME' => $adv, "SORT" => ($arCurrentValues["ITEM_PARAMS_SORT_{$k}"] ? 600 + $arCurrentValues["ITEM_PARAMS_SORT_{$k}"] : 600)];

    $paramsList = [];
    //IBLOCK_ID//
    $arIBlock = array();
    $codeIBlock = "";
    $rsIBlock = CIBlock::GetList(
        array('sort' => 'asc'),
        array('TYPE' => $arCurrentValues["ITEM_IBLOCK_TYPE_{$k}"], 'ACTIVE' => 'Y')
    );
    while ($arr = $rsIBlock->Fetch()) {

        if(isset($arCurrentValues["ITEM_IBLOCK_ID_{$k}"])){

            if($arr['ID']==$arCurrentValues["ITEM_IBLOCK_ID_{$k}"]){
                $codeIBlock = $arr['CODE'];
            }
           ;
        }

        $arIBlock[$arr['ID']] = '[' . $arr['ID'] . '] ' . $arr['NAME'];
    }



//PROPS
    $fields = [];

    if (!empty($arCurrentValues["ITEM_IBLOCK_ID_{$k}"])) {
        $propsInfo = [];
        $dbl = CIBlockProperty::GetList([], ["IBLOCK_ID" => $arCurrentValues["ITEM_IBLOCK_ID_{$k}"]]);
        while ($res = $dbl->Fetch()) {

            if (!in_array($res["PROPERTY_TYPE"], ["S", "N", "E", "F"])) {
                continue;
            }
            $fields[$res["CODE"]] = (!empty($res["NAME"]) ? $res["NAME"] :$res["CODE"] );
        }
    }


    $paramsList["ITEM_PARAMS_ACTIVE_{$k}"] = [
        'PARENT' => "ITEM_GROUP_{$k}",
        "NAME" => Loc::getMessage('ITEM_PARAMS_ACTIVE'),
        "TYPE" => "CHECKBOX",
        "SORT" => 100,
        "DEFAULT" => "Y",
    ];
    $paramsList["ITEM_PARAMS_HR_{$k}"] = [
        'PARENT' => "ITEM_GROUP_{$k}",
        "NAME" => Loc::getMessage('ITEM_PARAMS_HR'),
        "TYPE" => "CHECKBOX",
        "SORT" => 100,
        'REFRESH' => 'Y',
        "DEFAULT" => "N",
    ];

    if($arCurrentValues["ITEM_PARAMS_HR_{$k}"]=="Y"){

        $paramsList["ITEM_PARAMS_HR_BG_COLOR_{$k}"] = [
            'PARENT' => "ITEM_GROUP_{$k}",
            "NAME" => Loc::getMessage('ITEM_PARAMS_HR_BG_COLOR'),
            "TYPE" => "TEXT",
            "SORT" =>100,
            "DEFAULT"  =>  Loc::getMessage('ITEM_PARAMS_HR_BG_COLOR_DEF'),
        ];
    }



    $paramsList["ITEM_PARAMS_SORT_{$k}"] = [
        'PARENT' => "ITEM_GROUP_{$k}",
        "NAME" => Loc::getMessage('ITEM_PARAMS_SORT'),
        "TYPE" => "TEXT",
        "DEFAULT" => "10",
        "SORT" => 200,
    ];

    $paramsList["ITEM_IBLOCK_TYPE_{$k}"] = [
        'PARENT' => "ITEM_GROUP_{$k}",
        "NAME" => Loc::getMessage('ITEM_IBLOCK_TYPE'),
        "TYPE" => "LIST",
        "SORT" => 200,
        'VALUES' => $arIBlockType,
        'ADDITIONAL_VALUES' => 'N',
        'REFRESH' => 'Y',
        'MULTIPLE' => 'N',
    ];

    $paramsList["ITEM_IBLOCK_ID_{$k}"] = [
        'PARENT' => "ITEM_GROUP_{$k}",
        "NAME" => Loc::getMessage('ITEM_IBLOCK_ID'),
        "TYPE" => "LIST",
        "SORT" => 200,
        'VALUES' => $arIBlock,
        'ADDITIONAL_VALUES' => 'N',
        'REFRESH' => 'Y',
        'MULTIPLE' => 'N',
    ];

    if (in_array($codeIBlock, $arrTempatesCode)) {
       switch ($codeIBlock) {
            case ("block1"):
                $paramsList["ITEM_PARAMS_BLOCK1_ID_ELEMENT_{$k}"] = [
                    'PARENT' => "ITEM_GROUP_{$k}",
                    "NAME" => Loc::getMessage('ITEM_PARAMS_BLOCK1_ID_ELEMENT'),
                    "TYPE" => "TEXT",
                    "SORT" => 200,
                ];
                break;
            case ("block2"):
                $paramsList["ITEM_PARAMS_BLOCK2_SECTIOM_ID_{$k}"] = [
                    'PARENT' => "ITEM_GROUP_{$k}",
                    "NAME" => Loc::getMessage('ITEM_PARAMS_BLOCK2_SECTIOM_ID'),
                    "TYPE" => "TEXT",
                    "SORT" => 200,
                ];
                break;
           case ("block3"):
               $paramsList["ITEM_PARAMS_BLOCK3_SECTIOM_ID_{$k}"] = [
                   'PARENT' => "ITEM_GROUP_{$k}",
                   "NAME" => Loc::getMessage('ITEM_PARAMS_BLOCK3_SECTIOM_ID'),
                   "TYPE" => "TEXT",
                   "SORT" => 200,
               ];
               break;
            case ("block4"):
                $paramsList["ITEM_PARAMS_BLOCK4_SECTIOM_ID_{$k}"] = [
                    'PARENT' => "ITEM_GROUP_{$k}",
                    "NAME" => Loc::getMessage('ITEM_PARAMS_BLOCK4_SECTIOM_ID'),
                    "TYPE" => "TEXT",
                    "SORT" => 200,
                ];
                break;
           case ("block5"):
               $paramsList["LEFT_TITLE_{$k}"] = [
                   'PARENT' => "ITEM_GROUP_{$k}",
                   "NAME" => Loc::getMessage('LEFT_TITLE'),
                   "TYPE" => "TEXT",
                   "SORT" => 200,
                   "DEFAULT"  =>  Loc::getMessage('LEFT_TITLE_DEF'),
               ];
               $paramsList["LEFT_TEXT_{$k}"] = [
                   'PARENT' => "ITEM_GROUP_{$k}",
                   "NAME" => Loc::getMessage('LEFT_TEXT'),
                   "TYPE" => "TEXT",
                   "SORT" => 200,
                   "DEFAULT"  =>  Loc::getMessage('LEFT_TEXT_DEF'),
               ];
               $paramsList["LEFT_BG_COLOR_{$k}"] = [
                   'PARENT' => "ITEM_GROUP_{$k}",
                   "NAME" => Loc::getMessage('LEFT_BG_COLOR'),
                   "TYPE" => "TEXT",
                   "SORT" => 200,
                   "DEFAULT"  =>  Loc::getMessage('LEFT_BG_COLOR_DEF'),
               ];
               $paramsList["LEFT_FILE_{$k}"] = [
                   'PARENT' => "ITEM_GROUP_{$k}",
                   "NAME" => Loc::getMessage('LEFT_FILE'),
                   "TYPE" => "FILE",
                   "DEFAULT" => "",
                   "REFRESH" => "Y",

               ];


               $paramsList["FORM_TITLE_{$k}"] = [
                   'PARENT' => "ITEM_GROUP_{$k}",
                   "NAME" => Loc::getMessage('FORM_TITLE'),
                   "TYPE" => "TEXT",
                   "SORT" => 200,
                   "DEFAULT"  =>  Loc::getMessage('FORM_TITLE_DEF'),
               ];
               $paramsList["FORM_BTN_TITLE_{$k}"] = [
                   'PARENT' => "ITEM_GROUP_{$k}",
                   "NAME" => Loc::getMessage('FORM_BTN_TITLE'),
                   "TYPE" => "TEXT",
                   "SORT" => 200,
                   "DEFAULT"  =>  Loc::getMessage('FORM_BTN_TITLE_DEF'),
               ];
               $paramsList["FORM_EVENT_NAME_{$k}"] = [
                   'PARENT' => "ITEM_GROUP_{$k}",
                   "NAME" => Loc::getMessage('FORM_EVENT_NAME'),
                   "TYPE" => "LIST",
                   "VALUES" => $eventName,
                   "DEFAULT" => "",
                   "REFRESH" => "Y",
               ];

               $paramsList["FORM_FIELDS_{$k}"] = [
                   'PARENT' => "ITEM_GROUP_{$k}",
                   "NAME" => Loc::getMessage('FORM_FIELDS'),
                   "TYPE"     => "LIST",
                   "VALUES"   => $fields,
                   "SIZE"     => 10,
                   "MULTIPLE" => "Y",
                   "ADDITIONAL_VALUES" => "N"
               ];
               $paramsList["FORM_FIELDS_REQUIRED_{$k}"] = [
                   'PARENT' => "ITEM_GROUP_{$k}",
                   "NAME" => Loc::getMessage('FORM_FIELDS_REQUIRED'),
                   "TYPE"     => "LIST",
                   "VALUES"   => $fields,
                   "SIZE"     => 10,
                   "MULTIPLE" => "Y",
                   "ADDITIONAL_VALUES" => "N"
               ];
               $paramsList["FORM_SUCCESS_TITLE_{$k}"]=[
                   'PARENT' => "ITEM_GROUP_{$k}",
                   'NAME' => Loc::getMessage('FORM_SUCCESS_TITLE'),
                   'TYPE' => 'STRING',
                   'REFRESH' => 'Y',
                   "DEFAULT"  =>  Loc::getMessage('FORM_SUCCESS_TITLE_DEF'),
               ];

               $paramsList["FORM_SUCCESS_TEXT_{$k}"]=[
                   'PARENT' => "ITEM_GROUP_{$k}",
                   'NAME' => Loc::getMessage('FORM_SUCCESS_TEXT'),
                   'TYPE' => 'STRING',
                   'REFRESH' => 'Y',
                   "DEFAULT"  =>  Loc::getMessage('FORM_SUCCESS_TEXT_DEF'),
               ];

               $paramsList["FORM_SUCCESS_WRITE_{$k}"]=[
                   'PARENT' => "ITEM_GROUP_{$k}",
                   'NAME' => Loc::getMessage('FORM_SUCCESS_WRITE'),
                   'TYPE' => 'STRING',
                   'REFRESH' => 'Y',
                   "DEFAULT"  =>  Loc::getMessage('FORM_SUCCESS_WRITE_DEF'),
               ];

               $paramsList["FORM_SUCCESS_GO_{$k}"]=[
                   'PARENT' => "ITEM_GROUP_{$k}",
                   'NAME' => Loc::getMessage('FORM_SUCCESS_GO'),
                   'TYPE' => 'STRING',
                   'REFRESH' => 'Y',
                   "DEFAULT"  =>  Loc::getMessage('FORM_SUCCESS_GO_DEF'),
               ];

               $paramsList["FORM_SUCCESS_CALL_{$k}"]=[
                   'PARENT' => "ITEM_GROUP_{$k}",
                   'NAME' => Loc::getMessage('FORM_SUCCESS_CALL'),
                   'TYPE' => 'STRING',
                   'REFRESH' => 'Y',
                   "DEFAULT"  =>  Loc::getMessage('FORM_SUCCESS_CALL_DEF'),
               ];

               break;
            case ("block6"):
                $paramsList["ITEM_PARAMS_BLOCK6_ID_ELEMENT_{$k}"] = [
                    'PARENT' => "ITEM_GROUP_{$k}",
                    "NAME" => Loc::getMessage('ITEM_PARAMS_BLOCK6_ID_ELEMENT'),
                    "TYPE" => "TEXT",
                    "SORT" => 200,
                ];
                break;
            case ("block7"):
                $paramsList["ITEM_PARAMS_BLOCK7_SECTIOM_ID_{$k}"] = [
                    'PARENT' => "ITEM_GROUP_{$k}",
                    "NAME" => Loc::getMessage('ITEM_PARAMS_BLOCK7_SECTIOM_ID'),
                    "TYPE" => "TEXT",
                    "SORT" => 200,
                ];

                $paramsList["ITEM_PARAMS_PADDING_{$k}"] = [
                    'PARENT' => "ITEM_GROUP_{$k}",
                    "NAME" => Loc::getMessage('ITEM_PARAMS_PADDING'),
                    "TYPE" => "CHECKBOX",
                    "SORT" => 100,
                    'REFRESH' => 'Y',
                    "DEFAULT" => "N",
                ];
                break;
            case ("block8"):
                $paramsList["ITEM_PARAMS_BLOCK8_ID_ELEMENT_{$k}"] = [
                    'PARENT' => "ITEM_GROUP_{$k}",
                    "NAME" => Loc::getMessage('ITEM_PARAMS_BLOCK8_ID_ELEMENT'),
                    "TYPE" => "TEXT",
                    "SORT" => 200,
                ];
                $paramsList["ITEM_PARAMS_PADDING_{$k}"] = [
                    'PARENT' => "ITEM_GROUP_{$k}",
                    "NAME" => Loc::getMessage('ITEM_PARAMS_PADDING'),
                    "TYPE" => "CHECKBOX",
                    "SORT" => 100,
                    'REFRESH' => 'Y',
                    "DEFAULT" => "N",
                ];
                break;
            default;

        }

    }
    foreach ($arCurrentValues["PARAM_LIST"] as $code) {
        if (empty($arCurrentValues["PARAM_{$code}_NAME"])) {
            continue;
        }

        switch ($arCurrentValues["PARAM_{$code}_TYPE"]) {

            case "SVG":
                $paramsList["ITEM_PARAMS_{$code}_{$k}"] = [
                    'NAME' => $arCurrentValues["PARAM_{$code}_NAME"],
                    'TYPE' => "FILE",
                    'PARENT' => "ITEM_GROUP_{$k}",
                    "FD_TARGET" => "F",
                    "FD_EXT" => 'svg',
                    "FD_UPLOAD" => true,
                    "FD_USE_MEDIALIB" => false,
                    "FD_MEDIALIB_TYPES" => false,
                ];

                break;
            case "INCLUDE_FILE":
                $paramsList["ITEM_PARAMS_{$code}_{$k}"] = [
                    'NAME' => $arCurrentValues["PARAM_{$code}_NAME"],
                    'TYPE' => "FILE",
                    'PARENT' => "ITEM_GROUP_{$k}",
                    "FD_TARGET" => "F",
                    "FD_EXT" => 'php',
                    "FD_UPLOAD" => true,
                    "FD_USE_MEDIALIB" => false,
                    "FD_MEDIALIB_TYPES" => false,
                ];
                break;
            case "FILE":
                $paramsList["ITEM_PARAMS_{$code}_{$k}"] = [
                    'NAME' => $arCurrentValues["PARAM_{$code}_NAME"],
                    'TYPE' => $arCurrentValues["PARAM_{$code}_TYPE"],
                    'PARENT' => "ITEM_GROUP_{$k}",
                    "FD_TARGET" => "F",
                    "FD_EXT" => 'jpg,jpeg',
                    "FD_UPLOAD" => true,
                    "FD_USE_MEDIALIB" => true,
                    "FD_MEDIALIB_TYPES" => ['image'],
                ];
                break;

            case "TEXT":

                $paramsList["ITEM_PARAMS_{$code}_{$k}"] = [
                    'NAME' => $arCurrentValues["PARAM_{$code}_NAME"],
                    'TYPE' => $arCurrentValues["PARAM_{$code}_TYPE"],
                    'PARENT' => "ITEM_GROUP_{$k}",
                    'COLS' => '100',
                    'ROWS' => '40',
                ];
                break;
            case "STRING":
            default :
                $paramsList["ITEM_PARAMS_{$code}_{$k}"] = [
                    'NAME' => $arCurrentValues["PARAM_{$code}_NAME"],
                    'TYPE' => "TEXT",
                    'PARENT' => "ITEM_GROUP_{$k}",
                ];
        }
        $paramsList["ITEM_PARAMS_{$code}_{$k}"] ["SORT"] = 600;
    }

    $arComponentParameters["PARAMETERS"] = array_merge($arComponentParameters['PARAMETERS'], $paramsList);
}


CBitrixComponent::includeComponentClass("lib:baseComponent");
CShBaseComponent::addSmartCacheParam($arComponentParameters);
