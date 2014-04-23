<?php

	class message {
		
		var $type = 'general';
		var $message = '';
		
		function message() {
			$args = args(func_get_args());
			if (isset($_SESSION['flint']['message']['type'])) 
				$this->type = $_SESSION['flint']['message']['type'];
			if (isset($_SESSION['flint']['message']['message']))
				$this->message = $_SESSION['flint']['message']['message'];

			foreach ($args as $argKey => $argValue)
				$this->$argKey = $argValue;
		} // message constructor
		
		function clear() {
			$_SESSION['flint']['message']['type'] = $this->type = '';
			$_SESSION['flint']['message']['message'] = $this->message = '';
			return true;
		} // clear
		
		function display() {
			// this should be some sort of HTML view
			if ($this->message && !empty($this->message)) {
				load_include('message.php');
			}
		} // display
		
		function set($message = '', $type = 'general') {
			$args = args(func_get_args());
			$type = (isset($args['type'])) ? $args['type'] : $type;
			$message = (isset($args['message'])) ? $args['message'] : $message;
			$_SESSION['flint']['message']['type'] = $this->type = $type;
			$_SESSION['flint']['message']['message'] = $this->message = $message;
			return true;
		} // set
		
	} // class message
		
	function clear_message() {
		global $msg;
		$msg->clear();
	}
	
	function display_message() {
		global $msg;
		$msg->display();
	}
	
	function failure($message) {
		set_message($message, 'failure');
	}
	
	function message($return = false) {
		global $msg;
		if ($return && $msg->$return) {
			return $msg->$return;
		}
		return $msg;
	}
	
	function set_message($message, $type = 'general') {
		global $msg;
		$msg->set($message, $type);
	}

	function start_message() {
		global $msg;
		$msg = new message();
		//register_shutdown_function('stop_message');
	}
	
	function stop_message() {
		global $msg;
		if (isset($msg) && is_object($msg)) $msg->clear();	// make sure to clear any messages that were set.
	}
	
	function success($message) {
		set_message($message, 'success');
	}
	
	start_message();

?>