<?php 
namespace MassUpdate\Admin\Controllers;

class Updaters extends \Admin\Controllers\BaseAuth
{
	
	public function index()
	{
		$f3 = \Base::instance();
		$selected_updater = $f3->get("PARAMS.id", "");
		$selected_model = $f3->get("PARAMS.model", "");
		
		echo $this->getListHtml($selected_updater, $selected_model );
	}

	private function getListHtml($updater, $model ){
		$f3 = \Base::instance();
		$f3->set('pagetitle', 'Mass Update');
		$f3->set('subtitle', '');
		
		$service = \Dsc\System::instance()->get('massupdate');
		$service->initializeGroups();
		$f3->set('service', $service );
		$f3->set('selected_updater', $updater );
		$f3->set('selected_model', $model );
		$f3->set('models', $this->getModelsMetadata());

		echo \Dsc\System::instance()->get('theme')->render('MassUpdate/Admin/Views::updaters/list.php');
	}

	private function getModelsMetadata(){
		$models = array();
		$updaters = \Dsc\System::instance()->get('massupdate')->getGroups();
		if( count( $updaters ) > 0 ) {
			foreach($updaters as $updater ) {
				if( count( $updater->getModels() ) > 0 ){
					$m = $updater->getModels();
					foreach( $m as $model ){
						$models []= array(
								'slug' => $model->getSlugMassUpdate(),
								'updater' => $updater->getName(),
								'title' => $model->getTitleMassUpdate(),
								'title_updater' => $updater->title
						);
					}
				}
			}
		}
		return $models;
	}

	public function getUpdaterData($updater, $model) {
		echo $this->getUpdaterDataHtml($updater, $model);
	}

	private function getUpdaterDataHtml($updater, $model){
		if( empty( $updater ) || empty( $model ) ){
			return "";
		}
		$service = \Dsc\System::instance()->get('massupdate');
		$service->initializeGroups();
		$selected_model = $service->getModel($model, $updater);
		if( $selected_model != null ){
			$f3 = \Base::instance();
			$f3->set('selected_updater', $updater );
			$f3->set('selected_model', $model );
			$f3->set( "model", $selected_model );
			return \Dsc\System::instance()->get('theme')->renderLayout('MassUpdate/Admin/Views::updaters/list_data.php');
		}
		return "";
	}
	
	public function getUpdaterDataAjax(){
		$f3 = \Base::instance();
		$updater = $f3->get("PARAMS.updater");
		$model = $f3->get("PARAMS.model");
		
		$html = $this->getUpdaterDataHtml( $updater, $model );

        echo $this->outputJson( $this->getJsonResponse( array(
                'result' => $html
        ) ) );
	}
	
	public function doUpdate(){
		$f3 = \Base::instance();
		$updater = $f3->get("PARAMS.id");
		$model_name = $f3->get("PARAMS.model");
		if( strlen( $updater ) == 0 || strlen( $model_name ) == 0 ) {
			throw new \Exception("Could not find appropriate model and updater in controller Updaters");
		}
		$service = \Dsc\System::instance()->get('massupdate');
		$service->initializeGroups();
		$selected_model = $service->getModel($model_name, $updater);
		
		if( $selected_model == null ){
			\Dsc\System::instance()->addMessage( "This model does not exist", "error" );
			echo $this->getListHtml( "", "" );
			return;
		}
		$where_part = $this->processWherePart( $selected_model );
		$update_operations = $this->processSpecificPart( $selected_model, "update" );
		
		$collection = $selected_model->collection();
		
		$model_settings = $this->getModel();
		$settings = $model_settings->populateState()->getItem();
		$mode = $settings['general.updater_mode'];
		
		// check, if the update can be performed (if all update operations are OK with this mode)
		$complains = array(); // array of all update operations complaining about mode
		if( count( $update_operations ) > 0 ){
			foreach ( $update_operations as $op_data ){
				$requirement = $op_data[0]->getRequiredMode();
				if( $requirement == -1 || $requirement == $mode ){
					// so far, we're cool
					continue;
				}
				// nope, start complaining like a girl NOW!
				$complains []= $op_data[0];
			}
		}
		
		if( count( $complains ) > 0 ){
			// we have operations complaing about the mode => notify user and do nothing
			foreach( $complains as $op ){
				$msg  = 'Update operation "'.$op->getLabel().'" in attribute "'.$op->getAttribute()->getAttributeTitle().'".';
				$msg .= ' This operations requires mode with code '.$op->getRequiredMode().' If you wish to switch to this mode, click on ';
				$msg .= ' <a href="#" class="link_updater_mode_settings" data-updater-mode="'.$op->getRequiredMode().'" >following link</a>';
				
				\Dsc\System::instance()->addMessage( $msg, "error" );
			}
		} else {
			// handle the update
			if( count( $update_operations ) > 0 ){
				$res = array();
				if( $mode == 1 ){ // document-by-document
					$res = $this->handleDocumentByDocument( $selected_model, $where_part, $update_operations, $collection );
				} else { // bulk update
					$res = $this->handleBulkUpdate( $selected_model, $where_part, $update_operations, $collection );
				}
					
				// process results
				if( $res['error'] ){
					\Dsc\System::instance()->addMessage( "An error has occured during the mass update", "error" );
					\Dsc\System::instance()->addMessage( $res['error_msg'], "error" );
				} else {
					\Dsc\System::instance()->addMessage( $res['records']." record(s) were successfully updated!" );
				}
			} else {
				\Dsc\System::instance()->addMessage( "No operation for update was selected!" );
			}
		}

		echo $this->getListHtml( $updater, $model_name );
	}
	
