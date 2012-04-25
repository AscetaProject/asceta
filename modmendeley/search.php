<?php
/**
* Display advanced search
*
*
 * @package   mod_modmendeley
 * @copyright 2011 María del Mar Jiménez Torres (mjimenez@fidesol.org) - Fundación I+D del Software Libre (www.fidesol.org)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/
$params = array ('consumer_key' => $user->consumer_key);
$categories = getPublicMethods('GET', $user, rtrim($user->url,'/').'/documents/categories', $params);
echo "<div id='content-container'>\n";
echo "      <div class='heading'>\n";
echo "          <div class='padding unfloat'>\n";
echo "              <div class='papers-overview'>\n";
echo "                  <h1>Papers</h1>\n";
echo "              </div>\n";
echo "          </div>\n";
echo "      </div>\n";
echo "<table style='width:100%'><tr><td style='width:60%; vertical-align:top; padding:15px;'>\n"; //TABLE
echo "      <div class='column-a'>\n";
echo "          <div class='padding'>\n";
echo "              <form action='view.php' method='GET' id='advanced-search-form' onSubmit='return yearFormat()'>\n";
echo "                  <div id='advanced-wrapper' style='padding-top:10px'>\n";
echo "                      <script>function f() {document.getElementById('search-bar').remove();}; </script>\n";
echo "                      <h2 class='heading-line'><span>Advanced search options</span></h2>\n";
echo "                      <div class='advanced-search'>\n";
echo "                          <div style='font-weight:bold; margin-bottom: 10px;'>Search</div>\n";
echo "                          <ul>\n";
echo "                              <li class='items-options'>\n";
echo "                                  <label for='advanced-query-search'>Everywhere</label>\n";
echo "                                  <input name='query' class='advanced-search-fields' type='text' value='' id='advanced-query-search'/>\n";
echo "                              </li>\n";
echo "                              <li class='items-options'>\n";
echo "                                  <label for='advanced-title-search'>Within title</label>\n";
echo "                                  <input name='title' class='advanced-search-fields' type='text' value='' id='advanced-title-search'/>\n";
echo "                              </li>\n";
echo "                              <li class='items-options'>\n";
echo "                                  <label for='advanced-author-search'>Within authors</label>\n";
echo "                                  <input name='author' class='advanced-search-fields' type='text' value='' id='advanced-author-search'/>\n";
echo "                              </li>\n";
echo "                              <li class='items-options'>\n";
echo "                                  <label for='advanced-abstract-search'>Within abstract</label>\n";
echo "                                  <input name='abstract' class='advanced-search-fields' type='text' value='' id='advanced-abstract-search'/>\n";
echo "                              </li>\n";
echo "                              <li class='items-options'>\n";
echo "                                  <label for='advanced-meshterm-search'>Within MeSH terms</label>\n";
echo "                                  <input name='meshterm' class='advanced-search-fields' type='text' value='' id='advanced-meshterm-search'/>\n";
echo "                              </li>\n";
echo "                          </ul>\n";
echo "                       </div>\n";
echo "                       <div class='advanced-search'>\n";
echo "                          <label for='advanced-documenttype-search'>Document type: </label>\n";
echo "                          <select id='advanced-documenttype-search' name='type' style='margin-left: 5px;'>\n";
echo "                               <option value=''>any type</option>\n";
echo "                               <option value='bill'>Bill</option>\n";
echo "                               <option value='book'>Book</option>\n";
echo "                               <option value='book-section'>Book   Section</option>\n";
echo "                               <option value='case'>Case</option>\n";
echo "                               <option value='computer-program'>Computer   Program</option>\n";
echo "                               <option value='conference-proceedings'>Conference   Proceedings</option>\n";
echo "                               <option value='encyclopedia-article'>Encyclopedia   Article</option>\n";
echo "                               <option value='film'>Film</option>\n";
echo "                               <option value='generic'>Generic</option>\n";
echo "                               <option value='hearing'>Hearing</option>\n";
echo "                               <option value='journal'>Journal Article</option>\n";
echo "                               <option value='magazine-article'>Magazine   Article</option>\n";
echo "                               <option value='newspaper-article'>Newspaper   Article</option>\n";
echo "                               <option value='patent'>Patent</option>\n";
echo "                               <option value='report'>Report</option>\n";
echo "                               <option value='statute'>Statute</option>\n";
echo "                               <option value='television-broadcast'>Television   Broadcast</option>\n";
echo "                               <option value='thesis'>Thesis</option>\n";
echo "                               <option value='webpage'>Webpage</option>\n";
echo "                               <option value='working-paper'>Working   Paper</option>\n";
echo "                          </select>\n";
echo "                       </div>\n";
echo "                       <div class='advanced-search'>\n";
echo "                           <label for='advanced-date-search'>Publication year:</label>\n";
echo "                               <input style='width:30px; font-size:11px !important;' class='advanced-search-fields year-field' name='date' id='advanced-datefrom-search' value='' type='text'>\n";
echo "                           <div id='advanced-search-years' class='noshow'>\n";
echo "                               <label for='advanced-datefrom-search'>From</label>\n";
echo "                               <input style='font-size:11px !important;' class='advanced-search-fields year-field' name='yearfrom' id='advanced-datefrom-search' value='' type='text'>\n";
echo "                               <label for='advanced-dateto-search'>to</label>\n";
echo "                               <input  style='font-size:11px !important;' class='advanced-search-fields year-field' name='yearto' id='advanced-dateto-search' value='' type='text'>\n";
echo "                           </div>\n";
echo "                           <div id='year-warning' class='noshow'>Year fields must be a full 4 character year, such as '1999'</div>\n";
echo "                       </div>\n";
echo "                       <div class='advanced-search'>\n";
echo "                           <label for='advanced-results-search'>Results per page</label>\n";
echo "                           <select class='advanced-search-fields' style='margin-left:5px' name='results' id='advanced-results-search'>\n";
echo "                               <option>20</option><option>50</option><option>100</option><option>200</option>\n";
echo "                           </select>\n";
echo "                       </div>\n";
echo "                       <div class='advanced-search'>\n";
echo "                           <input type='submit' class='primary' value='Search'/>\n";
echo "                       </div>\n";
echo "                   </div>\n";
echo "                   <input type='hidden' name='id' value='$id' />\n";
echo "                   <input type='hidden' name='sesskey' value=".sesskey()." />\n";
echo "                   <input type='hidden' name='option' value='paper' />\n";
echo "                   <input type='hidden' name='action' value='searching' />\n";
echo "              </form>\n";
echo "              <script type='text/javascript'>\n";
echo "                  function yearFormat(){\n"; //Check that the value has the format YYYY
echo "                      if( document.getElementById('advanced-datefrom-search').length > 0 && document.getElementById('advanced-datefrom-search').length != 4){\n";
echo "                          document.getElementById('year-warning').style.display = 'block';\n";
echo "                      }";
echo "                  };\n";
echo "              </script>\n";
echo "          </div>\n";
echo "          <div class='clear'></div>\n";
echo "      </div>\n";
echo "  <div class='column-b unfloat'>\n";
echo "  </div>\n";
echo "</td><td style='width:45%; vertical-align:top; padding:15px;'>\n"; //TABLE
echo "</td></tr></table>\n"; //TABLE
echo "</div>\n";

?>
