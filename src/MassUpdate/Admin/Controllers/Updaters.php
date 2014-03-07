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
		$selected = $f3->get("PARAMS.id");
		$f3->set('service', $service );
		$f3->set('selected', $selected );
		$service->initializeGroups();
		
		echo \Dsc\System::instance()->get('theme')->render('MassUpdate/Admin/Views::updaters/list.php');
	}
	
	private function getModelsMetadata(){
		if( count( $updaters ) > 0 ) {
			$models = array();
			foreach($updaters as $updater ) {
				if( count( $updater->getModels() ) > 0 ){
					$m = $updater->getModels();
					foreach( $m as $model ){
						$models []= array(
								'slug' => $model
						);
					}
				}
			}
		}
		
				
	}
}