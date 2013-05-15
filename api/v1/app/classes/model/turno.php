<?php

namespace Model;

/**
 * @property Cancha			$cancha
 * @property string			$fecha
 * @property string			$hora_fin
 * @property string			$hora_inicio
 * @property Socio			$socio_anfitrion
 * @property int			$luz
 * @property int			$tipo_partido
 * @property int			$estado
 * @property string			$observaciones
 * @property array			$socios
 *
 * @property Turno			$proximo_turno
 */

class Turno extends Base {
	protected static $_table_name = 'turnos';
	protected static $_primary_key = array('id');

	protected static $_properties = array(
		'id',
		'fecha' => array(
			'data_type' => 'time_mysql',
			'null' => false,
			'validation' => array('required'),
		),
		'hora_inicio' => array(
			'data_type' => 'varchar',
			'null' => false,
			'validation' => array('required', 'max_length' => array(5)),
		),
		'hora_fin' => array(
			'data_type' => 'time_mysql',
			'null' => false,
			'validation' => array('required', 'max_length' => array(5)),
		),
		'socio_anfitrion_id',
		'luz' => array(
			'data_type' => 'bool',
			'null' => false,
			'validation' => array('required'),
			'default' => false
		),
		'tipo_partido' => array(
			'data_type' => 'int',
			'null' => false,
			'validation' => array('required', 'numeric_min' => array(1)),
			'default' => 1
		),
		'estado' => array(
			'data_type' => 'int',
			'null' => false,
			'validation' => array('required', 'numeric_min' => array(1)),
			'default' => 1
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

	protected static $_has_many = array(
		'socios' => array(
			'key_from' => 'id',
			'model_to' => 'Model\\Socio',
			'key_to' => 'turno_id',
			'cascade_save' => true,
			'cascade_delete' => true
		),
	);

	const ESTADO_ESPERANDO = 1;
	const ESTADO_JUGANDO = 2;
	const ESTADO_JUGADO = 3;
	const ESTADO_NO_JUGADO = 4;
}

?>