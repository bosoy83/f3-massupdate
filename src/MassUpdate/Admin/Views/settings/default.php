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
					    <label class="control-label col-md-3">Default Error route</label>
			            <div class="input-group">
							<span class="input-group-addon">/</span>
		            			<input type="text" name="general[default_error_404]" placeholder="Error route" value="<?php echo $flash->old('general.default_error_404') ?>" class="form-control" />
		            	</div>
					</div>
				</div>
            </div>

        </div>
    </div>

</form>