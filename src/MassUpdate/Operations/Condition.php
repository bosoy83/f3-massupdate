<?php 
namespace MassUpdate\Operations;

/**
 * Base class for all Condition operations
 * 
 */
abstract class Condition implements \MassUpdate\Operations\Operation{

	use \MassUpdate\Service\Traits\Operation;

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
	 * This method returns generic labal in case a custom label was not defined
	 */
	public abstract function getGenericLabel();
	
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
}
?>