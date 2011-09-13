<?php
/**
* Display People view
*
*
 * @package   mod_modmendeley
 * @copyright 2011 María del Mar Jiménez Torres (mjimenez@fidesol.org) - Fundación I+D del Software Libre (www.fidesol.org)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/

echo "<div id='content-container'>";
echo "    <div id='main-content'>";
echo "        <div class='separator-bottom highlighted search content page-top separator-bottom-page-top separator-bottom-highlighted' style='height:98px'>";
echo "            <img class='search-controll' border='0' src='http://www.mendeley.com/graphics/commonnew/search_2373467049533883.gif' width='172' height='98' alt='search'>";
echo "            <div>";
echo "                <form action='/search/' method='post' name='searchForm' id='searchForm' onsubmit='return false;'><input type='hidden' name='csrf_token' value='18d5fcc7e9ecdbac2fe2aef6462147e4f036250b'>";
echo "                    <div class='search-input-button' style='visibility:hidden'>";
echo "                        <input type='text' name='keywords' id='searchinput' class='searchmembers pretty-form' value='' style='color: rgb(192, 192, 192); font-size: 12px; '>";
echo "                        <input type='image' src='http://www.mendeley.com/graphics/commonnew/search-button_2985783292805502.gif' value='' id='searchbutton' class='search-button search-button-main'>";
echo "                        <img src='http://www.mendeley.com/graphics/common/spacer_4223231388365693.gif' alt='' id='searchloading' class='right loader'>";
echo "                        <input type='hidden' name='searchFor' id='searchFor' value='[contacts:requesting+approved]'>";
echo "                        <input type='hidden' name='searchOrder' id='searchOrder' value='firstname'></div>";
echo "                </form>";
echo "                <div class='clear'></div>";
echo "            </div>";
echo "        </div>";
echo "        <div class='clear'></div>";
echo "        <div id='searchresultshead' class='spacing_bottom'>";
echo "            <div class='right'></div>";
echo "            <ul id='sub-menu' class='search'>";
echo "                <li><h1 style='border-top-left-radius: 7px 7px; border-top-right-radius: 7px 7px; border-bottom-left-radius: 7px 7px; border-bottom-right-radius: 7px 7px; '>My contacts</h1></li>";
echo "                <li><a href='#' onclick='window.location=\"view.php?id=$cm->id&amp;option=people&amp;action=profile&amp;element_id=me&amp;sesskey=" . sesskey()."\"' style='border-top-left-radius: 7px 7px; border-top-right-radius: 7px 7px; border-bottom-left-radius: 7px 7px; border-bottom-right-radius: 7px 7px; '>My Profile</a></li>";
echo "            </ul>";
echo "            <div class='clear'></div>";
echo "        </div>";
echo "        <div id='matching_members'>";
echo "            <div id='search_results'>";
echo "                <ul class='member-details-listing'>";
foreach($contacts as $contact){
    $contact_info = getLibraryValue('GET', $user, '/profiles/info/'.$contact->profile_id);
    $contact_info = $contact_info->main;
    echo "                    <li class='contact unfloat' id='".str_ireplace(" ", "_", $contact->name)."'>";
    echo "                        <div class='member-details-listing-spacer'></div>";
    echo "                        <div class='left'>";
    echo "                            <a href='#' onclick='window.location=\"view.php?id=$cm->id&amp;option=people&amp;action=profile&amp;element_id=$contact->profile_id&amp;sesskey=" . sesskey()."\"' class='thumb plain' rel='tipsy' title='$contact->name' data-dm-log-click='{&quot;event&quot;:&quot;click&quot;,&quot;page&quot;:&quot;peopleSearchResults&quot;,&quot;data[]&quot;:[&quot;Image&quot;,&quot;2505591&quot;,null,&quot;&quot;,0]}'>";
    echo "                                <img src='$contact_info->photo' alt='$contact->name' class=''>";
    echo "                            </a>";
    echo "                        </div>";
    echo "                        <div class='member-details'>";
    echo "                            <div class='member-name'>";
    echo "                                <a title='See member's public profile' href='#' onclick='window.location=\"view.php?id=$cm->id&amp;option=people&amp;action=profile&amp;element_id=$contact->profile_id&amp;sesskey=" . sesskey()."\"'>$contact->name</a>";
    echo "                            </div>";
    echo "                            <div class='member-location'>$contact_info->location</div>";
    echo "                            <div class='member-discipline-key member-field-key'>Discipline:&nbsp;</div>";
    echo "                            <div class='member-discipline-value member-field-value'><a href='/".str_replace(" ", "-", $contact_info->discipline_name)."/'>$contact_info->discipline_name</a></div>";
    echo "                            <div class='member-contact-count'><a href='".$contact_info->url."contacts/' target='_blank'>contacts</a></div>";
    echo "                            <div class='member-actions'>";
    echo "                                <div class='send-message' style='visibility:hidden'>";
    echo "                                    <a href='#' onclick='Mendeley.Profile.Contacts.write('juan-manuel-dodero');return false;'>";
    echo "                                        <span class='send-message-text'>Send message</span>";
    echo "                                    </a>";
    echo "                                </div>";
    echo "                            </div>";
    echo "                        </div>";
    echo "                    </li>";
}
echo "                </ul>";
echo "                <div class='member-details-listing-spacer'></div>";
echo "            </div>";
echo "        </div>";
echo "        <div id='searchresultsfoot'>";
echo "            <div class='right'></div>";
echo "            <div class='clear'></div>";
echo "        </div>";
echo "        <div class='clear'></div>";
echo "    </div>";
echo "</div>";
?>
<script>

</script>

