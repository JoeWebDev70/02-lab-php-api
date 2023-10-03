<?php

    class Router{
        private $routes = [];
        private $controllers = [
            'all' => 'Router',
            'category' => 'CategoryController',
            'technology' => 'TechnologyController',
            'resource' => 'ResourceController',
        ];


        //method
        public function addRoute($method, $pattern, $handler, $routeRegex, $arg = [], $routeExplanation = ""){
            $this->routes[] = [$method, $pattern, $handler, $routeRegex, $arg, $routeExplanation];
        }

        public function match(){ //match the route requested
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

                    // check if put or post some logo for technology
                    if(($method === "POST" || $method === "PUT") && $controller == "TechnologyController"){
                        if (isset($_FILES['logo'])) { //check if contain some file
                            $dataTempLogo = $this->gestionFile($_FILES['logo']);
                        }else {
                            $dataTempLogo = $this->gestionFile(file_get_contents('php://input'));
                        }

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
        
        private function gestionFile($dataFile){ //logo file gestion
            $uploadDir = './resources_logo/'; 
            $temporaryFileName = 'temporaryLogo';
            $temporaryFilePath = $uploadDir.$temporaryFileName;
            if(is_array($dataFile)){ //file get by $_FILE
                //get the extension file
                $fileTmpType =  $_FILES['logo']['type']; 
                $extensionExplode = explode("+",$fileTmpType);
                $fileTmpExt = explode("/",$extensionExplode[0]);
                $fileExt = strtolower($fileTmpExt[1]);
                $tmpName = $_FILES['logo']['tmp_name'];
                //name and pass temporary file => ./resources_logo/temporaryLogo.[extension]
                $temporaryFileFullPath = $temporaryFilePath.".".$fileExt;
                if(move_uploaded_file($tmpName, $temporaryFileFullPath)){ //store the file temporarly with always the same name
                    return ["extension" => $fileExt, "path" => $uploadDir ,"tmp_name" => $temporaryFileFullPath];
                }
            }else{ // file get by binary
                if($dataFile != ""){ //if contains data
                    file_put_contents($temporaryFilePath, $dataFile); //store the file temporarly with always the same name
                    //get the extension file
                    $fileTmpType = mime_content_type($temporaryFilePath);
                    $extensionExplode = explode("+",$fileTmpType);
                    $fileTmpExt = explode("/",$extensionExplode[0]);
                    $fileExt = strtolower($fileTmpExt[1]);
                    //name and pass temporary file => ./resources_logo/temporaryLogo.[extension]
                    $temporaryFileFullPath = $temporaryFilePath.".".$fileExt;
                    if(rename($temporaryFilePath, $temporaryFileFullPath)){ //rename it with extension
                        return ["extension" => $fileExt, "path" => $uploadDir ,"tmp_name" => $temporaryFileFullPath];
                    }
                }
            }
            return false;

        }


    }
        

?>
