<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

$arComponentParameters = [
    // группы в левой части окна
    "GROUPS" => [
        "SETTINGS" => [
            "NAME" => Loc::getMessage('GALERY_IMAGE_PATH_TITLE'),
            "SORT" => 550,
        ],
    ],
    // поля для ввода параметров в правой части
    "PARAMETERS" => [

        // Произвольный параметр типа СТРОКА
        "IMAGE_PATH" => [
            "PARENT" => "SETTINGS",
            "NAME" => Loc::getMessage('GALERY_IMAGE_PATH_TITLE'),
            "TYPE" => "STRING",
            "MULTIPLE" => "Y",
            "DEFAULT" => "",
            "COLS" => 25
        ],
        // Настройки кэширования
        'CACHE_TIME' => ['DEFAULT' => 3600],
    ]
];