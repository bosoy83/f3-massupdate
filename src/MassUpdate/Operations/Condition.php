<?php 
namespace MassUpdate\Operations;

/**
 * Base class for all Condition operations
 * 
 */
abstract class Condition implements \MassUpdate\Operations\Operation{
	
	/**
	 * Type of this operation
	 */
	protected $type = 'where';
	
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
	 * Name of filter in model's state - in case this operation is using model's filters
	 */
	protected $filter;
	
	/**
	 * This method returns where clause which will be later on passed to collection
	 */
	public abstract function getWhereClause($data);
		
	/**
	 * This method returns string representation how the operation should be rendered in form
	 */
	public abstract function getFormHtml();
	
	/**
	 * This method sets attribute for this operation
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
	 * This method returns nature of this operation - whether it uses mdoel's filter or generates its own where clause statement
	 * 
	 * @return True if it uses model's filter
	 */
	public abstract function getNatureOfOperation();
	
	/**
	 * This method sets filter name for this operation
	 * 
	 * @param $newFilter	Filter for this operation
	 * 
	 * @return Instance of this class in order to support chaining of operations
	 */
	protected function setOperationFilter($newFilter){
		$this->filter = $newFilter;
	}

	/**
	 * This methods sets additional parameters for this operation
	 * Note: For update operations, nothing by default
	 *
	 * @param $param 	Array with additional parameters
	 */
	public function setParams( $params ){
		if( !empty( $params ) && is_array( $params ) ){
			if($this->getNatureOfOperation() && isset( $params['filter'] ) ){
				$this->setOperationFilter( $params['filter']);
			}
		}
	}
	

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