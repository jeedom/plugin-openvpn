Ce plugin permet de connecter Jeedom à un serveur openvpn. Il est aussi
utilisé et donc obligatoire pour le service DNS Jeedom qui vous permet
d’accèder à votre Jeedom depuis internet

configuración del plugin
=======================

Après téléchargement du plugin, il vous suffit juste d’activer et
d’installer les dépendances openvpn (clic sur le bouton Installer/Mettre
à jour)

Configuration des équipements 
=============================

Aquí encontrará toda la configuración de su dispositivo:

-   **Nom de l’équipement Openvpn** : nom de votre équipement Openvpn,

-   **Objeto padre** : especifica el objeto padre al que pertenece
    equipos,

-   **Catégorie** : les catégories de l’équipement (il peut appartenir à
    plusieurs catégories),

-   ** ** Activar: para que su equipo activo,

-   ** ** visible hace que su equipo visible en el salpicadero,

> **Note**
>
> Les autres options ne seront pas détaillées ici, pour avoir de plus
> amples informations merci de vous référer à la [documention
> openvpn](https://openvpn.net/index.php/open-source/documentation.html)

En-dessous vous retrouvez la liste des commandes :

-   **Nom** : le nom affiché sur le dashboard,

-   **Afficher** : permet d’afficher la donnée sur le dashboard,

-   **Tester** : permet de tester la commande

> **Note**
>
> Jeedom va vérifier toutes les 15 minutes si le VPN est bien démarré ou
> arreté (s’il le faut) et agir en conséquence si ce n’est pas le cas
