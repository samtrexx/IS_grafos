<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">

</p>

## Como instalar


- git clone -b logica-grafos https://github.com/samtrexx/IS_grafos.git
- npm install (en la carpeta) 
Para Composer :
- Run composer update
- rename .env.example a .env
- Run php artisan key:generate
- create the data base
- run php artisan migrate
- [run php artisan serve
- npm run dev

api key : 8b53a847ebb34ffd91b0162c3f16c9b1
Generar una BD en NEO4j, local nombre "data"
Credenciales (nombre, contraseña) Contraseña se cambia en localhost:7474
Cambios en el config NEO4j:
{
# Bolt connector
server.bolt.enabled=true
server.bolt.listen_address=:7687
server.bolt.advertised_address=:7687

# HTTP Connector
server.http.enabled=true
server.http.listen_address=:7474
server.http.advertised_address=:7474

# HTTPS Connector (deshabilitado si no lo necesitas)
server.https.enabled=false
}

npm install vis-network
composer require laudis/neo4j-php-client
composer require guzzlehttp/guzzle


