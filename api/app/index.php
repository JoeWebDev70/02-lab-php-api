<?php

    require './controllers/get.php';


    // class Routeur{
    //     static public $routes = [
    //         //direct function assignment
    //         'GET:/' => 'getAllRoutes',
    //         'GET:/read/category' => 'readCategory',
    //     ];
    // }
    

    // function getAllRoutes(){
    //     $listRoutes = [];
    //     foreach(Routeur::$routes as $key => $value){
    //         $listRoutes[] = $key;
    //     }
    //     echo json_encode($listRoutes);
    // }
        
    // function router($url, $method){
    //     foreach(Routeur::$routes as $pattern => $handler){
            
    //         $fullUrl = $method.":".$url;
    //         $pattern = str_replace("{id}", "(\d+)", $pattern); //remplace par chiffre
    //         $pattern = str_replace("{name}", "([a-zA-Z]+)", $pattern); //remplace par nom
    //         $pattern = str_replace("/", "\/", $pattern);
    //         if(preg_match("/^" . $pattern . "$/", $fullUrl, $matches)){
    //             // var_dump(Routeur::$routes);
    //             array_shift($matches);
    //             call_user_func($handler, $matches);
    //             return ;
    //         } else{
    //             header('HTTP/1.0 404 Not Found'); //TODO : modifier
    //         }
    //     }
        
    // }

    // $request_url = $_SERVER['REQUEST_URI']; //url avec nom de domaine juste / si que nom de domaine
    // $method = $_SERVER['REQUEST_METHOD']; //
    // router($request_url, $method);

?>
