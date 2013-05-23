<?php
/**
 * User: lceballos
 * Date: 21/05/13
 * Time: 10:20
 */
namespace Controller;

use Fuel\Core\Input;
use Fuel\Core\Log;

class CrudController extends Base
{
	protected static $model_name;
	protected static $resource_name;

	// Create
	public function post_index()
	{
		/** @var \Model\Base $object */
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
		$model_name = static::$model_name;
		/** @var \Model\Base $model_name */
		$object = $model_name::find($id);
		return $this->response($object->to_array(), 200);
	}

	// Update
	public function put_index($id)
	{
		$model_name = static::$model_name;
		/** @var \Model\Base $model_name */
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
		/** @var \Model\Base $model_name */
		$object = $model_name::find($id);
		/** @var \Model\Base $result */
		$result = $object->delete();
		Log::info(static::$resource_name . " $id has been succesfully deleted by user: " . $this->current_user->username);
		return $this->response($result->to_array(), 200);
	}

}