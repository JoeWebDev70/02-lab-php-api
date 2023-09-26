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
            $url = $_SERVER['REQUEST_URI']; 
            $method = $_SERVER['REQUEST_METHOD'];

            foreach($this->routes as $route){
                //Assign variables as if they were an array
                list($routeMethod, $routePattern, $routeHandler, $routeRegex, $routeArgs) = $route;
                
                $regex = explode(":", $routeRegex);//explode to get what and regex to set
                
                if(sizeof($regex) > 1){
                    $routePattern = str_replace("{".$regex[0]."}", $regex[1], $routePattern);
                }
                $routePattern = str_replace("/", "\/", $routePattern); //replace spaces
                //check method and url pattern -- urldecode for spaces and specials char
                if ($method === $routeMethod && preg_match("#^$routePattern$#", urldecode($url), $matches)){ 

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
                    return [$controller, $routeHandler[1], $arg];
                }
            }
            //not found route
            return false;
        }
                
        public function getAllRoutes(){
            $listRoutes = [];
            foreach($this->routes as $route){
                //Assign variables as if they were an array
                list($routeMethod, $routePattern, $routeHandler, $routeRegex, $routeArgs) = $route;
                $listRoutes[] = ["methode" => $routeMethod, "url" => $routePattern, "arguments" => $routeArgs];
            }
            return $listRoutes;
        }
        

    }
        

?>
