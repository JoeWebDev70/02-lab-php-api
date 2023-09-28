<?php 
//manager = DAO - Data Access Object
    require_once './Entities/TechnologyModel.php';

    class TechnologyManager {
        private $connection; //PDO instance

        //constructor for db connection
        public function __construct($connection){
            $this->setConnection($connection);
        }

        //add
        public function add(Technology $technology){ //add new technology
            //get data to create technology
            $name = $technology->getName(); 
            $logo = $technology->getLogo(); 
            if($logo == ""){$logo = null;} //pass null for not creating void
            $categoryId = $technology->getCategoryId();
    //TODO check if category exist
            //check if technology exist with this category 
            $sql = "SELECT * FROM technology AS t 
                    WHERE t.name = :name AND t.category_id = :categoryId"; 
            $sth = $this->connection->prepare($sql);
            $sth->bindParam(':name', $name, PDO::PARAM_STR);
            $sth->bindParam(':categoryId', $categoryId, PDO::PARAM_INT);
            $sth->execute();
            $result = $sth->fetch(PDO::FETCH_ASSOC); 
            if(!$result){ //doesn't exist yet with this category then insert in db
                $sql = "INSERT INTO technology(name, logo, category_id) VALUES(:name, :logo, :categoryId)";
                $sth = $this->connection->prepare($sql);
                $sth->bindParam(':name', $name, PDO::PARAM_STR);
                $sth->bindParam(':logo', $logo, PDO::PARAM_STR);
                $sth->bindParam(':categoryId', $categoryId, PDO::PARAM_INT);
                if($sth->execute()){ 
                    return [true, 'Succès : Technology créée', 201];
                }else{
                    return [false, "Erreur : Dans l'execution de la requête", 400];
                }
            }else if ($result["deleted"] == 1){ // exist but it was deleted before then update
                $sql = "UPDATE technology AS t SET t.deleted = 0 
                        WHERE t.name = :name AND t.category_id = :categoryId";
                $sth = $this->connection->prepare($sql);
                $sth->bindParam(':name', $name, PDO::PARAM_STR);
                $sth->bindParam(':categoryId', $categoryId, PDO::PARAM_INT);
                if($sth->execute()){
                    return [true, 'Succès : Catégorie créée', 201];
                }else{
                    return [false, "Erreur : Dans l'execution de la requête", 400];
                }
            }else{
                return [false, 'Erreur : Technology déjà existante avec cette Catégorie', 403];
            } 
        }

        //get
        public function getList($orderBy){ //display all categories
            $sql = "SELECT t.id, t.name, t.logo, t.category_id, c.name AS 'category_name'
                    FROM (technology AS t 
                    LEFT JOIN category AS c ON c.id = t.category_id ) 
                    WHERE t.deleted = 0 ORDER BY t." . $orderBy . " ASC";

            $sth = $this->connection->prepare($sql);
            $sth->execute();
            while ($data = $sth->fetch(PDO::FETCH_ASSOC)){
                if($data["logo"] == null){$logo = "";}else{$logo = $data["logo"];}
                $dataTechnology = ["id" => $data["id"], "name" => $data["name"], "logo" => $logo, "categoryId" => $data["category_id"]];
                $result[] = [new Technology($dataTechnology), $data["category_name"]]; 
            }; 

            if(sizeof($result) <= 0){ 
                return [false, "Erreur : Aucune technology existante", 404];
            }else{
                return [true, $result, 200];
            }
            
        }

        public function getById($id){
            //c.id = ? LIMIT 0,1 : where condition return first ligne found 
            $sql = "SELECT t.id, t.name, t.logo, t.category_id, c.name AS 'category_name'
                    FROM (technology AS t 
                    LEFT JOIN category AS c ON c.id = t.category_id ) 
                    WHERE t.deleted = 0 AND t.id = ? LIMIT 0,1";  
            $sth = $this->connection->prepare($sql);
            $sth->bindParam(1, $id, PDO::PARAM_INT);
            $sth->execute();
            $data = $sth->fetch(PDO::FETCH_ASSOC); 
            if($data){ //check if contain some data
                if($data["logo"] == null){$logo = "";}else{$logo = $data["logo"];}
                $dataTechnology = ["id" => $data["id"], "name" => $data["name"], "logo" => $logo, "categoryId" => $data["category_id"]];
                $result[] = [new Technology($dataTechnology), $data["category_name"]]; 
                return [true, $result, 200];
            }else{
                return [false, "Erreur : Aucune catégorie existante", 404];
            }    
        }

        public function getByName($name){
            $sql = "SELECT t.id, t.name, t.logo, t.category_id, c.name AS 'category_name'
                    FROM (technology AS t 
                    LEFT JOIN category AS c ON c.id = t.category_id ) 
                    WHERE t.deleted = 0 AND t.name = :name";  
            $sth = $this->connection->prepare($sql);
            $sth->bindParam(':name', $name, PDO::PARAM_STR);
            $sth->execute();
            $data = $sth->fetch(PDO::FETCH_ASSOC); 
            if($data){ //check if contain some data
                if($data["logo"] == null){$logo = "";}else{$logo = $data["logo"];}
                $dataTechnology = ["id" => $data["id"], "name" => $data["name"], "logo" => $logo, "categoryId" => $data["category_id"]];
                $result[] = [new Technology($dataTechnology), $data["category_name"]]; 
                return [true, $result, 200];
            }else{
                return [false, "Erreur : Aucune catégorie existante", 404];
            }    
        }


    //     //update
    //     public function updateById($id, Category $technology){ //update category searched by id
    //         $name = $category->getName(); //get data to update category
    //         $sql = "UPDATE technology AS c SET c.name=:newName WHERE c.deleted = 0 AND c.id = :id";
    //         $sth = $this->connection->prepare($sql);
    //         $sth->bindParam(':newName', $name, PDO::PARAM_STR);
    //         $sth->bindParam(':id', $id, PDO::PARAM_INT);
    //         if($sth->execute()){
    //             return [true, 'Succès : Catégorie modifiée', 204];
    //         }else{
    //             return [false, "Erreur : Dans l'execution de la requête", 400];
    //         }
    //     }

       
    //     //delete = pass false/1 in column deleted
    //     public function deleteById($id, Category $technology){
    //         //check if it was deleted before
    //         $sql = "SELECT c.id, c.name
    //                 FROM technology AS c WHERE c.deleted = 1 AND c.id = ? LIMIT 0,1";  
    //         $sth = $this->connection->prepare($sql);
    //         $sth->bindParam(1, $id, PDO::PARAM_INT);
    //         $sth->execute();
    //         $result = $sth->fetch(PDO::FETCH_ASSOC); 

    //         if(!$result){ //technology wasn't deleted before
    //             $sql = "UPDATE technology AS c SET c.deleted = 1 WHERE id = :id ";
    //             $sth = $this->connection->prepare($sql);
    //             $sth->bindParam(':id', $id, PDO::PARAM_INT);
    //             if($sth->execute()){
    //                 return [true, 'Succès : Catégorie supprimée', 204];
    //             }else{
    //                 return [false, "Erreur : Dans l'execution de la requête", 400];
    //             }
    //         }else{
    //             return [false, "Erreur : Catégorie inexistante", 404];
    //         }
    //     }

        public function setConnection($connection){
            $this->connection = $connection ;
        }
    }

?>