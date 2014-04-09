<?php 
namespace MassUpdate\Operations\Update;

/**
 * Adds prefix or postfix to a string
 * 
 */
class ModifyTo extends \MassUpdate\Operations\Update{

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
		$data = $this->attribute->getInputFilter()->clean($data, "string");
		
		switch( $this->attribute->getUpdaterMode() ){
			case 0: // bulk update
				{
					throw new \Exception("Unsupported mode for this operation in Mass Update");
					break;
				}
			case 1: // document-by-document
				{
					$doc = $params['document'];
					$id = $params['idx'];
					$dataset = $params['dataset'];
					$attr_collection = $this->attribute->getAttributeCollection();
					$name_type = $this->attribute->getAttributeCollection().'_'.$this->getTypeString().'_'.$id.'_type';
					$name_op = $this->attribute->getAttributeCollection().'_'.$this->getTypeString().'_'.$id.'_operation';
					// cant find type => skip this
					if( empty($dataset[$name_type]) || empty( $dataset[$name_op])){
						return null;
					}
					
					$act_data = \Dsc\ArrayHelper::get( $doc, $attr_collection );
					$res = '';
					$type = $dataset[$name_type];
					$op = $dataset[$name_op];
					
					switch( $op ){
						case '+':
 							$res = $this->handleAdding( $type, $act_data, $data);
							break;
						case '-':
 							$res = $this->handleRemoving( $type, $act_data, $data);
							break;
						default:
							$res = $act_data;
							break;
					}
					\Dsc\ObjectHelper::set( $doc, $attr_collection, $res );
					return $doc;
				}
			default:
				throw new \Exception("Unknown type of operation in Mass Update");
		}
	}
	
	private function handleAdding( $type, $act_data, $new_data ){
		switch( $type ){
			case 'pre':
				return $new_data.$act_data;
			case 'suf':
				return $act_data.$new_data;
			default:
				return $act_data;
		}
	}

	private function handleRemoving( $type, $act_data, $new_data ){
		$len_act = strlen( $act_data );
		$len_new = strlen( $new_data );
		
		if( $len_act < $len_new ) { // nothing to do here => cant be altered
			return $act_data;
		}
		
		switch( $type ){
			case 'pre':
				$pos = strpos( $act_data, $new_data );
				if( $pos === false || $pos > 0 ){ // not prefix
					return $act_data;
				} else {
					return substr( $act_data, $len_new );
				}
			case 'suf':
				$pos = strrpos( $act_data, $new_data );
				
				if( $pos === false || $pos != ( $len_act - $len_new) ){ // not suffix
					return $act_data;
				} else {
					return substr( $act_data, 0, $len_act - $len_new );
				}
			default:
				return $act_data;
		}
	}
	
	/**
	 * This method returns string representation how the operation should be rendered in form
	 */
	public function getFormHtml(){
		static $define_script = true;
		$html = '';
		
		if( $define_script ) {
			$html = '<script type="text/javascript">
						Dsc.MassUpdate.handleModifyToDropDown = function(event){
							event.preventDefault();
	
							var $this = jQuery( event.currentTarget );
							var $parent = $this.closest("div[data-content-type=\"MassUpadte-ModifyTo\"]");
							var obj_id = $parent.data( "content-id" );
							var $this_link = jQuery("a[data-type-modification]", $this );
							var obj_type = $this_link.data("type-modification");
							var obj_operation = $this_link.data("type-operation");
							jQuery( "input#"+obj_id+"_type[type=\"hidden\"]").val(obj_type);
							jQuery( "input#"+obj_id+"_operation[type=\"hidden\"]").val(obj_operation);
							jQuery( "button[data-toggle]", $parent).html( $this_link.text() + " <span class=\"caret\"></span>"); 
						}
					
						jQuery(function(){
						    jQuery("div[data-content-type=\"MassUpadte-ModifyTo\"]")
								.on( "click","li", Dsc.MassUpdate.handleModifyToDropDown );
						});
					</script>
				';
			$define_script = false;
		}
		
		$name_with_idx = $this->getNameWithIdx();
		$html .= '
				<div class="input-group">
  						<div class="input-group-btn" data-content-type="MassUpadte-ModifyTo" data-content-id="'.$name_with_idx.'">
	  			  			<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
				    			Add Prefix <span class="caret"></span>
				  			</button>
					  		<ul class="dropdown-menu" role="menu">
					    		<li><a href="#" data-type-modification="pre" data-type-operation="+">Add Prefix</a></li>
					    		<li><a href="#" data-type-modification="suf" data-type-operation="+">Add Suffix</a></li>
  								<li class="divider"></li>
					    		<li><a href="#" data-type-modification="pre" data-type-operation="-">Remove Prefix</a></li>
					    		<li><a href="#" data-type-modification="suf" data-type-operation="-">Remove Suffix</a></li>
  							</ul>
						</div>
					<input name="'.$name_with_idx.'" class="form-control" type="text" value="" id="'.$name_with_idx.'" placeholder="'.$this->getLabel().'" type="text" />
				</div>
				<input type="hidden" name="'.$name_with_idx.'_type" id="'.$name_with_idx.'_type" value="pre" />
				<input type="hidden" name="'.$name_with_idx.'_operation" id="'.$name_with_idx.'_operation" value="+" />
						';
		
		return $html;
	}
	
	/**
	 * This method returns label for getFormHtml() element which should be used as a label for this
	 * operation in form
	 */
	public function getLabel(){
		return "Modify string accordingly";
	}
	
	/**
	 * Gets updater mode which this operation requires
	 * Note: -1 means that it doesnt matter for it
	 * 
	 * @return Number of mode
	 */
	public function getRequiredMode(){
		return 1;
	}
}
?>