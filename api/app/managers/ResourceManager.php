<?php 
    //manager = DAO - Data Access Object => controlled by ResourceController
    require_once './Entities/ResourceModel.php';
    require_once './Managers/TechnologyManager.php';

    class ResourceManager {
        private $connection; //PDO instance
        private $technology; //instance of technology

        //constructor for db connection
        public function __construct($connection){
            $this->setConnection($connection);
            $this->technology = new TechnologyManager($connection);
        }

        //add
        public function add(Resource $resource){ //add new resource
            $url = $resource->getUrl(); //get data to create resource
            $technologyId = $resource->getTechnologyId();

            $technologyExist = $this->technology->getBy($technologyId);//check if technology exist

            if($technologyExist[0]){ //technology exist and wasn't deleted - CF. public function getBy($idOrName) in TechnologyManager
                // check if exist yet in this technology
                $result = $this->checkIfExistWithTechnology($url, $technologyId); 

                if(!$result){ //doesn't exist yet then create
                    $sql = "INSERT INTO resource(url, technology_id) VALUES(:url, :technologyId)";
                    $sth = $this->connection->prepare($sql);
                    $sth->bindParam(':url', $url, PDO::PARAM_STR);
                    $sth->bindParam(':technologyId', $technologyId, PDO::PARAM_INT);
                    try{
                        $sth->execute();
                        return [true, 'Succès : Ressource créée', 201];
                    }catch(PDOException $e){
                        return [false, "Erreur : ".$e->getMessage(), 400];
                    }
                }else if($result == "erreur execution"){ //some errore in check if exist
                    return [false, "Erreur : Dans l'execution de la requête", 400];
                }else if ($result["deleted"] == 1){ //exist but it was deleted before then update 
                    $id = $result["id"];
                    $sql = "UPDATE resource AS r SET r.deleted = 0 WHERE r.id=:id";
                    $sth = $this->connection->prepare($sql);
                    $sth->bindParam(':id', $id, PDO::PARAM_INT);
                    try{ //update
                        $sth->execute();
                        return [true, 'Succès : Ressource créée', 201];
                    }catch(PDOException $e){
                        return [false, "Erreur : Dans l'execution de la requête", 400];
                    }
                }else{ // exist in this technology and wasn't delete 
                    return [false, 'Erreur : Ressource déjà existante avec cette technologie', 403];
                }

            }else{ //technology doesn't exist or some error in SQL execution
                return [$result[0], $result[1], $result[2]];
            }
        }

        //get
        public function getList(){ //display all resources 
            $displayResult = false; //bool for check if there is something to display
            $sql = "SELECT r.id, r.url, r.technology_id, t.name AS 'technology_name'
                    FROM (resource AS r 
                    INNER JOIN technology AS t ON t.id = r.technology_id) 
                    WHERE r.deleted = 0 ";
            $sth = $this->connection->prepare($sql);
            try{ 
                $sth->execute();
                while ($data = $sth->fetch(PDO::FETCH_ASSOC)){
                    $dataResource = ["id" => $data["id"], "url" => $data["url"], "technologyId" => $data["technology_id"]];
                    $result[] = [new Resource($dataResource), $data["technology_name"]]; 
                    $displayResult = true;
                }; 

                if($displayResult){ 
                    return [true, $result, 200];
                }else{
                    return [false, "Erreur : Resources inexistantes", 404];
                }
            }catch(PDOException $e){ //some error in sql execution
                return [false, "Erreur : Dans l'execution de la requête", 400];
            }
        }
        
        public function get($id){ //display one resource
            $result = $this->resourceExist($id); //check if technology exist
            if(!$result){ //doesn't exist
                return [false, "Erreur : Resource inexistante", 404];
            }else if($result == "erreur execution"){//some error in sql execution
                return [false, "Erreur : Dans l'execution de la requête", 400];
            }else{ //exist
                for($i = 0; $i < sizeof($result); $i ++){
                    if($result[$i]["deleted"] == 0){ //wasn't deleted
                        $dataResource = ["id" => $result[$i]["id"], "url" => $result[$i]["url"], "technologyId" => $result[$i]["technology_id"]];
                        $response[] = [new Resource($dataResource), $result[$i]["technology_name"]]; 
                    }
                }
                return [true, $response, 200];
            }
        }

        //update
        public function update($id, Resource $resource){ //update resource
            //in controller : check if resources exist and get old informations
            $url = $resource->getUrl(); //get data to create resource
            $technologyId = $resource->getTechnologyId();

            $result = $this->resourceExist($id); //check if exist
                
            if(!$result){ //doesn't exist 
                return [false, "Erreur : Resource inexistante", 404];
            }else if($result == "erreur execution"){ //some errore in check if exist
                return [false, "Erreur : Dans l'execution de la requête", 400];
            }else{
                if ($result[0]["deleted"] == 0){ //exist and wasn't deleted before
                    //check if technology exist
                    $technologyExist = $this->technology->getBy($technologyId);

                    if($technologyExist[0]){ //technology exist and wasn't deleted - CF. public function getBy($idOrName) in TechnologyManager

                        // check if exist yet in this technology
                        $response = $this->checkIfExistWithTechnology($url, $technologyId); 
                        $update = false;

                        if(!$response){ //doesn't exist yet with this technology then update in db
                            $update = true;
                        }else if($response == "erreur execution"){ //some errore in check if exist
                            return [false, "Erreur : Dans l'execution de la requête", 400];
                        }else{ //exist in this category and wasn't deleted 
                            if($id == $response["id"]){$update = true;} //if technology id is the same then update
                        } 

                        if($update){
                            $sql = "UPDATE resource AS r SET r.url=:url, r.technology_id=:technologyId 
                                    WHERE r.id=:id";
                            $sth = $this->connection->prepare($sql);
                            $sth->bindParam(':url', $url, PDO::PARAM_STR);
                            $sth->bindParam(':technologyId', $technologyId, PDO::PARAM_INT);
                            $sth->bindParam(':id', $id, PDO::PARAM_INT);
                            try{ 
                                $sth->execute();

                                return [true, 'Succès : Ressource modifiée', 201];
                            }catch(PDOException $e){
                                return [false, "Erreur : Dans l'execution de la requête", 400];
                            }
                        }else{
                            return [false, 'Erreur : Ressource déjà existante avec cette technologie', 403];
                        }
                    }else{ //technology doesn't exist or some error in SQL execution
                        return [$result[0], $result[1], $result[2]];
                    }  
                }else{ // exist but was deleted 
                    return [false, "Erreur : Resource inexistante", 404];
                }
            }

            
        }
       
        //delete = pass false/1 in column deleted
        public function delete($id){ //delete resource by id
            $result = $this->resourceExist($id);
            if(!$result){ //resource doesn't exist
                return [false, "Erreur : Resource inexistante", 404];
            }else if($result == "erreur execution"){//some error in sql execution
                return [false, "Erreur : Dans l'execution de la requête", 400];
            }else{ //resource exist
                if($result[0]["deleted"] == 0){ //exist and wasn't deleted
                    $sql = "UPDATE resource SET deleted = 1 WHERE id = :id ";
                    $sth = $this->connection->prepare($sql);
                    $sth->bindParam(':id', $id, PDO::PARAM_INT);
                    try{
                        $sth->execute(); 
                        return [true, 'Succès : resource supprimée', 200];
                    }catch(PDOException $e){ //some error in sql execution
                        return [false, "Erreur : Dans l'execution de la requête", 400];
                    } 
                }else{ //exist but was deleted
                    return [false, "Erreur : Resource inexistante", 404];
                }
            }
        }

        private function resourceExist($idOrUrl){ //check if category exist and return its data
            $displayResult = false; //bool for check if there is something to display
            if(is_string($idOrUrl)){
                $sql = "SELECT r.id, r.url, r.technology_id, r.deleted, t.name AS 'technology_name'
                        FROM (resource AS r 
                        INNER JOIN technology AS t ON t.id = r.technology_id) 
                        WHERE r.url=:url";
                $sth = $this->connection->prepare($sql);
                $sth->bindParam(':url', $idOrUrl, PDO::PARAM_STR);
            }else{
                $sql = "SELECT r.id, r.url, r.technology_id, r.deleted, t.name AS 'technology_name'
                        FROM (resource AS r 
                        INNER JOIN technology AS t ON t.id = r.technology_id) 
                        WHERE r.id=:id";
                $sth = $this->connection->prepare($sql);
                $sth->bindParam(':id', $idOrUrl, PDO::PARAM_INT);
            }

            try{ //return data for this category
                $sth->execute();
                while($data = $sth->fetch(PDO::FETCH_ASSOC)){
                    $result[] = $data;
                    $displayResult = true;  
                }
                
                if($displayResult){return $result;
                }else{return false;} 

            }catch(PDOException $e){ //some error in sql execution
                return "erreur execution";
            }
        }

        private function checkIfExistWithTechnology($url, $technologyId){
            //check if resource exist with this technology 
            $sql = "SELECT * FROM resource AS r 
                    WHERE r.url = :url AND r.technology_id = :technologyId"; 
            $sth = $this->connection->prepare($sql);
            $sth->bindParam(':url', $url, PDO::PARAM_STR);
            $sth->bindParam(':technologyId', $technologyId, PDO::PARAM_INT);
            try{ //return data 
                $sth->execute();
                return $sth->fetch(PDO::FETCH_ASSOC);
            }catch(PDOException $e){ //some error in sql execution
                return "erreur execution";
            }
        }

        public function setConnection($connection){ //db connection
            $this->connection = $connection ;
        }
    }

?>