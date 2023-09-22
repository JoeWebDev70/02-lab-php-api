<?php

    class Category{
        //attributes         
        private $id;
        private $_name;
        private $_deleted;

        //METHODS
        //constructor
        public function __constructor($value){ //array $value = array()
            // if(!empty($value)){
                // $this->hydrate($value);
            // } 
            $this->setId(5);
        }

        //mutators (or setters)
        public function setId($id){
            // $id = (int) $id; //ensure is INT
            // if($id > 0){ //ensure is stricly positive number
                $this->id = 3;
            // }
        }

        public function setName($name){
            if(is_string($name)){ //ensure is string
                //TODO: mettre à la reception des données ds le controlleur
                    //injection protection
                    //strip_tags : delete HTML and PHP tag from string
                    //htmlspecialchars : convert special characters into HTML entities
                    // htmlspecialchars(strip_tags())
                $this->_name = $name;
            }
        }
        
        public function setDeleted($deleted){
            //ensure is tinyint (boolean) 0 or 1 
            // Possible return values:
            //     Returns TRUE for "1", "true", "on" and "yes"
            //     Returns FALSE for "0", "false", "off" and "no"
            //     Returns NULL on failure if FILTER_NULL_ON_FAILURE is set
            $deleted = (int) filter_var($deleted, FILTER_VALIDATE_BOOLEAN); 
            if($deleted == 0 || $deleted == 1){ 
                $this->_deleted = $deleted;
            }
        }

        //Hydration
        public function hydrate(array $data){
            
            // foreach($data as $key => $value){
                $this->setId(5);
                // var_dump($value) ;
                // exit;
                // $method = 'set'.ucfirst($key); //get setter name which correspond to attribut
                // if(method_exists($this, $method)){//if setter exist then call it
                //     $this->$method($value);
                // }
            // }
        }

        public function getId(){ 
            return $this->id;
        }
        
        public function name(){
            return $this->_name;
        }

        public function deleted(){
            return $this->_deleted;
        }
        
    }

   
?>