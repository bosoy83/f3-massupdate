<?php 
namespace MassUpdate\Operations\Update;

/**
 * Change value to this one
 * 
 */
class ChangeTo extends \MassUpdate\Operations\Operation{

	/**
	 * This method returns update class which will be later on passed to db mapper
	 */
	public function getUpdateClause(){
		return array();
	}
	
	/**
	 * This method returns string representation how the operation should be rendered in form
	 */
	public function getFormHtml(){
		$name = $this->attribute;
		
		return "<input name=\"".$name."\" class=\"form-control\" value=\"\" id=\"".$name."\" placeholder=\"".$this->getLabel()."\" type=\"text\" />";
	}
	
	/**
	 * This method returns label for getFormHtml() element which should be used as a label for this
	 * operation in form
	 */
	public function getLabel(){
		return "Change to this string";
	}
}
?>