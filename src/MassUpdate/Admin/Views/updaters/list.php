<?php 
	$f3 = \Base::instance();
	// generate array with all models
	$models = $f3->get('models');
	$models_js = array();
	$updaters_js = array();
	$updaters_list = array();
	if( count( $models ) > 1){
		foreach( $models as $model ){
			$act_model = 'title: "'.$model['title'].'",';
			$act_model.= 'model_slug: "'.$model['slug'].'",';
			$act_model.= 'updater_slug: "'.$model['updater'].'"';
			$models_js []= '{'.$act_model.'}';
			if( in_array( $model['updater'], $updaters_list ) == false ){
				$act_updater = 'title: "'.$model['title_updater'].'",';
				$act_updater.= 'slug: "'.$model['updater'].'"';
				$updaters_js []= '{'.$act_updater.'}';;
				$updaters_list []= $model['updater'];
			}
		}
	}
?>
<script type="text/javascript">
Dsc.MassUpdate = {};
Dsc.MassUpdate.Models = [<?php echo implode( ',', $models_js ); ?>];
Dsc.MassUpdate.Updaters = [<?php echo implode( ',', $updaters_js ); ?>];

Dsc.MassUpdate.UpdateSelectUpdaters = function(selected){
	var updater_select = $('#updater-group');

	Dsc.MassUpdate.Elements.select_updater.empty();
	$('<option></option>', 
		{
			'text' : "-Pick Updater-",
			'data-slug' : "",
			'value' : "null"
		} ).appendTo( Dsc.MassUpdate.Elements.select_updater );

	for( i = 0; i < Dsc.MassUpdate.Updaters.length; i++ ) {
		var link = "/admin/massupdate/updaters/" + Dsc.MassUpdate.Updaters[i]['slug'];
		var opt = $('<option></option>', 
				{
					'text' : Dsc.MassUpdate.Updaters[i]['title'],
					'data-slug' : Dsc.MassUpdate.Updaters[i]['slug'],
					'data-action' : link
				});

		if( selected == Dsc.MassUpdate.Updaters[i]['slug'] ){
			opt.attr( "selected", "true" );
		}
		opt.appendTo( Dsc.MassUpdate.Elements.select_updater );
	}
	if(selected.length == 0){
		Dsc.MassUpdate.DisableUI();
	} 
}

Dsc.MassUpdate.UpdateSelectModels = function(selected_model, selected_updater){
	Dsc.MassUpdate.Elements.select_model.empty();
	$('<option></option>', 
		{
			'text' : "-Pick Model-",
			'data-action' : ""
		} ).appendTo( Dsc.MassUpdate.Elements.select_model );

	for( i = 0; i < Dsc.MassUpdate.Models.length; i++ ) {
		if( Dsc.MassUpdate.Models[i]['updater_slug'] == selected_updater ) {
			var link = "/admin/massupdate/updaters/" 
							+ Dsc.MassUpdate.Models[i]['updater_slug'] + "/"
							+ Dsc.MassUpdate.Models[i]['model_slug'];
			var opt = $('<option></option>', 
					{
						'text' : Dsc.MassUpdate.Models[i]['title'],
						'data-action' : link
					});

			if( selected_model == Dsc.MassUpdate.Models[i]['model_slug'] ){
				opt.attr( "selected", "true" );
			}
			opt.appendTo( Dsc.MassUpdate.Elements.select_model );
		}
	}
}

Dsc.MassUpdate.DisableUI = function(){
	Dsc.MassUpdate.Elements.perform.hide();
	Dsc.MassUpdate.Elements.data_wrapper.empty();
}

Dsc.MassUpdate.CacheElements = function(){
	Dsc.MassUpdate.Elements = {};
	Dsc.MassUpdate.Elements.form = jQuery("form#updaters");
	Dsc.MassUpdate.Elements.data_wrapper = jQuery("#updater-data");
	Dsc.MassUpdate.Elements.select_updater = jQuery("select#updater-group");
	Dsc.MassUpdate.Elements.select_model = jQuery("select#updater-model");
	Dsc.MassUpdate.Elements.perform = jQuery("#perform-update");
}

