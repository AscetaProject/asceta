<?php
/**
* Display add folder form
*
*
 * @package   mod_modmendeley
 * @copyright 2011 María del Mar Jiménez Torres (mjimenez@fidesol.org) - Fundación I+D del Software Libre (www.fidesol.org)
 * @license   http://www.apache.org/licenses/LICENSE-2.0.html Apache License, Version 2.0
*/

echo "<div id='library-documents' class='left' style='width: 660px;'>\n";
echo "  <div id='collections_add_show' style='display: none; '>\n";
echo "      <img class='icon edit' src='/graphics/common/spacer.gif'>\n";
echo "  </div>\n";
echo "  <div id='collections_add_edit' style=''>\n";
echo "      <div id='collection_add' class='collection_add'>\n";
echo "          <form name='collection_add' method='post' action='view.php?id=$id&amp;option=library&amp;action=save_folder&amp;element_selected=$element_selected&amp;sesskey=" . sesskey()."' enctype='multipart/form-data' onsubmit=''>\n";
echo "              <input type='hidden' name='csrf_token' value='e2a254accdcc3fa3d4daf85248d75fa491c05d7e'>\n";
echo "              <table cellpadding='0' cellspacing='0' class='edit_table'>\n";
echo "                  <tbody>\n";
echo "                      <tr><td class='edit_title' colspan='2'>Create Folder</td></tr>\n";
echo "                      <tr><td class='spacer' colspan='2'></td></tr>\n";
echo "                      <tr>\n";
echo "                          <td class='document_edit_label'>\n";
echo "                              <label for='library-collection-name'>Name</label>\n";
echo "                          </td>\n";
echo "                          <td class='document_edit_field'>\n";
echo "                              <input id='library-collection-name' name='collection_name' type='text'>\n";
echo "                          </td>\n";
echo "                      </tr>\n";
echo "                      <tr id='collection_description'>\n";
echo "                          <td class='document_edit_label'>\n";
echo "                              <label for='library-collection-description'>Description</label>\n";
echo "                          </td>\n";
echo "                          <td class='document_edit_field'>\n";
echo "                              <textarea id='library-collection-description' name='collection_description' cols='40' rows='4' style='width:300px'></textarea>\n";
echo "                          </td>\n";
echo "                      </tr>\n";
echo "                  </tbody>\n";
echo "              </table>\n";
echo "          </form>\n";
echo "      </div>\n";
echo "      <div class='buttons'>\n";
echo "          <div class='loader'></div>\n";
echo "          <a class='save_button' href='#' onclick='' tabindex='1'><img border='0' onclick=\"if(formValidation()) submitForm()\" title='' alt='Save' src='http://www.mendeley.com/graphics/common/button_save.gif' class='save_button'></a>\n";
echo "          <a class='cancel_button' href='#' onclick='' tabindex='1'><img border='0' onclick='window.history.go(-2)' title='' alt='Cancel' src='http://www.mendeley.com/graphics/common/button_cancel.gif' class='cancel_button'></a>\n";
echo "      </div>\n";
echo "  </div>\n";
echo "</div>\n";
?>
<script>
function submitForm(){
    document.collection_add.submit();
}
function formValidation() {
    if (document.collection_add.collection_name.value.length == 0) {
        alert('name can not be empty');
        document.publicationadd_edit.collection_name.focus();
        return false;
    }
    return true;
}
</script>
