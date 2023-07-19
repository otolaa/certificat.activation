<?php
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Context;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Type\DateTime;
use Local\Certificat\ActivationTable;
//
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

Loc::loadMessages(__FILE__);

class PageContentFormSidClass extends \CBitrixComponent
{
    public function onPrepareComponentParams($arParams)
    {
        $result = [
            "CACHE_TYPE" => isset($arParams["CACHE_TYPE"])?$arParams["CACHE_TYPE"]:"N",
            "CACHE_TIME" => isset($arParams["CACHE_TIME"])?$arParams["CACHE_TIME"]:0, // zero hour
            "CACHE_GROUPS" => isset($arParams["CACHE_GROUPS"])?$arParams["CACHE_GROUPS"]:"N",
            //
            "CAPTCHA" => isset($arParams["CAPTCHA"])?$arParams["CAPTCHA"]:'Y',
        ];

        return $result;
    }

    public function checkEmail($USER_EMAIL)
    {
        if (!filter_var($USER_EMAIL, FILTER_VALIDATE_EMAIL) === false) {
            return true;
        } else {
            return false;
        }
    }

    public function getCacheParams($arParams)
    {
        $arCacheParams = [];
        foreach ($arParams as $key => $value)
        {
            if (in_array($key, ["OK_MESSAGE", "tokenKey"])) continue;
            if(substr($key, 0, 1) != "~")
            {
                $arCacheParams[$key] = $value;
            }
        }
        return $arCacheParams;
    }

    public function executeComponent()
    {
        global $APPLICATION;
        $this->arResult["PARAMS_HASH"] = md5(serialize($this->getCacheParams($this->arParams)) . $this->GetTemplateName());
        $this->arResult["POST_FORM_ACTION"] = $APPLICATION->GetCurPage(false);
        $this->arResult["SID"] = 'ca1';

        // move the message calling algorithm here POST !?
        $request = Context::getCurrent()->getRequest();
        $get['method'] = $request->getRequestMethod();
        $get['go'] = $request->getPost("go");

        $error = $this->arResult['error'] = [];
        if ($get['method'] == "POST" && strlen($get['go']) > 0 && $this->arResult["PARAMS_HASH"] === $request->getPost("PARAMS_HASH")) {

            // validate oll
            $this->arResult["SID"] = $get['go'];

            // step1
            if ($get['go'] == 'ca1') {
                $this->arResult['email'] = $request->getPost("email");
                $this->arResult['certificat'] = $request->getPost("certificat");

                if (strlen($this->arResult['email']) == 0)
                    $error['email'] = 'Обязательное поле: email';

                if (strlen($this->arResult['email']) > 0  && !$this->checkEmail($this->arResult['email']))
                    $error['email'] = 'Формат поля не email: '.$this->arResult['email'];

                if (strlen($this->arResult['certificat']) == 0)
                    $error['certificat'] = 'Обязательное поле: Номер сертификата';

                // the valid Bitrix CAPTCHA
                if ($this->arParams['CAPTCHA'] == 'Y') {
                    if (!$APPLICATION->CaptchaCheckCode($request->getPost("captcha_word"), $request->getPost("captcha_sid")))
                        $error['CAPTCHA'] = 'Символы с картинки';
                }

                if (strlen($this->arResult['certificat']) > 0  && Loader::IncludeModule("certificat.activation")) {
                    $row = ActivationTable::getList([
                        'select' => ['ID','CERTIFICAT_NUM','ACTIVE_TO','ACTIVE'],
                        'filter' => ['=CERTIFICAT_NUM'=>$this->arResult['certificat']],
                    ])->fetch();

                    if (!$row)
                        $error['certificat'] = 'Отсутствует сертификат';

                    if ($row) {
                        $this->arResult['ID'] = $row['ID'];

                        $dateTime = new DateTime();
                        $t0 = $dateTime->getTimestamp();

                        if ($row['ACTIVE_TO']->getTimestamp() < $t0)
                            $error['certificat'] = 'Сертификат до '.$row['ACTIVE_TO']->toString();

                        if ($row['ACTIVE'] !== 'Y')
                            $error['certificat'] = 'Сертификат занят, им уже воспользовались';
                    }
                }
            }

            // step2
            if ($get['go'] == 'ca2') {
                $this->arResult['code'] = $request->getPost("code");

                if (strlen($this->arResult['code']) == 0)
                    $error['code'] = 'Обязательное поле: Код подтверждения';

                if (strlen($this->arResult['code']) > 0  && Loader::IncludeModule("certificat.activation")) {
                    $row = ActivationTable::getList([
                        'select' => ['ID','CERTIFICAT_NUM','ACTIVE_TO','ACTIVE'],
                        'filter' => ['=SECURITY_CODE'=>$this->arResult['code']],
                    ])->fetch();

                    if ($row) {
                        $this->arResult['ID'] = $row['ID'];
                        $this->arResult['ITEM'] = $row;
                    } else
                        $error['code'] = 'Отсутствует данный Код подтверждения';
                }
            }
            // end validate

            if (is_array($get) && count($error) == 0) {

                if ($get['go'] == 'ca1') {
                    $this->arResult["SID"] = 'ca2';
                } elseif ($get['go'] == 'ca2') {
                    $this->arResult["SID"] = 'ca3';
                }

                // ca1
                if ($get['go'] == 'ca1' && $this->arResult['ID'] && Loader::IncludeModule("certificat.activation")) {

                    $this->arResult['SECURITY_CODE'] = mt_rand(123456, 654321);

                    ActivationTable::update($this->arResult['ID'], [
                        'SECURITY_CODE'=>$this->arResult['SECURITY_CODE'],
                        'USER_EMAIL'=>$this->arResult['email'],
                    ]);
                }

                // ca2
                if ($get['go'] == 'ca2' && $this->arResult['ID'] && Loader::IncludeModule("certificat.activation"))
                    ActivationTable::update($this->arResult['ID'], ['ACTIVE'=>'N']);

            } else
                $this->arResult['error']  = $error;
        }

        // include default template
        $this->IncludeComponentTemplate();
    }
}