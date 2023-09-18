<?

/**
 * @author Dmitry Sharyk
 * email: d.sharyk@gmail.com
 */
CBitrixComponent::includeComponentClass("lib:baseComponent");

class CNewSiteCustom extends CShBaseComponent
{
    //парметры компонента без ключей
    public $fields = [
        0 => "ITEM_PARAMS_ACTIVE_",
        1 => "ITEM_PARAMS_SORT_",
        2 => "ITEM_IBLOCK_TYPE_",
        3 => "ITEM_IBLOCK_ID_",
        4 => "ITEM_PARAMS_BLOCK1_ID_ELEMENT_",
        5 => "ITEM_PARAMS_BLOCK2_SECTIOM_ID_",
        6 => "ITEM_PARAMS_BLOCK4_SECTIOM_ID_",
        7 => "ITEM_PARAMS_BLOCK6_ID_ELEMENT_",
        8 => "ITEM_PARAMS_BLOCK7_SECTIOM_ID_",
        9 => "ITEM_PARAMS_BLOCK8_ID_ELEMENT_",
        10 => "ITEM_PARAMS_HR_",
        11 => "ITEM_PARAMS_HR_BG_COLOR_",
        12 => "ITEM_PARAMS_PADDING_",
        13 => "ITEM_PARAMS_BLOCK3_SECTIOM_ID_"
    ];

    function onPrepareComponentParams($arCurrentValues)
    {

        $fields = array();
        global $USER, $APPLICATION;

        $arCurrentValues["ITEMS"] = array();


        foreach ($arCurrentValues["ITEM_LIST"] as $k => $adv) {
            if (isset($arCurrentValues["ITEM_PARAMS_ACTIVE_{$k}"]) && $arCurrentValues["ITEM_PARAMS_ACTIVE_{$k}"] == "N") {
                continue;
            }

            $arCurrentValues["ITEMS"][$k]["ITEM_CODE"] = $adv;
            $arCurrentValues["ITEMS"][$k]["SORT"] = $arCurrentValues["ITEM_PARAMS_SORT_{$k}"];
            foreach ($arCurrentValues["PARAM_LIST"] as $code) {


                if ($arCurrentValues["PARAM_{$code}_TYPE"] == "INSERT_FROM_BUFFER" && $arCurrentValues["ITEM_PARAMS_{$code}_{$k}"]) {
                    /* @var $APPLICATION type */
                    $arCurrentValues["ITEM_PARAMS_{$code}_{$k}"] = $APPLICATION->GetViewContent($arCurrentValues["ITEM_PARAMS_{$code}_{$k}"]);
                }


                $arCurrentValues["ITEMS"][$k][$code] = $arCurrentValues["ITEM_PARAMS_{$code}_{$k}"];
            }
        }

        uasort($arCurrentValues["ITEMS"], array($this, "sortBySortField"));

        return parent::onPrepareComponentParams($arCurrentValues);
    }

    function sortBySortField($a, $b)
    {
        return ($a["SORT"] == $b["SORT"]) ? 0 : (($a["SORT"] < $b["SORT"]) ? -1 : 1);
    }

