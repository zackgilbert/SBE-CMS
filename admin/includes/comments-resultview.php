	
	<div id="comments-<?= $comment->id; ?>" class="comments comment-item">
		<div class="comment-body">
			<?= format_text($comment->comment); ?> 
		</div>
		<p class="comment-byline">Commented in <a href="<?= $comment->parent('link'); ?>"><?= $comment->parent('title'); ?></a>, on <?= format_date($comment->created_at, 'm/d/y \a\t g:h:i a'); ?></p>
		
		<div class="comment-tools">
			<?php if (!is_string($comment->approved_at)) : ?> 
				<a href="<?= LOCATION; ?>admin/scripts/comments-approve/?id=<?= $comment->id; ?>" onclick="return commentApproval(this, <?= $comment->id; ?>);" class="approvelink">Approve</a>  
			<?php else : ?> 
				<a href="<?= LOCATION; ?>admin/scripts/comments-unapprove/?id=<?= $comment->id; ?>" onclick="return commentApproval(this, <?= $comment->id; ?>);" class="unapprovelink">Unapprove</a>  
			<?php endif; ?>
			<a href="<?= LOCATION; ?>admin/comments/<?= $comment->id; ?>/" class="editlink">Edit</a> 
			<a href="<?= LOCATION; ?>admin/comments/<?= $comment->id; ?>/delete/" class="deletelink">Delete</a>
		</div>

	</div>
	