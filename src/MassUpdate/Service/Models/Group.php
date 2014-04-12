<?php 
namespace MassUpdate\Service\Models;

/**
 * Abstract class containig a group of models which all support mass update
 */
abstract class Group extends \Prefab 
{
	protected $name;
	
	/**
	 * Title of this group
	 */
	public $title;
	public $slug;
	
	/**
	 * All models registered for mass update from this gruop
	 */
	private $models = array();
	
	/**
	 * Initialize list of models
	 * 
	 * @param	$mode	Mode of updater
	 */
	public abstract function initialize($mode);
	
	/**
	 * Adds a model into group
	 * 
	 * @param $model Model to be added into the group
	 * @param	$mode	Mode of updater
	 * 
	 * @return Instance of this class to support chaining of commands
	 */
	protected function addModel( $model, $mode ){
		$this->models []= $model;
		$model->setConfig( array( "context" => "MassUpdate.".$model->getSlugMassUpdate('.')));
		$attributes = $model->getMassUpdateOperationGroups();
		
		// set mode for all update operations
		if( count( $attributes ) ){
			foreach( $attributes as $attr ){
				$attr->setUpdaterMode( $mode )
					->setGroupName( $this->getName());
			}
		}
		return $this;
	}
	
	/**
	 * This method sets name of this group
	 * 
	 * @param $name		Name of this group
	 * @return \MassUpdate\Service\Models\Group Pointer to this instancce in order to support chaining
	 */
	public function setName($name){
		$this->name = $name;
		return $this;
	}

	/**
	 * This method gets name of this group
	 *
	 * @return	Name of this group
	 */
	public function getName(){
		return $this->name;
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