    public function executeComponent()
    {

        foreach ($this->arParams["ITEMS"] as $key => $item) {
            if (!empty($item["ITEM_CODE"])) {
                //проверяем активность
                if (isset($this->arParams[$this->fields[0] . $key]) && $this->arParams[$this->fields[0] . $key] == "Y") {
                    //Должен быть задан тип
                    if (isset($this->arParams[$this->fields[2] . $key]) && !empty($this->arParams[$this->fields[2] . $key])) {
                        //Должен быть id
                        if (isset($this->arParams[$this->fields[3] . $key]) && !empty($this->arParams[$this->fields[3] . $key])) {
                            //Достаем код
                            if ($code = \Helper::GetCodeIblock($this->arParams[$this->fields[2] . $key], $this->arParams[$this->fields[3] . $key])) {
                                //Шаблон 1
                                if ($code == "block1") {
                                    //Должен быть id элемента
                                    if (isset($this->arParams[$this->fields[4] . $key]) && $this->arParams[$this->fields[4] . $key] > 0) {
                                        $this->ShowComponents($code, $key, 4);
                                    }
                                }
                                //Шаблон 2
                                if ($code == "block2") {
                                    //Должен быть id раздела
                                    if (isset($this->arParams[$this->fields[5] . $key]) && $this->arParams[$this->fields[5] . $key] > 0) {
                                        $this->ShowComponents($code, $key, 5);
                                    }
                                }
                                //Шаблон 3
                                if ($code == "block3") {
                                    //Должен быть id раздела
                                    if (isset($this->arParams[$this->fields[13] . $key]) && $this->arParams[$this->fields[13] . $key] > 0) {
                                        $this->ShowComponents($code, $key, 13);
                                    }
                                }
                                //Шаблон 4
                                if ($code == "block4") {
                                    //Должен быть id раздела
                                    if (isset($this->arParams[$this->fields[6] . $key]) && $this->arParams[$this->fields[6] . $key] > 0) {
                                        $this->ShowComponents($code, $key, 6);
                                    }
                                }

                                //Шаблон 5
                                if ($code == "block5") {
                                    $this->ShowComponents($code, $key);
                                }
                                //Шаблон 6
                                if ($code == "block6") {
                                    //Должен быть id раздела
                                    if (isset($this->arParams[$this->fields[7] . $key]) && $this->arParams[$this->fields[7] . $key] > 0) {
                                        $this->ShowComponents($code, $key, 7);
                                    }
                                }
                                //Шаблон 7
                                if ($code == "block7") {
                                    //Должен быть id раздела
                                    if (isset($this->arParams[$this->fields[8] . $key]) && $this->arParams[$this->fields[8] . $key] > 0) {
                                        $this->ShowComponents($code, $key, 8);
                                    }
                                }
                                //Шаблон 8
                                if ($code == "block8") {
                                    //Должен быть id раздела
                                    if (isset($this->arParams[$this->fields[9] . $key]) && $this->arParams[$this->fields[9] . $key] > 0) {
                                        $this->ShowComponents($code, $key, 9);
                                    }
                                }

                            }

                        }

                    }
                }

            }
        }

        $this->IncludeComponentTemplate();
    }

