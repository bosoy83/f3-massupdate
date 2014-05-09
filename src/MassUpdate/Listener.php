<?php 
namespace MassUpdate;

class Listener extends \Prefab 
{
    public function onSystemRebuildMenu( $event )
    {
    	if ($model = $event->getArgument('model'))
    	{
    		$root = $event->getArgument( 'root' );
    		$update = clone $model;
    	
    		$update->insert(
    				array(
    						'type'	=> 'admin.nav',
    						'priority' => 60,
    						'title'	=> 'Mass Update',
    						'icon'	=> 'fa fa-signal',
        					'is_root' => false,
    						'tree'	=> $root,
    						'base' => '/admin/massupdate/',
    				)
    		);
    		
            $children = array(
                    array( 'title'=>'List Updaters', 'route'=>'/admin/massupdate/updaters', 'icon'=>'fa fa-list' ),
                    array( 'title'=>'Settings', 'route'=>'/admin/massupdate/settings', 'icon'=>'fa fa-cogs' ),
            );
           	$update->addChildrenItems( $children, $root, $model );
            
            \Dsc\System::instance()->addMessage('Mass Update added its admin menu items.');
        }
    }
}