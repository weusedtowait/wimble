<?php
/**
 * User: lceballos
 * Date: 21/05/13
 * Time: 10:20
 */
namespace Controller;

use Fuel\Core\Controller_Rest;
use Fuel\Core\Input;
use Fuel\Core\Log;
use Model\Base;
//use Warden\Warden;

class CrudController extends Controller_Rest
{
	protected static $model_name;
	protected static $resource_name;
	protected $format = 'json';
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
		/** @var Base $modelName */
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

	// Create
	public function post_index()
	{
		/** @var Base $object */
		/** @noinspection PhpUndefinedFieldInspection */
		$object = new static::$model_name();
		$object->set(Input::json());
		$object->save();
		Log::info(static::$resource_name . " $object->id has been succesfully created by user: " . $this->current_user->username);
		return $this->response($object->to_array(), 201);
	}

	// Read
	public function get_index($id = null)
	{

	}

	// Update
	public function put_index($id)
	{
		$model_name = static::$model_name;
		/** @var Base $model_name */
		$object = $model_name::find($id);
		$object->set(Input::json());
		$object->save();
		Log::info(static::$resource_name . " $id has been succesfully updated by user: " . $this->current_user->username);
		return $this->response($object->to_array(), 200);
	}

	// Delete
	public function delete_index($id)
	{
		$model_name = static::$model_name;
		/** @var Base $model_name */
		$object = $model_name::find($id);
		/** @var Base $result */
		$result = $object->delete();
		Log::info(static::$resource_name . " $id has been succesfully deleted by user: " . $this->current_user->username);
		return $this->response($result->to_array(), 200);
	}

}