<?php

namespace MassUpdate\Service\Models;

abstract class Model
{
	/**
	 * This method returns an instance of model
	 */
	abstract public function getModel();
	
	/**
	 * This method gets list of attribute groups with operations
	 *
	 * @return	Array with attribute groups
	 */
	abstract public function getOperationGroups();
	
	/**
	 * Array with all attribute groups
	 */
	protected $MU_operations = array();
	
	/**
	 * This method returns unique string identifying this model.
	 * The unique identifier is generated from namespaced class name of the model
	 */
	public function getSlug($with = '-'){
		return str_replace( '\\', $with, get_class( $this ) );
	}
	
	/**
	 * This method returns title for this model
	 * The title is generated from class name of the model (excluding namespace)
	 */
	public function getTitle(){
		$names = explode( '\\', get_class( $this ) );
		$c = count( $names );
		return $names[$c-1];
	}
	
	/**
	 * This method that tells you whether Mass Update data for this model need to be initialized
	 * 
	 * @return True or false
	 */
	public function needInitialization(){
		return empty( $this->MU_operations );
	}
	
	/**
	 * This method adds an attribute group to this model
	 * 
	 * @param unknown $group
	 */
	protected function addAttributeGroup($group){
		$attr = $group->getAttributeCollection();
		
		if( empty( $this->MU_operations[$attr] ) ){
			$this->MU_operations[$attr] = $group;
		} else {
			$ops = $group->getOperations();
			if( count( $ops ) > 0 ) {
				foreach( $ops as $op ){
					$this->MU_operations[$attr]->addOperation( $op, $op->getTypeString(), array() );
				}
			}
		}
	}
	
	/**
	 * Returns operations based on its attribute name, type and name
	 * 
	 * @param $attribute	Name of attribute in collection to which the operation is connected
	 * @param $operation	Name of operation
	 * @param $type			Type of operation
	 */
	public function getOperation( $attribute, $operation, $type ){
		if( $this->needInitialization() ){
			$this->getOperationGroups();
		}
		
		if( empty( $this->MU_operations[$attribute] ) ) {
			return null;
		}
		return $this->MU_operations[$attribute]->getOperation( $operation, $type );
	}
	
	/**
	 * Gives you array with all attribute groups
	 * 
	 * @return Array with all attribute groups
	 */
	public function getAttributeGroups(){
		return $this->MU_operations;
	}
}