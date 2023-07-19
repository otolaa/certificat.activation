<?php

use \Bitrix\Main\Localization\Loc;
use \Bitrix\Main\Loader;

loc::loadMessages(__FILE__);

if ($APPLICATION->GetGroupRight("certificat.activation") > "D") {

    require_once(Loader::getLocal('modules/certificat.activation/prolog.php'));

    // the types menu  dev.1c-bitrix.ru/api_help/main/general/admin.section/menu.php
    $aMenu = [
        "parent_menu" => "global_menu_settings", // global_menu_content - раздел "Контент" global_menu_settings - раздел "Настройки"
        "section" => "certificat.activation",
        "sort" => 400,
        "module_id" => "certificat.activation",
        "text" => 'Активации сертификатов',
        "title"=> 'Модуль для активации сертификатов',
        "icon" => "fileman_menu_icon", // sys_menu_icon bizproc_menu_icon util_menu_icon
        "page_icon" => "fileman_menu_icon", // sys_menu_icon bizproc_menu_icon util_menu_icon
        "items_id" => "menu_certificat_activation",
        "items" => [
            [
                "text" => 'Настройки',
                "title" => 'Настройки',
                "url" => "settings.php?mid=certificat.activation&lang=".LANGUAGE_ID,
            ],
            [
                "text" => 'Список сертификатов',
                "title" => 'Список сертификатов',
                "url" => "perfmon_table.php?lang=".LANGUAGE_ID."&table_name=b_certificat_activation",
            ],
        ]
    ];

    return $aMenu;
}

return false;