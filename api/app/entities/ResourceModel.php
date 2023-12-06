<?php
    //entities = models => managed by ResourceManager

    class Resource{
        //attributes         
        private int $id;
        private string $url;
        private bool $deleted;    
        private int $technologyId;

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

        public function setUrl($url){
            if(is_string($url)){ //ensure is string
                $this->url = $url;
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
                $this->deleted = $deleted;
            }
        }
        
        public function setTechnologyId($technologyId){
            $technologyId = (int) $technologyId; //ensure is INT
            if($technologyId > 0){ //ensure is stricly positive number
                $this->technologyId = $technologyId;
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

        //getters
        public function getId(){
            return $this->id;
        }
        
        public function getUrl(){
            return $this->url;
        }

        public function getDeleted(){
            return $this->deleted;
        }
        
        public function getTechnologyId(){
            return $this->technologyId;
        }
    }
   
?>