	/**
	 * This method handles bulk update
	 * 
	 * @param $selected_model 	Selected model to be updated
	 * @param $where_part		Array of condition operations
	 * @param $update_data		Array of update operations
	 * @param $collection		Collection to be updated
	 * 
	 * @return Result of this operation
	 */
	private function handleBulkUpdate($selected_model, $where_part, $update_data, $collection ){
		$update_part = array();
		$params = array( "dataset" => \Base::instance()->get("REQUEST"));
		if( count( $update_data ) > 0 ){
			foreach( $update_data as $row ){
				$row[0]->setIndex( $row[2]);
				$clause = $row[0]->getUpdateClause( $row[1], $params );
				// skip clauses which couldnt create an update operation
				if( $clause == null ){
					continue;
				}
				
				if( !isset( $updates[$clause[0]] ) ){
					$update_part[$clause[0]] = array();
				}
				$update_part[$clause[0]] = $clause[1] + $update_part[$clause[0]];
			}
		}
		$res = array(
				'records' => 0,
				'error' => false,
				'error_msg' => ""
		);
		try{
			$collection->update( $where_part, $update_part, array("multiple" => true  ) );
			
			// process results
			$stats = \Dsc\System::instance()->get("mongo")->lastError();
			if( empty( $stats['err'] ) && isset( $stats['ok'] ) && $stats['ok'] == 1 ){
				$res['records'] = $stats['n'];
			} else {
				$res['error'] = true;
				$res['error_msg'] = \Dsc\Debug::dump( $stats );
			}

		} catch( \Exception $e){
			$res['error'] = true;
			$res['error_msg'] = $e->getMessage();	
		}
		
		return $res;
	}
	
	/**
	 * This method handles document-by-document
	 * 
	 * @param $selected_model 	Selected model to be updated
	 * @param $where_part		Array of condition operations
	 * @param $update_data		Array of update operations
	 * @param $collection		Collection to be updated
	 * 
	 * @return Result of this operation
	 */
	private function handleDocumentByDocument( $selected_model, $where_part, $update_data, $collection ){
		
		$params = array( "dataset" => \Base::instance()->get("REQUEST"));
		$res = array(
				'records' => 0,
				'error' => false,
				'error_msg' => ""
		);
		
		$cursor = $collection->find( $where_part );
		$num = 0;
		foreach( $cursor as $doc ){
			$selected_model->bind( $doc );
			
			foreach( $update_data as $op ){
				$op[0]->setIndex( $op[2]);
				$params['document'] = $selected_model;

				$res_op = $op[0]->getUpdateClause( $op[1], $params );
				// skip clauses which couldnt create a where condition
				if( $res_op == null ){
					continue;
				} else {
					$selected_model = $res_op;
				}
			}	
			$collection->update(
                			array('_id'=> new \MongoId((string) $selected_model->get('id') ) ),
                			$selected_model->cast(),
					   		array('upsert'=>true, 'multiple'=>false)
					);

			$stats = \Dsc\System::instance()->get("mongo")->lastError();
			if( empty( $stats['err'] ) && isset( $stats['ok'] ) && $stats['ok'] == 1 ){
				$res['records']++;
			} else {
				$res['error'] = true;
				$res['error_msg'] .= "\n".\Dsc\Debug::dump( $stats );
			}
		}
		return $res;
	}

	/**
	 * This method takes out only important data from request, sanitise them via Operations and returns them back to controller for furtner processing
	 *
	 * @param $selected_model	Instance of model
	 * @param $type				Type of operation
	 *
	 * @return	array of sanitized update commands for collection
	 */
	private function processSpecificPart(  $selected_model, $type ){
		$result = array();
		$attr_groups = $selected_model->getMassUpdateOperationGroups();
		$request = \Base::instance()->get('REQUEST');
		if( count( $attr_groups ) > 0 ){
			foreach( $attr_groups as $attr ){
				// replace all dots with underscores
				$attr_name = str_replace('.', '_', $attr->getAttributeCollection());
	
				// make sure we have at least some information about this attribute
				if( !isset( $request[$attr_name.'_'.$type.'_cb'] ) ||
				is_array($request[$attr_name.'_'.$type.'_cb']) == false ||
				!isset($request[$attr_name.'_'.$type.'_cb'][0])  ){
					// something is not right with this attribute -> skip it
					continue;
				}
	
				$opt = (int)$request[$attr_name.'_'.$type.'_cb'][0];
				$data = empty($request[$attr_name.'_'.$type.'_'.$opt]) ? '' : $request[$attr_name.'_'.$type.'_'.$opt];
				// now we need to find operation with a proper index
				$operations = $attr->getOperations($type);
				if( empty( $operations[$opt] ) || !($operations[$opt] instanceof \MassUpdate\Operations\Operation )) {
					// something is not right with this attribute -> skip it
					continue;
				}
				$result []= array( $operations[$opt], $data, $opt );
			}
		}
		return $result;
	}
	
