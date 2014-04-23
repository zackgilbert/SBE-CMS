<?php

	/*
	**********************************************
	**
	**	File: admin/Flint/scripts/contact-send.php
	**	Creation Date: 11/25/09
	**	Description: Send contact form mail
	**
	**********************************************
	*/
	clear_field_values();
	clear_required_fields();
	
	$to = request('recipient');
	$subject = request('subject');
	
	require_once(LIBRARY . 'handleRequired.php');
	
	$table = 'contact';
	include(LIBRARY . 'handlePostVars.php');
	
	// check email...
	if (isset($contact['email']) && isset($contact['email_confirm'])) {
	
		if ($contact['email'] != $contact['email_confirm']) {
			$_SESSION[$table] = $_SESSION['postFields'] = $postFields;
			$_SESSION['requiredFields'] = array('contact[email]', 'contact[email_confirm]');
			failure('Your email address appears to not match when asked to confirm. Please make sure you provide us with a proper email.');
			redirect_failure();
		}

		if (!is_valid_email($contact['email'])) {
			$_SESSION[$table] = $_SESSION['postFields'] = $postFields;
			$_SESSION['requiredFields'] = array('contact[email]', 'contact[email_confirm]');
			failure('It appears as though you didn\'t supply us with a valid email address. We need a valid one to get in touch.');
			redirect_failure();			
		}
	
		unset($contact['email_confirm']);
		
	}
	
	$message_fields = '';
	foreach ($contact as $name => $value) {
		$message_fields .= str_replace("_", " ", $name) . ": " . $value . "\n\n";
	}
	
	$message = load_message('contact-forms', array('subject' => $subject, 'fields' => $message_fields, 'url' => 'http://' . SERVER . $_POST['redirect']['failure']));
	
	if (sendmail('zackgilbert@gmail.com', $to, $subject, $message)) {
		//success("Thanks for taking to time to fill out our form. We'll be in touch as soon as we can.");
		redirect_success();
	} else {
		$_SESSION[$table] = $_SESSION['postFields'] = $postFields;
		failure('There was an error sending this form. Try emailing us manually at: ' . $to);
		redirect_failure();
	}
	
?>