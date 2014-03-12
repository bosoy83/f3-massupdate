<?php 
namespace MassUpdate\Operations;

/**
 * Base class for all operations
 * 
 */
interface Operation{
		
	/**
	 * This method returns string representation how the operation should be rendered in form
	 */
	public function getFormHtml();
	
	/**
	 * This method attribute for this operation
	 * 
	 * @param $attr		Attribute in collection
	 * 
	 * @return Instance of this class in order to support chaining of operations
	 */
	public function setAttribute($attr);
	
	/**
	 * This method returns label for getFormHtml() element which should be used as a label for this
	 * operation in form
	 */
	public function getLabel();
	
	/**
	 * This method sets index of this operation in list of all of them
	 * 
	 * @param $index	Index in the array
	 * 
	 * @return Instance of this class in order to support chaining of operations
	 */
	public function setIndex($index);

	/**
	 * This methods sets additional parameters for this operation
	 * 
	 * @param $param 	Array with additional parameters
	 */
	public function setParams( $params );
}
?>