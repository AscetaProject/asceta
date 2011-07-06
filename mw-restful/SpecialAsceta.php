<?php

global $wgPdfExportAttach, $wgPdfExportHttpsImages;
$wgPdfExportAttach = false; // set to true if you want output as an attachment
$wgPdfExportHttpsImages = false; // set to true if page is on a HTTPS server and contains images that are on the HTTPS server and also
                                 // reachable with HTTP

class SpecialAsceta extends SpecialPage {

        function SpecialAsceta (){
            SpecialPage::SpecialPage( 'asceta', '', false );
        }

        function execute( $par ) {
            global $wgRequest, $wgOut, $wgTitle, $pages, $wgLang, $mwuser, $mwpr;
            wfLoadExtensionMessages('asceta ');

            require_once 'lib/REST.php';
            if(isset($_SESSION)) $mwuser = $_SESSION['wsUserID'];
            $apiRegister = new MWAPIREGISTER();
            
            //Call to the main page (Register)
            if(getName($_GET['page']) == $pages[$wgLang->getCode()]['mainpage'] || $_GET['title'] == $pages[$wgLang->getCode()]['special'].':Asceta/'.$pages[$wgLang->getCode()]['mainpage']){
                require_once 'lib/OAuthUtils.php';

                if($_POST['submit_application'] == 'Finish'){
                    $consumer = OAuthController::doRegister();
                    if($consumer != null){
                         $wgOut->addHTML($this->printConsumerInfo($consumer));
                    } else {
                        $texto = $pages[$this->getLanguage()]['loginError'];
                        $wgOut->addHTML($texto);
                    }
                }else if (isset($_GET['callback_uri']) || isset($_COOKIE['callback_uri'])){
                        $parameters = $this->getValues();
                        $consumer_list = $apiRegister->isRegistred($mwuser);
                        if(!empty ($consumer_list)){
                            $consumer_list[0]['callback_uri'] = $parameters['callback_uri'];
                            $apiRegister->updateConsumer($mwuser, $consumer_list[0]);
                            $html = $this->printConsumerInfo($consumer_list[0]);
                            $wgOut->addHTML($html);
                        }else{
                            $consumer = OAuthController::automaticRegister($parameters);
                            if($consumer != null){
                                 $wgOut->addHTML($this->printConsumerInfo($consumer));
                            } else {
                                $texto = $pages[$this->getLanguage()]['loginError'];
                                $wgOut->addHTML($texto);
                            }
                        }
                        unset($parameters);
                }else{
                    if($apiRegister->isLogged()){
                        $path = null;
                        $consumer_list = $apiRegister->isRegistred($mwuser);
                        if(!empty ($consumer_list)){
                            $html = $this->printConsumerInfo($consumer_list[0]);
                        }else{
                            $html = $this->printRegisterForm(); 
                        }
                       $wgOut->addHTML($html);
                    }else{
                        $get_params = "";
                        if($_GET['callback_uri']){
                            saveParams($_GET);
                        }
                        $url = $GLOBALS['wgServer'].$GLOBALS['wgScriptPath'].'/index.php/'.$pages[$wgLang->getCode()]['special'].':'.$pages[$wgLang->getCode()]['login'].'/'.$pages[$wgLang->getCode()]['mainpage'].'?returnto='.$pages[$wgLang->getCode()]['special'].':Asceta&returntoquery='.'page='.$pages[$wgLang->getCode()]['mainpage'];
                        //header("Location: ".$url);
                        $wgOut->redirect($url);
                    }
                }
            }else{   
                header( "Content-type: text/plain; charset=utf-8" );
                // Disable the regular OutputPage stuff -- we're taking over output!
                $wgOut->disable();
                $apiREST = new MWAPIREST();
                $apiREST->processRequest();
            }
        }

        protected function getLanguage(){
            global $wgLang;
            if($wgLang->getCode() != 'es') {
                   return 'en';
            }
            return 'es';
        }
        protected function printConsumerInfo($consumer){
            global $pages;
            $lang = $this->getLanguage();
            return "<h2>".$pages[$lang]['credentials']."</h2>
                    <div style=\"margin-top: 40px;\">
                    <p><label>Consumer Token: </label><span style=\"font-size:18px; color: #999\">".$consumer['consumer_key']."</span></p>
                    <p><label>Consumer Secret Token: </label><span style=\"font-size:18px; color: #999\">".$consumer['consumer_secret']."</span></p></div>";
        }

        protected function printRegisterForm(){
            global $pages, $wgLang;
            $lang = $this->getLanguage();
            return "<h2>".$pages[$lang]['credentials']."</h2>
                    <p>".$pages[$lang]['oauthmessage']."</p>
                    <div style=\"margin-top: 40px;\">
                    <form action=\"\" method=\"post\" style=\"text-align: left;\">
                    <p>
                    <label>Callback URL</label>
                        <input class=\"text-input large-input\" type=\"text\" style=\"width: 200px;\" id=\"callback_uri\" name=\"callback_uri\" value=\"\" />
                        <small>".$pages[$lang]['callbackurltext']."</small>
                    </p>
                    <p>
                    <label>".$pages[$lang]['applicationurl']."</label>
                        <input class=\"text-input large-input\" type=\"text\" style=\"width: 200px;\" id=\"application_uri\" name=\"application_uri\" value=\"\" />
                        <small>".$pages[$lang]['applicationurltext']."</small>
                    </p>
                    <p>
                        <label>".$pages[$lang]['applicationtitle']."</label>
                        <input class=\"text-input large-input\" type=\"text\" style=\"width: 200px;\" id=\"application_title\" name=\"application_title\" value=\"\" />
                        <small>".$pages[$lang]['applicationtitletext']."</small>
                    </p>
                    <p style=\"margin-top: 20px;\">
                    <label>".$pages[$lang]['applicationdescription']."</label>
                    <textarea class=\"text-input textarea wysiwyg\" id=\"textarea\" name=\"application_descr\" cols=\"70\" rows=\"15\"></textarea>
                    <small>".$pages[$lang]['applicationdescriptiontext']."</small>
                    </p>
                    <p>
                    <label>".$pages[$lang]['applicationtype']."</label>
                    <select class=\"small-input\" name=\"application_type\">
                        <option selected=\"selected\" value=\"website\">".$pages[$lang]['website']."</option>
                        <option value=\"iphone\">".$pages[$lang]['applicationiphone']."</option>
                   </select>
                    <br/><small>".$pages[$lang]['applicationtypetext']."</small>
                    </p>
                    <p>
                    <label>".$pages[$lang]['comercialuse']."</label>
                    <select class=\"small-input\" name=\"application_commercial\">
                        <option selected=\"selected\" value=\"0\">No</option>
                        <option value=\"1\">".$pages[$lang]['yes']."</option>
                    </select>
                    <br/>
                    </p>
                    <input type=\"submit\" value=\"Finish\" name=\"submit_application\" class=\"submit\"/>
                    </form>
                    </div>";
        }

        protected function getValues(){
               if(isset($_GET['callback_uri'])){
                    saveParams($_GET);
                    return $_GET;
                }else{
                    return $_COOKIE;
                }
        }


}

?>

