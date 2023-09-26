<?

Class yan_hiddenprice extends CModule
{
    var $MODULE_ID = "yan.hiddenprice";
    var $MODULE_VERSION;
    var $MODULE_VERSION_DATE;
    var $MODULE_NAME;
    var $MODULE_DESCRIPTION;
    var $MODULE_CSS;

    function yan_hiddenprice()
    {
        $arModuleVersion = array();

        $path = str_replace("\\", "/", __FILE__);
        $path = substr($path, 0, strlen($path) - strlen("/index.php"));
        include($path."/version.php");

        if (is_array($arModuleVersion) && array_key_exists("VERSION", $arModuleVersion))
        {
            $this->MODULE_VERSION = $arModuleVersion["VERSION"];
            $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
        }
        $this->PARTNER_NAME = GetMessage("YAN_HIDDEN_PRICE_PARTNER");
        $this->PARTNER_URI = GetMessage("YAN_HIDDEN_PRICE_PARTNER_URI");
        $this->MODULE_NAME = GetMessage("YAN_HIDDEN_PRICE_MODULE_NAME");
        $this->MODULE_DESCRIPTION = GetMessage("YAN_HIDDEN_PRICE_DESCRIPTION");
    }

    function InstallFiles()
    {
        return true;
    }

    function UnInstallFiles()
    {
        return true;
    }
    function InstallDB(){
	
	    return true;
	}
	function UnInstallDB(){
		
	    return true; 
	}
    function InstallEvents() {

        return true;

    }

    function UnInstallEvents() {
        
        return true;
    }
	
    function DoInstall()
    {
        global $DOCUMENT_ROOT, $APPLICATION;
        $this->InstallFiles();
		$this->InstallDB();
        RegisterModule($this->MODULE_ID);
        $APPLICATION->IncludeAdminFile(GetMessage("YAN_HIDDEN_PRICE_INSTALL_TITLE"), $DOCUMENT_ROOT."/bitrix/modules/".$this->MODULE_ID."/install/step.php");
    }

    function DoUninstall()
    {
        global $DOCUMENT_ROOT, $APPLICATION;
        $this->UnInstallFiles();
		$this->UnInstallDB();
        UnRegisterModule($this->MODULE_ID);
        $APPLICATION->IncludeAdminFile(GetMessage("YAN_HIDDEN_PRICE_UNINSTALL_TITLE"), $DOCUMENT_ROOT."/bitrix/modules/".$this->MODULE_ID."/install/unstep.php");
    }
}
?>
