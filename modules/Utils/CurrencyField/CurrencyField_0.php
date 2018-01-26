<?php
/**
 * @author Arkadiusz Bisaga <abisaga@telaxus.com>
 * @copyright Copyright &copy; 2006, Telaxus LLC
 * @version 1.0
 * @license MIT
 * @package epesi-utils
 * @subpackage CurrencyField
 */
defined("_VALID_ACCESS") || die('Direct access forbidden');

class Utils_CurrencyField extends Module {
	private static $positions;
	private static $active;
	
	public function construct() {
		self::$positions = array(0=>__('After'), 1=>__('Before'));
		self::$active = array(1=>__('Yes'), 0=>__('No'));
	}
	
	public function admin() {
		if($this->is_back()) {
			if($this->parent->get_type()=='Base_Admin')
				$this->parent->reset();
			else
				location(array());
			return;
		}

		$fiat_gb = $this->init_module('Utils_GenericBrowser',null,'currencies');
		$fiat_gb->set_table_columns([
            ['name'=>__('ID')],
			['name'=>__('Code')],
			['name' => __('Full Name')],
			['name'=>__('Default')],
			['name'=>__('Active')]
		]);
		$ret = DB::Execute('SELECT id, code, default_currency, active FROM utils_currency ORDER BY id ASC');
		while($row = $ret->FetchRow()) {
			$fiat_gb_row = $fiat_gb->get_new_row();
			$fiat_gb_row->add_data_array(array(
                    $row['id'],
					$row['code'],
					Utils_CommonDataCommon::get_value('Currencies_Codes/'.$row['code']),
					self::$active[$row['default_currency']],
					self::$active[$row['active']]
				));
			$fiat_gb_row->add_action($this->create_callback_href(array($this, 'edit_currency'),array($row['id'])),'edit');
		}
		Base_ActionBarCommon::add('add', __('New Currency'), $this->create_callback_href(array($this, 'add_currency'), array(null)));
		Base_ActionBarCommon::add('add', __('Add Cryptocurrency'), $this->create_callback_href(array($this, 'add_cryptocurrency'), array(null)));
		Base_ActionBarCommon::add('back', __('Back'), $this->create_back_href());
		$this->display_module($fiat_gb);

		$cr_gb = $this->init_module('Utils_GenericBrowser', null, 'cryptocurrencies');
        $cr_gb->set_table_columns([
            ['name'=>__('ID')],
            ['name'=>__('Code')],
            ['name'=>__('Cryptocurrency Name')],
            ['name'=>__('Default')],
            ['name'=>__('Active')]
        ]);
        $ret = DB::Execute('SELECT * FROM utils_cryptocurrencies ORDER BY id ASC');
        while($row = $ret->FetchRow()) {
        	$cr_gb_row = $cr_gb->get_new_row();
        	$cr_gb_row->add_data_array([
        		$row['id'],
				$row['code'],
                Utils_CommonDataCommon::get_value('Cryptocurrencies_Codes/'.$row['code']),
                self::$active[$row['default_currency']],
                self::$active[$row['active']]
			]);
        	$cr_gb_row->add_action($this->create_callback_href([$this,'edit_cryptocurrency'],[$row['id']]),'edit');
        }
		$this->display_module($cr_gb);
	}

	public function add_currency() {
        if ($this->is_back()) return false;
        $form = $this->init_module('Libs_QuickForm');
        $options = self::get_currency_options();
        $form->addElement('select', 'currency', __('Currency'), $options);
        $form->addElement('select', 'default_currency', __('Default'), self::$active);
        $form->addElement('select', 'active', __('Active'), self::$active);

        if ($form->validate()) {
            $vals = $form->exportValues();
            if(isset($vals['default_currency']) && $vals['default_currency']) DB::Execute('UPDATE utils_currency SET default_currency=0');
            $vals = [
            	' ',
				$vals['currency'],
				'.',
				' ',
				2,
				0,
                htmlspecialchars($vals['active']),
                isset($vals['default_currency'])?htmlspecialchars($vals['default_currency']):1
            ];
            $sql = 'INSERT INTO utils_currency (symbol, code, decimal_sign, thousand_sign, decimals, pos_before, active, default_currency) VALUES (%s, %s, %s, %s, %d, %d, %d, %d)';
            DB::Execute($sql, $vals);
        }
        $form->display();
        Base_ActionBarCommon::add('back', __('Back'), $this->create_back_href());
        Base_ActionBarCommon::add('save', __('Save'), $form->get_submit_form_href());
        return true;
	}
	
