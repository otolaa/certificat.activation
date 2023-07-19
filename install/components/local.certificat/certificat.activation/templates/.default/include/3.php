<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die(); ?>

<h2 class="h4 mb-3">Шаг 3</h2>
<div class="alert alert-secondary" role="alert">
    <h4 class="alert-heading">Все закончилось!? Сертификат активирован.</h4>
    <? if ($arResult['ITEM']) : ?>
        <hr>
        ID: <?=$arResult['ITEM']['ID']?><br>
        Номер сертификата: <?=$arResult['ITEM']['CERTIFICAT_NUM']?><br>
        Время действия: <?=$arResult['ITEM']['ACTIVE_TO']->toString()?><br>
    <? endif; ?>
</div>