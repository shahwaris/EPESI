<?php
/**
 * @author Paul Bukowski <pbukowski@telaxus.com>
 * @copyright Copyright &copy; 2007, Telaxus LLC
 * @version 1.0
 * @license SPL
 * @package epesi-tests
 * @subpackage shared-unique-href
 */
defined("_VALID_ACCESS") || die('Direct access forbidden');

class Tests_SharedUniqueHref_a extends Module {
	
	public function body() {
		print('Submodule received: '.$this->get_unique_href_variable('test'));
	}
}
?>


