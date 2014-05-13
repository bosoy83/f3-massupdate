<?php
namespace MassUpdate\Apps\Redirect;

class App extends \MassUpdate\Service\Models\App{

	public $title = 'Route Manager';
	
	/**
	 * Initialize list of models
	 *
	 * @param	$mode	Mode of updater
	 * 
	 * @return	Whether the list was initialized or not (in case the app is not available)
	 */
	public function initialize($mode) {
		$result = parent::initialize($mode);
		if( $result ) {
			$this->addModel( new \MassUpdate\Apps\Redirect\Models\Routes );
			return true;
		}
		return false;
	}	
}