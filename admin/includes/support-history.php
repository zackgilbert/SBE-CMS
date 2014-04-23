
	<div class="support-item">
	
		<h3 class="support-item-title">Your Support Ticket History</h3>
	
		<table id="support-tickets" cellspacing="0">
			<tr>
				<th>Ticket #</th>
				<th>Submission Date</th>
				<th>Message</th>
			</tr>
			<?php foreach ($tickets = get_support_tickets() as $key => $ticket) : ?> 
			<tr id="ticket-<?= $ticket['id']; ?>" class="row<?= $key%2; ?>">
				<td align="center" valign="top">
					#<?= $ticket['id']; ?> 
				</td>
				<td valign="top">
					<?= format_date($ticket['created_at'], 'm/d/Y'); ?> 
				</td>
				<td>
					<strong><?= $ticket['subject']; ?></strong><br />
					<?= nl2br($ticket['message']); ?> 
				</td>
			</tr>
			<?php endforeach; ?> 
			<?php if (count($tickets) < 1) : ?> 
			<tr>
				<td colspan="3" align="center">
					No tickets have been submitted.
				</td>
			</tr>
			<?php endif; ?> 
		</table>

	</div>	