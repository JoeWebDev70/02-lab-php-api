<?php

    class ResourceLogo {
    
        private $uploadDir; 
        private $internDir; 
        private $urlDir;
        private $temporaryFileName;
        private $temporaryFilePath;

        // constructor
        public function __construct(){
            $this->uploadDir = 'resources_logo/'; 
            $this->internDir = "./".$this->uploadDir;
            $this->temporaryFileName = 'temporaryLogo';
            $this->temporaryFilePath = $this->internDir.$this->temporaryFileName;
        }
       

        public function logoDataTreatment($data){ //logo file gestion
            //create a temporary file for treating data after
            $result;
            if(is_array($data)){ //file get by $_FILE
                $result = $this->formDataTreatment($data);
            }else{ // file get by binary
                if($data != ""){ //if contains data
                    $result = $this->binaryTreatment($data);
                }else{
                    $result = false;
                }
            }
            return $result;
        }
        
        public function getUploadDir(){
           return $this->uploadDir;
        }
        
        public function getInternDir(){
            return $this->internDir;
         }

        public function setUrlDir($url){
            $this->urlDir = $url."/";
        }

        private function formDataTreatment($data){ 
            $fileExt = $this->getExtension($data['type']); //get the extension file
            if($fileExt){
                $temporaryFileFullPath = $this->getTmpFullPath($fileExt); //get full path of tmp file
                $tmpName = $data['tmp_name'];
                $urlFileDir = $this->urlDir.$this->uploadDir;
                if(move_uploaded_file($tmpName, $temporaryFileFullPath)){ //store the file temporarly with always the same name
                    $result = ["extension" => $fileExt, "url_path" => $urlFileDir, "tmp_name" => $temporaryFileFullPath];
                }else{
                    $result =  false;
                }
            }else{
                $result =  false;
            }
            return $result;
        }

        private function binaryTreatment($data){
            file_put_contents($this->temporaryFilePath, $data); //store the file temporarly with always the same name
            $fileExt = $this->getExtension(mime_content_type($this->temporaryFilePath));  //get the extension file
            if($fileExt){
                $temporaryFileFullPath = $this->getTmpFullPath($fileExt); //get full path of tmp file
                $urlFileDir = $this->urlDir.$this->uploadDir;
                if(rename($this->temporaryFilePath, $temporaryFileFullPath)){ //rename it with extension
                    $result = ["extension" => $fileExt, "url_path" => $urlFileDir, "tmp_name" => $temporaryFileFullPath];
                }else{
                    $result =  false;
                }
            }else{
                $result =  false;
            }
            return $result;
        }

        private function getExtension($fileType){
            $fileExt = "";
            $extensionExplode = explode("+",$fileType);
            $fileTmpExt = explode("/",$extensionExplode[0]);
            $fileExt = strtolower($fileTmpExt[1]);

            if($fileExt != ""){
                return $fileExt;
            }else{
                return false;
            }
        }
        
        private function getTmpFullPath($fileExt){ //name and pass temporarly file => ./resources_logo/temporaryLogo.[extension]
            return $this->temporaryFilePath.".".$fileExt;
        }
    }

?>