<?php

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.


/**
 * Internal library of functions for module modmendeley
 *
 * All the modmendeley specific functions, needed to implement the module
 * logic, should go here. Never include this file from your lib.php!
 *
 * @package   mod_modmendeley
 * @copyright 2011 María del Mar Jiménez Torres (mjimenez@fidesol.org) - Fundación I+D del Software Libre (www.fidesol.org)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();


if (!function_exists('implode_assoc')) {
/**
 * Joins key:value pairs by inner_glue and each pair together by outer_glue
 * @param string $inner_glue The HTTP method (GET, POST, PUT, DELETE)
 * @param string $outer_glue Full URL of the resource to access
 * @param array $array Associative array of query parameters
 * @return string Urlencoded string of query parameters
 */
function implode_assoc($inner_glue, $outer_glue, $array) {
  $output = array();
  foreach($array as $key => $item) {
    $output[] = $key . $inner_glue . urlencode($item);
  }
  return implode($outer_glue, $output);
}

}


if (!function_exists('explode_assoc')) {
/**
 * Split key:value pairs by inner_glue and each pair by outer_glue from a string
 * @param string $inner_glue The HTTP method (GET, POST, PUT, DELETE)
 * @param string $outer_glue Full URL of the resource to access
 * @param string $string String of query parameters
 * @return array Parameters array
 */
function explode_assoc($inner_glue, $outer_glue, $string) {
    $data = array();
    foreach (explode($outer_glue,$string) as $param) {
        $d = explode($inner_glue,$param);
        $data[trim($d[0])] = $d[1];
    }
    return $data;
}
}
/**
 * Get user specific methods
 * @param <object> $user user information
 * @param <string> $uri indicates the rest of the url
 * @return <array> an array with the result of the response
 */
function getLibraryValue($method, $user, $uri, $params = null){
    $basefeed = rtrim($user->url, '/') . "/".$uri;
    $consumer_key = $user->consumer_key;
    $consumer_secret = $user->consumer_secret;
    $access_token = $user->access_token;
    $access_secret = $user->access_secret;
    if($method == 'GET'){
        $consumer = new OAuthConsumer($consumer_key, $consumer_secret, NULL);
        $token = new OAuthToken($access_token, $access_secret, NULL);
        $request = OAuthRequest::from_consumer_and_token($consumer, $token, $method, $basefeed, $params);
        $request->sign_request(new OAuthSignatureMethod_HMAC_SHA1(), $consumer, $token);
        $response = modmendeley_send_request('GET', $request->to_url());
    }
    return json_decode($response);
}

/**
 * Post user specific methods
 * @param <object> $user user information
 * @param <string> $uri indicates the rest of the url
 * @return <array> an array with the result of the response
 */
function postLibraryValue($method, $user, $uri, $post_data){
    $basefeed = rtrim($user->url, '/') .$uri;
    $consumer_key = $user->consumer_key;
    $consumer_secret = $user->consumer_secret;
    $access_token = $user->access_token;
    $access_secret = $user->access_secret;
    if($method == 'POST'){
        $consumer = new OAuthConsumer($consumer_key, $consumer_secret, NULL);
        $token = new OAuthToken($access_token, $access_secret, NULL);
        $request = OAuthRequest::from_consumer_and_token($consumer, $token, $method, $basefeed, $post_data);
        $request->sign_request(new OAuthSignatureMethod_HMAC_SHA1(), $consumer, $token);
        $response = modmendeley_send_request('POST', $request->get_normalized_http_url(), array(), $request->to_postdata());
    }
    return json_decode($response);
}

/**
 * Delete user specific methods
 * @param <object> $user user information
 * @param <string> $uri indicates the rest of the url
 * @return <array> an array with the result of the response
 */
function deleteLibraryValue($method, $user, $uri, $post_data){
    $basefeed = rtrim($user->url, '/') .$uri;
    $consumer_key = $user->consumer_key;
    $consumer_secret = $user->consumer_secret;
    $access_token = $user->access_token;
    $access_secret = $user->access_secret;
    if($method == 'DELETE'){
        $consumer = new OAuthConsumer($consumer_key, $consumer_secret, NULL);
        $token = new OAuthToken($access_token, $access_secret, NULL);
        $request = OAuthRequest::from_consumer_and_token($consumer, $token, $method, $basefeed, $post_data);
        $request->sign_request(new OAuthSignatureMethod_HMAC_SHA1(), $consumer, $token);
        $response = modmendeley_send_request('DELETE', $request->to_url(), $request->to_header(), null);
        //$response = modmendeley_send_request('DELETE', $request->get_normalized_http_url(), array(), $request->to_postdata());
    }
    return json_decode($response);
}

/**
 * Get Mendeley Publics Methods
 * @param <string> $method request method
 * @param <object> $user user information
 * @param <string> $url indicates the url
 * @param <array>  $params an array with the params
 * @return <array> An array with the result of the response
 */
function getPublicMethods($method, $user, $url, $params){
    //$basefeed = rtrim($user->url,'/').$uri;
    if ($method == 'GET'){
        $response = modmendeley_send_request($method, $url, null, null, $params);
    }
    return json_decode($response);
}

