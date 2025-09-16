# Macro ERP (Docker)

## Requerimentos
- Docker

## Detalhes
Projeto usando `docker-compose`, levantando uma imagem do server PHP e outra do banco MySql.

## Instalação
Buildando pela primeira vez, instalando requerimentos do `composer` , construindo e populando o banco localmente (salvo na pasta `mysql-data`).:

`docker-build.bat`

Para as vezes seguintes, somente levantando os servers:

`docker-up.bat`

Acessando o servidor de php via terminal:

`docker-terminal-app.bat`

Aplicação disponível em http://localhost

