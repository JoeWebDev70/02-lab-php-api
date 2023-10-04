<?php
    
    require_once './utilities/ResourceLogo.php';

    class Router{
        private $routes = [];
        private $controllers = [
            'all' => 'Router',
            'category' => 'CategoryController',
            'technology' => 'TechnologyController',
            'resource' => 'ResourceController',
        ];

        private $logo;
        
        //method
        public function addRoute($method, $pattern, $handler, $routeRegex, $arg = [], $routeExplanation = ""){
            $this->routes[] = [$method, $pattern, $handler, $routeRegex, $arg, $routeExplanation];
        }

        public function match(){ //match the route requested
            $url = urldecode($_SERVER['REQUEST_URI']);  //urldecode for spaces and specials char
            $method = $_SERVER['REQUEST_METHOD']; //get method
            $data = []; // for getting data in url if set

            //check if url is compatible with method
            if(($method === "POST" || $method === "PUT") && str_contains($url, "?")){ //explode data if it's necessary
                $dataExplode = explode("?", $url);
                $url = $dataExplode[0]; //get url for matching
                $data[] = $dataExplode[1]; //get data set in url
            }else if((($method === "GET" || $method === "DELETE") && str_contains($url, "?"))
                || (($method === "POST" || $method === "PUT") && !str_contains($url, "?"))){ //not allowed
                return [false, "message" => "Erreur : Methode non autorisée", "http" => 405];
            }
            
            foreach($this->routes as $route){ //loop on routes for matching
                //Assign variables as if they were an array
                list($routeMethod, $routePattern, $routeHandler, $routeRegex, $routeArgs) = $route;
                
                $regex = explode(":", $routeRegex);//explode to get what and regex to set
                if(sizeof($regex) > 1){ //replace id by number and name by letters
                    $routePattern = str_replace("{".$regex[0]."}", $regex[1], $routePattern);
                }
                $routePattern = str_replace("/", "\/", $routePattern); //replace spaces

                //check method and url pattern
                if ($method === $routeMethod && preg_match("#^$routePattern$#", $url, $matches)){ 
                    array_shift($matches);

                    if(sizeof($matches) > 0){ //pass arguments for functions
                        $arg = $matches; //need to be set by user
                    }else{
                        $arg = $routeArgs; //set by default
                    }

                    //get controller name
                    $controllerName = strtolower($routeHandler[0]);
                    $controller = $this->controllers[$controllerName];

                    // check if put or post some logo for technology
                    if(($method === "POST" || $method === "PUT") && $controller == "TechnologyController"){
                        $this->logo = new ResourceLogo();
                        $fullUrlDir = $_SERVER['REQUEST_SCHEME']."://".$_SERVER['HTTP_HOST'];
                        $this->logo->setUrlDir($fullUrlDir);
                        if (isset($_FILES['logo'])) { //check if contain some file
                            $dataTempLogo = $this->logo->logoDataTreatment($_FILES['logo']);
                        }else { //or data
                            $dataTempLogo = $this->logo->logoDataTreatment(file_get_contents('php://input'));
                        }
                        //set in data array to send in functions
                        if($dataTempLogo){$data[] = ["logo" => $dataTempLogo];}
                    }

                    //return controller file name and class, function to call and argument to pass
                    return [$controller, $routeHandler[1], $arg, $data];
                }
            }
            //route not found
            return [false, "message" => "Erreur : Route non trouvée", "http" => 404];
            
        }
        
        public function getAllRoutes(){ //display all routes and explaination
            foreach($this->routes as $route){
                //Assign variables as if they were an array
                list($routeMethod, $routePattern, $routeHandler, $routeRegex, $routeArgs, $routeExplanation) = $route;
                $listeRoutes[] = ["methode" => $routeMethod, "url" => $routePattern, "arguments" => $routeArgs, "explication" => $routeExplanation];
            }
            return $listeRoutes;
        }
        
    }
        

?>
