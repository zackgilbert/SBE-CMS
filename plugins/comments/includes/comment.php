
	<div id="comment-<?= $comment->id; ?>" class="comment<?= $count%2; ?> comment-item">
		<h4 class="comment-name">
			<?= $comment->authorlink(); ?> 
			<span class="comment-time">said <span title="<?= format_date($comment->created_at, 'M. d, Y \a\t g:i'); ?>"><?= ago($comment->created_at); ?></span></span>
		</h4>
		<div class="comment-body">
			<?= format_text($comment->comment); ?> 
		</div>
	</div>
