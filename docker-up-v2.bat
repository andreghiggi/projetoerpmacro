@echo off
setlocal

echo ### Parando containers ###
docker stop app app_db

echo ### Removendo containers ###
docker rm app app_db

echo ### Rodando app_db ###
docker run -d ^
  --name app_db ^
  --network local ^
  -p 3306:3306 ^
  -e MYSQL_DATABASE=app ^
  -e MYSQL_USER=user ^
  -e MYSQL_PASSWORD=pass ^
  -e MYSQL_ROOT_PASSWORD=root ^
  -v ./mysql-data:/var/lib/mysql ^
  mysql:8.0

echo ### Rodando app ###
docker run -d ^
  --name app ^
  --network local ^
  -p 80:80 ^
  -v .:/app ^
  app-image