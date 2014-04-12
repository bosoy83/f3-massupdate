<?php 
namespace MassUpdate\Service\Models;

/**
 * Main moodel for storing list of supported update operations for a certain attribute in model
 */
class AttributeGroup extends \Prefab 
{
	/**
	 * Name of attribute in collection
	 */
	protected $attribute;
	
	/**
	 * Name of attribute displayed in system
	 */
	protected $title;
	
	/**
	 * Namea of group of models this group of attributes is assigned to
	 * @var unknown
	 */
	protected $group_name;
	
	/**
	 * Model to which the group is associated
	 */
	protected $model;
	
	/**
	 * List of operations
	 */
	protected $operations = array();

	/**
	 * Mode of updater
	 */
	protected $updater_mode;
	
	/**
	 * This method sets name of attribute in collection for this group of operations
	 * 
	 * @param $attr Name in collection
	 * 
	 * @return Instance of this class in order to support chaining of operations
	 */
	public function setAttributeCollection($attr){
		$this->attribute = $attr;
		return $this;
	}

	/**
	 * This method gets name of attribute in collection for this group of operations
	 *
	 * @return Name in collection
	 */
	public function getAttributeCollection(){
		return $this->attribute;
	}
	
	/**
	 * Sets name of this attribute group which will appear in system
	 * 
	 * @param $t	Name of the group
	 * 
	 * @return Instance of this class in order to support chaining of operations
	 */
	public function setAttributeTitle( $t ){
		$this->title = $t;
		return $this;
	}
	/**
	 * Gets name of this attribute group which will appear in system
	 *
	 * @return	Name of the group
	 */
	public function getAttributeTitle(){
		return $this->title;
	}
	
	
	/**
	 * This method adds operation into list of operations for this group
	 * 
	 * @param $op 		Instance of update operation
	 * @param $type		Type of operation (so far, either Condition or Update)
	 * @param $params	Parameters which can be passed to operations during adding prcess
	 * 
	 * @return Instance of this class in order to support chaining of operations
	 */
	public function addOperation( $op, $type, $params = array() ){
		if( $op instanceof \MassUpdate\Operations\Operation ){
			$op->setAttribute( $this );
			if( !isset( $this->operations[$type] ) ){
				$this->operations[$type] = array();
			}
			$this->operations[$type] []= $op;
			$op->setParams( $params );
			
			
		} else { // warn us, if we pass here instance of an unsupported object
			throw new \Exception( "Unsupported Operation object" );
		}
		return $this;
	}
	
	/**
	 * This method returns array of all operations for this group
	 * @param $type		Type of operation (so far, either Condition or Update)
	 * 
	 * @return Array of all operations for this group
	 */
	public function getOperations($type){
		if( !isset($this->operations[$type] ) ){
			return array();
		} else {
			return $this->operations[$type];
		}
	}
	
	/**
	 * This method returns instance of input filter assigned to this attribute group
	 *
	 * @return Instance of InputFilter
	 */
	public function getInputFilter(){
		return \Dsc\System::instance()->get('inputfilter');
	}
	
	/**
	 * This method sets updater mode for this operation
	 * 
	 * @param $mode		Current updater mode
	 * 
	 * @return	Pointer to this instance in order to support chaining
	 */
	public function setUpdaterMode($mode){
		$this->updater_mode = $mode;
		return $this;
	}
	
	/**
	 * This method gets updater mode for this operation
	 * 
	 * @return	Current updater mode
	 */
	public function getUpdaterMode(){
		return $this->updater_mode;
	}
	
	/**
	 * This method sets  model associated to this attribute
	 * 
	 * @param $model	Current model
	 * 
	 * @return	Pointer to this instance in order to support chaining
	 */
	public function setModel($model){
		$this->model = clone $model;
		return $this;
	}
	
	/**
	 * This method gets model associated to this attribute
	 * 
	 * @return Instance of model associatedto this attribute
	 */
	public function getModel(){
		return $this->model;
	}
	
	/**
	 * This method sets name of group of models this attribute group is assigned to
	 * 
	 * @param $group	Current group name
	 * 
	 * @return	Pointer to this instance in order to support chaining
	 */
	public function setGroupName($group){
		$this->group_name = $group;
		return $this;
	}
	
	/**
	 * This method gets name of group of models this attribute group is assigned to
	 * 
	 * @return Name of group of models this attribute group is assigned to
	 */
	public function getGroupName(){
		return $this->group_name;
	}
}