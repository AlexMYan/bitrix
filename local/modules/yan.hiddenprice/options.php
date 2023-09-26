<?php

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Loader;

Loc::loadMessages(__FILE__);

$moduleID = "yan.hiddenprice";
global $APPLICATION;


$RIGHT = $APPLICATION->GetGroupRight($moduleID);
if($RIGHT >= "R") {
    if (!Loader::includeModule($moduleID))
        return;

    $arTabs = \Yan\Hiddenprice\Tools::getSiteTabList(true);

    $tabControl = new CAdminTabControl(
        'tabControl',
        $arTabs
    );

    if ($REQUEST_METHOD == "POST" /*&& strlen($Update . $Apply . $RestoreDefaults) */&& $RIGHT >= "W" && check_bitrix_sessid()) {
        global $APPLICATION;

        COption::RemoveOption($moduleID, "sid");
        foreach ($arTabs as $key => $arTab) {
            foreach($arTab["OPTIONS"] as $arOption) {

                \Yan\Hiddenprice\Tools::saveOptionList($moduleID, $arOption, $arTab["SITE_ID"]);
            }
        }
        unset($key, $arTab);


        $APPLICATION->RestartBuffer();
    }

    $tabControl->begin();
    ?>

    <form action="<?= $APPLICATION->getCurPage(); ?>?mid=<?= $moduleID; ?>&lang=<?= LANGUAGE_ID; ?>" method="POST">

        <?= bitrix_sessid_post(); ?>
        <?
        foreach ($arTabs as $key => $arTab) {
            $tabControl->BeginNextTab();
            if ($arTab["SITE_ID"]) {
                foreach($arTab["OPTIONS"] as $arOption){
                    \Yan\Hiddenprice\Tools::getOptionsTabList($moduleID, $arOption, $arTab["SITE_ID"]);
                }
            }
        }
        unset($key, $arTab);
        $tabControl->buttons();

        ?>
        <input type="submit" name="apply"
               value="<?= Loc::GetMessage('YAN_HIDDEN_PRICE_INPUT_APPLY'); ?>" class="adm-btn-save"/>

    </form>

    <?php
    $tabControl->end();
}else{
    CAdminMessage::ShowMessage(Loc::GetMessage("NO_RIGHTS_FOR_VIEWING"));
}
?>