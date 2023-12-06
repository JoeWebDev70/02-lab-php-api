<?php
    //controller
    require_once './Database/Connection.php';
    require_once './Managers/ResourceManager.php';
    require_once './Entities/ResourceModel.php';
   
//!injection protection
//strip_tags : delete HTML and PHP tag from string
//htmlspecialchars : convert special characters into HTML entities
    
    class ResourceController{

        private $resourceManager;
        private $db;
        private $connection;
        private $technology;

        // constructor
        public function __construct(){ //set connection and create an instance of resourcemanager
            $this->setConnection();
            $this->resourceManager = new ResourceManager($this->connection); 
            $this->technology = new TechnologyManager($this->connection);

        }

        //add
        public function addResource($arg, $data){
            $url = null;
            $technologyId = null;

            $dataResource = strval($data[0]); //ensure is string
            $dataResource  = explode("&", $dataResource); //explode data to create Resource
            
            if(sizeof($dataResource) > 0){ //check if contain values 
                foreach($dataResource as $key){
                    $dataExplode[] = explode("=", $key); 
                }
            }

            //set data 
            $UrlAndTechnologyId = $this->setUrlAndTechnologyId($dataExplode); 
            $url = $UrlAndTechnologyId[0];
            $technologyId = $UrlAndTechnologyId[1];

            //create new instance of resource
            if($url != null && ($technologyId != null && $technologyId > 0)){ //if data are set
                $technologyExist = $this->technology->getBy($technologyId);//check if technology exist
                if($technologyExist[0]){ //technology exist and wasn't deleted - CF. public function getBy($idOrName) in TechnologyManager
                
                    $resourceData = [
                        'url' => $url,
                        'technologyId' => $technologyId,
                    ];
                                    
                    $resource = new Resource($resourceData);
                    $result = $this->resourceManager->add($resource);

                    return ["message" => $result[1], "http" => $result[2]];
                
                }else{ //technology doesn't exist or some error in SQL execution
                    return [$technologyExist[0], $technologyExist[1], $technologyExist[2]];
                }
            }else{ //Miss url or technology id  
                return ["message" => "Erreur dans la requête", "http" => 400 ];
            }
        }

        //get
        public function showResources(){
            $result = $this->resourceManager->getList();

            if($result[0]){ //formating response
                for($i = 0; $i < sizeof($result[1]); $i++){
                    $response[] = [
                        'id' => $result[1][$i][0]->getId(),  
                        'url' => htmlspecialchars_decode($result[1][$i][0]->getUrl()),
                        'technologyId' => $result[1][$i][0]->getTechnologyId(),
                        'technologyName' => htmlspecialchars_decode($result[1][$i][1]),
                    ];
                }
                return ["Ressources" => $response, "http" => $result[2]];
            }else{
                return ["message" => $result[1], "http" => $result[2]];
            }
        }

        public function showResource($id){
            $id = (int) htmlspecialchars(strip_tags(strval($id))); 
            $result = $this->resourceManager->get($id);

            if($result[0]){ //formating response
                for($i = 0; $i < sizeof($result[1]); $i++){
                    $response[] = [
                        'id' => $result[1][$i][0]->getId(),  
                        'url' => htmlspecialchars_decode($result[1][$i][0]->getUrl()),
                        'technologyId' => $result[1][$i][0]->getTechnologyId(),
                        'technologyName' => htmlspecialchars_decode($result[1][$i][1]),
                    ];
                }
                return ["Ressources" => $response, "http" => $result[2]];
            }else{
                return ["message" => $result[1], "http" => $result[2]];
            }
        }
        
        public function showResourcesFor($idTechnology){
            if(is_numeric(htmlspecialchars(strip_tags($idTechnology)))){
                $idTechnology = (int) htmlspecialchars(strip_tags($idTechnology));
            }else{
                return ["message" => "Erreur dans la requête", "http" => 400 ];
            }
            $technologyExist = $this->technology->getBy($idTechnology);//check if technology exist
                if($technologyExist[0]){ //technology exist and wasn't deleted - CF. public function getBy($idOrName) in TechnologyManager
                
                    
                    $result = $this->resourceManager->getListFor($idTechnology);

                    if($result[0]){ //formating response
                        for($i = 0; $i < sizeof($result[1]); $i++){
                            $response[] = [
                                'id' => $result[1][$i][0]->getId(),  
                                'url' => htmlspecialchars_decode($result[1][$i][0]->getUrl()),
                            ];
                        }
                        return ["Ressources" => $response, "http" => $result[2]];
                    }else{
                        return ["message" => $result[1], "http" => $result[2]];
                    }
                
                }else{ //technology doesn't exist or some error in SQL execution
                    return [$technologyExist[0], $technologyExist[1], $technologyExist[2]];
                }
        }

        //update
        public function updateResource($arg, $data){
            if(is_numeric(htmlspecialchars(strip_tags($arg)))){
                $id = (int) htmlspecialchars(strip_tags($arg));
            }else{
                return ["message" => "Erreur dans la requête", "http" => 400 ];
            }

            $url = "";
            $technologyId = "";

            $dataResource = strval($data[0]); //ensure is string
            $dataResource  = explode("&", $dataResource); //explode data to create Resource
            
            if(sizeof($dataResource) > 0){ //check if contain values 
                foreach($dataResource as $key){
                    $dataExplode[] = explode("=", $key); 
                }
            }

            //set data 
            $UrlAndTechnologyId = $this->setUrlAndTechnologyId($dataExplode); 
            $url = $UrlAndTechnologyId[0];
            $technologyId = $UrlAndTechnologyId[1];

            if($id != null){
                $technologyExist = $this->technology->getBy($technologyId);//check if technology exist
                if($technologyExist[0]){ //technology exist and wasn't deleted - CF. public function getBy($idOrName) in TechnologyManager
                
                    $oldResource = $this->resourceManager->get($id);
                    if($oldResource[0]){ //formating response
                        for($i = 0; $i < sizeof($oldResource[1]); $i++){
                            $oldUrl = $oldResource[1][$i][0]->getUrl();
                            $oldTechnology = $oldResource[1][$i][0]->getTechnologyId();
                        }
                    
                        if($url == ""){$url = $oldUrl;}       
                        if($technologyId == ""){$technologyId = $oldTechnology;}
                    
                    }else{
                        return ["message" => $oldResource[1], "http" => $oldResource[2]];
                    }

                    $resourceData = [
                        'id' => $id,
                        'url' => $url,
                        'technologyId' => $technologyId,
                    ];

                    $resource = new Resource($resourceData);
                    $result = $this->resourceManager->update($id, $resource);

                    return ["message" => $result[1], "http" => $result[2]];
                
                }else{ //technology doesn't exist or some error in SQL execution
                    return [$technologyExist[0], $technologyExist[1], $technologyExist[2]];
                }
                    
            }else{ //miss id  
                return ["message" => "Erreur dans la requête", "http" => 400 ];
            }
        }
        
        //delete 
        public function deleteResource($arg){
            if(is_numeric(htmlspecialchars(strip_tags($arg)))){
                $id = (int) htmlspecialchars(strip_tags($arg));
            }else{
                return ["message" => "Erreur dans la requête", "http" => 400 ];
            }
            $result = $this->resourceManager->delete($id);
            return ["message" => $result[1], "http" => $result[2]];
        }

        private function setUrlAndTechnologyId($dataExplode){
            $url = null;
            $technologyId = null;
            for($i = 0; $i < sizeof($dataExplode); $i++){ //get values to create instance of resource
                for($j = 0; $j < sizeof($dataExplode[$i]); $j++){ 
                    if($dataExplode[$i][$j] == 'url'){
                        $url = htmlspecialchars(strip_tags($dataExplode[$i][$j+1]));
                    }else if($dataExplode[$i][$j] == 'technologyId'){
                        if(is_numeric(htmlspecialchars(strip_tags($dataExplode[$i][$j+1])))){
                        $technologyId = (int) htmlspecialchars(strip_tags($dataExplode[$i][$j+1]));
                        }
                    }
                }
            }
            return [$url, $technologyId];
        }

        public function setConnection(){ //get instance of connection to create db connection with pattern sigleton
            $this->db = Database::getInstance();
            $this->connection = $this->db->getConnection();
        }

    }

?>

