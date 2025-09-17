@echo off
setlocal
:PROMPT
SET /P CONFIRM=Esta acao ira apagar e reconstruir todas as imagens, containers e banco de dados locais, continuar?(Y/N)
IF /I "%CONFIRM%" NEQ "Y" GOTO END

echo ### Parando containers ###
docker stop app app_db
echo ### Removendo containers ###
docker rm app app_db
echo ### Removendo Network ###
docker network rm local
echo ### Removendo imagem ###
docker rmi app-image
echo ### Removendo banco local ###
rm -rf mysql-data

echo ### Criando Rede Local ###
docker network create local
echo ### Criando Volume Local ###
docker volume create mysql_data

echo ### Build da imagem do app ###
docker build -f docker/Dockerfile -t app-image .

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

echo ### Aplicando configuracoes finais ###
docker exec -it app /app/app_config.sh

:END
