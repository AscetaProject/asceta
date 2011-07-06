<?php

/*
 * For access to the MediaWiki API, needed to communicate REST
 */

/**
* GET api/users -> list all users
*
* @return array list all users data {user_id, user_name, user_real_name, user_email}
*/
function getUsers(){
    $fields = array('user_id', 'user_name', 'user_real_name', 'user_password', 'user_email');
    $res = select('user',$fields);
    $dbr = wfGetDB( DB_SLAVE );
    $result = array();
    while ( $row = $dbr->fetchObject( $res ) ) {
        array_push($result, array($row->user_id, $row->user_name, $row->user_real_name, $row->user_email));
    }
    $dbr->freeResult( $res );
    return $result;
}

/**
* GET api/users/1 -> list user with id
*
* @return object User
* @param integer $ID user identification number
*/

function getUser($ID){
    $user = User::newFromId($ID);
    $find = $user->loadFromId();
    if ($find)
        return $user;
    return null;
}

/**
* POST api/users -> create a new user
*
* @return object User
* @param array $data user data
*/

function createUser($data){
    global $wgRequest;

    $user = User::newFromName($data['name']);
    if(!$user)
        throw new Exception("Invalid data\n", "001");
    if ( !$user->getID() ) {
        $user->setPassword($data['password']);
        $newuser = $user->createNew($user->getName(), array(
					"password" => $user->mPassword,
					"email" => $data['email'],
					"real_name" => $data['realname']));
    } else {
        $user->setPassword($data['password']);
        $newuser = $user;
    }
    $user->saveSettings();
    return $newuser;
}

/**
* PUT api/users/1 -> edit user with id
*
* @param integer $ID user identification number
* @param array $data user data
*/
function updateUser($ID, $data){
    $user = User::newFromId($ID);
    $user->load();
    if($user->mId == 0)
        throw new Exception("Not exists users '$data->name' with id '$ID'\n","001");
    foreach($data as $key=>$value){
        if(isset($value)){
            $method = 'set' . ucwords($key);
            call_user_func(array($user, $method), $value);
        }
    }
    $user->saveSettings();
}

/**
 * DELETE api/users/1 -> delete user with id
 *
 * @return array (User id, result operation)
 * @param integer $ID user identification number
 */

function deleteUser($ID){
    global $mwpr;
    $user = User::newFromId($ID);
    $user->load();
    if($user->mId == 0)
        throw new Exception("Not exists user with id '$ID'\n","001");
    $db = wfGetDB( DB_MASTER );
    $result = $db->delete( 'user', array( 'user_id' => $ID ) );
    if ($db->delete( 'user', array( 'user_id' => $ID ) )){
        return array( 'ID' => $ID, 'deleted' => true);
    } else {
        throw new Exception('Error deleting user.');
    }
}

/**
* GET api/pages -> list all pages
*
* @return array list all users data {user_id, user_name, user_real_name, user_email}
*/
function  getPages(){
    $res = select('page',array('page_id', 'page_title'));
    $dbr = wfGetDB( DB_SLAVE );
    $result = array();
    while ( $row = $dbr->fetchObject( $res ) ) {
        $art = new Article(Title::newFromText($row->page_title));
        array_push($result, array($row->page_id, $row->page_title, $art->getComment()));
    }
    $dbr->freeResult( $res );
    return $result;
    return $res;
}

/**
* GET api/pages/1 -> list page with id
*
* @return string page title
* @param integer $page page identification number
*/
function getPage($page){
    $res = select('page','page_title','page_id = '.$page);
    $dbr = wfGetDB( DB_SLAVE );
    $row = $dbr->fetchObject( $res );
    return $row->page_title;
}

/**
* POST api/pages-> create a new page
*
* @param string $name page title
* @param string $text page text
* @param string $summary page summary
*/
function createPage($name, $text, $summary){
    $title = Title::newFromText( $name );
    if ( is_null($title) ) {
            throw new Exception("Invalid data title\n", "001");
    }

    $aid = $title->getArticleID( GAID_FOR_UPDATE );
    if ($aid != 0) {
            throw new Exception("Duplicate article '$name'\n", "001" );
    }

    $art = new Article($title);
    $art->insertNewArticle($text, $summary);

}

/**
* PUT api/pages/1 -> edit page with id
*
* @param string $name page title
* @param string $text page text
* @param string $summary page summary
*/
function updatePage($name, $text, $summary){
    $title = Title::newFromText( $name );
    if ( is_null($title) ) {
           throw new Exception("Invalid data title\n", "001");
    }

    $aid = $title->getArticleID( GAID_FOR_UPDATE );
    if ($aid == 0) {
            throw new Exception("Not exists article '$name'\n", "001");
    }

    $art = new Article($title);
    $art->updateArticle( $text, $summary);
}

/**
* make a select query
*
* @return array the result of the query
* @param string $table table name
* @param array $columns columns list
* @param string $cond conditions
*/
function select($table, $columns, $cond=''){
    #Get data from database
    $dbr = wfGetDB( DB_SLAVE );
    $res = $dbr->select($table,$columns, $cond);
    return $res;
}

/**
* Get action method
*
* @return string action method
* @param string $data uri request
*/
function getAction($data){
    if (is_null($data)){
        return null;
    }
    $text = explode('?', strtolower($data));
    $text = explode('/', $text[0]);
    $size = count($text);
    return $text[4];
}

/**
* Get id
*
* @return string id
* @param string $data uri request
*/
function getID($data){
    $text = explode('?', strtolower($data));
    $text = explode('/', $text[0]);
    return $text[5];
}

