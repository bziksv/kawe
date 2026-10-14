<?php
IncludeModuleLangFile(__FILE__);

class niges_cookiesaccept extends CModule
{
	var $MODULE_ID = 'niges.cookiesaccept';
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
	var $PARTNER_NAME;
	var $PARTNER_URI;
	var $strError = '';

	function __construct()
	{
		$arModuleVersion = array();
		include dirname(__FILE__).'/version.php';
		$this->MODULE_VERSION = $arModuleVersion['VERSION'];
		$this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
		$this->MODULE_NAME = GetMessage('niges.cookiesaccept_MODULE_NAME');
		$this->MODULE_DESCRIPTION = GetMessage('niges.cookiesaccept_MODULE_DESC');
		$this->PARTNER_NAME = GetMessage('niges.cookiesaccept_PARTNER_NAME');
		$this->PARTNER_URI = GetMessage('niges.cookiesaccept_PARTNER_URI');
	}

	function InstallDB($arParams = array())
	{
		if (!IsModuleInstalled($this->MODULE_ID)) {
			RegisterModule($this->MODULE_ID);
		}
		RegisterModuleDependences('main', 'OnEpilog', $this->MODULE_ID, 'CNigesCookiesAcceptPublic', 'OnEpilog');
		return true;
	}

	function UnInstallDB($arParams = array())
	{
		UnRegisterModuleDependences('main', 'OnEpilog', $this->MODULE_ID, 'CNigesCookiesAcceptPublic', 'OnEpilog');
		UnRegisterModule($this->MODULE_ID);
		return true;
	}

	function InstallEvents()
	{
		return true;
	}

	function UnInstallEvents()
	{
		return true;
	}

	function InstallFiles($arParams = array())
	{
		CopyDirFiles(
			$_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.$this->MODULE_ID.'/install/components/',
			$_SERVER['DOCUMENT_ROOT'].'/bitrix/components/',
			true,
			true
		);
		return true;
	}

	function UnInstallFiles()
	{
		if (is_dir($_SERVER['DOCUMENT_ROOT'].'/bitrix/components/niges/cookiesaccept/')) {
			DeleteDirFilesEx('/bitrix/components/niges/cookiesaccept/');
		}
		return true;
	}

	function DoInstall()
	{
		$this->InstallFiles();
		$this->InstallDB();
	}

	function DoUninstall()
	{
		$this->UnInstallDB();
		$this->UnInstallFiles();
	}
}
