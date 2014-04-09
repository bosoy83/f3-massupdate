<?php 
namespace MassUpdate\Operations\Condition;

/**
 * Checks, if a field is equally old, older or newer than selected date and time
 * 
 */
class DateTimeCompare extends \MassUpdate\Operations\Condition{

	private $mode; // 0 => only date, 1 => date + time
	private $dateformat; // "yyyy-mm-dd" by default
	
	private function isShowTime(){
		return $this->mode > 0;
	}
	
	/**
	 * This method returns where clause which will be later on passed to collection
	 * 
	 * @param 	$data		Data from request
	 * @param	$params		Arrays with possible additional params (for different modes of updater
	 */
	public function getWhereClause($data, $params = array()){
		// check required parameters
		if( !$this->checkParams( $params ) ){
			return null;
		}
		$dataset = $params['dataset'];
		$name_with_idx = $this->getNameWithIdx();
		// cleans up input
		if( empty( $dataset[$name_with_idx.'_sign']) ){
			return null;
		}
		$sign = $dataset[$name_with_idx.'_sign'];
		$in_range = $sign == 'range';
		
		$data_dates = array();
		$data_times = array();
		$data_dates []= (string) preg_replace('/[^0-9\-]/i', '', $data);
		$data_dates[0] = ltrim($data_dates[0], '.');
		if( $this->isShowTime() ){
			if( empty( $dataset[$name_with_idx.'_time']) ){
				return null;
			}
			preg_match('/^([0-9]|0[0-9]|1[0-9]|2[0-3]):[0-5][0-9]$/', $dataset[$name_with_idx.'_time'], $match);
			
			if( is_array( $match ) && !empty( $match ) ){
				$data_times []= (string)$match[0];
			}
		}
		
		
		if( $in_range ){
			if( empty( $dataset[$name_with_idx.'_end']) ){
				return null;
			}
			
			$data_dates []= (string) preg_replace('/[^0-9\-]/i', '', $dataset[$name_with_idx.'_end'] );
			$data_dates[1] = ltrim($data_dates[1], '.');
			
			if( $this->isShowTime() ){
				if( empty( $dataset[$name_with_idx.'_time_end']) ){
					return null;
				}				
				preg_match('/^([0-9]|0[0-9]|1[0-9]|2[0-3]):[0-5][0-9]$/', $dataset[$name_with_idx.'_time_end'], $match);
			
				if( is_array( $match ) && !empty( $match ) ){
					$data_times []= (string)$match[0];
				}
			}
		}
		
		$res_clause = new \MassUpdate\Service\Models\Clause();
		$res_clause->{'key'} = $this->attribute->getAttributeCollection();
		switch( $sign ){
			case 'new':
			case 'old':
				$operator = $sign == "new" ? '$gt' : '$lt' ;
				$final_date = $data_dates[0].' ';
				if($this->isShowTime()){
					$final_date .= $data_times[0].':00';
				} else {
					$final_date .= '00:00:00';
				}
				$date = strtotime($final_date);
				
				$res_clause->{'val'} = array( $operator => $date );
				return $res_clause;
			case 'equ':
				$date1 = $data_dates[0].' ';
				$date2 = $data_dates[0].' ';
				if($this->isShowTime()){
					$date1 .= $data_times[0].':00';
					$date2 .= $data_times[0].':59';
				} else {
					$date1 .= '00:00:00';
					$date2 .= '23:59:59';
				}
				$res_clause->{'val'} = array(
						'$gte' => strtotime( $date1 ),
						'$lte' => strtotime( $date2 )
				);
				return $res_clause;

			case 'range':
				$date1 = $data_dates[0].' ';
				$date2 = $data_dates[1].' ';
				if($this->isShowTime()){
					$date1 .= $data_times[0].':00';
					$date1 = strtotime($date1);
					$date2 .= $data_times[1].':00';
					$date2 = strtotime($date2);
				} else {
					$date1 = strtotime($date1.'00:00:00');
					$date2 = strtotime($date2.'23:59:59');
				}
				$res_clause->{'val'} = array(
						'$gte' => $date1,
						'$lte' => $date2
				);
				
				return $res_clause;
		}
		return null;
	}
	
