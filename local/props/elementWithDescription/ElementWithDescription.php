<?php
/**
 * Это новый типа свойства для свойства инфоблока с двумя полями
 */

class ElementWithDescription
{
    function GetIBlockPropertyDescription()
    {
        return array(
            "PROPERTY_TYPE" => "E", // Прототип типа свойства - привязка к элементам
            "USER_TYPE" => "ElementWithDescription",
            "DESCRIPTION" => "Привязка к элементам с описанием", //Название нового типа свойства
            'GetPropertyFieldHtml' => array(__CLASS__, 'GetPropertyFieldHtml'),
            "ConvertToDB" => array(__CLASS__,"ConvertToDB"),
            "ConvertFromDB" => array(__CLASS__,"ConvertFromDB"),
        );
    }

    function GetPropertyFieldHtml($arProperty, $value, $strHTMLControlName)
    {
        $value["DESCRIPTION"] = unserialize($value["DESCRIPTION"]);

        $arItem = Array(
            "ID" => 0,
            "IBLOCK_ID" => 0,
            "NAME" => ""
        );

        if(intval($value["VALUE"]) > 0)
        {
            $arFilter = Array(
                "ID" => intval($value["VALUE"]),
                "IBLOCK_ID" => $arProperty["LINK_IBLOCK_ID"],
            );
            $arItem = \CIBlockElement::GetList(Array(), $arFilter, false, false, Array("ID", "IBLOCK_ID", "NAME"))->Fetch();
        }
        $html ='<style>.custom_yan{display: inline-block; width: 300px; margin-top: 5px; }</style>';
        $html .= '<input name="'.$strHTMLControlName["VALUE"].'" id="'.$strHTMLControlName["VALUE"].'" value="'
            .htmlspecialcharsex($value["VALUE"]).'" size="5" type="text">';
        $html .= ' <span class="custom_yan" id="sp_'.md5($strHTMLControlName["VALUE"]).'_'.$key.'">'.$arItem["NAME"].'</span>';
        $html .= '<input type="button" value="Выбрать" onclick="jsUtils.OpenWindow(\'/bitrix/admin/iblock_element_search.php?lang='.LANG.'&IBLOCK_ID='.$arProperty["LINK_IBLOCK_ID"].'&n='.$strHTMLControlName["VALUE"].'\', 600, 500);">';
        $html .= ' Таб:<input type="text" id="quan" name="'.$strHTMLControlName["DESCRIPTION"].'" value="'
            .htmlspecialcharsex($value["DESCRIPTION"]).'">';
        return  $html;
    }

    function GetAdminListViewHTML($arProperty, $value, $strHTMLControlName)
    {
        return;
    }

    function ConvertToDB($arProperty, $value)
    {
        $return = false;

        if( is_array($value) && array_key_exists("VALUE", $value) && ($value['VALUE'] > 0))
        {
            $return = array(
                "VALUE" => serialize($value["VALUE"]),
                "DESCRIPTION" => serialize($value["DESCRIPTION"]),
            );
        }

        return $return;
    }

    function ConvertFromDB($arProperty, $value)
    {
        $return = false;

        if(!is_array($value["VALUE"]))
        {
            $return = array(
                "VALUE" => unserialize($value["VALUE"]),
            );
        }

        if(!is_array($value["DESCRIPTION"]))
        {
            $return["DESCRIPTION"] = unserialize($value["DESCRIPTION"]);
        }

        if ($return['VALUE'] > 0):
            return $return;
        endif;
    }
}
