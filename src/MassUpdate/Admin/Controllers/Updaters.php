<?php 
namespace MassUpdate\Admin\Controllers;

class Updaters extends \Admin\Controllers\BaseAuth
{
	public function index()
	{
		$f3 = \Base::instance();
		$f3->set('pagetitle', 'Mass Update');
		$f3->set('subtitle', '');

		$service = \Dsc\System::instance()->get('massupdate');
		$service->initializeGroups();
		$selected_updater = $f3->get("PARAMS.id");
		$selected_model = $f3->get("PARAMS.model");
		$f3->set('service', $service );
		$f3->set('selected_updater', $selected_updater );
		$f3->set('selected_model', $selected_model );
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
		$groups = $service->getGroup( $updater );
		$selected_model = null;
		// find selected model
		if( $groups != null && count( $models = $groups->getModels() ) > 0 ){
			foreach( $models as $m ){
				if( $m->getSlugMassUpdate() == $model ){
					$selected_model = $m;
					break;
				}
			}
		}
		if( $selected_model != null ){
			\Base::instance()->set( "model", $selected_model );
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
}