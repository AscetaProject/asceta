<?php
/**
* Display public group list
*
*
 * @package   mod_modmendeley
 * @copyright 2011 María del Mar Jiménez Torres (mjimenez@fidesol.org) - Fundación I+D del Software Libre (www.fidesol.org)
 \* @license   http://www.apache.org/licenses/LICENSE-2.0.html Apache License, Version 2.0
*/

echo "<div class='column-a'>";
echo "  <div class='padding'>";
if ($show_stats){
    echo "   <h2 class='heading-line extra-padding'><span>Popular groups</span></h2>";
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
echo "      <div id='groups-content' class='groups switchable shrinkable' data-shirnk='10'>";
foreach ($groups->groups as $g) {
    $group = getPublicMethods('GET', $user, rtrim($user->url,'/').'/documents/groups/'.$g->id, $params);
    $tags = getTagsFromDiscipline($group->disciplines, $user, $params);
    $people = $group->people;
    echo "          <ol class='item-list groups'>";
    echo "          <li>";
    echo "              <article class='item group not-private not-member not-follower not-invited not-owner not-admin' data-group-id='-$group->id'>";
    echo "                  <div class='item-info'>";
    echo "                      <div class='title'><a href='$group->public_url' target='blank'>$group->name</a></div>";
    echo "                      <div class='description'>$group->description</div>";
    echo "                      <div class='tags-list one-line'>";
    foreach ($tags as $tag){
        echo "                      <a href='/tags/$tag/' class='tag' rel='tag'>$tag<span></span></a>";
    }
    echo "                      </div>";
    echo "                      <table class='group-footer'>";
    echo "                          <tbody>";
    echo "                              <tr>";
    echo "                                  <td class='actions'></td>"; // TODOOOO CUANDO SEA PRIVADO
    echo "                                  <td class='count'>";
if (intval($people->members) > 0){
    echo "                                      <a class='member-count' title='$people->members members on Mendeley' href='http://www.mendeley.com/groups/1289343/interactive-analog-media/members/'><strong>$people->members</strong> members</a>";
}
if (intval($group->total_documents) > 0){
    echo "                                      <span class='separator right' style='padding:0 5px'>·</span>";
    echo "                                      <a class='paper-count' title='$group->total_documents readers on Mendeley' href='http://www.mendeley.com/groups/1289343/interactive-analog-media/papers/'><strong>$group->total_documents</strong> papers</a>";
}
    echo "                                  </td>";
    echo "                              </tr>";
    echo "                          </tbody>";
    echo "                      </table>";
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
?>
