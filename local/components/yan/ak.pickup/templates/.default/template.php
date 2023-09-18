<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

global $APPLICATION;

if (empty($arResult["ITEMS"])) {
    return;
}
?>

<div class="container" id="conteinerPickup">
    <div id="pickup" class="pickup js-pickup">
        <h1 class="pickup__title">   <?= Loc::getMessage('TEMPLATE_BLOCK_TITLE'); ?></h1>
        <div class="pickup__main">
            <div class="pickup__left">
                <form class="pickup__form" action="/information/samovyvoz/" method="get">
                    <div class="select-mob js-select-mob">
                        <div class="select-mob__btn js-select-mob__btn">
                            <span class="select-mob__text js-select-mob__btn-text"><?= Loc::getMessage('CITY_ALL_TITLE'); ?></span>
                            <i class="select-search__icon"></i>
                        </div>
                        <div class="select-mob__modal js-select-mob__modal">
                            <div class="pickup__modal-header">
                                <div class="pickup__modal-header-title js-select-mob__close" role="button">
                                    <i class="pickup__modal-header-title-icon fa fa-angle-left"></i>
                                    <?= Loc::getMessage('CITY_TITLE'); ?>
                                </div>
                                <div role="button" class="pickup__modal-btn-close js-select-mob__close"
                                     type="button"></div>
                            </div>
                            <? //CITIES ?>
                            <? if ($arResult["CITIES"]) { ?>
                                <div class="select-mob__items">
                                    <div class="select-mob__item js-select-mob__option active"
                                         data-value="all"><?= Loc::getMessage('CITY_ALL_TITLE'); ?></div>

                                    <? foreach ($arResult["CITIES"] as $key => $city) { ?>
                                        <div class="select-mob__item js-select-mob__option"
                                             data-value="<?= $key ?>"><?= $city ?></div>
                                    <? } ?>

                                </div>
                            <? } ?>
                        </div>
                    </div>
                    <? //CITIES ?>
                    <? if ($arResult["CITIES"]) { ?>
                        <div class="select-search select-search--big select-search--hidden-mob js-select-search">
                            <input type="hidden" name="city" class="js-select-search-input-hidden js-pickup-select"
                                   value="all">
                            <input type="text" class="select-search__input js-select-search-input"
                                   placeholder="Выберите город" value="Все города">
                            <i class="select-search__icon"></i>

                            <div class="select-search__dropdown">
                                <div class="select-search__options js-select-search-options">
                                    <div class="select-search__option js-select-search-option" value="all"><?= Loc::getMessage('CITY_ALL_TITLE'); ?></div>

                                    <? foreach ($arResult["CITIES"] as $key => $city) { ?>
                                        <div class="select-search__option js-select-search-option" data-city="<?= $city ?>" value="<?= $key ?>"><?= $city ?></div>
                                    <? } ?>

                                </div>
                            </div>
                        </div>
                    <? } ?>
                </form>
                <div class="pickup__items scrollbar">
                    <? foreach ($arResult["ITEMS"] as $key => $ITEM) { ?>

                        <div class="pickup__item js-pickup__item" data-coord-lat="<?=$ITEM["position"][0]?>"
                             data-item="<?=
                             $key ?>"
                             data-src="">
                            <div class="pickup__item-address"><?= $ITEM["ADRESS"] ?></div>
                            <? if(!empty($ITEM["TIME_WORK"])){?>
                                <div class="pickup__item-schedule">
                                    <? foreach ($ITEM["TIME_WORK"] as $work) {?>
                                        <div><?=$work?></div>
                                    <? }?>
                                </div>
                            <? }?>
                            <? if(!empty($ITEM["PHONE"])){?>
                                <div class="pickup__item-phones">
                                    <? foreach ($ITEM["PHONE"] as $phone) {?>
                                    <div class="pickup__item-phone"><?=$phone?></div>
                                    <? }?>
                                </div>
                            <? }?>

                        </div>

                    <? } ?>

                </div>
            </div>
            <? $first = reset($arResult["ITEMS"]);
            ?>
            <div class="pickup__right js-pickup__right">
                <div class="pickup__modal-header">
                    <div class="pickup__modal-header-title js-pickup__btn-close" role="button">
                        <i class="pickup__modal-header-title-icon fa fa-angle-left"></i>
                        <?= Loc::getMessage('TEMPLATE_BLOCK_TITLE'); ?>
                    </div>
                    <div role="button" class="pickup__modal-btn-close js-pickup__btn-close" type="button"></div>
                </div>
                <div class="pickup__modal-main">
                    <div class="pickup__modal-address"><?= $first["ADRESS"] ?></div>
                    <div class="pickup__modal-info">
                        <? if(!empty($first["TIME_WORK"])){?>
                            <div class="pickup__modal-schedule">
                                <? foreach ($first["TIME_WORK"] as $work) {?>
                                    <div><?=$work?></div>
                                <? }?>
                            </div>
                        <? }?>


                        <? if(!empty($first["PHONE"])){?>
                            <div class="pickup__modal-phones">
                                <? foreach ($first["PHONE"] as $phone) {?>
                                    <a href="#"><?=$phone?></a>
                                <? }?>
                            </div>
                        <? }?>
                        <div class="pickup__modal-pay"><?= $first["PAY"] ?></div>
                        <div class="pickup__modal-description"><?= $first["DESCRIPTION_TEXT"] ?></div>
                    </div>
                </div>

                <div class="pickup__map-wrap">
                    <div class="pickup__iframe" id="map">

                    </div>

                </div>


                <div class="pickup__modal-action">
                    <button class="pickup__modal-action-btn js-pickup__btn-close"
                            type="button"><?= Loc::getMessage('MOBILE_BTN_CLOSE_TITLE'); ?></button>
                </div>
            </div>
        </div>


    </div>

    <script>
        function init() {
            //создаем карту
            var myMap = new ymaps.Map('map', {
                center: [53.906717, 27.545352],
                zoom: 10,

            });

            var maps = new JCPickupPage({
                container: 'conteinerPickup',
                map: myMap,
                points: <?=json_encode($arResult["ITEMS"])?>,
                cities: <?=json_encode($arResult["CITIES"])?>,
                componentPath: '<?=CUtil::JSEscape($componentPath)?>',

            });
        }

        ymaps.ready(init);


    </script>

    <script>


        const $pickup = $('.js-pickup');
        if ($pickup.length) {
            $pickup.each(function () {
                let $self = $(this);
                let $items = $self.find('.js-pickup__item');
                let $right = $self.find('.js-pickup__right');
                let $iframe = $self.find('.js-pickup__iframe');
                let $btnClose = $self.find('.js-pickup__btn-close');

                let $mobSelect = $self.find('.js-select-mob');
                let $mobSelectBtn = $mobSelect.find('.js-select-mob__btn');
                let $mobSelectBtnText = $mobSelect.find('.js-select-mob__btn-text');
                let $mobSelectModal = $mobSelect.find('.js-select-mob__modal');
                let $mobSelectClose = $mobSelect.find('.js-select-mob__close');
                let $mobSelectOption = $mobSelect.find('.js-select-mob__option');

                $mobSelectBtn.on('click', function () {
                    $mobSelectModal.addClass('open');


                });

                $mobSelectClose.on('click', function () {
                    $mobSelectModal.removeClass('open');
                });


                $mobSelectOption.on('click', function () {
                    let optionValue = $(this).attr('data-value');

                    let optionText = $(this).text();
                    $(this).siblings().removeClass('active');
                    $(this).addClass('active');
                    $mobSelectBtnText.text(optionText);
                    $mobSelectModal.removeClass('open');
                });


                $btnClose.each(function () {
                    $(this).on('click', function () {
                        $right.removeClass('open');
                        $items.siblings().removeClass('active');
                    });
                });

                $items.each(function () {
                    $(this).on('click', function () {

                        $right.addClass('open');
                        let pos = this.getAttribute('data-item');
                        if (!!pos) {
                            window.JCPickupPage.prototype.setModalInfo(<?=json_encode($arResult["ITEMS"])?>, pos);
                        }


                        $(this).siblings().removeClass('active');
                        $(this).addClass('active');
                        if ($iframe.length) {
                            $iframe.attr("src", $(this).attr('data-src'));
                        }
                    });
                });
            });
        }
    </script>
</div>
