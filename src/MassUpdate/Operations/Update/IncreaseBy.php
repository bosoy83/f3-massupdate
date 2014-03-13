<?php 
namespace MassUpdate\Operations\Update;

/**
 * Change value to this one
 * 
 */
class IncreaseBy extends \MassUpdate\Operations\Update{

	/**
	 * This method returns update clause which will be later on passed to collection
	 * 
	 * @param 	$data		Data from request
	 * @param	$params		Arrays with possible additional params (for different modes of updater
	 * 
	 * @return	Based on mode of updater, either update clause or updated document
	 */
	public function getUpdateClause($data, $params = array()){
		$data = $this->attribute->getInputFilter()->clean($data, "float");
		return array('$inc', $this->attribute->getAttributeCollection().' = '.$data );
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
		return "Increase by";
	}
}
?>