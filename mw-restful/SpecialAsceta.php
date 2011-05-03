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
            global $wgRequest, $wgOut;
            wfLoadExtensionMessages('asceta ');
            // Disable the regular OutputPage stuff -- we're taking over output!
            $wgOut->disable();

            // Set your content type... this can XML or binary or whatever you need.
            header( "Content-type: text/plain; charset=utf-8" );

            require_once 'lib/REST.php';

            $apiREST = new MWAPIREST();
            $apiREST->processRequest();

        }



}

?>

