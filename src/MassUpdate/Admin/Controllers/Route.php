<?php 
namespace Redirect\Admin\Controllers;

class Route extends \Admin\Controllers\BaseAuth
{
    use \Dsc\Traits\Controllers\CrudItemCollection;

    protected $list_route = '/admin/redirect/routes';
    protected $create_item_route = '/admin/redirect/route/create';
    protected $get_item_route = '/admin/redirect/route/read/{id}';    
    protected $edit_item_route = '/admin/redirect/route/edit/{id}';
    
    protected function getModel() 
    {
        $model = new \Redirect\Admin\Models\Routes;
        return $model; 
    }
    
    protected function getItem() 
    {
        $f3 = \Base::instance();
        $id = $this->inputfilter->clean( $f3->get('PARAMS.id'), 'alnum' );
        $model = $this->getModel()
            ->setState('filter.id', $id);

        try {
            $item = $model->getItem();
        } catch ( \Exception $e ) {
            \Dsc\System::instance()->addMessage( "Invalid Item: " . $e->getMessage(), 'error');
            $f3->reroute( $this->list_route );
            return;
        }

        return $item;
    }
    
    protected function displayCreate() 
    {
        $f3 = \Base::instance();
        $f3->set('pagetitle', 'Create New Route');
        
        echo \Dsc\System::instance()->get('theme')->render('Redirect/Admin/Views::routes/create.php');
    }
    
    protected function displayEdit()
    {
        $f3 = \Base::instance();
        $f3->set('pagetitle', 'Edit Route');
        
        echo \Dsc\System::instance()->get('theme')->render('Redirect/Admin/Views::routes/edit.php');
    }
    
    /**
     * This controller doesn't allow reading, only editing, so redirect to the edit method
     */
    protected function doRead(array $data, $key=null) 
    {
        $f3 = \Base::instance();
        $id = $this->getItem()->get( $this->getItemKey() );
        $route = str_replace('{id}', $id, $this->edit_item_route );
        $f3->reroute( $route );
    }
    
    protected function displayRead() {}
    
    public function quickadd()
    {
    	echo \Dsc\System::instance()->get('theme')->renderView('Redirect/Admin/Views::routes/quickadd.php');
    }
}