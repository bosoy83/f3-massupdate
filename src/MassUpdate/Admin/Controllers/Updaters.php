<?php 
namespace MassUpdate\Admin\Controllers;

class Updaters extends \Admin\Controllers\BaseAuth
{
	public function index()
	{
		\Base::instance()->set('pagetitle', 'Mass Update');
		\Base::instance()->set('subtitle', '');

		$service = new \MassUpdate\Service\MassUpdate;
		\Base::instance()->set('service', $service );
	
		echo \Dsc\System::instance()->get('theme')->render('MassUpdate/Admin/Views::updaters/list.php');
	}
}