# Projet_API

Dans ce fichier vous pouvez recuperer le fichier restpatrop.json pour avoir toutes les requetes pour tester l'api sur insomnia.

Roles :
    - Client (utilisateur classique) : 0
    - Proprio : 1
    - Modérateur : 2
    - Administrateur : 3

Pour pouvoir faire une quelconque requete (hormis pour se connecter et créer un user), il
faut un token valide. Celui ci est généré dans authentification_middleware.php avec JWT
(dans Token.php);

authentification_middleware : 
    - Verifie si le client ne cherche pas à se créer un compte ou à se connecter.
    - Verifie si le champs authorization est bien rempli, sinon retourne une erreur : Token is NULL !
    - Verifie si le token existe en base de données pour eviter qu'un utilisateur utile un ancien 
      token toujours valide (au niveau du temps). Dans ce cas la : Error = Token doesn't exist.
    - Verifie si le token est toujours valide, on décode on voit si l'heure est supérieur à celle actuelle. 
      Sinon on renvoie une erreur : Token is expired !
    - Apres on verifie les acces + les droits de modifer certaines choses, ceci est expliqué en dessous avec toutes les requetes.

Sur Insomnia : 

Authentification : 
    - Log in : permet de se connecter pour obtenir son token. (POST)
    - Log out : permet de se déconnecter pour supprimer son token. (DELETE)

Site Web :
    Apartment : 
        Modérateurs : 
            - Seuls les modérateurs (role : 2) peuvent créer des appartements. (POST)
            - Seuls les modérateurs (role : 2) peuvent modifier les infos des appartements. (UPDATE)
        - Get apartments : tous les utilisateurs peuvent get tous les appartements.
        - Get apartment : tous les utilisateurs peuvent get un appartement d'id = x.
    
    Reservation :
        - (GET) reservations : tous les utilisateurs peuvent get toutes les reservations.
        - (GET) reservation : tous les utilisateurs peuvent get une reservations d'id = x.
        - (Post) reservation : tous les utilisateurs peuvent créer une reservation.
        - (Update) reservation : tous les utilisateurs peuvent modifier LEUR reservation. 
        (Une erreur est retournée si un utilisateur essaie de modifier une autre 
        reservation que la sienne)
        - (Delete) reservation : tous les utilisateurs peuvent supprimer LEUR reservation. 
        (Une erreur est retournée si un utilisateur essaie de supprimer une autre 
        reservation que la sienne)

    User :
        - (GET) users : tous les utilisateurs peuvent get tous les users.
        - (GET) user : tous les utilisateurs peuvent get un user.

        - (POST) user : tous les utilisateurs peuvent se créer un compte (role = 0 attribué)

        - (Update) user : tous les utilisateurs peuvent modifier LEUR profil.
        (Une erreur est retournée si un utilisateur essaie de modifier un 
        autre utilisateur que lui)
        -(Delete) user : tous les utilisateurs peuvent supprimer LEUR profil.
        (Une erreur est retournée si un utilisateur essaie de supprimer un 
        autre utilisateur que lui)

Application (Seuls les propriétaires y ont accès, role : 1): 
    (UPDATE) apartment : Les propriétaires peuvent modifier uniquement la disponibilité de LEURS appartements.
    (GET) apartments : Les propriétaires peuvent get que leurs appartements.

Back office (Seuls les administrateurs peuvent acceder au back office, role : 3):
    Apartment : 
        - (Get) apartments : Les administrateurs peuvent get tous les appartements.
        - (Get) apartment : Les administrateurs peuvent get un appartement d'id = x.
        - (POST) apartment : Les administrateurs peuvent créer des appartements.
        - (UPDATE) apartment : Les administrateurs peuvent modifier les informations des appartements.

    User : 
        - (GET) users : Les administrateurs peuvent get tous les users.
        - (GET) user : Les administrateurs peuvent get un user d'id = x.
        - (UPDATE) user : Les administrateurs peuvent que modifier le role du user.
        - (Delete) user : Les administrateurs peuvent delete n'importe quel user.

Contrainte métier : 

    User : 
        CREATE : Tu ne peux pas créer un utilisateur avec une adresse mail déjà utilisé.
        UPDATE : Tu ne peux pas modifier ton adresse mail par une adresse déjà utilisé. 
    Apartment : 
        CREATE : Tu ne peux pas créer un appart avec la meme adresse.
    Reservation : 
        CREATE : Tu ne peux pas reserver à la meme date qu'une reservation d'un autre utilisateur.
        UPDATE : Tu ne peux pas modifier la date à la meme date qu'une reservation d'un autre utilisateur.

Qui a fait quoi ? :

    - USER : Léna | HATEOAS : Nicolas/Arthur
    - APARTMENT : Nicolas | HATEOAS : Nicolas/Arthur
    - RESERVATION : Arthur | HATEOAS : Nicolas/Arthur

    - token : Lena/Arthur
    - authentification_middleware : Lena(80%)/Arthur(20%)
    - generation url : Nicolas

    Gestions des Erreurs : Lena/Nicolas/Arthur.



        