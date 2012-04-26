<?php
/**
* Display add group form
*
*
 * @package   mod_modmendeley
 * @copyright 2011 María del Mar Jiménez Torres (mjimenez@fidesol.org) - Fundación I+D del Software Libre (www.fidesol.org)
 * @license   http://www.apache.org/licenses/LICENSE-2.0.html Apache License, Version 2.0
*/


echo "<div id='content-container'>";
echo "    <div id='main-content'>";
echo "        <div id='group-breadcrumbs'>";
echo "        <a href='view.php?id=$cm->id&amp;option=group&amp;sesskey=".sesskey()."' > Groups </a> › Create a group";
echo "        </div>";
echo "        <h1>Create a group</h1>";
echo "        <div>Use groups to collaborate with other researchers in your field, share research papers, and change the world.</div>";
echo "        <hr>";
echo "        <div id='cc-wrapper'>";
echo "            <div id='group-create'>";
echo "            <form id='form-group-create' name='form_group_create' method='post' action='view.php?id=$cm->id&amp;option=group&amp;action=save_group&amp;sesskey=" . sesskey()."' onkeypress='return event.keyCode!=13'>";
echo "                    <ul class='unstyled-list'>";
echo "                        <li>";
echo "                            <div class='create-label'><label>Group name</label></div>";
echo "                            <div class='create-field'><input type='text' name='name' id='group-name-input' value=''></div>";
echo "                            <br class='clear'>";
echo "                        </li>";
echo "                        <li>";
echo "                            <div class='create-label'><label>Group description</label></div>";
echo "                            <div class='create-field'>";
echo "                                <div id='group-description'><textarea class='fixed-width' id='description' name='description' onKeyUp='validationSizeTextarea()'></textarea></div>";
echo "                                <div style='padding-top:4px' class='left hide' id='description-chars-remaining'></div>";
echo "                            </div>";
echo "                            <br class='clear'>";
echo "                        </li>";
echo "                        <li style='padding-bottom:8px;'>";
echo "                            <div class='create-label'><label>Privacy settings</label></div>";
echo "                            <div class='create-field'>";
echo "                                <div class='fieldset'>";
echo "                                    <div class='legend'>Private</div>";
echo "                                    <div class='privacy-references-info'>Share references + files</div>";
echo "                                    <div id='group-privacy-settings'>";
echo "                                        <ul class='unstyled-list'>";
echo "                                            <li>";
echo "                                                <div class='left'><input id='group-private' type='radio' value='private' name='privacy-state'></div>";
echo "                                                <div class='left glabel'>";
echo "                                                    <h2><label for='group-private'>Private</label></h2>";
echo "                                                    <div class='minor'>Not visible to the public; great for private research projects</div>";
echo "                                                </div>";
echo "                                                <br class='clear'>";
echo "                                            </li>";
echo "                                        </ul>";
echo "                                    </div>";
echo "                                </div>";
echo "                                <div class='fieldset'>";
echo "                                    <div class='legend'>Public</div>";
echo "                                    <div class='privacy-references-info'>Share references only</div>";
echo "                                    <div id='group-privacy-settings'>";
echo "                                        <ul class='unstyled-list'>";
echo "                                            <li class='unfloat'>";
echo "                                                <div class='left'><input id='group-invite-only' type='radio' value='invite_only' name='privacy-state'></div>";
echo "                                                <div class='left glabel'>";
echo "                                                    <h2><label for='group-invite-only'>Invite-only</label></h2>";
echo "                                                    <div class='minor'>Publicly visible, but you decide who contributes; great for public reading lists or curating your lab's research output</div>";
echo "                                                </div>";
echo "                                            </li>";
echo "                                            <li class='unfloat'>";
echo "                                                <div class='left'><input id='group-open' type='radio' value='open' name='privacy-state' checked='checked'></div>";
echo "                                                <div class='left glabel'>";
echo "                                                    <h2><label for='group-open'>Open</label></h2>";
echo "                                                    <div class='minor'>Publicly visible, everyone can contribute; great for open discussion groups around any subject</div>";
echo "                                                </div>";
echo "                                            </li>";
echo "                                        </ul>";
echo "                                    </div>";
echo "                                </div>";
echo "                                <span class='text-minor'><a href='javascript:toggleAdditionalInfoFields();'>Add additional info</a> (e.g. tags, disciplines)</span>";
echo "                            </div>";
echo "                            <br class='clear'>";
echo "                        </li>";
echo "                        <li class='create-group-additional-info' style='display:none'>";
echo "                            <div class='create-label'><label>Tags</label></div>";
echo "                            <div class='create-field'>";
echo "                                <div id='group-tags'>";
echo "                                    <div id='tags-top'><input type='text' class='x-form-exempt' name='tag' id='tag-adder' onkeydown='if(event.keyCode == 13){addTags(); return false;}'></div>";
echo "                                    <div id='tags-bottom'>";
echo "                                        <div id='no-tags'><em>No tags added yet</em></div>";
echo "                                        <div id='tags-list' class='noshow unfloat'>";
echo "                                            <ul class='unstyled-list x-form-group' id='all-tags'></ul>";
echo "                                            <input type='hidden' id='total-tags' name='total-tags' value='0'>";
echo "                                        </div>";
echo "                                    </div>";
echo "                                </div>";
echo "                                <div id='tag-remove'><a href='javascript:;' onclick='deleteSelectedTags()'>remove tag</a></div>";
echo "                            </div>";
echo "                            <br class='clear'>";
echo "                        </li>";
echo "                        <li class='create-group-additional-info' style='display:none'>";
echo "                            <div class='create-label'><label>Disciplines</label></div>";
echo "                            <div class='create-field' id='create-disciplines'>";
echo "                                <select id='disciplines_0' name='disciplines[]' onchange='selectedDiscipline(0, this.value);' style='margin-bottom: 4px;'>";
echo "                                    <option value='1'>Arts and Literature</option>";
echo "                                    <option value='2'>Astronomy / Astrophysics / Space Science</option>";
echo "                                    <option value='3'>Biological Sciences</option>";
echo "                                    <option value='4'>Business Administration</option>";
echo "                                    <option value='5'>Chemistry</option>";
echo "                                    <option value='6' selected='selected'>Computer and Information Science</option>";
echo "                                    <option value='7'>Earth Sciences</option>";
echo "                                    <option value='8'>Economics</option>";
echo "                                    <option value='9'>Education</option>";
echo "                                    <option value='10'>Electrical and Electronic Engineering</option>";
echo "                                    <option value='11'>Engineering</option>";
echo "                                    <option value='12'>Environmental Sciences</option>";
echo "                                    <option value='13'>Humanities</option>";
echo "                                    <option value='14'>Law</option>";
echo "                                    <option value='15'>Linguistics</option>";
echo "                                    <option value='16'>Management Science / Operations Research</option>";
echo "                                    <option value='17'>Materials Science</option>";
echo "                                    <option value='18'>Mathematics</option>";
echo "                                    <option value='19'>Medicine</option>";
echo "                                    <option value='20'>Philosophy</option>";
echo "                                    <option value='21'>Physics</option>";
echo "                                    <option value='22'>Psychology</option>";
echo "                                    <option value='23'>Social Sciences</option>";
echo "                                    <option value='24'>Sports and Recreation</option>";
echo "                                    <option value='25'>Design</option>";
echo "                                </select>";
echo "                                <br>";
echo "                                <select id='disciplines_1' name='disciplines[]' onchange='selectedDiscipline(1, this.value);' style='margin-bottom: 4px;'>";
echo "                                    <option value='0' selected='selected'>Select an optional second discipline</option>";
echo "                                    <option value='1'>Arts and Literature</option>";
echo "                                    <option value='2'>Astronomy / Astrophysics / Space Science</option>";
echo "                                    <option value='3'>Biological Sciences</option>";
echo "                                    <option value='4'>Business Administration</option>";
echo "                                    <option value='5'>Chemistry</option>";
echo "                                    <option value='6'>Computer and Information Science</option>";
echo "                                    <option value='7'>Earth Sciences</option>";
echo "                                    <option value='8'>Economics</option>";
echo "                                    <option value='9'>Education</option>";
echo "                                    <option value='10'>Electrical and Electronic Engineering</option>";
echo "                                    <option value='11'>Engineering</option>";
echo "                                    <option value='12'>Environmental Sciences</option>";
echo "                                    <option value='13'>Humanities</option>";
echo "                                    <option value='14'>Law</option>";
echo "                                    <option value='15'>Linguistics</option>";
echo "                                    <option value='16'>Management Science / Operations Research</option>";
echo "                                    <option value='17'>Materials Science</option>";
echo "                                    <option value='18'>Mathematics</option>";
echo "                                    <option value='19'>Medicine</option>";
echo "                                    <option value='20'>Philosophy</option>";
echo "                                    <option value='21'>Physics</option>";
echo "                                    <option value='22'>Psychology</option>";
echo "                                    <option value='23'>Social Sciences</option>";
echo "                                    <option value='24'>Sports and Recreation</option>";
echo "                                    <option value='25'>Design</option>";
echo "                                </select>";
echo "                                <br>";
echo "                                <select id='disciplines_2' name='disciplines[]' onchange='selectedDiscipline(2, this.value);' style='margin-bottom: 4px;' disabled='disabled'>";
echo "                                    <option value='0' selected='selected'>Select an optional third discipline</option>";
echo "                                    <option value='1'>Arts and Literature</option>";
echo "                                    <option value='2'>Astronomy / Astrophysics / Space Science</option>";
echo "                                    <option value='3'>Biological Sciences</option>";
echo "                                    <option value='4'>Business Administration</option>";
echo "                                    <option value='5'>Chemistry</option>";
echo "                                    <option value='6'>Computer and Information Science</option>";
echo "                                    <option value='7'>Earth Sciences</option>";
echo "                                    <option value='8'>Economics</option>";
echo "                                    <option value='9'>Education</option>";
echo "                                    <option value='10'>Electrical and Electronic Engineering</option>";
echo "                                    <option value='11'>Engineering</option>";
echo "                                    <option value='12'>Environmental Sciences</option>";
echo "                                    <option value='13'>Humanities</option>";
echo "                                    <option value='14'>Law</option>";
echo "                                    <option value='15'>Linguistics</option>";
echo "                                    <option value='16'>Management Science / Operations Research</option>";
echo "                                    <option value='17'>Materials Science</option>";
echo "                                    <option value='18'>Mathematics</option>";
echo "                                    <option value='19'>Medicine</option>";
echo "                                    <option value='20'>Philosophy</option>";
echo "                                    <option value='21'>Physics</option>";
echo "                                    <option value='22'>Psychology</option>";
echo "                                    <option value='23'>Social Sciences</option>";
echo "                                    <option value='24'>Sports and Recreation</option>";
echo "                                    <option value='25'>Design</option>";
echo "                                </select>";
echo "                                <br>";
echo "                                <div class='minor'>The tags and disciplines information will be used to help people discover your group.</div>";
echo "                            </div>";
echo "                            <br class='clear'>";
echo "                        </li>";
echo "                        <li id='additional-info-line'>";
echo "                            <div class='create-label'></div>";
echo "                            <div class='create-field'><hr></div>";
echo "                            <br class='clear'>";
echo "                        </li>";
echo "                        <li>";
echo "                            <div class='create-label'></div>";
echo "                            <div class='create-field' id='create-buttons'>";
echo "                                <div class='right'>";
echo "                                    <input type='submit' value='Create Group' onclick='submitForm(' id='group-create-button'>";
echo "                                    <input type='button' value='Cancel' onclick='window.history.go(-2)'>";
echo "                                </div>";
echo "                                <br class='clear'>";
echo "                            </div>";
echo "                            <br class='clear'>";
echo "                        </li>";
echo "                    </ul>";
echo"                </form>";
echo "            </div>";
echo "        </div>";
echo "        <div class='clear'></div>";
echo "    </div>";
echo "</div>";
?>

