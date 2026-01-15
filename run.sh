#!/bin/bash

set -e
set -x

HERE=$(dirname $(readlink -f "$0"));
cd "$HERE";



check_docker() {
    if ! command -v docker &> /dev/null; then
        echo "Error: docker is not installed"
        exit 1
    fi
}

check_docker_compose() {
    if ! docker compose version &> /dev/null; then
        echo "Error: docker compose is not installed"
        exit 1
    fi
}

check_env_file() {
  if [ ! -f ".env" ]; then
      echo ".env file is missing."
      echo "Creating .env file from .env.dist."
      echo "please adjust the ports and names to your needs."
      cp .env.dist .env

      read -p "Do you want to edit the .env file with vim? (y/N): " response
      if [[ "$response" =~ ^[Yy]$ ]]; then
          vim .env
      else
          exit 0
      fi
  fi
}

check_docker
check_docker_compose
check_env_file

source .env

required_vars=("COMPOSE_PROJECT_NAME" "DB_PORT" "WEB_PORT")
missing_vars=()

for var_name in "${required_vars[@]}"; do
    if [ -z "${!var_name}" ]; then
        missing_vars+=("$var_name")
    fi
done

if [ ${#missing_vars[@]} -ne 0 ]; then
    echo "Error: The following variables are not set in .env file: ${missing_vars[*]}"
    exit 1
fi

docker compose --env-file .env --file docker-compose.yml  up -d --build --force-recreate --pull always
docker compose exec web ./composer.phar install

if [ ! -f "log" ]; then
    touch log
fi
sudo chmod 777 log


if [ ! -d "backups" ]; then
    mkdir backups
fi
sudo chmod -cR 777 backups

echo "web running under http://localhost:$WEB_PORT"
echo "db running under $DB_PORT"