<?php

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.


/**
 * Prints a particular instance of modmendeley
 *
 * You can have a rather longer description of the file as well,
 * if you like, and it can span multiple lines.
 *
 * @package   mod_modmendeley
 * @copyright 2011 María del Mar Jiménez Torres (mjimenez@fidesol.org) - Fundación I+D del Software Libre (www.fidesol.org)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/// (Replace modmendeley with the name of your module and remove this line)

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/lib.php');

$id = optional_param('id', 0, PARAM_INT); // course_module ID, or
$n  = optional_param('n', 0, PARAM_INT);  // modmendeley instance ID - it should be named as the first character of the module
$option = optional_param('option', '', PARAM_ALPHA); //indicates option tab selected
$action = optional_param('action', '', PARAM_ALPHA); //indicates action selected
$type = optional_param('type', '', PARAM_ALPHA); // indicates type of action selected
$search_data = optional_param('search_data', '', PARAM_ALPHA); //contain search terms
$page = optional_param('page', 0, PARAM_INT); // pagination number
$element_id = optional_param('element_id', 0, PARAM_INT); //element id
$element_name = optional_param('element_name', '', PARAM_ALPHA); // folder_name or group_name
$element_selected = optional_param('element_selected', '', PARAM_ALPHANUMEXT); // folder or group value selected

if ($id) {
    $cm         = get_coursemodule_from_id('modmendeley', $id, 0, false, MUST_EXIST);
    $course     = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
    $modmendeley  = $DB->get_record('modmendeley', array('id' => $cm->instance), '*', MUST_EXIST);
} elseif ($n) {
    $modmendeley  = $DB->get_record('modmendeley', array('id' => $n), '*', MUST_EXIST);
    $course     = $DB->get_record('course', array('id' => $modmendeley->course), '*', MUST_EXIST);
    $cm         = get_coursemodule_from_instance('modmendeley', $modmendeley->id, $course->id, false, MUST_EXIST);
} else {
    error('You must specify a course_module ID or an instance ID');
}

if ($modmendeley->user_id) {
    $user = $DB->get_record('modmendeley_users', array('id' => $modmendeley->user_id), '*', MUST_EXIST);
}

require_login($course, true, $cm);

/// Print the page header

$PAGE->set_url('/mod/modmendeley/view.php', array('id' => $cm->id));
$PAGE->set_title($modmendeley->name);
$PAGE->set_heading($course->shortname);
$PAGE->set_button(update_module_button($cm->id, $course->id, get_string('modulename', 'modmendeley')));

$modmendeleyoutput = $PAGE->get_renderer('mod_modmendeley');

$option = ($option) ? $option : 'paper';
$extraeditbuttons = true;

if (!$modmendeley->user_id) {
   echo $OUTPUT->heading(get_string("configure_user_url","modmendeley"));
} else {
    echo $modmendeleyoutput->header($modmendeley, $cm, $option, $extraeditbuttons);
    switch ($option){
        case 'library':
            if($modmendeley->private){
            }
            switch ($action){
                case 'savedocument':
                    $add_to = explode("-",$_POST['add_to']);
                    if($add_to[1] == 'group'){
                        $data = $_POST[$_POST['pub_type']];
                        $data['group_id'] = $add_to[2];
                    }
                    //$document_id = postLibraryValue('POST', $user, '/library/documents', array('document' => json_encode(toArray($_POST[$_POST['pub_type']]))));
                    $document_id = postLibraryValue('POST', $user, '/library/documents', array('document' => json_encode(toArray($data))));
                    if($document_id != null){
                        if ($add_to[1] == 'profile' && $add_to[2] != 'all'){
                            $document_id = postLibraryValue('POST', $user, '/library/folders/'.$add_to[2].'/'.$document_id, array());
                        }
                        redirect($CFG->wwwroot."/mod/modmendeley/view.php?id=$cm->id&amp;option=library&amp;action=documents&amp;element_selected=$element_selected&amp;sesskey=" . sesskey());
                    }
                    break;
                case 'deletedocument':
                    $documents_id = explode(",", $_GET['documents_id']);
                    foreach($documents_id as $doc_id){
                        deleteLibraryValue('DELETE', $user, '/library/documents/'.$doc_id, array());
                    }
                    redirect($CFG->wwwroot."/mod/modmendeley/view.php?id=$cm->id&amp;option=library&amp;action=documents&amp;element_selected=$element_selected&amp;sesskey=" . sesskey());
                    break;
                case 'savefolder':
                    $fields = array('name'=>$_POST['collection_name'], 'description'=>$_POST['collection_description']);
                    $folder_id = postLibraryValue('POST', $user, '/library/folders', array('folder' => json_encode($fields)));
                    if($folder_id != null){
                        redirect($CFG->wwwroot."/mod/modmendeley/view.php?id=$cm->id&amp;option=library&amp;action=documents&amp;element_selected=$element_selected&amp;sesskey=" . sesskey());
                    }
                    break;
                case 'deletefolder':
                    deleteLibraryValue('DELETE', $user, '/library/folders/'.$element_id, array());
                    redirect($CFG->wwwroot."/mod/modmendeley/view.php?id=$cm->id&amp;option=library&amp;action=documents&amp;element_selected=$element_selected&amp;sesskey=" . sesskey());
                    break;
                case 'deletegroup':
                    deleteLibraryValue('DELETE', $user, '/library/groups/'.$element_id, array());
                    redirect($CFG->wwwroot."/mod/modmendeley/view.php?id=$cm->id&amp;option=library&amp;action=documents&amp;element_selected=$element_selected&amp;sesskey=" . sesskey());
                    break;
                case 'deletedocumentfolder':
                    $documents_id = explode(",", $_GET['documents_id']);
                    foreach($documents_id as $doc_id){
                        deleteLibraryValue('DELETE', $user, '/library/folders/'.$element_id.'/'.$doc_id, array());
                    }
                    redirect($CFG->wwwroot."/mod/modmendeley/view.php?id=$cm->id&amp;option=library&amp;action=documents&amp;element_selected=$element_selected&amp;sesskey=" . sesskey());
                    break;
                default:
                    //$docs_in_folder = getDocumentsInFolder($user);
                    $folders = getLibraryValue('GET', $user, '/library/folders/');
                    $groups = getLibraryValue('GET', $user, '/library/groups/');
                    if ($page)$params['page'] = intval($page) -1;
                    $data_documents = array();
                    if ($action == 'documents') {
                        $data_documents['uri'] =  '/library/';
                        $data_documents['image'] = 'https://www.mendeley.com/graphics/common/folder-all-documents_1437443173056970.png';
                        $data_documents['title'] = 'All Documents';
                        $data_documents['menu'] = 'folder-profile-all';
                    }
                    elseif ($action == 'publications'){
                        $data_documents['uri'] = '/library/documents/authored/';
                        $data_documents['image'] = 'http://www.mendeley.com/graphics/common/folder-my-publications_1484555991348322.png';
                        $data_documents['title'] = 'My Publications';
                        $data_documents['menu'] = 'folder-profile-authored';
                    }
                    elseif ($action == 'folders'){
                        $data_documents['uri'] = '/library/folders/'.$element_id;
                        $data_documents['image'] = 'http://www.mendeley.com/graphics/common/folder-private_6173323349516461.png';
                        $data_documents['title'] = $element_name;
                        $data_documents['menu'] = 'folder-profile-'.$element_id;
                    }
                    elseif ($action == 'groups') {
                        $data_documents['uri'] = '/library/groups/'.$element_id;
                        $data_documents['image'] = 'http://www.mendeley.com/graphics/common/group_2834463714580773.png';
                        $data_documents['title'] = $element_name;
                        $data_documents['menu'] = 'folder-group-'.$element_id;

                    }
                    $documents = getLibraryValue('GET', $user, $data_documents['uri'], $params);
                    $redirect_data = "id=$cm->id&amp;option=library&amp;action=documents&amp;element_selected=$element_selected&amp;sesskey=" . sesskey();
                    include($CFG->dirroot.'/mod/modmendeley/my_library.php');
                    break;
            }
            break;
        case 'paper':
            $params['consumer_key'] = $user->consumer_key;
            if ($action == 'search'){
                include($CFG->dirroot.'/mod/modmendeley/search.php');
            } else if ($action == 'searching'){
                $search_data = $_GET;
                $show_stats = false;
                include($CFG->dirroot.'/mod/modmendeley/searching.php');
                include($CFG->dirroot.'/mod/modmendeley/paper.php');
            } else {
                $papers = getPublicMethods('GET', $user, rtrim($user->url,'/').'/stats/papers', $params);
                $categories = getPublicMethods('GET', $user, rtrim($user->url,'/').'/documents/categories', $params);
                $publications = getPublicMethods('GET', $user, rtrim($user->url,'/').'/stats/publications', $params);
                $show_stats = true;
                include($CFG->dirroot.'/mod/modmendeley/paper.php');
            }
            break;
        case 'group':
            if ($action == 'add'){
                include($CFG->dirroot.'/mod/modmendeley/create_group.php');
            }elseif ($action == 'savegroup'){
                $fields = array('name'=>$_POST['name'], 'type'=>$_POST['privacy-state']);
                $group_id = postLibraryValue('POST', $user, '/library/groups', array('group' => json_encode($fields)));
                if($group_id != null){
                    redirect($CFG->wwwroot."/mod/modmendeley/view.php?id=$cm->id&amp;option=group&amp;sesskey=" . sesskey());
                }
            }else {
                $show_user_group = false;
                if($modmendeley->private){
                    $user_groups = getLibraryValue('GET', $user, '/library/groups/');
                    if(!empty($user_groups)){
                        $show_user_group = true;
                    }
                }
                $params['consumer_key'] = $user->consumer_key;
                if(!$show_user_group){
                    $groups = getPublicMethods('GET', $user, rtrim($user->url,'/').'/documents/groups', $params);
                }
                $show_stats = true;
                $categories = getPublicMethods('GET', $user, rtrim($user->url,'/').'/documents/categories', $params);
                include($CFG->dirroot.'/mod/modmendeley/group.php');
            }
            break;
        case 'people':
            if($modmendeley->private){
            }
            switch ($action){
                case 'contacts':
                    $contacts= getLibraryValue('GET', $user, '/profiles/contacts/');
                    include($CFG->dirroot.'/mod/modmendeley/people.php');
                    break;
                case 'profile':
                    if($element_id == 0){
                        $profile_info = getLibraryValue('GET', $user, '/profiles/info/me');
                    }else {
                        $profile_info = getLibraryValue('GET', $user, '/profiles/info/'.$element_id);
                    }
                    include($CFG->dirroot.'/mod/modmendeley/profile.php');
                    break;
                default:
                    break;
            }
            break;
        case 'stats':
            if($action == 'library'){
                $authors = getLibraryValue('GET', $user, '/library/authors');
                $publications = getLibraryValue('GET', $user, '/library/publications');
                $tags = getLibraryValue('GET', $user, '/library/tags');
                $library = getLibraryValue('GET', $user, '/library');
                include($CFG->dirroot.'/mod/modmendeley/stats_library.php');
            }else{
                $params['consumer_key'] = $user->consumer_key;
                $articles = getPublicMethods('GET', $user, rtrim($user->url,'/').'/stats/papers', $params);
                $authors = getPublicMethods('GET', $user, rtrim($user->url,'/').'/stats/authors', $params);
                $publications = getPublicMethods('GET', $user, rtrim($user->url,'/').'/stats/publications', $params);
                $tags = getPublicMethods('GET', $user, rtrim($user->url,'/').'/stats/tags/6', $params);
                $params['discipline'] = '6';
                $articles_discipline = getPublicMethods('GET', $user, rtrim($user->url,'/').'/stats/papers', $params);
                $authors_discipline = getPublicMethods('GET', $user, rtrim($user->url,'/').'/stats/authors', $params);
                $publications_discipline = getPublicMethods('GET', $user, rtrim($user->url,'/').'/stats/publications', $params);
                include($CFG->dirroot.'/mod/modmendeley/stats.php');
            }
            break;
        default :
            break;
    }
}
// Finish the page
echo $OUTPUT->footer();
 ?>

<script>
    selectMenuOption(document.getElementById('<?php echo $element_selected ?>'));
</script>