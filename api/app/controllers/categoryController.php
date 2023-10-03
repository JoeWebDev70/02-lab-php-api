<?php
    //controller
    require_once './Database/Connection.php';
    require_once './Managers/CategoryManager.php';
    require_once './Entities/CategoryModel.php';
   
//!injection protection
//strip_tags : delete HTML and PHP tag from string
//htmlspecialchars : convert special characters into HTML entities
    
    class CategoryController{

        private $categoryManager;
        private $db;
        private $connection;

        // constructor
        public function __construct(){ //set connection and create an instance of category manager
            $this->setConnection();
            $this->categoryManager = new CategoryManager($this->connection); 
        }

        //add
        public function addCategory($arg, $data){ 
            $data = strval($data[0]); //ensure is string
            $data = explode("=", $data); //explode data to create Category
            if(sizeof($data) > 0){
                $name = htmlspecialchars(strip_tags($data[1])); 
                $categoryData = [
                    'name' => $name,
                ];
                //create an instance of category
                $category = new Category($categoryData);
                $result = $this->categoryManager->add($category);
                
                return ["message" => $result[1], "http" => $result[2]];

            }else{
                return ["message" => "Erreur dans la requête", "http" => $result[2]];
            }
        }

        //get
        public function showCategories($orderBy = "id"){ //show all categories 
            $orderBy = strval($orderBy); //ensure is string
            if($orderBy != "id" && $orderBy != "name"){$orderBy = "id";} //check if $orderBy exist in column category
            $result = $this->categoryManager->getList($orderBy);
            if($result[0]){ //formating response
                foreach($result[1] as $category){
                    $response[] = [
                        'id' => $category->getId(),  
                        'name' => $category->getName(),
                    ];
                }
                return ["Categories" => $response, "http" => $result[2]];
            }else{
                return ["message" => $result[1], "http" => $result[2]];
            }
        }

        public function showCategoriesTechnologies($orderBy = "id"){ //show all categories which contains technologies
            $orderBy = strval($orderBy); //ensure is string
            if($orderBy != "id" && $orderBy != "name"){$orderBy = "id";} //check if $orderBy exist in column category
            $result = $this->categoryManager->getLists($orderBy);

            if($result[0]){ //formating response
                for($i = 0; $i < sizeof($result[1]); $i++){
                    $response[] = [
                        'id' => $result[1][$i][0]->getId(),  
                        'name' => $result[1][$i][0]->getName(),
                        'technologies_id' => $result[1][$i][1],
                        'technologies_names' => $result[1][$i][2],
                    ];
                }

                return ["Technologies par Catégorie" => $response, "http" => $result[2]];
            }else{
                return ["message" => $result[1], "http" => $result[2]];
            }
        }

        public function showCategoryBy($arg){ //show category by its name or id
            $arg = htmlspecialchars(strip_tags(strval($arg))); //check and format arg
            if(is_numeric($arg)){
                $arg = (int) $arg;
            }
            $result = $this->categoryManager->getBy($arg);
            
            if($result[0]){ //formating data
                $response[] = [
                    'id' => $result[1]->getId(),  
                    'name' => $result[1]->getName(),
                ];
                return ["Categories" => $response, "http" => $result[2]];
            }else{
                return ["message" => $result[1], "http" => $result[2]];
            }
        }

        public function showCategoryTechnologiesBy($arg){ //show category by name or id if have some technologies
            $arg = htmlspecialchars(strip_tags(strval($arg))); //check and format arg
            if(is_numeric($arg)){
                $arg = (int) $arg;
            }
            $result = $this->categoryManager->getListBy($arg); //send to manager functions
            if($result[0]){ //formating response
                for($i = 0; $i < sizeof($result[1]); $i++){
                    $response[] = [
                        'id' => $result[1][$i][0]->getId(),  
                        'name' => $result[1][$i][0]->getName(),
                        'technologies_id' => $result[1][$i][1],
                        'technologies_names' => $result[1][$i][2],
                    ];
                }
                return ["Technologies par Catégorie" => $response, "http" => $result[2]];
            }else{
                return ["message" => $result[1], "http" => $result[2]];
            }
        }

        //update
        public function updateCategoryBy($arg, $data){ //update category by name or id
            $arg = htmlspecialchars(strip_tags(strval($arg))); //check and format arg
            if(is_numeric($arg)){
                $arg = (int) $arg;
            }
            $data = explode("=", strval($data[0])); //ensure is string and explode data to create Category
            $name = htmlspecialchars(strip_tags($data[1]));
            $categoryData = [
                'name' => $name,
            ];
            //create an instance of category
            $category = new Category($categoryData);
            $result = $this->categoryManager->updateBy($arg, $category);
            return ["message" => $result[1], "http" => $result[2]];
        }
       
        //delete
        public function deleteCategoryBy($arg){  //delete category by its name or id if not contains technology
            $label = "name"; 
            $arg = htmlspecialchars(strip_tags(strval($arg))); //check and format arg
            if(is_numeric($arg)){
                $arg = (int) $arg;
                $label = "id";
            }
           
            $result = $this->categoryManager->deleteBy($arg);
            
            if(!$result[0] && $result[2] === 403){ //formating response   
                for($i = 0; $i < sizeof($result[1]); $i++){
                    $response[] = [
                        'id' => $result[1][$i][0]->getId(),  
                        'name' => $result[1][$i][0]->getName(),
                        'technologies_id' => $result[1][$i][1],
                        'technologies_names' => $result[1][$i][2],
                    ];
                }
                return ["message" => ["Erreur : Technologies présentent dans la Catégorie", $response], "http" => $result[2]]; 
            }else{                
                return ["message" => $result[1], "http" => $result[2]];
            }
        }

        public function setConnection(){ //get instance of connection to create db connection with pattern sigleton
            $this->db = Database::getInstance();
            $this->connection = $this->db->getConnection();
        }

    }

?>

