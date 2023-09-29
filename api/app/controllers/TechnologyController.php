<?php
//controller

    require_once './Database/Connection.php';
    require_once './Managers/TechnologyManager.php';
    require_once './Entities/TechnologyModel.php';

    class TechnologyController{

        private $technologyManager;
        private $db;
        private $connection;

        // constructor
        public function __construct(){
            $this->setConnection();
            $this->technologyManager = new TechnologyManager($this->connection); // create new instance of classe
        }

    //!injection protection
    //strip_tags : delete HTML and PHP tag from string
    //htmlspecialchars : convert special characters into HTML entities

    //TODO check for factorization
        public function addTechnology($arg, $data){ 
            $name = null;
            $logo = "";
            $categoryId = null;

            $dataTechnology = strval($data[0]); //ensure is string
            $dataTechnology = explode("&", $dataTechnology); //explode data to create Technology
            
            if(sizeof($dataTechnology) > 0){ //check if contain values 
                foreach($dataTechnology as $key){
                    $dataExplode[] = explode("=", $key); 
                }
                
                for($i = 0; $i < sizeof($dataExplode); $i++){ //get values
                    for($j = 0; $j < sizeof($dataExplode[$i]); $j++){ 
                        if($dataExplode[$i][$j] == 'name'){
                            $name = htmlspecialchars(strip_tags($dataExplode[$i][$j+1]));
                        }else if($dataExplode[$i][$j] == 'categoryId'){
                            $categoryId = (int) htmlspecialchars(strip_tags($dataExplode[$i][$j+1]));
                        }
                    }
                }

                //create new technology
                if($name != null && ($categoryId != null && $categoryId > 0)){
                    if(isset($data[1]['logo'])){ //some logo is set
                        $fileExt = $data[1]['logo']['extension'];
                        //check if its valid format
                        if (in_array($fileExt, array('jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'))) {
                            $fileName = $name."_".$categoryId.".".$fileExt;
                            $fileDir = $data[1]['logo']['path'];
                            $fileFullPath = $fileDir.$fileName;
                            $temporaryFileFullPath = $data[1]['logo']['tmp_name'];
                            $logo = $fileFullPath;
                            
                        }
                    }
 
                    $technologyData = [
                        'name' => $name,
                        'logo' => $logo,
                        'categoryId' => $categoryId,
                    ];
                                    
                    $technology = new Technology($technologyData);
                    $result = $this->technologyManager->add($technology);
                    
                    if($result[2] == 201){ 
                        rename($temporaryFileFullPath, $fileFullPath);
                    }else{
                        unlink($temporaryFileFullPath);
                    }

                    return ["message" => $result[1], "http" => $result[2]];
                        
                }else{
                    unlink($temporaryFileFullPath);
                    return ["message" => "Erreur dans la requête", "http" => 400 ];
                }
            }else{
                return ["message" => "Erreur dans la requête", "http" => 400 ];
            }
        }

        public function showTechnologies($orderBy = "id"){ //show all technologies
            $orderBy = strval($orderBy); //ensure is string
            if($orderBy != "id" && $orderBy != "name"){$orderBy = "id";} //check if $orderBy exist in column category
            $result = $this->technologyManager->getList($orderBy);
            if($result[0]){ //formating response
                for($i = 0; $i < sizeof($result[1]); $i++){
                    $response[] = [
                        'id' => $result[1][$i][0]->getId(),  
                        'name' => $result[1][$i][0]->getName(),
                        'logo' => $result[1][$i][0]->getLogo(),
                        'categoryId' => $result[1][$i][0]->getCategoryId(),
                        'categoryName' => $result[1][$i][1],
                    ];
                }
                return ["Technologies" => $response, "http" => $result[2]];
            }else{
                return ["message" => $result[1], "http" => $result[2]];
            }
        }

        public function showTechnologyById($id){ 
            $id = (int) $id; //ensure is INT
            $result = $this->technologyManager->getById($id);
            if($result[0]){ //formating response
                for($i = 0; $i < sizeof($result[1]); $i++){
                    $response[] = [
                        'id' => $result[1][$i][0]->getId(),  
                        'name' => $result[1][$i][0]->getName(),
                        'logo' => $result[1][$i][0]->getLogo(),
                        'categoryId' => $result[1][$i][0]->getCategoryId(),
                        'categoryName' => $result[1][$i][1],
                    ];
                }
                return ["Technologies" => $response, "http" => $result[2]];
            }else{
                return ["message" => $result[1], "http" => $result[2]];
            }
        }

        public function showTechnologyByName($name){  
            $name = strval($name);  //ensure is string
            $result = $this->technologyManager->getByName($name);
            if($result[0]){ //formating response
                for($i = 0; $i < sizeof($result[1]); $i++){
                    $response[] = [
                        'id' => $result[1][$i][0]->getId(),  
                        'name' => $result[1][$i][0]->getName(),
                        'logo' => $result[1][$i][0]->getLogo(),
                        'categoryId' => $result[1][$i][0]->getCategoryId(),
                        'categoryName' => $result[1][$i][1],
                    ];
                }
                return ["Technologies" => $response, "http" => $result[2]];
            }else{
                return ["message" => $result[1], "http" => $result[2]];
            }
        }

//TODO check for id is_numeric before ((int)) for other function also in category
        //update
        public function updateTechnology($arg, $data){
            if(is_numeric(htmlspecialchars(strip_tags($arg)))){
                $id = (int) htmlspecialchars(strip_tags($arg));
            }else{
                return ["message" => "Erreur dans la requête", "http" => 400 ];
            }
            
            $name = "";
            $logo = "";
            $categoryId = "";

            $dataTechnology = strval($data[0]); //ensure is string
            $dataTechnology = explode("&", $dataTechnology); //explode data to create Technology
            
            if(sizeof($dataTechnology) > 0){ //check if contain values 
                foreach($dataTechnology as $key){
                    $dataExplode[] = explode("=", $key); 
                }
                
                for($i = 0; $i < sizeof($dataExplode); $i++){ //get values
                    for($j = 0; $j < sizeof($dataExplode[$i]); $j++){ 
                        if($dataExplode[$i][$j] == 'name'){
                            $name = htmlspecialchars(strip_tags($dataExplode[$i][$j+1]));
                        }else if($dataExplode[$i][$j] == 'categoryId'){
                            $categoryId = (int) htmlspecialchars(strip_tags($dataExplode[$i][$j+1]));
                        }
                    }
                }

                //create new technology
                if($id != null){
                    $oldTechnology = $this->technologyManager->getById($id);
                    if($oldTechnology[0]){ //formating response
                        for($i = 0; $i < sizeof($oldTechnology[1]); $i++){
                            $oldName = $oldTechnology[1][$i][0]->getName();
                            $oldLogo = $oldTechnology[1][$i][0]->getLogo();
                            $oldCategory = $oldTechnology[1][$i][0]->getCategoryId();
                        }
                    
                        if($name == ""){$name = $oldName;}       
                        if($categoryId == ""){$categoryId = $oldCategory;}
                        
                        if(isset($data[1]['logo'])){ //some logo is set
                            $newFileExt = $data[1]['logo']['extension'];
                            //check if its valid format
                            if (in_array($newFileExt, array('jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'))) {
                                
                                $fileName = $newName ."_".$newCategoryId.".".$newFileExt;
                                $fileDir = $data[1]['logo']['path'];
                                $fileFullPath = $fileDir.$fileName;
                                $temporaryFileFullPath = $data[1]['logo']['tmp_name'];
                                $logo = $fileFullPath;
                            }
                        }

                        if($logo == ""){$logo = $oldLogo;}

                    }else{
                        return ["message" => $result[1], "http" => $result[2]];
                    }

                    $technologyData = [
                        'id' => $id,
                        'name' => $name,
                        'logo' => $logo,
                        'categoryId' => $categoryId,
                    ];
                                    
                    $technology = new Technology($technologyData);
                    $result = $this->technologyManager->update($id, $technology);
                    
                    //TODO si ok result alors enlever old logo des fichiers et set nouveau
                    //sinon détruire nouveau


                    return ["message" => $result[1], "http" => $result[2]];
                
                }else{
                    return ["message" => "Erreur dans la requête", "http" => 400 ];
                }
            }else{
                return ["message" => "Erreur dans la requête", "http" => 400 ];
            }
        }

        public function setConnection(){
            $this->db = Database::getInstance();
            $this->connection = $this->db->getConnection();
        }

    }

?>

