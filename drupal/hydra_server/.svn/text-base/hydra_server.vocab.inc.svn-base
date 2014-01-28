<?php


/**
 *  Returns the full vocab/documentation for the Hydra API.
 */
function _hydra_vocab() {
    $result = '
        {
            "@context": {
                "ApiDocumentation": "hydra:ApiDocumentation", 
                "code": "hydra:statusCode", 
                "description": "rdfs:comment", 
                "domain": {
                    "@id": "rdfs:domain", 
                    "@type": "@id"
                }, 
                "expects": {
                    "@id": "hydra:expects", 
                    "@type": "@id"
                }, 
                "hydra": "http://purl.org/hydra/core#", 
                "label": "rdfs:label", 
                "method": "hydra:method", 
                "property": "hydra:property", 
                "range": {
                    "@id": "rdfs:range", 
                    "@type": "@id"
                }, 
                "rdf": "http://www.w3.org/1999/02/22-rdf-syntax-ns#", 
                "rdfs": "http://www.w3.org/2000/01/rdf-schema#", 
                "readonly": "hydra:readonly", 
                "returns": {
                    "@id": "hydra:returns", 
                    "@type": "@id"
                }, 
                "statusCodes": "hydra:statusCodes", 
                "supportedClasses": "hydra:supportedClasses", 
                "supportedOperations": "hydra:supportedOperations", 
                "supportedProperties": "hydra:supportedProperties", 
                "vocab": "%absolute_server_path%vocab#", 
                "writeonly": "hydra:writeonly"
            }, 
            "@id": "%absolute_server_path%vocab", 
            "@type": "ApiDocumentation", 
            "supportedClasses": [
                {
                    "@id": "%absolute_server_path%vocab#Article", 
                    "@type": "hydra:Class", 
                    "description": "An Article tracked by the system.", 
                    "label": "Article", 
                    "supportedOperations": [
                        {
                            "@id": "_:article_replace", 
                            "description": "", 
                            "expects": "%absolute_server_path%vocab#Article", 
                            "label": "Replaces an existing article", 
                            "method": "PUT", 
                            "returns": "%absolute_server_path%vocab#Article", 
                            "statusCodes": [
                                {
                                    "code": 404, 
                                    "description": "If the article was not found."
                                }
                            ]
                        }, 
                        {
                            "@id": "_:article_delete", 
                            "description": "", 
                            "expects": null, 
                            "label": "Deletes an Article", 
                            "method": "DELETE", 
                            "returns": "void", 
                            "statusCodes": [
                                {
                                    "code": 404, 
                                    "description": "If the article was not found."
                                }
                            ]
                        }, 
                        {
                            "@id": "_:article_retrieve", 
                            "description": "", 
                            "expects": null, 
                            "label": "Retrieves an Article", 
                            "method": "GET", 
                            "returns": "%absolute_server_path%vocab#Article", 
                            "statusCodes": [
                                {
                                    "code": 404, 
                                    "description": "If the Issue entity was not found."
                                }
                            ]
                        }
                    ], 
                    "supportedProperties": [
                        {
                            "property": {
                                "@id": "%absolute_server_path%vocab#Article/title", 
                                "@type": "rdf:Property", 
                                "description": "The Articles title", 
                                "domain": "%absolute_server_path%vocab#Article", 
                                "label": "Title", 
                                "range": "http://www.w3.org/2001/XMLSchema#string", 
                                "supportedOperations": []
                            }, 
                            "readonly": false, 
                            "writeonly": false
                        }, 
                        {
                            "property": {
                                "@id": "%absolute_server_path%vocab#Article/description", 
                                "@type": "rdf:Property", 
                                "description": "A description of the Article", 
                                "domain": "%absolute_server_path%vocab#Article", 
                                "label": "Description", 
                                "range": "http://www.w3.org/2001/XMLSchema#string", 
                                "supportedOperations": []
                            }, 
                            "readonly": false, 
                            "writeonly": false
                        }, 
                        {
                            "property": {
                                "@id": "%absolute_server_path%vocab#Article/ownedBy", 
                                "@type": "rdf:Property", 
                                "description": "The user who own the Article", 
                                "domain": "%absolute_server_path%vocab#Article", 
                                "label": "raised_by", 
                                "range": "%absolute_server_path%vocab#User", 
                                "supportedOperations": [
                                    {
                                        "@id": "_:user_retrieve", 
                                        "description": "", 
                                        "expects": null, 
                                        "label": "Retrieves a User entity", 
                                        "method": "GET", 
                                        "returns": "%absolute_server_path%vocab#User", 
                                        "statusCodes": [
                                            {
                                                "code": 404, 
                                                "description": "If the User entity was not found."
                                            }
                                        ]
                                    }
                                ]
                            }, 
                            "readonly": true, 
                            "writeonly": false
                        }, 
                        {
                            "property": {
                                "@id": "%absolute_server_path%vocab#Article/createdAt", 
                                "@type": "rdf:Property", 
                                "description": "The date and time this Article was created", 
                                "domain": "%absolute_server_path%vocab#Article", 
                                "label": "created_at", 
                                "range": "http://www.w3.org/2001/XMLSchema#dateTime", 
                                "supportedOperations": []
                            }, 
                            "readonly": true, 
                            "writeonly": false
                        } 
                    ]
                }, 

                {
                    "@id": "http://purl.org/hydra/core#Collection", 
                    "@type": "hydra:Class", 
                    "description": "A generic collection keeping references to a number of resources.", 
                    "label": "Collection", 
                    "supportedOperations": [], 
                    "supportedProperties": [
                        {
                            "property": {
                                "@id": "http://purl.org/hydra/core#members", 
                                "@type": "rdf:Property", 
                                "description": "The members of this collection.", 
                                "domain": "http://purl.org/hydra/core#Collection", 
                                "label": "members", 
                                "range": null, 
                                "supportedOperations": []
                            }, 
                            "readonly": false, 
                            "writeonly": false
                        }
                    ]
                }, 
                {
                    "@id": "%absolute_server_path%vocab#EntryPoint", 
                    "@type": "hydra:Class", 
                    "description": "The main entry point or homepage of the API.", 
                    "label": "EntryPoint", 
                    "supportedOperations": [
                        {
                            "@id": "_:entry_point", 
                            "description": "", 
                            "expects": null, 
                            "label": "The APIs main entry point.", 
                            "method": "GET", 
                            "returns": "%absolute_server_path%vocab#EntryPoint", 
                            "statusCodes": []
                        }
                    ], 
                    "supportedProperties": [
                        {
                            "property": {
                                "@id": "%absolute_server_path%vocab#EntryPoint/articles", 
                                "@type": "rdf:Property", 
                                "description": "The collection of all articles", 
                                "domain": "%absolute_server_path%vocab#EntryPoint", 
                                "label": "Articles", 
                                "range": "http://purl.org/hydra/core#Collection", 
                                "supportedOperations": [
                                    {
                                        "@id": "_:article_create", 
                                        "description": "", 
                                        "expects": "%absolute_server_path%vocab#Article", 
                                        "label": "Creates a new Article", 
                                        "method": "POST", 
                                        "returns": "%absolute_server_path%vocab#Article", 
                                        "statusCodes": []
                                    }, 
                                    {
                                        "@id": "_:article_collection_retrieve", 
                                        "description": "", 
                                        "expects": null, 
                                        "label": "Retrieves all Articles", 
                                        "method": "GET", 
                                        "returns": "http://purl.org/hydra/core#Collection", 
                                        "statusCodes": []
                                    }
                                ]
                            }, 
                            "readonly": true, 
                            "writeonly": false
                        } 
                    ]
                }, 
                {
                    "@id": "http://www.w3.org/2001/XMLSchema#string", 
                    "@type": "rdfs:Datatype", 
                    "description": "", 
                    "label": "string"
                }, 
                {
                    "@id": "http://www.w3.org/2001/XMLSchema#integer", 
                    "@type": "rdfs:Datatype", 
                    "description": "", 
                    "label": "integer"
                }, 
                {
                    "@id": "http://www.w3.org/2001/XMLSchema#float", 
                    "@type": "rdfs:Datatype", 
                    "description": "", 
                    "label": "float"
                }, 
                {
                    "@id": "http://www.w3.org/2001/XMLSchema#double", 
                    "@type": "rdfs:Datatype", 
                    "description": "", 
                    "label": "double"
                }, 
                {
                    "@id": "http://www.w3.org/2001/XMLSchema#boolean", 
                    "@type": "rdfs:Datatype", 
                    "description": "", 
                    "label": "boolean"
                }, 
                {
                    "@id": "http://www.w3.org/2001/XMLSchema#anyURI", 
                    "@type": "rdfs:Datatype", 
                    "description": "", 
                    "label": "IRI"
                }, 
                {
                    "@id": "http://www.w3.org/2001/XMLSchema#dateTime", 
                    "@type": "rdfs:Datatype", 
                    "description": "", 
                    "label": "datetime"
                }, 
                {
                    "@id": "http://www.w3.org/2002/07/owl#Nothing", 
                    "@type": "http://www.w3.org/2002/07/owl#Class", 
                    "description": "", 
                    "label": "void"
                }
            ]
        }';

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

    return preg_replace('/\s+/', ' ', trim($result));
}
