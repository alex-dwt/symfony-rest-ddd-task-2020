up:
	docker-compose up

composer-install:
	docker-compose exec php composer install

init-database:
	docker-compose exec php bin/console doctrine:database:drop --if-exists --force --no-interaction
	docker-compose exec php bin/console doctrine:database:create
	docker-compose exec php bin/console doctrine:schema:update --force
	docker-compose exec php bin/console app:fill-currency-table

reload-fake-data:
	docker-compose exec php bin/console app:create-fake-transactions-and-rates

download-exchange-rates-for-today:
	docker-compose exec php bin/console app:fetch-currency-rates

run-concurrency-test:
	docker-compose exec php /concurrency_test.bash

run-concurrency-test-2:
	docker-compose exec php /concurrency_test_2.bash
