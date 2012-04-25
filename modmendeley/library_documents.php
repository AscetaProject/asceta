<?php
/**
* Display library document list
*
*
 * @package   mod_modmendeley
 * @copyright 2011 María del Mar Jiménez Torres (mjimenez@fidesol.org) - Fundación I+D del Software Libre (www.fidesol.org)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/


echo "      <div id='library-documents' class='left' style='width: 660px;'>";
echo "          <div id='library-title-bar'>";
echo "              <img src='".$data_documents['image']."' height='16' width='16' class='folder-icon' alt='".$data_documents['title']."'>";
echo "              <h3>".$data_documents['title']."</h3>";
echo "              <div class='clear'></div>";
echo "              <div id='library-collection-settings'></div>";
echo "          </div>";
if ($action == 'groups'){
    echo "<div id='view-group-page'><a href='http://www.mendeley.com/groups/".$element_id."/".$element_name."/' target='_blank' title='View group details'>View group details</a></div>";
}
if (empty($documents->document_ids)){
    echo "  <div class='user_message grey' style='padding-top: 100px;'>There are no documents in this collection.</div>";
}else{
    if(false){
        echo "          <div id='library-search-results' style='border-top-left-radius: 5px 5px; border-top-right-radius: 5px 5px; border-bottom-left-radius: 5px 5px; border-bottom-right-radius: 5px 5px; '>";
        echo "              <div id='remove-filter'>";
        echo "                  <a href='#' onclick=''>Clear</a>";
        echo "              </div>";
        echo "              <div>Results for '<strong>how</strong>'</div>";
        echo "          </div>";
    }
    echo "          <div class='library-toolbar'>";
    echo "              <div class='library-auto-select'>";
    echo "                  <select id='auto-select' onchange='changeSelection(this.value);'>";
    echo "                      <option value=''>Select...</option>";
    echo "                      <optgroup>";
    echo "                          <option value='all'>All</option>";
    echo "                          <option value='invert'>Invert</option>";
    echo "                          <option value='none'>None</option>";
    echo "                      </optgroup>";
    echo "                      <optgroup label='&nbsp;'>";
    echo "                          <option value='star-filled'>Favorites</option>";
    echo "                          <option value='unread-document'>Unread</option>";
    echo "                          <option value='read'>Read</option>";
    echo "                      </optgroup>";
    echo "                  </select>";
    echo "              </div>";
    echo "              <div class='library-add-to-group'>";
    echo "                  <select class='library-group-select' id='add-to-group-select' onchange='addSelectedTo(this.value);' disabled='disabled' tabindex='1'>";
    echo "                      <option value='none'>Add selected documents to...</option>";
    echo "                      <optgroup label='My Library' id='add-to-group-select-optgroup-my-lib'>";
    foreach($folders as $folder){
        echo "                          <option class='select-profile-private' value='folder-profile-".$folder->id."'>".$folder->name."</option>";
    }
    echo "                      </optgroup>";
    echo "                  </select>";
    echo "              </div>";
    echo "              <div class='library-pagemenu'>";
    echo "                  <div class='pagemenu'>";
    showPaginationString(intval($documents->current_page)+1, intval($documents->total_pages), 1, $redirect_data, false);
    echo "                  </div>";
    echo "              </div>";
    echo "              <div class='clear'></div>";
    echo "          </div>";
    $c = 0;
    foreach($documents->document_ids as $document){
        if(!empty($documents->group_id)){
            $document_detail = getLibraryValue('GET', $user, '/library/groups/'.$documents->group_id.'/'.$document);
        }else {
            $document_detail = getLibraryValue('GET', $user, '/library/documents/'.$document);
        }
        echo "          <div class='library-document library-document-".(($c = !$c)? 'odd': 'even')."-row' id='library-document-".$document."'>";
        echo "              <a href='#' name='document-".$document."'></a>";
        echo "              <div class='left'>";
        echo "                  <div class='document-checkbox'>";
        echo "                      <input type='checkbox' value='".$document."' id='document-checkbox-".$document."' onclick='checkboxClicked();'>";
        echo "                      <input type='hidden' value='folder-profile-all' id='document-folder-".$document."'>";
        echo "                  </div>";
        echo "                  <div class='document-icons'>";
        echo "                      <a title='Mark as Favorite' class='star-empty' href='#' onclick=''>";
        echo "                          <img src='https://www.mendeley.com/graphics/common/star-empty_7298568672004598.png' alt='star_empty' width='16' height='16'>";
        echo "                      </a>";
        echo "                      <a title='Mark as Read' class='unread-document' href='#' onclick=''>";
        echo "                          <img src='http://www.mendeley.com/graphics/common/unread_1738973333671741.png' alt='unread' width='16' height='16'>";
        echo "                      </a>";
        if($action == 'documents' || $action == 'folders' || $action == 'groups'){
            if(!empty($document_detail->files)){
                echo "                      <a title='Click here to show the document's files' class='pdf' href='#' onclick='showDownloadFile($document)'>";
                echo "                          <img src='https://www.mendeley.com/graphics/common/pdf_3275105525101106.gif' alt='pdf' width='16' height='16'>";
                echo "                      </a>";
            }
        }
        echo "                  </div>";
        echo "              </div>";
        echo "              <div class='document-description'>";
        echo "                  <a class='black' href='#' onclick='showAndHideDownloadFile($document)'>".$document_detail->title."</a><br>";
        $document_authors = array();
        foreach ($document_detail->authors as $v){
            $document_authors [] = "$v->forename $v->surname";
        }
        echo "                  <div> ".implode(',',$document_authors)." ". ((!empty($document_detail->year))? ' ('.$document_detail->year.')': '')." </div>";
        echo "                  <div> <em>".((!empty($document_detail->published_in))?$document_detail->published_in: '')."</em> ".((!empty($document_detail->pages))?' p. '.$document_detail->pages:'')." </div>";
        if(!empty($document_detail->url)){
            echo "                  <div><a rel='nofollow' class='light' href='$document_detail->url'>$document_detail->url</a><br></div>";
        }
        echo "                  <div id='tag-list-".$document."'>";
        echo "                      <div id='document-tags-".$document."'>";
        foreach($document_detail->tags as $tag){
            echo "                          <a href='#' onclick=''>".$tag."</a>	";
        }
        echo "                      </div>";
        echo "                  </div>";
        echo "                  <div id='document-extra-".$document."' class='document-extra'>";
        echo "                      <div id='download-document-".$document."'>";
        echo "                          <a name='download-document-".$document."' href='#'></a> Download: &nbsp;";
        foreach($document_detail->files as $file){
            $profile_main = $profile_info->main;
            echo "                          <a href='http://www.mendeley.com/download/personal/".$profile_main->profile_id."/".$document."/".$file->file_hash."/dl.".$file->file_extension."' target='_blank' class='red'>".$file->file_extension."</a> (".$file->file_size." MB) &nbsp;";
        }
        echo "                      </div>";
        echo "      		<div id='tags-notes-".$document."-extra' class='extra'>";
        echo "                          <a name='tag-edit-".$document."'></a>";
        echo "                          <div class='edit-tags-and-notes' id='edit-tags-and-notes-".$document."'>";
        echo "                          <table cellpadding='0' cellspacing='0' class='edit_table'>";
        echo "                              <tbody>";
        echo "                                  <tr><td class='edit_title'>Edit Tags and Notes</td></tr>";
        echo "                                  <tr><td style='padding-top: 5px;' class='edit_field_text2'>Tags</td></tr>";
        echo "                                  <tr><td class='edit_field_text2'><input id='input-tags-".$document."' name='tags' value='Java; Programacion' tabindex='1' style='width: 400px; ' placeholder='Enter your tags here (Tag 1; Tag 2; ...)'></td></tr>";
        echo "                                  <tr><td class='edit_field_text2'>Notes</td></tr>";
        echo "                                  <tr><td class='edit_field_text2'><div class='uEditor' style=''><ul class='uEditorToolbar'><li><a title='Bold' class='uEditorButtonBold' href='javascript:void(0)'>Bold</a></li><li><a title='Italic' class='uEditorButtonItalic' href='javascript:void(0)'>Italic</a></li><li><a title='Underline' class='uEditorButtonUnderline' href='javascript:void(0)'>Underline</a></li></ul><iframe class='uEditorIframe'></iframe><input type='hidden' name='input-notes-".$document."' value='&lt;p&gt;&lt;/p&gt;'></div></td></tr>";
        echo "                              </tbody>";
        echo "                          </table>";
        echo "                          <div class='buttons'>";
        echo "                              <a class='save_button' href='#' onclick='' tabindex='1'><img src='https://www.mendeley.com/graphics/common/button_save_2573160900840882.gif' onclick='' alt='Save' width='72' height='22'></a>";
        echo "                              <a class='cancel_button' href='#' onclick='' tabindex='1'><img src='https://www.mendeley.com/graphics/common/button_cancel_1538495746221986.gif' onclick='' alt='Cancel' width='72' height='22'></a>";
        echo "                          </div>";
        echo "                      </div>";
        echo "                  </div>";
        echo "                  <div id='document-details-".$document."-extra' class='extra'>";
        echo "                      <a name='publications-info-".$document."'></a>";
        echo "                      <div id='publications_info_".$document."_show'><img class='icon edit' src='https://www.mendeley.com/graphics/common/spacer_4223231388365693.gif' alt='-' width='1' height='1'></div>";
        echo "                  </div>";
        echo "              </div>";
        echo "          </div>";
        echo "          <div class='clear'></div>";
        echo "      </div>";
    }
    echo "<div class='clear'></div>";
    echo "<div style='float: right; padding-top:6px;'>";
    echo "</div>";
}
echo "</div>";
?>
<script>
  checkboxClicked();
  function checkboxClicked(){
      if(isAnyDocumentChecked()){
          document.getElementById('toolbar-delete-document').classList.remove('disabled-icon');
          if(document.getElementById('add-to-group-select') != null)
            document.getElementById('add-to-group-select').disabled = false;
      }else{
          document.getElementById('toolbar-delete-document').classList.add('disabled-icon');
          document.getElementById('toolbar-remove-document').classList.add('disabled-icon');
          if(document.getElementById('add-to-group-select') != null)
            document.getElementById('add-to-group-select').disabled = true;

      }
      if ('<?php echo $action ?>' == 'folders' && isAnyDocumentChecked()){
          document.getElementById('toolbar-remove-document').classList.remove('disabled-icon');
      } else {
          document.getElementById('toolbar-remove-document').classList.add('disabled-icon');
      }
      this.checkActivityPermission();

  }
  function isAnyDocumentChecked(){
      index = 0;
      while(index < document.getElementsByClassName('document-checkbox').length){
          if (document.getElementsByClassName('document-checkbox')[index].firstChild.nextSibling.checked){
              return true;
          }
          index++;
      }
      return false;
  }
  function showDownloadFile(element){
      document.getElementById('document-extra-'+element).style.display = "block";
  }
  function showAndHideDownloadFile(element){
      if(document.getElementById('document-extra-'+element).style.display == "block"){
        document.getElementById('document-extra-'+element).style.display = "none";
      }else{
        document.getElementById('document-extra-'+element).style.display = "block";
      }
  }
  function changeSelection(type){
      switch(type){
          case"all":
              index = 0;
              while(index < document.getElementsByClassName('document-checkbox').length){
                  document.getElementsByClassName('document-checkbox')[index].firstChild.nextSibling.checked = true;
                  index ++;
              }
              break;
          case"none":
              index = 0;
              while(index < document.getElementsByClassName('document-checkbox').length){
                  document.getElementsByClassName('document-checkbox')[index].firstChild.nextSibling.checked = false;
                  index ++;
              }
              break;
          case"invert":
              index = 0;
              while(index < document.getElementsByClassName('document-checkbox').length){
                  if(document.getElementsByClassName('document-checkbox')[index].firstChild.nextSibling.checked){
                    document.getElementsByClassName('document-checkbox')[index].firstChild.nextSibling.checked = false;
                  }else{
                      document.getElementsByClassName('document-checkbox')[index].firstChild.nextSibling.checked = true;
                  }
                  index ++;
              }
              break;
          case"star-filled":
          case"unread-document":
          case"read":
              index = 0;
              while(index < document.getElementsByClassName('library-document').length){
                  index2 = 0;
                  while(index2 < document.getElementsByClassName('library-document')[index].getElementsByClassName('document-icons')[0].getElementsByClassName(type).length){
                      if(document.getElementsByClassName('library-document')[index].getElementsByClassName('document-checkbox')[0].firstChild.nextSibling.checked){
                          document.getElementsByClassName('library-document')[index].getElementsByClassName('document-checkbox')[0].firstChild.nextSibling.checked = false;
                      }else{
                          document.getElementsByClassName('library-document')[index].getElementsByClassName('document-checkbox')[0].firstChild.nextSibling.checked = true;
                      }
                      index2 ++;
                  }
                  index ++;
              }
              break;
      }
      document.getElementById('auto-select').value = '';
      checkboxClicked();
  }
  function convertStringToFolder(id){
      if(typeof(id)=="string"){
          var dashPosition1=id.indexOf('-');
          if(id.substring(0,dashPosition1)=='folder'){
              var dashPosition2=id.indexOf('-',dashPosition1+1);
              if(dashPosition1>-1&&dashPosition2>-1){
                  return {type:id.substring(dashPosition1+1,dashPosition2),id:id.substring(dashPosition2+1),element:$('#'+id),string:id};
              }
          }
      }
      return undefined;
  }
  function getSelectedFolderInfo(){
      var selected=document.getElementsByClassName('library-group-selected')[0].id;
      if(selected){
          return convertStringToFolderInfo(selected);
      }
      return undefined;
  }
  function getSelectedDocuments(selectAll){
      if(selectAll){
          var currentDocuments = new Array();
          index = 0;
          while(index < document.getElementsByClassName('library-document').length){
              currentDocuments[index] = document.getElementsByClassName('library-document')[0].id;
              index++;
          }
          return currentDocuments;
      }
      var selectedDocs= new Array();
      index = 0;
      while (index < document.getElementsByClassName('document-checkbox').length){
          if (document.getElementsByClassName('document-checkbox')[index].firstChild.nextSibling.checked){
            selectedDocs[index] = document.getElementsByClassName('document-checkbox')[index].firstChild.nextSibling.value;
          }
          index++;
      }
      return selectedDocs;
  }
  function getDocumentFolderInfo(documentId){
      return getSelectedFolderInfo();
  }
  function getDefaultOverlayOptions(){
      return{showOKButton:true,showCancelButton:true,showButtonsInFooterClass:true,contentStyle:'text-align:left;padding-left:6px;padding-right:6px;'};
  }
  function submitAddDocumentsAJAXRequest(folderInfo,selectedDocs,fromGroupId){
      alert("Llamar a la API");
  }
  function convertStringToFolderInfo(id){
      if(typeof(id)=="string"){
          var dashPosition1=id.indexOf('-');
          if(id.substring(0,dashPosition1)=='folder'){
              var dashPosition2=id.indexOf('-',dashPosition1+1);
              if(dashPosition1>-1&&dashPosition2>-1){
                  return{type:id.substring(dashPosition1+1,dashPosition2),id:id.substring(dashPosition2+1),element:document.getElementById(id),string:id};
              }
          }
      }
      return undefined;
  }
  function addSelectedTo(folderId){
      var folderInfo=convertStringToFolderInfo(folderId);
      var selectedDocs=getSelectedDocuments();
      var fromGroupId=(getSelectedFolderInfo().type=='group'||getSelectedFolderInfo().type=='followedgroup')?getSelectedFolderInfo().id:0;
      if(selectedDocs.length==0||folderInfo.type===undefined){
          document.getElementById('add-to-group-select').value = 'none';
          return;
      }
      var folderName=document.getElementById('add-to-group-select')[document.getElementById('add-to-group-select').selectedIndex].text;
      var folderValue=document.getElementById('add-to-group-select')[document.getElementById('add-to-group-select').selectedIndex].value.split('-');
      var sds=(selectedDocs.length>1);
      var question="the "+((sds)?selectedDocs.length+' ':'')+"selected document"+((sds)?'s':'');
      switch(folderInfo.type){
          case"group":
              question="Add "+question+" to the group '"+folderName+"'?";
              break;
          case"profile":
              var defaultQuestion="Add "+question+" to your library collection '"+folderName+"'?";
              switch(folderInfo.id){
                  case"all":
                      if(this.getSelectedFolderInfo().type=="profile"){
                          alert("The document"+((sds)?'s are':' is')+" already in your library.");
                          return;
                      }else{
                          defaultQuestion="Add "+question+" to your library?";
                      }
                    break;
              }
              question=defaultQuestion;
              break;
          case"trash":
              question="Move "+question+" to the Trash? (Documents will remain in the Trash until you delete them permanently)";
              break;
          default:
              return;
      }
      var lastFolderId=null;
      var allInSame=true;
      for(i in selectedDocs){
          var docFolderInfo=getDocumentFolderInfo(selectedDocs[i]);
          if(docFolderInfo){
              if(lastFolderId==null)lastFolderId=docFolderInfo.string;
              if(lastFolderId!=docFolderInfo.string){
                  allInSame=false;
                  break;
              }else{
                  allInSame=true;
              }
          }else{
              document.getElementById('add-to-group-select').value = 'none';
              return;
          }
      }
      if(allInSame&&lastFolderId==folderId){
          alert('Nothing to do.');
      }else{
          var overlayOpts=getDefaultOverlayOptions();
          var commandText='Add Document'+(sds?'s':'');
          overlayOpts.okButtonValue=commandText;
          overlayOpts.okButtonCallback=function(){
              submitAddDocumentsAJAXRequest(folderInfo,selectedDocs,fromGroupId);
          };
          var documents = getDocumentsChecked();
          window.location="view.php?id="+"<?php echo $cm->id?>"+"&option=library&action=add_dialog&search_data="+folderValue[1]+"&element_id="+folderValue[2]+"&element_name="+folderName+"&page="+documents.length+"&documents_id="+documents+"&element_selected="+"<?php echo $element_selected?>"+"&sesskey="+"<?php echo sesskey()?>"+"";
      }
      document.getElementById('add-to-group-select').value = 'none';
  }
  
</script>

