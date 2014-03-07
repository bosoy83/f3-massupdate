<?php
namespace MassUpdate\Service\Traits;

trait Model
{
	/**
	 * This method returns unique string identifying this model.
	 * The unique identifier is generated from namespaced class name of the model
	 */
	public function getSlugMassUpdate(){
		return str_replace( '\\', '-', get_class( $this ) );
	}
}