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
	<div class="row">
		<div class="col-xs-10 col-sm-6 col-md-6 col-lg-4">
			<div class="form-group">
				<div class="input-group">
					<select id="bulk-actions" name="bulk_action" class="form-control">
						<option value="null">-Pick Updater-</option>
<?php
		$updaters = $service->getGroups();
		if( count( $updaters ) > 0 ) {
			foreach( $updaters as $updater ){ ?>
						<option value="<?php echo $updater->slug;?>"
							data-action="./admin/massupdate/updaters/<?php echo $updater->slug;?>" 
							<?php echo $selected === $updater->slug ? 'selected' : '';?>>
							<?php echo $updater->title;?>
						</option>
<?php 
			}
		} ?>
					</select>
					<span class="input-group-btn">
						<button class="btn btn-default bulk-actions" type="button" data-target="bulk-actions">Apply</button>
					</span>
				</div>
			</div>
		</div>
	</div>
		
	<div class="row">
		<form id="routes" class="searchForm" action="./admin/massupdate/updaters" method="post">
			dude
		</form>
	</div>
</form>