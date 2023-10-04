# 02-lab-php-api

## Effectuer les requêtes

### Infos Diverses:

    - Les catégories sont des containers, elles sont uniques et ne peuvent être supprimées si elles sont associées à des technologies;

    - Les technologies sont unique par catégorie;

    - Les requêtes se font dans Params;

    - Les fichiers images = logos:
        - methode POST : sont a insérér dans Body -> form-data ou binary;
        - methode PUT : sont a insérér dans Body -> binary;
        
    - Caractères autorisés pour les noms : a-zA-Z0-9À-ÿ -_

    - Les Arguments à renseigner :
        - entre accolade dans l'url :
            - ex : {id}, {name}
        - commençant par "?":
            - ex : "?name=name&categoryId=id"
            Exemple : 
                {
                    "methode": "PUT",
                    "url": "/category/{name}",
                    "arguments": [
                        "name",
                        "?name=newName"
                    ]
                },
                URL => http://php-dev-2.online/category/Développement front end?name=Développement front-end
            
    - Les autres Arguments ne sont pas à renseigner (ils sont fixes et servent pour la requête).
        Exemple : 
            {
                "methode": "GET",
                "url": "/categories/name",
                "arguments": [
                    "name"
                ]
            },


### GET : 

    Exemples d'url :
        - Recherche toutes les routes : http://php-dev-2.online
        - Recherches toutes une table:
            - Ordre Id = http://php-dev-2.online/categories/
            - Ordre nom = http://php-dev-2.online/categories/name
        - Recherche par id = http://php-dev-2.online/category/1  
        - Recherche par nom = http://php-dev-2.online/category/Développement front end
            
### POST :

    Exemples d'url :
        - http://php-dev-2.online/category?name=Développement front end
        - http://php-dev-2.online/technology?name=Javascript&categoryId=1 
            ==>> logo en Form-data ou Binary

### PUT : 

    Exemples d'url :
        - http://php-dev-2.online/category/5?name=Développement back end 
        - http://php-dev-2.online/category/Développement front end?name=Développement front end 
            ==>> par nom : valable seulement pour catégorie

### DELETE :

    Exemples d'url :
        - http://php-dev-2.online/category/5
        - http://php-dev-2.online/category/Développement front end  
            ==>> par nom : valable seulement pour catégorie


## Routes

