<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$this->setFrameMode(true);

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);
//если  ReferenceError: BX is not defined
CJSCore::Init(array("fx"));

if (!isset($arResult["IBLOCK"]["PROPERTIES"]))
    return;

$obName = "ob" . preg_replace("/[^a-zA-Z0-9_]/", "x", $this->GetEditAreaId($this->randString()));

$containerName = "form-" . $obName;
?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/5.0.5/jquery.inputmask.min.js"></script>
<script>
    // Настройки для маски ввода телефона
    var phoneMaskSettings = {
        mask: '+7 (999) 999-99-99',
        placeholder: '+7 (___) ___-__-__'
    };

    // Применяем маску ввода для поля телефона
    $('#phone').inputmask(phoneMaskSettings);
</script>
<div id="<?= $containerName ?>">

    <div id="form" class="form-area" style="background-image: url('/local/images/events/form-bg.jpg')">

        <form class="form js-form hide-group" action="<?= $APPLICATION->GetCurPage() ?>">

            <input type="hidden" name="AJAX_CALL" value="Y"/>
            <input type="hidden" name="bxajaxid" value="<?= $arParams["AJAX_ID"] ?>">
            <input type="hidden" name="NEW_USER_COUNT" class="new-user-count" value="0">
            <? //FORM TITLE?>
            <div class="form__title"><?= Loc::getMessage("FORMS_TITLE") ?></div>
            <? // SHOW MESSAGE SUCCESS?>

            <div class="form__group_success  <?= ($arResult["FORM_SUCCESS"] ? "form_success" : "") ?> ">
                <div class="form__fields">
                    <div class="form__field">
                        <?= Loc::getMessage("MESSAGE_FROM_SEND_SUCCESS") ?>
                    </div>
                </div>
            </div>

            <div class="form__fields">
                <? //FORM FIELDS
                foreach ($arResult["IBLOCK"]["PROPERTIES"] as $arProp) {
                    if ($arProp["USER_TYPE"] != "HTML") {
                        if ($arProp["PROPERTY_TYPE"] == "S") {
                            //USER_PHONE
                            if ($arProp["CODE"] == "USER_PHONE") { ?>
                                <div class="form__field field">
                                    <input type="tel" class="field__input tel" name="<?= $arProp["CODE"] ?>"
                                           placeholder="<?= $arProp["NAME"] ?>"  >
                                </div>
                                <? //USER_EMAIL?>
                            <? } elseif ($arProp["CODE"] == "USER_EMAIL") { ?>
                                <div class="form__field field">
                                    <input type="email" class="field__input" name="<?= $arProp["CODE"] ?>"
                                           placeholder="<?= $arProp["NAME"] ?>">
                                </div>
                            <? } else { ?>
                                <div class="form__field field">
                                    <input type="text" class="field__input" name="<?= $arProp["CODE"] ?>"
                                           placeholder="<?= $arProp["NAME"] ?>">
                                </div>
                            <? }
                        }
                    }
                } ?>
            </div>

            <div class="form__group js-form-add-group">
                <div class="form__fields">
                    <div class="form__field">
                        Новый участник
                    </div>
                    <div class="form__field field">
                        <input type="text" class="field__input" name="FIO_NEW" placeholder="ФИО">
                    </div>
                    <div class="form__field field">
                        <input type="text" class="field__input" name="USER_JOB_TITLE_NEW" placeholder="Должность">
                    </div>
                    <div class="form__field field">
                        <input type="tel" id="phone" class="field__input tel" name="USER_PHONE_NEW" placeholder="Телефон">
                    </div>
                    <div class="form__field field">
                        <input type="email" class="field__input" name="USER_EMAIL_NEW" placeholder="Email">
                    </div>

                </div>
            </div>
            <div id="EXTRA_ADD" class="form__action hide-group-extra">
                <div class="form__add-btn_new js-form-add-btn" data-action="EXTRA_ADD">
                    Добавить
                </div>
                <div type="button" class="form__add-btn_new js-form-remove-fields">
                    Отмена
                </div>
            </div>

            <div class="form__action form__action--add">
                <button type="button" class="form__add-btn js-form-add-fields">
                    <svg class="form__add-btn-icon">
                        <use href="#svg-icon-plus"></use>
                    </svg>
                    <? // BTN ADD?>
                    <?= Loc::getMessage("FORMS_BTN_ADD_NEW_USER_TITLE") ?>
                </button>
            </div>
            <div class="wrap-new-users">
                <div class="form__group_users">

                </div>
            </div>
            <div class="form__action" id="REGISTRATION_USER">
                <? // BTN SUBMIT?>
                <button type="submit" data-action="ADD"
                        class="btn form__submit"><?= Loc::getMessage("FORMS_BTN_SUBMIT_TITLE") ?></button>
            </div>
            <div class="form__action">
                <label class="checkbox">
                    <input type="checkbox" name="CHBX_PRIVACY_POLICY" class="checkbox__input policy_privacy">
                    <svg class="checkbox__icon">
                        <use href="#svg-icon-checkbox"></use>
                    </svg>
                    <? //Privacy Policy?>
                    <span class="checkbox__text"><?= Loc::getMessage("FORMS_CHECKBOX_PP", ['#LINK#' => $arParams["LINK_PP"]]) ?></span>

                </label>
            </div>
            <div>
                <div class="policy_privacy_error">
                </div>
            </div>

        </form>

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
