<?php
//controller
    // namespace Controllers\CategoryController;
    require_once './Database/Connection.php';
    require_once './Managers/CategoryManager.php';
    require_once './Entities/CategoryModel.php';

    class CategoryController{

        private $categoryManager;
        private $db;
        private $connection;

        // constructor
        public function __construct(){
            $this->setConnection();
            $this->categoryManager = new CategoryManager($this->connection); // create new instance of classe
        }

    //!injection protection
    //strip_tags : delete HTML and PHP tag from string
    //htmlspecialchars : convert special characters into HTML entities

        public function addCategory($arg, $data){
            $data = strval($data); //ensure is string
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

        //TODO
        // public function showCategoriesTechnologies($orderBy = "id"){ //show all categories and its technologies
        //     $orderBy = strval($orderBy); //ensure is string
        //     if($orderBy != "id" && $orderBy != "name"){$orderBy = "id";} //check if $orderBy exist in column category
        //     $result = $this->categoryManager->getLists($orderBy);
        //     var_dump($result);
            // if($result){ //formating response
            //     foreach($result as $category){
            //         $response[] = [
            //             'id' => $category->getId(),  
            //             'name' => $category->getName(),
            //         ];
            //     }
            //     return ["Categories" => $response];
            // }else{
            //     return ["message" => "Erreur dans la requête"];
            // }
        // }


        public function showCategoryById($id){ 
            $id = (int) $id; //ensure is INT
            $result = $this->categoryManager->getById($id);
            if($result[0]){ //formating data
                $response[] = [
                    'id' => $result[1]->getId(),  
                    'name' => $result[1]->getName(),
                ];
                return ["Categories" => $response];
            }else{
                return ["message" => $result[1]];
            }
        }

        public function showCategoryByName($name){  
            $name = strval($name);  //ensure is string
            $result = $this->categoryManager->getByName($name);
            if($result[0]){ //formating data
                $response[] = [
                    'id' => $result[1]->getId(),  
                    'name' => $result[1]->getName(),
                ];
                return ["Categories" => $response];
            }else{
                return ["message" => $result[1]];
            }
        }

        public function updateCategoryById($id, $data){ 
            $id = htmlspecialchars(strip_tags((int) $id));
            $data = explode("=", strval($data)); //ensure is string and explode data to create Category
            $name = htmlspecialchars(strip_tags($data[1]));
            $categoryData = [
                'name' => $name,
            ];
            //create an instance of category
            $category = new Category($categoryData);
            $result = $this->categoryManager->updateById($id, $category);
            return ["message" => $result[1]];
        }

        public function updateCategoryByName($name, $data){ 
            $name = htmlspecialchars(strip_tags($name));
            $data = explode("=", strval($data)); //ensure is string and explode data to create Category
            $newName = htmlspecialchars(strip_tags($data[1]));
            $categoryData = [
                'name' => $newName,
            ];
            //create an instance of category
            $category = new Category($categoryData);
            $result = $this->categoryManager->updateByName($name, $category);
            return ["message" => $result[1]];
        }

        public function deleteCategoryById($id, $data){ 
            $id = htmlspecialchars(strip_tags((int) $id));
            $categoryData = [
                'deleted' => true,
            ];
            //create an instance of category
            $category = new Category($categoryData);
            $result = $this->categoryManager->deleteById($id, $category);
            return ["message" => $result[1]];
        }

        public function deleteCategoryByName($name, $data){ 
            $name = htmlspecialchars(strip_tags(strval($name)));
            $categoryData = [
                'name' => $name,
                'deleted' => true,
            ];
            //create an instance of category
            $category = new Category($categoryData);
            $result = $this->categoryManager->deleteByName($name, $category);
            return ["message" => $result[1]];
        }

        public function setConnection(){
            $this->db = Database::getInstance();
            $this->connection = $this->db->getConnection();
        }

    }

?>

