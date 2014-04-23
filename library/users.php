<?php

	class user {
	
		var $id = false;
		var $name = false;
		var $openid = false;
		var $email = false;
		var $password = false;
		var $types = '';
		var $last_seen_at = false;
		var $created_at = false;
		var $updated_at = false;
		var $deleted_at = false;
		var $isLoggedIn = false;
		
		function user() {
			global $db;
			if (isset($_SESSION['user'])) {
				foreach ($_SESSION['user'] as $key => $val) $this->$key = $val;
				$this->sessionUpdate();
			} else if (isset($_COOKIE['user'])) {
				$cookieInfo = explode(md5(SERVER), $_COOKIE['user']);
				if (count($cookieInfo) > 1) {
					$u = $db->getOne('table=>users', 'id=>' . $cookieInfo[0]);
					if (($u['password'] == $cookieInfo[1]) && (md5($this->last_seen_at) == $cookieInfo[2])) {
						foreach ($u as $key => $val) $this->$key = $val;
						$this->isLoggedIn = true;
						$this->sessionUpdate();
					}					
				} 
			}		
		} // user constructor
		
		function createPassword($numericOnly = false, $length = 6) {
			if (!$numericOnly) {
				//$pass = chr(mt_rand(65,90));	// A-Z
				$pass = '';
				for($k=0; $k<=$length; $k++) {
					$probab = mt_rand(1,10);
					if($probab <= 8)			// a-z probability is 80%
						$pass .= chr(mt_rand(97,122));
					else 						// 0-9 probability is 20%
						$pass .= chr(mt_rand(48, 57));
				}
			} else {
				$pass = '';
				for($k=0; $k<=$length; $k++) {
					$pass .= chr(mt_rand(48, 57));
				}
			}
			return $pass;
		} // createPassword
		
		function encryptPassword($password) {
			return encrypt_password($password);
		} // encryptPassword
		
		function forget() {
			$cookie = '';
			if (isset($_COOKIE['user'])) setcookie('user', $cookie, time() - 31104000, "/");
			return true;
		} // forget	
		
		function canAccess($types = false) {
			if (!$this->isLoggedIn) 
				return false;
			if (!$types)
				$types = get_restricted_to();
			if (is_string($types)) 
				$types = trim_explode(',', $types);
			foreach ($types as $type)
				if (in_array($type, $this->getTypes()))
					return true;
			$this->isLoggedIn = false;
			$this->sessionUpdate();
			return false;
		} // canAccess
		
		function getTypes() {
			if (is_string($this->types)) {
				return array_merge(trim_explode(",", $this->types), array('users'));
			}
			return $this->types;
		} // getTypes
		
		function isType($type) {
			if (in_array($type, $this->getTypes())) {
				return true;
			}
			return false;
		} // isType
		
		function isAdmin() {
			return $this->isType('admins');
		} // isAdmin
		
		function isValidPassword($password, $hash) {
			return is_valid_password($password, $hash);
		} // isValidPassword

		function login($email, $password, $remember = false, $userTypes = 'users') {
			global $db;
			$userToCheck = $db->get("table=>users", "where=>(`email` = '" . $db->escape($email) . "') AND (`deleted_at` IS NULL)");
			
			if (!is_array($userToCheck) || (count($userToCheck) != 1)) return false;
			$userInfo = $userToCheck[0];
			
			if (!is_array($userTypes)) $userTypes = trim_explode(',', $userTypes);
			
			/*if (is_array($userTypes) && !in_array('users', $userTypes)) {
				$types = trim_explode(',', $userInfo['types']);
				if (!in_array($userType, $types)) return false;
				return false;
				//$userToCheck = $db->get("table=>" . $userType, "where=>(`id` = " . $userInfo['id'] . ") AND (`deleted_at` IS NULL)");
				//if (!is_array($userToCheck) || (count($userToCheck) != 1)) return false;
				//$userInfo['types'] = array('users', $userType);
			}*/
			
			$types = trim_explode(',', $userInfo['types']);
			$types[] = 'users';
			
			foreach ($userTypes as $userType) {
				if (in_array($userType, $types)) {
					if ($this->isValidPassword($password, $userInfo['password'])) {
						foreach ($userInfo as $key => $value) $this->$key = $value;
						$this->isLoggedIn = true;
						$this->last_seen_at = NOW;
						$this->sessionUpdate();
						$db->execute("UPDATE `users` SET `last_seen_at` = '" . $this->last_seen_at . "' WHERE (`id` = " . $this->id . ");");
						if ($remember) $this->remember();
						return $this->isLoggedIn;
					} else {
						return false;
					}
				}
			}
			return false;
		} // login
				
		/* THIS IS A VERY DANGEROUS FUNCTION TO HAVE... COMPLETELY BYPASSES LOGIN PROCESS
		function loginById($user_id = false) {
			global $db;
			if (!$user_id || !is_numeric($user_id)) return false;
			$user = $db->get('table=>users', "where=>(`id` = " . $db->escape($user_id) . ") AND (`deleted_at` IS NULL)");
			if (count($user) != 1) return false;
			$this->logout();
			foreach ($user[0] as $key => $val) $this->$key = $val;
			return true;
		} // loginById*/
		
		function logout() {
			$this->reset();
			$this->isLoggedIn = false;
			return true;
		} // logout
				
		function register() {
			$args = args(func_get_args());

		} // register
		
		function remember() {
			$cookie = $this->id . md5(SERVER) . $this->password . md5(SERVER) . md5($this->last_seen_at);
			setcookie('user', $cookie, time() + (360 * 24 * 60 * 60), "/");
			return true;
		} // remember
		
		function reset() {
			$vars = get_class_vars(get_class($this));
			foreach ($vars as $varKey => $varValue) $this->$varKey = $varValue;
			return $this->sessionClear();
		} // reset
		
		function sessionClear() {
			unset($_SESSION['user']);
			return true;
		} // sessionClear
		
		function sessionUpdate() {
			return $_SESSION['user'] = get_object_vars($this);
		} // sessionUpdate
		
	} // class user
	
	
	if (!isset($usr)) {
		global $usr;
		$usr = new user();
	}
	
	function get_user() {
		global $usr;
		return $usr;
	} // get_user
	
	function user_logout() {
		global $usr;
		$usr->forget();
		$usr->logout();
		$usr->sessionUpdate();
	} // user_logout
	
	function check_user_login($type = 'users', $return = false) {
		global $usr,$users;
		if ($usr->isLoggedIn) :
			redirect(LOCATION . get_folder());		
		endif;
		
		if (isset($_POST['users'])) :
			$users = $_POST['users'];
			$return = (is_string($return)) ? $return : LOCATION . get_folder();
			$remember = (isset($users['remember'])) ? true : false;
			if ($usr->login($users['email'], $users['password'], $remember, $type)) :
				$r = (get_session_var('referral_page')) ? get_session_var('referral_page') : $return;
				redirect($r);
			else:
				failure('Invalid login attempt.');
			endif;
		endif;
	} // check_user_login
	
	function user($varName = 'id') {
		global $usr;
		if (isset($usr->$varName)) {
			return $usr->$varName;
		} else if (method_exists($usr, $varName)) {
			return $usr->$varName();
		}
		return false;
	} // user
	
	function user_is_admin() {
		global $usr;
		return ($usr->isType('admins'));
	} // user_id_admin
	
	function is_valid_password($password, $hash) {
		// hack for old flint systems.
		$isOld = false;
		
		if (substr($hash, 0, 6) == '{SSHA}') {
			$hash = substr($hash,6);
			$isOld = true;
		}
		$hash = base64_decode($hash);

		$original_hash = substr($hash, 0, 20);
		$salt = substr($hash, 20);
		
		if ($isOld && function_exists('mhash')) {
			$new_hash = mhash(MHASH_SHA1, $password . $salt);
		} else {
			$new_hash = pack("H*", sha1($password . $salt));
		}
		
		return (strcmp($original_hash, $new_hash) == 0) ? true : false;
	} // is_valid_password
	
	function encrypt_password($password) {
		mt_srand((double)microtime()*1000000);
		$rand8bites4salt = substr(pack('h*', md5(mt_rand())), 0, 8);

		// mhash library must be installed... 
		if (function_exists("mhash")) {
			$salt = mhash_keygen_s2k(MHASH_SHA1, $password, $rand8bites4salt, 4);
			$hash = base64_encode(mhash(MHASH_SHA1, $password.$salt).$salt);
		} else {
			// possible non mhash way?
			$salt = substr(pack("H*", md5($rand8bites4salt . $password)), 0, 4);
			$hash = base64_encode(pack("H*", sha1($password.$salt)).$salt);
		}
		return $hash;
	} // encrypt_password
	
	function is_me($user_id = 0) {
		if (!is_logged_in()) :
			return false;
		elseif (user('id') == $user_id) :
			return true;
		endif;
		return false;
	} // is_me
	
	function is_logged_in() {
		return user('isLoggedIn');
	} // is_logged_in
	
	function has_profile($user_id = false) {
		global $db;
		if (!is_plugin('user_profiles'))
			return false;
		
		// hardcore... too many sql calls... but most accurate...
		if (!is_numeric($user_id)) {
			if (!is_logged_in())
				return false;
			$user_id = user('id');
		}
			
		$profiles = $db->get('table=>user_profiles', "where=>(1 = 1) AND (`user_id` = " . $db->escape($user_id) . ") AND (`deleted_at` IS NULL)");
		
		return (is_array($profiles) && (count($profiles) > 0));
		//// BUILD OUT MORE? ////
		//$profile = get_my_profile();
		//return $profile->wasFound();
	} // has_profile
	
	function user_has_thumb($user_id) {
		$photo = get_presentation_file('uploads/users/' . $user_id . '.gif');
		if ($photo['found']) :
			return true;
		endif;
		$photo = get_presentation_file('uploads/users/' . $user_id . '.jpg');
		if ($photo['found']) :
			return true;
		endif;
		$photo = get_presentation_file('uploads/users/' . $user_id . '.png');
		if ($photo['found']) :
			return true;
		endif;
		return false;
	} // user_has_thumb
	
	function user_thumb($user_id, $width = 150, $height = 150, $cropratio = '1:1') {
		$photo = get_presentation_file('uploads/users/' . $user_id . '.gif');
		if ($photo['found']) :
			return add_photo_info($photo['versioned'], $width, $height, $cropratio);
		endif;
		$photo = get_presentation_file('uploads/users/' . $user_id . '.jpg');
		if ($photo['found']) :
			return add_photo_info($photo['versioned'], $width, $height, $cropratio);
		endif;
		$photo = get_presentation_file('uploads/users/' . $user_id . '.png');
		if ($photo['found']) :
			return add_photo_info($photo['versioned'], $width, $height, $cropratio);
		endif;
		return add_photo_info('uploads/users/0.gif', $width, $height, $cropratio);
	} // user_thumb

?>