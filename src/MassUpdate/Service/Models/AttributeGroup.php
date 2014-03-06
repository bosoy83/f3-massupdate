<?php 
namespace MassUpdate\Service\Models;

/**
 * Main moodel for storing list of supported update operations for a certain attribute in model
 */
class AttributeGroup extends \Prefab 
{
	/**
	 * Name of attribute in collection
	 */
	protected $attribute;
	
	
	/**
	 * List of operations
	 */
	protected $operations = array();

	/**
	 * This method sets name of attribute in collection for this group of update operations
	 * 
	 * @param $attr Name in collection
	 * 
	 * @return Instance of this class in order to support chaining of operations
	 */
	public function setAttributeCollection($attr){
		$this->attribute = $attr;
		return $this;
	}
	
	/**
	 * This method adds operation into list of operations for this group
	 * 
	 * @param $op Instance of update operation
	 * 
	 * @return Instance of this class in order to support chaining of operations
	 */
	public function addOperation( $op ){
		if( $op instanceof \MassUpdate\Operations\Update\Operation ){
			$op->setAttributeName( $this->attribute );
			$this->operations []= $op;
		} else { // warn us, if we pass here instance of an unsupported object
			throw new \Exception( "Unsupported Operation object" );
		}
		return $this;
	}
	
	/**
	 * This method returns array of all operations for this group
	 * 
	 * @return Array of all operations for this group
	 */
	public function getOperations(){
		return $this->operations;
	}
}