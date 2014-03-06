<?php 
namespace MassUpdate\Service\Models;

/**
 * Interface which defines all operation which need to be implemented in a model in order to fully support
 * MassUpdate
 */
interface MassUpdateOperations{

	/**
	 * This method gets list of UpdateOperation groups
	 */
	public function getUpdateOperationGroups();
}