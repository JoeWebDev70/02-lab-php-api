<?php

    require_once './Router/Router.php';
    require_once './utilities/functions.php';

    $route = new Router();

    //GET
    $route->addRoute('GET','/', ['all','getAllRoutes'], '', [], "Voir toutes les routes disponibles");
    $route->addRoute('GET','/categories', ['category','showCategories'], '', ['id'], "Voir toutes les catégories, ordonnées par Id");
    $route->addRoute('GET','/categories/name', ['category','showCategories'], '', ['name'], "Voir toutes les catégories, ordonnées par nom");
    $route->addRoute('GET','/categories/technologies', ['category','showCategoriesTechnologies'], '', ['id'], "Voir toutes les catégories qui contiennent des technologies, ordonnées par Id");
    $route->addRoute('GET','/categories/name/technologies', ['category','showCategoriesTechnologies'], '', ['name'], "Voir toutes les catégories qui contiennent des technologies, ordonnées par nom");
    $route->addRoute('GET','/category/{id}', ['category','showCategoryBy'], 'id:(\d+)', ['id'], "Voir une catégorie par son Id");
    $route->addRoute('GET','/category/{name}', ['category','showCategoryBy'],'name:([a-zA-Z0-9À-ÿ \-_\']+)', ['name'], "Voir une catégorie par son nom"); 
    $route->addRoute('GET','/category/{id}/technologies', ['category','showCategoryTechnologiesBy'], 'id:(\d+)', ['id'], "Voir une catégorie par son Id si elle contient des technologies");
    $route->addRoute('GET','/category/{name}/technologies', ['category','showCategoryTechnologiesBy'],'name:([a-zA-Z0-9À-ÿ \-_\']+)', ['name'], "Voir une catégorie par son nom si elle contient des technologies"); 
    $route->addRoute('GET','/technologies', ['technology','showTechnologies'], '', ['id'], "Voir toutes les technologies, ordonnées par Id");
    $route->addRoute('GET','/technologies/name', ['technology','showTechnologies'], '', ['name'], "Voir toutes les technologies, ordonnées par nom");
    $route->addRoute('GET','/technology/{id}', ['technology','showTechnologyBy'], 'id:(\d+)', ['id'], "Voir une technologie par son Id");
    $route->addRoute('GET','/technology/{name}', ['technology','showTechnologyBy'], 'name:([a-zA-Z0-9À-ÿ \-_\']+)', ['name'], "Voir toutes les technologies du même nom");
    $route->addRoute('GET','/resources', ['resource','showResources'], '', ['id'], "Voir toutes les ressources, ordonnées par Id");
    $route->addRoute('GET','/resource/{id}', ['resource','showResource'], 'id:(\d+)', ['id'], "Voir une ressource par son Id");
    $route->addRoute('GET','/resource/technology/{id}', ['resource','showResourcesFor'], 'id:(\d+)', ['id'], "Voir les ressources d'une technologie par son Id");
    
    //POST
    $route->addRoute('POST','/category', ['category','addCategory'], '', ['?name=name'], "Creer une nouvelle categorie");
    $route->addRoute('POST','/technology', ['technology','addTechnology'], '', ['?name=name&categoryId=id'], "Creer une nouvelle technologie");
    $route->addRoute('POST','/resource', ['resource','addResource'], '', ['?technologyId=id&url=url'], "Creer une nouvelle ressource pour une technologie");
    
    //PUT
    $route->addRoute('PUT','/category/{id}', ['category','updateCategoryBy'], 'id:(\d+)', ['id', '?name=newName'], "Mettre à jour une catégorie par son Id");
    $route->addRoute('PUT','/category/{name}', ['category','updateCategoryBy'], 'name:([a-zA-Z0-9À-ÿ \-_\']+)', ['name', '?name=newName'], "Mettre à jour une catégorie par son nom");
    $route->addRoute('PUT','/technology/{id}', ['technology','updateTechnology'], 'id:(\d+)', ['id', '?name=newName'], "Mettre à jour une technologie par son Id");
    $route->addRoute('PUT','/resource/{id}', ['resource','updateResource'], 'id:(\d+)', ['id', '?url=newUrl&technologyId=newTechnologyId'], "Mettre à jour une ressource par son Id");
    
    //DELETE
    $route->addRoute('DELETE','/category/{id}', ['category','deleteCategoryBy'], 'id:(\d+)', ['id'], "Supprimer une catégorie par son Id");
    $route->addRoute('DELETE','/category/{name}', ['category','deleteCategoryBy'], 'name:([a-zA-Z0-9À-ÿ \-_\']+)', ['name'], "Supprimer une catégorie par son nom");
    $route->addRoute('DELETE','/technology/{id}', ['technology','deleteTechnology'], 'id:(\d+)', ['id'], "Supprimer une technologie par son Id");
    $route->addRoute('DELETE','/resource/{id}', ['resource','deleteResource'], 'id:(\d+)', ['id'], "Supprimer une ressource par son Id");
    

    //required headers
    header("Access-Control-Allow-Origin: *"); //acces for all sites and devices
    header("Content-Type: application/json; charset=UTF-8"); //format of data sending
    header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE"); //method GET for read
    header("Access-Control-Max-Age: 3600"); //cache this information for a specified time = request time life : 1hour
    
    $response = $route->match();

    if(isset($response[0]) && $response[0] != false){  //route found
        if($response[0] === 'Router'){ //display all routes
            $allRoutes = $route->getAllRoutes();
            http_response_code(200);
            echo json_encode(["plus d'informations" => "https://github.com/JoeWebDev70/02-lab-php-api","Routes" => $allRoutes], JSON_UNESCAPED_UNICODE);
        }else if(file_exists('./Controllers/'.$response[0].'.php')){  //particular route 
            require_once './Controllers/'.$response[0].'.php'; //include file controller
            $class = $response[0]; //class name
            $controller = new $class();//create instance of class
            $function = $response[1]; //function name
            if(sizeof($response) >= 3 && sizeof($response[2]) > 0 ){//get arguments
                $arg = $response[2][0]; 
            }else{
                $arg = null;
            }
            if(sizeof($response) >= 4 && sizeof($response[3]) > 0 ){//get data
                $data = $response[3];
            }else{
                $data = null;
            }
            $reply = $controller->$function($arg, $data); //get function and send array of args and data
            $replyName = getReplyName($reply); // get the name of message to display
            //JSON_UNESCAPED_UNICODE : option to display correct words without unicode
            http_response_code($reply["http"]);
            echo json_encode([$replyName  => $reply[$replyName ]], JSON_UNESCAPED_UNICODE);
        }
    }else{ //route not found
        http_response_code($response["http"]);
        echo json_encode(["route match" => $response["message"]]);
    }

    

?>