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
* @param array $data user data
*/

function createUser($data){
    global $wgRequest;

    $user = User::newFromName($data['name']);
    if(!$user)
        throw new Exception("Invalid data\n", "001");
    if ( !$user->getID() ) {

        $user->setPassword($data['password']);
        $user->createNew($user->getName(), array(
					"password" => $user->mPassword,
					"email" => $data['email'],
					"real_name" => $data['realname']));
    } else {
        $user->setPassword($data['password']);
        echo "The password has been changed";
    }
    $user->saveSettings();
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
?>
