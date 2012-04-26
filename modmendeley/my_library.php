<?php
/**
* Display My Library view
*
*
 * @package   mod_modmendeley
 * @copyright 2011 María del Mar Jiménez Torres (mjimenez@fidesol.org) - Fundación I+D del Software Libre (www.fidesol.org)
 \* @license   http://www.apache.org/licenses/LICENSE-2.0.html Apache License, Version 2.0
*/

?>
<script>
function checkActivityPermission(){
    var create_document = '<?php echo $modmendeley->permission_create_document ?>';
    var delete_document= '<?php echo $modmendeley->permission_delete_document ?>';
    var new_folder = '<?php echo $modmendeley->permission_new_folder ?>';
    var delete_folder = '<?php echo $modmendeley->permission_delete_folder ?>';
    var add_doc_folder = '<?php echo $modmendeley->permission_add_doc_folder ?>';
    var delete_doc_folder = '<?php echo $modmendeley->permission_delete_doc_folder ?>';
    var new_group = '<?php echo $modmendeley->permission_new_group ?>';
    var delete_group = '<?php echo $modmendeley->permission_delete_group ?>';
    var element_selected = '<?php echo $element_selected ?>';

    if(create_document == '0') document.getElementById('toolbar-add-document').classList.add('disabled-icon');
    if(delete_document == '0') document.getElementById('toolbar-delete-document').classList.add('disabled-icon');
    if(new_folder == '0') document.getElementById('toolbar-create-collection').classList.add('disabled-icon');
    if(delete_folder == '0') document.getElementById('toolbar-remove-folder').classList.add('disabled-icon');
    if(add_doc_folder == '0'){
        if (element_selected != 'folder-profile-all'){
            document.getElementById('toolbar-add-document').classList.add('disabled-icon');
        }
        if (document.getElementById('add-to-group-select') != null)
            document.getElementById('add-to-group-select').disabled = true;
    }
    if(delete_doc_folder == '0') document.getElementById('toolbar-remove-document').classList.add('disabled-icon');
    if(new_group == '0') document.getElementById('toolbar-create-group').classList.add('disabled-icon');
    if(delete_group == '0') document.getElementById('toolbar-remove-group').classList.add('disabled-icon');

}
</script>
<?php
echo "<div id='content-container'>\n";
echo "  <div id='main-content'>\n";
echo "      <div id='publications'>\n";
echo "          <div id='main-library-toolbar' style='text-align:center; padding-bottom:4px;'>\n";
echo "              <div onclick='window.location=\"view.php?id=$cm->id&amp;option=library&amp;action=add&amp;element_selected=$element_selected&amp;sesskey=" . sesskey() . "\"' class='library-toolbar-icon' id='toolbar-add-document' style='display: block; '>\n";
echo "                  <img src='https://www.mendeley.com/graphics/32x32-icons/document-new_2429099784913441.png' alt='Add Document' width='32' height='32'><br>\n";
echo "                  <span>Add<br>Document</span>\n";
echo "              </div>\n";
echo "              <div onclick='' class='library-toolbar-icon disabled-icon' id='toolbar-empty-trash' style='display: none; '>\n";
echo "                  <img src='https://www.mendeley.com/graphics/32x32-icons/empty-trash_1897675351204627.png' alt='Empty Trash' width='32' height='32'><br>\n";
echo "                  <span>Empty<br>Trash</span>\n";
echo "              </div>\n";
echo "              <div onclick='if(this.classList.length < 2){ window.location=\"view.php?id=$cm->id&amp;option=library&amp;action=dialog&amp;element_selected=$element_selected&amp;type=document&amp;documents_id=\"+getDocumentsChecked()+\"&amp;sesskey=" . sesskey() . "\";}' class='library-toolbar-icon disabled-icon' id='toolbar-delete-document' style='display: block; '>\n";
echo "  		<img src='https://www.mendeley.com/graphics/32x32-icons/document-remove_7943009072385789.png' alt='Delete Documents' width='32' height='32'><br>\n";
echo "  		<span>Delete<br>Documents</span>\n";
echo "              </div>\n";
echo "              <div onclick='' class='library-toolbar-icon' id='toolbar-restore-document' style='display: none; '>\n";
echo "  		<img src='https://www.mendeley.com/graphics/32x32-icons/document-restore_1500467146515178.png' alt='Restore Documents' width='32' height='32'><br>\n";
echo "  		<span>Restore<br>Documents</span>\n";
echo "              </div>\n";
echo "              <div onclick='if(this.classList.length < 2){ window.location=\"view.php?id=$cm->id&amp;option=library&amp;action=dialog&amp;element_selected=$element_selected&amp;type=folderdocument&amp;element_id=\"+getFolderSelected()+\"&amp;documents_id=\"+getDocumentsChecked()+\"&amp;sesskey=" . sesskey() . "\";}' class='library-toolbar-icon disabled-icon' id='toolbar-remove-document' style='display: block; '>\n";
echo "              	<img src='https://www.mendeley.com/graphics/32x32-icons/document_remove_from_collection_7911637889654408.png' alt='Remove Document from Folder' width='32' height='32'><br>\n";
echo "              	<span>Remove from<br>Folder</span>\n";
echo "              </div>\n";
echo "              <div onclick='window.location=\"view.php?id=$cm->id&amp;option=library&amp;action=createfolder&amp;element_selected=$element_selected&amp;sesskey=" . sesskey() . "\"' class='library-toolbar-icon' id='toolbar-create-collection' style='display: block; '>\n";
echo "                      <img src='https://www.mendeley.com/graphics/32x32-icons/group-new_1455927467811945.png' alt='Create Folder' width='32' height='32'><br>\n";
echo "                      <span>Create<br>Folder</span>\n";
echo "              </div>\n";
echo "              <div onclick='window.location=\"view.php?id=$cm->id&amp;option=group&amp;action=add&amp;element_selected=$element_selected&amp;sesskey=" . sesskey()."\"' class='library-toolbar-icon' id='toolbar-create-group' style='display: block; '>\n";
echo "                      <img src='https://www.mendeley.com/graphics/32x32-icons/group-create_7591776616040563.png' alt='Create Group' width='32' height='32'><br>\n";
echo "                      <span>Create<br>Group</span>\n";
echo "              </div>\n";
echo "              <div onclick='if(this.classList.length < 2){ window.location=\"view.php?id=$cm->id&amp;option=library&amp;action=dialog&amp;element_selected=$element_selected&amp;type=folder&amp;element_id=\"+getFolderSelected()+\"&amp;sesskey=" . sesskey() . "\";}' class='library-toolbar-icon disabled-icon' id='toolbar-remove-folder' style='display: block; '>\n";
echo "                      <img src='https://www.mendeley.com/graphics/32x32-icons/group-remove_2868771543177372.png' alt='Remove Collection' width='32' height='32'><br>\n";
echo "                      <span>Remove<br>Folder</span>\n";
echo "              </div>\n";
echo "              <div onclick='if(this.classList.length < 2){ window.location=\"view.php?id=$cm->id&amp;option=library&amp;action=dialog&amp;element_selected=$element_selected&amp;type=group&amp;element_id=\"+getGroupSelected()+\":&amp;sesskey=" . sesskey() . "\";}' class='library-toolbar-icon' id='toolbar-remove-group' style='display: none; '>\n";
echo "                      <img src='https://www.mendeley.com/graphics/32x32-icons/group-remove_2868771543177372.png' alt='Remove Collection' width='32' height='32'><br>\n";
echo "                      <span>Remove<br>Group</span>\n";
echo "              </div>\n";
echo "              <div onclick='' class='library-toolbar-icon' id='toolbar-unfollow-group' style='display: none; '>\n";
echo "                      <img src='https://www.mendeley.com/graphics/32x32-icons/group-unfollow_2388632288751457.png' alt='Remove Collection' width='32' height='32'><br>\n";
echo "                      <span>Unfollow<br>Group</span>\n";
echo "              </div>\n";
echo "              <div onclick='window.open(\"http://www.mendeley.com/import/\")' target='_blank' class='library-toolbar-icon' id='toolbar-web-importer' style='display: block; '>\n";
echo "                      <img src='https://www.mendeley.com/graphics/32x32-icons/document-importer-add_4079890253702532.png' alt='Web Importer' width='32' height='32'><br>\n";
echo "                      <span>Web<br>Importer</span>\n";
echo "              </div>\n";
echo "              <div onclick='window.open(\"http://www.mendeley.com/library/\")' target='_blank' class='library-toolbar-icon disabled-icon' id='toolbar-account-usage' style='display: block; '>\n";
echo "                      <img src='https://www.mendeley.com/graphics/32x32-icons/document-account-usage_2740011126369223.png' alt='Account Usage' width='32' height='32'><br>\n";
echo "                      <span>Account<br>Usage</span>\n";
echo "              </div>\n";
echo "              <div class='library-search' style='display:none'>\n";
echo "                  <form onsubmit=''>\n";
echo "                  <input id='librarySearchQuery'>\n";
echo "                  <input type='submit' value='Search'>\n";
echo "                  </form>\n";
echo "              </div>\n";
echo "              <div class='clear'></div>\n";
echo "          </div>\n";
echo "          <div id='library-action-notification'>\n";
echo "          	<div id='documents-added' style='border-top-left-radius: 4px 4px; border-top-right-radius: 4px 4px; border-bottom-left-radius: 4px 4px; border-bottom-right-radius: 4px 4px; display: none; '>Document(s) successfully added to collection</div>\n";
echo "          </div>\n";
echo "<table style='width:100%'><tr><td style='width:5%; vertical-align:top; padding:15px;'>\n"; //TABLE
echo "          <div id='library-menu' class='left' style='width: 215px; margin-top: 4px;'>\n";
echo "              <h4 class='top' id='my-library-heading'><a href='#' onclick='return false;' title='Your personal library is frozen'><img src='https://www.mendeley.com/graphics/commonnew/exclamation_2198924773340602.png' alt='Frozen group' height='16' width='16' class='frozen-icon' id='my-library-frozen-icon' style='display: none; '></a>My Library</h4>\n";
echo "              <div id='library-profile-menu'>\n";
echo "                  <div id='folder-profile-all' class='library-group folder-all-documents' onclick='checkMenuClicked(this);selectMenuOption(this);window.location=\"view.php?id=$cm->id&amp;option=library&amp;action=documents&amp;element_selected=\"+this.id+\"&amp;sesskey=" . sesskey()."\"';>\n";
echo "                      <img src='http://www.mendeley.com/graphics/common/folder-all-documents_1437443173056970.png' alt='All Documents' height='16' width='16' class='folder-icon'><span class='folder-name'>All Documents</span>\n";
echo "                  </div>\n";
/*echo "                  <div id='folder-profile-recent' class='library-group folder-recently-added' onclick=''>\n";
echo "                      <img src='https://www.mendeley.com/graphics/common/folder-recently-added_8401285208557136.png' alt='Recently Added' height='16' width='16' class='folder-icon'><span class='folder-name'>Recently Added</span>\n";
echo "                  </div>\n";
echo "                  <div id='folder-profile-starred' class='library-group folder-starred' onclick=''>\n";
echo "                      <img src='https://www.mendeley.com/graphics/common/star-filled_1001750051244996.png' alt='Favorites' height='16' width='16' class='folder-icon'><span class='folder-name'>Favorites</span>\n";
echo "                  </div>\n";
echo "                  <div id='folder-profile-unconfirmed' class='library-group folder-unconfirmed' onclick=''>\n";
echo "                      <img src='https://www.mendeley.com/graphics/common/confirm-metadata_1937381403660675.png' alt='Needs Review' height='16' width='16' class='folder-icon'><span class='folder-name'>Needs Review</span>\n";
echo "                  </div>\n";*/
echo "                  <div id='folder-profile-authored' class='library-group folder-my-publications' onclick='checkMenuClicked(this);selectMenuOption(this);window.location=\"view.php?id=$cm->id&amp;option=library&amp;action=publications&amp;element_selected=\"+this.id+\"&amp;sesskey=" . sesskey()."\"'>\n";
echo "                      <img src='http://www.mendeley.com/graphics/common/folder-my-publications_1484555991348322.png' alt='My Publications' height='16' width='16' class='folder-icon'><span class='folder-name'>My Publications</span>\n";
echo "                  </div>\n";
/*echo "                  <div id='folder-profile-ungrouped' class='library-group folder-ungrouped' onclick=''>\n";
echo "                      <img src='https://www.mendeley.com/graphics/common/folder-ungrouped_3676601721788085.png' alt='Unsorted' height='16' width='16' class='folder-icon'><span class='folder-name'>Unsorted</span>\n";
echo "                  </div>\n";*/
foreach($folders as $folder){
echo "                  <div id='folder-profile-".$folder->id."' class='library-group' onclick='checkMenuClicked(this);selectMenuOption(this);window.location=\"view.php?id=$cm->id&amp;option=library&amp;action=folders&amp;element_selected=\"+this.id+\"&amp;element_id=$folder->id&amp;element_name=$folder->name&amp;sesskey=" . sesskey()."\"'>\n";
echo "                      <img src='https://www.mendeley.com/graphics/common/folder-private_6173323349516461.png' alt='Private Collection' height='16' width='16' class='folder-icon'><span class='folder-name'>".$folder->name."</span>\n";
echo "                  </div>\n";
}
echo "              </div>\n";
echo "              <br>\n";
echo "              <h4>Groups</h4>\n";
echo "              <div id='library-group-menu'>\n";
if(empty($groups)){
    echo "                  <span class='text-minor'>No groups yet. <br>Find new <a href='/groups/'>groups</a>.</span>\n";
}else{
    foreach($groups as $group){
        echo "<div id='folder-group-".$group->id."' class='library-group' onclick='checkMenuClicked(this);selectMenuOption(this);window.location=\"view.php?id=$cm->id&amp;option=library&amp;action=groups&amp;element_selected=\"+this.id+\"&amp;element_id=$group->id&amp;element_name=$group->name&amp;sesskey=" . sesskey()."\"'>";
        echo "  <a href='#' onclick='window.location=\"view.php?id=$cm->id&amp;option=library&amp;action=groups&amp;element_id=$group->id&amp;element_name=$group->name&amp;element_selected=\"+this.id+\"&amp;sesskey=" . sesskey()."\"' title='This group is frozen'><img src='https://www.mendeley.com/graphics/commonnew/exclamation_2198924773340602.png' alt='Frozen group' height='16' width='16' class='frozen-icon' style='display: none; '></a>";
        echo "  <span class='lock-icon' ".(($group->type == 'public')? 'style="display:none;"' : '')."></span>";
        echo "  <img src='https://www.mendeley.com/graphics/common/group.png' alt='Group' height='16' width='16' class='folder-icon'>";
        echo "  <span class='folder-name'>".$group->name."</span>";
        echo "</div>";
    }
}
echo "              </div>\n";
echo "              <br>\n";
echo "</td><td style='width:45%; vertical-align:top; padding:15px;'>\n"; //TABLE
//VIEW WITH ALL ACTIONS
switch ($action){
    case 'folders':
        $data_documents['title'] = $documents->folder_name;
        include($CFG->dirroot.'/mod/modmendeley/library_documents.php');
        break;
    case 'groups':
        $data_documents['title'] = $documents->group_name;
        include($CFG->dirroot.'/mod/modmendeley/library_documents.php');
        break;
    case 'documents':
    case 'publications':
        include($CFG->dirroot.'/mod/modmendeley/library_documents.php');
        break;
    case 'add':
        include($CFG->dirroot.'/mod/modmendeley/add_document.php');
        break;
    case 'dialog':
        switch ($type){
            case 'document':
                $data_documents['title'] = 'Delete Document';
                $data_documents['url'] = 'window.location="view.php?id='.$cm->id.'&amp;option=library&amp;action=deletedocument&amp;documents_id='.$_GET['documents_id'].'&amp;element_selected='.$element_selected.'&amp;sesskey=' . sesskey().'"';
                $data_documents['text'] = 'Move the selected document to the Trash? (Documents will remain in the Trash until you delete them permanently)';
                $data_documents['button_text'] = 'Delete Document';
                break;
            case 'folder':
                $data_documents['title'] = 'Remove Collection';
                $data_documents['url'] = 'window.location="view.php?id='.$cm->id.'&amp;option=library&amp;action=deletefolder&amp;element_id='.$element_id.'&amp;element_selected='.$element_selected.'&amp;sesskey=' . sesskey() . '"';
                $data_documents['text'] = 'Are you sure you wish to remove this collection?';
                $data_documents['button_text'] = 'Remove Collection';
                break;
            case 'folderdocument':
                $data_documents['title'] = 'Remove Documents from Collection';
                $data_documents['url'] = 'window.location="view.php?id='.$cm->id.'&amp;option=library&amp;action=deletedocumentfolder&amp;element_id='.$element_id.'&amp;documents_id='.$_GET['documents_id'].'&amp;element_selected='.$element_selected.'&amp;sesskey=' . sesskey() . '"';
                $data_documents['text'] = 'Remove the '.count($_GET['documents_id']).' selected documents from this collection?';
                $data_documents['button_text'] = 'Remove Document';
                break;
            case 'group':
                $data_documents['title'] = 'Delete '.$element_name.'?';
                $data_documents['url'] = 'window.location="view.php?id='.$cm->id.'&amp;option=library&amp;action=deletegroup&amp;element_id='.$element_id.'&amp;element_selected='.$element_selected.'&amp;sesskey=' . sesskey() . '"';
                $data_documents['text'] = 'This action cannot be undone, and the group will be permanently deleted. All other members may lose access to these documents. ';
                $data_documents['button_text'] = 'Delete forever';
                break;
        }
        include($CFG->dirroot.'/mod/modmendeley/delete_dialog.php');
        break;
    case 'adddialog':
        include($CFG->dirroot.'/mod/modmendeley/add_dialog.php');
        break;
    case 'createfolder':
        include($CFG->dirroot.'/mod/modmendeley/create_folder.php');
        break;
    case 'creategroup':
        include($CFG->dirroot.'/mod/modmendeley/create_group.php');
        break;
}
echo "</td></tr></table>\n"; //TABLE
echo "</div>\n";
echo "<div class='clear'></div>\n";
echo "</div>\n";
echo "</div>\n";

