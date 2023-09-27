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

        public function addTechnology($arg, $data){
            $data = strval($data); //ensure is string
            $data = explode("&", $data); //explode data to create Technology
            $name = null;
            $logo = "";
            $categoryId = null;

            if(sizeof($data) > 0){ //check if contain values 
                foreach($data as $key){
                    $dataExplode[] = explode("=", $key); 
                }

                for($i = 0; $i < sizeof($dataExplode); $i++){ //get values
                    for($j = 0; $j < sizeof($dataExplode[$i]); $j++){ 
                        if($dataExplode[$i][$j] == 'name'){
                            $name = htmlspecialchars(strip_tags($dataExplode[$i][$j+1]));
                        }else if($dataExplode[$i][$j] == 'logo'){
                            $logo = htmlspecialchars(strip_tags($dataExplode[$i][$j+1]));
                        }else if($dataExplode[$i][$j] == 'categoryId'){
                            $categoryId = (int) htmlspecialchars(strip_tags($dataExplode[$i][$j+1]));
                        }
                    }
                }
                //create new technology
                if($name != null && ($categoryId != null && $categoryId > 0)){
                    $technologyData = [
                        'name' => $name,
                        'logo' => $logo,
                        'categoryId' => $categoryId,
                    ];
                                    
                    $technology = new Technology($technologyData);
                    $result = $this->technologyManager->add($technology);
                    
                    return ["message" => $result[1], "http" => $result[2]];
                
                }else{
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

        // public function updateCategoryById($id, $data){ 
        //     $id = htmlspecialchars(strip_tags((int) $id));
        //     $data = explode("=", strval($data)); //ensure is string and explode data to create Category
        //     $name = htmlspecialchars(strip_tags($data[1]));
        //     $categoryData = [
        //         'name' => $name,
        //     ];
        //     //create an instance of category
        //     $category = new Category($categoryData);
        //     $result = $this->categoryManager->updateById($id, $category);
        //     return ["message" => $result[1]];
        // }

        

        // public function deleteCategoryById($id, $data){ 
        //     $id = htmlspecialchars(strip_tags((int) $id));
        //     $categoryData = [
        //         'deleted' => true,
        //     ];
        //     //create an instance of category
        //     $category = new Category($categoryData);
        //     $result = $this->categoryManager->deleteById($id, $category);
        //     return ["message" => $result[1]];
        // }

        

        public function setConnection(){
            $this->db = Database::getInstance();
            $this->connection = $this->db->getConnection();
        }

    }

?>

