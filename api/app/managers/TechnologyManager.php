<?php 
//manager = DAO - Data Access Object
    require_once './Entities/TechnologyModel.php';
    require_once './Managers/CategoryManager.php';
    require_once './Managers/ResourceManager.php';

    class TechnologyManager {
        private $connection; //PDO instance
        private $category; //instance of category
        private $resource; //instance of resource

        //constructor for db connection
        public function __construct($connection){
            $this->connection = $connection;
            $this->category = new CategoryManager($this->connection);
            $this->resource = new ResourceManager($this->connection);
        }

        //add
        public function add(Technology $technology){ //add new technology
            //get data to create technology
            $name = $technology->getName(); 
            $logo = $technology->getLogo(); 
            if($logo == ""){$logo = null;} //pass null for not creating void
            $categoryId = $technology->getCategoryId();

            $result = $this->category->getBy($categoryId); //check if category exist

            if($result){ //category exist and wasn't deleted
                //check if technology exist with this category 
                $response = $this->checkIfExistWithCategory($name, $categoryId);
            
                if(!$response){ //doesn't exist yet with this category then insert in db
                    $sql = "INSERT INTO technology(name, logo, category_id) VALUES(:name, :logo, :categoryId)";
                    $sth = $this->connection->prepare($sql);
                    $sth->bindParam(':name', $name, PDO::PARAM_STR);
                    $sth->bindParam(':logo', $logo, PDO::PARAM_STR);
                    $sth->bindParam(':categoryId', $categoryId, PDO::PARAM_INT);
                    try{ 
                        $sth->execute();
                        return [true, 'Technologie créée', 201];
                    }catch(PDOException $e){ 
                        return [false, "Erreur dans l'execution de la requête", 400];
                    }
                }else if ($response["deleted"] == 1){ // exist but it was deleted before then update
                    $id = $response["id"];
                    $sql = "UPDATE technology AS t SET t.deleted = 0 WHERE t.id = :id";
                    $sth = $this->connection->prepare($sql);
                    $sth->bindParam(':id', $id, PDO::PARAM_INT);
                    try{ 
                        $sth->execute();
                        return [true, 'Technologie créée', 201];
                    }catch(PDOException $e){ 
                        return [false, "Erreur dans l'execution de la requête", 400];
                    }
                }else if($result == "erreur execution"){ //some errore in check if exist
                    return [false, "Erreur dans l'execution de la requête", 400];
                }else{ //exist in this category and wasn't deleted 
                    return [false, 'Technologie déjà existante avec cette Catégorie', 403];
                } 
            }else{
                return [$result[0], $result[1], $result[2]];
            }
        }

        //get
        public function getList($orderBy){ //display all technologies order by id or name
            $displayResult = false; //bool for check if there is something to display
            $sql = "SELECT t.id, t.name, t.logo, t.category_id, c.name AS 'category_name'
                    FROM (technology AS t 
                    LEFT JOIN category AS c ON c.id = t.category_id ) 
                    WHERE t.deleted = 0 ORDER BY t." . $orderBy . " ASC";
            $sth = $this->connection->prepare($sql);
            
            try{ 
                $sth->execute();
                while ($data = $sth->fetch(PDO::FETCH_ASSOC)){
                    if($data["logo"] == null){$logo = "";}else{$logo = $data["logo"];}
                    $resources = $this->getResourcesOfTechnology($data["id"]);
                    $dataTechnology = ["id" => $data["id"], "name" => $data["name"], "logo" => $logo, "categoryId" => $data["category_id"]];
                    $result[] = [new Technology($dataTechnology), $data["category_name"], $resources]; 
                    $displayResult = true;
                }; 

                if($displayResult){ //there is something to display
                    return [true, $result, 200];
                }else{
                    return [false, "Technologies inexistantes", 404];
                }
            }catch(PDOException $e){ //some error in sql execution
                return [false, "Erreur dans l'execution de la requête", 400];
            }
        }

        public function getBy($idOrName){ //display a technology by its Id
            $result = $this->technologyExist($idOrName); //check if technology exist
            if(!$result){ //doesn't exist
                return [false, "Technologie inexistante", 404];
            }else if($result == "erreur execution"){//some error in sql execution
                return [false, "Erreur dans l'execution de la requête", 400];
            }else{ //exist
                for($i = 0; $i < sizeof($result); $i ++){
                    if($result[$i]["deleted"] == 0){ //wasn't deleted
                        if($result[$i]["logo"] == null){$logo = "";}else{$logo = $result[$i]["logo"];}
                        $resources = $this->getResourcesOfTechnology($result[$i]["id"]);
                        $dataTechnology = ["id" => $result[$i]["id"], "name" => $result[$i]["name"], "logo" => $logo, "categoryId" => $result[$i]["category_id"]];
                        $response[] = [new Technology($dataTechnology), $result[$i]["category_name"], $resources]; 
                    }
                }
                return [true, $response, 200];
            }
        }

        //update
        public function update($id, Technology $technology){ //update technology searched by id 
            //in controller : check if technology exist and get old informations
            //get data to create technology
            $name = $technology->getName(); 
            $logo = $technology->getLogo(); 
            if($logo == ""){$logo = null;} //pass null for not creating void
            $categoryId = $technology->getCategoryId();

            $technologyExist = $this->technologyExist($id); //check if technology exist
            
            if(!$technologyExist){ //technology doesn't exist
                return [false, "Technologie inexistante", 404];
            }else if($technologyExist == "erreur execution"){//some error in sql execution
                return [false, "Erreur dans l'execution de la requête", 400];
            }else{ //technology exist
                if($technologyExist[0]["deleted"] == 0){ //exist and wasn't deleted
                    $result = $this->category->getBy($categoryId); //check if category exist 
        
                    if($result){ //category exist and wasn't deleted - CF. public function getBy($idOrName) in CategoryManager
                        //check if technology exist with this category 
                        $response = $this->checkIfExistWithCategory($name, $categoryId);
                        $update = false;

                        if(!$response){ //doesn't exist yet with this category then update in db
                            $update = true;
                        }else if($response == "erreur execution"){ //some errore in check if exist
                            return [false, "Erreur dans l'execution de la requête", 400];
                        }else{ //exist in this category and wasn't deleted 
                            if($id == $response["id"]){$update = true;} //if technology id is the same then update
                        } 

                        if($update){
                            $sql = "UPDATE technology 
                                    SET name = :name, logo = :logo, category_id = :categoryId 
                                    WHERE id = :id ";
                            $sth = $this->connection->prepare($sql);
                            $sth->bindParam(':id', $id, PDO::PARAM_INT);
                            $sth->bindParam(':name', $name, PDO::PARAM_STR);
                            $sth->bindParam(':logo', $logo, PDO::PARAM_STR);
                            $sth->bindParam(':categoryId', $categoryId, PDO::PARAM_INT);
                            try{
                                $sth->execute(); 
                                return [true, 'Technologie modifiée', 200];
                            }catch(PDOException $e){ //some error in sql execution
                                return [false, "Erreur dans l'execution de la requête", 400];
                            } 
                        }else{
                            return [false, 'Technologie déjà existante avec cette Catégorie', 403];
                        }

                    }else{ //category doesn't exist or some error in SQL execution
                        return [$result[0], $result[1], $result[2]];
                    }
                }else{ //exist but was deleted
                    return [false, "Technologie inexistante", 404];
                }
            }
        }

        //delete = update column deleted set 1
        public function delete($id){
            $result = $this->technologyExist($id);
            if(!$result){ //technology doesn't exist
                return [false, "Technologie inexistante", 404];
            }else if($result == "erreur execution"){//some error in sql execution
                return [false, "Erreur dans l'execution de la requête", 400];
            }else{ //technology exist
                if($result[0]["deleted"] == 0){ //exist and wasn't deleted
                    $sql = "UPDATE technology SET deleted = 1 WHERE id = :id ";
                    $sth = $this->connection->prepare($sql);
                    $sth->bindParam(':id', $id, PDO::PARAM_INT);
                    try{
                        $sth->execute(); 
                        return [true, 'Technologie supprimée', 200];
                    }catch(PDOException $e){ //some error in sql execution
                        return [false, "Erreur dans l'execution de la requête", 400];
                    } 
                }else{ //exist but was deleted
                    return [false, "Technologie inexistante", 404];
                }
            }
        }

        private function technologyExist($idOrName){
            $displayResult = false; //bool for check if there is something to display
            if(is_string($idOrName)){
                $sql = "SELECT t.id, t.name, t.logo, t.deleted, t.category_id, c.name AS 'category_name'
                        FROM (technology AS t 
                        LEFT JOIN category AS c ON c.id = t.category_id ) 
                        WHERE t.name = :name";  
                $sth = $this->connection->prepare($sql);
                $sth->bindParam(":name", $idOrName, PDO::PARAM_STR);
            }else{
                $sql = "SELECT t.id, t.name, t.logo, t.deleted, t.category_id, c.name AS 'category_name'
                        FROM (technology AS t 
                        LEFT JOIN category AS c ON c.id = t.category_id ) 
                        WHERE t.id = :id";  
                $sth = $this->connection->prepare($sql);
                $sth->bindParam(":id", $idOrName, PDO::PARAM_INT);
            }
            
            try{ 
                $sth->execute();
                while($data = $sth->fetch(PDO::FETCH_ASSOC)){
                    $result[] = $data;
                    $displayResult = true;  
                }
                
                //return data or false
                if($displayResult){return $result;
                }else{return false;} 

            }catch(PDOException $e){ //some error in sql execution
                return "erreur execution";
            }     
        }

        private function checkIfExistWithCategory($name, $categoryId){
            //check if technology exist with this category 
            $sql = "SELECT * FROM technology AS t 
            WHERE t.name = :name AND t.category_id = :categoryId"; 
            $sth = $this->connection->prepare($sql);
            $sth->bindParam(':name', $name, PDO::PARAM_STR);
            $sth->bindParam(':categoryId', $categoryId, PDO::PARAM_INT);
            try{ //return data 
                $sth->execute();
                return $sth->fetch(PDO::FETCH_ASSOC);
            }catch(PDOException $e){ //some error in sql execution
                return "erreur execution";
            }
        }

        //get ressources associated with technology
        private function getResourcesOfTechnology($idTechnology){
            $result = $this->resource->getListFor($idTechnology);
            if($result[0]){ //formating response
                for($i = 0; $i < sizeof($result[1]); $i++){
                    $resources[] = [
                        'id' => $result[1][$i][0]->getId(),  
                        'url' => $result[1][$i][0]->getUrl(),
                    ];
                }
                return $resources;
            }else{
                return "Pas de ressource associée";
            }
            
        }

    }

?>