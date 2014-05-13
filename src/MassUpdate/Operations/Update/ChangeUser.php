<?php 
namespace MassUpdate\Operations\Update;

/**
 * Changes user
 * 
 */
class ChangeUser extends \MassUpdate\Operations\Update{

	private $mode; // 0 => only date, 1 => date + time
	
	/**
	 * This method returns update clause which will be later on passed to collection
	 *
	 * @param 	$data		Data from request
	 * @param	$params		Arrays with possible additional params (for different modes of updater
	 *
	 * @return	Based on mode of updater, either update clause or updated document
	 */
	public function getUpdateClause($data, $params = array() ){
		// check required parameters
		if( !$this->checkParams( $params ) ){
			return null;
		}
		
		$dataset = $params['dataset'];
		$data = $this->inputFilter()->clean($data, "alnum");
		$act_user = \Users\Models\Users::collection()->find( array( "_id" => new \MongoId( (string)$data ) ) )->skip(0)->limit(1);
		if( !$act_user->hasNext() ){
			return null;
		}
		$act_user = $act_user->getNext();
		$res_updates = array(
			$this->attribute->getAttributeCollection().'.id' => new \MongoId( (string)$data ),
			$this->attribute->getAttributeCollection().'.name' => $act_user['username']
		);
		
		switch( $this->attribute->getUpdaterMode() ){
			case 0: // bulk update
				{
					return array('$set', $res_updates );
				}
			case 1: // document-by-document
				{
					$doc = $params['document'];
					foreach( $res_updates as $key => $value ) {
						$doc[$key] = $value;
					}
					return $doc;
				}
		}
	}
	
	/**
	 * This method returns string representation how the operation should be rendered in form
	 */
	public function getFormHtml(){
		static $users = array();
		if( count( $users ) == 0 ){
			$users_model = new \Users\Models\Users();
			$users_model->setConfig( array( 'context' => 'MassUpdate.'.$this->getNameWithIdx() ) );
			$users = $users_model->emptyState()->populateState()->getItems();
		}
		
		$html = '';
		$name_with_idx = $this->getNameWithIdx();
		
		$html .= '<select name="'.$this->getNameWithIdx().'" id="'.$this->getNameWithIdx().'" class="form-control">';
		if( count( $users ) > 0 ){
			foreach( $users as $user ){
				$html .= '<option value="'.$user['_id'].'">'.$user['first_name'].' '.$user['last_name'].'</option>';
			}
		}
		$html .= '</div>';
		
		return $html;
	}
	
	/**
	 * This method returns label for getFormHtml() element which should be used as a label for this
	 * operation in form
	 */
	public function getGenericLabel(){
		return "Select User";
	}
	
	/**
	 * This methods sets additional parameters for this operation
	 * Note: For update operations, nothing by default
	 *
	 * @param $param 	Array with additional parameters
	 */
	public function setParams( $params ){
		parent::setParams( $params );
		if( empty( $params['mode'] ) ){
			$this->mode = 0;
		} else {
			$this->mode = $params['mode'];
		}
		
		if( empty( $params[ 'metastamp' ]) ){
			$this->metastamp = false;
		} else {
			$this->metastamp = (int)$params[ 'metastamp' ];
		}
		
		if( !empty( $params['attribute_dt'] ) && is_array( $params['attribute_dt'] ) ) {
			$this->attribute_datetime = $params['attribute_dt'];
		}
	}
}
?>