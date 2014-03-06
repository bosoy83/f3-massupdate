<?php 
namespace MassUpdate;

class Service extends \Prefab 
{
	private $list_models = array();
	
	/**
	 * Registers models which are able to integrate with f3-massupdate
	 * 
	 * @param $group	Group with title and list of supported models
	 */
	public function regiseterModels($group){
		$this->list_models []=$group;
	}
}