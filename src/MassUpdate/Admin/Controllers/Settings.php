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
		$settings = \MassUpdate\Admin\Models\Settings::fetch();
		$current_settings = $settings->populateState()->getItem();
       	$current_settings['general.updater_mode'] = (int)$mode;
       	$settings->bind( $current_settings );
    	$settings->save();
    	echo $this->outputJson( $this->getJsonResponse( array(
    			'result' => "Updater mode has been changed"
    	) ) );
    }
}