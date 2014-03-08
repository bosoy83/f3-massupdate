<?php 
namespace MassUpdate\Operations\Update;

/**
 * Base class for all update operations
 * 
 */
class AppendTo extends \MassUpdate\Operations\Update\Operation{

	/**
	 * This method returns update class which will be later on passed to db mapper
	 */
	public function getUpdateClause(){
		return array();
	}
	
	/**
	 * This method returns string representation how the update clause should be rendered in form
	 */
	public function getFormHtml(){
		return "here should be HTML";
	}
	
	/**
	 * This method returns label for getFormHtml() element which should be used as a label for this
	 * operation in form
	 */
	public function getLabel(){
		return "Append this string";
	}
}
?>