<?php

class UserRESTController extends WPAPIRESTController {

    protected function __construct() {
        
    }

    protected function getUsers() {
        global $wpdb;
        $array = array();
        $users = $wpdb->get_results("SELECT ID FROM " . $wpdb->users . " WHERE ID > 0");
        // Check if we only have 1 element
        if (count($users) == 1) {
	return $this->_return($this->getUser($users[0]->ID));
        }
        foreach ($users as $user) {
	$array[] = $this->getUser($user->ID);
        }
        return $this->_return($array);
    }

    protected function getUser($user = 0) {
        return $this->_return(get_userdata($user));
    }

    protected function postUser($data) {

        $user_data = array();
        error_log(">>>>> user_login >>>+++>>>> ".$data['user_login']);
        error_log(">>>>> user_password >>>+++>>>> ".$data['user_password']);

        if (isset($data['user_login']) && isset($data['user_password']) && isset($data['user_email'])) {
	$user_data['user_login'] = $data['user_login'];
	$user_data['user_password'] = $data['user_password'];
	$user_data['user_email'] = $data['user_email'];
	if (isset($data['user_url'])) {
	    $user_data['user_url'] = $data['user_url'];
	}
	if (isset($data['user_nicename'])) {
	    $user_data['user_nicename'] = $data['user_nicename'];
	}
	if (isset($data['display_name'])) {
	    $user_data['display_name'] = $data['display_name'];
	}
	if (isset($data['nickname'])) {
	    $user_data['nickname'] = $data['nickname'];
	}
	if (isset($data['first_name'])) {
	    $user_data['first_name'] = $data['first_name'];
	}
	if (isset($data['role'])) {
	    $user_data['role'] = $data['role'];
	} else {
	    $user_data['role'] = get_option('default_role');
	}
	$new_user_id = wp_insert_user($user_data);
        }

        if (is_wp_error($new_user_id)) {
	   error_log($new_user_id->get_error_message());
	return new WP_Error('error', __('Error adding new user.'));
        } else {
	return $this->_return(get_userdata($new_user_id));
        }
    }

    protected function putUser($data) {

        $user_data = array();
        $user_data['ID'] = $data['id'];

        if (isset($data['user_login'])) {
	$user_data['user_login'] = $data['user_login'];
        }
        if (isset($data['user_password'])) {
	$user_data['user_password'] = $data['user_password'];
        }
        if (isset($data['user_email'])) {
	$user_data['user_email'] = $data['user_email'];
        }
        if (isset($data['user_url'])) {
	$user_data['user_url'] = $data['user_url'];
        }
        if (isset($data['user_nicename'])) {
	$user_data['user_nicename'] = $data['user_nicename'];
        }
        if (isset($data['display_name'])) {
	$user_data['display_name'] = $data['display_name'];
        }
        if (isset($data['nickname'])) {
	$user_data['nickname'] = $data['nickname'];
        }
        if (isset($data['first_name'])) {
	$user_data['first_name'] = $data['first_name'];
        }
        if (isset($data['role'])) {
	$user_data['role'] = $data['role'];
        } else {
	$user_data['role'] = get_option('default_role');
        }
        $updated_user_id = wp_update_user($user_data);

        return $this->_return(get_userdata($updated_user_id));
    }

    protected function deleteUser($user_id) {
        if (mysql_query("DELETE FROM wp_users WHERE id='".$user_id."'")) {
	return array( 'ID' => $user_id, 'deleted' => true);
        } else {
	return new WP_Error('error', __('Error deleting user.'));
        }
    }

    protected function verify_credentials($userdata) {
        global $current_user, $wpdb;
        wpr_set_defaults($userdata, array('username' => '', 'password' => '', 'sign_on' => 0, 'session_id' => null));
        // Check if we're already signed on
        wp_get_current_user();

        if (0 == $current_user->ID) {
	// Check given user credentials
	if (empty($userdata['username']) || empty($userdata['password'])) {
	    $error = new WP_Error();

	    if (empty($$userdata['username']))
	        $error->add('empty_username', __('The username field is empty.'));

	    if (empty($userdata['password']))
	        $error->add('empty_password', __('The password field is empty.'));

	    return $error;
	}

	// Check if we provide a session
	if (!is_null($userdata['session_id'])) {
	    $sql = "SELECT * FROM " . WPR_USERS_PLUGIN_DB_TABLE . " WHERE session_id = " . $userdata['session_id'];
	    $session_data = $wpdb->get_row($sql);
	    $actual_microtime = microtime(true);
	    if (($actual_microtime - $session_data['microtime']) <= (60 * 60)) {
	        return get_userdata($session_data['user_id']);
	    }
	}

	$is_authentic = user_pass_ok($userdata['username'], $userdata['password']);
	// If user is authentic
	if ($is_authentic) {

	    $credentials = array('user_login' => $userdata['username'], 'user_password' => $userdata['password'], 'remember' => false);
	    $current_user = wp_signon($credentials, false);
	    // Send user information to filter system and return
	    if ($userdata['sign_on']) {
	        $session_id = md5(uniqid());
	        $sql = "INSERT INTO " . WPR_USERS_PLUGIN_DB_TABLE . " (`session_id`,`user_id`,`microtime`) VALUES ('" . $session_id . "'," . $current_user->ID . "," . microtime(true) . ")";
	        $wpdb->query($sql);
	        $return_array = array('session_id' => $session_id, 'wp_user' => $current_user);
	        return $return_array;
	    } else
	        return $this->_return($current_user);
	} else {
	    // There was a problem with your credentials
	    return new WP_Error('invalid_credentials', __('Invalid credentials'));
	}
        } else {
	// If the user was already signed through the API
	return $this->_return($this->getUser($current_user->ID));
        }
    }

    /**
     * Apply request filter
     * 
     * @since 0.1
     * 
     * @return (mixed) filtered content
     */
    private function _return($content) {
        return wpr_filter_content($content, wpr_get_filter("Users"));
    }

}

?>