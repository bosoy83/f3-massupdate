<?php $attrs = $model->getUpdateOperationGroups(); ?>

<div class="row">
	<div class="col-lg-5 col-md-5 col-sm-5 col-xs-12 pull-left">
	<table class="table table-stripped table-bordered">
	<tr>
	<td>
		<br/>
		<br/>
				<br/>
				<br/>
				<br/>
				<br/>
				<br/>
				<br/>
				<br/>
				<br/>
				<br/>
				<br/>
				<br/>
				<br/>
				<br/>
				<br/>
				<br/>
				<br/>
				<br/>
				<br/>
				<br/>
				<br/>
				<br/>
				<br/>
				<br/>
				<br/>
				<br/>
				<br/>
				<br/>
				<br/>
				<br/>
				<br/>
				<br/>
				<br/>
				<br/>
				<br/>
				<br/>
				<br/>
				<br/>
				<br/>
				<br/>
				<br/>
				<br/>
				<br/>
				<br/>
				<br/>
				<br/>
				<br/>
				<br/>
				<br/>
				<br/>
				<br/>
				<br/>
				<br/>
				<br/>
				<br/>
				<br/>
				<br/>
				<br/>
				<br/>
				<br/>
				<br/>
				<br/>
				<br/>
				<br/>
				<br/>
		</td>
	</tr>
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
</div>