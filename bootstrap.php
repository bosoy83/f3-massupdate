<?php 
class MassUpdateBootstrap extends \Dsc\BaseBootstrap{
	protected $dir = __DIR__;
	protected $namespace = 'MassUpdate';
	
	protected function preAdmin(){
		\Dsc\System::instance()->get('container')->share( 'massupdate', function() {
				return new \MassUpdate\Service\MassUpdate;
			} );
	}
}
$app = new MassUpdateBootstrap();