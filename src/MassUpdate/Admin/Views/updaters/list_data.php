<?php 
	$attrs = $model->getMassUpdateOperationGroups();
?>
<form id="updaters" class="massupdaterForm" action="./admin/massupdate/updaters/<?php echo $selected_updater;?>/<?php echo $selected_model;?>" method="post">	
	<div class="col-lg-5 col-md-5 col-sm-5 col-xs-12 pull-left">
		<h2>Attributes</h2>
		<?php echo \Dsc\Request::internal( "\MassUpdate\Admin\Controllers\Updaters->getOperationsTableHtml", array( $attrs, "update" ) ); ?>
	</div>

	<div class="col-lg-5 col-md-5 col-sm-5 col-xs-12 pull-left">
		<h2>Conditions</h2>
		<?php echo \Dsc\Request::internal( "\MassUpdate\Admin\Controllers\Updaters->getOperationsTableHtml", array( $attrs, "where" ) ); ?>
	</div>
</form>