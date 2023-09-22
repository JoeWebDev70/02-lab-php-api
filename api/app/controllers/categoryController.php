<?php
//controller

    require_once './database/connection.php';
    require_once './managers/categoryManager.php';

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

    public function showCategories(){
        $result = $this->categoryManager->getList();
        if($result){
            foreach($result as $category){

                $reponse[] = [
                    'id' => $category->getId(),  
                    'name' => $category->getName(),
                ];
            }
            //JSON_UNESCAPED_UNICODE : option to display correct words without unicode
            http_response_code(200);
            echo json_encode(["Categories" => $reponse], JSON_UNESCAPED_UNICODE);
        }else{
            echo json_encode(["message" => "Erreur dans la requête SQL : Aucune catégorie"]);
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

//     //required headers
    //acces for all sites and devices
    header("Access-Control-Allow-Origin: *");
    //format of data sending
    header("Content-Type: application/json; charset=UTF-8");
    //method GET for read
    header("Access-Control-Allow-Methods: GET");
    //cache this information for a specified time = request time life : 1hour
    header("Access-Control-Max-Age: 3600");

//     // if($_SERVER["REQUEST_METHOD"] == "GET"){


    
    // }else{
    //     //405 method not allowed
    //     http_response_code(405);
    //     echo json_encode(["message" => "Erreur de méthode : pour lire vous devez utiliser la méthode GET "]);
    // }

?>

