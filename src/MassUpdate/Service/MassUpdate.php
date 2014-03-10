<?php 
namespace MassUpdate\Service;

class MassUpdate extends \Prefab 
{
	private $list_groups = array();
	
	/**
	 * Registers models which are able to integrate with f3-massupdate
	 * 
	 * @param $group	Group with title and list of supported models
	 */
	public function registerGroup($group){
		$this->list_groups []=$group;
	}

	/**
	 * Lets all registered groups initialize
	 */
	public function initializeGroups(){
		if( count( $this->list_groups ) > 0 ){
			foreach( $this->list_groups as $group ){
				$group->initialize();
			}
		}
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
			foreach( $this->list_groups as $group ){
				if( $group->slug == $slug ){
					return $group;
				}
			}
			return null;
		} else {
			return null;
		}
	}
}