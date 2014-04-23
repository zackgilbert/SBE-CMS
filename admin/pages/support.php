	
	<div id="content">
	
		<div id="content-header">
			<p class="header-section">Support</p>	
			<h2 class="header-title">
				Have a Problem? We're here to help. 				
			</h2>
		</div>
		

		<div id="content-2colR-left">
	
			<div id="support-sidebar">
			
				<h3 class="support-sidebar-title">Select an Item</h3>
				
				<ul class="support-items">
					<li<?= ($support == 'ticket') ? ' class="selected"' : ''; ?>><a href="<?= LOCATION; ?>admin/support/">Submit A Ticket</a></li>
					<li<?= ($support == 'history') ? ' class="selected"' : ''; ?>><a href="<?= LOCATION; ?>admin/support/history/">Your Support History</a></li>
				</ul> 
				
			</div>
			
		</div>
	
		<div id="content-2colR-right">
				
			<?php load_include('support-' . $support); ?> 
			
		</div>
		
	</div>
	