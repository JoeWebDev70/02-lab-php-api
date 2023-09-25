<?php

    require_once './Router/Router.php';
    // use Router\Router;

    $route = new Router();

    $route->addRoute('GET','/', ['all','getAllRoutes'], []);
    $route->addRoute('GET','/category', ['category','showCategories'], []);
    $route->addRoute('GET','/categoryOrderName', ['category','showCategories'], ['name']);
    $route->addRoute('GET','/category/{id}', ['category','showCategory'], ['id']);
    $route->addRoute('GET','/category/{name}', ['category','showCategory'], ['name']); 

    //required headers
    header("Access-Control-Allow-Origin: *"); //acces for all sites and devices
    header("Content-Type: application/json; charset=UTF-8"); //format of data sending
    header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE"); //method GET for read
    header("Access-Control-Max-Age: 3600"); //cache this information for a specified time = request time life : 1hour
    
    $response = $route->match();
// var_dump($response);
    if(isset($response[0])){
        if($response[0] === 'Router'){
            $allRoutes = $route->getAllRoutes();
            echo json_encode(["Routes" => $allRoutes], JSON_UNESCAPED_UNICODE);
        }else if(file_exists('./Controllers/'.$response[0].'.php')){
            require_once './Controllers/'.$response[0].'.php'; //include file controller
            //create instance of class
            $class = $response[0]; 
            $controller = new $class();
            //get function
            $function = $response[1];
            //get argument
            $arg = $response[2][0];
            $reply = $controller->$function($arg);

            //JSON_UNESCAPED_UNICODE : option to display correct words without unicode
            http_response_code(200);
            echo json_encode($reply, JSON_UNESCAPED_UNICODE);
        }
    }else{
        http_response_code(404);
        echo json_encode($response);
    }
?>
