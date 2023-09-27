<?php 
//manager = DAO - Data Access Object
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

            //check if exist yet
            $sql = "SELECT * FROM category AS c WHERE name=:name";
            $sth = $this->connection->prepare($sql);
            $sth->bindParam(':name', $name, PDO::PARAM_STR);
            $sth->execute();
            $result = $sth->fetch(PDO::FETCH_ASSOC);
            
            if(!$result){ //category doesn't exist yet
                $sql = "INSERT INTO category(name) VALUES(:name)";
                $sth = $this->connection->prepare($sql);
                $sth->bindParam(':name', $name, PDO::PARAM_STR);
                if($sth->execute()){ 
                    return [true, 'Succès : Catégorie créée', 201];
                }else{
                    return [false, "Erreur : Dans l'execution de la requête", 400];
                }
            }else if ($result["deleted"] == 1){ //name exist but it was deleted before then update
                $sql = "UPDATE category AS c SET c.deleted = 0 WHERE c.name=:name";
                $sth = $this->connection->prepare($sql);
                $sth->bindParam(':name', $name, PDO::PARAM_STR);
                if($sth->execute()){
                    return [true, 'Succès : Catégorie créée', 201];
                }else{
                    return [false, "Erreur : Dans l'execution de la requête", 400];
                }
            }else{
                return [false, 'Erreur : Catégorie déjà existante', 403];
            }
        }

        //get
        public function getList($orderBy){ //display all categories
            $sql = "SELECT c.id, c.name
                    FROM category AS c WHERE c.deleted = 0 ORDER BY " . $orderBy . " ASC";
            $sth = $this->connection->prepare($sql);
            $sth->execute();
            while ($data = $sth->fetch(PDO::FETCH_ASSOC)){
                $result[] = new Category($data);
            }; 
            
            if(sizeof($result) <= 0){ 
                return [false, "Erreur : Aucune catégorie existante", 404];
            }else{
                return [true, $result, 200];
            }
            
        }
//TODO : compléter après création des classes nécessaires
        // public function getLists($orderBy){ //display all categories and its technologies
        //     $sql = "SELECT c.id, c.name, GROUP_CONCAT(t.id SEPARATOR ', '), GROUP_CONCAT(t.name SEPARATOR ', ')  
        //             FROM (category AS c
        //             LEFT JOIN technology AS t on c.id = t.category_id)
        //             WHERE c.deleted = 0 AND t.deleted = 0 GROUP BY c.id ORDER BY c." . $orderBy . " ASC";
        //     $sth = $this->connection->prepare($sql);
        //     $sth->execute();

        //     while ($data = $sth->fetch(PDO::FETCH_ASSOC)){
        //         $result[] = new Category($data);
        //     }; 
            
        //     if(sizeof($result) <= 0){ 
        //         return [false, "Erreur : Aucune catégorie existante", 404];
        //     }else{
        //         return [true, $result, 200];
        //     }
        // }


        public function getById($id){
            //c.id = ? LIMIT 0,1 : where condition return first ligne found 
            $sql = "SELECT c.id, c.name
                    FROM category AS c WHERE c.deleted = 0 AND c.id = ? LIMIT 0,1";  
            $sth = $this->connection->prepare($sql);
            $sth->bindParam(1, $id, PDO::PARAM_INT);
            $sth->execute();
            $data = $sth->fetch(PDO::FETCH_ASSOC); 
            if($data){ //check if contain some data
                $result = new Category($data);
                return [true, $result, 200];
            }else{
                return [false, "Erreur : Aucune catégorie existante", 404];
            }    
        }

        public function getByName($name){
            $sql = "SELECT c.id, c.name
                    FROM category AS c WHERE c.deleted = 0 AND c.name = :name"; 
            $sth = $this->connection->prepare($sql);
            $sth->bindParam(':name', $name, PDO::PARAM_STR);
            $sth->execute();
            $data = $sth->fetch(PDO::FETCH_ASSOC); 
            if($data){ //check if contain some data
                $result = new Category($data);
                return [true, $result, 200];
            }else{
                return [false, "Erreur : Aucune catégorie existante", 404];
            }
        }


        //update
        public function updateById($id, Category $category){ //update category searched by id
            $name = $category->getName(); //get data to update category
            $sql = "UPDATE category AS c SET c.name=:newName WHERE c.deleted = 0 AND c.id = :id";
            $sth = $this->connection->prepare($sql);
            $sth->bindParam(':newName', $name, PDO::PARAM_STR);
            $sth->bindParam(':id', $id, PDO::PARAM_INT);
            if($sth->execute()){
                return [true, 'Succès : Catégorie modifiée', 204];
            }else{
                return [false, "Erreur : Dans l'execution de la requête", 400];
            }
        }

        public function updateByName($name, Category $category){ //update category searched by name
            $newName = $category->getName(); //get data to update category
            $sql = "UPDATE category AS c SET c.name=:newName WHERE c.deleted = 0 AND c.name= :name";
            $sth = $this->connection->prepare($sql);
            $sth->bindParam(':newName', $newName, PDO::PARAM_STR);
            $sth->bindParam(':name', $name, PDO::PARAM_STR);
            if($sth->execute()){
                return [true, 'Succès : Catégorie modifiée', 204];
            }else{
                return [false, "Erreur : Dans l'execution de la requête", 400];
            }
        }

        //TODO : add select if contains technology -->> change first
        //delete = pass false/1 in column deleted
        public function deleteById($id, Category $category){
            //check if it was deleted before
            $sql = "SELECT c.id, c.name
                    FROM category AS c WHERE c.deleted = 1 AND c.id = ? LIMIT 0,1";  
            $sth = $this->connection->prepare($sql);
            $sth->bindParam(1, $id, PDO::PARAM_INT);
            $sth->execute();
            $result = $sth->fetch(PDO::FETCH_ASSOC); 

            if(!$result){ //category wasn't deleted before
                $sql = "UPDATE category AS c SET c.deleted = 1 WHERE id = :id ";
                $sth = $this->connection->prepare($sql);
                $sth->bindParam(':id', $id, PDO::PARAM_INT);
                if($sth->execute()){
                    return [true, 'Succès : Catégorie supprimée', 204];
                }else{
                    return [false, "Erreur : Dans l'execution de la requête", 400];
                }
            }else{
                return [false, "Erreur : Catégorie inexistante", 404];
            }
        }
 //TODO : add select if contains technology -->> change first
        public function deleteByName($name, Category $category){
            //check if it was deleted before
            $sql = "SELECT c.id, c.name
                    FROM category AS c WHERE c.deleted = 1 AND c.name=:name";  
            $sth = $this->connection->prepare($sql);
            $sth->bindParam(':name', $name, PDO::PARAM_STR);
            $sth->execute();
            $result = $sth->fetch(PDO::FETCH_ASSOC); 

            if(!$result){ //category wasn't deleted before
                $sql = "UPDATE category AS c SET c.deleted = 1 WHERE c.name=:name";
                $sth = $this->connection->prepare($sql);
                $sth->bindParam(':name', $name, PDO::PARAM_STR);
                if($sth->execute()){
                    return [true, 'Succès : Catégorie supprimée', 204];
                }else{
                    return [false, "Erreur : Dans l'execution de la requête", 400];
                }
            }else{
                return [false, "Erreur : Catégorie inexistante", 404];
            }
        }

        public function setConnection($connection){
            $this->connection = $connection ;
        }
    }

?>