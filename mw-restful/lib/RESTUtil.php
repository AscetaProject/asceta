<?php

//Funciones para acceso a la API de MediWiki, necesarios para la comunicación REST
//
//1. GET api/users -> list all users
function getUsers(){
    //TODO hacerlo con variables globales
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
//2. GET api/users/1 -> list user with id 1
function getUser($ID){
    $user = User::newFromId($ID);
    $user->loadFromId();
    return $user;
}
//3. POST api/users -> create a new user
function createUser($data){
    $user = User::newFromName($data->name);
    if ( !$user->getID() ) {
        $user = User::createNew($user->getName(), array(
					"password" => $data->password,
					"email" => $data->email,
					"real_name" => $data->realname));
    } else {
        $user->setPassword($data->password);
    }
    $user->saveSettings();
}
//4. PUT api/users/1 -> edit user with id 1
function updateUser($ID, $data){
    $user = User::newFromId($ID);
    $user->load();
    foreach($data as $key=>$value){
        if(isset($value)){
            $method = 'set' . ucwords($key);
            call_user_func(array($user, $method), $value);
        }
    }
    $user->saveSettings();
}
//5. GET api/pages -> list all pages
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
//6. GET api/pages/1 -> list page with id 1
function getPage($page){
    $res = select('page','page_title','page_id = '.$page);
    $dbr = wfGetDB( DB_SLAVE );
    $row = $dbr->fetchObject( $res );
    return $row->page_title;
}
//7. POST api/pages-> create a new page
function createPage($name, $text, $summary){
    $title = Title::newFromText( $name );
    if ( is_null($title) ) {
            wfDie( "invalid title\n" );
    }

    $aid = $title->getArticleID( GAID_FOR_UPDATE );
    if ($aid != 0) {
            wfDie( "duplicate article '$name'\n" );
    }

    $art = new Article($title);
    $art->insertNewArticle($text, $summary);

}
//8. PUT api/pages/1 -> edit page with id 1
function updatePage($name, $text, $summary){
    $title = Title::newFromText( $name );
    if ( is_null($title) ) {
            wfDie( "invalid title\n" );
    }

    $aid = $title->getArticleID( GAID_FOR_UPDATE );
    if ($aid == 0) {
            wfDie( "not exists article '$name'\n" );
    }

    $art = new Article($title);
    $art->updateArticle( $text, $summary);
}

function select($table, $columns, $cond=''){
    #Get data from database
    $dbr = wfGetDB( DB_SLAVE );
    $res = $dbr->select($table,$columns, $cond);
    return $res;
}


function getAction($data){
    $text = explode('/', strtolower($data));
    return $text[4];
}

function getID($data){
    $text = explode('/', strtolower($data));
    return $text[5];
}
?>
