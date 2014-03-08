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
	
	/**
	 * This method returns title for this model
	 * The title is generated from class name of the model (excluding namespace)
	 */
	public function getTitleMassUpdate(){
		$names = explode( '\\', get_class( $this ) );
		$c = count( $names );
		$title = $names[$c-1];
		if( $c == 1 ){
			$title = $names[0];
		}
		return $title;
	}
}