	public function edit_currency($id) {
		if ($this->is_back()) return false;
		$form = $this->init_module('Libs_QuickForm');
		$form->addElement('static', null, Utils_CurrencyFieldCommon::get_code($id));
		$form->addElement('select', 'default_currency', __('Default'), self::$active);
		$form->addElement('select', 'active', __('Active'), self::$active);

		$defs = DB::GetRow('SELECT active, default_currency FROM utils_currency WHERE id=%d', [$id]);
		$form->setDefaults($defs);
		if($defs['default_currency']) $form->freeze(array('default_currency'));

		if ($form->validate()) {
			$vals = $form->exportValues();
			if(isset($vals['default_currency']) && $vals['default_currency']) DB::Execute('UPDATE utils_currency SET default_currency=0');
			$vals = [
				htmlspecialchars($vals['active']),
				isset($vals['default_currency'])?htmlspecialchars($vals['default_currency']):1,
				$id
			];
			$sql = 'UPDATE utils_currency SET '.
							'active=%d,'.
							'default_currency=%d'.
							' WHERE id=%d';
			DB::Execute($sql, $vals);
		}
		$form->display();
		Base_ActionBarCommon::add('back', __('Back'), $this->create_back_href());
		Base_ActionBarCommon::add('save', __('Save'), $form->get_submit_form_href());
		return true;
	}

	public function edit_cryptocurrency($id) {

        if ($this->is_back()) return false;
        $form = $this->init_module('Libs_QuickForm');
        $curr = Utils_CurrencyFieldCommon::get_cryptocurrency_by_id($id);
        $name = $curr.' - '.Utils_CommonDataCommon::get_value('Cryptocurrencies_Codes/'.$curr);

		$form->addElement('static', null, '<h3>'.$name.'</h3>');
        $form->addElement('select', 'default_currency', __('Default Cryptocurrency'), self::$active);
        $form->addElement('select', 'active', __('Active'), self::$active);

        $defs = DB::GetRow('SELECT * FROM utils_cryptocurrencies WHERE id=%d', [$id]);
        $form->setDefaults($defs);
        if($defs['default_currency']) $form->freeze(['default_currency']);
        if ($form->validate()) {
            $vals = $form->exportValues();
            if(isset($vals['default_currency']) && $vals['default_currency']) DB::Execute('UPDATE utils_cryptocurrencies SET default_currency=0');
            $vals = [
                htmlspecialchars($vals['active']),
                isset($vals['default_currency']) ? htmlspecialchars($vals['default_currency']) : 1,
            	$id
			];
            $sql = 'UPDATE utils_cryptocurrencies SET '.
                'active=%d,'.
                'default_currency=%d'.
                ' WHERE id=%d';
            DB::Execute($sql, $vals);
        }
        $form->display();
        Base_ActionBarCommon::add('back', __('Back'), $this->create_back_href());
        Base_ActionBarCommon::add('save', __('Save'), $form->get_submit_form_href());
        return true;
	}

	public function add_cryptocurrency() {
        if ($this->is_back()) return false;
        $options = self::get_crypto_options();
		$form = $this->init_module(Libs_QuickForm::module_name());
		$form->addElement('select', 'cryptocurrencies', __('Add Cryptocurrency').': ', $options);
		$form->addElement('select', 'default_currency', __('Default cryptocurrency'), self::$active);
		$form->addElement('select', 'active', __('Active'), self::$active);

		if ($form->validate()) {
			$vals = $form->exportValues();
			if(isset($vals['default_currency']) && $vals['default_currency']) DB::Execute('UPDATE utils_cryptocurrencies SET default_currency=0');
			$vals = [
				htmlspecialchars($vals['cryptocurrencies']),
				htmlspecialchars($vals['active']),
				isset($vals['default_currency']) ? htmlspecialchars($vals['default_currency']) : 1
			];
			$sql = 'INSERT INTO utils_cryptocurrencies ('.
								'code, '.
								'active, '.
								'default_currency'.
							') VALUES ('.
								'%s, '.
								'%d, '.
								'%d'.
							')';
            DB::Execute($sql, $vals);
		}
		$form->display();
        Base_ActionBarCommon::add('back', __('Back'), $this->create_back_href());
        Base_ActionBarCommon::add('save', __('Save'), $form->get_submit_form_href());
		return true;
	}

	public function get_crypto_options() {
        $options = Utils_CommonDataCommon::get_array('Cryptocurrencies_Codes');
        $local_crypto = array_values(Utils_CurrencyFieldCommon::get_all_cryptocurrencies());

        foreach($local_crypto as $k => $v) {
            if(array_search($v, $options) !== false) unset($options[$v]);
        }

        foreach($options as $k => $v) $options[$k] = $k.' - '.$v;
        return $options;
	}

	public static function get_currency_options() {
        $options = Utils_CommonDataCommon::get_array('Currencies_Codes');
        $local_currencies = array_values(Utils_CurrencyFieldCommon::get_all_currencies());
//
        foreach($local_currencies as $k => $v) {
            if(key_exists($v, $options)) {
            	unset($options[$v]);
            }
        }

        foreach($options as $k => $v) $options[$k] = $k.' - '.$v;
		return $options;
	}

}

?>
