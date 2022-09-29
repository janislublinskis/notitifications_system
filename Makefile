start:
	symfony server:start

consume:
	symfony console messenger:consume async -vv

up:
	docker-compose up -d

down:
	docker-compose down

prune:
	docker stop $(docker ps -qa) && docker system prune -af --volumes

build:
	docker-compose up -d --build

rebuild:
	docker-compose down -v --remove-orphans
	docker-compose rm -vsf
	docker-compose up -d --build

db:
	docker-compose exec php ./bin/console doctrine:database:drop --force
	docker-compose exec php ./bin/console doctrine:database:create
	docker-compose exec php ./bin/console doctrine:database:create --env=test
	docker-compose exec php ./bin/console doctrine:migrations:migrate -n

prod:
	docker-compose -f docker-compose_prod.yml up -d

prod_build:
	docker-compose -f docker-compose_prod.yml build

coverage:
	docker-compose exec php ./vendor/bin/phpunit --coverage-html=cov/ #coverage driver yet to implement
