
	<div id="comment-form" class="content-leavecomment">
		<h4 class="leavecomment-title">Leave A Comment</h4>		
		<form method="post" action="<?= LOCATION; ?>plugins/comments/scripts/comments-save/">
			<p>
				<input type="hidden" name="redirect" value="<?= $_SERVER['REQUEST_URI']; ?>#comment-form" />
				<input type="hidden" name="comments[table]" value="<?= $table; ?>" />
				<input type="hidden" name="comments[table_id]" value="<?= $table_id; ?>"/>
				<input type="hidden" name="comments[sitemap_id]" value="<?= $section_id; ?>"/>
			</p>

			<p class="leavecomment-info">
				<label for="comments_name">Name: </label>
				<input type="text" id="comments_name" name="comments[name]" class="field-medium" value="<?= value('comments[name]'); ?>"/>
			</p>

			<p class="leavecomment-info">
				<label for="comments-email">Email: </label>
				<input type="text" id="comments-email" name="comments[email]" class="field-medium" value="<?= value('comments[name]'); ?>"/>
				<span class="leavecomment-notice">(This will not be published)</span>
			</p>

			<p class="leavecomment-info">
				<label for="comments-url">Website: </label>
				<input type="text" id="comments-url" name="comments[url]" class="field-medium" value="<?= value('comments[name]'); ?>"/>
				<span class="leavecomment-notice">(Optional)</span>
			</p>

			<p class="leavecomment-info">
				<label for="comments-comment">Comment: </label>
				<textarea id="comments-comment" name="comments[comment]" cols="50" rows="4" onfocus="if(this.value == 'Please stay on topic, and be respectful.') { this.value = ''; }">Please stay on topic, and be respectful.</textarea>
			</p>

			<p class="leavecomment-submit">
				<input type="submit" value="Add Comment" class="submit"/>
			</p>
		</form>
	</div>
