<?php //print_debug($order->toArray(),true); ?>
<div class="">
	<div class="row">
		<div class="col-md-6 order-address">
			<?php foreach($order->addresses as $address): ?>
				<p>
					<strong><?php echo $address->firstname; ?> <?php echo $address->lastname; ?></strong><br>
					<?php if(!empty($address->company)) echo $address->company . "<br>"; ?>
					<?php echo $address->street; ?><br>
					<?php echo $address->city; ?>, <?php echo $address->region; ?> <?php echo $address->postcode; ?><br>
					<a href="mailto:#"><?php echo $address->customer_email; ?></a>
				</p>
			<?php endforeach; ?>
		</div>
		<div class="col-md-6 order-info">
			<p>Order Status: <?php echo ucfirst($order->status); ?><br>
			Order Number: #<?php echo str_pad($order->entity_id, 8, '0', STR_PAD_LEFT); ?><br>
			Order Total: $<?php echo number_format($order->grand_total,2); ?></p>
		</div>
	</div>
	<hr />
	<div class="row">
		<div class="col-md-6">
			<?php echo apply_filters( 'the_content', $default_post->post_content ); ?>
		</div>
		<div class="col-md-6">

			<div id="signature-div" style="<?php echo $signature_display; ?>">
				<div class="alert alert-success"><?php echo __('<strong>Success!</strong> Order successfully authorized. Please check your email for further info.','laskbrook-magento-integration'); ?></div>
				<img src="<?php echo $signature_source; ?>" id="user-signature" />
			</div>
			<?php if(empty($signature_source)): ?>
				<div id="auth-signature"></div>
				<p class="text-center"><?php echo __('Sign above the line.','laskbrook-magento-integration'); ?></p>
				<div class="text-center">
					<button class="btn btn-primary" id="authorize-signature">Authorize</button> <button class="btn btn-secondary" id="reset-signature">Reset</button>
				</div>
			<?php endif; ?>
		</div>
	</div>
	<hr />
	<div class="row">
		<table class="table m_order-table">
			<thead>
				<th><?php echo __('Qty','laskbrook-magento-integration'); ?></th>
				<th><?php echo __('Item','laskbrook-magento-integration'); ?></th>
				<th><?php echo __('Price','laskbrook-magento-integration'); ?></th>
				<th><?php echo __('Total','laskbrook-magento-integration'); ?></th>
			</thead>
			<tbody>
				<?php 
				foreach($order->items as $item):
					if($item->parent_item_id) continue;
					$options = unserialize($item->product_options);
					//print_debug($options);
			?>
				<tr scope="row">
					<td><?php echo number_format($item->qty_ordered,0); ?></td>
					<td>
						<?php echo $item->name;?>
						<?php foreach($options['additional_options'] as $attribute):
							$l = explode(":",serialize($attribute['label']));
							$att_label = preg_replace('/\\"|;s/', "", $l[8]);
							$att_value = $attribute['value'];
						?>
							<div class="product-attribute"><?php echo $att_label . " : " . $attribute['value'] ?></div>

						<?php endforeach; ?>
					</td>
					<td class="text-right">$<?php echo number_format($item->base_price,2); ?></td>
					<td class="text-right">$<?php echo number_format($item->row_total,2); ?></td>
				</tr>
				<?php endforeach; ?>
				<tr>
					<td>&nbsp;</td>
                    <td class="text-right">Shipping:</strong></td>
                    <td>&nbsp;</td>
                    <td class="text-right">$<?php echo number_format($order->shipping_amount,2); ?></td>
				</tr>
				<tr>
                    <td>&nbsp;</td>
                    <td class="text-right">Tax:</strong></td>
                    <td>&nbsp;</td>
                    <td class="text-right">$<?php echo number_format($order->tax_amount,2); ?></td>
                </tr>
				<tr>
					<td>&nbsp;</td>
					<td class="text-right"><strong>Total:</strong></td>
					<td>&nbsp;</td>
					<td class="text-right">$<?php echo number_format($order->grand_total,2); ?></td>
				</tr>
			</tbody>
		</table>
	</div>