?>
<script>
if('<?php echo $action ?>' == 'groups'){
    document.getElementById('toolbar-remove-group').style.display = 'block';
    document.getElementById('toolbar-remove-folder').style.display = 'none';
}
if('<?php echo $action ?>' == 'folders' && ('<?php echo $data_documents['menu'] ?>' != 'folder-profile-all' || '<?php echo $data_documents['menu'] ?>' != 'folder-profile-authored')){
        document.getElementById('toolbar-remove-folder').classList.remove('disabled-icon');
}
checkActivityPermission();
function showDialog(){
    document.getElementById('interVeil').style.position="absolute"
    document.getElementById('interVeil').style.width=document.getElementById('main-content').getBoundingClientRect().width+"px" //set up veil over page
    document.getElementById('interVeil').style.height=document.getElementById('main-content').getBoundingClientRect().height+"px" //set up veil over page
    document.getElementById('interVeil').style.left=document.getElementById('main-content').getBoundingClientRect().left-4+"px" //Position veil over page
    document.getElementById('interVeil').style.top=document.getElementById('maincontent').getBoundingClientRect().top+3+"px"  //Position veil over page
    document.getElementById('interVeil').style.visibility="visible" //Show veil over page
    document.getElementById('interVeil').style.backgroundColor="gray"
    document.getElementById('interVeil').style.opacity=0.8
}
function getDocumentsChecked(){
  list = new Array();
  i = 0;
  index = 0;
  while (index < document.getElementsByClassName('document-checkbox').length){
      if (document.getElementsByClassName('document-checkbox')[index].firstChild.nextSibling.checked){
        list[i] = document.getElementsByClassName('document-checkbox')[index].firstChild.nextSibling.value;
        i++;
      }
      index++;
  }
  return list;
}

