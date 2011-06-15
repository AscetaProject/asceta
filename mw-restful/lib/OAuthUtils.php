<?php


/**
* OAuth Controller
*
* To Control the Authorizatión process.
* Allow the following actions
*  Register
*  Request-token
*  Auth
*  Access token
*
* @author Mara Jiménez Torres
* @author Fundación I+D del Software Libre
*
*/
class OAuthController {

	public $is_signed;
	public $consumer_key;
	public $consumer_secret;
	public $consumer_id;
	protected $store;

        /**
        * Construct function
        *
        */
	public function __construct() {
		global $do_return;
		//print_r($_SESSION);
		// First check if we are working with one of oauth main methods (register, request_token, auth or access-token)
                $this->dispatch ();
		// Check if the request comes signed with oauth protocol
		//$this->isSigned ();
	}

        /**
        * From the variable $mwpr finds that action is to perform
        * and execute the corresponding function
        *
        */
	public function dispatch() {
		global $mwpr;
		switch (getAction($mwpr['uri'])) {
			case 'register' :
				self::doRegister ();
				break;
			case 'request-token' :
				self::doRequestToken ();
				break;
			case 'auth' :
				self::doAuthorize ();
				break;
			case 'access-token' :
				self::doAccessToken ();
				break;
		}
	}

        /**
        * Check if the uri call is signed.
        *
        */
	public function isSigned() {
		global $mwpr;
		OAuthStore::instance ( 'MySQL', array ('conn' => $mwpr['conn'] ) );
		if (OAuthRequestVerifier::requestIsSigned ()) {
			try {
				$req = new OAuthRequestVerifier ( );
				$user_id = $req->verify ('request');
				// If we have an user_id, then login as that user (for this request)
				if ($user_id) {
					$this->is_signed = true;
				}
			} catch ( OAuthException2 $e ) {
				// The request was signed, but failed verification
				header ( 'HTTP/1.1 401 Unauthorized' );
				header ( 'WWW-Authenticate: OAuth realm=""' );
				header ( 'Content-Type: text/plain; charset=utf8' );

				echo $e->getMessage ();
				exit ();
			}
		} else {
			$this->is_signed = false;
		}
	}

        /**
        * Sets the output header
        *
        */
	protected static function header() {
		header ( 'X-XRDS-Location: http://' . $_SERVER ['SERVER_NAME'] . '/services.xrds' );
	}

        /**
        * Add the application in the system, generating the values ​​
        * for the consumer key and the consumer secret
        *
        * @return object if the register finished ok
        */
	public function doRegister() {
                global $mwuser;
                // Future check for only registred users to sign to API
                $args = array();
		if (0 != $mwuser) {
			self::header ();
                        $user = getUser($mwuser);
                        $args = array('requester_name' => $user->mName,
                                'requester_email' => $user->mEmail,
                                'callback_uri' => @$_POST ['callback_uri'],
                                'application_uri' => @$_POST ['application_uri'],
                                'application_title' => @$_POST ['application_title'],
                                'application_descr' => @$_POST ['application_descr'],
                                'application_notes' => @$_POST ['application_notes'],
                                'application_type' => @$_POST ['application_type'],
                                'application_commercial' => @$_POST ['application_commercial']);
                        unset ($_POST);
                        // Register the consumer
                        $this->store = OAuthStore::instance ( 'MySQL', array ('conn' => $mwpr['conn']));
                        $key = $this->store->updateConsumer ( $args, $mwuser);
                        // Get the complete consumer from the store
                        $consumer = $this->store->getConsumer ( $key, $mwuser);
                        // Some interesting fields, the user will need the key and secret
                        $this->consumer_id = $consumer ['id'];
                        $this->consumer_key = $consumer ['consumer_key'];
                        $this->consumer_secret = $consumer ['consumer_secret'];
                        return $consumer;
		} else {
			return null;
		}
	}

        /**
        * Obtain the request token
        *
        */
	protected static function doRequestToken() {
		global $mwpr;

		self::header ();
		OAuthStore::instance ( 'MySQL', array ('conn' => $mwpr['conn'] ) );
		$server = new OAuthServer ( );
		$token = $server->requestToken ();
		exit();
	}

