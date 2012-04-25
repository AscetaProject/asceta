<?php
/**
* Display paper view
*
*
 * @package   mod_modmendeley
 * @copyright 2011 María del Mar Jiménez Torres (mjimenez@fidesol.org) - Fundación I+D del Software Libre (www.fidesol.org)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/

echo "<div id='content-container'>";
echo "<div class='heading'>";
echo "  <div class='padding unfloat'>";
echo "      <div class='papers-overview'>";
echo "		<h1>Papers</h1>";
echo "      </div>";
echo "  </div>";
echo "</div>";
//SEARCH
echo "<div id='search-container'>";
echo "  <div id='search-bar'>";
echo "      <div class='specific-search'>";
echo "          <div class='search'>";
echo "              <form action='view.php' method='GET' onsubmit=''> ";
echo "                  <div class='search-box'> ";
$search_value = ($attributes != '') ? str_replace('&', ' ', $attributes) : 'Search papers...';
echo "                      <input type='text' name='query' value='$search_value' > ";
echo "                      <input type='hidden' class='search-label' value='research-papers'> ";
echo "                  </div> ";
echo "                  <button class='search-btn' id='simple-search' type='submit'>Search</button> ";
echo "                   <input type='hidden' name='id' value='$id' />\n";
echo "                   <input type='hidden' name='sesskey' value=".sesskey()." />\n";
echo "                   <input type='hidden' name='option' value='paper' />\n";
echo "                   <input type='hidden' name='action' value='searching' />\n";
echo "              </form> ";
echo "          </div> ";
echo "          <div class='advanced-search-example left'>eg: <a href='$CFG->wwwroot/mod/modmendeley/view.php?id=$cm->id&amp;option=paper&amp;action=searching&amp;sesskey=" . sesskey() . "&amp;query=scientific+impact+measures' style='color:inherit' rel='nofollow'>scientific impact measures</a></div> ";
echo "          <div class='advanced-search-link right'><a href='$CFG->wwwroot/mod/modmendeley/view.php?id=$cm->id&amp;option=paper&amp;action=search&amp;sesskey=" . sesskey() . "' rel='nofollow'>Advanced search</a></div> ";
echo "          <div class='clear'></div> ";
echo "      </div>";
echo "  </div>";
echo "</div>";
//LIST DOCUMENTS
echo "<table style='width:100%'><tr><td style='width:45%; vertical-align:top; padding:15px;'>"; //TABLE
echo "<div class='column-a'>";
echo "  <div class='padding unfloat'>";
if ($show_stats){
    echo "   <h2 class='heading-line'><span>Papers</span></h2>";
} else {
    echo "   <div class='pagination-container'>";
    $min_item = ($search->current_page*$search->items_per_page)+1;
    $max_item = ($min_item + $search->items_per_page -1 < $search->total_results )?$min_item + $search->items_per_page -1: $search->total_results;
    echo "      <div class='pagination-results'>Results<strong> ".number_format($min_item)." - ".number_format($max_item)." </strong> of <strong> ".number_format($search->total_results)." </strong></div>";
    echo "          <div class='pagination-pages'>";
    echo "              <div class='pagemenu'>";
    showPaginationString(intval($search->current_page)+1, intval($search->total_pages), 1, $search_data, true);
    echo "              </div>";
    echo "           </div>";
    echo "          <div style='clear:both'></div>";
    echo "      </div>";
}
echo "      <div id='papers-popular' class='papers switchable shrinkable' data-shirnk='10'>";
foreach ($papers as $paper) {
    $authors = array();
    foreach ($paper->authors as $v){
        $authors [] = "$v->forename $v->surname";
    }
    echo "          <ol class='item-list documents'>";
    echo "          <li>";
    echo "              <article class='item document' id='document-$paper->title' data-doc='".$paper_encode = json_encode($paper)."'>";
    echo "                  <div class='item-info'>";
    echo "                      <div class='title'><a href='$paper->mendeley_url' target='blank'>$paper->title</a></div>";
    echo "                      <div class='metadata'>";
    echo "                          <span class='authors'><span class='author'>".implode(', ',$authors)."</span></span>";
    echo "                          <span class='sep'>in</span> <span class='publication'>$paper->publication</span>";
    echo "                          <span class='year'>($paper->year)</span>";
    echo "                      </div>";
    echo "                      <div class='actions'>";
    echo "                          <span class='reader-count' title='$paper->value readers on Mendeley'><strong>".number_format($paper->value)."</strong> readers</span>";
    echo "                          <a rel='nofollow' href='http://www.mendeley.com/research-papers/?rec=".str_replace(' ','-',strtolower(str_replace('.','',$paper->title)))."' target='blank'>";
    echo "                              <span><strong>Related</strong> research</span>";
    echo "                          </a>";
    echo "                      </div>";
    echo "                  </div>";
    echo "              </article>";
    echo "              <div class='heading-line'><span></span></div>";
    echo "          </li>";
    echo "          </ol>";
}
if(!$show_stats){
    echo "      <div class='pagination-container'>";
    echo "          <div class='pagination-pages'>";
    echo "              <div class='pagemenu'>";
    showPaginationString(intval($search->current_page)+1, intval($search->total_pages), 1, $search_data, true);
    echo "              </div>";
    echo "          </div>";
    echo "          <div style='clear:both'></div>";
    echo "      </div>";
}
echo "      </div>";
echo "  </div>";
echo "</div>";
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
echo "   <div class='padding'>";
echo "      <h2 class='heading-line'><span>Top publication outlets</span></h2>";
echo "      <table id='stats-top-outlets'>";
$count = 1;
foreach ($publications as $publication){
    echo "      <tr>";
    echo "          <td class='stats-position'><span>$count</span></td>";
    echo "          <td class='stats-name'><a href='http://www.mendeley.com/research-papers/search?query=published_in:%22$publication->name%22' target='_blank' rel='nofollow'>$publication->name</a></td>";
    echo "          <td class='stats-count'>".number_format($publication->value)."<span class='plain'> readers</span></td>";
    echo "      </tr>";
    $count += 1;
}
echo "      </table>";
if ($user->oauth == "1"){
    echo "  <div class='more'><a href='$CFG->wwwroot/mod/modmendeley/view.php?id=$cm->id&amp;option=stats&amp;sesskey=" . sesskey() . "' class='link-button right' rel='nofollow'>More statistics</a></div>";
}
echo "   </div>";
}
echo "</div>";
echo "</td></tr></table>"; //TABLE

echo "</div>";

?>
