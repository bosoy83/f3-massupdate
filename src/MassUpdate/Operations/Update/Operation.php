<?php 
namespace MassUpdate\Operations\Update;

/**
 * Base class for all update operations
 * 
 */
abstract class Operation{

	/**
	 * Name of attribute to which this operation is assigned
	 */
	protected $attribute;
	
	/**
	 * This method returns update class which will be later on passed to db mapper
	 */
	public abstract function getUpdateClause();
	
	/**
	 * This method returns string representation how the update clause should be rendered in form
	 */
	public abstract function getFormHtml();
	
	/**
	 * This method returns string representation how the update clause should be rendered in form
	 * 
	 * @param $attr		Name of the attribute in collection
	 */
	public function setAttributeName($attr){
		$this->attribute = $attr;
	}
	
	/**
	 * This method returns label for getFormHtml() element which should be used as a label for this
	 * operation in form
	 */
	public abstract function getLabel();
}
?>