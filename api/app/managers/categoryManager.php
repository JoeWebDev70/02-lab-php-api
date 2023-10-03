<?php 
    //manager = DAO - Data Access Object => controlled by Category Controller
    require_once './Entities/CategoryModel.php';

    class CategoryManager {
        private $connection; //PDO instance

        //constructor for db connection
        public function __construct($connection){
            $this->setConnection($connection);
        }

        //add
        public function add(Category $category){ //add new category
            $name = $category->getName(); //get data to create category
            $result = $this->categoryExist($name); //check if exist yet 

            if(!$result){ //category doesn't exist yet then create it
                $sql = "INSERT INTO category(name) VALUES(:name)";
                $sth = $this->connection->prepare($sql);
                $sth->bindParam(':name', $name, PDO::PARAM_STR);
                try{ //create
                    $sth->execute();
                    return [true, 'Succès : Catégorie créée', 201];
                }catch(PDOException $e){
                    return [false, "Erreur : ".$e->getMessage(), 400];
                }
            }else if ($result["deleted"] == 1){ //name exist but it was deleted before then update it
                $id = $result["id"];
                $sql = "UPDATE category AS c SET c.deleted = 0 WHERE c.id=:id";
                $sth = $this->connection->prepare($sql);
                $sth->bindParam(':id', $id, PDO::PARAM_STR);
                try{ //update
                    $sth->execute();
                    return [true, 'Succès : Catégorie créée', 201];
                }catch(PDOException $e){
                    return [false, "Erreur : Dans l'execution de la requête", 400];
                }
            }else if($result == "erreur execution"){ //some errore in check if exist
                return [false, "Erreur : Dans l'execution de la requête", 400];
            }else{ //name exist and wasn't deleted 
                return [false, 'Erreur : Catégorie déjà existante', 403];
            }
        }

        //get
        public function getList($orderBy){ //display all categories name without the technologies associated
            $displayResult = false; //bool for check if there is something to display
            $sql = "SELECT c.id, c.name
                    FROM category AS c WHERE c.deleted = 0 ORDER BY " . $orderBy . " ASC";
            $sth = $this->connection->prepare($sql);
            try{ 
                $sth->execute();
                while ($result = $sth->fetch(PDO::FETCH_ASSOC)){
                    if($result["id"] != null){ 
                        $response[] = new Category($result);
                        $displayResult = true; 
                    }
                }; 
                
                if($displayResult){  //exist some category then display result
                    return [true, $response, 200];
                }else{ //no category exist
                    return [false, "Erreur : Aucune catégorie existante", 404];
                }
            }catch(PDOException $e){  //some error in the sql execution
                return [false, "Erreur : Dans l'execution de la requête", 400];
            }
        }
        
        public function getLists($orderBy){ //display Categories which have some technologies associated and their name
            $displayResult = false; //bool for check if there is something to display
            $sql = "SELECT c.id AS c_id, c.name AS c_name, GROUP_CONCAT(t.id SEPARATOR ', ') AS t_id, GROUP_CONCAT(t.name SEPARATOR ', ') AS t_name  
                    FROM (category AS c
                    LEFT JOIN technology AS t on c.id = t.category_id)
                    WHERE c.deleted = 0 AND t.deleted = 0 GROUP BY c.id ORDER BY c." . $orderBy . " ASC";
            $sth = $this->connection->prepare($sql);
            try{
                $sth->execute();
                while ($result = $sth->fetch(PDO::FETCH_ASSOC)){
                    if($result["c_id"] != null){
                        $dataCategory = ["id" => $result["c_id"], "name" => $result["c_name"]];
                        $response[] = [new Category($dataCategory), $result["t_id"], $result["t_name"]];
                        $displayResult = true;
                    } 
                }; 
                if($displayResult){ //some categories had technology
                    return [true, $response, 200];
                }else{
                    return [false, "Erreur : Aucune catégorie n'est associée à une technologie", 404];
                }
            }catch(PDOException $e){ //some error in SQL execution
                return [false, "Erreur : Dans l'execution de la requête", 400];
            }
        }

        public function getBy($idOrName){ //display one category by its id or name
            $result = $this->categoryExist($idOrName); //check if exist 
            if($result){ //category exist 
                if($result["deleted"] == 0){ //name exist and wasn't deleted 
                    $response = new Category($result);
                    return [true, $response, 200];
                }else{  //exist but was deleted
                    return [false, "Erreur : Aucune catégorie existante", 404];
                }
            }else if($result == "erreur execution"){ //some error in chek if exist
                return [false, "Erreur : Dans l'execution de la requête", 400];
            }else{ //doesn't exist
                return [false, "Erreur : Aucune catégorie existante", 404];
            }
        }

        public function getListBy($idOrname){ //display one category by its id or name if have some technologies
            $result = $this->categoryExist($idOrname); //check if exist 
            
            if($result){ //category exist 
                if ($result["deleted"] == 0){ //name exist wasn't deleted 
                    $id = $result["id"];
                    $response = $this->getTechnologies($id);
                    if($response){ //category contain some technologies
                        return [true, $response[1], 200];
                    }else if($response == "erreur d'execution"){
                        return [false, "Erreur : Dans l'execution de la requête", 400];
                    }else{ //category doesn't contain technology
                        return [false, "La catégorie ".$result["id"] ." : ". $result["name"] ." ne contient pas de technologie", 404];
                    }
                }else{ //exist but was deleted
                    return [false, "Erreur : Aucune catégorie existante", 404];
                }
            }else if($result == "erreur execution"){ //some errore on check if category exist
                return [false, "Erreur : Dans l'execution de la requête", 400];
            }else{ //doesn't exist
                return [false, "Erreur : Aucune catégorie existante", 404];
            }
        }

        //update
        public function updateBy($arg, Category $category){ //update category searched by id or name
            $newName = $category->getName(); //get data to update category    
            $result = $this->categoryExist($arg); //check if exist

            if($result){ //category exist in db
                if ($result["deleted"] == 0){ //name exist wasn't deleted 
                    $id = $result["id"];
                    $sql = "UPDATE category AS c SET c.name=:newName WHERE c.id = :id";
                    $sth = $this->connection->prepare($sql);
                    $sth->bindParam(':newName', $newName, PDO::PARAM_STR);
                    $sth->bindParam(':id', $id, PDO::PARAM_INT);
                    try{
                        $sth->execute();
                        return [true, 'Succès : Catégorie modifiée', 200];
                    }catch(PDOException $e){ //some error in sql execution
                        return [false, "Erreur : Dans l'execution de la requête", 400];
                    }
                }else{ //exist but was deleted
                    return [false, "Erreur : Aucune catégorie existante", 404];
                }
            }else if($result == "erreur execution"){ //some errore on check if category exist
                return [false, "Erreur : Dans l'execution de la requête", 400];
            }else{ //doesn't exist
                return [false, "Erreur : Aucune catégorie existante", 404];
            }
        }
       
        //delete = pass false/1 in column deleted
        public function deleteBy($arg, Category $category){ //delete category by id or name
            $result = $this->categoryExist($arg); //check if exist
            
            if($result){ //category exist in db
                if ($result["deleted"] == 0){ //name exist wasn't deleted 
                    $id = $result["id"];
                    $response = $this->getTechnologies($id);
                    if($response){
                        return [false, $response[1], 403];
                    }else if($response == "erreur d'execution"){
                        return [false, "Erreur : Dans l'execution de la requête", 400];
                    }else{//doesn't contain technologies then update
                        $sql = "UPDATE category AS c SET c.deleted = 1 WHERE c.id = :id";
                        $sth = $this->connection->prepare($sql);
                        $sth->bindParam(':id', $id, PDO::PARAM_INT);
                        try{
                            $sth->execute();
                            return [true, "Succès : Catégorie supprimée", 200];
                        }catch(PDOException $e){
                            return [false, "Erreur : Dans l'execution de la requête", 400];
                        }
                    }
                }else{ //exist but was deleted
                    return [false, "Erreur : Aucune catégorie existante", 404];
                }
            }else if($result == "erreur execution"){ //some errore on check if category exist
                return [false, "Erreur : Dans l'execution de la requête", 400];
            }else{ //doesn't exist
                return [false, "Erreur : Aucune catégorie existante", 404];
            }
        }

        private function categoryExist($idOrName){ //check if category exist and return its data
            if(is_int($idOrName)){ //by id
                $sql = "SELECT * FROM category WHERE id=:id";
                $sth = $this->connection->prepare($sql);
                $sth->bindParam(':id', $idOrName, PDO::PARAM_INT);
            }else{ //by name
                $sql = "SELECT * FROM category WHERE name=:name";
                $sth = $this->connection->prepare($sql);
                $sth->bindParam(':name', $idOrName, PDO::PARAM_STR);
            }

            try{ //return data for this category
                $sth->execute();
                return $sth->fetch(PDO::FETCH_ASSOC);
            }catch(PDOException $e){ //some error in sql execution
                return "erreur execution";
            }
            
        }

        private function getTechnologies($id){
            $displayResult = false; // check if there is some informations to display
            $sql = "SELECT c.id AS c_id, c.name AS c_name, GROUP_CONCAT(t.id SEPARATOR ', ') AS t_id, GROUP_CONCAT(t.name SEPARATOR ', ') AS t_name  
                    FROM (category AS c
                    LEFT JOIN technology AS t on c.id = t.category_id)
                    WHERE t.deleted = 0 AND c.deleted = 0 AND c.id = :id";
            $sth = $this->connection->prepare($sql);
            $sth->bindParam(':id', $id, PDO::PARAM_INT);
            try{ 
                $sth->execute();
                while ($result = $sth->fetch(PDO::FETCH_ASSOC)){
                    if($result["c_id"] != null){
                        $dataCategory = ["id" => $result["c_id"], "name" => $result["c_name"]];
                        $response[] = [new Category($dataCategory), $result["t_id"], $result["t_name"]];
                        $displayResult = true;
                    } 
                }; 
                if($displayResult){
                    return [$displayResult, $response];
                }else{
                    return $displayResult;
                }  
            }catch(PDOException $e){ //some error in sql execution
                return "erreur execution";
            }
        }

        public function setConnection($connection){ //db connection
            $this->connection = $connection ;
        }
    }

?>