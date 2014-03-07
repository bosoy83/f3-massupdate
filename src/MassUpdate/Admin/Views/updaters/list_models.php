<div class="col-xs-10 col-sm-6 col-md-6 col-lg-4">
	<div class="form-group">
		<div class="input-group">
			<select id="updater-group" name="updater_group" class="form-control">
				<option value="null">-Pick Updater-</option>
	<?php
			$updaters = $service->getGroups();
			if( count( $updaters ) > 0 ) {
				foreach( $updaters as $updater ){ ?>
				<option value="<?php echo $updater->slug;?>"
					data-action="./admin/massupdate/updaters/<?php echo $updater->slug;?>" 
					<?php echo $selected == $updater->slug ? 'selected' : '';?>>
					<?php echo $updater->title;?>
				</option>
	<?php 
				}
			} ?>
			</select>
		</div>
	</div>
</div>
<div class="col-xs-10 col-sm-6 col-md-6 col-lg-4">
	<div class="form-group">
		<div class="input-group">
			<select id="updater-model" name="updater_model" class="form-control">
		    <template type="text/template" id="udpdater-model-template">
		    	<option value="./admin/massupdate/updaters/{updater}/{model}">{title}</option>
		    </template>
			<option value="null">-Pick Model-</option>
		</select>
	</div>
</div>
</div>
