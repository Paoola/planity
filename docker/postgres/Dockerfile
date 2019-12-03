FROM postgres

RUN ln -s /var/lib/postgresql /home/postgres
RUN apt-get update && apt-get install -y postgresql-contrib && rm -rf /var/lib/apt/lists/*

COPY ./initdb-hstore.sh /docker-entrypoint-initdb.d/initdb-hstore.sh
COPY ./initdb-uuid.sh /docker-entrypoint-initdb.d/initdb-uuid.sh
