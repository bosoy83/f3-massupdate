<?php 
namespace MassUpdate\Service;

class MassUpdate extends \Prefab 
{
	private $list_apps = array();
	private $initialized = false;

	/**
	 * Lets all registered groups initialize
	 */
	public function initializeApps(){
		if( $this->initialized ){
			return;
		}
		
		// get current mode from Settings model
		$settings = \MassUpdate\Admin\Models\Settings::fetch();
		$paths = \Base::instance()->get('dsc.massupdate.paths');
		
		if( !empty( $paths ) ){
			foreach ($paths as $path)
			{
				if (file_exists( $path . '/App.php' )) {
					$app = null;
					require $path . '/App.php';
					if( $app != null ){
						$res = $app->initialize($settings->{'general.updater_mode'});
							
						if( $res ) {
							$this->list_apps[$app->getName()] = $app;
						}
					}
				}
			}
		}
		$this->initialized = true;
	}
	
	/**
	 * Gets you list of all registered groups
	 */
	public function getApps(){
		return $this->list_apps;
	}
	
	/**
	 * Returns group with the wanted slug
	 * 
	 * @param $slug Slug of a group we're looking for
	 * 
	 * @return	Instance of Group of models
	 */
	public function getApp($slug){
		if( count( $this->list_apps ) > 0 ){
			if( empty( $this->list_apps[$slug] ) ){
				return null;
			} else {
				return $this->list_apps[$slug];
			}
		} else {
			return null;
		}
	}
	
	/**
	 * Finds appropriate model in group of models
	 * 
	 * @param $model		Slug for model
	 * @param $group  	Group in which we want to look (either its slug or instane of it)
	 * 
	 * @return	Instance of model or null in case it wasnt found
	 */
	public function getModel( $model, $app ){
		if( empty( $model ) || empty( $app ) ){
			throw new \Exception("Missing parameters in MassUpdate service in method getModel");
		}

		if( is_string( $app ) ) {
			$app = $this->getApp( $app );
			if( empty( $app )	 ){
				return null;
			}
		}
		
		$res = null;
			// find selected model
		if( count( $models = $app->getModels() ) > 0 ){
			if( empty( $models[$model] ) ){
				return null;
			} else {
				return $models[$model];
			}
		}
		return $res;
	}
}