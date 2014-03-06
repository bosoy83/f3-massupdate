<?php 
namespace MassUpdate\Service\Models;

/**
 * Abstract class containig a group of models which all support mass update
 */
abstract class Group extends \Prefab 
{
	/**
	 * Title of this group
	 */
	public $title;
	
	/**
	 * All models registered for mass update from this gruop
	 */
	private $models = array();
	
	/**
	 * Initialize list of models
	 */
	public abstract function initialize();
	
	/**
	 * Adds a model into group
	 * 
	 * @param $model Model to be added into the group
	 * @return Instance of this class to support chaining of commands
	 */
	protected function addModel( $model ){
		$this->models []= $model;
		return $this;
	}
	
	/**
	 * List of models for this group
	 * 
	 * @return	Array with all registered models
	 */
	public function getModels(){
		return $this->models;
	}
}