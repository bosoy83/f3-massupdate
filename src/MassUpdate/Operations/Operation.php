<?php 
namespace MassUpdate\Operations;

/**
 * Base class for all operations
 * 
 */
abstract class Operation{

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
	 * This method returns update class which will be later on passed to db mapper
	 */
	public abstract function getUpdateClause();
	
	/**
	 * This method returns string representation how the operation should be rendered in form
	 */
	public abstract function getFormHtml();
	
	/**
	 * This method sets name of attribute for this operation
	 * 
	 * @param $attr		Name of the attribute in collection
	 * 
	 * @return Instance of this class in order to support chaining of operations
	 */
	public function setAttributeName($attr){
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
}
?>