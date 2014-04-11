<?php 
namespace MassUpdate\Operations\Condition;

/**
 * Checks, if a field contains selected user
 */
class IsUser extends \MassUpdate\Operations\Condition{


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
		$data = $this->attribute->getInputFilter()->clean($data, "alnum");

		$res_clause = new \MassUpdate\Service\Models\Clause();
		$res_clause->{'key'} = $this->attribute->getAttributeCollection();
		$res_clause->{'val'} = new \MongoId( (string)$data);
		return $res_clause;
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
		
		$html .= '
					<div class="pull-left">
						<div class="input-group col-md-8 pull-left">
							<select name="'.$this->getNameWithIdx().'" id="'.$this->getNameWithIdx().'">';
		if( count( $users ) > 0 ){
			foreach( $users as $user ){
				$html .= '<option value="'.$user['_id'].'">'.$user['first_name'].' '.$user['last_name'].'</option>';
			}
		}
		$html .= '			</select>
						</div>
					</div>';
		
		return $html;
	}
	
	/**
	 * This method returns label for getFormHtml() element which should be used as a label for this
	 * operation in form
	 */
	public function getGenericLabel(){
		return "Select user";
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