	/**
	 * This method takes out only important data from request, sanitise them via Operations and returns them back to controller for furtner processing
	 *
	 * @param $selected_model	Instance of model
	 *
	 * @return	array of sanitized where commands for collection
	 */
	private function processWherePart(  $selected_model ){
		$conditions = array();
		$filters = array();
		$conditions_data = $this->processSpecificPart( $selected_model, "where" );
		$params = array( "dataset" => \Base::instance()->get("REQUEST"));
		if( count( $conditions_data ) > 0 ){
			foreach( $conditions_data as $row ){
				$row[0]->setIndex( $row[2] );
				$clauses = $row[0]->getWhereClause( $row[1], $params );
				// skip clauses which couldnt create a where condition
				if( empty( $clauses ) ){
					continue;
				}
				
				if( is_array( $clauses ) == false ){
					$clauses = array( $clauses );
				}
				
				if( $row[0]->getNatureOfOperation() ){ // if this operation works with model filter
					// we will deal with them later
					foreach( $clauses as $clause ){
						$filters []= $clause;
					}
				} else { // nope, it has its own condition
					foreach( $clauses as $clause ){
						if( isset( $conditions[$clause->{"key"}] ) ){
							if( is_array( $conditions[$clause->{"key"}] ) == false ){
								$conditions[$clause->{"key"}] = array( $conditions[$clause->{"key"}] );
							}
							$conditions[$clause->{"key"}] = $clause->{"val"} + $conditions[$clause->{"key"}];
						} else {
							$conditions[$clause->{"key"}] = $clause->{"val"};
						}
					}
				}
			}
		}
		
		// now, we should union specified conditions with the one used via filters
		$selected_model->emptyState()->populateState();
		if( count( $filters) > 0 ){
			// yes, we need to merge with filters
			$state  = $selected_model->getState();
			foreach($filters as $filter ){
				$state->set('filter.'.$filter->{"filter"}, $filter->{"val"});
			}
		}
		$conditions = $selected_model->conditions() + $conditions;
		
		return $conditions;
	}

	/**
	 * Method for HMVC request to render table with operations of one type
	 * 
	 * @param $attributes 	Array with all attributes having operations
	 * @param $type			Type of operation that is being rendered
	 */
	public function getOperationsTableHtml($attributes, $type ){
		$f3 = \Base::instance();
		$f3->set('attributes',$attributes);	
		$f3->set('type',$type);	
		echo \Dsc\System::instance()->get('theme')->renderLayout('MassUpdate/Admin/Views::updaters/list_operations.php');
	}
	
	/**
	 * Returns approprate model
	 * 
	 * @param $type		Name of model
	 * 
	 * @return Instance of model
	 */
	public function getModel( $type = "settings" ){
		$model = null;
		switch( $type ){
			case "settings":
				// get current mode from Settings model
				$model = \MassUpdate\Admin\Models\Settings::fetch();
				break;
		}
		return $model;
	}
	
	/**
	 * This method handles AJAX requests from operations and dispatches them further
	 * 
	 * @param	$f3		Base application
	 */
	public function doAjax($f3){
		// first of all, lets check, if we know where and what do we want to dispatch
		$request = $f3->get( "POST" );
		if( empty( $request['model'] ) || 
			empty( $request['group'] ) ||
			empty( $request['attr']) ||
			empty( $request['op']) ||
			empty( $request['op_type'])){
			$res = array(
				'error' => true,
				'message' => 'There is not enough information',
			);
			return $this->getJsonResponse($res);
		}
		$inputFilter = \Dsc\System::instance()->get('inputfilter');
		
		$model_name = $inputFilter->clean( $request['model'], 'string' );
		$group = $inputFilter->clean( $request['group'], 'alnum' );
		$attr_name = $inputFilter->clean( $request['attr'], 'alnum' );
		$op_index = $inputFilter->clean( $request['op'], 'int' );
		$op_type = $inputFilter->clean( $request['op_type'], 'alnum' );
		
		$service = \Dsc\System::instance()->get( 'massupdate' );
		$model = $service->getModel( $model_name, $group );
		if( empty( $model ) ){
			$res = array(
				'error' => true,
				'message' => 'Model '.$model_name.' does not exist',
			);
			return $this->getJsonResponse($res);
		}
		$op = $model->getOperationMassUpdate( $attr_name, $op_index, $op_type );
		if( empty( $model ) ){
			$res = array(
					'error' => true,
					'message' => 'Operation at index '.$op_index.' (type - '.$op_type.') does not exist in attribute '.$attr_name,
			);
			return $this->getJsonResponse($res);
		}
		$response = $op->handleAjax( $request );
		return $this->getJsonResponse($response);
	}
}