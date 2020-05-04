Dieses Plugin ermöglicht die Verbindung von Jeedom mit einem openvpn-Server. Er ist auch
verwendet und daher obligatorisch für den Jeedom DNS-Dienst, der Ihnen erlaubt
um über das Internet auf Ihr Jeedom zuzugreifen

Plugin Konfiguration 
=======================

Nach dem Herunterladen des Plugins müssen Sie nur noch und aktivieren
Installieren Sie OpenVPN-Abhängigkeiten (klicken Sie auf die Schaltfläche Installieren / Aktualisieren
aktuell)

Gerätekonfiguration 
=============================

Hier finden Sie die gesamte Konfiguration Ihrer Geräte :

-   **Name des openvpn-Geräts** : Name Ihres Openvpn-Geräts,

-   **Übergeordnetes Objekt** : gibt das übergeordnete Objekt an, zu dem es gehört
    Ausrüstung,

-   **Kategorie** : Gerätekategorien (es kann gehören
    mehrere Kategorien),

-   **Activer** : macht Ihre Ausrüstung aktiv,

-   **Visible** : macht Ihre Ausrüstung auf dem Armaturenbrett sichtbar,

> **Note**
>
> Die anderen Optionen werden hier nicht näher erläutert, um mehr zu haben
> Weitere Informationen finden Sie in der [Dokumentation
> openvpn](https://openvpn.net/index.php/open-source/documentation.html)

> **Note**
>
> Für Shell-Befehle, die nach dem Start ausgeführt werden, wird das Tag # interface # für den Namen der Schnittstelle automatisch ersetzt

Nachfolgend finden Sie die Liste der Bestellungen :

-   **Nom** : Der im Dashboard angezeigte Name,

-   **Afficher** : ermöglicht die Anzeige der Daten im Dashboard,

-   **Tester** : Wird zum Testen des Befehls verwendet

> **Note**
>
> Jeedom überprüft alle 15 Minuten, ob das VPN gestartet wurde oder
> verhaftet (falls erforderlich) und entsprechend handeln, wenn dies nicht der Fall ist
