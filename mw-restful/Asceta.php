<?php
# Alert the user that this is not a valid entry point to MediaWiki if they try to access the special pages file directly.
if (!defined('MEDIAWIKI')) {
        echo <<<EOT
To install my extension, put the following line in LocalSettings.php:
require_once( "\$IP/extensions/asceta/asceta.php" );
EOT;
        exit( 1 );
}

$wgExtensionCredits['specialpage'][] = array(
        'name' => 'Asceta',
        'author' => 'mjimenez, Fundación I+D software libre',
        'url' => 'http://www.mediawiki.org/wiki/Extension:asceta',
        'description' => 'To comunicate with moodle via REST',
        'descriptionmsg' => 'asceta-desc',
        'version' => '0.0.1'
);

$dir = dirname(__FILE__) . '/';

$wgAutoloadClasses['SpecialAsceta'] = $dir . 'SpecialAsceta.php'; # Location of the SpecialMyExtension class (Tell MediaWiki to load this file)
$wgExtensionMessagesFiles['Asceta'] = $dir . 'Asceta.i18n.php'; # Location of a messages file (Tell MediaWiki to load this file)
$wgSpecialPages['Asceta'] = 'SpecialAsceta'; # Tell MediaWiki about the new special page and its class name
$wgSpecialPageGroups['Asceta'] = 'pagetools';

$wgHooks['SkinTemplateBuildNavUrlsNav_urlsAfterPermalink'][] = 'wfSpecialAscetaNav';
$wgHooks['SkinTemplateToolboxEnd'][] = 'wfSpecialAscetaToolbox';

function wfSpecialAscetaNav( &$skintemplate, &$nav_urls, &$oldid, &$revid ) {
	wfLoadExtensionMessages( 'asceta' );
        $nav_urls['asceta'] = array(
                        'text' => wfMsg( 'asceta_link' ),
                        'href' => $skintemplate->makeSpecialUrl( 'asceta', "page=" . wfUrlencode( "{$skintemplate->thispage}" )  )
                );
        return true;
}

function wfSpecialAscetaToolbox( &$monobook ) {
	wfLoadExtensionMessages( 'asceta' );
        if ( isset( $monobook->data['nav_urls']['asceta'] ) )
                if ( $monobook->data['nav_urls']['asceta']['href'] == '' ) {
                        ?><li id="t-ispdf"><?php echo $monobook->msg( 'asceta_link' ); ?></li><?php
                } else {
                        ?><li id="t-pdf">
<?php
                                ?><a href="<?php echo htmlspecialchars( $monobook->data['nav_urls']['asceta']['href'] ) ?>"><?php
                                        echo $monobook->msg( 'asceta_link' );
                                ?></a><?php
                        ?></li><?php
                }
        return true;
}



global $mwpr, $mwuser;
$mwpr = array();
$mwuser = '';
/*
 * Initialize the database connection
 */
$info = parse_url($wgDBtype."://".$wgDBuser.":".$wgDBpassword."@".$wgDBserver."/".$wgDBname);
($GLOBALS['db_conn'] = mysql_connect($info['host'], $info['user'], $info['pass'])) || die(mysql_error());
mysql_select_db(basename($info['path']), $GLOBALS['db_conn']) || die(mysql_error());
unset($info);


require_once 'lib/library/OAuthServer.php';

/*
 * Initialize OAuth store
 */
require_once 'lib/library/OAuthStore.php';
OAuthStore::instance('MySQL', array('conn' => $GLOBALS['db_conn']));

$mwpr['conn'] = $GLOBALS['db_conn'];


/*
 * Internacionalization
 */

global $pages;
$pages = array();

