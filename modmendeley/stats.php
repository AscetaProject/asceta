<?php
/**
* Display General stats view
*
*
 * @package   mod_modmendeley
 * @copyright 2011 María del Mar Jiménez Torres (mjimenez@fidesol.org) - Fundación I+D del Software Libre (www.fidesol.org)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/

echo "<div id='content-container'>";
echo "  <div id='main-content'>";
echo "      <ul id='sub-menu'>";
echo "  	<li><h1 style='border-top-left-radius: 7px 7px; border-top-right-radius: 7px 7px; border-bottom-left-radius: 7px 7px; border-bottom-right-radius: 7px 7px; '>General stats</h1></li>";
echo "  	<li><a href='$CFG->wwwroot/mod/modmendeley/view.php?id=$cm->id&amp;option=stats&amp;action=library&amp;element_selected=$element_selected&amp;sesskey=" . sesskey() . "' style='border-top-left-radius: 7px 7px; border-top-right-radius: 7px 7px; border-bottom-left-radius: 7px 7px; border-bottom-right-radius: 7px 7px; '>My library stats</a></li>";
echo "      </ul>";
echo "<table style='width:100%'><tr><td style='width:5%; vertical-align:top; padding:15px;'>"; //TABLE
echo "      <div class='leftcontent-rightbox left'>";
echo "      <div class='stats-left'>";
echo "		<h3 class='underlined'>Most read articles</h3>";
echo "          <div class='left stats-right'>";
echo "              <div class='stats-subtitle'>Most read articles in all disciplines</div><br>";
echo "                  <table class='stats-table'>";
echo "                      <tbody>";
$c = 0;
foreach($articles as $article){
    $article_authors = array();
    foreach ($article->authors as $v){
        $article_authors [] = "$v->forename $v->surname";
    }
    echo "                          <tr class='row$c stats_row'>";
    echo "                              <td class='border'></td>";
    echo "                              <td class='number stats_number'>";
    echo "                                  <div class='stats-number'>".number_format($article->value)."</div>";
    echo "                                  <div class='stats-number-description'>READERS</div>";
    echo "                              </td>";
    echo "                              <td class='stats_description'><h2><a href='$article->mendeley_url' target='_blank'>$article->title</a></h2>";
    echo "                                  <span class='author'>".implode(', ',$article_authors)."</span>";
    echo "                                  <span class='year'>($article->year)</span>";
    echo "                                  <br><em>$article->publication</em>";
    echo "                              </td>";
    echo "  			</tr>";
    $c ++;
}
echo "                  </tbody>";
echo "              </table>";
echo "  	</div>";
echo "          <div class='right stats-right'>";
echo "              <div class='stats-subtitle'>Most read articles in <a href='http://www.mendeley.com/computer-and-information-science/'>Computer and Information Science</a></div>";
echo "              <br><table class='stats-table'>";
echo "                  <tbody>";
$c = 0;
foreach($articles_discipline as $article){
    $article_authors = array();
    foreach ($article->authors as $v){
        $article_authors [] = "$v->forename $v->surname";
    }
    echo "                          <tr class='row$c stats_row'>";
    echo "                              <td class='border'></td>";
    echo "                              <td class='number stats_number'>";
    echo "                                  <div class='stats-number'>".number_format($article->value)."</div>";
    echo "                                  <div class='stats-number-description'>READERS</div>";
    echo "                              </td>";
    echo "                              <td class='stats_description'><h2><a href='$article->mendeley_url' target='_blank'>$article->title</a></h2>";
    echo "                                  <span class='author'>".implode(', ',$article_authors)."</span>";
    echo "                                  <span class='year'>($article->year)</span>";
    echo "                                  <br><em>$article->publication</em>";
    echo "                              </td>";
    echo "  			</tr>";
    $c += 1;
}
echo "                  </tbody>";
echo "              </table>";
echo "         	</div>";
echo "          <div class='clear'></div>";
echo "       </div>";
echo "      <div class='stats-left'>";
echo "          <h3 class='underlined'>Most read authors</h3>";
echo "          <div class='left'>";
echo "              <div class='stats-subtitle'>Most read authors in all disciplines</div>";
echo "              <br><table width='50%' border='0' cellspacing='0' cellpadding='0' class='stats-table'>";
echo "                   <tbody>";
$c = 0;
foreach ($authors as $author){
    echo "                       <tr class='row$c stats_row'>";
    echo "                           <td class='border'></td>";
    echo "                           <td class='number stats_number'>";
    echo "                               <div class='stats-number'>".number_format($author->value)."</div>";
    echo "                               <div class='stats-number-description'>READERS</div>";
    echo "                           </td>";
    echo "                           <td class='stats_description'>";
    echo "                               <h2>$author->name</h2>";
    echo "                           </td>";
    echo "                       </tr>";
    $c += 1;
}
echo "                   </tbody>";
echo "               </table>";
echo "           </div>";
echo "           <div class='right stats-right'>";
echo "              <div class='stats-subtitle'>Most read authors in <a href='http://www.mendeley.com/computer-and-information-science/' target='_blank'>Computer and Information Science</a></div>";
echo "              <br><table width='100%' border='0' cellspacing='0' cellpadding='0' class='stats-table'>";
echo "                  <tbody>";
$c = 0;
foreach ($authors_discipline as $author){
    echo "                       <tr class='row$c stats_row'>";
    echo "                           <td class='border'></td>";
    echo "                           <td class='number stats_number'>";
    echo "                               <div class='stats-number'>".number_format($author->value)."</div>";
    echo "                               <div class='stats-number-description'>READERS</div>";
    echo "                           </td>";
    echo "                           <td class='stats_description'>";
    echo "                               <h2>$author->name</h2>";
    echo "                           </td>";
    echo "                       </tr>";
    $c += 1;
}echo "                  </tbody>";
echo "              </table>";
echo "		</div>";
echo "          <div class='clear'></div>";
echo "      </div>";
echo "      <div class='stats-left'>";
echo "		<h3 class='underlined'>Most read publication outlets</h3>";
echo "          <div class='left stats-right'>";
echo "              <div class='stats-subtitle'>Most read publication outlets</div>";
echo "              <table width='100%' border='0' cellspacing='0' cellpadding='0' class='stats-table'>";
echo "                  <tbody>";
$c = 0;
foreach ($publications as $publication){
    echo "                      <tr class='row$c stats_row'>";
    echo "                          <td class='border'></td>";
    echo "                          <td class='number stats_number'>";
    echo "                               <div class='stats-number'>".number_format($publication->value)."</div>";
    echo "                              <div class='stats-number-description'>READERS</div>";
    echo "                          </td>";
    echo "                          <td class='stats_description'>";
    echo "                              <h2>$publication->name</h2>";
    echo "                          </td>";
    echo "                      </tr>";
    $c += 1;
}
echo "                  </tbody>";
echo "              </table>";
echo "          </div>";
echo "          <div class='right stats-right'>";
echo "              <div class='stats-subtitle'>Most read outlets in <a href='http://www.mendeley.com/computer-and-information-science/' target='_blank'>Computer and Information Science</a></div>";
echo "              <br><table width='100%' border='0' cellspacing='0' cellpadding='0' class='stats-table'>";
echo "                  <tbody>";
$c = 0;
foreach ($publications_discipline as $publication){
    echo "                      <tr class='row$c stats_row'>";
    echo "                          <td class='border'></td>";
    echo "                          <td class='number stats_number'>";
    echo "                               <div class='stats-number'>".number_format($publication->value)."</div>";
    echo "                              <div class='stats-number-description'>READERS</div>";
    echo "                          </td>";
    echo "                          <td class='stats_description'>";
    echo "                              <h2>$publication->name</h2>";
    echo "                          </td>";
    echo "                      </tr>";
    $c += 1;
}
echo "                  </tbody>";
echo "              </table>";
echo "          </div>";
echo "          <div class='clear'></div>";
echo "      </div>";
echo "      <div class='stats-left'>";
echo "          <h3 class='underlined'>Up and coming articles and publications outlets</h3>";
echo "          <div class='left stats-right'>";
echo "              <div class='stats-subtitle'>Articles in <a href='http://www.mendeley.com/computer-and-information-science/' target='_blank'>Computer and Information Science</a></div>";
echo "              <br><img src='http://www.mendeley.com/graphics/commonnew/stats_icon_1616465761622334.png' alt='no stats icon'>&nbsp;Trends temporarily unavailable</div>";
echo "              <div class='right stats-right'>";
echo "                  <div class='stats-subtitle'>Publications outlets in <a href='http://www.mendeley.com/computer-and-information-science/' target='_blank'>Computer and Information Science</a></div>";
echo "                  <br><img src='http://www.mendeley.com/graphics/commonnew/stats_icon_1616465761622334.png' alt='no stats icon'>&nbsp;Trends temporarily unavailable</div>";
echo "                  <div class='clear'></div>";
echo "              </div>";
echo "              <div class='stats-left'>";
echo "                  <h3 class='underlined'>Most frequently used tags in <a href='http://www.mendeley.com/computer-and-information-science/' target='_blank'>Computer and Information Science</a></h3>";
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
echo "              </div>";
echo "          </div>";
echo "</td><td style='width:45%; vertical-align:top; padding:15px;'>"; //TABLE
/*echo "          <div class='leftcontent-rightbox left'>";
echo "              <div class='total-stats stats-right'>";
echo "                  <h3 class='underlined'>Added articles</h3>";
echo "                  <ul class='stats-right-list'>";
echo "                      <li><span class='stats-big-number'>102,897,576</span> added overall</li>";
echo "                      <li><span class='stats-big-number'>9,402,634</span> added in <span class='discipline'><a href='http://www.mendeley.com/computer-and-information-science/'>Computer and Information Science</a></span></li>";
echo "                  </ul>";
echo "              </div>";
echo "              <div class='total-stats stats-right'>";
echo "                  <h3 class='underlined'>Downloadable articles</h3>";
echo "                  <ul class='stats-right-list'>";
echo "                      <li><span class='stats-big-number'>1,057,735</span> downloadable overall for free </li>";
echo "                      <li><span class='stats-big-number'>110,689</span> downloadable for free in <span class='discipline'><a href='http://www.mendeley.com/computer-and-information-science/'>Computer and Information Science</a></span></li>";
echo "                  </ul>";
echo "              </div>";
echo "              <div class='stats-right'>";
echo "                  <h3 class='underlined'>Mendeley users by discipline</h3>";
echo "                  <table border='0' cellspacing='0' cellpadding='0' class='academ-discipl-tbl'>";
echo "                      <tbody>";
echo "                          <tr>";
echo "                              <td valign='top' width='280' class='rounded-indicat' style='padding-bottom:5px'>";
echo "                                  <div style='width: 36px'>";
echo "                                      <span style='width:300px'><a href='http://www.mendeley.com/search/discipline/1'>Arts and Literature</a></span>";
echo "                                   </div>";
echo "                              </td>";
echo "                          </tr>";
echo "                      </tbody>";
echo "                  </table>";
echo "              </div>";
echo "          </div>";
echo "          <div class='clear'></div>";*/
echo "</td></tr></table>"; //TABLE
echo "      </div>";
echo "	</div>";
?>
