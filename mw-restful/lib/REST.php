<?php

require_once('RESTUtil.php');
require_once('library/OAuthRequestVerifier.php');
require_once('OAuthUtils.php');

/**
* MWAPIREST
*
* To manage request REST from client
* Allow the following actions
*  get/post/put pages
*  get/post/put users
*
* @author Mara Jiménez Torres
* @author Fundación I+D del Software Libre
*
*/
class MWAPIREST {

    /**
    * Proccess the request
    *
    */
    public function processRequest(){
        global $mwpr;

        $mwpr['method'] = strtolower($_SERVER['REQUEST_METHOD']);
        $mwpr['uri'] = convertToBeautyURL(strtolower($_SERVER['REQUEST_URI']));
        $data = null;

        try{
            $do_return = false;
            $oauth = new OAuthController();

            $req = new OAuthRequestVerifier();
            $GLOBALS['vars'] = $req->getBody();
            $user_id = $req->verify();

            switch ($mwpr['method'])
            {
                case 'get':
                        $data = $_GET;
                        $this->processGETRequest($mwpr['uri']);
                        break;
                case 'post':
                        $data = $_POST;
                        $this->processPOSTRequest($mwpr['uri'], $data);
                        break;
                case 'put':
                        $data = json_decode($GLOBALS['vars']);
                        $this->processPUTRequest($mwpr['uri'], $data);
                        break;
           }
        } catch (OAuthException2 $e){
            header ( 'HTTP/1.1 401 Unauthorized' );
            header ( 'WWW-Authenticate: OAuth realm=""' );
            header ( 'Content-Type: text/plain; charset=utf8' );

            echo $e->getMessage ();
            exit ();
        } catch (Exception $e) {
            $code = $e->getCode();
            switch ($code){
                case 1:
                    header ( 'HTTP/1.1 400 Bad Request' );
                    break;
                case 2:
                    header ( 'HTTP/1.1 404 Not Found' );
                    break;
                default:
                    header ( 'HTTP/1.1 406 Not Acceptable' );
            }

            echo $e->getMessage ();
            exit ();
        }
    }

    /**
    * Proccess Get request
    *
    * @param string $request_uri request uri
    */
    function processGETRequest($request_uri){
        switch (getAction($request_uri))
        {
            case 'pages':
                $this->manageGetPage(getID($request_uri));
                break;
            case 'users':
                $this->manageGetUser(getID($request_uri));
                break;
        }
    }

    /**
    * Proccess POST request
    *
    * @param string $request_uri request uri
    * @param array $data parameters from the body header
    */
    function processPOSTRequest($request_uri, $data){
        switch(getAction($request_uri))
        {
            case 'pages':
                $this->managePostPage($data);
                break;
            case 'users':
                $this->managePostUser($data);
                break;
        }
    }

    /**
    * Proccess PUT request
    *
    * @param string $request_uri request uri
    * @param array $data parameters from the body header
    */
    function processPUTRequest($request_uri, $data){
        switch(getAction($request_uri))
        {
            case 'pages':
                $this->managePutPage($data);
                break;
            case 'users':
                $this->managePutUser(getID($request_uri), $data);
                break;
        }
    }


    /**
    * Manage GET request page
    *
    * @param string $ID page identification number
    */
    public function manageGetPage($ID){
        if(isset($ID)){
                $title = getPage($ID);
                $articleTitle = Title::newFromText($title);
                if (!is_null($articleTitle)){
                    $article = new Article($articleTitle);
                    $res = array('titulo'=>$title, 'texto'=>$article->getContent(), 'resumen'=>$article->getComment());
                } else{
                    throw new Exception("Undefined index. The page that you try to access does not exist", "002");
            }

        }else{
            $res = getPages();
        }
        $this->manageResponse($res);
    }

    /**
    * Manage POST request page
    *
    * @return string header with the message 201 Created
    * @param array $data parameters from the body header
    */
    public function managePostPage($data){
        createPage($data['titulo'], $data['texto'], $data['resumen']);
        return header("{$_SERVER['SERVER_PROTOCOL']} 201 Created");
    }

    /**
    * Manage PUT request page
    *
    * @return string header with the message 200 OK
    * @param array $data parameters from the body header
    */
    public function managePutPage($data){
        updatePage($data->titulo, $data->texto, $data->resumen);
        return header("{$_SERVER['SERVER_PROTOCOL']} 200 OK");
    }

    /**
    * Manage GET request user
    *
    * @param string $ID user identification number
    */
    public function manageGetUser($ID){
        if(isset($ID)){
            $user = getUser($ID);
            if(!is_null($user)){
                $res = array('nombre'=>$user->getName(), 'mail'=>$user->getEmail(), 'real name'=>$user->getRealName());
            }else{
                throw new Exception("Undefined index. The user that you try to access does not exist", "002");
            }
        }else{
            $res = getUsers();
        }
        $this->manageResponse($res);
    }

    /**
    * Manage POST user
    *
    * @return string header with the message 201 Created
    * @param array $data parameters from the body header
    */
    public function managePostUser($data){
        createUser($data);
        return header("{$_SERVER['SERVER_PROTOCOL']} 201 Created");
    }

    /**
    * Manage PUT user
    *
    * @return string header with the message 201 Created
    * @param string $ID user identification number
    * @param array $data parameters from the body header
    */
    public function managePutUser($ID, $data){
        updateUser($ID, $data);
        return header("{$_SERVER['SERVER_PROTOCOL']} 200 OK");
    }


    /**
    * Create the reply message with $data
    *
    * @param array $data parameters from the body header
    */
    public function manageResponse($data){
        // headers for not caching the results
        header('Cache-Control: no-cache, must-revalidate');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');

        // headers to tell that result is JSON
        header('Content-type: application/json');

        // send the result now
        echo json_encode($data);
    }
    
    /**
    * show the response Form
    *
    */
    public function responseForm(){
        // headers for not caching the results
        header('Cache-Control: no-cache, must-revalidate');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');

        // headers to tell that result is JSON
        header('Content-Type: text/html; charset=ISO-8859-4');

        //send the result now
        require_once 'html_api_register.php';

    }
}

/**
* MWAPIREGISTER
*
* To help in the register process
*   check if the user is logged
*   Check if the user is registred
*
* @author Mara Jiménez Torres
* @author Fundación I+D del Software Libre
*
*/
class MWAPIREGISTER {

    /**
    * check if the user is logged
    *
    * @return boolean true if the session user is logged
    */
    public function isLogged(){
        if($_SESSION['wsUserID'] != null)
            return true;
        return false;
    }

    /**
    * check if the user is register
    *
    * @return boolean true si la direccion es correcta
    * @param string $user_id user identification number
    */
    public function isRegistred($user_id) {
        global $mwpr;
        $store = OAuthStore::instance('MySQL', array('conn' => $mwpr['conn']));
        $list = $store->listConsumers($user_id);

        return $list;
    }


    /**
     * Update callback_uri data from the consumer
     *
     * @return
     * @param string $user_id user identification number
     * @param array $consumer consumer data
     */
    public function updateConsumer($user_id, $consumer){
        global $mwpr;
        $store = OAuthStore::instance('MySQL', array('conn' => $mwpr['conn']));
        $key = $store->updateConsumer ( $consumer, $user_id);
    }

}


?>
