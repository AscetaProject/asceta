<?php
/**
* Display user group list
*
*
 * @package   mod_modmendeley
 * @copyright 2011 María del Mar Jiménez Torres (mjimenez@fidesol.org) - Fundación I+D del Software Libre (www.fidesol.org)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/

echo "<div class='column-a'>";
echo "    <div class='padding' >";
echo "        <h2 class='heading-line extra-padding'><span>My groups</span></h2>";
echo "        <div class='groups-content'>";
echo "        <h2 class='group-type-heading'> Groups I own or can administrate</h2>";
echo "            <ol class='item-list groups'>";
foreach($user_groups as $group){
    $group_people = getLibraryValue('GET', $user, '/library/groups/'.$group->id.'/people');
    $group_documents = getLibraryValue('GET', $user, '/library/groups/'.$group->id);
    echo "                <li>";
    echo "                    <article class='item group private	member	not-follower	not-invited	owner	not-admin' data-group-id='".$group->id."'>";
    echo "                        <a href='http://www.mendeley.com/groups/".$group->id."/".$group->name."/' target='_blank' class='thumb ' rel='tipsy' data-dm-log-click='{&quot;event&quot;:&quot;click&quot;,&quot;page&quot;:&quot;groups&quot;,&quot;data[]&quot;:[&quot;Image&quot;]}' original-title='asfasf'><img src='http://www.mendeley.com/graphics/disciplines/small/computer-and-information-science_1443803365765644.png' alt='".$group->name."' class=''></a>";
    echo "                        <div class='item-info' style='margin-left: 64px;'>";
    echo "                            <div class='title'><a href='http://www.mendeley.com/groups/".$group->id."/".$group->name."/' target='_blank' data-dm-log-click='{&quot;event&quot;:&quot;click&quot;,&quot;page&quot;:&quot;groups&quot;,&quot;data[]&quot;:[&quot;Title&quot;]}' rel='nofollow'>".$group->name."</a></div>";
    echo "                            <div class='description'>".$group->name."</div>";
    echo "                            <div class='tags-list  one-line'></div>";
    echo "                            <table class='group-footer'>";
    echo "                                <tbody>";
    echo "                                    <tr>";
    echo "                                        <td class='actions'>";
    echo "                                            <a class='group-action ask-to-join-group' href='#' data-action='asktojointemplate' title='Ask to join this invite-only group' data-dm-log-click='{&quot;event&quot;:&quot;click&quot;,&quot;page&quot;:&quot;groups&quot;,&quot;data[]&quot;:[&quot;AskToJoinGroup&quot;]}'><span class='action-name'>Ask to join</span> group</a>";
    echo "                                            <a class='group-action leave-group' href='#' data-action='leave' title='Leave this group' data-dm-log-click='{&quot;event&quot;:&quot;click&quot;,&quot;page&quot;:&quot;groups&quot;,&quot;data[]&quot;:[&quot;LeaveGroup&quot;]}'><span class='action-name'>Leave</span> group</a>";
    echo "                                            <a class='group-action accept-group' href='#' data-action='acceptinvitation' title='Accept an invitation to this group' data-dm-log-click='{&quot;event&quot;:&quot;click&quot;,&quot;page&quot;:&quot;groups&quot;,&quot;data[]&quot;:[&quot;AcceptGroupInvitation&quot;]}'><span class='action-name'>Accept</span> invitation</a>";
    echo "                                            <a class='group-action decline-group' href='#' data-action='declineinvitation' title='Decline an invitation to this group' data-dm-log-click='{&quot;event&quot;:&quot;click&quot;,&quot;page&quot;:&quot;groups&quot;,&quot;data[]&quot;:[&quot;DeclineGroupInvitation&quot;]}'><span class='action-name'>Decline</span> invitation</a>";
    echo "                                            <a class='group-action follow-group' href='#' data-action='follow' title='Receive updates from this group' data-dm-log-click='{&quot;event&quot;:&quot;click&quot;,&quot;page&quot;:&quot;groups&quot;,&quot;data[]&quot;:[&quot;FollowGroup&quot;]}'><span class='action-name'>Follow</span> group</a>";
    echo "                                            <a class='group-action unfollow-group' href='#' data-action='unfollow' title='Stop receiving updates from this group' data-dm-log-click='{&quot;event&quot;:&quot;click&quot;,&quot;page&quot;:&quot;groups&quot;,&quot;data[]&quot;:[&quot;UnfollowGroup&quot;]}'><span class='action-name'>Unfollow</span> group</a>";
    echo "                                            <a class='group-link group-settings' href='http://www.mendeley.com/groups/".$group->id."/".$group->name."/settings/' target='_blank' title='Administer this group'>Group settings</a>";
    echo "                                        </td>";
    echo "                                        <td class='counts'>";
    echo "                                            <a class='member-count' title='".count($group_people->members)." members on Mendeley' href='http://www.mendeley.com/groups/".$group->id."/".$group->name."/members/' target='_blank' rel='nofollow'><strong>".count($group_people->members)."</strong> member</a>";
    if(count($group_documents->document_ids) > 0){
        echo "                                            <span class='separator right' style='padding:0 5px'>·</span>";
        echo "                                            <a class='paper-count' title='".count($group_documents->document_ids)." readers on Mendeley' href='http://www.mendeley.com/groups/$group->id/name/papers/' target='_blanck' rel='nofollow'><strong>".count($group_documents->document_ids)."</strong> papers</a>";
    }
    echo "                                        </td>";
    echo "                                    </tr>";
    echo "                                </tbody>";
    echo "                            </table>";
    echo "                        </div>";
    echo "                    </article>";
    echo "                </li>";
}
echo "            </ol>";
echo "        </div>";
echo "    </div>";
echo "    <div class='clear'></div>";
echo "</div>";
?>