        /**
        * Ask to the user if allow that consumer can communicate with the
        * server
        *
        */
	protected static function doAuthorize() {
                global $mwpr, $mwuser, $pages, $wgLang, $wgOut;
		self::header ();
		// Future check for only registred users to sign to API
		if (0 != $mwuser) {
			$store = OAuthStore::instance ( 'MySQL', array ('conn' => $mwpr['conn'] ) );
			$server = new OAuthServer ( );
			try {
				// Check if there is a valid request token in the current request
				// Returns an array with the consumer key, consumer secret, token, token secret and token type.
				$rs = $server->authorizeVerify ();
				if ($_SERVER ['REQUEST_METHOD'] == 'POST') {
					// See if the user clicked the 'allow' submit button (or whatever you choose)
					$authorized = array_key_exists ( 'allow', $_POST );
					//$authorized = true;
					// Set the request token to be authorized or not authorized
					// When there was a oauth_callback then this will redirect to the consumer
					$result = $server->authorizeFinish ( $authorized, $mwuser );

					// No oauth_callback, show the user the result of the authorization
					// ** your code here **
                                        $consumer = $store->getConsumer ( $rs['consumer_key'], $mwuser);
                                        header ('Location: '.$consumer['callback_uri']);
					//echo 'Authorized';
				} elseif ($_SERVER ['REQUEST_METHOD'] == 'GET') {
					//$authorized = true;
                                        // headers for not caching the results
                                        header('Cache-Control: no-cache, must-revalidate');
                                        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');

                                        // headers to tell that result is JSON
                                        header('Content-Type: text/html; charset=ISO-8859-4');
					$html = self::printAuthorizeRequest(); //file_get_contents($GLOBALS['dir'].'lib/html_api_authorize.php');
                                        echo $html;
					// Safely break back to WordPress
					exit ();
					//$server->authorizeFinish ( $authorized, $_SESSION ['user_id'] );
					//echo 'Authorized';
				} else {
					echo 'No recognized request. Only POST or GET.';
				}
			} catch ( OAuthException $e ) {
				// No token to be verified in the request, show a page where the user can enter the token to be verified
				echo 'No Token found!';
			}
		} else {
			//WPRESTUtils::sendResponse ( 401 );
			$url = $GLOBALS['wgServer'].$GLOBALS['wgScriptPath'].'/index.php/'.$pages[$wgLang->getCode()]['special'].':'.$pages[$wgLang->getCode()]['login'].'/'.$pages[$wgLang->getCode()]['mainpage'];
                        header("Location: ".$url);
			exit ();
		}
                exit();
	}

        /**
        * Obtain the access token
        *
        */
	protected static function doAccessToken() {
		global $wpdb;

		self::header ();
		OAuthStore::instance ( 'MySQL', array ('conn' => $mwpr['conn'] ) );
		$server = new OAuthServer ( );
		$server->accessToken ();
		exit();
	}

        /**
        * Generate a html string with the form authorize
        *
        * @return string html string
        */
        protected function printAuthorizeRequest(){
            global $pages, $wgLang;
            $lang = 'es';
            if($wgLang->getCode() != 'es') {
                   $lang = 'en';
            }
            $application = null;
            if(isset($_SESSION['verify_oauth_app_title']) && !empty($_SESSION['verify_oauth_app_title'])){
                $application = $_SESSION['verify_oauth_app_title'];
            }else{
                $application = urldecode($_GET['oauth_callback']);
            }
            return "<html><body>
                    <h2>".$pages[$lang]['authorize']."</h2>
                    <p>".$pages[$lang]['authorizetext']." ".$application."</p>
                    <div style=\"margin-top: 40px;\">
                    <form action=\"\" name=\"auth_form\" method=\"post\">
                    <input type=\"submit\" name=\"allow\" value=\"".$pages[$lang]['allow']."\" />
                    <input type=\"submit\" name=\"deny\" value=\"".$pages[$lang]['deny']."\" />
                    </form>
                    </div>
                    </body></html>";
        }
}
?>
