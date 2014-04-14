<?php 
namespace MassUpdate\Operations\Condition;

/**
 * Checks, if a field is equal to a content (no regexps in condition)
 * 
 */
class EqualsTo extends \MassUpdate\Operations\Condition{

	/**
	 * This method returns where clause which will be later on passed to collection
	 * 
	 * @param 	$data		Data from request
	 * @param	$params		Arrays with possible additional params (for different modes of updater)
	 */
	public function getWhereClause($data, $params = array()){
		if( !$this->checkParams( $params ) ){
			return null;
		}
		$data = $this->inputFilter()->clean($data, "alnum");

		$res_clause = new \MassUpdate\Service\Models\Clause();
		$res_clause->{'key'} = $this->attribute->getAttributeCollection();
		$res_clause->{'val'} = $data;
		return $res_clause;
	}
	
	/**
	 * This method returns string representation how the operation should be rendered in form
	 */
	public function getFormHtml(){
		$name = $this->attribute->getAttributeCollection();
		
		return "<input name=\"".$this->getNameWithIdx()."\" class=\"form-control\" value=\"\" id=\"".$name."_".$this->getNameWithIdx()."\" placeholder=\"".$this->getLabel()."\" type=\"text\" />";
	}
	
	/**
	 * This method returns label for getFormHtml() element which should be used as a label for this
	 * operation in form
	 */
	public function getGenericLabel(){
		return "Equals to";
	}

	/**
	 * This method returns nature of this operation - whether it uses mdoel's filter or generates its own where clause statement
	 * 
	 * @return True if it uses model's filter
	 */
	public function getNatureOfOperation(){
		return false;
	}
}
?>