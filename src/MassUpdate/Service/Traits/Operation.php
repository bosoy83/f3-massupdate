<?php
namespace MassUpdate\Service\Traits;

trait Operation
{
	/**
	 * This trait requires interface \MassUpdate\Operations\Operation
	 */


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
	public function getLabel(){
		if( strlen( $this->custom_label ) ){
			return $this->custom_label;
		} else {
			return $this->getGenericLabel();
		}
	}

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
	
	/**
	 * This method handles dispatching request to appropriate method in operation
	 * 
	 * @param $request	Array with input data
	 * 
	 * @return Result of operation
	 */
	public function handleAjax( $request ){
		$action = $this->inputFilter()->clean( trim( $request['action'] ), 'CMD' );
		$method = new \ReflectionMethod($this, $action );
		if( $method->isPublic() ){
			$res = $op->{$action}( $request );
		} else {
			$res = array(
					'error' => true,
					'message' => 'Operation '.$op_name.' (type - '.$op_type.') in attribute '.$attr_name.' does not contain public method '.$method,
			);
		}
		return $res;
	}
	
	public function inputFilter(){
		return \Dsc\System::instance()->get('inputfilter');
	}
}