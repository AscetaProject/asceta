<?php

require_once("../../config.php");
require_once($CFG->libdir.'/adminlib.php');
require_once("lib.php");
require_once("locallib.php");

$id   = optional_param('id', '', PARAM_INT);

$url = new moodle_url('/mod/hydra/data.php', array('id'=>$id));
$PAGE->set_url($url);
global $DB;

admin_externalpage_setup('managemodules'); // this is hacky, tehre should be a special hidden page for it

$form = data_submitted();

if ($id != '') {
    $data = $DB->get_records("hydra_api", array('id'=>$id));
}


// --------------------------  SAVE NEW API URL ----------------------------------------------------------------
if ($form) {
	if (!strlen($form->api_url)) {
        echo print_string('api_url_empty', 'hydra');
        die;
    } else {
    $dataobject = array();
    $dataobject['apiurl'] = $form->api_url;
    if ($id != ''){
        $dataobject['id'] = $form->id;
        $DB->update_record('hydra_api', $dataobject, false, false);
    } else {
        $DB->insert_record('hydra_api', $dataobject, false, false);
    }
    redirect("$CFG->wwwroot/$CFG->admin/settings.php?section=modsettinghydra#hydra_header");
    die;
    }
}

$strmodulename = get_string("modulename", "hydra");
?>


<?php 
echo $OUTPUT->header();
echo $OUTPUT->heading($strmodulename . ': ' . get_string("new_url","hydra"));
echo '<form name="dataform" method="post" action="data.php" onsubmit="return dataform_validation();" id="dataform">';
echo '<table width="60%" align="center" class="generalbox">';
?>
<tr>
    <td><label for="api_url">API URL</label></td>
    <td><input name="api_url" id="user_url" size="60" <?php  echo "value='".$data[$id]->apiurl."'"; ?> /></td>
</tr>
<tr>
    <td colspan="3" align="left">
<input type="submit" value="<?php print_string("savechanges") ?>" />
    </td>
</tr>
<input type="hidden" name="id"    value="<?php p($id) ?>" />
<?php
echo '</table>';
echo '</form>';
echo $OUTPUT->footer();
?>

