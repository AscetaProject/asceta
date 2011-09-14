<?php
/**
* Display add document form
*
*
 * @package   mod_modmendeley
 * @copyright 2011 María del Mar Jiménez Torres (mjimenez@fidesol.org) - Fundación I+D del Software Libre (www.fidesol.org)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/
include($CFG->dirroot.'/mod/modmendeley/types.php');

echo "<div id='library-documents' class='left' style='width: 660px;'>\n";
echo "  <div id='publications_info_add_show' style='display: none; '>\n";
echo "      <img class='icon edit' src='http://www.mendeley.com/graphics/common/spacer.gif'>\n";
echo "  </div>\n";
echo "  <div id='publications_info_add_edit' style=''>\n";
echo "      <div id='publicationadd_edit' class='publication_edit'>\n";
echo "          <form name='publicationadd_edit' method='post' action='view.php?id=$id&amp;option=library&amp;action=save_document&amp;element_selected=$element_selected&amp;sesskey=" . sesskey()."' enctype='multipart/form-data' onsubmit=''>\n";
echo "              <input type='hidden' name='csrf_token' value='86d066e7903fa1824b799f63d77276616a883ec6'>\n";
echo "              <input type='hidden' name='pid' value='add'>\n";
echo "              <input type='hidden' name='groupId' value=''>\n";
echo "              <table cellpadding='0' cellspacing='0' class='edit_table'>\n";
echo "                  <tbody>\n";
echo "                      <tr><td class='edit_title' colspan='2'>Add New Document</td></tr>\n";
echo "                      <tr><td class='spacer' colspan='2'></td></tr>\n";
echo "                      <tr>\n";
echo "                          <td class='document_edit_label'><strong>Add to</strong></td>\n";
echo "                          <td class='document_edit_field'>\n";
echo "                              <select class='dropdown library-group-select' id='add-to-group-select' style='width: 200px;' name='add_to' tabindex='1'>\n";
echo "                                  <optgroup label='My Library' id='add-to-group-select-optgroup-my-lib'>\n";
echo "                                      <option class='select-profile-all' value='folder-profile-all'"; if($element_selected == 'folder-profile-all') echo "selected='selected'"; echo ">All Documents</option>\n";
foreach ($folders as $folder){
    echo "                                      <option class='select-profile-private' value='folder-profile-$folder->id' "; if($element_selected == 'folder-profile-'.$folder->id) echo "selected='selected'"; echo ">$folder->name</option>\n";
}
echo "                                  </optgroup>\n";
echo "                                  <optgroup label='Groups' id='add-to-group-select-optgroup-shared'>\n";
foreach ($groups as $group){
    echo "                                      <option class='select-group-al' value='folder-group-$group->id' "; if ($element_selected == 'folder-group-'.$group->id) echo "selected='selected'"; echo ">$group->name</option>\n";
}
echo "                                  </optgroup>\n";

echo "                              </select>\n";
echo "                          </td>\n";
echo "                      </tr>\n";
echo "                      <tr>\n";
echo "                          <td class='document_edit_label'>Type</td>\n";
echo "                          <td class='document_edit_field'>\n";
echo "                              <div class='right' style='padding-right:10px;'>\n";
echo "                                  <span id='additional-fields-add-link' class='arrowlink' onclick=\"showAdditionalFields(document.getElementById('pub_type').selectedIndex-1);\" style='display:none;'>\n";
echo "                                      <img src='http://www.mendeley.com/graphics/commonnew/list-arrow-right_3884326895063066.gif' alt='&lt;' width='12' height='15'><span>Additional Fields</span>\n";
echo "                                  </span>\n";
echo "                              </div>\n";
echo "                              <select class='dropdown' id='pub_type' name='pub_type' onchange=\"showFields()\" tabindex='1'>\n";
echo "                                  <option value=''>Select the type:</option>\n";
foreach (getDocumentsType() as $type){
    echo "                                  <option value='".str_replace(" ", "_", $type)."'>$type</option>\n";
}
echo "                              </select>\n";
echo "                              <input type='hidden' id='type_add' name='type_add' value=''>\n";
echo "                          </td>\n";
echo "                      </tr>\n";
echo "                      <tr>\n";
echo "                          <td colspan='2'>\n";
foreach (getDocumentsType() as $type){
    $type = str_replace(" ", "_", $type);
    echo "                              <div id='publication_".$type."_add' class='publication_fields' style='display:none;'>\n";
    echo "                              <table class='visible' cellpadding='0' cellspacing='0'>\n";
    echo "                                  <tbody>\n";
    foreach($document_type[strtolower($type)]['Primary'] as $primary_field){
        echo "                                      <tr>\n";
        echo str_replace('VARIABLE', $type, constant(strtoupper($primary_field)));
        echo "                                      </tr>\n";
    }
    echo "                                  </tbody>\n";
    echo "                              </table>\n";
    echo "                              <div class='hiddens scrollpane'>\n";
    echo "                                  <table cellpadding='0' cellspacing='0'>\n";
    echo "                                      <tbody>\n";
    foreach($document_type[strtolower($type)]['Additional'] as $additional_field){
        echo "                                          <tr>\n";
        echo str_replace('VARIABLE', $type, constant(strtoupper($additional_field)));
        echo "                                          </tr>\n";
    }
    echo "                                      </tbody>\n";
    echo "                                  </table>\n";
    echo "                              </div>\n";
    echo "                            </div>\n";
}
echo "                          </td>\n";
echo "                       </tr>\n";
echo "                  </tbody>\n";
echo "              </table>\n";
echo "          </form>\n";
echo "      </div>\n";
echo "      <div class='buttons'>\n";
echo "          <div class='loader'></div>\n";
echo "          <a class='save_button' href='#' onclick=\"if(formValidation(document.getElementById('pub_type').selectedIndex)) submitForm()\" tabindex='1'>\n";
echo "              <img border='0' onclick='' title='' alt='Save' src='http://www.mendeley.com/graphics/common/button_save.gif' class='save_button'>\n";
echo "          </a>\n";
echo "          <a class='cancel_button' href='#' onclick='window.history.go(-2)' tabindex='1'>\n";
echo "              <img border='0' onclick='' title='' alt='Cancel' src='http://www.mendeley.com/graphics/common/button_cancel.gif' class='cancel_button'>\n";
echo "          </a>\n";
echo "      </div>\n";
echo "   </div>\n";
echo "</div>\n";
?>

<script>
function submitForm(){
  document.publicationadd_edit.submit();
}
function formValidation(index) {
  if (document.publicationadd_edit.pub_type.value.length == 0) {
      alert('type can not be empty');
      document.publicationadd_edit.pub_type.focus();
      return false;
  }
  var action = 'document.publicationadd_edit.post_title_'+document.getElementById('pub_type')[index].value;
  if (eval(action).value.length == 0) {
      alert('title can not be empty');
      eval(action).focus();
      return false;
  }
  return true;
}
function showFields(){
  hideAlls();
  var id = 'publication_'+document.getElementById('pub_type').value+'_add';
  if(document.getElementById(id) != null){
      document.getElementById(id).style.display = 'block';
      document.getElementById('additional-fields-add-link').style.display = 'block';
  }
}
function hideAlls(){
  var type = new Array('Bill','Book','Book Section','Case','Computer Program','Conference Proceedings','Encyclopedia Article','Film','Generic','Hearing','Journal Article','Magazine Article','Newspaper Article','Patent','Report','Statute','Television Broadcast','Thesis','Web Page','Working Paper');
  for ( x in type){
      id = 'publication_'+type[x].replace(' ', '_')+'_add';
      if(document.getElementById(id) != null){
          document.getElementById(id).style.display = 'none';
      }
  }
  document.getElementById('additional-fields-add-link').style.display = 'none';
  document.getElementsByClassName('hidden scrollpane')[document.getElementById('pub_type').selectedIndex-1].style.display = 'none';
  document.getElementById('additional-fields-add-link').innerHTML = document.getElementById('additional-fields-add-link').innerHTML.replace('down','right');

}
function showAdditionalFields(index){
  if(document.getElementsByClassName('hidden scrollpane')[index].style.display == 'none'){
      document.getElementsByClassName('hidden scrollpane')[index].style.display = 'block';
      document.getElementById('additional-fields-add-link').innerHTML = document.getElementById('additional-fields-add-link').innerHTML.replace('right','down');
  } else {
      document.getElementsByClassName('hidden scrollpane')[index].style.display = 'none';
      document.getElementById('additional-fields-add-link').innerHTML = document.getElementById('additional-fields-add-link').innerHTML.replace('down','right');
  }
}
</script>