<form id="settings-form" role="form" method="post" class="form-horizontal clearfix">

    <div class="form-actions clearfix">
        <button type="submit" class="btn btn-primary pull-right">Save Changes</button>
    </div>
    
    <hr/>

    <div class="row">
        <div class="col-md-3 col-sm-4">
            <ul class="nav nav-pills nav-stacked">
                <li class="active">
                    <a href="#tab-general" data-toggle="tab"> General Settings </a>
                </li>            
            </ul>
        </div>

        <div class="col-md-9 col-sm-8">

            <div class="tab-content stacked-content">
            
                <div class="tab-pane fade in active" id="tab-general">
					<h3 class="">General Settings</h3>
					
					<div class="form-group">
					    <label class="control-label col-md-3">Mode</label>
			            <div class="input-group">
							<select id="updater-mode" name="general[updater_mode]" class="form-control">
								<option value="0" <?php if( $flash->old('general.updater_mode') == 0) echo 'selected'; ?>>Bulk update (0)</option>
								<option value="1" <?php if( $flash->old('general.updater_mode') == 1) echo 'selected'; ?>>Document-by-document update (1)</option>
							</select>
		            	</div>
					</div>
				</div>
            </div>

        </div>
    </div>

</form>