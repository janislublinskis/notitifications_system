#Notifications Microservice with APIs<br/>

#Setup<br/>
Consists of 5 docker container services: <br/><br/>
postgres <br/>
php <br/>
nginx <br/>
mailhog <br/>
rabbitmq <br/>

Project has a <b>Makefile</b> which contains a bunch of macros.<br/>
Should be started as easy as (with installed Docker & Docker Compose):<br/><br/>
```docker-compose up -d --build```<br/><br/>
Followed by (in case it did not happen by it self:<br/><br/>
```docker-compose exec php ./bin/console doctrine:database:create```<br/>
```docker-compose exec php ./bin/console doctrine:database:create --env=test```<br/>
```docker-compose exec php ./bin/console doctrine:migrations:migrate -n```<br/><br/>
On success we launch server and message consumer:<br/><br/>
```symfony server:start```<br/>
```symfony console messenger:consume async -vv```<br/><br/>

<b>Reaching out services:</b><br/><br/>
https://localhost:8000/api #API <br/>
https://localhost:8025/# #Mailhog <br/>
https://localhost:15672/#/ #Rabbitmq <br/>