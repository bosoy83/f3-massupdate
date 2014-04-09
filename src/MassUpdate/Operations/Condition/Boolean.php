<?php 
namespace MassUpdate\Operations\Condition;

/**
 * Checks, if a field (of boolean type) matches the expected value
 * 
 */
class Boolean extends \MassUpdate\Operations\Condition{

	/**
	 * This method returns where clause which will be later on passed to collection
	 * 
	 * @param 	$data		Data from request
	 * @param	$params		Arrays with possible additional params (for different modes of updater
	 */
	public function getWhereClause($data, $params = array()){
		if( !$this->checkParams( $params ) ){
			return null;
		}
		
		$data = (string)((int)$data);
		$res_clause = new \MassUpdate\Service\Models\Clause();
		$res_clause->{'key'} = '$or';
		$res_clause->{'val'} = array( 
								array( $this->attribute->getAttributeCollection() => $data == '1' ),
								array( $this->attribute->getAttributeCollection() => $data )
								);
		return $res_clause;
	}
	
	/**
	 * This method returns string representation how the operation should be rendered in form
	 */
	public function getFormHtml(){
		$name = $this->attribute->getAttributeCollection();
		$html = '
				<select name="'.$this->getNameWithIdx().'" id="'.$this->getNameWithIdx().'">
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
		return "Boolean value is";
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