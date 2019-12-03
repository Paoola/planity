
#!/bin/bash

set -e

psql -v ON_ERROR_STOP=1 --username "$POSTGRES_USER" "$POSTGRES_DB" <<-EOSQL
CREATE EXTENSION IF NOT EXISTS "hstore";
EOSQL

# To have extension created in new DB
psql -v ON_ERROR_STOP=1 --username "$POSTGRES_USER" "template1" <<-EOSQL
CREATE EXTENSION IF NOT EXISTS "hstore";
EOSQL
