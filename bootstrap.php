<?php 
class MassUpdateBootstrap extends \Dsc\BaseBootstrap{
	protected $dir = __DIR__;
	protected $namespace = 'MassUpdate';
}
$app = new MassUpdateBootstrap();