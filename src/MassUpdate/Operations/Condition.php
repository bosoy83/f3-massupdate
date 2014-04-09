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
	 * Custom label in case we want it
	 */
	protected $custom_label = '';
	
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
	 * 
	 * @param 	$data		Data from request
	 * @param	$params		Arrays with possible additional params (for different modes of updater
	 */
	public abstract function getWhereClause($data, $params = array());
		
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
	public function getLabel(){
		if( strlen( $this->custom_label ) ){
			return $this->custom_label;
		} else {
			return $this->getGenericLabel();
		}
	}
	
	/**
	 * This method returns generic labal in case a custom label was not defined
	 */
	public abstract function getGenericLabel();
	
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
			
			if( !empty( $params['custom_label'] ) ){
				$this->custom_label = $params['custom_label'];
			}
		}
	}
	
	/**
	 * Checks, if all necesarry parameters for this operation are provided
	 * 
	 * @param unknown $params
	 */
	public function checkParams( $params ){
		if( empty( $params['dataset'] ) ){
			return false;
		}
		return true;
	}

	/**
	 * This method returns string representation of type of the operation
	 * 
	 * @return String representation of type of the operation (where or update)
	 */
	public function getTypeString(){
		return $this->type;
	}

	/**
	 * This method returns representation of name of this option including its index
	 *
	 * @return String representation of name of this option including its index
	 */
	public function getNameWithIdx(){
		return str_replace( '.', '_', $this->attribute->getAttributeCollection() ) .'_'.$this->getTypeString().'_'.$this->idx;
	}
	
	/**
	 * This method returns custom label, if it was defined
	 */
	protected function getCustomLabel(){
		return $this->custom_label;
	}
}
?>