<script>
function toggleAdditionalInfoFields(){
    if(document.getElementsByClassName('create-group-additional-info')[0].style.display == "none"){
        display = "list-item";
    }else {
        display = "none";
    }
    index = 0;
    while (index < document.getElementsByClassName('create-group-additional-info').length){
        document.getElementsByClassName('create-group-additional-info')[index].style.display = display;
        index ++;
    }
}
function formValidation() {
    if (document.form_group_create.name.value.length == 0) {
        alert('name can not be empty');
        document.form_group_create.name.focus();
        return false;
    }
    return true;
}
function submitForm(){
    if(formValidation()){
        document.form_group_create.submit();
    }
}
function validationSizeTextarea(){
    var characters = 512 - document.form_group_create.description.value.length;
    if (characters >= 0 && characters <= 20) {
        document.getElementById('description-chars-remaining').style.display = 'block';
        document.getElementById('description-chars-remaining').innerHTML = "<i><b>"+characters+"</b> characters left</i>"
    }else if(characters < 0){
        document.getElementById('description-chars-remaining').style.display = 'block';
        document.getElementById('description-chars-remaining').innerHTML = '<div style="height:16px;padding-top:2px;background:transparent url(http://www.mendeley.com/graphics/newsfeed_icons/exclamation.png) no-repeat scroll 0 0;padding-left:20px;"> <i><b>Description is too long:</b> delete <b>'+ Math.abs(characters)+'</b> characters</i></div>';
    }else{
        document.getElementById('description-chars-remaining').style.display = 'none';
    }
}
function addTags(){
    document.getElementById('no-tags').style.display = 'none';
    document.getElementById('tags-list').style.display = 'block';
    children = existsTag(document.getElementById('tag-adder').value);
    if (children != null){
        document.getElementById('all-tags').children[children].classList.add('selected');
    }else {
        document.getElementById('all-tags').innerHTML += '<li><div class="left-edge sprite left"></div><div class="x-form-element left tag-entry">'+document.getElementById('tag-adder').value+'</div><div class="right-edge sprite left"></div><br class="clear"></li>';
    }
    document.getElementById('tag-adder').value = '';
}
function existsTag(tag){
    index = 0;
    while (index < document.getElementById('all-tags').children.length){
        if(document.getElementById('all-tags').children[index].children[1].innerHTML == tag){
            return index;
        }
        index ++;
    }
    return null;
}
function deleteSelectedTags(){
    index = 0;
    size = document.getElementById('all-tags').getElementsByClassName('selected').length;
    while (index < size){
        document.getElementById('all-tags').removeChild(document.getElementById('all-tags').getElementsByClassName('selected')[0]);
        index ++;
    }
    if (document.getElementById('all-tags').children.length == 0){
        document.getElementById('no-tags').style.display = 'block';
        document.getElementById('tags-list').style.display = 'none';
    }
}
function selectedDiscipline(placement,value){
    if(placement>0){
        var next=document.getElementById('disciplines_'+(placement+1));
        if(next.length>0){
            if(value>0)next.disabled = false;
            else{
                var selected=next.children[next.selectedIndex];
                if(selected.length==1&&selected[0].defaultSelected)
                    next.disabled = true;
            }
        }
    }
    this.handleDisciplinesLists();
}
function handleDisciplinesLists(){
    var index=0,list,selectedDisciplines=[],selectedDiscipline;
    while(index < document.getElementById('create-disciplines').children.length){
        list = document.getElementById('create-disciplines').children[index];
        disableOption(list);
        selectedDiscipline=list.children[list.selectedIndex];
        if(selectedDiscipline==null || selectedDiscipline.value<=0)selectedDiscipline=null;
        for(var x=0;x<selectedDisciplines.length;x++){
            list.children[selectedDisciplines[x]].disabled = true;
            if(selectedDiscipline&&selectedDiscipline.value==selectedDisciplines[x]){
                list.children[0].selected = true;
                selectedDiscipline=null;
            }
        }
        if(selectedDiscipline)selectedDisciplines.push(selectedDiscipline.value);
        index +=2;
    }
}

function disableOption(element){
    index = 0;
    while (index < element.children.length){
        element.children[index].disabled = false;
        index++;
    }
}
</script>
