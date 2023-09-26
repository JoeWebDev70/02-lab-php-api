<?php
//controller
    // namespace Controllers\CategoryController;
    require_once './Database/Connection.php';
    require_once './Managers/CategoryManager.php';

    class CategoryController{

    private $categoryManager;
    private $db;
    private $connection;

    // constructor
    public function __construct(){
        $this->setConnection();
        $this->categoryManager = new CategoryManager($this->connection); // create new instance of classe
    }

    public function showCategories($orderBy = "id"){ //show all categories 
        $orderBy = strval($orderBy); //ensure is string
        if($orderBy != "id" && $orderBy != "name"){$orderBy = "id";} //check if $orderBy exist in column category
        $result = $this->categoryManager->getList($orderBy);
        if($result){ //formating response
            foreach($result as $category){
                $response[] = [
                    'id' => $category->getId(),  
                    'name' => $category->getName(),
                ];
            }
            return ["Categories" => $response];
        }else{
            return ["message" => "Erreur dans la requête SQL : Aucune catégorie"];
        }
    }

    public function showCategoryById($id){ //show a category by id 
        $id = (int) $id; //ensure is INT
        $result = $this->categoryManager->getById($id);
        if($result){ //formating data
            $response[] = [
                'id' => $result->getId(),  
                'name' => $result->getName(),
            ];
            return ["Categories" => $response];
        }else{
            return ["message" => "Erreur dans la requête SQL : Aucune catégorie"];
        }
    }

    public function showCategoryByName($name){ //show a category by name 
        $name = strval($name);  //ensure is string
        $result = $this->categoryManager->getByName($name);
        if($result){ //formatting data
            $response[] = [
                'id' => $result->getId(),  
                'name' => $result->getName(),
            ];
            return ["Categories" => $response];
        }else{
            return ["message" => "Erreur dans la requête SQL : Aucune catégorie"];
        }
    }

    
                //TODO: mettre à la reception des données ds le controlleur
                    //injection protection
                    //strip_tags : delete HTML and PHP tag from string
                    //htmlspecialchars : convert special characters into HTML entities
                    // htmlspecialchars())


    public function setConnection(){
        $this->db = Database::getInstance();
        $this->connection = $this->db->getConnection();
    }


}

//    

//     // if($_SERVER["REQUEST_METHOD"] == "GET"){


    
    // }else{
    //     //405 method not allowed
    //     http_response_code(405);
    //     echo json_encode(["message" => "Erreur de méthode : pour lire vous devez utiliser la méthode GET "]);
    // }

?>

