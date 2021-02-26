
## 🛠💻 Initial setup for development


### ⚙️ Setup

Once you have checked out the project, you'll need to run:


- `yarn install` to install the dependencies
- `yarn update:submodules` to initialize the server with git submodules.


## :dancers: Work hard, dev hard
🏝 In isolation
To run the code like a pro (= the fastest way), you'll need to run :

- `docker-compose up -d` to up all containers 

- `docker ps` to list all containers

- `docker exec -ti {CONTAINER ID] /bin/bash` to enter in lana-beauty-nails_web container

- `bin/console doctrine:schema:update --force` to update the database

- then go to `http://127.0.0.1:8080` and import sql db migration `lana-beauty.sql`

- create a user in UI interface `http://127.0.0.1:82/inscription` 
- Edit the field `roles` in the `user` table with`a:1:{i:0;s:10:"ROLE_ADMIN";}`

- Add a `tarif` in `Gérer mon salon` 

- run once (and each time the server is updated) yarn build:server

and then go to http://127.0.0.1:82 to see the results 🎉.


