<?php 
namespace MassUpdate\Operations;

/**
 * Base class for all operations Update operations
 * 
 */
abstract class Update implements \MassUpdate\Operations\Operation{

	use \MassUpdate\Service\Traits\Operation;
	
	/**
	 * Type of this operation
	 */
	protected $type = 'update';

	/**
	 * Custom label in case we want it
	 */
	protected $custom_label = '';
	
	/**
	 * Mode of updater
	 */
	protected $updater_mode;
	
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
	 * 
	 * @param 	$data		Data from request
	 * @param	$params		Arrays with possible additional params (for different modes of updater
	 * 
	 * @return	Based on mode of updater, either update clause or updated document
	 */
	public abstract function getUpdateClause($data, $params = array() );
		
	/**
	 * This method returns string representation how the operation should be rendered in form
	 */
	public abstract function getFormHtml();
	
	/**
	 * This method returns generic labal in case a custom label was not defined
	 */
	public abstract function getGenericLabel();

	/**
	 * This methods sets additional parameters for this operation
	 * Note: For update operations, nothing by default
	 *
	 * @param $param 	Array with additional parameters
	 */
	public function setParams( $params ){
		if( !empty( $params ) && is_array( $params ) ){
			if( !empty( $params['custom_label'] ) ){
				$this->custom_label = $params['custom_label'];
			}
		}
	}
	
	/**
	 * Gets updater mode which this operation requires
	 * Note: -1 means that it doesnt matter for it
	 * 
	 * @return Number of mode
	 */
	public function getRequiredMode(){
		return -1;
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