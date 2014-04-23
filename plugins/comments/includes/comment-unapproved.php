	
	<div id="comment-<?= $comment->id; ?>" class="comment<?= $count%2; ?> comment-item unapproved">
		<h4 class="comment-name">
			<?= $comment->authorlink(); ?> 
			<span class="comment-time">said <span title="<?= format_date($comment->created_at, 'M. d, Y \a\t g:i'); ?>"><?= ago($comment->created_at); ?></span></span>
		</h4>
		<div class="comment-body">
			<p class="comment-pending">This comment pending approval by City. Once approved, the full text will be displayed.</p>
		</div>		
	</div>
	