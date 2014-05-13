<?php 
namespace MassUpdate\Operations\Condition;

/**
 * Checks, if a product is in a category
 */
class Category extends \MassUpdate\Operations\Condition{

	private $mode; // 0 => only ancestors are considered; 1 => even the category id is considered
	private $category_id = '_id'; // name of variable with id

	/**
	 * This method returns where clause which will be later on passed to collection
	 * 
	 * @param 	$data		Data from request
	 * @param	$params		Arrays with possible additional params (for different modes of updater)
	 */
	public function getWhereClause($data, $params = array()){
		if( is_array( $data ) == false ){
			return array();
		}
		$ids = array();
		$empty_categories = false;
		foreach( $data as $id ){
			$id = $this->inputFilter()->clean($id, "string");
			if( $id == 'empty' ){
				$empty_categories = true;
				continue;
			}
			if( strlen( trim( $id ) ) > 0 ){
				$ids []= new \MongoId( (string)$id );
			}
		}
		if( count( $ids ) > 0 || $empty_categories ){
			$res = array();
			if( $empty_categories ){ // we want to select products with no categories
				$res_clause = new \MassUpdate\Service\Models\Clause();
				$res_clause->{"idx"} = $this->idx;
				$res_clause->{'key'} = '$or';
				$res_clause->{'val'} = array(
						array(
								$this->attribute->getAttributeCollection() => array(
										'$size' => 0
								)
						),
						array(
								$this->attribute->getAttributeCollection() => array(
										'$exists' => 0
								)
						)
				);
				if( count( $ids ) > 0 ){ // also,we want to check ID with several selected categories
					$res_clause->{"val"} []= array( $this->attribute->getAttributeCollection() => array( '$in' => $ids) );
				}
				
				$res []= $res_clause;
			} else {
				// only with selected categories, if you can find any
				if( count( $ids ) > 0 ) {
					$res_clause = new \MassUpdate\Service\Models\Clause();
					$res_clause->{"idx"} = $this->idx;
					$res_clause->{'key'} = $this->attribute->getAttributeCollection();
					$res_clause->{'val'} = array( '$in' => $ids);
					$res []= $res_clause;
				}
			}
			
			// should we include main category ID?
			if( $this->mode == 1 ){ // yop
				$res_clause = new \MassUpdate\Service\Models\Clause();
				$original_clause = array( $res[0]->{'key'} => $res[0]->{'val'} );
				
				
				$res_clause->{"idx"} = $this->idx;
				$res_clause->{'key'} = '$or';
				$res_clause->{'val'} = array( $original_clause, 
											array( $this->category_id => array(  '$in' => $ids) ) 
											);
				$res = array($res_clause);
			}
			return $res;
		}
		return null;
	}

	/**
	 * This method returns string representation how the operation should be rendered in form
	 */
	public function getFormHtml(){
		$categories = $this->model->emptyState()->populateState()->getItems();

		$html = '<div class="max-height-200 list-group-item">';
		$html .= '
					<div class="checkbox">
						<label>
							<input type="checkbox" name="'.$this->getNameWithIdx().'[]" class="icheck-input" value="empty">
							- No Category Assigned -
						</label>
					</div>
					';
		
		foreach ($categories as $one) {
			$dash = @str_repeat( "&ndash;", substr_count( @$one->path, "/" ) - 1 );
			
			$html .= '
					<div class="checkbox">
						<label>
							<input type="checkbox" name="'.$this->getNameWithIdx().'[]" class="icheck-input" value="'.$one->_id.'">
							'.$dash.' '.$one->title.'
					    </label>
					</div>
					';
		}
				
		return $html;
	}
	
	/**
	 * This method returns label for getFormHtml() element which should be used as a label for this
	 * operation in form
	 */
	public function getGenericLabel(){
		return "Category is";
	}

	/**
	 * This method returns nature of this operation - whether it uses mdoel's filter or generates its own where clause statement
	 * 
	 * @return True if it uses model's filter
	 */
	public function getNatureOfOperation(){
		return false;
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
			if( !empty( $params['id'] ) ){ // otherwise use default
				$this->category_id = $params['id'];
			}
			
			$this->mode = $params['mode'];
		}
			
		if( empty( $params[ 'model' ]) ){
			$this->model = new \Dsc\Mongo\Collections\Categories;
		} else {
			$this->model = $params[ 'model' ];
		}
	}
}
?>