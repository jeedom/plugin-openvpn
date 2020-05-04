Este plugin permite conectar o Jeedom a um servidor openvpn. Ele também é
usado e, portanto, obrigatório para o serviço DNS Jeedom, que permite que você
para acessar seu Jeedom da Internet

Configuração do plugin 
=======================

Depois de baixar o plugin, você só precisa ativar e
instalar dependências openvpn (clique no botão Instalar / Atualizar
atualizado)

Configuração do equipamento 
=============================

Aqui você encontra toda a configuração do seu equipamento :

-   **Nome do dispositivo openvpn** : nome do seu dispositivo Openvpn,

-   **Objeto pai** : indica o objeto pai ao qual pertence
    o equipamento,

-   **Categoria** : categorias de equipamentos (pode pertencer a
    várias categorias),

-   **Activer** : torna seu equipamento ativo,

-   **Visible** : torna seu equipamento visível no painel,

> **Note**
>
> As outras opções não serão detalhadas aqui, para ter mais
> Para mais informações, consulte a [documentação
> openvpn](https://openvpn.net/index.php/open-source/documentation.html)

> **Note**
>
> Para comandos shell executados após a inicialização, a tag # interface # para o nome da interface é substituída automaticamente

Abaixo você encontra a lista de pedidos :

-   **Nom** : o nome exibido no painel,

-   **Afficher** : permite exibir os dados no painel,

-   **Tester** : permite testar o comando

> **Note**
>
> O Jeedom irá verificar a cada 15 minutos se a VPN foi iniciada ou
> preso (se necessário) e agir de acordo se esse não for o caso
