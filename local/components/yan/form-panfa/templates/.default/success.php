<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);
?>
<div class="modal__body" >
    <form class="form">
        <div class="form__title"><?=$arParams["FORM_SUCCESS_TITLE"]?></div>
        <div class="form__text"><?=$arParams["FORM_SUCCESS_TEXT"]?></div>
    </form>
</div>
<div class="modal__footer">
    <div class="modal__footer-item">
        <span class="modal__footer-item-label"><?=Loc::getMessage('FORM_SUCCESS_WRITE')?></span>
        <a href="mailto:<?=$arParams["FORM_SUCCESS_WRITE"]?>" class="modal__footer-item-value"><?=$arParams["FORM_SUCCESS_WRITE"]?></a>
    </div>
    <div class="modal__footer-item">
        <span class="modal__footer-item-label"><?=Loc::getMessage('FORM_SUCCESS_GO')?></span>
        <span class="modal__footer-item-value"><?=$arParams["FORM_SUCCESS_GO"]?></span>
    </div>
    <div class="modal__footer-item">
        <span class="modal__footer-item-label"><?=Loc::getMessage('FORM_SUCCESS_CALL')?></span>
        <a href="tel:<?=$arParams["FORM_SUCCESS_CALL"]?>" class="modal__footer-item-value"><?=$arParams["FORM_SUCCESS_CALL"]?></a>
    </div>
</div>