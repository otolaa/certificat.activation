<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die(); ?>

<h2 class="h4 mb-3">Шаг 1</h2>

<div class="mb-3">
    <label for="exampleInputEmail1" class="form-label">Email</label>
    <input type="email" class="form-control" id="exampleInputEmail1" name="email" value="<?=$arResult["email"]?>">
</div>

<div class="mb-3">
    <label for="exampleInputPassword1" class="form-label">Номер сертификата</label>
    <input type="text" class="form-control" id="exampleInputPassword1" name="certificat" value="<?=$arResult["certificat"]?>">
</div>

<? if ($arParams['CAPTCHA']=='Y') : ?>
    <? $captcha_sid = $APPLICATION->CaptchaGetCode(); ?>
    <div class="mb-3">
        <div class="row">
            <div class="col-3">
                <img src="/bitrix/tools/captcha.php?captcha_sid=<?=$captcha_sid?>">
            </div>
            <div class="col-9">
                <input type="text" class="form-control" name="captcha_word" placeholder="Введите символы на картинке">
                <input type="hidden" name="captcha_sid" value="<?=$captcha_sid?>">
            </div>
        </div>
    </div>
<? endif; ?>