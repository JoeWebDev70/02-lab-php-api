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
        // create new instance of classe
        $this->categoryManager = new CategoryManager($this->connection);
    }

    public function showCategories($orderBy){
        $result = $this->categoryManager->getList($orderBy);
        if($result){
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

    public function showCategory($id){
        
        $id = (int) $id;
        $result = $this->categoryManager->get($id);
        if($result){
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
                    // htmlspecialchars(strip_tags())


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