/**
 * Function to return the pagination String
 * @param int $page the current page number
 * @param <type> $totalitems is the total number of items in the set.
 * @param int $limit
 * @param int $adjacents is the number of page links to put adjacent to the current page.
 * @param string $targetpage
 * @param <type> $pagestring
 * @return string
 */
function showPaginationString($page,$lastpage,$adjacents, $search_data, $is_search)
{
	$prev = $page - 1; //previous page is page - 1
	$next = $page + 1; //next page is page + 1
        if($is_search){
            $url = 'view.php?'. http_build_query($search_data);
        }else{
            $url = 'view.php?'. $search_data;
        }

	if($lastpage > 1)
	{
		//previous button
		if ($page > 1){
                    echo "                  <a rel='nofollow' href='$url&amp;page=$prev' class='pagemenu_prev'>";
                    echo "                      <div class='arrow-enabled'>Prev</div>";
                    echo "                      <img class='arrow-next-prev' src='http://www.mendeley.com/graphics/pagination/prev_2705221835265492.png' alt='&lt;' title='Previous page' width='7' height='10'>";
                    echo "                  </a>";
                    echo "                  <ul>";
                }else{
                    echo "                  <div class='arrow-disabled'>Prev</div>";
                    echo "                  <img class='arrow-next-prev' src='http://www.mendeley.com/graphics/pagination/prev-disabled_2933436396704262.png' alt='&lt;' title='Previous page' width='7' height='10'>";
                    echo "                  <ul>";
                }

		//pages
		if ($lastpage < 7 + ($adjacents * 2))	//not enough pages to bother breaking it up
		{
			for ($counter = 1; $counter <= $lastpage; $counter++)
			{
				if ($counter == $page)
					echo "                      <li class='selected'>$counter</li>";
				else
                                        echo "                      <li><a rel='nofollow' href='$url&amp;page=$counter' class='page'>$counter</a></li>";
			}
		}
		elseif($lastpage >= 7 + ($adjacents * 2))	//enough pages to hide some
		{
			//close to beginning; only hide later pages
			if($page < ($adjacents * 3))
			{
				for ($counter = 1; $counter < 2 + ($adjacents * 2); $counter++)
				{
                                        if ($counter == $page)
                                                echo "                      <li class='selected'>$counter</li>";
                                        else
                                                echo "                      <li><a rel='nofollow' href='$url&amp;page=$counter' class='page'>$counter</a></li>";
				}
                                echo "                      <li><a rel='nofollow' href='$url&amp;page=$lastpage' class='page'>... $lastpage</a></li>";
			}
			//in middle; hide some front and some back
			elseif($lastpage - ($adjacents * 2) > $page && $page > ($adjacents * 2))
			{
                                if ($page < 4)
                                    echo "                      <li><a rel='nofollow' href='$url&amp;page=1' class='page'>1</a></li>";
                                else
                                    echo "                      <li><a rel='nofollow' href='$url&amp;page=1' class='page'>1...</a></li>";
				for ($counter = $page - $adjacents; $counter <= $page + $adjacents; $counter++)
				{
                                        if ($counter == $page)
                                                echo "                      <li class='selected'>$counter</li>";
                                        else
                                                echo "                      <li><a rel='nofollow' href='$url&amp;page=$counter' class='page'>$counter</a></li>";
				}
                                echo "                      <li><a rel='nofollow' href='$url&amp;page=$lastpage&amp' class='page'>...$lastpage</a></li>";
			}
			//close to end; only hide early pages
			else
			{
                                echo "                      <li><a rel='nofollow' href='$url&amp;page=1' class='page'>1...</a></li>";

                                if (($lastpage - $page) > 1){
                                    $init_counter = $lastpage - (($adjacents * 3));
                                }
                                else{
                                    $init_counter = $lastpage - (($adjacents * 2));
                                }
                                
				for ($counter = $init_counter; $counter <= $lastpage; $counter++)
				{
                                        if ($counter == $page)
                                                echo "                      <li class='selected'>$counter</li>";
                                        else
                                                echo "                      <li><a rel='nofollow' href='$url&amp;page=$counter' class='page'>$counter</a></li>";
				}
			}
		}

		//next button
		if ($page < $counter - 1){
                    echo "                  </ul>";
                    echo "                  <a href='$url&amp;page=$next' class='pagemenu_next'>";
                    echo "                      <img src='http://www.mendeley.com/graphics/pagination/next_1673068485949050.png' alt='&gt;' title='Next page' class='arrow-next-prev' width='7' height='10'>";
                    echo "                      <div class='arrow-enabled'>Next</div>";
                    echo "                  </a>";
                } else{
                    echo "                  </ul>";
                    echo "                  <img src='http://www.mendeley.com/graphics/pagination/next-disabled_1925942861470175.png' alt='&gt;' title='Next page' class='arrow-next-prev' width='7' height='10'>";
                    echo "			<div class='arrow-enabled'>Next</div>";
                }
	}

}


