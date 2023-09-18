<?php
define('STOP_STATISTICS', true);
define('NOT_CHECK_PERMISSIONS', true);
define('NO_AGENT_CHECK', true);
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

if (!CModule::IncludeModule('iblock')) {
    ShowError('Ошибка!');
    die();
}

//Список городов
$cities = array();
$dbl = CIBlockElement::GetList(array(), array("IBLOCK_ID" => CITIES_IBLOCK_ID, "ACTIVE" => "Y"), false, false, array("ID", "NAME"));
while ($city = $dbl->Fetch()) {
    $cities[$city['ID']] = $city['NAME'];
}

$bdbl = CIBlockElement::GetList(array(), array('IBLOCK_ID' => BANK_IBLOCK_ID, 'ACTIVE' => 'Y'), false, false,
    array('ID', 'NAME', 'IBLOCK_ID', "CODE"));
$bank = array();
while ($item = $bdbl->Fetch()) {
    $bank[$item['ID']] = $item;
}

$cdbl = CIBlockElement::GetList(array(), array('IBLOCK_ID' => CURRENCIES_IBLOCK_ID, 'ACTIVE' => 'Y'), false, false,
    array('ID', 'NAME', 'IBLOCK_ID'));
$currencies = array();
while ($item = $cdbl->Fetch()) {
    $currencies[$item['ID']] = $item;
}

$dbl = CIBlockElement::GetList(array(), array('IBLOCK_ID' => ATM_IBLOCK_ID, 'ACTIVE' => 'Y'), false, false,
    array('ID', 'NAME', 'IBLOCK_ID', 'CODE'));

while ($res = $dbl->Fetch()) {

    $props = array();
    $dbl_prp = CIBlockElement::GetProperty($res['IBLOCK_ID'], $res['ID'], array());
    while ($prp = $dbl_prp->Fetch()) {
        if ($prp['CODE'] == 'CURRENCIES') {
            $res[$prp['CODE']][] = $currencies[$prp['VALUE']]['NAME'];
        } else {
            $res[$prp['CODE']] = $prp['VALUE'];
        }
        //Проверяем город
        if ($prp['CODE'] == 'CITY') {
            $res[$prp['CODE']] = "";
            if ($prp['VALUE'] > 0) {
                if (array_key_exists($prp['VALUE'], $cities)) {
                    $res[$prp['CODE']] = $cities[$prp['VALUE']];
                }
            }
        }
    }
    $arResult[] = $res;
}
//prent($bank); die();
//prent($arResult); die();

header("Content-Type: text/xml");
header("Expires: Thu, 19 Feb 1998 13:24:18 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-cache, must-revalidate");
header("Cache-Control: post-check=0,pre-check=0");
header("Cache-Control: max-age=0");
header("Pragma: no-cache");

echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<atmlist>
    <? foreach ($arResult as $value) : ?>
        <atm>
            <number><?= empty($value['CODE']) ? trim(htmlspecialchars($value['NAME'])) : trim(htmlspecialchars($value['CODE'])); ?></number>
            <address><?= trim(htmlspecialchars($value['ATM_ADDRESS'])); ?></address>
            <location><?= trim(htmlspecialchars($value['ATM_LOCATION'])); ?></location>
            <city><?= trim(htmlspecialchars($value['CITY'])); ?></city>
            <? if (!empty($value['BANK'])): ?>
                <affiliation><?= $bank[$value['BANK']]['CODE'] ?></affiliation><? endif; ?>
            <worktime><?= trim(htmlspecialchars($value['ATM_WORKING_TIME'])); ?></worktime>
            <? if (is_array($value['CURRENCIES'])) : ?>
                <? $cur_count = count($value['CURRENCIES']); ?>
                <? $counter = 1; ?>
                <facilities><? foreach ($value['CURRENCIES'] as $cur_item) : ?><?= trim($cur_item); ?><? if ($cur_count > $counter) : ?>,<? endif; ?><? $counter++; ?><? endforeach; ?></facilities>
            <? endif; ?>
            <? if (!empty($value['ATM_YANDEX']) && strpos($value['ATM_YANDEX'], ',')) : ?>
                <? $pos = explode(',', $value['ATM_YANDEX']); ?>
                <? if (is_array($pos) && count($pos) == 2) : ?>
                    <longitude><?= $pos[1]; ?></longitude>
                    <latitude><?= $pos[0]; ?></latitude>
                <? endif; ?>
            <? endif; ?>
        </atm>
    <? endforeach; ?>
</atmlist>
