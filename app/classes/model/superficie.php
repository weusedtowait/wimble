<?php

namespace Model;

/**
 * @property int			$numero
 * @property int			$estado
 * @property Superficie		$superficie
 * @property int			$luz
 * @property int			$habilitada_torneo
 * @property string			$observaciones
 *
 * @property Turno			$turno_actual
 * @property Turno			$turno_siguiente
 * @property array			$turnos_libres
 */

class Superficie extends Base {
	protected static $_table_name = 'canchas';
	protected static $_primary_key = array('id');

	protected static $_properties = array(
		'id',
		'numero' => array(
			'data_type' => 'int',
			'null' => false,
			'validation' => array('required', 'numeric_min' => array(1)),
		),
		'estado' => array(
			'data_type' => 'int',
			'null' => false,
			'validation' => array('required', 'numeric_min' => array(1)),
			'default' => 1
		),
		'superficie_id',
		'luz' => array(
			'data_type' => 'bool',
			'null' => false,
			'validation' => array('required'),
			'default' => false
		),
		'habilitada_torneo' => array(
			'data_type' => 'bool',
			'null' => false,
			'validation' => array('required'),
			'default' => false
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

	protected static $_belongs_to = array(
		'superficie' => array(
			'key_from' => 'superficie_id',
			'model_to' => 'Model\\Superficie',
			'key_to' => 'id',
			'cascade_save' => false
		),
	);

	const ESTADO_ESPERANDO = 1;
	const ESTADO_JUGANDO = 2;
	const ESTADO_JUGADO = 3;
	const ESTADO_NO_JUGADO = 4;

	/* ################################### Getters & Setters ################################### */

	protected function get_turno_actual() {
	}

	protected function get_turno_siguiente() {
	}

	protected function get_turnos_libres() {
	}

	/* #################################### Public  methods #################################### */
	/**
	 * @return bool
	 */
	public function bloqueada_por_torneo() {
		return $this->estado == self::ESTADO_OCUPADA_TORNEO;
	}

	/**
	 * @return bool
	 */
	public function quedan_turnos() {
		return count($this->turnos_libres) > 0;
	}

	/**
	 * @return bool|string
	 */
	public function proxima_hora_inicio() {
		if (count($this->turnos_libres) > 0) {
			return $this->turnos_libres[0]->horaInicio;
		}
		return false;
	}

	/**
	 * @param string	$horaInicio
	 * @return bool
	 */
	public function es_hora_inicio_posible($horaInicio) {
		foreach ($this->turnos_libres as $turno) {
			/** @var $turno Turno */
			if ($turno->hora_inicio == $horaInicio) {
				return true;
			}
		}
		return false;
	}
}

?>