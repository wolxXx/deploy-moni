# deploy-moni

## about
this tool allows you to track deployments. 
just curl the log endpoint and watch the dashboard reloads itself. 
or have a look at the latest deployment-list.



## developer notes
copy .env.dist to .env -> adjust ports and names

launch ./run.sh to bring up the containers

make sure you have deployment keys in the database and provide the key within a log request

backup: docker compose exec web php backup.php

apply database.sql to database host

get http://127.0.0.1:7000 for overview

get http://127.0.0.1:7000/recent for recent deployments

get http://127.0.0.1:7000/api/v1/items for json formatted deployment list

replace the port with the actual value from the .env file ;) 


apply exampleData.sql if you want
run /random if you want

check deployments under / 

track deployment via get-query to /api/v1/log
with get parameters:  
 - group -> base64 encoded
 - name -> base64 encoded
 - date -> formatted 2024-01-01%2012:34:56 or null, which means now
with header:
- x-api-key: 12345 -> defined in deployment_key table, checked with validity

see add.http for examples