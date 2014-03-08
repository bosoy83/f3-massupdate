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
			if( in_array( $model['updater'], $updaters_list ) === false ){
				$act_updater = 'title: "'.$model['title_updater'].'",';
				$act_updater.= 'slug: "'.$model['updater'].'"';
				$updaters_js []= '{'.$act_updater.'}';;
				$updaters_list []= $model['updater'];
			}
		}
	}
?>
<script type="text/javascript">
Dsc.MassUpdateModels = [<?php echo implode( ',', $models_js ); ?>];
Dsc.MassUpdateUpdaters = [<?php echo implode( ',', $updaters_js ); ?>];

Dsc.MassUpdateUpdateSelectUpdaters = function(selected){
	var updater_select = $('#updater-group');

	updater_select.empty();
	$('<option></option>', 
		{
			'text' : "-Pick Updater-",
			'data-slug' : "",
			'value' : "null"
		} ).appendTo( updater_select );

	for( i = 0; i < Dsc.MassUpdateUpdaters.length; i++ ) {
		var link = "./admin/massupdate/updaters/" + Dsc.MassUpdateUpdaters[i]['slug'];
		var opt = $('<option></option>', 
				{
					'text' : Dsc.MassUpdateUpdaters[i]['title'],
					'data-slug' : Dsc.MassUpdateUpdaters[i]['slug'],
					'data-action' : link
				});

		if( selected == Dsc.MassUpdateUpdaters[i]['slug'] ){
			opt.attr( "selected", "true" );
		}
		opt.appendTo( updater_select );
	}
}

Dsc.MassUpdateUpdateSelectModels = function(selected_model, selected_updater){
	var model_select = $('#updater-model');

	model_select.empty();
	$('<option></option>', 
		{
			'text' : "-Pick Model-",
			'value' : "null"
		} ).appendTo( model_select );

	for( i = 0; i < Dsc.MassUpdateModels.length; i++ ) {
		if( Dsc.MassUpdateModels[i]['updater_slug'] == selected_updater ) {
			var link = "./admin/massupdate/updaters/" 
							+ Dsc.MassUpdateModels[i]['updater_slug'] + "/"
							+ Dsc.MassUpdateModels[i]['model_slug'];
			var opt = $('<option></option>', 
					{
						'text' : Dsc.MassUpdateModels[i]['title'],
						'data-action' : link
					});

			if( selected_model == Dsc.MassUpdateModels[i]['model_slug'] ){
				opt.attr( "selected", "true" );
			}
			opt.appendTo( model_select );
		}
	}
}

$(function(){
	Dsc.MassUpdateUpdateSelectUpdaters("<?php echo $selected_updater; ?>");
	Dsc.MassUpdateUpdateSelectModels("<?php echo $selected_model; ?>", "<?php echo $selected_updater; ?>");

	$( '#updater-group' ).on( "change", function(){
			var $opt = $("option:selected", $(this));
			Dsc.MassUpdateUpdateSelectModels("", $opt.data('slug'));
		}); 

	$( '#updater-models' ).on( "change", function(){
		// loads form with values to update
	}); 
});
</script>

<div class="col-xs-10 col-sm-6 col-md-6 col-lg-4">
	<div class="form-group pull-left">
		<div class="input-group">
			<select id="updater-group" name="updater_group" class="form-control">
			</select>
		</div>
	</div>
	<div class="form-group pull-left">
		<div class="input-group pull-right">
			<select id="updater-model" name="updater_model" class="form-control">
			</select>
		</div>
	</div>
</div>
