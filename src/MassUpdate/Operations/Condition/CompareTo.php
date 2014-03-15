<?php 
namespace MassUpdate\Operations\Condition;

/**
 * Checks, if a field is equal, greater or lesser to a content
 * 
 */
class CompareTo extends \MassUpdate\Operations\Condition{

	/**
	 * This method returns where clause which will be later on passed to collection
	 * 
	 * @param 	$data		Data from request
	 * @param	$params		Arrays with possible additional params (for different modes of updater
	 */
	public function getWhereClause($data, $params = array()){
		$data = $this->attribute->getInputFilter()->clean($data, "alnum");
		// check required parameters
		if( !isset( $params['idx'] ) || empty( $params['dataset'] ) ){
			return null;
		}
		$id = $params['idx'];
		$dataset = $params['dataset'];
		$name = $this->attribute->getAttributeCollection().'_'.$this->getTypeString().'_'.$id.'_sign';
		// cant find sign => skip this
		if( empty($dataset[$name]) ){
			return null;
		}
		$sign = $dataset[$name];
		$res = null;		
		switch( $sign ){
			case '$gt':
			case '$lt':
				$res = array( $sign => array( $this->attribute->getAttributeCollection() => $data ) );
				break;
			case '$eq':
				$res = array( $this->attribute->getAttributeCollection(), $data );
				break;
		}
		return $res;
	}
	
	/**
	 * This method returns string representation how the operation should be rendered in form
	 */
	public function getFormHtml(){
		$name = $this->attribute->getAttributeCollection();
		$name_with_idx = $name.'_'.$this->getTypeString().'_'.$this->idx;
		
		$html = '
				<script type="text/javascript">
					Dsc.MassUpdate.handleCompareToDropDown = function(event){
						event.preventDefault();

						var $this = jQuery( event.currentTarget);
						var $parent = $this.closest("div[data-content-type=\"MassUpadte-CompareTo\"]");
						var obj_id = $parent.data( "content-id" );
						var $this_link = jQuery("a[data-sign]", $this );
						var obj_sign = $this_link.data("sign");
						console.log( obj_sign);
						console.log( obj_id  );
						console.log( $parent );
						jQuery( "input#"+obj_id+"_sign[type=\"hidden\"]").val(obj_sign);
						jQuery( "button[data-toggle]", $parent).html( $this_link.text() + "<span class=\"caret\"></span>"); 
					}
				
					jQuery(function(){
					    jQuery("div[data-content-type=\"MassUpadte-CompareTo\"]")
							.on( "click","li", Dsc.MassUpdate.handleCompareToDropDown );
					});
				</script>

				<div class="input-group">
  						<div class="input-group-btn" data-content-type="MassUpadte-CompareTo" data-content-id="'.$name_with_idx.'">
	  			  			<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
				    			Eqauls to <span class="caret"></span>
				  			</button>
					  		<ul class="dropdown-menu" role="menu">
					    		<li><a href="#" data-sign="$eq" >Equals to</a></li>
					    		<li><a href="#" data-sign="$gt">Greater than</a></li>
					    		<li><a href="#" data-sign="$lt">Lesser than</a></li>
					  		</ul>
						</div>
					<input name="'.$name_with_idx.'" class="form-control" type="text" value="" id="'.$name_with_idx.'" placeholder="'.$this->getLabel().'" type="text" />
				</div>
				  <input type="hidden" name="'.$name_with_idx.'_sign" id="'.$name_with_idx.'_sign" value="$eq" />
							';
		
		return $html;//"<input name=\"".$name."_".$this->getTypeString()."_".$this->idx."\" class=\"form-control\" value=\"\" id=\"".$name."_".$this->getTypeString()."_".$this->idx."\" placeholder=\"".$this->getLabel()."\" type=\"text\" />";
	}
	
	/**
	 * This method returns label for getFormHtml() element which should be used as a label for this
	 * operation in form
	 */
	public function getLabel(){
		return "The content is";
	}

	/**
	 * This method returns nature of this operation - whether it uses models's filter or generates its own where clause statement
	 * 
	 * @return True if it uses model's filter
	 */
	public function getNatureOfOperation(){
		return false;
	}
}
?>