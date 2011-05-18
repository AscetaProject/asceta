<?php

require_once('RESTUtil.php');
require_once('library/OAuthRequestVerifier.php');
require_once('OAuthUtils.php');

class MWAPIREST {

    public function processRequest(){
        global $mwpr;

        $mwpr['method'] = strtolower($_SERVER['REQUEST_METHOD']);
        $mwpr['uri'] = strtolower($_SERVER['REQUEST_URI']);
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
                        $data = json_decode($GLOBALS['vars']);
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

    public function managePostPage($data){
        createPage($data->titulo, $data->texto, $data->resumen);
        return header("{$_SERVER['SERVER_PROTOCOL']} 201 Created");
    }

    public function managePutPage($data){
        updatePage($data->titulo, $data->texto, $data->resumen);
        return header("{$_SERVER['SERVER_PROTOCOL']} 200 OK");
    }

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

    public function managePostUser($data){
        createUser($data);
        return header("{$_SERVER['SERVER_PROTOCOL']} 201 Created");
    }

    public function managePutUser($ID, $data){
        updateUser($ID, $data);
        return header("{$_SERVER['SERVER_PROTOCOL']} 200 OK");
    }


    public function manageResponse($data){
        // headers for not caching the results
        header('Cache-Control: no-cache, must-revalidate');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');

        // headers to tell that result is JSON
        header('Content-type: application/json');

        // send the result now
        echo json_encode($data);
    }
    
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


class MWAPIREGISTER {

    public function isLogged(){
        if($_SESSION['wsUserID'] != null)
            return true;
        return false;
    }

    public function isRegistred($user_id) {
        global $mwpr;
        $store = OAuthStore::instance('MySQL', array('conn' => $mwpr['conn']));
        $list = $store->listConsumers($user_id);

        return $list;
    }

}


?>
