<?php

class CommentRESTController extends WPAPIRESTController {

    protected function __construct() {
        
    }

    protected function getComments() {
        global $wpdb;
        $array = array();
        wpr_set_defaults($_POST, array('post_id' => false, 'comment_id' => false));
        // Get all comments
        if ($_POST['post_id'])
	$comments = $wpdb->get_results($wpdb->prepare("SELECT c.*, user_nicename as comment_author_name FROM " . $wpdb->comments . " c, ".$wpdb->users." u  WHERE comment_post_ID = %d AND comment_approved = 1 AND c.comment_author = u.ID ORDER BY comment_date ASC", $_POST['post_id']));
        elseif ($_POST['comment_id'])
	$comments = $wpdb->get_results($wpdb->prepare("SELECT c.*, user_nicename as comment_author_name FROM " . $wpdb->comments ." c, ".$wpdb->users." u WHERE comment_ID = %d AND comment_approved = 1 AND c.comment_author = u.ID ORDER BY comment_date ASC", $_POST['comment_id']));
        else
	$comments = $wpdb->get_results("SELECT c.*, user_nicename as comment_author_name FROM " . $wpdb->comments . " c, ".$wpdb->users." u WHERE comment_ID > 0 AND comment_approved = 1 AND c.comment_author = u.ID ORDER BY comment_date ASC");

        if (count($comments) == 1) {
	return $this->_return($comments[0]);
        }
        foreach ($comments as $comment) {
	$array[] = $comment;
        }
        return $this->_return($array);
    }

    protected function getComment($post) {
        // Get requested posts
        $comment_data = get_comment($post);
        if(is_null($comment_data)){
            throw new Exception("Comment '$post' not found \n","404");
        }
        return $this->_return($comment_data);
    }

    protected function postComment($data) {
        $comment_data = array();
        $comment_data['comment_post_ID'] = $data['id'];
        if (isset($data['comment_author'])) {
	$comment_data['comment_author'] = $data['comment_author'];
        } else {
	$comment_data['comment_author'] = 1;
        }
        if (isset($data['comment_author_email']))
	$comment_data['comment_author_email'] = $data['comment_author_email'];
        if (isset($data['comment_author_url']))
	$comment_data['comment_author_url'] = $data['comment_author_url'];
        $comment_data['comment_content'] = $data['comment_content'];
        $comment_data['comment_author_IP'] = $_SERVER["HTTP_CLIENT_IP"];
        $comment_data['comment_agent'] = $_SERVER['HTTP_USER_AGENT'];
        $comment_data['comment_date'] = date('Y-m-d H:i:s');
        $comment_data['comment_date_gmt'] = date('Y-m-d H:i:s');
        $comment_data['comment_approved'] = 1;

        $new_comment_id = wp_insert_comment($comment_data);

        if ($new_comment_id > 0) {
	return $this->_return(get_comment($new_comment_id));
        } else {
	return new WP_Error('error', __('Error adding your comment.'));
        }
    }

    protected function putComment($data) {
        $comment_data = array();

        if (isset($data['id']))
	$comment_data['comment_ID'] = $data['id'];
        if (isset($data['comment_author']))
	$comment_data['comment_author'] = $data['comment_author'];
        if (isset($data['comment_author_email']))
	$comment_data['comment_author_email'] = $data['comment_author_email'];
        if (isset($data['comment_author_url']))
	$comment_data['comment_author_url'] = $data['comment_author_url'];
        if (isset($data['comment_content']))
	$comment_data['comment_content'] = $data['comment_content'];
        $comment_data['comment_author_IP'] = $_SERVER["HTTP_CLIENT_IP"];
        $comment_data['comment_agent'] = $_SERVER['HTTP_USER_AGENT'];
        $comment_data['comment_date'] = date('Y-m-d H:i:s');
        $comment_data['comment_date_gmt'] = date('Y-m-d H:i:s');
        $comment_data['comment_approved'] = 1;

        $comment_exists = get_comment($data['id']);

        if ($comment_exists) {
	$updated = wp_update_comment($comment_data);

	if ($updated == 1) {
	    return $this->_return(get_comment($data['id']));
	} else {
	    throw new Exception("Comment '".$data['id']."' not modified \n","400");
	}

        } else {
	throw new Exception("Comment '".$data['id']."' not found \n","404");

        }


    }

    protected function deleteComment($comment_id) {
        $comment_exists = get_comment($comment_id);

        if ($comment_exists) {

	if (wp_delete_comment($comment_id)) {
	    return array('ID' => $comment_id, 'deleted' => true);
	} else {
	    throw new Exception("Comment '".$data['id']."' not deleted \n","400");
	}
        } else {
	throw new Exception("Comment '".$comment_id."' not found \n","404");

        }
    }

//	protected function add_comment($commentdata) {
//		$comment_id = wp_insert_comment($commentdata);
//		if($comment_id > 0)
//			return array('success' => 'Comment added with ID: '.$comment_id);
//		else
//			return new WP_Error('error', __('Error adding your comment.'));
//	}

    /**
     * Apply request filter
     * 
     * @since 0.1
     * 
     * @return (mixed) filtered content
     */
    private function _return($content) {
        return wpr_filter_content($content, wpr_get_filter("Comments"));
    }

}

?>