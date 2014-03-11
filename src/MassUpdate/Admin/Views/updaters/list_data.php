<?php 
	$attrs = $model->getUpdateOperationGroups();
	$conditions = $model->getUpdateCondition
?>
<form id="routes" class="searchForm" action="./admin/massupdate/updaters" method="post">	
	<div class="col-lg-5 col-md-5 col-sm-5 col-xs-12 pull-left">
	<h2>Attributes</h2>
		<table class="table table-striped">
		<?php if( count( $attrs ) ) {
			 foreach( $attrs as $attr ){ 
				$ops = $attr->getOperations();
				if( count( $ops ) > 0 ) {
				?>
			<tr class="info">
				<td colspan="2">
					<h3><?php echo $attr->getAttributeTitle(); ?></h3>
				</td>
			</tr>
				<?php
					$idx = 0;
					$name = $attr->getAttributeCollection()."_cb";
					foreach( $ops as $op ) { 
						$op->setIndex( $idx );
				?>
					<tr>
						<td>
							<input type="checkbox" value="<?php echo $idx; ?>" name="<?php echo $name?>[]" data-group-attr="<?php echo $name?>"/>
						</td>
						<td>
							<div class="form-group clearfix">
					            <h4><?php echo $op->getLabel() ?></h4>
									<?php echo $op->getFormHtml() ?>
			        	    </div>
					    </td>
					</tr>
			
		<?php		
						$idx++;
					} ?>
				<?php }
				}
			?>
			
			</ul>
		<?php } ?>
		</table>
	</div>
	<div class="col-lg-5 col-md-5 col-sm-5 col-xs-12 pull-left">
		<h2>Conditions</h2>
	</div>
	<div class="col-lg-2 col-md-2 col-sm-2" style="position: absolute; right : 0px; top:230px;>
		<h2>Attributes</h2>
		<?php if( count( $attrs ) ) { ?>
			<ul class="nav">
			<?php 
				 foreach( $attrs as $attr ){ ?>
				 <li><?php echo $attr->getAttributeTitle(); ?></li>
				<?php }
			?>
			
			</ul>
		<?php } ?>
	</div>
</form>