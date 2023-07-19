<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
//
$this->setFrameMode(true); ?>
<noindex>
    <? if (count($arResult['error'])) : ?>
        <div class="alert alert-danger mb-3"><?=implode('<br/>', $arResult['error'])?></div>
    <? endif; ?>

    <form name="<?=$arResult["SID"]?>" id="form_<?=$arResult["SID"]?>" action="<?=$arResult["POST_FORM_ACTION"]?>"
          role="form" method="POST" novalidate>

        <? // themes fields
        if ($arResult["SID"] == 'ca1') {
            include __DIR__.'/include/1.php';
        } elseif ($arResult["SID"] == 'ca2') {
            include __DIR__.'/include/2.php';
        } elseif ($arResult["SID"] == 'ca3') {
            include __DIR__.'/include/3.php';
        }
        // end themes fields
        ?>

        <? if ($arResult["SID"] !== 'ca3') { ?>
        <div class="buttons">
            <input type="hidden" name="PARAMS_HASH" value="<?=$arResult["PARAMS_HASH"]?>">
            <input type="hidden" name="go" value="<?=$arResult["SID"]?>">
            <button type="submit" class="btn btn-primary" value="Y">Отправить</button>
        </div>
        <? } ?>
    </form>
</noindex>
