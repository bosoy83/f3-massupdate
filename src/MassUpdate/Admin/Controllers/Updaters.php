<?php 
namespace MassUpdate\Admin\Controllers;

class Updaters extends \Admin\Controllers\BaseAuth
{
	public function index()
	{
		$f3 = \Base::instance();
		$f3->set('pagetitle', 'Mass Update');
		$f3->set('subtitle', '');

		$service = new \MassUpdate\Service\MassUpdate;
		$selected = $f3->get("PARAMS.id");
		$f3->set('service', $service );
		$f3->set('selected', $selected );
		
		$service->regiseterGroup( new \Redirect\MassUpdateGroup );
		$service->regiseterGroup( new \Shop\MassUpdateGroup );
		$service->initializeGroups();
		
		echo \Dsc\System::instance()->get('theme')->render('MassUpdate/Admin/Views::updaters/list.php');
	}
}