<?php

require_once('RESTUtil.php');

class MWAPIREST {

   
        public function processRequest(){
		$request_method = strtolower($_SERVER['REQUEST_METHOD']);
                $request_uri = strtolower($_SERVER['REQUEST_URI']);
                $data = null;
		switch ($request_method)
		{
                    case 'get':
                            $data = $_GET;
                            $this->processGETRequest($request_uri);
                            break;
                    case 'post':
                            $data = json_decode(file_get_contents('php://input'));
                            $this->processPOSTRequest($request_uri, $data);
                            break;
                    case 'put':
                            $data = json_decode(file_get_contents('php://input'));
                            $this->processPUTRequest($request_uri, $data);
                            break;
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
                    $this->managePutUser($data);
                    break;
            }
        }


public function manageGetPage($ID){
    if(isset($ID)){
        $title = getPage($ID);
        $article = new Article(Title::newFromText($title));
        $res = array('titulo'=>$title, 'texto'=>$article->getContent(), 'resumen'=>$article->getComment());

    }else{
        $res = getPages();
    }
    $this->manageResponse($res);
}

public function managePostPage($data){
    createPage($data->Titulo, $data->Texto, $data->Resumen);
    return header("{$_SERVER['SERVER_PROTOCOL']} 201 Created");
}

public function managePutPage($data){
    updatePage($data->Titulo, $data->Texto, $data->Resumen);
    return header("{$_SERVER['SERVER_PROTOCOL']} 200 OK");
}

public function manageGetUser($ID){
    if(isset($ID)){
        $user = getUser($ID);
        $res = array('nombre'=>$user->getName(), 'mail'=>$user->getEmail(), 'real name'=>$user->getRealName());
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
}

?>
