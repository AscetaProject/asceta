<?php
/**
* Send a search from advanced search form
*
*
 * @package   mod_modmendeley
 * @copyright 2011 María del Mar Jiménez Torres (mjimenez@fidesol.org) - Fundación I+D del Software Libre (www.fidesol.org)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/

$attributes = '';
if($search_data['query'] != null){
    $attributes .= $search_data['query'].' ';
}
if($search_data['title'] != null){
    $attributes .= 'title:'.$search_data['title'].' ';
}
if($search_data['author'] != null){
    $attributes .= 'author:'.$search_data['author'].' ';
}
if($search_data['abstract'] != null){
    $attributes .= 'abstract:'.$search_data['abstract'].' ';
}
if($search_data['meshterm'] != null){
    $attributes .= 'meshterm:'.$search_data['meshterm'].' ';
}
if($search_data['type'] != null){
    $attributes .= 'type:'.$search_data['type'].' ';
}
if($search_data['date'] != null){
    $attributes .= 'year:'.$search_data['date'];
}

if ($page){
    $params['page'] = intval($page) -1;
}
if ($search_data['results']){
    $params['items'] = intval($search_data['results']);
}
$search = getPublicMethods('GET', $user, rtrim($user->url,'/').'/documents/search/'.str_replace(' ', '+', $attributes), $params);
$papers = $search->documents;

?>