jQuery(function(){
	Dsc.MassUpdate.CacheElements();
	Dsc.MassUpdate.UpdateSelectUpdaters("<?php echo $selected_updater; ?>");
	Dsc.MassUpdate.UpdateSelectModels("<?php echo $selected_model; ?>", "<?php echo $selected_updater; ?>");

	
	Dsc.MassUpdate.Elements.select_updater.on( "change", function(){
			var $opt = jQuery("option:selected", $(this));
			Dsc.MassUpdate.UpdateSelectModels("", $opt.data('slug'));
			Dsc.MassUpdate.Elements.perform.hide();
		}); 

	Dsc.MassUpdate.Elements.select_model.on( "change", function(){
		var this_url = $( "option:selected", $(this)).data("action");
		if( this_url.length > 0 ){
			Dsc.MassUpdate.Elements.perform.show();
		    var request = jQuery.ajax({
		        type: 'get', 
		        url: this_url
		    }).done(function(data){
		        var lr = jQuery.parseJSON( JSON.stringify(data), false);
		        if (lr.result) {
		        	Dsc.MassUpdate.Elements.data_wrapper.html(lr.result);
		        }
		    });
		} else {
			Dsc.MassUpdate.DisableUI();
		}
	}); 

	Dsc.MassUpdate.Elements.perform.on("click", function(){
		// lets first check, if any update operation is selected
		$upd_checks = jQuery( "table[data-type=\"update\"] td[data-type-td=\"attr-check\"] :checked", Dsc.MassUpdate.Elements.form );

		if( $upd_checks.size() == 0 ){
			alert( "Please, select at least one attribute you want to update." );
		} else {
			// now, make sure that user is OK with updating without any where conditions (in case this happens)
			var do_submit = false;

			$where_checks = jQuery( "table[data-type=\"where\"] td[data-type-td=\"attr-check\"] :checked", Dsc.MassUpdate.Elements.form );
			if( $where_checks.size() == 0 ){
				if( confirm( "Do you really want to perform update on all records?" ) ) { // yop, wer got the green light from him
					do_submit = true;
				}
			} else{ // there are some checks so just update
				do_submit = true;
			}
			if( do_submit ) {
				$("form#updaters").submit();
			}
		}
	});

	Dsc.MassUpdate.HandleCheckboxSelectGroup = function(event){
		$this = jQuery(event.currentTarget);

		var group = $this.data("group-attr");
		if( this.checked ){
			jQuery(":checkbox[data-group-attr='"+group+"']", Dsc.MassUpdate.Elements.data_wrapper).removeAttr("checked");
			$this.prop("checked", "true");
		}
	}

	Dsc.MassUpdate.Elements.data_wrapper.on("change", ":checkbox", Dsc.MassUpdate.HandleCheckboxSelectGroup);

	jQuery("#system-message-container").on('click', 'a.link_updater_mode_settings', function(event){
		event.preventDefault();
		$this = jQuery(event.currentTarget);
		var mode_num = $this.data('updater-mode');
		var link_url = '/admin/massupdate/settings/mode/'+mode_num;
	    var request = jQuery.ajax({
	        type: 'get', 
	        url: link_url
	    }).done(function(data){
	        var lr = jQuery.parseJSON( JSON.stringify(data), false);
	        if (lr.result) {
	        	alert(lr.result);
	        }
	    });
	});
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


<div class="no-padding">
	<div class="widget-body-toolbar">
		<div class="row">
	        <?php echo $this->renderLayout('MassUpdate/Admin/Views::updaters/list_toolbar.php'); ?>
		</div>
	</div>
</div>	
<hr />
<div class="row" id="updater-data">
	<?php echo \Dsc\Request::internal( "\MassUpdate\Admin\Controllers\Updaters->getUpdaterData", array( $selected_updater, $selected_model ) ); ?>
</div>
