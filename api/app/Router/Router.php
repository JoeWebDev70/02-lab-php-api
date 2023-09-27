<?php

    // namespace Router\Router;
// require_once './Controllers/CategoryController.php';

// $category = new CategoryController();
// $category->showCategories();

    class Router{
        private $routes = [];
        private $controllers = [
            'all' => 'Router',
            'category' => 'CategoryController',
            'technology' => 'TechnologyController',
            'resource' => 'ResourceController',
        ];


        //method
        public function addRoute($method, $pattern, $handler, $routeRegex, $arg = []){
            $this->routes[] = [$method, $pattern, $handler, $routeRegex, $arg];
        }

        public function match(){
            $url = urldecode($_SERVER['REQUEST_URI']);  //urldecode for spaces and specials char
            $method = $_SERVER['REQUEST_METHOD'];
            
            //check if url is compatible with method
            if(($method === "POST" || $method === "PUT") && str_contains($url, "?")){ //explode data if it's necessary
                $dataExplode = explode("?", $url);
                $url = $dataExplode[0];
                $data[] = $dataExplode[1];
            }else if((($method === "GET" || $method === "DELETE") && str_contains($url, "?"))
                || (($method === "POST" || $method === "PUT") && !str_contains($url, "?"))){
                return [false, "message" => "Erreur : Methode non autorisée", "http" => 405];
            }else{
                $data = [];
            }
            
            foreach($this->routes as $route){
                //Assign variables as if they were an array
                list($routeMethod, $routePattern, $routeHandler, $routeRegex, $routeArgs) = $route;
                
                $regex = explode(":", $routeRegex);//explode to get what and regex to set
                if(sizeof($regex) > 1){
                    $routePattern = str_replace("{".$regex[0]."}", $regex[1], $routePattern);
                }
                $routePattern = str_replace("/", "\/", $routePattern); //replace spaces

                //check method and url pattern
                if ($method === $routeMethod && preg_match("#^$routePattern$#", $url, $matches)){ 

                    array_shift($matches);

                    if(sizeof($matches) > 0){ //pass arguments for functions
                        $arg = $matches;
                    }else{
                        $arg = $routeArgs;
                    }
                    //get controller name
                    $controllerName = strtolower($routeHandler[0]);
                    $controller = $this->controllers[$controllerName];

                    //return controller file name and class, function to call and argument to pass
                    return [$controller, $routeHandler[1], $arg, $data];
                }
            }
            //route not found
            return [false, "message" => "Erreur : Route non trouvée", "http" => 404];
            
        }
                
        public function getAllRoutes(){
            // $listRoutes = [];
            foreach($this->routes as $route){
                //Assign variables as if they were an array
                list($routeMethod, $routePattern, $routeHandler, $routeRegex, $routeArgs) = $route;
                $listRoutes[] = ["methode" => $routeMethod, "url" => $routePattern, "arguments" => $routeArgs];
            }
            return $listRoutes;
        }
        

    }
        

?>
