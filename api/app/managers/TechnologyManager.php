<?php 
//manager = DAO - Data Access Object
    require_once './Entities/TechnologyModel.php';
    require_once './Managers/CategoryManager.php';

    class TechnologyManager {
        private $connection; //PDO instance
        private $category; //instance of category

        //constructor for db connection
        public function __construct($connection){
            $this->setConnection($connection);
            $this->category = new CategoryManager($connection);
        }

        //add
        public function add(Technology $technology){ //add new technology
            //get data to create technology
            $name = $technology->getName(); 
            $logo = $technology->getLogo(); 
            if($logo == ""){$logo = null;} //pass null for not creating void
            $categoryId = $technology->getCategoryId();

            //check if category exist
            $result = $this->category->getBy($categoryId);

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
                        return [true, 'Succès : Technology créée', 201];
                    }catch(PDOException $e){ 
                        return [false, "Erreur : Dans l'execution de la requête", 400];
                    }
                }else if ($response["deleted"] == 1){ // exist but it was deleted before then update
                    $sql = "UPDATE technology AS t SET t.deleted = 0 
                            WHERE t.name = :name AND t.category_id = :categoryId";
                    $sth = $this->connection->prepare($sql);
                    $sth->bindParam(':name', $name, PDO::PARAM_STR);
                    $sth->bindParam(':categoryId', $categoryId, PDO::PARAM_INT);
                    try{ 
                        $sth->execute();
                        return [true, 'Succès : Technology créée', 201];
                    }catch(PDOException $e){ 
                        return [false, "Erreur : Dans l'execution de la requête", 400];
                    }
                }else if($result == "erreur execution"){ //some errore in check if exist
                    return [false, "Erreur : Dans l'execution de la requête", 400];
                }else{ //exist in this category and wasn't deleted 
                    return [false, 'Erreur : Technology déjà existante avec cette Catégorie', 403];
                } 
            }else if($result[2] == 400){
                return [false, "Erreur : Dans l'execution de la requête", 400];
            }else{ //category does't exist
                return [false, "Erreur : Aucune catégorie existante", 404];
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
                    $dataTechnology = ["id" => $data["id"], "name" => $data["name"], "logo" => $logo, "categoryId" => $data["category_id"]];
                    $result[] = [new Technology($dataTechnology), $data["category_name"]]; 
                    $displayResult = true;
                }; 

                if($displayResult){ 
                    return [false, "Erreur : Aucune technology existante", 404];
                }else{
                    return [true, $result, 200];
                }
            }catch(PDOException $e){ //some error in sql execution
                return [false, "Erreur : Dans l'execution de la requête", 400];
            }
        }

        public function getById($id){ //display a technology by its Id
            //c.id = ? LIMIT 0,1 : where condition return first ligne found 
            $sql = "SELECT t.id, t.name, t.logo, t.category_id, c.name AS 'category_name'
                    FROM (technology AS t 
                    LEFT JOIN category AS c ON c.id = t.category_id ) 
                    WHERE t.deleted = 0 AND t.id = ? LIMIT 0,1";  
            $sth = $this->connection->prepare($sql);
            $sth->bindParam(1, $id, PDO::PARAM_INT);
            try{ 
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
            }catch(PDOException $e){ //some error in sql execution
                return [false, "Erreur : Dans l'execution de la requête", 400];
            }  
        }

        public function getByName($name){ //display technologies which have the same name
            $displayResult = false; //bool for check if there is something to display
            $sql = "SELECT t.id, t.name, t.logo, t.category_id, c.name AS 'category_name'
                    FROM (technology AS t 
                    LEFT JOIN category AS c ON c.id = t.category_id ) 
                    WHERE t.deleted = 0 AND t.name = :name";  
            $sth = $this->connection->prepare($sql);
            $sth->bindParam(':name', $name, PDO::PARAM_STR);
            $sth->execute();
            try{ 
                while($data = $sth->fetch(PDO::FETCH_ASSOC)){ //check if contain some data
                    if($data["logo"] == null){$logo = "";}else{$logo = $data["logo"];}
                    $dataTechnology = ["id" => $data["id"], "name" => $data["name"], "logo" => $logo, "categoryId" => $data["category_id"]];
                    $response[] = [new Technology($dataTechnology), $data["category_name"]];
                    $displayResult = true;  
                }
                if($displayResult){
                    return [true, $response, 200];
                }else{
                    return [false, "Erreur : Aucune catégorie existante", 404];
                } 
            }catch(PDOException $e){ //some error in sql execution
                return [false, "Erreur : Dans l'execution de la requête", 400];
            }     
        }

        //update
        public function update($id, Technology $technology){ //update category searched by id
            //check if technology exist did in controller and get old informations
            //get data to create technology
            $name = $technology->getName(); 
            $logo = $technology->getLogo(); 
            
            if($logo == ""){$logo = null;} //pass null for not creating void
            $categoryId = $technology->getCategoryId();

            //check if category exist
            $result = $this->category->getBy($categoryId);
   
            if($result){ //category exist and wasn't deleted
                //check if technology exist with this category 
                $response = $this->checkIfExistWithCategory($name, $categoryId);
            
                if(!$response){ //doesn't exist yet with this category then update in db
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
                        return [true, 'Succès : Technology modifiée', 200];
                    }catch(PDOException $e){ //some error in sql execution
                        return [false, "Erreur : Dans l'execution de la requête", 400];
                    } 
                }else if($result == "erreur execution"){ //some errore in check if exist
                    return [false, "Erreur : Dans l'execution de la requête", 400];
                }else{ //exist in this category and wasn't deleted 
                    return [false, 'Erreur : Technology déjà existante avec cette Catégorie', 403];
                } 
            }else if($result[2] == 400){
                return [false, "Erreur : Dans l'execution de la requête", 400];
            }else{ //category does't exist
                return [false, "Erreur : Aucune catégorie existante", 404];
            }
        }

        private function checkIfExistWithCategory($name, $categoryId){
            //check if technology exist with this category 
            $sql = "SELECT * FROM technology AS t 
            WHERE t.name = :name AND t.category_id = :categoryId"; 
            $sth = $this->connection->prepare($sql);
            $sth->bindParam(':name', $name, PDO::PARAM_STR);
            $sth->bindParam(':categoryId', $categoryId, PDO::PARAM_INT);
            try{ //return data for this category
                $sth->execute();
                return $sth->fetch(PDO::FETCH_ASSOC);
            }catch(PDOException $e){ //some error in sql execution
                return "erreur execution";
            }
        }


        public function setConnection($connection){
            $this->connection = $connection ;
        }
    }

?>