/**
* Get name
*
* @return string name
* @param string $data uri request
*/
function getName($data){
    $text = explode('?', $data);
    $text = explode('/', $text[0]);
    return $text[0];
}

/**
* Save params in $_SESSION
*
* @param string $data uri request
*/
function saveParams($data){
    $expire=time()+60*60*24*30;
    setcookie("callback_uri", $data['callback_uri'], $expire,'/');
    setcookie("application_uri", $data['application_uri'], $expire,'/');
    setcookie("application_title", $data['application_title'], $expire,'/');
    setcookie("application_type", $data['application_type'], $expire,'/');
    setcookie("application_commercial", $data['application_commercial'], $expire,'/');
}

/**
 * Restore params saved into cookies
 */
function restoreParams(){
    $expire=time()-3600;
    setcookie("callback_uri", $data['callback_uri'], $expire,'/');
    setcookie("application_uri", $data['application_uri'], $expire,'/');
    setcookie("application_title", $data['application_title'], $expire,'/');
    setcookie("application_type", $data['application_type'], $expire,'/');
    setcookie("application_commercial", $data['application_commercial'], $expire,'/');
}

/**
* Convert mediawiki url (title=value&page=value) to beauty url (/value/value)
*
* @return string url
* @param string $url mediawiki url
*/
function convertToBeautyURL($url){
    $title_pattern = '/title/i';
    $page_pattern = '/page/i';
    if(preg_match($title_pattern, $url) && preg_match($page_pattern, $url)){
        $text = explode('?',$url);
        $params = explode('&',$text[1]);
        $result = array();
        $result[] = $text[0];
        foreach ($params as $param){
            $value = explode('=',$param);
            $result[] = $value[1];
        }
        $temp = implode('/',$result);
        return $temp;
    }
    return $url;
}

/*
 * Return url with the oauth data
 * @return string url
 */
function getUrl(){
    if(!isset($_GET['oauth_version'])){
        $_GET['oauth_version'] = $_COOKIE['oauth_version'];
        $_GET['oauth_nonce'] = $_COOKIE['oauth_nonce'];
        $_GET['oauth_timestamp'] = $_COOKIE['oauth_timestamp'];
        $_GET['oauth_consumer_key'] = $_COOKIE['oauth_consumer_key'];
        $_GET['oauth_token'] = $_COOKIE['oauth_token'];
        $_GET['oauth_signature_method'] = $_COOKIE['oauth_signature_method'];
        $_GET['oauth_signature'] = $_COOKIE['oauth_signature'];
    }
    $url = $GLOBALS['wgServer'].$GLOBALS['wgScriptPath'].'/index.php/'.$_GET['title'].'/'.$_GET['page']
    .'?oauth_version='.$_GET['oauth_version']
    .'&oauth_nonce='.$_GET['oauth_nonce']
    .'&oauth_timestamp='.$_GET['oauth_timestamp']
    .'&oauth_consumer_key='.$_GET['oauth_consumer_key']
    .'&oauth_token='.$_GET['oauth_token']
    .'&oauth_signature_method='.$_GET['oauth_signature_method']
    .'&oauth_signature='.$_GET['oauth_signature'];
    return $url;
}

/**
 * Restore the data Oauth save into cookies
 */
function restoreOauthCookies(){
    if(isset($_COOKIE['oauth_version'])){
        $_GET['oauth_version'] = $_COOKIE['oauth_version'];
        $_GET['oauth_nonce'] = $_COOKIE['oauth_nonce'];
        $_GET['oauth_timestamp'] = $_COOKIE['oauth_timestamp'];
        $_GET['oauth_consumer_key'] = $_COOKIE['oauth_consumer_key'];
        $_GET['oauth_token'] = $_COOKIE['oauth_token'];
        $_GET['oauth_signature_method'] = $_COOKIE['oauth_signature_method'];
        $_GET['oauth_signature'] = $_COOKIE['oauth_signature'];
        $expire = time() - 3600;
        setcookie('oauth_version', $_COOKIE['oauth_version'], $expire,'/');
        setcookie('oauth_nonce', $_COOKIE['oauth_nonce'], $expire,'/');
        setcookie('oauth_timestamp', $_COOKIE['oauth_timestamp'], $expire,'/');
        setcookie('oauth_consumer_key', $_COOKIE['oauth_consumer_key'], $expire,'/');
        setcookie('oauth_token', $_COOKIE['oauth_token'], $expire,'/');
        setcookie('oauth_signature_method', $_COOKIE['oauth_signature_method'], $expire,'/');
        setcookie('oauth_signature', $_COOKIE['oauth_signature'], $expire,'/');
    }
}

/**
 * Save the data Oauth into cookies
 */
function saveOauthCookies(){
    $expire=time()+60*60*24*30;
    setcookie('oauth_version', $_GET['oauth_version'], $expire,'/');
    setcookie('oauth_nonce', $_GET['oauth_nonce'], $expire,'/');
    setcookie('oauth_timestamp', $_GET['oauth_timestamp'], $expire,'/');
    setcookie('oauth_consumer_key', $_GET['oauth_consumer_key'], $expire,'/');
    setcookie('oauth_token', $_GET['oauth_token'], $expire,'/');
    setcookie('oauth_signature_method', $_GET['oauth_signature_method'], $expire,'/');
    setcookie('oauth_signature', $_GET['oauth_signature'], $expire,'/');
}
?>
