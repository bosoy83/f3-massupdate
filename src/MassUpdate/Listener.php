<?php 
namespace MassUpdate;

class Listener extends \Prefab 
{
    public function onSystemRebuildMenu( $event )
    {
        if ($mapper = $event->getArgument('mapper')) 
        {
            $mapper->reset();
            $mapper->priority = 40;
            $mapper->id = 'f3-redirect';
            $mapper->title = 'Mass Update';
            $mapper->route = '';
            $mapper->icon = 'fa fa-signal';
            $mapper->children = array(
                    json_decode(json_encode(array( 'title'=>'List Updaters', 'route'=>'/admin/massupdate/updaters', 'icon'=>'fa fa-list' )))
            );
            $mapper->save();
            
            \Dsc\System::instance()->addMessage('Routes Manager added its admin menu items.');
        }
    }
}