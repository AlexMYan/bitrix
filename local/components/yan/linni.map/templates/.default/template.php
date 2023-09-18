<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

global $APPLICATION;

if (empty($arResult["ITEMS"])) {
    return;
}
?>
<style>
    #map{
        width: 100%;
        height: 400px;
    }
</style>
<section class="where_to_buy" id="where_to_buy">
    <div class="container">
        <div class="content">
            <div class="text">
                <h2 class="where_to_buy__subtitle subtitle"><?=$arParams["BLOCK_TITLE"]?></h2>

                <? //MAP?>
                <div id="map"></div>
            </div>
        </div>
</section>

<div class="accordion js-accordion">
    <div class="container">
        <? foreach ($arResult["COUNTRY_NAME"] as $groupKey => $groupValue){ ?>
            <? if (empty($groupKey)) {
                continue;
            } ?>
            <div class="accordion__item js-accordion__item">

                <div class="accordion__btn js-accordion__btn">
                    <?= $groupValue ?>
                </div>
                <div class="accordion__body js-accordion__body">
                    <div class="accordion__grid-wrap js-accordion__wrap">
                        <div class="accordion__grid">
                            <? foreach ( $arResult["COUNTRY"][$groupKey] ?? [] as $name => $value){ ?>
                                <div class="accordion__col">
                                    <div class="accordion__col-item">
                                        <div class="accordion__col-item-name"><?= $value['NAME'] ?></div>
                                        <div class="accordion__col-item-text">Адрес
                                            офиса: <?= $value['ADRESS'] ?></div>
                                    </div>
                                </div>
                            <? } ?>
                        </div>
                    </div>
                </div>
            </div>
        <? } ?>
    </div>
</div>


    <script>
        function init() {
            //создаем карту
            var myMap = new ymaps.Map('map', {
                center: [<?=$arParams["MAP_COORDS_CENTER"]?>],
                zoom: <?=$arParams["MAP_ZOOM_CENTER"]?>,
               // controls: []
            });

          //  myMap.controls.remove('geolocationControl'); // удаляем геолокацию
            myMap.controls.remove('searchControl'); // удаляем поиск
          //  myMap.controls.remove('trafficControl'); // удаляем контроль трафика
            myMap.controls.remove('typeSelector'); // удаляем тип
           // myMap.controls.remove('fullscreenControl'); // удаляем кнопку перехода в полноэкранный режим
           // myMap.controls.remove('zoomControl'); // удаляем контрол зуммирования
            myMap.controls.remove('rulerControl'); // удаляем контрол правил
           // myMap.behaviors.disable(['scrollZoom']); // отключаем скролл карты (опционально)

            var maps = new JCPickupPage({
                container: 'where_to_buy',
                map: myMap,
                points: <?=json_encode($arResult["ITEMS"])?>,
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
