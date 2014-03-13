<?php 
namespace MassUpdate\Admin\Controllers;

class Updaters extends \Admin\Controllers\BaseAuth
{
	public function index()
	{
		$f3 = \Base::instance();
		$selected_updater = $f3->get("PARAMS.id");
		$selected_model = $f3->get("PARAMS.model");
		
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
								'updater' => $updater->slug,
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
		
		$update_part = $this->processUpdatePart( $selected_model );
		$where_part = $this->processWherePart( $selected_model );
		$collection = $selected_model->collection();
		
		$collection->update( $where_part, $update_part, array("multiple" => true  ) );
		$stats = \Dsc\System::instance()->get("mongo")->lastError();
		if( empty( $stats['err'] ) && isset( $stats['ok'] ) && $stats['ok'] == 1 ){
			\Dsc\System::instance()->addMessage( $stats['n']." record(s) were successfully updated!" );
		} else {
			\Dsc\System::instance()->addMessage( "An error has occured during the mass update", "error" );
			\Dsc\System::instance()->addMessage( \Dsc\Debug::dump( $stats ), "error" );
		}
		echo $this->getListHtml( $updater, $model_name );
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
				$result []= array( $operations[$opt], $data );
			}
		}
	
		return $result;
	}

	/**
	 * This method takes out only important data from request, sanitise them via Operations and returns them back to controller for furtner processing
	 * 
	 * @param $selected_model	Instance of model
	 * 
	 * @return	array of sanitized update commands for collection
	 */
	private function processUpdatePart(  $selected_model ){
		$updates = array();
		$update_data = $this->processSpecificPart( $selected_model, "update" );
		if( count( $update_data ) > 0 ){
			foreach( $update_data as $row ){
				$clause = $row[0]->getUpdateClause( $row[1] );
				if( !isset( $updates[$clause[0]] ) ){
					$updates[$clause[0]] = array();
				}
				$updates[$clause[0]] = $clause[1] + $updates[$clause[0]];
			}
		}
	
		return $updates;
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

		if( count( $conditions_data ) > 0 ){
			foreach( $conditions_data as $row ){
				$clause = $row[0]->getWhereClause( $row[1] );
				
				if( $row[0]->getNatureOfOperation() ){ // if this operation works with model filter
					// we will deal with them later
					$filters []= $clause;
				} else { // nope, it has its own condition
					if( !isset( $conditions[$clause[0]] ) ){
						$conditions[$clause[0]] = array();
					}
					$conditions[$clause[0]] = $clause[1];
				}
			}
		}
		
		// now, we should union specified conditions with the one used via filters
		if( count( $filters) > 0 ){
			// yes, we need to merge with filters
			$state  = $selected_model->emptyState()->populateState()->getState();
			foreach($filters as $filter ){
				$state->set('filter.'.$filter[0], $filter[1]);
			}
			$conditions = $state->conditions() + $conditions;
		}
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
}