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
        echo "                  <a href='#' onclick='Mendeley.Publication.getDocuments(Mendeley.Publication.getSelectedFolderInfo().element, 0);Mendeley.Publication.resetForm();return false;'>Clear</a>";
        echo "              </div>";
        echo "              <div>Results for '<strong>how</strong>'</div>";
        echo "          </div>";
    }
    echo "          <div class='library-toolbar'>";
    echo "              <div class='library-auto-select'>";
    echo "                  <select id='auto-select' onchange='Mendeley.Publication.changeSelection($(this).val());'>";
    echo "                      <option value=''>Select...</option>";
    echo "                      <optgroup>";
    echo "                          <option value='all'>All</option>";
    echo "                          <option value='invert'>Invert</option>";
    echo "                          <option value='none'>None</option>";
    echo "                      </optgroup>";
    echo "                      <optgroup label='&nbsp;'>";
    echo "                          <option value='star-filled'>Favorites</option>";
    echo "                          <option value='unread'>Unread</option>";
    echo "                          <option value='read'>Read</option>";
    echo "                      </optgroup>";
    echo "                  </select>";
    echo "              </div>";
    echo "              <div class='library-add-to-group'>";
    echo "                  <select class='library-group-select' id='add-to-group-select' onchange='Mendeley.Publication.addSelectedTo($(this).val());' disabled='disabled' tabindex='1'>";
    echo "                      <option value='none'>Add selected documents to...</option>";
    echo "                      <optgroup label='My Library' id='add-to-group-select-optgroup-my-lib'>";
    echo "                          <option class='select-profile-starred' value='folder-profile-starred'>Favorites</option>";
    echo "                          <option class='select-profile-authored' value='folder-profile-authored'>My Publications</option>";
    foreach($folders as $folder){
        echo "                          <option class='select-profile-private' value='folder-profile-".$folder->id."'>".$folder->name."</option>";
    }
    echo "                      </optgroup>";
    if (!empty($groups)){
        echo "                      <optgroup label='Groups' id='add-to-group-select-optgroup-shared'>";
        foreach($groups as $group){
            echo "                          <option class='select-group-all' value='folder-group-".$group->id."'>".$group->name."</option>";
        }
    echo "                      </optgroup>";
    }
    echo "                  </select>";
    echo "              </div>";
    echo "              <div class='library-pagemenu'>";
    echo "                  <div class='pagemenu'>";
    showPaginationString(intval($documents->current_page)+1, intval($documents->total_pages), 1, $redirect_data, false);
    echo "                  </div>";
    echo "              </div>";
    echo "              <div class='clear'></div>";
    echo "          </div>";
    foreach($documents->document_ids as $document){
        if($documents->group_id != ''){
            $document_detail = getLibraryValue('GET', $user, '/library/groups/'.$documents->group_id.'/'.$document);
        }else {
            $document_detail = getLibraryValue('GET', $user, '/library/documents/'.$document);
        }
        echo "          <div class='library-document library-document-".(($c = !$c)? 'odd': 'even')."-row' id='library-document-".$document."'>";
        echo "              <a href='#' name='document-".$document."'></a>";
        echo "              <div class='left'>";
        echo "                  <div class='document-checkbox'>";
        echo "                      <input type='checkbox' value='".$document."' id='document-checkbox-".$document."' onclick='checkboxClicked(this);'>";
        echo "                      <input type='hidden' value='folder-profile-all' id='document-folder-".$document."'>";
        echo "                  </div>";
        echo "                  <div class='document-icons'>";
        echo "                      <a title='Mark as Favorite' class='star-empty' href='#' onclick='Mendeley.Publication.changeDocumentValue(this, '".$document."', 'isStarred', 'toggle'); return false;'>";
        echo "                          <img src='https://www.mendeley.com/graphics/common/star-empty_7298568672004598.png' alt='star_empty' width='16' height='16'>";
        echo "                      </a>";
        echo "                      <a title='Mark as Read' class='unread-document' href='#' onclick='Mendeley.Publication.changeDocumentValue(this, '".$document."', 'isRead', 'toggle'); return false;'>";
        echo "                          <img src='http://www.mendeley.com/graphics/common/unread_1738973333671741.png' alt='unread' width='16' height='16'>";
        echo "                      </a>";
        if($action == 'documents'){
            echo "                      <a title='Click here to show the document's files' class='pdf' href='#' onclick='Mendeley.Publication.dispatchExtra('files', '".$document."'); return false;'>";
            echo "                          <img src='https://www.mendeley.com/graphics/common/pdf_3275105525101106.gif' alt='pdf' width='16' height='16'>";
            echo "                      </a>";
            echo "                      <a title='Click here to edit tags and notes' class='tags-or-notes' href='#' onclick='Mendeley.Publication.dispatchExtra('tags-notes', '".$document."'); return false;'>";
            echo "                          <img src='https://www.mendeley.com/graphics/common/has-tags-or-notes_1282490988286945.gif' alt='tags/notes' width='16' height='16'>";
            echo "                      </a>";
        }
        echo "                  </div>";
        echo "              </div>";
        echo "              <div class='document-share-options'>";
        echo "              <!-- need to sort out double encoding issue below -->";
        echo "                  <a class='send-document-via-email' title='Send document via e-mail' href='#' data-title='0 - Prefacio' onclick='Mendeley.Publication.sendDocument($(this).data('title'), '".$document.":');return false;'>Send document via e-mail</a>";
        echo "              </div>";
        echo "              <div class='document-description'>";
        echo "                  <a class='black' href='#' onclick='Mendeley.Publication.dispatchExtra('menu', '".$document."'); return false;'>".$document_detail->title."</a><br>";
        $document_authors = array();
        foreach ($document_detail->authors as $v){
            $document_authors [] = "$v->forename $v->surname";
        }
        echo "                  <div> ".implode(',',$document_authors)." ". ((!empty($document_detail->year))? ' ('.$document_detail->year.')': '')." </div>";
        echo "                  <div> <em>".((!empty($document_detail->published_in))?$document_detail->published_in: '')."</em> ".((!empty($document_detail->pages))?' p. '.$document_detail->pages:'')." </div>";
        echo "                  <div id='tag-list-".$document."'>";
        echo "                      <div id='document-tags-".$document."'>";
        foreach($document_detail->tags as $tag){
            echo "                          <a href='#' onclick='Mendeley.Publication.getDocumentsByIdAndTag(".$document.", {}); return false;'>".$tag."</a>	";
        }
        echo "                      </div>";
        echo "                  </div>";
        echo "                  <div id='document-extra-".$document."' class='document-extra'>";
        echo "                      <div id='download-document-".$document."'>";
        echo "                          <a name='download-document-".$document."' href='#'></a> Download: &nbsp;";
        foreach($document_detail->files as $file){
            echo "                          <a href='http://www.mendeley.com/download/personal/5056951/".$document."/".$file->file_hash."/dl.".$file->file_extension."' class='red'>".$file->file_extension."</a> (".$file->file_size." MB) &nbsp;";
        }
        echo "                      </div>";
        echo "      		<div class='document-links'>";
        echo "                          <span id='tags-notes-".$document."-link' class='arrowlink' onclick='Mendeley.Publication.dispatchExtra('tags-notes', '".$document."');'>";
        echo "                              <img src='https://www.mendeley.com/graphics/commonnew/list-arrow-right_3884326895063066.gif' alt='&gt;' width='12' height='15'><span>Edit tags and notes</span>";
        echo "                          </span> &nbsp; &nbsp;";
        echo "                          <span id='document-details-".$document."-link' class='arrowlink' onclick='Mendeley.Publication.dispatchExtra('document-details', '".$document."');'>";
        echo "      				<img src='https://www.mendeley.com/graphics/commonnew/list-arrow-right_3884326895063066.gif' alt='&gt;' width='12' height='15'><span>Edit document details</span>";
        echo "                          </span>";
        echo "      		</div>";
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
        echo "                              <a class='save_button' href='#' onclick='' tabindex='1'><img src='https://www.mendeley.com/graphics/common/button_save_2573160900840882.gif' onclick='Mendeley.Publication.tagsAndNotesSave('".$document."', ''); return false;' alt='Save' width='72' height='22'></a>";
        echo "                              <a class='cancel_button' href='#' onclick='' tabindex='1'><img src='https://www.mendeley.com/graphics/common/button_cancel_1538495746221986.gif' onclick='Mendeley.Publication.tagsAndNotesCancel('".$document."', true); return false;' alt='Cancel' width='72' height='22'></a>";
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
  function checkboxClicked(element){
      if(isAnyDocumentChecked()){
          document.getElementById('toolbar-delete-document').classList.remove('disabled-icon');
          document.getElementById('add-to-group-select').disabled = false;
      }else{
          document.getElementById('toolbar-delete-document').classList.add('disabled-icon');
          document.getElementById('toolbar-remove-document').classList.add('disabled-icon');
          document.getElementById('add-to-group-select').disabled = true;

      }
      if ('<?php echo $action ?>' == 'folders' && isAnyDocumentChecked()){
          document.getElementById('toolbar-remove-document').classList.remove('disabled-icon');
      } else {
          document.getElementById('toolbar-remove-document').classList.add('disabled-icon');
      }

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
</script>

