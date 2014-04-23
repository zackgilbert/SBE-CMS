<?php

	load_plugin('comments');

	$table = 'comments';
	$error = false;
	$saved = false;
	
	$count = (isset($_POST['comment_count'])) ? $_POST['comment_count'] : ((isset($_POST['count'])) ? $_POST['count'] : 0);
	
	if (is_ajax()) {
		$comment = array();
		$comment['table'] = $_POST['comment_table'];
		$comment['table_id'] = $_POST['comment_table_id'];
		$comment['name'] = $_POST['comment_name'];
		$comment['email'] = $_POST['comment_email'];
		$comment['url'] = $_POST['comment_url'];
		$comment['comment'] = $_POST['comment_comment'];
	} else if (isset($_POST['comments'])) {
		$comment = $_POST['comments'];
		$_SESSION['comments'] = $comment;
	}
	
	if (isset($comment)) {	
		if (empty($comment['table']) || empty($comment['table_id'])) {
			$error = "Required information was not supplied. Please contact an administrator.";
		} else if (empty($comment['name']) || empty($comment['email'])) {
			$error = "Please supply your name and a valid email address.";
		} else if (!is_valid_email($comment['email'])) {
			$error = "Please supply a valid email address.";
		//} else if (empty($comment['comment']) || ($comment['comment'] == 'Please stay on topic, and be respectful.') || ($comment['comment'] == 'Please be polite and relevant.')) {
		} else if (in_array($comment['comment'], array('', 'Please be polite and relevant.', 'Please stay on topic, and be respectful.'))) {
			$error = "Please don't submit blank comments.";
		}

		if (empty($comment['url'])) {
			$comment['url'] = NULL;
		} else if (substr($comment['url'], 0, 7) != 'http://') {
			$comment['url'] = 'http://' . $comment['url'];
		}

		$comment['ip'] = $_SERVER['REMOTE_ADDR'];

		// figure out why this causes the insert to fail...
		if (isset($_SERVER['HTTP_USER_AGENT']))
			$comment['agent'] = $_SERVER['HTTP_USER_AGENT'];
			
		if (is_logged_in() && (user('id') > 0))
			$comment['user_id'] = user('id');

		//if (false) {
		if (USE_AKISMET) {
			// load array with comment data 
			$akis_comment = array(
				'author' => $comment['name'], 
				'email' => $comment['email'], 
				'website' => $comment['url'], 
				'body' => $comment['comment']
			);

			// instantiate an instance of the class 
			require_once(PLUGINS . "comments/class.akismet.php");
			//$akismet = new Akismet('http://' . $_SERVER['SERVER_NAME'], get_api_key('akismet'), $akis_comment); // test for errors 
			$akismet = new Akismet('http://' . $_SERVER['SERVER_NAME'], get_api_key('akismet'));
			$akismet->setCommentAuthor($comment['name']);
			$akismet->setCommentAuthorEmail($comment['email']);
			$akismet->setCommentAuthorURL($comment['url']);
			$akismet->setCommentContent($comment['comment']);
			
			/*if ($akismet->errorsExist()) { // returns true if any errors exist
				$error = 'There was an error connecting to Akismet servers. Comment was not added.';
			} else { 
				// No errors, check for spam 
				// returns true if Akismet thinks the comment is spam 
				if ($akismet->isSpam()) { 
					// do something with the spam comment 
					error_log("\n--".NOW."--\n".var_export($comment,true), 3, LOGS . 'spam/' . TODAY .'.log');
					$error = 'This comment has been flagged as spam. It has not been added.';
				}
			}*/
			if ($akismet->isCommentSpam()) {
				error_log("\n--".NOW."--\n".var_export($comment,true), 3, LOGS . 'spam/' . TODAY .'.log');
				$error = 'This comment has been flagged as spam. It has not been added.';
			}
			
		}
		
		if (!$error) {
			$saved = $db->insert("table=>comments", $comment);
			$comment_id = $db->last_id;
			flush_memcache();
			//$saved = true;
			if ($saved) {
				//storage("kill_me");

				//$db->update('table=>' . $db->escape($comment['table']), 'id=>' . $db->escape($comment['table_id']), array('fields' => array('comment_count' => NULL)));
				$_SESSION['comment']['comment'] = '';
				
			}
			$error = ($saved) ? "" : "There was an error saving your comment to the database. Please try again.";
			//echo $error;
		}

		if (is_ajax()) {
			if ($saved) {
				$comment = $db->getOne("table=>" . $table, "id=>" . $comment_id);
				$comment_counter = (($count+1)%2);
				load_comment($comment, $comment_counter);
			} else {
				echo $error;
			}
		} else {
			if ($error && !empty($error)) {
				failure($error);
				redirect_failure(referral_page() . "#comment-form");
			} else {
				if (isset($_POST['redirect'])) 
					$_POST['redirect'] = str_replace("#comment-form", "#comment-" . $comment_id, $_POST['redirect']);
				
				success('Your comment was successfully added. It will show up as soon as it is approved.');
				redirect_success();
				//redirect(referral_page() . "#comment-" . $comment_id);
			}
		}
	}

?>