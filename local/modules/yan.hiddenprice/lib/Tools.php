<?php

namespace Yan\Hiddenprice;

IncludeTemplateLangFile(__FILE__);

class Tools
{
    const MODULE_ID = "yan.hiddenprice";

    static $arParametrsList = array();

    public static function getModuleOptionsList($flag = true)
    {

        $optionList = array();

        if ($flag) {

            $optionList[] = array('USER_AUTH_ONLY', GetMessage('USER_AUTH_ONLY'), 'N', array('checkbox'));
            $optionList[] = array('SUBSTITUTION_TEXT', GetMessage('SUBSTITUTION_TEXT'), '', array('text', 100));
        }
        return $optionList;
    }

    public static function getSiteTabList($flag = false)
    {

        $arSites = [];
        $db_res = \CSite::GetList($by="sort", $order="desc", array());
        while ($res = $db_res->GetNext()) {
            $arSites[] = $res;
        }
        $arTabs = array();
        foreach ($arSites as $key => $arSite) {
            $arTabs[] = array(
                'DIV' => 'edit' . ($key + 1),
                "TAB" => GetMessage("YAN_HIDDEN_PRICE_MAIN_OPTIONS_SITE_TITLE", array("#SITE_NAME#" => $arSite["NAME"], "#SITE_ID#" => $arSite["ID"])),
                'ICON' => 'settings',
                'PAGE_TYPE' => 'site_settings',
                'SITE_ID' => $arSite['ID'],
                'OPTIONS' => self::getModuleOptionsList(),
            );
        }

        return $arTabs;
    }



    public static function saveOptionList($moduleId, $arOption, $siteID)
    {


        if (!is_array($arOption)) {
            return false;
        }

        $arControllerOption = \CControllerClient::GetInstalledOptions($moduleId);
        if (isset($arControllerOption[$arOption[0]])) {
            return false;
        }

        $name = $arOption[0];
        $value = $_REQUEST[$name."_".$siteID];

        if (array_key_exists(4, $arOption) && $arOption[4] == 'Y') {
            if ($arOption[3][0] == 'checkbox') {
                $value = 'N';
            } else {
                return false;
            }
        }

        if ($arOption[3][0] == 'checkbox' && $value != 'Y') {
            $value = 'N';
        }

        \COption::SetOptionString($moduleId, $name, $value, $arOption[1], $siteID);
    }

    public static function getOptionsTabList($moduleId, $Option, $siteID)
    {

        if (!is_array($Option)): ?>
            <tr class="heading">
                <td colspan="2"><?= $Option ?></td>
            </tr>
        <? elseif (isset($Option['note'])):
            $name = $Option[0];
            ?>
            <tr class="row-<?= strtolower($name) ?>">
                <td colspan="2" align="center">
                    <?= BeginNote('align="center"'); ?>
                    <?= $Option['note'] ?>
                    <?= EndNote(); ?>
                </td>
            </tr>
        <? else:

            $name = $Option[0];
            $value = \COption::GetOptionString($moduleId, $name, $Option[2], $siteID);

            $type = $Option[3];
            $disabled = array_key_exists(4, $Option) && $Option[4] == 'Y' ? ' disabled' : '';
            $sup_text = array_key_exists(5, $Option) ? $Option[5] : '';
            ?>
            <tr class="row-<?= strtolower($name) ?>">
                <td width="50%"><?
                    if ($type[0] == 'checkbox') echo '<label for="' . htmlspecialcharsbx($name) . '">' . $Option[1] . '</label>';
                    else echo $Option[1];
                    if (strlen($sup_text) > 0) { ?><span class="required"><sup><?= $sup_text ?></sup></span><? }
                    ?></td>
                <td width="50%"><?
                    if ($type[0] == 'checkbox'): ?><input
                        type="checkbox"
                        id="<?= htmlspecialcharsbx($name."_".$siteID) ?>"
                        name="<?= htmlspecialcharsbx($name."_".$siteID) ?>"
                        value="Y"<? if ($value == "Y") echo " checked"; ?><?= $disabled ?><? if ($type[2] <> '') echo " " . $type[2] ?>><?
                    elseif ($type[0] == 'text' ): ?>
                        <input
                        class="adm-input" type="<?= $type[0] ?>"
                        size="<?= $type[1] ?>" maxlength="255"
                        value="<?= htmlspecialcharsbx($value) ?>"
                        name="<?= htmlspecialcharsbx($name."_".$siteID) ?>"<?= $disabled ?>><?

                    endif;
                    ?></td>
            </tr>
        <?
        endif;
    }
}
