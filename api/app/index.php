<?php
    //TODO : 
    //voir spacename
    //files logo à charger

    require_once './Router/Router.php';

    $route = new Router();

    //GET
    //TODO voir pour explaination of routes to display and link to Github Readme
    $route->addRoute('GET','/', ['all','getAllRoutes'], '', [], "Toutes les routes disponibles");
    $route->addRoute('GET','/categories', ['category','showCategories'], '', ['id'], "Voir toutes les catégories, ordonnées par Id");
    $route->addRoute('GET','/categories/name', ['category','showCategories'], '', ['name'], "Voir toutes les catégories, ordonnées par nom");
    $route->addRoute('GET','/categories/technologies', ['category','showCategoriesTechnologies'], '', ['id'], "Voir toutes les catégories qui contiennent des technologies, ordonnées par Id");
    $route->addRoute('GET','/categories/name/technologies', ['category','showCategoriesTechnologies'], '', ['name'], "Voir toutes les catégories qui contiennent des technologies, ordonnées par nom");
    $route->addRoute('GET','/category/{id}', ['category','showCategoryBy'], 'id:(\d+)', ['id'], "Voir une catégorie par son Id");
    $route->addRoute('GET','/category/{name}', ['category','showCategoryBy'],'name:([a-zA-Z0-9À-ÿ \-_]+)', ['name'], "Voir une catégorie par son nom"); 
    $route->addRoute('GET','/category/{id}/technologies', ['category','showCategoryTechnologiesBy'], 'id:(\d+)', ['id'], "Voir une catégorie par son Id si elle contient des technologies");
    $route->addRoute('GET','/category/{name}/technologies', ['category','showCategoryTechnologiesBy'],'name:([a-zA-Z0-9À-ÿ \-_]+)', ['name'], "Voir une catégorie par son nom si elle contient des technologies"); 
    $route->addRoute('GET','/technologies', ['technology','showTechnologies'], '', ['id'], "Voir toutes les technologies, ordonnées par Id");
    $route->addRoute('GET','/technologies/name', ['technology','showTechnologies'], '', ['name'], "Voir toutes les technologies, ordonnées par nom");
    $route->addRoute('GET','/technology/{id}', ['technology','showTechnologyById'], 'id:(\d+)', ['id'], "Voir une technologie par son Id");
    $route->addRoute('GET','/technology/{name}', ['technology','showTechnologyByName'], 'name:([a-zA-Z0-9À-ÿ \-_]+)', ['name'], "Voir les technologies du même nom");

    //POST
    $route->addRoute('POST','/category', ['category','addCategory'], '', ['?name=name'], "Creer une nouvelle categorie");
    $route->addRoute('POST','/technology', ['technology','addTechnology'], '', ['?name=name&[logo=directoryFile&]categoryId=id'], "Creer une nouvelle technologie");
    
    //PUT
    $route->addRoute('PUT','/category/{id}', ['category','updateCategoryBy'], 'id:(\d+)', ['id', '?name=newName'], "Mettre à jour une catégorie par son Id");
    $route->addRoute('PUT','/category/{name}', ['category','updateCategoryBy'], 'name:([a-zA-Z0-9À-ÿ \-_]+)', ['name', '?name=newName'], "Mettre à jour une catégorie par son nom");
    $route->addRoute('PUT','/technology/{id}', ['technology','updateTechnology'], 'id:(\d+)', ['id', '?name=newName'], "Mettre à jour une technologie par son Id");
    
    //DELETE
    $route->addRoute('DELETE','/category/{id}', ['category','deleteCategoryBy'], 'id:(\d+)', ['id'], "Supprimer une catégorie par son Id");
    $route->addRoute('DELETE','/category/{name}', ['category','deleteCategoryBy'], 'name:([a-zA-Z0-9À-ÿ \-_]+)', ['name'], "Supprimer une catégorie par son nom");

    //required headers
    header("Access-Control-Allow-Origin: *"); //acces for all sites and devices
    header("Content-Type: application/json; charset=UTF-8"); //format of data sending
    header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE"); //method GET for read
    header("Access-Control-Max-Age: 3600"); //cache this information for a specified time = request time life : 1hour
    
    $response = $route->match();
// var_dump($response);
    if(isset($response[0]) && $response[0] != false){  //route found
        if($response[0] === 'Router'){ //display all routes
            $allRoutes = $route->getAllRoutes();
            http_response_code(200);
            echo json_encode(["Routes" => $allRoutes], JSON_UNESCAPED_UNICODE);
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

    function getReplyName($reply){ //return the name of message to display
        if(isset($reply["message"])){
            return "message";
        }else if(isset($reply["Categories"])){
            return "Categories";
        }else if(isset($reply["Technologies par Catégorie"])){
            return "Technologies par Catégorie";
        }else if(isset($reply["Technologies"])){
            return "Technologies";
        }
    }

