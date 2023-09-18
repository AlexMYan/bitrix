<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

global $APPLICATION;

if (!$arResult["IMAGE_PATH"])
    return;

//Если картинок несколько
if (count($arResult["IMAGE_PATH"]) > 1) {
    ?>

    <div class="fotorama-photo">
        <div class="fotorama js-fotorama" data-auto="false">

            <img itemprop="image" src="<?= rawurlencode($arResult["IMAGE_PATH"][0]["DETAIL"]) ?>" alt=""
                 class="hidden-img">
            <?

            foreach ($arResult["IMAGE_PATH"] as $item) {

                if ($item["DETAIL"] && $item["PREVIEW"]) {
                    ?>

                    <a href="<?= $item["DETAIL"] ?>">

                        <img src="<?= $item["PREVIEW"] ?>" alt=""></a>

                <? } ?>
            <? } ?>
        </div>
    </div>

<?php } else { ?>
    <div class="product-photo">
        <div class="product-photo_img">
            <img itemprop="image" class="logoArea"
                 src="<?= $arResult["IMAGE_PATH"][0]["DETAIL"] ?>"
                 alt=""></div>
        <span class="product-photo_empty">Картинки нет</span>
    </div>
<?php } ?>

