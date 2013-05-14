<?php

namespace Model;

/**
 * @property string		$nombre
 * @property string		$observaciones
 */

class Superficie extends Base {
	protected static $_table_name = 'superficies';
	protected static $_primary_key = array('id');

	protected static $_properties = array(
		'id',
		'nombre' => array(
			'data_type' => 'varchar',
			'null' => false,
			'validation' => array('required', 'max_length' => array(45)),
		),
		'observaciones' => array(
			'data_type' => 'text',
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
}

?>