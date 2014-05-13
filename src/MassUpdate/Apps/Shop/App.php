<?php

namespace MassUpdate\Apps\Shop;

class App extends \MassUpdate\Service\Models\App{

	public $title = 'Shop';
	
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
			$this->addModel( new \MassUpdate\Apps\Shop\Models\Products );
			$this->addModel( new \MassUpdate\Apps\Shop\Models\Manufacturers );
			$this->addModel( new \MassUpdate\Apps\Shop\Models\Categories );
			return true;
		}
		return false;
	}	
}