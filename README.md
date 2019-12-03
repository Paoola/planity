# Version

- php 7.1
- symfony 4.3

# Installation

Installation des packages 

```
composer install
```

```
yarn install
```

```
yarn run encore dev --watch
```

Configuration du fichier .env pour la connection à la base de données
```
DATABASE_URL=mysql://root:root@127.0.0.1:3306/salon
```
Création de la base de données:

* Init DB: `bin/console doctrine:schema:create`


Mise à jour schéma en base de données 
```
 bin/console doctrine:schema:update --force
```

Lancer le projet sur le serveur
```
 bin/console server:run
```