function getTagsFromDiscipline($disciplines, $user, $params){
    $tags = array();
    foreach ($disciplines as $discipline){
        $preview_tag = getPublicMethods('GET', $user, rtrim($user->url,'/').'/stats/tags/'.$discipline, $params);
        foreach ($preview_tag as $periods){
            foreach ($periods->tags as $tag){
                $tags[] = $tag->name;
                if (count($tags) > 5) return array_unique($tags);
            }
        }
    }
    return array_unique($tags);
}

function checkStatsLibraryImage(){
    $json = modmendeley_send_request('GET', 'http://www.mendeley.com/image/library_stats/');
    $result = json_decode($json);
    if ($result == null){
        return true;
    }
    return false;
}

function getDocumentsType(){
    return array("Bill","Book","Book Section","Case","Computer Program","Conference Proceedings","Encyclopedia Article","Film","Generic","Hearing","Journal Article","Magazine Article","Newspaper Article","Patent","Report","Statute","Television Broadcast","Thesis","Web Page","Working Paper");
}

function toArray($data){
    $new_data = array();
    $new_data['type'] = $_POST['pub_type'];
    foreach ($data as $key => $value){
        if ($value != ""){
            $new_data[$key] = $value;
        }
    }
    return $new_data;
}

function getDocumentsInFolder($user){
    $new_data = "";
    $folders = getLibraryValue('GET', $user, '/library/folders/');
    foreach($folders as $folder){
        $documents = getLibraryValue('GET', $user, '/library/folders/'.$folder->id);
        $new_data .= implode(",", $documents->document_ids);
    }
    return explode(",", $new_data);
}

function createDate($start_date, $end_date){
    $months = array("Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec");
    if ($end_date[0] == '0000' ){
        return $start_date[0]." - Present";
    }
    return $months[intval($start_date[1])-1]." ".$start_date[0]." - ".$months[intval($end_date[1])-1]." ".$end_date[0];
   
}

function checkActivityPermission($modmendeley, $option, $action, $type){
    $show = true;
    if($option == 'library'){
        switch ($action){
            case 'addselecteddocument':
            case 'adddialog':
                if($modmendeley->permission_add_doc_folder == 0) $show = false;
                break;
            case 'savedocument':
            case 'add':
                if($modmendeley->permission_create_document == 0) $show = false;
                break;
            case 'deletedocument':
                if($modmendeley->permission_delete_document == 0) $show = false;
                break;
            case 'savefolder':
            case 'createfolder':
                if($modmendeley->permission_new_folder == 0) $show = false;
                break;
            case 'deletefolder':
                if($modmendeley->permission_delete_folder == 0) $show = false;
                break;
            case 'deletegroup':
                if($modmendeley->permission_delete_group == 0) $show = false;
                break;
            case 'deletedocumentfolder':
                if($modmendeley->permission_delete_doc_folder == 0) $show = false;
                break;
            case 'dialog':
                switch($type){
                   case 'document':
                        if($modmendeley->permission_delete_document == 0) $show = false;
                        break;
                    case 'folder':
                        if($modmendeley->permission_delete_folder == 0) $show = false;
                        break;
                    case 'folderdocument':
                        if($modmendeley->permission_delete_doc_folder == 0) $show = false;
                        break;
                    case 'group':
                        if($modmendeley->permission_delete_group == 0) $show = false;
                        break;
                }
                break;
            case 'creategroup':
                if($modmendeley->permission_new_group == 0) $show = false;
                break;
        }
    }else if($option == 'group'){
        if ($action == 'add' || $action == 'savegroup'){
            if($modmendeley->permission_new_group == 0) $show = false;
        }
    }
    return $show;
}

function documentIsInFolder($document, $user){
    $folders = getLibraryValue('GET', $user, '/library/folders/');
    foreach ($folders as $folder){
        $folder_detail = getLibraryValue('GET', $user, '/library/folders/'.$folder->id);
        foreach($folder_detail->document_ids as $doc_id){
            if($doc_id == $document){
                return $folder->id;
            }
        }
    }
    return null;
}

function documentIsInGroup($document, $user){
    $groups = getLibraryValue('GET', $user, '/library/groups/');
    foreach ($groups as $group){
        $group_detail = getLibraryValue('GET', $user, '/library/groups/'.$group->id);
        foreach($group_detail->document_ids as $doc_id){
            if($doc_id == $document){
                return $group->id;
            }
        }
    }
    return null;
}

function deleteDocuments($document, $user){
    if(($folder_id = documentIsInFolder($document, $user)) != null){
        deleteLibraryValue('DELETE', $user, '/library/folders/'.$folder_id.'/'.$document, array());
    }else if(($group_id = documentIsInGroup($document, $user)) != null){
        deleteLibraryValue('DELETE', $user, '/library/groups/'.$group_id.'/'.$document, array());
    }
    deleteLibraryValue('DELETE', $user, '/library/documents/'.$document, array());
}


function getTabs($value){
    switch ($value){
        case '0':
            return 'library';
            break;
        case '1':
            return 'paper';
            break;
        case '2':
            return 'group';
            break;
        case '3':
            return 'people';
            break;
        default:
            return 'paper';
            break;
    }
}