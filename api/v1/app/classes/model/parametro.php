<?php

namespace Model;

use Orm\Observer_Typing;

/**
 * @property mixed		$valor
 * @property string		$data_type
 * @property string		$observaciones
 */
class Parametro extends Base {
	protected static $_table_name = 'parametros';
	protected static $_primary_key = array('id');

	protected static $_properties = array(
		'id',
		'valor' => array(
			'data_type' => 'text',
		),
		'data_type' => array(
			'data_type' => 'varchar',
			'validation' => array('required', 'max_length' => array(20))
		),
		'observaciones' => array(
			'data_type' => 'varchar',
			'validation' => array('max_length' => array(255)),
		),
		'created_at' => array(
			'data_type' => 'time_mysql'
		),
		'updated_at' => array(
			'data_type' => 'time_mysql'
		),
		'deleted_at' => array(
			'data_type' => 'time_mysql'
		)
	);

	public function _event_after_save() {
		$this->encode_decode('before');
	}

	public function _event_after_load() {
		$this->_event_before_save();
	}

	public function _event_before_save() {
		$this->encode_decode('after');
	}

	private function encode_decode($event_type) {
		foreach (Observer_Typing::$regex_methods as $match => $methods) {
			$method = !empty($methods[$event_type]) ? $methods[$event_type] : false;
			if ($method === false) {
				continue;
			}
			if ($method) {
				if (preg_match_all($match, $this->data_type, $matches) > 0) {
					$this->valor = call_user_func($method, $this->valor, array('data_type', $this->data_type, 'json_assoc' => true), $matches);
					continue;
				}
			}
		}
	}

	public static function val($id_parametro) {
		/** @var Parametro $val */
		$val = static::find($id_parametro);
		if (is_null($val)) {
			return null;
		}
		return $val->valor;
	}
}

?>