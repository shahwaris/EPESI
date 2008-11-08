<?php
/**
 * @author Kuba Slawinski <kslawinski@telaxus.com>
 * @copyright Copyright &copy; 2006, Telaxus LLC
 * @version 0.9
 * @license SPL
 * @package epesi-utils
 * @subpackage gallery
 */
defined("_VALID_ACCESS") || die('Direct access forbidden');

class Utils_GalleryInstall extends ModuleInstall {
	public function install() {
		Base_ThemeCommon::install_default_theme('Utils/Gallery');
		return true;
	}

	public function uninstall() {
		return true;
	}

	public function version() {
		return array('1.0.0');
	}
	public function requires($v) {
		return array(
			array('name'=>'Libs/Lytebox', 'version'=>0),
			array('name'=>'Utils/Image', 'version'=>0)
		);
	}
}

?>
