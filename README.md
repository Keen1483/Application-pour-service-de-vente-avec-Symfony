- Ce project a été realisé evec Symfony 5.2 et composer 2.1
- Rassurez vous qu'en plus de ces deux éléments, vous avez un Système de Gestion de Base de Données adéquate (SGBD) et le logiciel POstman pour tester l'api

Tester le project en suivant les instructions suivantes


1 - Lancer votre SGBD
2 - Importez y le fichier inimov.sql pour mettre en place la base de données
3 - Ouvrez votre terminale
4 - $ git remote add origin https://github.com/Keen1483/Application-pour-service-de-vente-avec-Symfony.git
5 - $ cd direct_project_name
6 - $ php bin/console install
7 - $ php bin/console d:m:m
8 - $ php bin/console d:f:l --no-interaction
9 - $ symfony server:start -d
10 - Ouvrez votre navigateur à l'adresse http://127.0.0.1:8000/
 
Vous visualisez à présent le projet

Comment le tester ?

*** La page d'erreur n'a pas été conçu pour que vous puissiez faire la différence entre l'authentification et l'autorisation qui ont été mise en place ***

1 - Inscrivez vous et connectez vous
Vous pouvez à présent donnez votre avis ou commenter sur chaque produit

2 - Regar la bare de navigation à l'extrème, un lien "admin" est apparu lorsque vous vous êtes connecté

3 - Cliquez sur ce lien "admin"
    **************************
    Symfony vous renvoie ACCESS DENIED
    C'est n'est pas une erreur
    Vous avez été authentifié pour commenter les produits, mais vous n'avez l'autorisation de les modifier.
    Ce lien "admin" est celui qui n'apparaît jamais lorsque nous navigons sur les sites internets.
    Ce lien n'est pas pour vous et il ne devrais même être là.
    Si la page d'erreur étais mise en place vous serez tout simple diridé vers elle (ERROR 404 PAGE NOT FOUND) et pourtant cette existe bien.

4 - Accédons maintenant à cette page
    ********************************
    - Connectez avec les identifiants suivantes:
      Email: karl@gmail.com
      Mot de passe: kkkkkkkk
    ********************************
    C'est un utilisateur qui a les droit d'accès

5 - Vous à présent accéder à ce lien "admin"
    ***************************************
    En réalité ce lien de devrait apparaître que si vous avez les droits d'accès.
    Il a été mis là juste pour le test

6 - Vous pouvez à présent
    *********************
    - Ajouter, Modifier et Supprimer les produits
    - Vous pouvez même vous donnez le droit d'administration pour pouvoir administrer les données aves vos propre identifiants (Email et Mot de passe)

7 - Consulter bien la suppression du produit qui supprime l'image dans la base de données et dans le repertoire "upload/images" qui trouve également dans le repertoire "public" à la racine du projet.

******************************************
******************************************

    Testons à présent l'api
    ***********************
1 - Rassurez vous le projet est toujours en cours d'excécussion

2 - Lancer Postman

3 - Faîtes une requêtte GET vers le lien "/api/products"
    ***************************************************
    Vous obtenez un document JSON qui contient tous les produits de la base de données

4 - Faîtes une requêtte POST vers la même adresse
    *********************************************
    - Si votre reqette est incomplète, Symfony renvera un message à Postman vous signalant les éléments manquantes : ("l'image nes peut pas être nulle ou d'autres éléments)
    - Ces viennent de la configuration de la base de données, puisque Postman cherche à y insérer les données, il qu'il respect donc les contrats mise en place.

*****************************************************
*****************************************************

****** Esthétiques ******
Je sais que l'aspet esthétique n'a pas été mise en place, car il falais que je:

1 - Contruise le "Package.json" avec "npm", "sass" et "autoprefixer"
2 - Rendre les image uniforme avec Photoshop et Adobe illustrator

Je vous renverai également le l'application IONIC pour bien tester l'api "et surtout pour recharger instanément et dynamiquement les produit sur la page d'acceuil avec la bibliothèque "Reactive js" intégré à IONIC.

J'ai mise en place ce appel dynamique dans l'application symfony avec le pouce qui se trouve sur la page d'accueil. Lorsque vous cliquez sur ce pouce, les produit changenr aléatoirement sur la page. En claire, un utilisateur ne peut pas déterminer le nombre de produit en stoque puisque les image charges aléatoirement.
Mais avec IONIC nous aurons pas besoin de cliquez sur quoi que ce soit pour que les images changent. Ce service est manifiquement gérer avec la Reactive JS.

Je sais qu'avec Symfony il étais toujours possible d'éffectuer ceete tâche avec les bibliothèques "mockjax" et "jQuery". Mais à bon de perdre tout ce temps alors qu'il d'autre moyens plus simple pour faire le même travail.


******************** Cordialement **********************

                DONGMO BERNARD GERAUD

                Gmail : keenndjc@gmail.com
                GitHub : https://github.com/keen1483
                GitLab: https://gitlab.com/keen1483
                Codepen : https://codepen.io/keen1483
                Twitter : https://twitter.com/DongmoGeraud

