<?php

use FintechFab\MoneyTransferEmulator\Models\Payment;

/**
 * @var Payment[]|Illuminate\Pagination\Paginator $payments
 */

?>
	<table class="table table-striped table-hover">
		<tr>
			<th>id</th>
			<th>created</th>
			<th>type</th>
			<th>code</th>
			<th>amount</th>
			<th>cur</th>
			<th>city</th>
			<th>from</th>
			<th>to</th>
			<th>status</th>
			<th>actions</th>
		</tr>
		<?php foreach ($payments as $payment) { ?>
			<tr>
				<td><?= $payment->id ?></td>
				<td><?= $payment->created_at ?></td>
				<td><?= $payment->type ?></td>
				<td><?= $payment->code ?></td>
				<td><?= $payment->amount ?></td>
				<td><?= $payment->cur ?></td>
				<td><?= $payment->cityName() ?></td>
				<td><?= $payment->from ?></td>
				<td><?= $payment->to ?></td>
				<td class="status"><?= $payment->status ?></td>
				<td>
					<?php
					if ($payment->getPossibleStatus()) {
						echo Form::button('Move to [' . $payment->getPossibleStatus() . ']', array(
							'class'   => 'post-approve',
							'data-id' => $payment->id,
						));
					}
					?>
				</td>
			</tr>
		<?php } ?>
	</table>
<?= $payments->links();