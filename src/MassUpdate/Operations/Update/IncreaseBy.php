<?php 
namespace MassUpdate\Operations\Update;

/**
 * Change value to this one
 * 
 */
class IncreaseBy extends \MassUpdate\Operations\Operation{

	/**
	 * This method returns update class which will be later on passed to db mapper
	 */
	public function getUpdateClause($data){
		$data = \Joomla\Filter\InputFilter::clean($data, "float");
		return array('$inc', $this->getAttributeCollection().' = '.$data );
	}
	
	/**
	 * This method returns string representation how the operation should be rendered in form
	 */
	public function getFormHtml(){
		$name = $this->attribute->getAttributeCollection();
		
		return "<input name=\"".$name."_".$this->idx."\" class=\"form-control\" value=\"\" id=\"".$name."_".$this->idx."\" placeholder=\"".$this->getLabel()."\" type=\"text\" />";
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