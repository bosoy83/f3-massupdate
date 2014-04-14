<?php
namespace MassUpdate\Service\Traits;

trait Model
{
	/**
	 * This trait requires interface \MassUpdate\Models\MassUpdateOperations
	 */
	
	/**
	 * Array with all attribute groups
	 */
	private $MU_operations = array();
	
	/**
	 * This method returns unique string identifying this model.
	 * The unique identifier is generated from namespaced class name of the model
	 */
	public function getSlugMassUpdate($with = '-'){
		return str_replace( '\\', $with, get_class( $this ) );
	}
	
	/**
	 * This method returns title for this model
	 * The title is generated from class name of the model (excluding namespace)
	 */
	public function getTitleMassUpdate(){
		$names = explode( '\\', get_class( $this ) );
		$c = count( $names );
		$title = $names[$c-1];
		if( $c == 1 ){
			$title = $names[0];
		}
		return $title;
	}
	
	/**
	 * This method that tells you whether Mass Update data for this model need to be initialized
	 * 
	 * @return True or false
	 */
	public function needInitializationMassUpdate(){
		return empty( $this->MU_operations );
	}
	
	/**
	 * This method adds an attribute group to this model
	 * 
	 * @param unknown $group
	 */
	private function addAttributeGroupMassUpdate($group){
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
	public function getOperationMassUpdate( $attribute, $operation, $type ){
		if( $this->needInitializationMassUpdate() ){
			$this->getMassUpdateOperationGroups();
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
	public function getAttributeGroupsMassUpdate(){
		return $this->MU_operations;
	}
}