<?php
$plural = ($page > 1) ? 's' : '';
$elements = ($page > 1)? ($page).' ' : '';
$defaultQuestion="Add the ".$elements."selected document".$plural." to your library collection '".$element_name."'?";
echo "<div id = 'interContainer'>\n";
echo "<div class='blockUI blockMsg blockPage' style='z-index: 1001; padding-top: 0px; padding-right: 0px; padding-bottom: 0px; padding-left: 0px; margin-top: 0px; margin-right: 0px; margin-bottom: 0px; margin-left: -150px; width: 300px; top: 200px; left: 40%; text-align: center; color: rgb(0, 0, 0); border-top-width: 10px; border-right-width: 10px; border-bottom-width: 10px; border-left-width: 10px; border-top-style: solid; border-right-style: solid; border-bottom-style: solid; border-left-style: solid; border-top-color: rgba(82, 82, 82, 0.699219); border-right-color: rgba(82, 82, 82, 0.699219); border-bottom-color: rgba(82, 82, 82, 0.699219); border-left-color: rgba(82, 82, 82, 0.699219); cursor: auto; border-top-left-radius: 10px 10px; border-top-right-radius: 10px 10px; border-bottom-right-radius: 10px 10px; border-bottom-left-radius: 10px 10px; position: absolute;'>\n";
echo "  <div cellspacing='0' cellpadding='0' class='overlay-content'>\n";
echo "      <div class='overlay-header' id='overlay_box_title'>Add Document</div>\n";
echo "      <div class='overlay_spacer'></div>\n";
echo "      <div id='overlay_box_content'>\n";
echo "          <div style='padding-bottom:31px;text-align:left;padding-left:6px;padding-right:6px;'>".$defaultQuestion."</div>\n";
echo "          <div>\n";
echo "              <div class='overlay-footer'>\n";
echo "                  <input type='button' class='overlay-ok' id='overlay-ok-button' value='Add Document' onclick='window.location=\"view.php?id=$cm->id&amp;option=library&amp;action=addselecteddocument&amp;element_id=$element_id&amp;element_name=$search_data&amp;documents_id=".$_GET['documents_id']."&amp;element_selected=$element_selected&amp;sesskey=" . sesskey() . "\"'>\n";
echo "                  <input type='button' class='overlay-cancel' id='overlay-cancel-button' value='Cancel' onclick='window.history.go(-1)'>\n";
echo "              </div>\n";
echo "          </div>\n";
echo "      </div>\n";
echo "      <div class='clear'></div>\n";
echo "      <div class='overlay_spacer'></div>\n";
echo "  </div>\n";
echo "</div>\n";
echo "</div>\n";
echo "<div id='interVeil'></div>\n";
?>
<script>
document.getElementById('interVeil').style.position="absolute"
document.getElementById('interVeil').style.width=document.getElementById('main-content').getBoundingClientRect().width+"px"
document.getElementById('interVeil').style.height=document.getElementById('main-content').getBoundingClientRect().height+"px"
document.getElementById('interVeil').style.left=document.getElementById('main-content').getBoundingClientRect().left-4+"px"
document.getElementById('interVeil').style.top=document.getElementById('maincontent').getBoundingClientRect().top+3+"px"
document.getElementById('interVeil').style.visibility="visible"
document.getElementById('interVeil').style.backgroundColor="#353535"
document.getElementById('interVeil').style.opacity=0.4
</script>