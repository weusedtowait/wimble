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

class Cancha extends Base {
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

	const ESTADO_DISPONIBLE = 1;
	const ESTADO_OCUPADA = 2;
	const ESTADO_OCUPADA_TORNEO = 3;
	const ESTADO_NO_DISPONIBLE = 4;

	const SUPERFICIE_POLVO = 1;
	const SUPERFICIE_CEMENTO = 2;
	const SUPERFICIE_CESPED = 3;

	/* ################################### Getters & Setters ################################### */

	protected function get_turno_actual() {
		if (!isset($this->turno_actual)) {
			$turno = Turno::query()
				->where('cancha_id', '=', $this->id)
				->where('estado', '=', Turno::ESTADO_JUGADO)
				->where('fecha', '=', date(time(), 'Y-m-d'))
				->where('hora_inicio', '<', date(time(), 'H:i:s'))
				->where('hora_fin', '>', date(time(), 'H:i:s'))
				->order_by(array('fecha' => 'DESC', 'hora_inicio' => 'DESC'))
				->get_one();
			$this->turno_actual = $turno;
		}
		return $this->turno_actual;
	}

	protected function get_turno_siguiente() {
		if (!isset($this->turno_siguiente)) {
			$turno = Turno::query()
				->where('cancha_id', '=', $this->id)
				->where('estado', '=', Turno::ESTADO_ESPERANDO)
				->where('fecha', '=', date(time(), 'Y-m-d'))
				->where('hora_inicio', '>', date(time(), 'H:i:s'))
				->order_by(array('fecha' => 'DESC', 'hora_inicio' => 'DESC'))
				->get_one();
			$this->turno_siguiente = $turno;
		}
		return $this->turno_siguiente;
	}

	protected function get_turnos_libres() {
		if (!isset($this->turnos_libres)) {
			$turnos = array();
			$this->turnos_libres = $turnos;
		}
		return $this->turnos_libres;

		/* Método anterior, hay que rehacerlo

		if (!isset($this->__turnosLibres)) {
			$horaFinDia = Parametro::get(1);
			$duracionFraccion = Parametro::get(2);
			$where = 'idCancha = ' . Core_DBI::getInstance()->objectToDB($this->idCancha);
			$where .= ' AND fecha = ' . Core_DBI::getInstance()->objectToDB(Core_Functions::today());
			$where .= ' AND horaInicio > ' . Core_DBI::getInstance()->objectToDB(Core_Functions::time());
			$where .= ' ORDER BY horaInicio ASC';
			$turnos = Core_Base::getObjectList('Turno', $where);
			if ($this->turnoActual->id != null)
				$proxHora = $this->turnoActual->horaFin;
			else
				$proxHora = Core_Functions::getProximaHora();
			$arrTurnos = array();
			foreach($turnos as $turno) { //Tengo que hacer un loop que genere todos los turnos posibles, a partir de la lista de los vigentes (un while?)
				$break = false;
				$begin = $proxHora;
				while (!$break) {
					if (!Type_Time::isLaterThan(Type_Time::addMinutes($proxHora, $duracionFraccion), $turno->horaInicio)) {
						$proxHora = Core_Functions::sumaMinutos($proxHora, $duracionFraccion);
					} else {
						$break = true;
						$newTurno = new Turno();
						$newTurno->horaInicio = $begin;
						$newTurno->horaFin = $turno->horaInicio;
						$arrTurnos[] = $newTurno;
						$proxHora = $turno->horaFin;
					}
				}
			}
			if (!Type_Time::isLaterThan(Type_Time::addMinutes($proxHora, $duracionFraccion), $horaFinDia)) {
				//Acá tengo q crear muchos turnos de X duración (30 min)
				$newTurno = new Turno();
				$newTurno->horaInicio = $proxHora;
				$newTurno->horaFin = $horaFinDia;
				$arrTurnos[] = $newTurno;
			}
			$newArrTurnos = array();
			foreach($arrTurnos as $turno) {
				if ($turno->horaInicio != $turno->horaFin)
					$newArrTurnos[] = $turno;
			}
			$this->__turnosLibres = $newArrTurnos;
		}
		return $this->__turnosLibres;
		*/
	}

	/* #################################### Public  methods #################################### */

	/**
	 * Método que verifica si en la cancha se está jugando actualmente un torneo
	 *
	 * @return bool
	 */
	public function bloqueada_por_torneo() {
		return $this->estado == self::ESTADO_OCUPADA_TORNEO;
	}

	/**
	 * Método que verifica si quedan turnos libres en el día
	 *
	 * @return bool
	 */
	public function quedan_turnos() {
		return count($this->turnos_libres) > 0;
	}

	/**
	 * Método que devuelve la hora de inicio del próximo turno LIBRE. Si no hay más turnos libres en el día, devuelve false
	 *
	 * @return string|bool
	 */
	public function proxima_hora_inicio() {
		if (count($this->turnos_libres) > 0) {
			return $this->turnos_libres[0]->horaInicio;
		}
		return false;
	}

	/**
	 * Método que verifica si la hora de inicio indicada es válida (si es la hora de inicio de alguno de los turnos libres)
	 *
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

	/**
	 * Método que verifica si la hora de fin indicada es válida (si es la hora de fin de alguno de los turnos libres)
	 *
	 * @param $horaFin
	 * @return bool
	 */
	public function es_hora_fin_posible($horaFin) {
		foreach ($this->turnos_libres as $turno) {
			/** @var $turno Turno */
			if ($turno->hora_fin == $horaFin) {
				return true;
			}
		}
		return false;
	}
}

?>