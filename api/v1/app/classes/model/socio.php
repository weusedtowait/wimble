<?php

namespace Model;

/**
 * @property string			$nombre
 * @property string			$apellido
 * @property string			$penalizado				Fecha "hasta" de penalización
 * @property int			$estado
 * @property string			$observaciones
 *
 * @property string			$nombre_apellido
 * @property Turno			$turno_actual
 * @property Turno			$turno_para_check_in
 * @property array			$turnos_por_jugar
 */

class Socio extends Base {
	protected static $_table_name = 'socios';
	protected static $_primary_key = array('id');

	protected static $_properties = array(
		'id',
		'nombre' => array(
			'data_type' => 'varchar',
			'null' => false,
			'validation' => array('required', 'max_length' => array(45)),
		),
		'apellido' => array(
			'data_type' => 'varchar',
			'null' => false,
			'validation' => array('required', 'max_length' => array(45)),
		),
		'penalizado' => array(
			'data_type' => 'varchar',
			'null' => false,
			'validation' => array('required', 'max_length' => array(10)),
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

	const ESTADO_NORMAL = 1;
	const ESTADO_INHABILITADO = 2;
	const ESTADO_SANCIONADO = 3;

	/* ################################### Getters & Setters ################################### */

	protected function get_nombre_apellido() {
		return $this->nombre . ' ' . $this->apellido;
	}

	protected function get_turno_actual() {
		if(!isset($this->turno_actual)){
			$turno = Turno::query()
				->related('socios')
				->where('socios.id', '=', $this->id)
				->where('estado', '=', Turno::ESTADO_JUGANDO)
				->get_one();
			if (!is_null($turno)) {
				$this->turno_actual = $turno;
			}
		}
		return $this->turno_actual;
	}

	protected function get_turno_para_check_in() {
		if(!isset($this->turno_para_check_in)){
			/*
			 * Todo: hacer con parámetros
			 * Tener en cuenta algunas cosas:
			 * 1) Los minutos desde el check in y los de tolerancia deberían poder ser negativos,
			 * 		porque tal vez se quiere obligar a que tenga que hacer el check in COMO MUCHO 3 minutos antes de la hora,
			 * 		y también se puede querer que el check in sólo pueda hacerse una vez pasada la hora (no tiene sentido, peeeeeeeeero... no cuesta nada. Hay gente muy rara)
			 * 2) Previo a esto, hay que verificar si este club lleva check in o no, y si es con o sin tolerancia. Lo mismo con las penalizaciones
			 */
			$minutosDesdeCheckIn = 10; //Debe ser parámetro. Es CUÁNTOS minutos antes se puede hacer check-in
			$minutosTolerancia = 10; //Debe ser parámetro. Es CUÁNTOS minutos se pueden exceder de la hora de inicio para hacer check-in
			$restaCheckIn = '18:20:00'; //Es la resta entre la hora de inicio y los $minutosDesdeCheckIn
			$sumaTolerancia = '18:40:00'; //Es la suma entre la hora de inicio y los $minutosTolerancia

			$turno = Turno::query()
				->related('socios')
				->where('socios.id', '=', $this->id)
				->where('estado', '=', Turno::ESTADO_ESPERANDO)
				->where('fecha', '=', date(time(), 'Y-m-d'))
				->where('hora_inicio', '>', $restaCheckIn)
				->where('hora_inicio', '<', $sumaTolerancia)
				->get_one();
			if (!is_null($turno)) {
				$this->turno_para_check_in = $turno;
			}
		}
		return $this->turno_para_check_in;
	}

	protected function get_turnos_por_jugar() {
		if(!isset($this->turnos_por_jugar)){
			$turnos = Turno::query()
				->related('socios')
				->where('socios.id', '=', $this->id)
				->where('estado', '=', Turno::ESTADO_ESPERANDO)
				->order_by(array('fecha' => 'ASC', 'hora_inicio' => 'ASC'))
				->get();
			$this->turnos_por_jugar = $turnos;
		}
		return $this->turnos_por_jugar;
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