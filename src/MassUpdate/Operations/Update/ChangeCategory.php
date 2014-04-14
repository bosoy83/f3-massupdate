<?php 
namespace MassUpdate\Operations\Update;

/**
 * Changes category
 * 
 */
class ChangeCategory extends \MassUpdate\Operations\Update{

	private $allow_add; // false => disables adding a new category, 1 => allows adding a new category
	
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
		$data = $this->attribute->getInputFilter()->clean($data, "alnum");
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
		$categories = $this->attribute->getModel()->emptyState()->populateState()->getItems();

		$js = '<script type="text/javascript">
					Dsc.MassUpdateChangeCategoryAddCategory = function( ev ){
						$this = jQuery( ev.currentTarget );
						var url_link = "./admin/massupdate/updaters/ajax";
				
				        var request = jQuery.ajax({
				            type: "POST", 
				            url: url_link,
				            data: form_data
				        }).done(function(data){
				            var r = jQuery.parseJSON( JSON.stringify(data), false);
				            console.log( r );
				        }).fail(function(data){
				
				        }).always(function(data){
				
				        });
				
					}
				</script>
				';
		
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
		return "New Category is";
	}
	
	/**
	 * This methods sets additional parameters for this operation
	 * Note: For update operations, nothing by default
	 *
	 * @param $param 	Array with additional parameters
	 */
	public function setParams( $params ){
		parent::setParams( $params );
		if( empty( $params['allow_add'] ) ){
			$this->allow_add = false;
		} else {
			$this->allow_add = $params['allow_add'];
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