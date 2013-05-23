<?php
/**
 * User: Lea Marty
 * Date: 22/05/13
 * Time: 22:20
 */
namespace Controller;

use Fuel\Core\Controller_Rest;
//use Warden\Warden;

class Base extends Controller_Rest
{
	protected static $format = 'json';
	protected $current_user = null;

	public function before()
	{/*
		parent::before();
		if (!Warden::check()) {
			return $this->response(array(
										'statusMessage' => 'Permission denied'
								   ), 401);
		} else {
			$this->current_user = Warden::current_user();
		}
	*/}

	protected function forge_objects_for_relation($modelName, $primaryKeysArray, $onlyId = true)
	{
		/** @var \Model\Base $modelName */
		$objects = array();
		if (is_array($primaryKeysArray) && !empty($primaryKeysArray)) {
			$filter = array('where' => array(
				array('id', 'IN', $primaryKeysArray)
			));
			$onlyId && $filter['select'] = array('id');
			$objects = array_values($modelName::find('all', $filter));
		}
		return $objects;
	}
}
