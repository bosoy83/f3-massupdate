<?php 
namespace MassUpdate\Operations\Condition;

/**
 * Checks, if a field is containts this string
 * 
 */
class Contains extends \MassUpdate\Operations\Condition{

	/**
	 * This method returns where clause which will be later on passed to collection
	 * 
	 * @param 	$data		Data from request
	 * @param	$params		Arrays with possible additional params (for different modes of updater
	 */
	public function getWhereClause($data, $params = array()){
		$data = $this->attribute->getInputFilter()->clean($data, "alnum");
		return array( $this->filter, $data );
	}
	
	/**
	 * This method returns string representation how the operation should be rendered in form
	 */
	public function getFormHtml(){
		$name = $this->attribute->getAttributeCollection();
		
		return "<input name=\"".$name."_".$this->getTypeString()."_".$this->idx."\" class=\"form-control\" value=\"\" id=\"".$name."_".$this->getTypeString()."_".$this->idx."\" placeholder=\"".$this->getLabel()."\" type=\"text\" />";
	}
	
	/**
	 * This method returns label for getFormHtml() element which should be used as a label for this
	 * operation in form
	 */
	public function getLabel(){
		return "Contains";
	}

	/**
	 * This method returns nature of this operation - whether it uses mdoel's filter or generates its own where clause statement
	 *
	 * @return True if it uses model's filter
	 */
	public function getNatureOfOperation(){
		return true;
	}
}
?>