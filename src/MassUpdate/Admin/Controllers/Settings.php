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
    
    /**
     * Changes updater mode via AJAX
     */
    public function changeUpdaterMode(){
    	$f3 = \Base::instance();
    	$mode = $f3->get("PARAMS.mode");
    	$model_settings = new \MassUpdate\Admin\Models\Settings;
    	$settings = $model_settings->populateState()->getItem();
    	$settings['general.updater_mode'] = (int)$mode;
    	$settings->save();    	
    	echo $this->outputJson( $this->getJsonResponse( array(
    			'result' => "Updater mode has been changed"
    	) ) );
    }
}