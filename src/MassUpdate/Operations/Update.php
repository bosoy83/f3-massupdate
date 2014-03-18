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
	 * This method returns attribute for this operation
	 *
	 * @return Attribute for this operation
	 */
	public function getAttribute(){
		return $this->attribute;
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
	public function setParams( $params ){
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
	 * Gets updater mode which this operation requires
	 * Note: -1 means that it doesnt matter for it
	 * 
	 * @return Number of mode
	 */
	public function getRequiredMode(){
		return -1;
	}
	

	/**
	 * This method returns representation of name of this option including its index
	 *
	 * @return String representation of name of this option including its index
	 */
	public function getNameWithIdx(){
		return $this->attribute->getAttributeCollection().'_'.$this->getTypeString().'_'.$this->idx;
	}
}
?>