<?php 
namespace MassUpdate\Service\Models;

/**
 * This class holds information about condition clause returned from condition operation
 */
class Clause extends \Prefab 
{
	/**
	 * Key used in condition operation
	 */
	public $key;
	
	/**
	 * Value of condition operation
	 */
	public $val;

	/**
	 * Name of filter, if used
	 */
	public $filter;
}