	/**
	 * This method returns string representation how the operation should be rendered in form
	 */
	public function getFormHtml(){
		static $define_script = true;
		$html = '';
		
		if( $define_script ) {
			
			$html = '<script type="text/javascript">
						Dsc.MassUpdate.handleDateTimeCompareRange = function($this, $parent, obj_id ){
							jQuery( "input[name=\""+obj_id+"\"]", $parent ).prop( "placeholder", "Select start date" ); 
							jQuery( "div[data-datetime-range=\""+obj_id+"\"").show();
							jQuery( "span[data-addon-type=\"start\"]").html( "From" ); 
						}

						Dsc.MassUpdate.handleDateTimeCompareRangeHide = function($parent, obj_id ){
							jQuery( "input[name=\""+obj_id+"\"]", $parent ).prop( "placeholder", "Select date" ); 
							jQuery( "div[data-datetime-range=\""+obj_id+"\"").hide();
							jQuery( "span[data-addon-type=\"start\"]").html( "Date" ); 
						}
					
						Dsc.MassUpdate.handleDateTimeCompareDropDown = function(event){
							event.preventDefault();
	
							var $this = jQuery( event.currentTarget );
							var $parent = $this.closest("div[data-content-type=\"MassUpadte-DateTimeCompare-start\"]");
							var obj_id = $parent.data( "content-id" );
							var $this_link = jQuery("a[data-sign]", $this );
							var obj_sign = $this_link.data("sign");
					
							if( obj_sign == "range" ) {
								Dsc.MassUpdate.handleDateTimeCompareRange($this, $parent, obj_id );
							} else {
								Dsc.MassUpdate.handleDateTimeCompareRangeHide($parent, obj_id );
							}
							jQuery( "input#"+obj_id+"_sign[type=\"hidden\"]").val(obj_sign);
							jQuery( "button[data-toggle]", $parent).html( $this_link.text() + " <span class=\"caret\"></span>"); 
						}
					
						jQuery(function(){
						    jQuery("div[data-content-type=\"MassUpadte-DateTimeCompare\"]")
								.on( "click","li", Dsc.MassUpdate.handleDateTimeCompareDropDown );
					
							// hide all "Range" fields					
							jQuery("div[data-datetime-range]").each(function( idx, e ){
									var $this = jQuery( e );
									var $parent = jQuery("div[data-content-type=\"MassUpadte-DateTimeCompare-start\"]", $this.parent() );
									var obj_id = $this.attr( "data-datetime-range" );

									Dsc.MassUpdate.handleDateTimeCompareRangeHide($parent, obj_id );
								});
						});
					</script>
				';
			$define_script = false;			
		}
		$name_with_idx = $this->getNameWithIdx();
		
		$html .= '
					<div class="pull-left" data-content-type="MassUpadte-DateTimeCompare-start" data-content-id="'.$name_with_idx.'">
						<div class="input-group col-md-8 pull-left">
							<div class="input-group-btn" data-content-type="MassUpadte-DateTimeCompare">
			  			  			<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
						    			Eqauls to <span class="caret"></span>
						  			</button>
							  		<ul class="dropdown-menu" role="menu">
							    		<li><a href="#" data-sign="equ" >Equals to</a></li>
							    		<li><a href="#" data-sign="new">Newer than</a></li>
							    		<li><a href="#" data-sign="old">Older than</a></li>
							    		<li><a href="#" data-sign="range">In range</a></li>
		  							</ul>
							</div>
				  			<input type="hidden" name="'.$name_with_idx.'_sign" id="'.$name_with_idx.'_sign" value="equ" />
							
							<span class="input-group-addon" data-addon-type="start">Date</span>
				  			<input name="'.$name_with_idx.'" placeholder="Select date" value="" class="ui-datepicker form-control" contenteditable="false" type="text" data-date-format="'.$this->dateformat.'" data-date-today-highlight="true" data-date-today-btn="true">
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
		
		$html .= '	</div>
					<div class="pull-left" data-datetime-range="'.$name_with_idx.'">
						<div class="input-group col-md-offset-2 col-md-6 pull-left">
							<span class="input-group-addon">To</span>
							<input name="'.$name_with_idx.'_end" value="" placeholder="Select end date" class="ui-datepicker form-control" type="text" data-date-format="'.$this->dateformat.'" data-date-today-highlight="true" data-date-today-btn="true">
							<span class="input-group-addon"><i class="fa fa-calendar-o"></i></span>
						</div>
				 ';
									
		if( $this->isShowTime() ){
			$html .= '
						<div class="input-group col-md-3 pull-left">
                            <input name="'.$name_with_idx.'_time_end" value="" type="text" class="ui-timepicker form-control" data-show-meridian="false" data-show-inputs="false">
                            <span class="input-group-addon"><i class="fa fa-clock-o"></i></span>
                        </div>
						';
		}

		$html .= '</div>';
		
		return $html;
	}
	
	/**
	 * This method returns label for getFormHtml() element which should be used as a label for this
	 * operation in form
	 */
	public function getLabel(){
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
		
		if( empty( $params['dateformat'])){
			$this->dateformat = 'yyyy-mm-dd';
		} else {
			$this->dateformat = $params['dateformat'];
		}
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