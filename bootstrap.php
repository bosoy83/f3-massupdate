<?php 
$f3 = \Base::instance();
$global_app_name = $f3->get('APP_NAME');

switch ($global_app_name) 
{
    case "admin":
        // register event listener
        \Dsc\System::instance()->getDispatcher()->addListener(\MassUpdate\Listener::instance());

        // register all the routes        
        \Dsc\System::instance()->get('router')->mount( new \MassUpdate\Routes, 'massupdate' );
        
        // append this app's UI folder to the path
        // new way
        \Dsc\System::instance()->get('theme')->registerViewPath( __dir__ . '/src/MassUpdate/Admin/Views/', 'MassUpdate/Admin/Views' );
        
        // TODO set some app-specific settings, if desired
                
        break;
    case "site":        
        // TODO set some app-specific settings, if desired
        break;
}
?>
