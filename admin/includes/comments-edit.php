	
	<?php if (is_string($comment->deleted_at)) : ?> 
		
		<div class="deleted-content">
			
			<p>This Comment Has Been Deleted!</p>
			
		</div>
		
	<?php endif; ?>
	
	<div class="comment-edit">
	
		<form method="post" action="<?= LOCATION; ?>admin/plugins/comments/scripts/comments-save/">
			<p>
				<input type="hidden" name="required" value="comments[name],comments[email],comments[comment]" />
				<input type="hidden" name="redirect[success]" value="<?= LOCATION; ?>admin/comments/" />
				<input type="hidden" name="redirect[failure]" value="<?= $_SERVER['REQUEST_URI']; ?>" />
				<input type="hidden" name="comments[id]" value="<?= $comment->id; ?>"/>
			</p>
			
			<div class="comment-edit-info">
				<h3 class="comment-edit-info-title">Comment Location:</h3>
				<p>
					<?= build_page_location(get_comment_section($comment), ' - '); ?>:
					<a href="<?= $comment->parent('link'); ?>"><?= $comment->parent('title'); ?></a>
				</p>
			</div>
			
			<div class="comment-edit-item">
				<label for="name">Name: </label>
				<input type="text" id="name" name="comments[name]" value="<?= value('comments[name]', $comment->name); ?>" class="field-medium" />
			</div>
			
			<div class="comment-edit-item">
				<label for="email">Email: </label>
				<input type="text" id="email" name="comments[email]" value="<?= value('comments[email]', $comment->email); ?>" class="field-medium" />
			</div>
			
			<div class="comment-edit-item">
				<label for="url">URL: </label>
				<input type="text" id="url" name="comments[url]" value="<?= value('comments[url]', $comment->url); ?>" class="field-medium" />
			</div>
			
			<div class="comment-edit-item">
				<label for="comment">Comment: </label>
				<textarea id="comment" name="comments[comment]" rows="6" cols="40"><?= value('comments[comment]', htmlentities2($comment->comment)); ?></textarea>
			</div>
			
			
			<?php if (is_string($comment->approved_at)) : ?> 
				<div class="comment-edit-item">
					<label for="approved_at">Approved at: </label>
					<input type="text" id="approved_at" name="comments[approved_at]" value="<?= $comment->approved_at; ?>" class="field-medium" />
				</div>
			<?php else: ?> 
				<div class="comment-edit-item-approve">
					<label><input type="checkbox" name="comments[approved_at]" value="<?= NOW; ?>"/> Approve this comment</label>
				</div>
			<?php endif; ?> 
	
			<div class="comment-edit-item-moderate">
			<?php if (is_string($comment->moderated_at)) : ?> 
				<label for="moderated_at">Moderated at: </label>
				<input type="text" id="moderated_at" name="comments[moderated_at]" value="<?= $comment->moderated_at; ?>" class="field-medium" />
			<?php else: ?> 
				<label><input type="checkbox" name="comments[moderated_at]" value="<?= NOW; ?>"/> Moderate this comment</label>
			<?php endif; ?> 
			</div>
			
			<?php if (is_string($comment->deleted_at)) : ?> 
			<div class="comment-edit-item">
				<label for="deleted_at">Deletion Date/Time</label>
				<input type="text" id="deleted_at" name="comments[deleted_at]" value="<?= $comment->deleted_at; ?>" class="field-medium"/>				
			</div>				
			<?php endif; ?> 
	
			<div class="comment-edit-item-submit">
				<input type="submit" name="continue" value="Save and Continue Editing" class="btn-submit" /> 
				<input type="submit" name="save" id="submit" value="Save" class="btn-submit" /> 
				or <a href="<?= LOCATION; ?>admin/comments/" class="cancel">Cancel</a>
			</div>
		
		</form>	
		
		<div>
			<input type="button" name="delete" value="Delete This Comment" id="delete" class="delete-button" onclick="if (confirm('Are you sure  you want to delete this comment?')) window.location = '<?= LOCATION; ?>admin/comments/<?= $comment->id; ?>/delete/';" />
		</div>	
	
	</div>	
