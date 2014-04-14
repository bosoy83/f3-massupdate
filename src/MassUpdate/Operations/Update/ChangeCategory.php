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
		if( is_array( $data ) === false ){
			return null;
		}
		
		$categories = array();
		
		foreach( $data as $cat ) {
			$cat = $this->inputFilter()->clean( $cat, 'ALNUM' );
			if( $cat == 'empty' ){
				$categories = array();
				break;
			}
			$cat_id = new \MongoId( (string)$cat  );
			$act_cat = $this->attribute->getModel()->collection()->find( array( "_id" => $cat_id ) )->skip(0)->limit(1);
			if( !$act_cat->hasNext() ){
				continue;
			}
			$act_cat = $act_cat->getNext();
			$categories []= array(
				'id' => $cat_id,
				'title' => $act_cat['title'],
				'slug' => $act_cat['slug']
			);
		}
		
		$name_with_idx = $this->getNameWithIdx();
		$mode = $dataset[$name_with_idx.'_mode'];
		
		switch( $mode) {
			case 'replace':
				switch( $this->attribute->getUpdaterMode() ){
					case 0: // bulk update
						{
							return array('$set', array( $this->attribute->getAttributeCollection() => $categories ) );
						}
					case 1: // document-by-document
						{
							$doc = $params['document'];
							$doc->set( $this->attribute->getAttributeCollection(), $categories);
							return $doc;
						}
				}
				break;
			case 'add':
				switch( $this->attribute->getUpdaterMode() ){
					case 0: // bulk update
						{
							return array('$push', array( $this->attribute->getAttributeCollection() => 
													array( '$each' => $categories ) ) );
						}
					case 1: // document-by-document
						{
							$doc = $params['document'];
							$act_cats =  $doc->get( $this->attribute->getAttributeCollection());
							if( count( $categories ) > 0 ){
								foreach( $categories as $cat ){
									$act_cats []= $cat;
								}
							}
							
							$doc->set( $this->attribute->getAttributeCollection(), $act_cats);
							return $doc;
						}
				}
				break;
			case 'remove':
				break;	
			
		}
	}
	
	/**
	 * This method returns string representation how the operation should be rendered in form
	 */
	public function getFormHtml(){
		$categories = $this->attribute->getModel()->emptyState()->populateState()->getItems();
		$html = '';
		$name = $this->getNameWithIdx();
		static $add_js = true;
		
		if( $add_js ){
			$html = '<script type="text/javascript">
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
					
					jQuery(function() {
						jQuery( "div[data-operation="'.$name.'"] ).on( "click", Dsc.MassUpdateChangeCategoryAddCategory );
					});
				</script>
				';
		}
		
		$html .= '<div class="max-height-200 list-group-item">
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
		$html .= '<input type="hidden" name="'.$this->getNameWithIdx().'_mode" value="add">';
		$html .= '</div>';

		$model_name = $this->attribute->getModel()->getSlugMassUpdate();
		$group_name = $this->attribute->getGroupName();
		$attr = $this->attribute->getAttributeCollection();
		$op = $this->idx;
		$op_type = $this->getTypeString();
		$action = 'addCategory';
		
		$html .= '<div class="well" data-operation="'.$name.'">

				    <h3>Or Add New Category</h3>
				
				    <div id="quick-form-response-container"></div>
				
				        <div class="form-group">
				            <input type="text" name="title" placeholder="Title" class="form-control"/>
				        </div>
				        <!-- /.form-group -->
				
				        <div id="parents" class="form-group">
							<label>Parent</label> 
							'.$this->getParentSelectHtml($categories).'
				        </div>
				        <!-- /.form-group -->
				
				        <div class="form-actions">
				            <button class="btn btn-primary" data-model="'.$model_name.'">Create</button>
				        </div>
				
				    </form>
				
				</div>';
		
		return $html;
	}
	
	/**
	 * This method will generate html code for select
	 * 
	 * @param $categories	Array with categores, If empty, categores will be fetched from db
	 * 
	 * @return String with html code for select
	 */
	private function getParentSelectHtml($categories = ''){
		if( empty( $categories ) ){
			$categories = $this->attribute->getModel()->emptyState()->populateState()->getItems();
		}
		
		$html  = '<select name="parent" class="form-control">
					<option value="null">None</option>';

		foreach ($categories as $one) {
			$html .= '<option value="'.$one->_id.'" >';
			$html .= @str_repeat( "&ndash;", substr_count( @$one->path, "/" ) - 1 ) . " " . $one->title;;
			$html .= '</option>';
		}
		$html .= '</select>';
		return $html;
	}
	
	/**
	 * This method returns label for getFormHtml() element which should be used as a label for this
	 * operation in form
	 */
	public function getGenericLabel(){
		return "Move to category";
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