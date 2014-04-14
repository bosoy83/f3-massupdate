<?php 
namespace MassUpdate\Service;

class MassUpdate extends \Prefab 
{
	private $list_groups = array();
	private $initialized = false;
	
	/**
	 * Registers models which are able to integrate with f3-massupdate
	 * 
	 * @param $group		Group with title and list of supported models
	 */
	public function registerGroup( $group ){
		if( $group instanceof \MassUpdate\Service\Models\Group ){
			$name = $group->getName();
			if( empty( $this->list_groups[$name]) ){
				$this->list_groups[$name] = $group;
			} else {
				throw new \Exception("Mass Update Group with name '".$name."' already exists in Mass Update!");
			}
		} else {
			throw new \Exception( "Group you want to register for Mass Update is not an instance of the correct class" );
		}
	}

	/**
	 * Lets all registered groups initialize
	 */
	public function initializeGroups(){
		if( $this->initialized ){
			return;
		}
		
		// get current mode from Settings model
		$settings = \MassUpdate\Admin\Models\Settings::fetch();
		$current_settings = $settings->populateState()->getItem();
		if( count( $this->list_groups ) > 0 ){
			foreach( $this->list_groups as $group ){
				$group->initialize($current_settings['general.updater_mode']);
			}
		}
		$this->initialized = true;
	}
	
	/**
	 * Gets you list of all registered groups
	 */
	public function getGroups(){
		return $this->list_groups;
	}
	
	/**
	 * Returns group with the wanted slug
	 * 
	 * @param $slug Slug of a group we're looking for
	 * 
	 * @return	Instance of Group of models
	 */
	public function getGroup($slug){
		if( count( $this->list_groups ) > 0 ){
			if( empty( $this->list_groups[$slug] ) ){
				return null;
			} else {
				return $this->list_groups[$slug];
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
	public function getModel( $model, $group ){
		if( empty( $model ) || empty( $group ) ){
			throw new \Exception("Missing parameters in MassUpdate service in method getModel");
		} 
		
		if( is_string( $group ) ) {
			$group = $this->getGroup( $group );
			if( empty( $group )	 ){
				return null;
			}
		}
		
		
		$res = null;
			// find selected model
		if( count( $models = $group->getModels() ) > 0 ){
			if( empty( $models[$model] ) ){
				return null;
			} else {
				return $models[$model];
			}
		}
		return $res;
	}

}