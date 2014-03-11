<script type="text/javascript">
jQuery(function(){

	Dsc.MassUpdateHandleCheckboxSelectGroup = function(event){
		$this = jQuery(event.currentTarget);

		console.log(event.currentTarget);
		var group = $this.data("group-attr");
		if( this.checked ){
			jQuery("#updater-data :checkbox[data-group-attr='"+group+"']").removeAttr("checked");
			$this.prop("checked", "true");
		}
	}

	jQuery("#updater-data").on("change", ":checkbox", Dsc.MassUpdateHandleCheckboxSelectGroup);
	jQuery("form#updater").on("focus", "input:not(:checkbox)", Dsc.MassUpdateHandleCheckboxSelectGroup);
	
});
</script>

<div class="row">
	<div class="col-xs-12 col-sm-7 col-md-7 col-lg-4">
		<h1 class="page-title txt-color-blueDark">
			<i class="fa fa-signal fa-fw "></i> 
				Mass Update
			<span> > 
				Updaters
			</span>
		</h1>
	</div>
</div>


<form id="updaters" class="updatersForm" action="./admin/massupdate/updater" method="post">
	<div class="no-padding">
		<div class="widget-body-toolbar">
			<div class="row">
		        <?php echo $this->renderLayout('MassUpdate/Admin/Views::updaters/list_toolbar.php'); ?>
			</div>
		</div>
	</div>	
	<div class="row" id="updater-data">
		<?php echo \Dsc\Request::internal( "\MassUpdate\Admin\Controllers\Updaters->getUpdaterData", array( $selected_updater, $selected_model ) ); ?>
	</div>
	<hr />
	
</form>
