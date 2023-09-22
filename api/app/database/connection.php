<?php
    //class MySQL use design of singleton pattern 
    //to make sure there's only one instance of the class and therefore only one connection
    class MySQL {

        //initialisation
        private static $_instance;
        private $_connection;
        private string $_host;
        private string $_dbname;
        private string $_dsn;
        private string $_user;
        private string $_password;

        // define a constructor in private to make it unreachable and ensure a single connection
        private function __construct(){
            //Set data
            $this->_host = "mysql";
            $this->_dbname = getenv("MYSQL_DATABASE");
            $this->_dsn = "mysql:host=" . $this->_host . ";dbname=" . $this->_dbname . ";charset=utf8";
            $this->_user = getenv("MYSQL_USER");
            $this->_password = getenv("MYSQL_PASSWORD");
            
            $this->connectionDb();
        }   

        //destructor : to ensure connection (PDO object) is destroy when no longer required
        public function __destruct() {
            if($this->_connection !== null){
                $this->_connection = null; 
            }
        }
        
        //prevents instance cloning 
        public function __clone() {
            throw new Exception("Impossible de cloner une connexion SQL");
        }
        
        //prevents instance reset after deserialization (singleton pattern)
        public function __wakeUp() {
            if(self::$_instance instanceof self) {
                throw new Exception("Impossible de rétablir une connexion SQL");
            }
            self::$instance = $this;
        }
        
        //allows functions of the PDO object to be called through the MySQL instance
        public function __call($method, $params) {
            if($this->_connection == null) {
                $this->__construct();
            }
            
            return call_user_func_array(array($this->_connection, $method), $params);
        }
        
        //return unique instance of MySQL class = accessor (or getter)
        static public function getInstance() {
            if(!(self::$_instance instanceof self)) {
                self::$_instance = new self();
            }
            return self::$_instance;
        }  

        public function getConnection(){
            return $this->_connection;
        }


        private function connectionDb(){
            try{ //try to connect
                //create connection db ==> PDO object
                $this->_connection = new PDO($this->_dsn, $this->_user, $this->_password);
                //configuration of PDO : error segnalation
                $this->_connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 
                // froced transactions in UTF-8
                $this->_connection->query("SET NAMES 'utf8'");
            }catch(PDOException $exception){ //catch exception and get its informations
                http_response_code(503); //503 error connection on db
                echo json_encode(["message" => "Erreur de connexion : " . $exception->getMessage()]);
            }
        }
    }

    // //external function to get connection or error
    // function startConnection(){
    //     $db = MySQL::getInstance();
    //     $conn = $db->getConnection();
    //     $exception = null;
    //     try{ //try to connect
    //         $conn =  // get unique instance of MySQL
    //         return $conn;
    //     }catch(PDOException $exception){ //catch exception and get its informations
    //         http_response_code(503); //503 error connection on db
    //         echo json_encode(["message" => "Erreur de connexion : " . $exception->getMessage()]);
    //     }
    // }
 
?>
