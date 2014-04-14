<table class="table table-striped" data-type="<?php echo $type; ?>">
<?php if( count( $attributes ) ) {
	foreach( $attributes as $attr ){ 
		$ops = $attr->getOperations($type);
		if( count( $ops ) > 0 ) {
		?>
	<tr class="info">
		<td colspan="2">
			<h3><?php echo $attr->getAttributeTitle(); ?></h3>
		</td>
	</tr>
		<?php
			$idx = 0;
			$name = $attr->getAttributeCollection()."_".$type."_cb";
			foreach( $ops as $op ) { 
				$op->setIndex( $idx );
		?>
			<tr>
				<td data-type-td="attr-check">
					<input type="checkbox" value="<?php echo $idx; ?>" name="<?php echo $name?>[]" data-group-attr="<?php echo $name?>"/>
				</td>
				<td>
					<div class="form-group clearfix">
			            <h4><?php echo $op->getLabel() ?></h4>
						<div data-operation="<?php echo $op->getNameWithIdx(); ?>">
							<?php echo $op->getFormHtml() ?>
						</div>
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
