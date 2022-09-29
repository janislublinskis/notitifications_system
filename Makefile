symfony console messenger:consume async -vv
symfony console server:start
docker-compose up -d
symfony console doctrine:migrations:migrate
symfony console doctrine:database:create
symfony console doctrine:database:create --env=test