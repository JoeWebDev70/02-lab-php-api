<?php
    //TODO : 
    //voir spacename
    //files logo à charger

    require_once './Router/Router.php';

    $route = new Router();

    //GET
    $route->addRoute('GET','/', ['all','getAllRoutes'], '', []);
    $route->addRoute('GET','/categories', ['category','showCategories'], '', ['id']);
    $route->addRoute('GET','/categories/name', ['category','showCategories'], '', ['name']);
    $route->addRoute('GET','/categories/technologies', ['category','showCategoriesTechnologies'], '', ['id']);
    $route->addRoute('GET','/categories/name/technologies', ['category','showCategoriesTechnologies'], '', ['name']);
    $route->addRoute('GET','/category/{id}', ['category','showCategoryBy'], 'id:(\d+)', ['id']);
    $route->addRoute('GET','/category/{name}', ['category','showCategoryBy'],'name:([a-zA-Z0-9À-ÿ \-_]+)', ['name']); 
    $route->addRoute('GET','/category/{id}/technologies', ['category','showCategoryTechnologiesBy'], 'id:(\d+)', ['id']);
    $route->addRoute('GET','/category/{name}/technologies', ['category','showCategoryTechnologiesBy'],'name:([a-zA-Z0-9À-ÿ \-_]+)', ['name']); 
    $route->addRoute('GET','/technologies', ['technology','showTechnologies'], '', ['id']);
    $route->addRoute('GET','/technologies/name', ['technology','showTechnologies'], '', ['name']);
    $route->addRoute('GET','/technology/{id}', ['technology','showTechnologyById'], 'id:(\d+)', ['id']);
    $route->addRoute('GET','/technology/{name}', ['technology','showTechnologyByName'], 'name:([a-zA-Z0-9À-ÿ \-_]+)', ['name']);

    //POST
    $route->addRoute('POST','/category', ['category','addCategory'], '', ['?name=name']);
    $route->addRoute('POST','/technology', ['technology','addTechnology'], '', ['?name=name&[logo=directoryFile&]categoryId=id']);
    
    //PUT
    $route->addRoute('PUT','/category/{id}', ['category','updateCategoryBy'], 'id:(\d+)', ['id', '?name=newName']);
    $route->addRoute('PUT','/category/{name}', ['category','updateCategoryBy'], 'name:([a-zA-Z0-9À-ÿ \-_]+)', ['name', '?name=newName']);
    $route->addRoute('PUT','/technology/{id}', ['technology','updateTechnology'], 'id:(\d+)', ['id', '?name=newName']);
    
    //DELETE
    $route->addRoute('DELETE','/category/{id}', ['category','deleteCategoryBy'], 'id:(\d+)', ['id']);
    $route->addRoute('DELETE','/category/{name}', ['category','deleteCategoryBy'], 'name:([a-zA-Z0-9À-ÿ \-_]+)', ['name']);

    //required headers
    header("Access-Control-Allow-Origin: *"); //acces for all sites and devices
    header("Content-Type: application/json; charset=UTF-8"); //format of data sending
    header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE"); //method GET for read
    header("Access-Control-Max-Age: 3600"); //cache this information for a specified time = request time life : 1hour
    
    $response = $route->match();
// var_dump($response);
    if(isset($response[0]) && $response[0] != false){
        if($response[0] === 'Router'){
            $allRoutes = $route->getAllRoutes();
            http_response_code(200);
            echo json_encode(["Routes" => $allRoutes], JSON_UNESCAPED_UNICODE);
        }else if(file_exists('./Controllers/'.$response[0].'.php')){
            require_once './Controllers/'.$response[0].'.php'; //include file controller
            $class = $response[0]; //class name
            $controller = new $class();//create instance of class
            $function = $response[1]; //function name
            if(sizeof($response) >= 3 && sizeof($response[2]) > 0 ){//get argument
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
            
            //JSON_UNESCAPED_UNICODE : option to display correct words without unicode
            http_response_code($reply["http"]);
            echo json_encode([getReplyName($reply) => $reply[getReplyName($reply)]], JSON_UNESCAPED_UNICODE);
            
        }
    }else{
        http_response_code($response["http"]);
        echo json_encode(["route match" => $response["message"]]);
    }

    function getReplyName($reply){
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

