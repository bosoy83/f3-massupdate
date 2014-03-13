<?php 
namespace MassUpdate\Admin\Models\Prefabs;

class Settings extends \Dsc\Prefabs
{
    /**
     * Default document structure
     * @var array
     */
    protected $document = array(
        'general'=>array(
            'updater_mode' => '0'
        )
    );
}