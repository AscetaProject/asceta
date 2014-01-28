<?php
/**
 * Returns in JSONLD format all nodes, with type article, owned by admin
 */
function _hydra_articles() {
    $result = "";
    $uid = 1;
    $query = db_query('SELECT n.nid, n.title, n.created FROM {node} n WHERE n.type="article" AND n.uid=:uid', array(':uid'=>$uid));
    $nodes = array();
    $result .= '{ "@context": "%absolute_server_path%contexts/Collection",';
    $result .= '"@id": "%absolute_server_path%articles",';
    $result .= '"@type": "Collection",';
    $result .= '"members": [';
    $multiple = FALSE;
    foreach ($query as $node) {
        $nodes[$node->nid] = $node;
        if ($multiple) {
            $result .= ", ";
        }
        $result .= '{ "@id": "%absolute_server_path%articles/'.$node->nid.'", "@type": "vocab:Article", "label": "'.$node->title.'" }';
        $multiple = TRUE;
    }
    $result .= '] }';

    $endpoint_path = services_get_server_info('endpoint_path', 'services/hydra'); // Gets the hydra service URL (Example: hydra)
    $absolute_server_path = curPageURL();
    $path_parts = explode("/",$absolute_server_path);
    $absolute_server_path = ""; // Gets the server URL (Example: http://localhost/drupal)

    foreach ($path_parts as $key => $value) {
        if ($value == $endpoint_path) break;
        $absolute_server_path .= $value."/";
    }

    $absolute_server_path .= $endpoint_path."/";

    $result = strtr($result, array( '%absolute_server_path%' => $absolute_server_path, '%endpoint_path%' => $endpoint_path ));

    return $result;
}

/**
 * Returns in JSONLD format the node specified by the id argument
 *
 * @param integer $id
 * @return node 
 *   The node info in JSONLD format
 */
function _hydra_article($id) {
    $response = "";
    $type = "article";
    $sql = 'SELECT n.nid, n.title, n.created, f.body_value FROM {node} AS n LEFT JOIN {node_revision} AS r ON r.nid=n.nid LEFT JOIN {field_data_body} AS f ON r.nid=f.revision_id AND n.nid=f.entity_id WHERE n.nid=:nid';
    $query = db_query($sql, array(":nid" => $id));

    $endpoint_path = services_get_server_info('endpoint_path', 'services/hydra'); // Gets the hydra service URL (Example: hydra)
    $absolute_server_path = curPageURL();
    $path_parts = explode("/",$absolute_server_path);
    $absolute_server_path = ""; // Gets the server URL (Example: http://localhost/drupal)

    foreach ($path_parts as $key => $value) {
        if ($value == $endpoint_path) break;
        $absolute_server_path .= $value."/";
    }

    $absolute_server_path .= $endpoint_path."/";

    foreach ($query as $node) {
        $nodes[$node->nid] = $node;
        $response = json_encode(
            array(
                '@context' => $absolute_server_path."contexts/Article",
                '@id' => $absolute_server_path."articles/".$id,
                '@type' => "Article",
                'title' => $node->title,
                'raised_by' => "",
                'created_at' => $node->created,
                'description' => $node->body_value
        ));
    }

    return $response;
}

/**
 * Creates a new node with the supplied info (title and description).
 * 
 * @param string $title
 * @param string $description
 * @return node 
 *   The new node info in JSONLD format
 */
function _hydra_article_create($title, $description) {
    $node = new stdClass();
    $node->type = "article";
    node_object_prepare($node);
    $node->title = $title;
    $node->language = LANGUAGE_NONE;
    $node->uid = 1; // TODO: Use user's id (Requires Authentication)
    $node->body[$node->language][0]['value'] = $description;
    $node->body[$node->language][0]['summary'] = text_summary($description);
    $node->body[$node->language][0]['format'] = "filtered_html";
    $path = "node_created_on".date('YmdHis');
    $node->path = array('alias' => $path);
    $node->status = 1; // Published
    $node->promote = 1; // Promoted
    $node->sticky = 1; // Allways on top
    $node->comment = 1; // Closed

    if ($node = node_submit($node)) {
        node_save($node);

        $response = "";

        $endpoint_path = services_get_server_info('endpoint_path', 'services/hydra'); // Gets the hydra service URL (Example: hydra)
        $absolute_server_path = curPageURL();
        $path_parts = explode("/",$absolute_server_path);
        $absolute_server_path = ""; // Gets the server URL (Example: http://localhost/drupal)

        foreach ($path_parts as $key => $value) {
            if ($value == $endpoint_path) break;
            $absolute_server_path .= $value."/";
        }

        $absolute_server_path .= $endpoint_path."/";
        $id = $node->nid;

        $response = json_encode(
            array(
                '@context' => $absolute_server_path."contexts/Article",
                '@id' => $absolute_server_path."articles/".$id,
                '@type' => "Article",
                'title' => $node->title,
                'raised_by' => 1,
                'created_at' => $node->created,
                'description' => $description
            )
        );
    }

    return $response;
}

/**
 * Updates a node especified by the supplied ID with the supplied info (title and description).
 * 
 * @param integer $id
 * @param string $title
 * @param string $description
 * @return node 
 *   The updated node info in JSONLD format
 */
function _hydra_article_update($id, $title, $description) {
    $response = "";
    if ($node = node_load($id) ) {
        $node->title = $title;
        $node->language = LANGUAGE_NONE;
        $node->body[$node->language][0]['value'] = $description;
        $node->body[$node->language][0]['summary'] = text_summary($description);
        $node->body[$node->language][0]['format'] = "filtered_html";
        node_save($node);

        $endpoint_path = services_get_server_info('endpoint_path', 'services/hydra'); // Gets the hydra service URL (Example: hydra)
        $absolute_server_path = curPageURL();
        $path_parts = explode("/",$absolute_server_path);
        $absolute_server_path = ""; // Gets the server URL (Example: http://localhost/drupal)

        foreach ($path_parts as $key => $value) {
            if ($value == $endpoint_path) break;
            $absolute_server_path .= $value."/";
        }

        $absolute_server_path .= $endpoint_path."/";
        $id = $node->nid;

        $response = json_encode(
            array(
                '@context' => $absolute_server_path."contexts/Article",
                '@id' => $absolute_server_path."articles/".$id,
                '@type' => "Article",
                'title' => $title,
                'raised_by' => 1,
                'created_at' => $node->created,
                'description' => $description
            )
        );
        
    }
    return $response;
}

/**
 * Deletes a node especified by the supplied ID.
 * 
 * @param integer $id
 * @return none 
 */
function _hydra_article_delete($id) {
    $response = "";
    if (node_delete_multiple(array($id))) {
        $response = "{}";
    }
    return $response;
}

