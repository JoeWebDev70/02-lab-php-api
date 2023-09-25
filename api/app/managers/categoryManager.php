<?php 
//manager = DAO - Data Access Object
    require_once './Entities/CategoryModel.php';

    class CategoryManager {
        private $_connection; //PDO instance

        //constructor
        public function __construct($connection){
            $this->setConnection($connection);
        }

        public function add(Category $category){
            $sql = "INSERT INTO category(name) VALUES(:name)";
            $sth = $this->_connection->prepare($sql);
            $sth->bindParam(':name', $category->getName(), PDO::PARAM_STR);
            if($sth->execute()){
                return true;
            }else{
                return false;
            }
        }

        public function get($id){
            $id = (int) $id; //ensure is INT
            //c.id = ? LIMIT 0,1 : where condition return first ligne found 
            $sql = "SELECT c.id, c.name
                    FROM category AS c WHERE deleted = 0 AND c.id = ? LIMIT 0,1"; // 
            $sth = $this->_connection->prepare($sql);
            $sth->bindParam(1, $id, PDO::PARAM_INT);
            $sth->execute();
            $data = $sth->fetch(PDO::FETCH_ASSOC); 
            $result = new Category($data);
            return $result;
        }

        public function getList($orderBy = "id"){
            //check if $orderBy exist in column category
            if($orderBy != "id" && $orderBy != "name"){$orderBy = "id";}
            $result = [];
            $sql = "SELECT c.id, c.name
                    FROM category AS c WHERE deleted = 0 ORDER BY " . $orderBy . " ASC";
            $sth = $this->_connection->prepare($sql);
            $sth->execute();
            
            while ($data = $sth->fetch(PDO::FETCH_ASSOC)){
                $result[] = new Category($data);
            }; 
            return $result;
        }

        //update
        public function update(Category $category){
            $sql = "UPDATE category SET name = :name WHERE deleted = 0 AND id = :id";
            $sth = $this->_connection->prepare($sql);
            $sth->bindParam(':name', $category->getName(), PDO::PARAM_STR);
            $sth->bindParam(':id', $category->getId(), PDO::PARAM_INT);
            if($sth->execute()){
                return true;
            }else{
                return false;
            }
        }

        //delete = pass false/1 in column deleted
        public function delete(Category $category){
            $sql = "UPDATE category SET deleted = 1 WHERE id = :id";
            $sth = $this->_connection->prepare($sql);
            $sth->bindParam(':id', $category->getId(), PDO::PARAM_INT);
            if($sth->execute()){
                return true;
            }else{
                return false;
            }
        }

        public function setConnection($connection){
            $this->_connection = $connection ;
        }
    }

?>