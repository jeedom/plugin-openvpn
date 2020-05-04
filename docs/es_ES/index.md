Este complemento permite conectar Jeedom a un servidor openvpn. El también es
usado y, por lo tanto, obligatorio para el servicio DNS de Jeedom que le permite
para acceder a su Jeedom desde internet

Configuración del plugin 
=======================

Después de descargar el complemento, solo necesita activar y
instalar dependencias openvpn (haga clic en el botón Instalar / Actualizar
al día)

Configuración del equipo 
=============================

Aquí encontrarás toda la configuración de tu equipo :

-   **Nombre del dispositivo openvpn** : nombre de su dispositivo Openvpn,

-   **Objeto padre** : indica el objeto padre al que pertenece
    equipo,

-   **Categoría** : categorías de equipos (puede pertenecer a
    categorías múltiples),

-   **Activer** : activa su equipo,

-   **Visible** : hace que su equipo sea visible en el tablero,

> **Note**
>
> Las otras opciones no se detallarán aquí, para tener más
> Para más información, consulte la [documentación
> openvpn](https://openvpn.net/index.php/open-source/documentation.html)

> **Note**
>
> Para los comandos de shell ejecutados después del inicio, tiene la etiqueta # interface # para el nombre de la interfaz reemplazada automáticamente

A continuación encontrará la lista de pedidos. :

-   **Nom** : el nombre que se muestra en el tablero,

-   **Afficher** : permite mostrar los datos en el tablero,

-   **Tester** : Se usa para probar el comando

> **Note**
>
> Jeedom verificará cada 15 minutos si se inicia la VPN o
> arrestado (si es necesario) y actuar en consecuencia si este no es el caso
