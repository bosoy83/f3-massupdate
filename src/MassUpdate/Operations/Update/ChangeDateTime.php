<?php 
namespace MassUpdate\Operations\Update;

/**
 * Changes date time in a field (or \Mongo\Metastamp)
 * 
 */
class ChangeDateTime extends \MassUpdate\Operations\Update{

	private $mode; // 0 => only date, 1 => date + time
	private $metastamp = false; // is this \Dsc\Mongo\Metastamp structure
	private $attribute_datetime = array(); // names of attributes for date and time
	
	private function isShowTime(){
		return $this->mode > 0;
	}
	
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
		$name_with_idx = $this->getNameWithIdx();
		// cleans up input
		$tz = $dataset[$name_with_idx.'_tz'];
		
		$time =  "00:00";
		$date = (string) preg_replace('/[^0-9\-]/i', '', $data);
		$date = ltrim($date, '.');
		if( $this->isShowTime() ){
			if( empty( $dataset[$name_with_idx.'_time']) ){
				return null;
			}
			preg_match('/^([0-9]|0[0-9]|1[0-9]|2[0-3]):[0-5][0-9]$/', $dataset[$name_with_idx.'_time'], $match);
			
			if( is_array( $match ) && !empty( $match ) ){
				$time = (string)$match[0];
			}
		}
		
		$res_updates = array();
		$timestamp  = strtotime( $date.' '.$time.':00' );
		$final_date = $timestamp;
		if( $tz == 'gtm' ){
			$final_date += Date( 'Z', $timestamp );
		}
		
		if( $this->metastamp ){
			$final_date = \Dsc\Mongo\Metastamp::getDate( $final_date );
		}

		$res_updates[$this->attribute->getAttributeCollection()] = $final_date;
		if( count( $this->attribute_datetime ) > 0 ){
			if( !empty( $this->attribute_datetime['date'] ) ){
				$res_updates[ $this->attribute_datetime['date'] ] = $date;
			}
			if( !empty( $this->attribute_datetime['time'] ) ){
				$res_updates[ $this->attribute_datetime['time'] ] = $time;
			}
		}
		
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
		static $define_script = true;
		$html = '';
		
		if( $define_script ) {
			
			$html = '<script type="text/javascript">
						Dsc.MassUpdate.handleChangeDateTimeDropDown = function(event){
							event.preventDefault();
	
							var $this = jQuery( event.currentTarget );
							var $parent = $this.closest("div[data-content-type=\"MassUpadte-ChangeDateTime\"]");
							var obj_id = $parent.data( "content-id" );
							var $this_link = jQuery("a[data-tz]", $this );
							var obj_tz = $this_link.data("tz");
					
							jQuery( "input#"+obj_id+"_tz[type=\"hidden\"]").val(obj_tz);
							jQuery( "button[data-toggle]", $parent).html( $this_link.text() + " <span class=\"caret\"></span>"); 
						}					
					
						jQuery(function(){
						    jQuery("div[data-content-type=\"MassUpadte-ChangeDateTime\"]")
								.on( "click","li", Dsc.MassUpdate.handleChangeDateTimeDropDown );
						});
					</script>
				';
			$define_script = false;			
		}
		$name_with_idx = $this->getNameWithIdx();
		
		$html .= '
					<div class="pull-left">
						<div class="input-group col-md-8 pull-left">
							<div class="input-group-btn" data-content-type="MassUpadte-ChangeDateTime" data-content-id="'.$name_with_idx.'">
			  			  			<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
						    			Local TZ <span class="caret"></span>
						  			</button>
							  		<ul class="dropdown-menu" role="menu">
							    		<li><a href="#" data-tz="local" >Local TZ</a></li>
							    		<li><a href="#" data-tz="gtm">GTM</a></li>
		  							</ul>
							</div>
				  			<input type="hidden" name="'.$name_with_idx.'_tz" id="'.$name_with_idx.'_tz" value="local" />
							
				  			<input name="'.$name_with_idx.'" placeholder="Select date" value="" class="ui-datepicker form-control" contenteditable="false" type="text" data-date-format="yyyy-mm-dd" data-date-today-highlight="true" data-date-today-btn="true">
							<span class="input-group-addon"><i class="fa fa-calendar-o"></i></span>
						</div>
				';
		if( $this->isShowTime() ){
			$html .= '
                        <div class="input-group col-md-3 pull-left">
                            <input name="'.$name_with_idx.'_time" value="" type="text" class="ui-timepicker form-control" data-show-meridian="false" data-show-inputs="false">
                            <span class="input-group-addon"><i class="fa fa-clock-o"></i></span>
                        </div>
					';
		}
		
		$html .= '	</div>';
		
		return $html;
	}
	
	/**
	 * This method returns label for getFormHtml() element which should be used as a label for this
	 * operation in form
	 */
	public function getGenericLabel(){
		if( $this->mode == 0 ){
			return "Select date";
		} else {
			return "Selete date and time";
		}
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

	/**
	 * Checks, if all necesarry parameters for this operation are provided
	 *
	 * @param unknown $params
	 */
	public function checkParams( $params ){
		if( parent::checkParams( $params ) ){
			
			$name_with_idx = $this->getNameWithIdx();
			if( empty( $params['dataset'][$name_with_idx.'_tz']) ){
				return false;
			}

			return true;
		} else {
			return false;
		}
	}
}
?>