/* *** English *** */
$pages['en'] = array(
        'special' => 'Special',
        'mainpage' => 'Main_Page',
        'login' => 'UserLogin',
        'credentials' => 'MW-RESTful REQUEST API CREDENTIALS',
        'oauthmessage' => 'In order to use our API you need to register your application on our server.<br/>Some of the methods require authentication, more specifically, <a href=\"http://www.oauth.com\" target=\"_blank\">OAuth</a>.<br/><u>Note:</u> None of the fields below are mandatory, but we recommend that you do.',
        'callbackurltext' => 'The URL to be called after we process your request. (Example, http://www.example.com/api/success)',
        'applicationurl' => 'Application URL',
        'applicationurltext' => 'The URL to your application website. It\'s always good to provide this.',
        'applicationtitle' => 'Application Title',
        'applicationtitletext' => 'The name we should give to your application. (Example, \"My first Flocks app\")',
        'applicationdescription' => 'Application Description',
        'applicationdescriptiontext' => 'Tell us your app or website does or is meant for.',
        'applicationtype' => 'Application Type',
        'website' => 'Website',
        'applicationiphone' => 'iPhone Application',
        'applicationwindow' => 'Window Mobile Application',
        'applicationtypetext' => 'What kind of application are you building?',
        'comercialuse' => 'Is your application for commercial use?',
        'yes' => 'Yes',
        'finish' => 'Finish',
        'authorize' => 'MW-RESTful API AUTHORIZE',
        'authorizetext' => 'Do you give permission to ',
        'allow' => 'Allow',
        'deny' => 'Deny',
        'loginError' => 'The user must be logged into the System'
);

/* *** 	Afrikaans *** */
$pages['af'] = array(
        'special' => 'Spesiaal',
        'mainpage' => 'Tuisblad',
        'login' => 'Teken_in'
);

/* *** 	Azerbaijani *** */
$pages['az'] = array(
        'special' => 'Xüsusi',
        'mainpage' => 'Ana_Səhifə',
        'login' => 'UserLogin'
);

/* *** 	Catalan *** */
$pages['ca'] = array(
        'special' => 'Especial',
        'mainpage' => 'Pàgina_principal',
        'login' => 'Registre_i_entrada'
);

/* *** 	German *** */
$pages['ge'] = array(
        'special' => 'Spezial',
        'mainpage' => 'Hauptseite',
        'login' => 'Anmelden'
);

/* *** Spanish (Español) *** */
$pages['es'] = array(
        'special' => 'Especial',
        'mainpage' => 'Página_Principal',
        'login' => 'Entrar',
        'credentials' => 'MW-REST API SOLICITUD DE CREDENCIALES',
        'oauthmessage' => 'Para poder utilizar nuestra API tendrá que registrar su solicitud en nuestro servidor. <br/> Algunos de los métodos requieren autenticación, más concretamente, <a href=\"http://www.oauth.com\" target=\"_blank\">OAuth</a>.<br/><u>Nota:</u> Ninguno de los siguientes campos son obligatorios, pero le recomendamos que los rellene.',
        'callbackurltext' => 'La dirección URL que se llamará después de procesar su solicitud. (Por ejemplo, http://www.example.com/api/success)',
        'applicationurl' => 'URL de la aplicación',
        'applicationurltext' => 'La dirección URL de su sitio web de la aplicación. Siempre es bueno para proporcionarlo.',
        'applicationtitle' => 'Titulo de la aplicación',
        'applicationtitletext' => 'Nombre que quiere darle a su aplicación',
        'applicationdescription' => 'Descripción de la aplicación',
        'applicationdescriptiontext' => 'Que hace o que proposito tiene su aplicacioón o página web',
        'applicationtype' => 'Tipo de aplicación',
        'website' => 'Página web',
        'applicationiphone' => 'Aplicación móvil',
        'applicationtypetext' => '¿Qué tipo de aplicación es?',
        'comercialuse' => '¿Es su aplicación para uso comercial?',
        'yes' => 'Sí',
        'finish' => 'Terminar',
        'authorize' => 'AUTORIZACIÓN API MW-REST',
        'authorizetext' => 'Autoriza el acceso a ',
        'allow' => 'Autorizar',
        'deny' => 'Denegar',
        'loginError' => 'El usuario debe de estar logueado en el Sistema'
);

/* *** 	French *** */
$pages['fr'] = array(
        'special' => 'Spécial',
        'mainpage' => 'Accueil',
        'login' => 'Connexion'
);


/* *** 	Italian *** */
$pages['it'] = array(
        'special' => 'Speciale',
        'mainpage' => 'Pagina_principale',
        'login' => 'Entra'
);


/* *** 	Portuguese *** */
$pages['pt'] = array(
        'special' => 'Especial',
        'mainpage' => 'Página_principal',
        'login' => 'Entrar'
);

?>

