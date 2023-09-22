<?php

    require 'connection_db.php';
        
   
    class Resource{
        static private $connection = null; //connection to db
        static private $table = "resource"; //table in db
        //properties
        public $id = null;
        public $url = null;
        static private $deleted = null;
        public $technology = null;

        public function __construct(){
            self::$connection = getConnection();
        }
    }
?>