function getFolderSelected(){
    return document.getElementById('library-profile-menu').getElementsByClassName('library-group-selected')[0].id.split('-')[2];
}
function getGroupSelected(){
    return document.getElementById('library-group-menu').getElementsByClassName('library-group-selected')[0].id.split('-')[2];
}
function checkMenuClicked(element){
    result = element.id.split('-');
    if(result.length != 0){
        if (result[1] == 'profile'){
            if (result[2] != 'all' && result[2] != 'authored'){
                document.getElementById('toolbar-remove-folder').classList.remove('disabled-icon');
            } else {
                document.getElementById('toolbar-remove-folder').classList.add('disabled-icon');
            }
        }else{
            
        }
    }
}
function selectMenuOption(element){
    index = 0;
    while(index < document.getElementById('library-profile-menu').getElementsByClassName('library-group-selected').length){
        document.getElementById('library-profile-menu').getElementsByClassName('library-group-selected')[index].classList.remove('library-group-selected');
        index++;
    }
    index = 0;
    while(index < document.getElementById('library-group-menu').getElementsByClassName('library-group-selected').length){
        document.getElementById('library-group-menu').getElementsByClassName('library-group-selected')[index].classList.remove('library-group-selected');
        index++;
    }
    element.classList.add('library-group-selected');
}
</script>