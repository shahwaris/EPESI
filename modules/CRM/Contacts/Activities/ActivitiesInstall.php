<?php
/**
 * Activities history for Company and Contacts
 * @author Arkadiusz Bisaga <abisaga@telaxus.com>
 * @copyright Arkadiusz Bisaga <abisaga@telaxus.com>
 * @license SPL
 * @version 0.9
 * @package crm-contacts--activities
 */
defined("_VALID_ACCESS") || die('Direct access forbidden');

class CRM_Contacts_ActivitiesInstall extends ModuleInstall {

	public function install() {
		Base_LangCommon::install_translations($this->get_type());
		Base_ThemeCommon::install_default_theme('CRM/Contacts/Activities');
		Utils_RecordBrowserCommon::new_addon('company', 'CRM/Contacts/Activities', 'company_activities', 'Activities');
		Utils_RecordBrowserCommon::new_addon('contact', 'CRM/Contacts/Activities', 'contact_activities', 'Activities');
		return true;
	}
	
	public function uninstall() {
		Base_ThemeCommon::uninstall_default_theme('CRM/Contacts/Activities');
		Utils_RecordBrowserCommon::delete_addon('company', 'CRM/Contacts/Activities', 'company_activities');
		Utils_RecordBrowserCommon::delete_addon('contact', 'CRM/Contacts/Actitivies', 'contact_activities');
		return true;
	}
	
	public function version() {
		return array("0.9");
	}
	
	public function requires($v) {
		return array(
			array('name'=>'CRM/Tasks', 'version'=>0),
			array('name'=>'CRM/PhoneCall', 'version'=>0),
			array('name'=>'CRM/Calendar', 'version'=>0),
			array('name'=>'Utils/RecordBrowser', 'version'=>0),
			array('name'=>'Utils/Attachment', 'version'=>0),
			array('name'=>'CRM/Acl', 'version'=>0),
			array('name'=>'Base/Lang', 'version'=>0),
			array('name'=>'Base/Acl', 'version'=>0),
			array('name'=>'Data/Countries', 'version'=>0)
		);
	}
	
	public static function info() {
		return array(
			'Description'=>'Activities history for Company and Contacts',
			'Author'=>'Arkadiusz Bisaga <abisaga@telaxus.com>',
			'License'=>'SPL');
	}
	
	public static function simple_setup() {
		return true;
	}
	
}

?>