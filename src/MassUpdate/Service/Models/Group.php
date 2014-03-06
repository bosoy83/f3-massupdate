<?php 
namespace MassUpdate\Service\Models;

abstract class Group extends \Prefab 
{
	/**
	 * Title of this group
	 */
	public $title;
	
	/**
	 * All models registered for mass update from this gruop
	 */
	protected $models = array();
	
	/**
	 * 
	 * List of models for this group
	 */
	public function getModels();
}