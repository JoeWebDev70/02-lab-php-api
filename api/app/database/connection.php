<?php

    //class MySQL use design of singleton pattern 
    //to make sure there's only one instance of the class and therefore only one connection
    class Database {

        //initialisation
        private static $instance;
        private $connection;
        private string $host;
        private string $dbname;
        private string $dsn;
        private string $user;
        private string $password;

        // define a constructor in private to make it unreachable and ensure a single connection
        private function __construct(){
            //Set data
            $this->host = "mysql";
            $this->dbname = getenv("MYSQL_DATABASE");
            $this->dsn = "mysql:host=" . $this->host . ";dbname=" . $this->dbname . ";charset=utf8";
            $this->user = getenv("MYSQL_USER");
            $this->password = getenv("MYSQL_PASSWORD");
            //set connection
            $this->connectionDb();
        }   

        //destructor : to ensure connection (PDO object) is destroy when no longer required
        public function __destruct() {
            if($this->connection !== null){
                $this->connection = null; 
            }
        }
        
        //prevents instance cloning 
        public function __clone() {
            throw new Exception("Impossible de cloner une connexion SQL");
        }
        
        //prevents instance reset after deserialization (singleton pattern)
        public function __wakeUp() {
            if(self::$instance instanceof self) {
                throw new Exception("Impossible de rétablir une connexion SQL");
            }
            self::$instance = $this;
        }
        
        //allows functions of the PDO object to be called through the MySQL instance
        public function __call($method, $params) {
            if($this->connection == null) {
                $this->__construct();
            }
            
            return call_user_func_array(array($this->connection, $method), $params);
        }
        
        //return unique instance of MySQL class = accessor (or getter)
        static public function getInstance() {
            if(!(self::$instance instanceof self)) {
                self::$instance = new self();
            }
            return self::$instance;
        }  

        //getter
        public function getConnection(){
            return $this->connection;
        }

        //set connection
        private function connectionDb(){
            try{ //try to connect
                //create connection db ==> PDO object
                $this->connection = new PDO($this->dsn, $this->user, $this->password);
                //configuration of PDO : error segnalation
                $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 
                // froced transactions in UTF-8
                $this->connection->query("SET NAMES 'utf8'");
            }catch(PDOException $exception){ //catch exception and get its informations
                http_response_code(503); //503 error connection on db
                echo json_encode(["message" => "Erreur de connexion : " . $exception->getMessage()]);
            }
        }
    }
 
?>
