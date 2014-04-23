	
	<div id="message-container">

		<div id="message" class="<?= message('type'); ?>">
			<a href="javascript:;" class="close" onclick="$(this.parentNode.parentNode).remove();">
				<img src="<?= LOCATION; ?>admin/images/icon-message-close.gif" alt="Close" title="Close this Message"/>
			</a>
			<img src="<?= LOCATION; ?>admin/images/icon-<?= message('type'); ?>.gif" alt=""/><br/>
			<p><?= message('message'); ?></p>
		</div>

		<script type="text/javascript">
			$('#message-container').hide().fadeIn(500)<?php if (message('type') == 'success') : ?>.animate({opacity:1.0}, 5000).fadeOut(500, function() { $(this).remove(); })<?php endif; ?>;
		</script>

	</div>
