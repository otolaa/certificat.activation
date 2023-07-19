<?php
use \Bitrix\Main\Localization\Loc;
use \Bitrix\Main\Loader;
use \Bitrix\Main\Config\Option;

loc::loadMessages(__FILE__);

Class certificat_activation extends CModule
{
    var $MODULE_ID = "certificat.activation";
    var $MODULE_VERSION;
    var $MODULE_VERSION_DATE;
    var $MODULE_NAME;
    var $MODULE_DESCRIPTION;
    var $MODULE_CSS;

    public function __construct()
    {
        $arModuleVersion = [];
        include(__DIR__.'/version.php');
        $this->MODULE_VERSION = $arModuleVersion["VERSION"];
        $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
        $this->MODULE_NAME = Loc::getMessage("ca_module_name");
        $this->MODULE_DESCRIPTION = Loc::getMessage("ca_module_desc");
        $this->PARTNER_NAME = 'Alex Noodles';
        $this->PARTNER_URI = '//github.com/otolaa';
    }

    public function getPageLocal($page)
    {
        return str_replace('index.php', $page, Loader::getLocal('modules/'.$this->MODULE_ID.'/install/index.php'));
    }

    public function getStringText($obj)
    {
        return is_array($obj)?implode('<br>', $obj):$obj;
    }

    public function InstallDB($arParams = array())
    {
        global $DB, $APPLICATION;
        $this->errors = false;

        // Database tables creation
        $SQL = 'CREATE TABLE IF NOT EXISTS b_certificat_activation
                (
                    ID  INT(11) NOT NULL auto_increment,
                    ACTIVE_TO datetime NOT NULL,
                    CERTIFICAT_NUM	INT(11) NOT NULL,  
                    SECURITY_CODE	INT(11) NULL,                  
                    USER_EMAIL		VARCHAR(255) NULL,
                    ACTIVE	CHAR(1)	NOT NULL DEFAULT "Y",                    
                    PRIMARY KEY (ID)
                ) CHARACTER SET utf8 COLLATE utf8_unicode_ci;';

        $this->errors = $DB->Query($SQL, true);

        if($this->errors !== false) {
            $APPLICATION->ThrowException($this->getStringText($this->errors));
            return false;
        } else
            return true;
    }

    public function UnInstallDB($arParams = array())
    {
        global $DB, $APPLICATION;
        $this->errors = false;

        if (!array_key_exists("save_tables", $arParams) || ($arParams["save_tables"] != "Y")) {
            $this->errors = $DB->Query('DROP TABLE if exists b_certificat_activation', false);
        }

        if($this->errors !== false) {
            $APPLICATION->ThrowException($this->getStringText($this->errors));
            return false;
        }

        return true;
    }

    public function InstallFiles($arParams = [])
    {
        CopyDirFiles($this->getPageLocal('admin'), $_SERVER["DOCUMENT_ROOT"]."/bitrix/admin");
        CopyDirFiles($this->getPageLocal('components'), $_SERVER["DOCUMENT_ROOT"]."/local/components", true, true);
        return true;
    }

    public function UnInstallFiles()
    {
        DeleteDirFiles($this->getPageLocal('admin'), $_SERVER["DOCUMENT_ROOT"]."/bitrix/admin");
        DeleteDirFiles($this->getPageLocal('components'), $_SERVER["DOCUMENT_ROOT"]."/local/components");
        return true;
    }

    public function DoInstall()
    {
        global $APPLICATION;
        \Bitrix\Main\ModuleManager::registerModule($this->MODULE_ID);
        $this->InstallDB();
        $this->InstallFiles();
        Option::set($this->MODULE_ID, 'CA_ERROR', 'Сертификат занят, им уже воспользовались');
        $APPLICATION->IncludeAdminFile("Установка модуля ".$this->MODULE_ID, $this->getPageLocal('step.php'));
        return true;
    }

    public function DoUninstall()
    {
        global $APPLICATION;
        \Bitrix\Main\ModuleManager::unRegisterModule($this->MODULE_ID);
        $this->UnInstallDB();
        $this->UnInstallFiles();
        Option::delete($this->MODULE_ID); // Will remove all module variables
        $APPLICATION->IncludeAdminFile("Деинсталляция модуля ".$this->MODULE_ID, $this->getPageLocal('unstep.php'));
        return true;
    }
}