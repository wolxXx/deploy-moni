# deploy-moni

check docker-compose.yml file for exposed ports

docker compose up --build -d 

apply database.sql to database host

get http://127.0.0.1:7000 for overview

apply exampleData.sql if you want
run /random if you want

check deployments under / 

track deployment via get-query to /api/v1/log
with get parameters:  
 - group -> base64 encoded
 - name -> base64 encoded
 - date -> formatted 2024-01-01%2012:34:56 or null, wich means now
with header:
- x-api-key: 12345 -> defined in deployment_key table, checked with validity

see add.http for examples