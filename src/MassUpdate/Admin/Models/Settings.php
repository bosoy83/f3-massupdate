<?php 
namespace MassUpdate\Admin\Models;

class Settings extends \Dsc\Mongo\Collections\Settings
{
    protected $__type = 'massupdate.settings';
    
    public $general = array(
    		'updater_mode' => 0
    );
}