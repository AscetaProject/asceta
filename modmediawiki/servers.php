<?php

require_once("../../config.php");
require_once($CFG->libdir.'/adminlib.php');

$id = required_param('id', 0, PARAM_INT); // server ID
//$action  = require_param('action', 0, PARAM_INT);  // action
$action = 'new';

/// Print the page header
$PAGE->set_url('/mod/modmediawiki/server.php', array('id' => $id, 'action' => $action));
admin_externalpage_setup('managemodules'); // this is hacky, tehre should be a special hidden page for it

// Output starts here
echo $OUTPUT->header();

switch ($action){
    case 'new':
        createNewServer();
        break;
    case 'register':
        break;
    case 'request':
        break;
    case 'auth':
        break;
    case 'access':
        break;
    default:
        break;
}

// Replace the following lines with you own code
//echo $OUTPUT->heading('Yay! It works!');


// Finish the page
echo $OUTPUT->footer();

function createNewServer(){
    echo $OUTPUT->heading('Yay! It works!');
}
?>