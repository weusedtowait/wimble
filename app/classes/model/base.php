<?php

namespace Model;
use Orm\Model_Soft;

/**
 * @property int		$id
 * @property string		$created_at
 * @property string		$updated_at
 * @property string		$deleted_at
 */
abstract class Base extends Model_Soft {
	protected static $_table_name;
	protected static $_primary_key;
	protected static $_properties;
	protected static $_soft_delete = array(
		'mysql_timestamp' => true,
	);
	protected static $_observers = array(
		'Orm\\Observer_Typing',
		'Orm\\Observer_Validation',
		'Orm\\Observer_Self',
		'Orm\\Observer_CreatedAt' => array(
			'mysql_timestamp' => true,
		),
		'Orm\\Observer_UpdatedAt' => array(
			'mysql_timestamp' => true,
		),
	);
	public function & __get($property) {
		$method = 'get_' . $property;
		$var = method_exists($this, $method) ? $this->$method() : parent::__get($property);
		return $var;
	}
	public function __set($property, $value) {
		$method = 'set_' . $property;
		if (method_exists($this, $method)) {
			return $this->$method($value);
		}
		return parent::__set($property, $value);
	}
	/**
	 * Redefino el find para poder borrar el cacheo de una clase y traer el objeto de nuevo
	 *
	 * @param null         $id
	 * @param array        $options
	 * @param bool         $forced
	 * @return array|object|Base
	 */
	public static function find($id = null, array $options = array(), $forced = false) {
		$class = get_called_class();
		if ($forced) {
			unset(static::$_cached_objects[$class]);
		}
		return parent::find($id, $options);
	}
}

?>