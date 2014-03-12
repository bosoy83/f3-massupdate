<?php 
namespace MassUpdate\Operations;

/**
 * Base class for all operations Update operations
 * 
 */
abstract class Update implements \MassUpdate\Operations\Operation{

	/**
	 * Type of this operation
	 */
	protected $type = 'update';
	
	/**
	 * Name of attribute to which this operation is assigned
	 */
	protected $attribute;

	/**
	 * Index in array of all operations (useful to generate unique name in form in case there is more
	 * update opetions in one group)
	 */
	protected $idx;

	
	/**
	 * This method returns update clause which will be later on passed to collection
	 */
	public abstract function getUpdateClause($data);
		
	/**
	 * This method returns string representation how the operation should be rendered in form
	 */
	public abstract function getFormHtml();
	
	/**
	 * This method attribute for this operation
	 * 
	 * @param $attr		Attribute in collection
	 * 
	 * @return Instance of this class in order to support chaining of operations
	 */
	public function setAttribute($attr){
		$this->attribute = $attr;
		
		return $this;
	}
	
	/**
	 * This method returns label for getFormHtml() element which should be used as a label for this
	 * operation in form
	 */
	public abstract function getLabel();
	
	/**
	 * This method sets index of this operation in list of all of them
	 * 
	 * @param $index	Index in the array
	 * 
	 * @return Instance of this class in order to support chaining of operations
	 */
	public function setIndex($index){
		$this->idx = $index;
		
		return $this;
	}

	/**
	 * This methods sets additional parameters for this operation
	 * Note: For update operations, nothing by default
	 *
	 * @param $param 	Array with additional parameters
	 */
	public function setParams( $params ){}


	/**
	 * This method returns string representation of type of the operation
	 *
	 * @return String representation of type of the operation (where or update)
	 */
	public function getTypeString(){
		return $this->type;
	}
	
}
?>