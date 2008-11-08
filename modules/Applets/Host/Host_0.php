<?php
/**
 * Gets host ip or domain
 * @author pbukowski@telaxus.com
 * @copyright pbukowski@telaxus.com
 * @license SPL
 * @version 0.1
 * @package applets-host
 */
defined("_VALID_ACCESS") || die('Direct access forbidden');

class Applets_Host extends Module {

	public function body() {
	
	}
	
	public function applet() {
		$f = $this->init_module('Libs/QuickForm');
		$l = $this->init_module('Base/Lang');
		$t = $f->createElement('text','t');
		$ok = $f->createElement('submit','ok',$l->ht('OK'));
		$f->addGroup(array($t,$ok),'w');
		$f->display();
		
		$msg = & $this->get_module_variable('msg');
		if($f->validate()) {
			$w = $f->exportValues();
			$w = $w['w']['t'];
			if(ip2long($w)===false) {
				$ip = gethostbynamel($w);
				if($ip) {
					$msg = '';
					foreach($ip as $i)
						$msg .= $i.'<br>';
				} else 
					$msg = $l->t('No such domain');
			} else {
				$domain = gethostbyaddr($w);
				if($domain!=$w)
					$msg = $domain;
				else
					$msg = $l->t('No such ip entry');
			}
		}
		print($msg);
	}
}

?>