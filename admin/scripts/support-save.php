<?php
	
	/*
	**********************************************
	**
	**	File: admin/scripts/support-save.php
	**	Creation Date: 10/20/09
	**	Description: Saves and sends support tickets
	**	Called From: admin/includes/support-ticket.php
	**
	**********************************************
	*/
	clear_field_values();
	clear_required_fields();
	
	require_once(LIBRARY . 'handleRequired.php');
	
	$table = 'ticket';
	include(LIBRARY . 'handlePostVars.php');
	
	$errors = false;
	
	if (!isset($ticket['message']) || !is_string($ticket['message']) || empty($ticket['message'])) {
		$errors = "Please supply a message describing your issue. Thank you.";
	}
	
	if (!isset($ticket['subject']) || !is_string($ticket['subject']) || empty($ticket['subject'])) {
		$ticket['subject'] = 'Other';
	}
	
	if (!$errors) {
		require_once CONFIG . 'support.php';
		
		$ticket['status'] = 'Unread';
		$ticket['assigned_to'] = 'Unassigned';
		$ticket['subject'] = $ticket['subject'];
		$ticket['message'] = $ticket['message'];
		$ticket['user_info'] = $_SERVER['HTTP_USER_AGENT'];

		$wasSubmitted = $db->insert('table=>tickets', $ticket);

		if ($wasSubmitted) {

			$ticket_id = $db->last_id;
			$wasSent = false;
			
			$wasSent = sendmail(SUPPORT_EMAIL, SUPPORT_NAME . "<".SUPPORT_EMAIL.">", "Support Ticket #" . $ticket_id . " - " . clean(stripslashes($ticket['subject'])), load_message('ticket', array('ticket_id' => $ticket_id, 'message' => $ticket['message'], 'when' => NOW, 'user_email' => user('email'), 'user_name' => user('name'), 'ip' => $_SERVER['REMOTE_ADDR'], 'agent' => $ticket['user_info'], 'domain' => $_SERVER['SERVER_NAME'])));

			if ($wasSent) {

				$wasSent = sendmail(user('email'), SUPPORT_NAME . "<".SUPPORT_EMAIL.">", 'We have received your support request [#' . $ticket_id . ']', load_message('ticket-confirmation', array('ticket' => $ticket_id, 'support_name' => SUPPORT_NAME, 'support_email' => SUPPORT_EMAIL)));
				
			}

			if (!$wasSent) {
				$errors = 'There was an error sending the support ticket. Try emailing us: ' . SUPPORT_EMAIL;
			}
			
		} else {
			$errors = "There was an error submitting your ticket.";
		}

	}
	
	if ($errors) {
		$_SESSION[$table] = $_SESSION['postFields'] = $postFields;
		failure($errors);
	} else {
		success("Your ticket has successfully been submitted. We'll be in touch shortly.");
	}
	redirect(LOCATION . "admin/support/");

?>