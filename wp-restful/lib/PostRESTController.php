<?php
class PostRESTController extends WPAPIRESTController {
    protected function __construct() {

    }

    protected function getPosts() {
        global $wpdb;
        // Get all posts
        return $this->_return($wpdb->get_results("SELECT * FROM ".$wpdb->posts." WHERE ID > 0 AND post_type LIKE 'post'"));
    }

    protected function getPost($post) {
        // Get requested posts
        $post_data = get_post($post);
        $post_data->comments = get_approved_comments($post);
        return $this->_return($post_data);
    }

    protected function postPost($data) {
        $new_post = array();
        $new_post['post_title'] = $data['post_title'];
        $new_post['post_content'] = $data['post_content'];
        $new_post['post_status'] = 'publish';
        //TODO: En lugar de que el post_author sea la ID del autor, que sea el login del autor
        // entonces buscar la id de este en la base de datos.
        if (isset($data['post_author'])) {
	$new_post['post_author'] = $data['post_author'];
        } else {
	$new_post['post_author'] = 1;
        }
        if (isset($data['post_type'])) {
	$new_post['post_type'] = $data['post_type'];
        } else {
	$new_post['post_type'] = 'post';
        }

        $new_post_id = wp_insert_post( $new_post );

        if ($new_post_id > 0) {
	return $this->_return(get_post($new_post_id));
        } else {
	return new WP_Error('error', __('Error adding your post.'));
        }
    }

    protected function putPost($data) {

        $update_post = array();
        $update_post['ID'] = $data['id'];

        if (isset($data['post_title'])) { error_log(" isset post_title "); }
        if (isset($data['post_title'])) {
	$update_post['post_title'] = $data['post_title'];
        }

        if (isset($data['post_content'])) { error_log(" isset post_content "); }
        if (isset($data['post_content'])) {
	$update_post['post_content'] = $data['post_content'];
        }
        $updated_post_id = wp_update_post( $update_post );

        return $this->_return(get_post($updated_post_id));
    }

    protected function deletePost($post) {
        if (wp_delete_post($post)) {
	return array( 'ID' => $post, 'deleted' => true);
        } else {
	return new WP_Error('error', __('Error deleting post.'));
        }
    }


    /**
     * Apply request filter
     *
     * @since 0.1
     *
     * @return (mixed) filtered content
     */
    private function _return($content) {
        return wpr_filter_content($content,wpr_get_filter("Posts"));
    }
}
?>