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
    //TODO enlever la partie recup vieiles infos déjà faites ds controller
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
                    return [true, 'Succès : Technologie créée', 201];
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

       //TODO check deleted .... better 

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
            // $data = $sth->fetch(PDO::FETCH_ASSOC); 
//TODO check on data
            while($data = $sth->fetch(PDO::FETCH_ASSOC)){ //check if contain some data
                if($data["logo"] == null){$logo = "";}else{$logo = $data["logo"];}
                $dataTechnology = ["id" => $data["id"], "name" => $data["name"], "logo" => $logo, "categoryId" => $data["category_id"]];
                $response[] = [new Technology($dataTechnology), $data["category_name"]]; 
            }
            if(sizeof($response) >= 0){
                return [true, $response, 200];
            }else{
                return [false, "Erreur : Aucune catégorie existante", 404];
            }    
        }

        //TODO revoir codes + message erreurs pour cette classe
        //update
        public function update($id, Technology $technology){ //update category searched by id
           //check if technology exist 
           $sql = "SELECT * FROM technology AS t 
                   WHERE t.id = :id"; 
           $sth = $this->connection->prepare($sql);
           $sth->bindParam(':id', $id, PDO::PARAM_INT);
           $sth->execute();
           $result = $sth->fetch(PDO::FETCH_ASSOC); 
           if($result){ //doesn't exist yet with this category then insert in db
                if ($result["deleted"] == 0){ // exist but it was deleted before then update
                    //get data to update technology if change or old values
                    if($technology->getName() != ""){ $newName = $technology->getName();
                    }else{ $newName = $result["name"]; }
                    
                    if($technology->getLogo() != ""){ $newLogo = $technology->getLogo();
                    }else{ $newLogo = $result["logo"]; }
                    
                    if($technology->getCategoryId() != ""){ $newCategoryId = $technology->getCategoryId();
                    }else{ $newnewCategoryIdName = $result["category_id"]; }
                    
                    $sql = "UPDATE technology 
                            SET name = :newName, logo = :newLogo, category_id = :newCategoryId 
                            WHERE id = :id ";
                    $sth = $this->connection->prepare($sql);
                    $sth->bindParam(':id', $id, PDO::PARAM_INT);
                    $sth->bindParam(':newName', $newName, PDO::PARAM_STR);
                    $sth->bindParam(':newLogo', $newLogo, PDO::PARAM_STR);
                    $sth->bindParam(':newCategoryId', $newCategoryId, PDO::PARAM_INT);
                    if($sth->execute()){ 
                        return [true, 'Succès : Technology modifiée', 201];
                    }else{
                        return [false, "Erreur : Dans l'execution de la requête", 400];
                    }
                }else{
                    return [false, 'Erreur : Technologie inexistante', 403];
                }
           }else{
               return [false, 'Erreur : Technology déjà existante avec cette Catégorie', 403];
           } 
        }
        
        public function getCategory($id){ //TODO completer
            $sql = "SELECT category_id FROM technology WHERE id=:id";
            $sth = $this->connection->prepare($sql);
            $sth->bindParam(':id', $id, PDO::PARAM_INT);
            $sth->execute();
            $result = $sth->fetch(PDO::FETCH_ASSOC);
            if(isset($result["category_id"])){
                return $result["category_id"];
            }
            return null;
        }

        private function technologyExist($idOrName){//check if technology exist and return its data
             if(is_int($idOrName)){ //by id
                $sql = "SELECT * FROM technology WHERE id=:id";
                $sth = $this->connection->prepare($sql);
                $sth->bindParam(':id', $idOrName, PDO::PARAM_INT);
            }else{ //by name
                $sql = "SELECT * FROM technology WHERE name=:name";
                $sth = $this->connection->prepare($sql);
                $sth->bindParam(':name', $idOrName, PDO::PARAM_STR);
            }

            try{
                $sth->execute();
                return $sth->fetch(PDO::FETCH_ASSOC);
            }catch(PDOException $e){
                return "erreur execution";
            }
            
        }

        public function setConnection($connection){
            $this->connection = $connection ;
        }
    }

?>