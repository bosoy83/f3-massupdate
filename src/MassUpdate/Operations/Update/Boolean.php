<?php 
namespace MassUpdate\Operations\Update;

/**
 * Updates boolean value in the field
 * 
 */
class Boolean extends \MassUpdate\Operations\Update{

	/**
	 * This method returns update clause which will be later on passed to collection
	 * 
	 * @param 	$data		Data from request
	 * @param	$params		Arrays with possible additional params (for different modes of updater
	 * 
	 * @return	Based on mode of updater, either update clause or updated document
	 */
	public function getUpdateClause($data, $params = array() ){
		$data = (int)$data == 1;
		
		switch( $this->attribute->getUpdaterMode() ){
			case 0: // buk update
				{
					return array('$set', array( $this->attribute->getAttributeCollection() => $data ));
				}
			case 1: // document-by-document
				{
					$doc = $params['document'];
					$doc[$this->attribute->getAttributeCollection()] = $data;
					return $doc;
				}
		}
	}
		
	/**
	 * This method returns string representation how the operation should be rendered in form
	 */
	public function getFormHtml(){
		$name = $this->attribute->getAttributeCollection();
		$html = '
				<select name="'.$this->getNameWithIdx().'" id="'.$this->getNameWithIdx().'" class="form-control">
					<option value="1">True</option>
					<option value="0">False</option>
				</select>
				';
		
		return $html;
	}
	
	/**
	 * This method returns label for getFormHtml() element which should be used as a label for this
	 * operation in form
	 */
	public function getGenericLabel(){
		return "Change boolean value to";
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