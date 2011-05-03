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

?>

