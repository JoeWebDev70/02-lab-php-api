<?php
//entities = models

    class Technology{
        //attributes         
        private int $id;
        private string $name;
        private string $logo;
        private bool $deleted; 
        private int $categoryId;

        //METHODS
        //constructor
        public function __construct(array $value = array()){
            if(!empty($value)){
                $this->hydrate($value);
            } 
        }

        //mutators (or setters)
        public function setId($id){
            $id = (int) $id; //ensure is INT
            if($id > 0){ //ensure is stricly positive number
                $this->id = $id;
            }
        }

        public function setName($name){
            if(is_string($name)){ //ensure is string
                $this->name = $name;
            }
        }

        public function setLogo($logo){
            if(is_string($logo)){ //ensure is string
                $this->logo = $logo;
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
        
        public function setCategoryId($categoryId){
            $categoryId = (int) $categoryId; //ensure is INT
            if($categoryId > 0){ //ensure is stricly positive number
                $this->categoryId = $categoryId;
            }
        }
        //Hydration
        public function hydrate(array $data){
            foreach($data as $key => $value){
                $method = 'set'.ucfirst($key); //get setter name which correspond to attribut
                if(method_exists($this, $method)){//if setter exist then call it
                    $this->$method($value);
                }
            }
        }

        public function getId(){
            return $this->id;
        }
        
        public function getName(){
            return $this->name;
        }
        
        public function getLogo(){
            return $this->logo;
        }

        public function getDeleted(){
            return $this->deleted;
        }
        
        public function getCategoryId(){
            return $this->categoryId;
        }
    }
   
?>