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
				switch( $this->attribute->getUpdaterMode() ){
					case 0: // bulk update
						{
							return array('$pull', array( $this->attribute->getAttributeCollection() =>
									array( '$in' => $categories ) ) );
						}
					case 1: // document-by-document
						{
							$doc = $params['document'];
							$act_cats =  $doc->get( $this->attribute->getAttributeCollection());
							if( count( $act_cats ) > 0 ){
								if( count( $categories ) > 0 ){
									$list_unset = array();
									
									foreach( $categories as $cat ){
										foreach( $act_cats as $idx=>$act ){
											if( (string)($act['id']) == 
												(string)($cat['id']) ){
												$list_unset []= $idx;
											}
										}
									}
									
									if( count( $list_unset ) > 0 ){
										foreach( $list_unset as $val ){
											unset($act_cats[$val]);
										}
									}
								}
								$doc->set( $this->attribute->getAttributeCollection(), $act_cats);
							}
								
							return $doc;
						}
				}
								
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
					Dsc.MassUpdate.changeCategoryAddCategory = function( ev ){
						$this = jQuery( this );
						
					
						data = {
								"model" : $this.data("model"),
								"group" : $this.data("group"),
								"op" : $this.data( "op" ),
								"op_type" : $this.data( "op_type" ),
								"attr" : $this.data( "attr" ),
								"action" : $this.data( "action" ),
								"parent" : jQuery( "#parents select option:selected", $this).val(),
								"title" : jQuert( "input[name=\"title\"", $this ).val()
								};
						var url_link = "/admin/massupdate/updaters/ajax";
			
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
                    Dsc.MassUpdate.refreshChangeCategories = function(r) {

                            var form_data = new Array();
                        	jQuery.merge( form_data, jQuery(\'#categories-checkboxes\').find(\':input\').serializeArray() );
                        	jQuery.merge( form_data, [{ name: "category_ids[]", value: r.result._id[\'$id\'] }] );

                            var request = jQuery.ajax({
                                type: \'post\', 
                                url: \'/admin/shop/categories/checkboxes\',
                                data: form_data
                            }).done(function(data){
                                var lr = jQuery.parseJSON( JSON.stringify(data), false);
                                if (lr.result) {
                                    jQuery(\'#categories-checkboxes\').html(lr.result);
                                    App.initICheck();
                                }
                          });
                    }
					
					Dsc.MassUpdate.handleChangeCategoryDropDown = function(event){
							event.preventDefault();
	
							var $this = jQuery( event.currentTarget );
							var $parent = $this.closest("div[data-content-type=\"MassUpdate-ChangeCategory\"]");
							var obj_id = $parent.data( "content-id" );
							var $this_link = jQuery("a[data-mode]", $this );
							var obj_mode = $this_link.data("mode");
					
							jQuery( "input#"+obj_id+"_mode[type=\"hidden\"]").val(obj_mode);
							jQuery( "button[data-toggle]", $parent).html( $this_link.text() + " <span class=\"caret\"></span>"); 
					}
					
					jQuery(function() {
						jQuery( "div[data-operation-create=\"'.$name.'\"]" )
							.on( "click", "button",  Dsc.MassUpdateChangeCategoryAddCategory );

						jQuery("div[data-content-type=\"MassUpdate-ChangeCategory\"]")
								.on( "click","li", Dsc.MassUpdate.handleChangeCategoryDropDown );
						});
					</script>
				';
			$add_js = false;
		}
		$html .= '<div class="input-group">
					<div class="input-group-btn" data-content-type="MassUpdate-ChangeCategory" data-content-id="'.$name.'">
			  		<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
					Add to Category<span class="caret"></span>
					</button>
					<ul class="dropdown-menu" role="menu">
						<li><a href="#" data-mode="add" >Add to Category</a></li>
						<li><a href="#" data-mode="replace">Replace Category</a></li>
						<li><a href="#" data-mode="remove">Delete from Category</a></li>
					</ul>
		  			<input type="hidden" name="'.$name.'_mode" id="'.$name.'_mode" value="add" />
					</div>
		  		  </div>
				';
		
		
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
		$html .= '</div>';

		$additional_data = array(
			'data-model="'.$this->attribute->getModel()->getSlugMassUpdate().'"',
			'data-group="'.$this->attribute->getGroupName().'"',
			'data-attr="'.$this->attribute->getAttributeCollection().'"',
			'data-op="'.$this->idx.'"',
			'data-op-type="'.$this->getTypeString().'"',
			'data-action="addCategory"',
			'data-operation-create="'.$name.'"'
		);
		
		$html .= '<div class="well" '.implode( ' ', $additional_data ).'>

                	<div data-toggle="collapse" data-target="#'.$name.'_form" class="btn btn-link">
                    	Or Add New Category
                    </div>
                    <div id="'.$name.'_form" class="collapse">			
					    <div id="'.$name.'-quick-form-response-container"></div>
				
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
				            <button class="btn btn-primary">Create</button>
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
	
	/**
	 * 
	 * Adds a category via AJAX request
	 * 
	 * @param $request
	 */
	public function addCategory($request){
		$res = array( "result" => $request );
		return $res;
	}
}