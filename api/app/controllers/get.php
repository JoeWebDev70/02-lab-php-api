<?php
    //required headers
    //acces for all sites and devices
    header("Access-Control-Allow-Origin: *");
    //format of data sending
    header("Content-Type: application/json; charset=UTF-8");
    //method GET for read
    header("Access-Control-Allow-Methods: GET");
    //cache this information for a specified time = request time life : 1hour
    header("Access-Control-Max-Age: 3600");

    // if($_SERVER["REQUEST_METHOD"] == "GET"){
        require './entities/category.php';
        // require './database/connection.php';
        // require './managers/categoryManager.php';

$a = new Category(["id"=>2]);
var_dump($a->getId());

        // $db = MySQL::getInstance();
        // $connection = $db->getConnection();

        // //create new instance of classe
        // $categoryManager = new CategoryManager($connection);
        // //get data
        // $result = $categoryManager->getList();
   
        // if($result){
        //     foreach($result as $category){
        //         var_dump($category);
        //         $reponse[] = [
        //             'id' => $category->id(),  
        //             'name' => $category->name()
        //         ];
        //     }
        //     //JSON_UNESCAPED_UNICODE : option to display correct words without unicode
        //     http_response_code(200);
        //     echo json_encode($reponse, JSON_UNESCAPED_UNICODE);
        // }else{
        //     echo json_encode(["message" => "Erreur dans la requête SQL : Aucune catégorie"]);
        // }

    
    // }else{
    //     //405 method not allowed
    //     http_response_code(405);
    //     echo json_encode(["message" => "Erreur de méthode : pour lire vous devez utiliser la méthode GET "]);
    // }

?>