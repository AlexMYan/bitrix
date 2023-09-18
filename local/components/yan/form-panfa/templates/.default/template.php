<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$this->setFrameMode(true);

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);
//если  ReferenceError: BX is not defined
CJSCore::Init(array("fx"));
CJSCore::Init(array("jquery"));

if (!isset($arResult["IBLOCK"]["PROPERTIES"]))
    return;


$obName = "ob" . preg_replace("/[^a-zA-Z0-9_]/", "x", $this->GetEditAreaId($this->randString()));

$containerName = "form-" . $obName;
?>

<div class="section" id="<?= $containerName ?>">
    <?//LEFT_BG_COLOR?>
    <div class="contact-form"  style="background:<?=$arParams["LEFT_BG_COLOR"]?$arParams["LEFT_BG_COLOR"]:''?>">
        <div class="container">
            <div class="contact-form__inner">
                <div class="contact-form__content">
                    <svg class="contact-form__icon">
                        <use href="#svg-icon-star"></use>
                    </svg>
                    <?//LEFT_TITLE?>
                    <div class="contact-form__title"><?=$arParams["LEFT_TITLE"]?></div>
                    <?//LEFT_TEXT?>
                    <div class="contact-form__text"><?=$arParams["LEFT_TEXT"]?></div>
                </div>
                <div class="contact-form__form-area" style="background: linear-gradient(203.26deg, #F7F5EA 3.31%, #FFFFFF 84.97%)">
                    <form class="form" action="<?= $APPLICATION->GetCurPage() ?>">

                        <input type="hidden" name="AJAX_CALL" value="Y"/>
                        <input type="hidden" name="bxajaxid" value="<?= $arParams["AJAX_ID"] ?>">

                        <?//FORM_TITLE?>
                        <div class="form__title"><?=$arParams["FORM_TITLE"]?></div>
                        <div class="form__fields">
                        <? //FORM FIELDS
                        foreach ($arResult["IBLOCK"]["PROPERTIES"] as $arProp) {
                            if ($arProp["USER_TYPE"] != "HTML") {
                                if ($arProp["PROPERTY_TYPE"] == "S") {
                                    //USER_PHONE
                                    if ($arProp["CODE"] == "USER_PHONE") { ?>

                                        <div class="form__field">
                                            <div class="field">
                                                <div class="field__label"><?= $arProp["NAME"] ?></div>
                                                <input type="tel" id="phone" class="field__input tel" name="<?= $arProp["CODE"] ?>" placeholder="+375 ( _ _ ) _ _ _ - _ _ - _ _">
                                            </div>
                                        </div>
                                        <? //USER_EMAIL?>
                                    <? } elseif ($arProp["CODE"] == "USER_EMAIL") { ?>

                                        <div class="form__field">
                                            <div class="field">
                                                <div class="field__label"><?= $arProp["NAME"] ?></div>
                                                <input type="email" class="field__input" name="<?= $arProp["CODE"] ?>">
                                            </div>
                                        </div>
                                    <? } else { ?>

                                        <div class="form__field">
                                            <div class="field">
                                                <div class="field__label"><?= $arProp["NAME"] ?></div>
                                                <input type="text" class="field__input" name="<?= $arProp["CODE"] ?>">
                                            </div>
                                        </div>
                                    <? }
                                }
                            }
                        } ?>
                            <div class="form__actions">
                                <button data-action="ADD" class="btn btn--brown btn-send-form"><?=$arParams["FORM_BTN_TITLE"]?></button>
                            </div>
                        </div>


                    </form>
                </div>
            </div>
        </div>
    </div>
</div>


<? $jsProps = array();
foreach ($arResult["IBLOCK"]["PROPERTIES"] as $arProp) {
    $jsProps[$arProp["CODE"]] = array(
        "CODE" => $arProp["CODE"],
        "REQUIRED" => $arProp["IS_REQUIRED"]
    );
}


unset($arProp); ?>

<script type="text/javascript">
    BX.message({
        FORMS_ALERT_ERROR: '<?=Loc::getMessage("FORMS_ALERT_ERROR")?>',
    });
    var <?=$obName?> = new JCFormEvenetComponent({

        jsProps: <?=CUtil::PhpToJSObject($jsProps)?>,
        container: '<?=$containerName?>',

    });
</script>
