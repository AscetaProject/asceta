<?php
/**
* Display Library stats view
*
*
 * @package   mod_modmendeley
 * @copyright 2011 María del Mar Jiménez Torres (mjimenez@fidesol.org) - Fundación I+D del Software Libre (www.fidesol.org)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/

echo "<div id='content-container'>";
echo "  <div id='main-content'>";
echo "      <ul id='sub-menu'>";
echo "          <li><a href='$CFG->wwwroot/mod/modmendeley/view.php?id=$cm->id&amp;option=stats&amp;action=general&amp;sesskey=" . sesskey() . "' style='border-top-left-radius: 7px 7px; border-top-right-radius: 7px 7px; border-bottom-left-radius: 7px 7px; border-bottom-right-radius: 7px 7px; '>General stats</a></li>";
echo "          <li><h1 style='border-top-left-radius: 7px 7px; border-top-right-radius: 7px 7px; border-bottom-left-radius: 7px 7px; border-bottom-right-radius: 7px 7px; '>My library stats</h1></li>";
echo "      </ul>";
echo "<table style='width:100%'><tr><td style='width:5%; vertical-align:top; padding:15px;'>"; //TABLE
echo "      <div class='leftcontent-rightbox left'>";
echo "          <div class='stats-left'>";
echo "              <table border='0' cellspacing='0' cellpadding='0' class='number-articles-tbl'>";
echo "                  <tbody>";
echo "                      <tr>";
if (checkStatsLibraryImage()){
    echo "                          <td class='tags' align='left' valign='bottom' style='width: 600px; height: 330px;'>";
    echo "                              <img src='http://www.mendeley.com/image/library_stats/?_e158bb76bab7f628fd6b30d6f64a232b' width='600' height='330'>";
}else{
    echo "                          <td class='tags' align='left' valing='bottom'>";
    echo "                              <br><a href='http://www.mendeley.com/image/library_stats/?_e158bb76bab7f628fd6b30d6f64a232b' target='_blank'><img src='http://www.mendeley.com/graphics/commonnew/stats_icon_1616465761622334.png' alt='no stats icon'></a>&nbsp;Trends temporarily unavailable";
}
echo "                          </td>";
echo "                          <td valign='top'>";
echo "                              <div style='position: relative; height: 100%;'></div>";
echo "                          </td>";
echo "                      </tr>";
echo "          	</tbody>";
echo "              </table>";
echo "          </div>";
echo "          <div class='clear'></div>";
echo "      	<div class='stats-left'>";
echo "              <div class='left stats-section' style='padding-right:40px;'>";
echo "                  <h3 class='stats-underlined'>Top authors</h3>";
echo "                  <table class='stats-table'>";
echo "                      <tbody>";
$c = 0;
foreach ($authors as $author){
    echo "                          <tr class='row$c stats_row'>";
    echo "                              <td class='border' style=''></td>";
    echo "                              <td class='number stats_number'>";
    echo "                      		<div class='stats-number'>$author->value</div>";
    echo "                                  <div class='stats-number-description'>ARTICLES</div>";
    echo "                              </td>";
    echo "                              <td class='stats_description'>";
    echo "                                  <h2>$author->name</h2>";
    echo "                              </td>";
    echo "                          </tr>";
    $c += 1;
}
echo "                          </tbody>";
echo "                      </table>";
echo "                  </div>";
echo "          	<div class='left stats-section'>";
echo "                      <h3 class='stats-underlined'>Top publication outlets</h3>";
echo "                          <table class='stats-table'>";
echo "                              <tbody>";
$c = 0;
array_splice($publications, 5);
foreach ($publications as $publication){
    echo "                                  <tr class='row$c stats_row'>";
    echo "                                      <td class='border' style=''></td>";
    echo "                                      <td class='number stats_number'>";
    echo "                                          <div class='stats-number'>$publication->value</div>";
    echo "                                          <div class='stats-number-description'>ARTICLE</div>";
    echo "                                       </td>";
    echo "                                       <td class='stats_description'>";
    echo "                                           <h2>$publication->name</h2>";
    echo "                                       </td>";
    echo "                                   </tr>";
    $c += 1;
}
echo "                                </tbody>";
echo "                          </table>";
echo "                      </div>";
echo "                      <div class='clear'></div>";
echo "                  </div>";
echo "                  <div class='clear'></div>";
echo "          	<div class='stats-left'>";
echo "                  <h3 class='stats-underlined'>Most frequently used tags</h3>";
echo "                  <table border='0' cellspacing='0' cellpadding='0' class='most-used-tags-tbl'>";
echo "                      <tbody>";
echo "                          <tr>";
echo "                              <td class='left-border' width='22'>&nbsp;</td>";
foreach ($tags as $tag){
    echo "                              <td class='tags'>";
    if(!is_array($tag->tags)) {
        echo "                                      <p class='line-5'>no new tags</p>";
    }else{
        array_splice($tag->tags, 10);
        $c = count($tag->tags);
        foreach (array_reverse($tag->tags) as $t){
            if ($c == 0) break;
                echo "                                  <p class='line-$c'><a href='https://www.mendeley.com/tags/".str_replace(' ','+',$t->name)."/' target='_blank'>$t->name</a></p>";
            $c -= 1;
            }
        echo "                               </td>";
    }
}
echo "                          </tr>";
echo "                          <tr>";
echo "                              <td class='left-border border-top-first'>&nbsp;</td>";
foreach ($tags as $tag){
    echo "                              <td class='border-top'><img src='http://www.mendeley.com/graphics/common/most-used-tags_border_1786541776003621.gif' width='2' height='18' alt='' title=''>$tag->period</td>";
}
echo "                              <td class='border-top-last' width='54'>&nbsp;</td>";
echo "                          </tr>";
echo "                      </tbody>";
echo "                  </table>";
echo "                  </div>";
echo "              </div>";
echo "</td><td style='width:45%; vertical-align:top; padding:15px;'>"; //TABLE
echo "              <div class='leftcontent-rightbox left'>";
echo "                  <div class='stats-right'>";
echo "                      <h3 class='underlined'>In My Library</h3>";
echo "                      <div class='general-info'>";
echo "                          <p><span class='number'>$library->total_results</span> articles</p>";
echo "                      </div>";
echo "                  </div>";
echo "              </div>";
echo "              <div class='clear'></div>";
echo "</td></tr></table>"; //TABLE
echo "          </div>";
echo "      </div>";

?>
