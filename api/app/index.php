<?php
    //TODO : 
    //voir spacename
    //files logo à charger

    require_once './Router/Router.php';

    $route = new Router();

    $route->addRoute('GET','/', ['all','getAllRoutes'], '', []);
    $route->addRoute('GET','/category', ['category','showCategories'], '', []);
    $route->addRoute('GET','/categoryOrderName', ['category','showCategories'], '', ['name']);
    $route->addRoute('GET','/category/{id}', ['category','showCategoryById'], 'id:(\d+)', ['id']);
    $route->addRoute('GET','/category/{name}', ['category','showCategoryByName'],'name:([a-zA-Z0-9À-ÿ ]+)', ['name']); 

    //required headers
    header("Access-Control-Allow-Origin: *"); //acces for all sites and devices
    header("Content-Type: application/json; charset=UTF-8"); //format of data sending
    header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE"); //method GET for read
    header("Access-Control-Max-Age: 3600"); //cache this information for a specified time = request time life : 1hour
    
    $response = $route->match();
var_dump($response);
    if(isset($response[0])){
        if($response[0] === 'Router'){
            $allRoutes = $route->getAllRoutes();
            echo json_encode(["Routes" => $allRoutes], JSON_UNESCAPED_UNICODE);
        }else if(file_exists('./Controllers/'.$response[0].'.php')){
            require_once './Controllers/'.$response[0].'.php'; //include file controller
            $class = $response[0]; //class name
            $controller = new $class();//create instance of class
            $function = $response[1]; //function name
            if(sizeof($response[2]) > 0){//get argument
                $arg = $response[2][0]; 
            }else{
                $arg = null;
            }
            $reply = $controller->$function($arg); //get function

            //JSON_UNESCAPED_UNICODE : option to display correct words without unicode
            http_response_code(200);
            echo json_encode($reply, JSON_UNESCAPED_UNICODE);
        }
    }else{
        http_response_code(404);
        echo json_encode($response);
    }
?>