{
    "plus d'informations": "https://github.com/JoeWebDev70/02-lab-php-api",
    "Routes": [
        {
            "methode": "GET",
            "url": "/",
            "arguments": [],
            "explication": "Voir toutes les routes disponibles"
        },
        {
            "methode": "GET",
            "url": "/categories",
            "arguments": [
                "id"
            ],
            "explication": "Voir toutes les catégories, ordonnées par Id"
        },
        {
            "methode": "GET",
            "url": "/categories/name",
            "arguments": [
                "name"
            ],
            "explication": "Voir toutes les catégories, ordonnées par nom"
        },
        {
            "methode": "GET",
            "url": "/categories/technologies",
            "arguments": [
                "id"
            ],
            "explication": "Voir toutes les catégories qui contiennent des technologies, ordonnées par Id"
        },
        {
            "methode": "GET",
            "url": "/categories/name/technologies",
            "arguments": [
                "name"
            ],
            "explication": "Voir toutes les catégories qui contiennent des technologies, ordonnées par nom"
        },
        {
            "methode": "GET",
            "url": "/category/{id}",
            "arguments": [
                "id"
            ],
            "explication": "Voir une catégorie par son Id"
        },
        {
            "methode": "GET",
            "url": "/category/{name}",
            "arguments": [
                "name"
            ],
            "explication": "Voir une catégorie par son nom"
        },
        {
            "methode": "GET",
            "url": "/category/{id}/technologies",
            "arguments": [
                "id"
            ],
            "explication": "Voir une catégorie par son Id si elle contient des technologies"
        },
        {
            "methode": "GET",
            "url": "/category/{name}/technologies",
            "arguments": [
                "name"
            ],
            "explication": "Voir une catégorie par son nom si elle contient des technologies"
        },
        {
            "methode": "GET",
            "url": "/technologies",
            "arguments": [
                "id"
            ],
            "explication": "Voir toutes les technologies, ordonnées par Id"
        },
        {
            "methode": "GET",
            "url": "/technologies/name",
            "arguments": [
                "name"
            ],
            "explication": "Voir toutes les technologies, ordonnées par nom"
        },
        {
            "methode": "GET",
            "url": "/technology/{id}",
            "arguments": [
                "id"
            ],
            "explication": "Voir une technologie par son Id"
        },
        {
            "methode": "GET",
            "url": "/technology/{name}",
            "arguments": [
                "name"
            ],
            "explication": "Voir toutes les technologies du même nom"
        },
        {
            "methode": "GET",
            "url": "/resources",
            "arguments": [
                "id"
            ],
            "explication": "Voir toutes les ressources, ordonnées par Id"
        },
        {
            "methode": "GET",
            "url": "/resource/{id}",
            "arguments": [
                "id"
            ],
            "explication": "Voir une ressource par son Id"
        },
        {
            "methode": "GET",
            "url": "/resource/technology/{id}",
            "arguments": [
                "id"
            ],
            "explication": "Voir les ressources d'une technologie par son Id"
        },
        {
            "methode": "POST",
            "url": "/category",
            "arguments": [
                "?name=name"
            ],
            "explication": "Creer une nouvelle categorie"
        },
        {
            "methode": "POST",
            "url": "/technology",
            "arguments": [
                "?name=name&categoryId=id"
            ],
            "explication": "Creer une nouvelle technologie"
        },
        {
            "methode": "POST",
            "url": "/resource",
            "arguments": [
                "?technologyId=id&url=url"
            ],
            "explication": "Creer une nouvelle ressource pour une technologie"
        },
        {
            "methode": "PUT",
            "url": "/category/{id}",
            "arguments": [
                "id",
                "?name=newName"
            ],
            "explication": "Mettre à jour une catégorie par son Id"
        },
        {
            "methode": "PUT",
            "url": "/category/{name}",
            "arguments": [
                "name",
                "?name=newName"
            ],
            "explication": "Mettre à jour une catégorie par son nom"
        },
        {
            "methode": "PUT",
            "url": "/technology/{id}",
            "arguments": [
                "id",
                "?name=newName"
            ],
            "explication": "Mettre à jour une technologie par son Id"
        },
        {
            "methode": "PUT",
            "url": "/resource/{id}",
            "arguments": [
                "id",
                "?url=newUrl&technologyId=newTechnologyId"
            ],
            "explication": "Mettre à jour une ressource par son Id"
        },
        {
            "methode": "DELETE",
            "url": "/category/{id}",
            "arguments": [
                "id"
            ],
            "explication": "Supprimer une catégorie par son Id"
        },
        {
            "methode": "DELETE",
            "url": "/category/{name}",
            "arguments": [
                "name"
            ],
            "explication": "Supprimer une catégorie par son nom"
        },
        {
            "methode": "DELETE",
            "url": "/technology/{id}",
            "arguments": [
                "id"
            ],
            "explication": "Supprimer une technologie par son Id"
        },
        {
            "methode": "DELETE",
            "url": "/resource/{id}",
            "arguments": [
                "id"
            ],
            "explication": "Supprimer une ressource par son Id"
        }
    ]
}



## HHTP Codes Erreurs

200 OK : La requête a réussi, et le résultat est retourné. Il s'agit de la réponse standard pour les requêtes HTTP réussies.

201 Created : La requête a été traitée avec succès, et une nouvelle ressource a été créée en conséquence.

204 No Content : La requête a été traitée avec succès, mais il n'y a pas de contenu à renvoyer en réponse (par exemple, pour une suppression réussie).

400 Bad Request : La requête est incorrecte, malformée ou incomplète, et le serveur ne peut pas la comprendre.

401 Unauthorized : L'accès à la ressource est refusé en raison d'une authentification insuffisante ou de l'absence d'authentification.

403 Forbidden : L'accès à la ressource est interdit, généralement en raison de restrictions d'autorisation ou de règles de sécurité.

404 Not Found : La ressource demandée n'a pas été trouvée sur le serveur.

405 Method Not Allowed : La méthode HTTP spécifiée dans la requête n'est pas autorisée pour la ressource demandée.

406 Not Acceptable : Le serveur ne peut pas produire une réponse acceptable en fonction des en-têtes "Accept" de la requête.

408 Request Timeout : La requête a expiré en raison d'un délai d'attente dépassé.

500 Internal Server Error : Une erreur interne du serveur s'est produite, indiquant un problème avec le serveur lui-même.

502 Bad Gateway : Le serveur agit comme une passerelle ou un proxy, et il a reçu une réponse incorrecte ou invalide de la part du serveur en amont.

503 Service Unavailable : Le serveur est temporairement indisponible ou surchargé, et il ne peut pas répondre à la requête.

504 Gateway Timeout : Le serveur agit comme une passerelle ou un proxy, et il n'a pas pu obtenir une réponse à temps de la part du serveur en amont.

505 HTTP Version Not Supported : La version du protocole HTTP utilisée dans la requête n'est pas prise en charge par le serveur.


#### Liens vers le projet

Trouver le projet dans ce <a href ="https://github.com/JoeWebDev70/02-lab-php-api_project">repository</a>