symfony console messenger:consume async -vv
symfony console server:start
docker-compose up -d
symfony console doctrine:migrations:migrate
symfony console doctrine:database:create
symfony console doctrine:database:create --env=test

https://127.0.0.1:8000/api //API
http://localhost:8025/# //Mailhog
http://localhost:15672/#/ //Rabbitmq
./vendor/bin/phpunit --coverage-html=cov/
php bin/phpunit