    /**
     * Показываем компоненты
     *
     * @param $code
     * @param $key
     * @return void
     */
    public function ShowComponents($code, $key, $id = 0)
    {
        global $APPLICATION;

        switch ($code) {
            case ("block1"):

                $APPLICATION->IncludeComponent(
                    "bitrix:news.detail",
                    $code,
                    array(
                        "DISPLAY_DATE" => "Y",
                        "DISPLAY_NAME" => "Y",
                        "DISPLAY_PICTURE" => "Y",
                        "DISPLAY_PREVIEW_TEXT" => "Y",
                        "USE_SHARE" => "N",
                        "AJAX_MODE" => "N",
                        "IBLOCK_TYPE" => $this->arParams[$this->fields[2] . $key],
                        "IBLOCK_ID" => $this->arParams[$this->fields[3] . $key],
                        "ELEMENT_ID" => $this->arParams[$this->fields[$id] . $key],
                        "ELEMENT_CODE" => "",
                        "CHECK_DATES" => "Y",
                        "FIELD_CODE" => array(
                            0 => "PREVIEW_TEXT",
                            1 => "PREVIEW_PICTURE",
                        ),
                        "PROPERTY_CODE" => array(
                            0 => "TITLE_COLOR_HEX",
                            1 => "BG_COLOR_HEX",
                            2 => "TITLE_BTN_ANCHOR",
                            3 => "TITLE_BLOCK"
                        ),
                        "IBLOCK_URL" => "",
                        "SET_TITLE" => "N",
                        "SET_BROWSER_TITLE" => "N",
                        "BROWSER_TITLE" => "-",
                        "SET_META_KEYWORDS" => "N",
                        "META_KEYWORDS" => "-",
                        "SET_META_DESCRIPTION" => "N",
                        "META_DESCRIPTION" => "-",
                        "SET_STATUS_404" => "N",
                        "INCLUDE_IBLOCK_INTO_CHAIN" => "N",
                        "ADD_SECTIONS_CHAIN" => "N",
                        "ADD_ELEMENT_CHAIN" => "N",
                        "ACTIVE_DATE_FORMAT" => "d.m.Y",
                        "USE_PERMISSIONS" => "N",
                        "CACHE_TYPE" => "A",
                        "CACHE_TIME" => "36000000",
                        "CACHE_GROUPS" => "Y",
                        "PAGER_TEMPLATE" => ".default",
                        "DISPLAY_TOP_PAGER" => "N",
                        "DISPLAY_BOTTOM_PAGER" => "N",
                        "PAGER_TITLE" => "Страница",
                        "PAGER_SHOW_ALL" => "N",
                        "AJAX_OPTION_JUMP" => "N",
                        "AJAX_OPTION_STYLE" => "Y",
                        "AJAX_OPTION_HISTORY" => "N",
                        "AJAX_OPTION_ADDITIONAL" => ""
                    ),
                    $component,
                    array("HIDE_ICONS" => "Y")
                );
                break;
            case ("block2"):

                $APPLICATION->IncludeComponent(
                    "bitrix:news.list",
                    $code,
                    array(
                        "DISPLAY_DATE" => "N",
                        "DISPLAY_NAME" => "Y",
                        "DISPLAY_PICTURE" => "N",
                        "DISPLAY_PREVIEW_TEXT" => "Y",
                        "AJAX_MODE" => "N",
                        "IBLOCK_TYPE" => $this->arParams[$this->fields[2] . $key],
                        "IBLOCK_ID" => $this->arParams[$this->fields[3] . $key],
                        "NEWS_COUNT" => "20",
                        "SORT_BY1" => "ACTIVE_FROM",
                        "SORT_ORDER1" => "DESC",
                        "SORT_BY2" => "SORT",
                        "SORT_ORDER2" => "ASC",
                        "FILTER_NAME" => "",
                        "FIELD_CODE" => array(
                            0 => "",
                            1 => "",
                        ),
                        "PROPERTY_CODE" => array(
                            0 => "BLOCK_WIDTH",
                            1 => "",
                        ),
                        "CHECK_DATES" => "Y",
                        "DETAIL_URL" => "",
                        "PREVIEW_TRUNCATE_LEN" => "",
                        "ACTIVE_DATE_FORMAT" => "d.m.Y",
                        "SET_TITLE" => "N",
                        "SET_BROWSER_TITLE" => "N",
                        "SET_META_KEYWORDS" => "N",
                        "SET_META_DESCRIPTION" => "N",
                        "SET_STATUS_404" => "N",
                        "INCLUDE_IBLOCK_INTO_CHAIN" => "N",
                        "ADD_SECTIONS_CHAIN" => "N",
                        "HIDE_LINK_WHEN_NO_DETAIL" => "N",
                        "PARENT_SECTION" => $this->arParams[$this->fields[$id] . $key],
                        "PARENT_SECTION_CODE" => "",
                        "INCLUDE_SUBSECTIONS" => "Y",
                        "CACHE_TYPE" => "A",
                        "CACHE_TIME" => "36000000",
                        "CACHE_FILTER" => "N",
                        "CACHE_GROUPS" => "Y",
                        "PAGER_TEMPLATE" => ".default",
                        "DISPLAY_TOP_PAGER" => "N",
                        "DISPLAY_BOTTOM_PAGER" => "N",
                        "PAGER_TITLE" => "Новости",
                        "PAGER_SHOW_ALWAYS" => "N",
                        "PAGER_DESC_NUMBERING" => "N",
                        "PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
                        "PAGER_SHOW_ALL" => "N",
                        "AJAX_OPTION_JUMP" => "N",
                        "AJAX_OPTION_STYLE" => "Y",
                        "AJAX_OPTION_HISTORY" => "N",
                        "AJAX_OPTION_ADDITIONAL" => ""
                    ),
                    $component,
                    array("HIDE_ICONS" => "Y")
                );
                break;
            case ("block3"):
                $APPLICATION->IncludeComponent(
                    "bitrix:news.list",
                    $code,
                    array(
                        "DISPLAY_DATE" => "N",
                        "DISPLAY_NAME" => "Y",
                        "DISPLAY_PICTURE" => "N",
                        "DISPLAY_PREVIEW_TEXT" => "Y",
                        "AJAX_MODE" => "N",
                        "IBLOCK_TYPE" => $this->arParams[$this->fields[2] . $key],
                        "IBLOCK_ID" => $this->arParams[$this->fields[3] . $key],
                        "NEWS_COUNT" => "20",
                        "SORT_BY1" => "ACTIVE_FROM",
                        "SORT_ORDER1" => "DESC",
                        "SORT_BY2" => "SORT",
                        "SORT_ORDER2" => "ASC",
                        "FILTER_NAME" => "",
                        "FIELD_CODE" => array(
                            0 => "",
                        ),
                        "PROPERTY_CODE" => array(
                            0 => "PICTURE",
                            1 => "",
                        ),
                        "CHECK_DATES" => "Y",
                        "DETAIL_URL" => "",
                        "PREVIEW_TRUNCATE_LEN" => "",
                        "ACTIVE_DATE_FORMAT" => "d.m.Y",
                        "SET_TITLE" => "N",
                        "SET_BROWSER_TITLE" => "N",
                        "SET_META_KEYWORDS" => "N",
                        "SET_META_DESCRIPTION" => "N",
                        "SET_STATUS_404" => "N",
                        "INCLUDE_IBLOCK_INTO_CHAIN" => "N",
                        "ADD_SECTIONS_CHAIN" => "N",
                        "HIDE_LINK_WHEN_NO_DETAIL" => "N",
                        "PARENT_SECTION" => $this->arParams[$this->fields[$id] . $key],
                        "PARENT_SECTION_CODE" => "",
                        "INCLUDE_SUBSECTIONS" => "Y",
                        "CACHE_TYPE" => "A",
                        "CACHE_TIME" => "36000000",
                        "CACHE_FILTER" => "N",
                        "CACHE_GROUPS" => "Y",
                        "PAGER_TEMPLATE" => ".default",
                        "DISPLAY_TOP_PAGER" => "N",
                        "DISPLAY_BOTTOM_PAGER" => "N",
                        "PAGER_TITLE" => "Новости",
                        "PAGER_SHOW_ALWAYS" => "N",
                        "PAGER_DESC_NUMBERING" => "N",
                        "PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
                        "PAGER_SHOW_ALL" => "N",
                        "AJAX_OPTION_JUMP" => "N",
                        "AJAX_OPTION_STYLE" => "Y",
                        "AJAX_OPTION_HISTORY" => "N",
                        "AJAX_OPTION_ADDITIONAL" => ""
                    ),
                    $component,
                    array("HIDE_ICONS" => "Y")
                );
                break;
            case ("block4"):
                ?>
                <? $APPLICATION->IncludeComponent(
                "bitrix:news.list",
                $code,
                array(
                    "DISPLAY_DATE" => "N",
                    "DISPLAY_NAME" => "Y",
                    "DISPLAY_PICTURE" => "N",
                    "DISPLAY_PREVIEW_TEXT" => "Y",
                    "AJAX_MODE" => "N",
                    "IBLOCK_TYPE" => $this->arParams[$this->fields[2] . $key],
                    "IBLOCK_ID" => $this->arParams[$this->fields[3] . $key],
                    "NEWS_COUNT" => "20",
                    "SORT_BY1" => "ACTIVE_FROM",
                    "SORT_ORDER1" => "DESC",
                    "SORT_BY2" => "SORT",
                    "SORT_ORDER2" => "ASC",
                    "FILTER_NAME" => "",
                    "FIELD_CODE" => array(
                        0 => "",
                        1 => "",
                    ),
                    "PROPERTY_CODE" => array(
                        0 => "BLOCK_WIDTH",
                        1 => "",
                    ),
                    "CHECK_DATES" => "Y",
                    "DETAIL_URL" => "",
                    "PREVIEW_TRUNCATE_LEN" => "",
                    "ACTIVE_DATE_FORMAT" => "d.m.Y",
                    "SET_TITLE" => "N",
                    "SET_BROWSER_TITLE" => "N",
                    "SET_META_KEYWORDS" => "N",
                    "SET_META_DESCRIPTION" => "N",
                    "SET_STATUS_404" => "N",
                    "INCLUDE_IBLOCK_INTO_CHAIN" => "N",
                    "ADD_SECTIONS_CHAIN" => "N",
                    "HIDE_LINK_WHEN_NO_DETAIL" => "N",
                    "PARENT_SECTION" => $this->arParams[$this->fields[$id] . $key],
                    "PARENT_SECTION_CODE" => "",
                    "INCLUDE_SUBSECTIONS" => "Y",
                    "CACHE_TYPE" => "A",
                    "CACHE_TIME" => "36000000",
                    "CACHE_FILTER" => "N",
                    "CACHE_GROUPS" => "Y",
                    "PAGER_TEMPLATE" => ".default",
                    "DISPLAY_TOP_PAGER" => "N",
                    "DISPLAY_BOTTOM_PAGER" => "N",
                    "PAGER_TITLE" => "Новости",
                    "PAGER_SHOW_ALWAYS" => "N",
                    "PAGER_DESC_NUMBERING" => "N",
                    "PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
                    "PAGER_SHOW_ALL" => "N",
                    "AJAX_OPTION_JUMP" => "N",
                    "AJAX_OPTION_STYLE" => "Y",
                    "AJAX_OPTION_HISTORY" => "N",
                    "AJAX_OPTION_ADDITIONAL" => ""
                ),
                $component,
                array("HIDE_ICONS" => "Y")
            ); ?>
                <?php break;
            case ("block5"):
                $fieldsForm = [
                    //левый блок
                    0 => "LEFT_TITLE_",
                    1 => "LEFT_TEXT_",
                    2 => "LEFT_BG_COLOR_",
                    3 => "LEFT_FILE_",
                    //форма
                    4 => "FORM_TITLE_",
                    5 => "FORM_BTN_TITLE_",
                    6 => "FORM_EVENT_NAME_",
                    7 => "FORM_FIELDS_",
                    8 => "FORM_FIELDS_REQUIRED_",
                    //ответ
                    9 => "FORM_SUCCESS_TITLE_",
                    10 => "FORM_SUCCESS_TEXT_",
                    11 => "FORM_SUCCESS_WRITE_",
                    12 => "FORM_SUCCESS_GO_",
                    13 => "FORM_SUCCESS_CALL_",
                ];
                //Показывать форму только если есть почтовое событие и поля
                if (isset($this->arParams[$fieldsForm[6] . $key]) &&
                    !empty($this->arParams[$fieldsForm[6] . $key]) &&
                    isset($this->arParams[$fieldsForm[7] . $key]) &&
                    !empty($this->arParams[$fieldsForm[7] . $key])
                ) {
                    $APPLICATION->IncludeComponent(
                        "yan:form",
                        ".default",
                        array(
                            "CACHE_TIME" => "36000000",
                            "CACHE_TYPE" => "A",
                            "IBLOCK_ID" => $this->arParams[$this->fields[3] . $key],
                            "IBLOCK_TYPE" => $this->arParams[$this->fields[2] . $key],
                            "COMPONENT_TEMPLATE" => ".default",
                            "LEFT_TITLE" => $this->arParams[$fieldsForm[0] . $key],
                            "LEFT_TEXT" => $this->arParams[$fieldsForm[1] . $key],
                            "LEFT_BG_COLOR" => $this->arParams[$fieldsForm[2] . $key],
                            "LEFT_FILE" => $this->arParams[$fieldsForm[3] . $key],
                            "FORM_TITLE" => $this->arParams[$fieldsForm[4] . $key],
                            "FORM_BTN_TITLE" => $this->arParams[$fieldsForm[5] . $key],
                            "FORM_EVENT_NAME" => $this->arParams[$fieldsForm[6] . $key],
                            "FORM_FIELDS" => $this->arParams[$fieldsForm[7] . $key],
                            "FORM_FIELDS_REQUIRED" => $this->arParams[$fieldsForm[8] . $key],
                            "FORM_SUCCESS_TITLE" => $this->arParams[$fieldsForm[9] . $key],
                            "FORM_SUCCESS_TEXT" => $this->arParams[$fieldsForm[10] . $key],
                            "FORM_SUCCESS_WRITE" => $this->arParams[$fieldsForm[11] . $key],
                            "FORM_SUCCESS_GO" => $this->arParams[$fieldsForm[12] . $key],
                            "FORM_SUCCESS_CALL" => $this->arParams[$fieldsForm[13] . $key],
                        ),
                        $component,
                        array("HIDE_ICONS" => "Y")
                    );
                }
                break;
            case ("block6"):
                $APPLICATION->IncludeComponent(
                    "bitrix:news.detail",
                    $code,
                    array(
                        "DISPLAY_DATE" => "Y",
                        "DISPLAY_NAME" => "Y",
                        "DISPLAY_PICTURE" => "Y",
                        "DISPLAY_PREVIEW_TEXT" => "Y",
                        "USE_SHARE" => "N",
                        "AJAX_MODE" => "N",
                        "IBLOCK_TYPE" => $this->arParams[$this->fields[2] . $key],
                        "IBLOCK_ID" => $this->arParams[$this->fields[3] . $key],
                        "ELEMENT_ID" => $this->arParams[$this->fields[$id] . $key],
                        "ELEMENT_CODE" => "",
                        "CHECK_DATES" => "Y",
                        "FIELD_CODE" => array(
                            0 => "PREVIEW_TEXT",
                            1 => "PREVIEW_PICTURE",
                        ),
                        "PROPERTY_CODE" => array(
                            0 => "TITLE_COLOR_HEX",
                        ),
                        "IBLOCK_URL" => "",
                        "SET_TITLE" => "N",
                        "SET_BROWSER_TITLE" => "N",
                        "BROWSER_TITLE" => "-",
                        "SET_META_KEYWORDS" => "N",
                        "META_KEYWORDS" => "-",
                        "SET_META_DESCRIPTION" => "N",
                        "META_DESCRIPTION" => "-",
                        "SET_STATUS_404" => "N",
                        "INCLUDE_IBLOCK_INTO_CHAIN" => "N",
                        "ADD_SECTIONS_CHAIN" => "N",
                        "ADD_ELEMENT_CHAIN" => "N",
                        "ACTIVE_DATE_FORMAT" => "d.m.Y",
                        "USE_PERMISSIONS" => "N",
                        "CACHE_TYPE" => "A",
                        "CACHE_TIME" => "36000000",
                        "CACHE_GROUPS" => "Y",
                        "PAGER_TEMPLATE" => ".default",
                        "DISPLAY_TOP_PAGER" => "N",
                        "DISPLAY_BOTTOM_PAGER" => "N",
                        "PAGER_TITLE" => "Страница",
                        "PAGER_SHOW_ALL" => "N",
                        "AJAX_OPTION_JUMP" => "N",
                        "AJAX_OPTION_STYLE" => "Y",
                        "AJAX_OPTION_HISTORY" => "N",
                        "AJAX_OPTION_ADDITIONAL" => ""
                    ),
                    $component,
                    array("HIDE_ICONS" => "Y")
                );
                break;
            case ("block7"):
                $APPLICATION->IncludeComponent(
                    "bitrix:news.list",
                    $code,
                    array(
                        "BLOCK_PADDING" => $this->arParams[$this->fields[12] . $key] ? $this->arParams[$this->fields[12] . $key] : false,
                        "DISPLAY_DATE" => "N",
                        "DISPLAY_NAME" => "Y",
                        "DISPLAY_PICTURE" => "N",
                        "DISPLAY_PREVIEW_TEXT" => "Y",
                        "AJAX_MODE" => "N",
                        "IBLOCK_TYPE" => $this->arParams[$this->fields[2] . $key],
                        "IBLOCK_ID" => $this->arParams[$this->fields[3] . $key],
                        "NEWS_COUNT" => "20",
                        "SORT_BY1" => "ACTIVE_FROM",
                        "SORT_ORDER1" => "DESC",
                        "SORT_BY2" => "SORT",
                        "SORT_ORDER2" => "ASC",
                        "FILTER_NAME" => "",
                        "FIELD_CODE" => array(
                            0 => "",
                            1 => "",
                        ),
                        "PROPERTY_CODE" => array(
                            0 => "LINK",
                            1 => "PICTURES",
                        ),
                        "CHECK_DATES" => "Y",
                        "DETAIL_URL" => "",
                        "PREVIEW_TRUNCATE_LEN" => "",
                        "ACTIVE_DATE_FORMAT" => "d.m.Y",
                        "SET_TITLE" => "N",
                        "SET_BROWSER_TITLE" => "N",
                        "SET_META_KEYWORDS" => "N",
                        "SET_META_DESCRIPTION" => "N",
                        "SET_STATUS_404" => "N",
                        "INCLUDE_IBLOCK_INTO_CHAIN" => "N",
                        "ADD_SECTIONS_CHAIN" => "N",
                        "HIDE_LINK_WHEN_NO_DETAIL" => "N",
                        "PARENT_SECTION" => $this->arParams[$this->fields[$id] . $key],
                        "PARENT_SECTION_CODE" => "",
                        "INCLUDE_SUBSECTIONS" => "Y",
                        "CACHE_TYPE" => "A",
                        "CACHE_TIME" => "36000000",
                        "CACHE_FILTER" => "N",
                        "CACHE_GROUPS" => "Y",
                        "PAGER_TEMPLATE" => ".default",
                        "DISPLAY_TOP_PAGER" => "N",
                        "DISPLAY_BOTTOM_PAGER" => "N",
                        "PAGER_TITLE" => "Новости",
                        "PAGER_SHOW_ALWAYS" => "N",
                        "PAGER_DESC_NUMBERING" => "N",
                        "PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
                        "PAGER_SHOW_ALL" => "N",
                        "AJAX_OPTION_JUMP" => "N",
                        "AJAX_OPTION_STYLE" => "Y",
                        "AJAX_OPTION_HISTORY" => "N",
                        "AJAX_OPTION_ADDITIONAL" => ""
                    ),
                    $component,
                    array("HIDE_ICONS" => "Y")
                );
                break;
            case ("block8"):

                $APPLICATION->IncludeComponent(
                    "bitrix:news.detail",
                    $code,
                    array(
                        "DISPLAY_DATE" => "Y",
                        "DISPLAY_NAME" => "Y",
                        "DISPLAY_PICTURE" => "Y",
                        "DISPLAY_PREVIEW_TEXT" => "Y",
                        "USE_SHARE" => "N",
                        "AJAX_MODE" => "N",
                        "IBLOCK_TYPE" => $this->arParams[$this->fields[2] . $key],
                        "IBLOCK_ID" => $this->arParams[$this->fields[3] . $key],
                        "ELEMENT_ID" => $this->arParams[$this->fields[$id] . $key],
                        "ELEMENT_CODE" => "",
                        "CHECK_DATES" => "Y",
                        "FIELD_CODE" => array(
                            0 => "PREVIEW_TEXT",
                            1 => "PREVIEW_PICTURE",
                        ),
                        "PROPERTY_CODE" => array(
                            0 => "TITLE_1",
                        ),
                        "IBLOCK_URL" => "",
                        "SET_TITLE" => "N",
                        "SET_BROWSER_TITLE" => "N",
                        "BROWSER_TITLE" => "-",
                        "SET_META_KEYWORDS" => "N",
                        "META_KEYWORDS" => "-",
                        "SET_META_DESCRIPTION" => "N",
                        "META_DESCRIPTION" => "-",
                        "SET_STATUS_404" => "N",
                        "INCLUDE_IBLOCK_INTO_CHAIN" => "N",
                        "ADD_SECTIONS_CHAIN" => "N",
                        "ADD_ELEMENT_CHAIN" => "N",
                        "ACTIVE_DATE_FORMAT" => "d.m.Y",
                        "USE_PERMISSIONS" => "N",
                        "CACHE_TYPE" => "A",
                        "CACHE_TIME" => "36000000",
                        "CACHE_GROUPS" => "Y",
                        "PAGER_TEMPLATE" => ".default",
                        "DISPLAY_TOP_PAGER" => "N",
                        "DISPLAY_BOTTOM_PAGER" => "N",
                        "PAGER_TITLE" => "Страница",
                        "PAGER_SHOW_ALL" => "N",
                        "AJAX_OPTION_JUMP" => "N",
                        "AJAX_OPTION_STYLE" => "Y",
                        "AJAX_OPTION_HISTORY" => "N",
                        "AJAX_OPTION_ADDITIONAL" => ""
                    ),
                    $component,
                    array("HIDE_ICONS" => "Y")
                );
                break;
            default;
        }
        //Полоска внизу блока
        if (isset($this->arParams[$this->fields[10] . $key]) && $this->arParams[$this->fields[10] . $key] == "Y") {
            //цвет заливки
            $bgColor = "#BF9C50";
            if (isset($this->arParams[$this->fields[11] . $key]) && !empty($this->arParams[$this->fields[11] . $key])) {
                $bgColor = $this->arParams[$this->fields[11] . $key];
            }

            echo '<div class="section"><div class="container"><hr class="hr" style="border-bottom: 2px solid ' . $bgColor . ';"></div></div>';
        }

    }


}
