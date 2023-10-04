<?php
//controller

    require_once './Database/Connection.php';
    require_once './Managers/TechnologyManager.php';
    require_once './Entities/TechnologyModel.php';
    require_once './utilities/ResourceLogo.php';

//!injection protection
//strip_tags : delete HTML and PHP tag from string
//htmlspecialchars : convert special characters into HTML entities
    
    class TechnologyController{

        private $technologyManager;
        private $db;
        private $connection;

        // constructor
        public function __construct(){
            $this->setConnection();
            $this->technologyManager = new TechnologyManager($this->connection); // create new instance of classe
        }

        //add
        public function addTechnology($arg, $data){ 
            $name = null;
            $logo = "";
            $fileFullPath = null;
            $temporaryFileFullPath = null;
            $categoryId = null;

            $dataTechnology = strval($data[0]); //ensure is string
            $dataTechnology = explode("&", $dataTechnology); //explode data to create Technology
            
            if(sizeof($dataTechnology) > 0){ //check if contain values 
                foreach($dataTechnology as $key){
                    $dataExplode[] = explode("=", $key); 
                }
                
                //set data 
                $nameAndCategory = $this->setNameAndCategoryId($dataExplode); 
                $name = $nameAndCategory[0];
                $categoryId = $nameAndCategory[1];

                //if technology name and category id are set 
                if($name != null && ($categoryId != null && $categoryId > 0)){
                    
                    if(isset($data[1]['logo'])){ //some logo is set
                        $pathLogo = $this->setLogoPath($name, $categoryId, $data[1]['logo']);
                        if($pathLogo){
                            $temporaryFileFullPath = $pathLogo[0];
                            $fileFullPath = $pathLogo[1];
                            $logo = $pathLogo[2];
                        }
                    }
                    
                    //create technology instance
                    $technologyData = [
                        'name' => $name,
                        'logo' => $logo,
                        'categoryId' => $categoryId,
                    ];
                                    
                    $technology = new Technology($technologyData);
                    $result = $this->technologyManager->add($technology);
                    
                    if($result[2] == 201){ //if result of SQL is OK then rename the temporary logo file
                        if($temporaryFileFullPath != null){rename($temporaryFileFullPath, $fileFullPath);}
                    }else{ //delete it
                        if($temporaryFileFullPath != null){unlink($temporaryFileFullPath);}
                    }

                    return ["message" => $result[1], "http" => $result[2]];
                        
                }else{ //Miss name or category id  to create = delete temporary file and send error message
                    if($temporaryFileFullPath != null){unlink($temporaryFileFullPath);}
                    return ["message" => "Erreur dans la requête", "http" => 400 ];
                }
            }else{ //some error in technologie data send
                if($temporaryFileFullPath != null){unlink($temporaryFileFullPath);}
                return ["message" => "Erreur dans la requête", "http" => 400 ];
            }
        }

        //get
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
                        'resources' => $result[1][$i][2],
                    ];
                }

                return ["Technologies" => $response, "http" => $result[2]];
            }else{
                return ["message" => $result[1], "http" => $result[2]];
            }
        }

        public function showTechnologyBy($arg){  
            $arg = htmlspecialchars(strip_tags(strval($arg)));  //ensure is string
            if(is_numeric($arg)){
                $arg = (int) $arg;
            }
            $result = $this->technologyManager->getBy($arg);

            if($result[0]){ //formating response
                for($i = 0; $i < sizeof($result[1]); $i++){
                    $response[] = [
                        'id' => $result[1][$i][0]->getId(),  
                        'name' => $result[1][$i][0]->getName(),
                        'logo' => $result[1][$i][0]->getLogo(),
                        'categoryId' => $result[1][$i][0]->getCategoryId(),
                        'categoryName' => $result[1][$i][1],
                        'resources' => $result[1][$i][2],
                    ];
                }
                return ["Technologies" => $response, "http" => $result[2]];
            }else{
                return ["message" => $result[1], "http" => $result[2]];
            }
        }

        //update
        public function updateTechnology($arg, $data){
            if(is_numeric(htmlspecialchars(strip_tags($arg)))){
                $id = (int) htmlspecialchars(strip_tags($arg));
            }else{
                return ["message" => "Erreur dans la requête", "http" => 400 ];
            }
            
            $name = "";
            $logo = "";
            $fileFullPath = null;
            $temporaryFileFullPath = null;
            $categoryId = "";

            $dataTechnology = strval($data[0]); //ensure is string
            $dataTechnology = explode("&", $dataTechnology); //explode data to create Technology
            
            if(sizeof($dataTechnology) > 0){ //check if contain values 
                foreach($dataTechnology as $key){
                    $dataExplode[] = explode("=", $key); 
                }
                
                //set data
                $nameAndCategory = $this->setNameAndCategoryId($dataExplode); 
                $name = $nameAndCategory[0];
                $categoryId = $nameAndCategory[1];

                
                if($id != null){
                    //get old data
                    $oldTechnology = $this->technologyManager->getBy($id);

                    if($oldTechnology[0]){ //formating response
                        for($i = 0; $i < sizeof($oldTechnology[1]); $i++){
                            $oldName = $oldTechnology[1][$i][0]->getName();
                            $oldLogo = $oldTechnology[1][$i][0]->getLogo();
                            $oldCategory = $oldTechnology[1][$i][0]->getCategoryId();
                        }
                    
                        if($name == ""){$name = $oldName;}       
                        if($categoryId == ""){$categoryId = $oldCategory;}
                    
                        if(isset($data[1]['logo'])){ //some logo is set
                            $pathLogo = $this->setLogoPath($name, $categoryId, $data[1]['logo']);
                            if($pathLogo){
                                $temporaryFileFullPath = $pathLogo[0];
                                $fileFullPath = $pathLogo[1];
                                $logo = $pathLogo[2];
                            }
                        }

                        if($logo == ""){
                            if($oldLogo == null){ //never set logo
                                $logo = "";
                            }else{ //set before but need update name but not extension
                                $UpdatePathLogo = $this->updateLogoPath($name, $categoryId, $oldLogo);
                                if($UpdatePathLogo){
                                    $oldLogoInternPath = $UpdatePathLogo[0];
                                    $newLogoInternPath = $UpdatePathLogo[1];
                                    $logo = $UpdatePathLogo[2];
                                }
                            }
                        }

                    }else{
                        return ["message" => $oldTechnology[1], "http" => $oldTechnology[2]];
                    }

                    //create new instance of technology
                    $technologyData = [
                        'id' => $id,
                        'name' => $name,
                        'logo' => $logo,
                        'categoryId' => $categoryId,
                    ];
                                    
                    $technology = new Technology($technologyData);
                    $result = $this->technologyManager->update($id, $technology);
                    
                    if($result[2] == 200){ //if result of SQL is OK then rename the temporary logo file
                        if($temporaryFileFullPath != null){ //new logo set
                            if($oldLogo != "" && isset($oldLogoInternPath)){unlink($oldLogoInternPath);}
                            rename($temporaryFileFullPath,  $fileFullPath);
                        }else{ //rename old logo
                            if(isset($oldLogoInternPath)){
                                rename($oldLogoInternPath, $newLogoInternPath);
                            }
                        }
                    }else{ //delete the new logo set
                        if($temporaryFileFullPath != null){unlink($temporaryFileFullPath);}
                    }

                    return ["message" => $result[1], "http" => $result[2]];
                
                }else{
                    if($temporaryFileFullPath != null){unlink($temporaryFileFullPath);}
                    return ["message" => "Erreur dans la requête", "http" => 400 ];
                }
            }else{
                if($temporaryFileFullPath != null){unlink($temporaryFileFullPath);}
                return ["message" => "Erreur dans la requête", "http" => 400 ];
            }
        }

        //delete
        public function deleteTechnology($arg){
            if(is_numeric(htmlspecialchars(strip_tags($arg)))){
                $id = (int) htmlspecialchars(strip_tags($arg));
            }else{
                return ["message" => "Erreur dans la requête", "http" => 400 ];
            }
            $result = $this->technologyManager->delete($id);
            return ["message" => $result[1], "http" => $result[2]];
        }

        private function setNameAndCategoryId($dataExplode){
            $name = null;
            $categoryId = null;
            for($i = 0; $i < sizeof($dataExplode); $i++){ //get values to create instance of technology
                for($j = 0; $j < sizeof($dataExplode[$i]); $j++){ 
                    if($dataExplode[$i][$j] == 'name'){
                        $name = htmlspecialchars(strip_tags($dataExplode[$i][$j+1]));
                    }else if($dataExplode[$i][$j] == 'categoryId'){
                        if(is_numeric(htmlspecialchars(strip_tags($dataExplode[$i][$j+1])))){
                           $categoryId = (int) htmlspecialchars(strip_tags($dataExplode[$i][$j+1]));
                        }
                    }
                }
            }
            return [$name, $categoryId];
        }

        private function setLogoPath($name, $categoryId, $dataFile){
            $fileExt = $dataFile['extension']; //check if its valid format
            if (in_array($fileExt, array('jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'))) {
                $resourceLogo = new ResourceLogo();
                $fileName = $name."_".$categoryId.".".$fileExt;
                $temporaryFileFullPath = $dataFile['tmp_name'];
                $fileInternDir = $resourceLogo->getInternDir();
                $fileFullPath = $fileInternDir.$fileName;
                $urlPath = $dataFile["url_path"];
                $displayPath = $urlPath.$fileName;
            }else{
                return false;
            }
            return [$temporaryFileFullPath, $fileFullPath, $displayPath];
        }

        private function updateLogoPath($name, $categoryId, $dataFile){
            // var_dump($dataFile);
            $resourceLogo = new ResourceLogo();
            $fileInternDir = $resourceLogo->getInternDir();
            $fileUploadDir = $resourceLogo->getUploadDir();

            $oldLogoExplodeFullPath = explode($fileUploadDir, $dataFile);
            $oldLogoInternPath = $fileInternDir.$oldLogoExplodeFullPath[1];
            $oldLogoExt = explode(".",$oldLogoExplodeFullPath[1]);
            $fileName = $name ."_".$categoryId.".".$oldLogoExt[1];
            
            $newLogoInternPath = $fileInternDir.$fileName;
            $displayPath = $oldLogoExplodeFullPath[0].$fileUploadDir.$fileName;
            
            return [$oldLogoInternPath, $newLogoInternPath, $displayPath];
        }

        public function setConnection(){ //set connection db
            $this->db = Database::getInstance();
            $this->connection = $this->db->getConnection();
        }

    }

?>

