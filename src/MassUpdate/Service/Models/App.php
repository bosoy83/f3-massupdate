<?php

namespace MassUpdate\Service\Models;

abstract class App extends \Prefab{

	protected $name;
	protected $mode;
	
	/**
	 * Title of this app
	 */
	public $title;
	
	/**
	 * All models registered for mass update from this app
	 */
	private $models = array();
	
	/**
	 * Initialize list of models
	 *
	 * @param	$mode	Mode of updater
	*/
	public function initialize($mode){
		$this->mode = $mode;
		return $this->canRun();
	}
	
	/**
	 * Make a preliminary check, whether the application can run
	 */
	protected function canRun(){
		return class_exists( $this->getName().'Bootstrap' );
	}
	
	/**
	 * Adds a model into app
	 *
	 * @param $model Model to be added into the group
	 *
	 * @return Instance of this class to support chaining of commands
	 */
	protected function addModel( $model ){
		$model_slug = $model->getSlug();
		if( !empty( $this->models[ $model_slug ] ) ){
			throw new \Exception( "Model with this slug - ".$model_slug." - already exists in Mass Update" );
		}
		$this->models[$model_slug] = $model;
		$model->getModel()->setConfig( array( "context" => "MassUpdate.".$model->getSlug('.')));
		$attributes = $model->getOperationGroups();
		// set mode for all update operations
		if( count( $attributes ) ){
			foreach( $attributes as $attr ){
				$attr->setUpdaterMode( $this->mode )
				->setGroupName( $this->getName() )
				->setModelSlug( $model_slug );
			}
		}
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