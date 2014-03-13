<?php 
namespace MassUpdate\Admin\Controllers;

class Settings extends \Admin\Controllers\BaseAuth 
{
	use \Dsc\Traits\Controllers\Settings;
	
	protected $layout_link = 'MassUpdate/Admin/Views::settings/default.php';
	protected $settings_route = '/admin/massupdate/settings';

    protected function getModel()
    {
        $model = new \MassUpdate\Admin\Models\Settings;
        return $model;
    }
}