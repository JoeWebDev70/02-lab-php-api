<?php

    //return the name of message to display
    function getReplyName($reply){ 
        if(isset($reply["message"])){
            return "message";
        }else if(isset($reply["Categories"])){
            return "Categories";
        }else if(isset($reply["Technologies par Catégorie"])){
            return "Technologies par Catégorie";
        }else if(isset($reply["Technologies"])){
            return "Technologies";
        }else if(isset($reply["Ressources"])){
            return "Ressources";
        }
    }

?>