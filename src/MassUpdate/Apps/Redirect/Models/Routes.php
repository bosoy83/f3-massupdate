<?php 
namespace MassUpdate\Apps\Redirect\Models;

class Routes extends \MassUpdate\Service\Models\Model {
	
	/**
	 * This method returns an instance of model
	 */
	public function getModel(){
		static $model = null;
		
		if( $model == null ){
			$model = new \Redirect\Admin\Models\Routes;
		}
		return $model;
	}
	
	/**
	 * This method gets list of attribute groups with operations
	 *
	 * @return	Array with attribute groups
	*/
	public function getOperationGroups(){
		if ($this->needInitialization())
		{		
			$attr_title = new \MassUpdate\Service\Models\AttributeGroup;
			$attr_title->setAttributeCollection('title')
			->setAttributeTitle( "Title" )
			->setParentModel( $this )
			->addOperation( new \MassUpdate\Operations\Update\ChangeTo )
			->addOperation( new \MassUpdate\Operations\Update\IncreaseBy )
			->addOperation( new \MassUpdate\Operations\Condition\CompareTo )
			->addOperation( new \MassUpdate\Operations\Condition\Contains, array( "filter" => 'title' ) )
			->addOperation( new \MassUpdate\Operations\Condition\EqualsTo );
		
			$attr_route = new \MassUpdate\Service\Models\AttributeGroup;
			$attr_route->setAttributeCollection('url.redirect')
			->setAttributeTitle( "Redirection" )
			->setParentModel( $this )
			->addOperation( new \MassUpdate\Operations\Update\ChangeTo )
			->addOperation( new \MassUpdate\Operations\Update\IncreaseBy )
			->addOperation( new \MassUpdate\Operations\Condition\Contains, array( "filter" => 'filter1' ) );
		
			$attr_empty = new \MassUpdate\Service\Models\AttributeGroup;
			$attr_empty->setAttributeCollection('url.alias')
			->setAttributeTitle( "Alias" )
			->setParentModel( $this )
			->addOperation( new \MassUpdate\Operations\Condition\Contains );
		
			$this->addAttributeGroup( $attr_title );
			$this->addAttributeGroup( $attr_empty );
			$this->addAttributeGroup( $attr_route );
		}
		
		return $this->getAttributeGroups();
	}
}