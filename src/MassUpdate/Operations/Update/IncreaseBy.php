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
		$data = $this->inputFilter()->clean($data, "float");
			
		switch( $this->attribute->getUpdaterMode() ){
			case 0: // buk update
				{
					return array('$inc', array( $this->attribute->getAttributeCollection() => $data ) );
				}
			case 1: // document-by-document
				{
					$doc = $params['document'];
					$doc[$this->attribute->getAttributeCollection()] += $data;
					return $doc;
				}
		}
	}
	
	/**
	 * This method returns string representation how the operation should be rendered in form
	 */
	public function getFormHtml(){
		return "<input name=\"".$this->getNameWithIdx()."\" class=\"form-control\" value=\"\" id=\"".$this->getNameWithIdx()."\" placeholder=\"".$this->getLabel()."\" type=\"text\" />";
	}
	
	/**
	 * This method returns label for getFormHtml() element which should be used as a label for this
	 * operation in form
	 */
	public function getGenericLabel(){
		return "Increase by";
	}
}
?>