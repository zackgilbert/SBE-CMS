
	<div class="support-item">
	
		<h3 class="support-item-title">Submit A New Support Ticket</h3>
	
		<form method="post" action="<?= LOCATION; ?>admin/support/save/" enctype="multipart/form-data" class="support-ticket">
	
			<div class="support-ticket-item">
				<label for="subject">Subject:</label>
				<select name="ticket[subject]" id="subject">
					<?php foreach (array("I'm having a problem with my site", "I'm having a problem in the admin", "I can't figure out how to...", "Other") as $subject) : ?> 
					<option value="<?= $subject; ?>"<?= (value('ticket[subject]') == $subject) ? ' selected="selected"' : ''; ?>><?= $subject; ?></option>
					<?php endforeach; ?>
				</select>
			</div>
	
	
			<div class="support-ticket-item">
				<label for="body">Message:</label>
				<textarea name="ticket[message]" id="body" rows="4" cols="40"></textarea>
			</div>
	
			<p class="submit-ticket">
				<input type="submit" id="submit" value="Submit Ticket" class="submit-ticket"/>
			</p>
	
		</form>
		
		<div class="support-item-direct">
			<p>If you'd rather contact us directly for support, you can email <em><?= SUPPORT_NAME; ?></em> at <a href="mailto:<?= SUPPORT_EMAIL; ?>"><?= SUPPORT_EMAIL; ?></a>.</p>
		</div>

	</div>
