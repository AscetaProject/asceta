<?php
/**
* Display group view
*
*
 * @package   mod_modmendeley
 * @copyright 2011 María del Mar Jiménez Torres (mjimenez@fidesol.org) - Fundación I+D del Software Libre (www.fidesol.org)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/

echo "<div id='content-container'>";
echo "<div class='heading'>";
echo "  <div class='padding unfloat'>";
echo "      <div class='groups-overview'>";
echo "		<h1>Groups</h1>";
echo "      </div>";
if($modmendeley->private){
    echo "      <div class='column-b'>";
    echo "          <div class='action-buttons'>";
    echo "              <a href='$CFG->wwwroot/mod/modmendeley/view.php?id=$cm->id&amp;option=group&amp;action=add&amp;sesskey=" . sesskey()."'  id='create_group' class='link-button primary'>Create a new group</a>";
    echo "          </div>";
    echo "      </div>";
}
echo "  </div>";
echo "</div>";
//SEARCH
echo "<div id='search-container'>";
echo "  <div id='search-bar'>";
echo "      <div class='specific-search'>";
echo "          <div class='search'>";
echo "              <form action='http://www.mendeley.com/groups/search/' target='_blank' method='GET' onsubmit=''> ";
echo "                  <div class='search-box'> ";
echo "                      <input type='text' name='query' value='Search groups...' > ";
echo "                      <input type='hidden' class='search-label' value='research-groups'> ";
echo "                  </div> ";
echo "                  <button class='search-btn' id='simple-search' type='submit'>Search</button> ";
echo "              </form> ";
echo "          </div> ";
echo "          <div class='advanced-search-example left'>eg: <a href='/research-groups/search/?query=scientific+impact+measures' style='color:inherit' rel='nofollow'>Social networks</a></div> ";
echo "          <div class='clear'></div> ";
echo "      </div>";
echo "  </div>";
echo "</div>";
//LIST DOCUMENTS
echo "<table style='width:100%'><tr><td style='width:45%; vertical-align:top; padding:15px;'>"; //TABLE
if($show_user_group){
    include($CFG->dirroot.'/mod/modmendeley/my_groups.php');
}else{
    include($CFG->dirroot.'/mod/modmendeley/public_groups.php');
}
echo "</td><td style='width:45%; vertical-align:top; padding:15px;'>"; //TABLE
//LIST STATS
echo "<div class='column-b unfloat'>";
if($show_stats){
echo "   <div class='padding'>";
echo "      <h2 class='heading-line'><span>Browse disciplines</span></h2>";
echo "      <ol class='discipline item-list'>";
foreach ($categories as $categorie){
    echo "          <li>";
    echo "              <a class='item' href='http://www.mendeley.com/$categorie->slug/' target='blank'>$categorie->name</a>";
    echo "          </li>";
}
echo "      </ol>";
echo "   </div>";
}
echo "</div>";
echo "</td></tr></table>"; //TABLE

echo "</div>";

?>

<script>
var new_group = '<?php echo $modmendeley->permission_new_group ?>';
if(new_group == '0') document.getElementById('create_group').style.display = 'none';
</script>