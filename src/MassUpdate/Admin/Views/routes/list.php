<div class="row">
	<div class="col-xs-12 col-sm-7 col-md-7 col-lg-4">
		<h1 class="page-title txt-color-blueDark">
			<i class="fa fa-external-link fa-fw "></i> 
				Routes
			<span> > 
				List
			</span>
		</h1>
	</div>
	<div class="col-xs-12 col-sm-5 col-md-5 col-lg-8">
        <ul id="sparks" class="list-actions list-unstyled list-inline">
            <li>
                <a class="btn btn-default" href="./admin/redirect/route/create">Add New</a>
            </li>
        </ul>            	
	</div>
</div>

<div class="row">
    <div class="col-md-9">
        <form id="routes" class="searchForm" action="./admin/redirect/routes" method="post">
    		<?php echo $this->renderLayout('Redirect/Admin/Views::routes/list_datatable.php'); ?>    
        </form>
    </div>
    <div class="col-md-3">
	    <?php echo \Dsc\Request::internal( "\Redirect\Admin\Controllers\Route->quickadd" ); ?>	
    </div>
</div>