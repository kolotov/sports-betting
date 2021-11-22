workdir = /var/www/site

#composer path
phpunit = ./vendor/bin/phpunit

#docker compose container
app_exec = docker-compose exec --workdir $(workdir)
app_run  = docker-compose run --workdir $(workdir)

#install logs
logs =  | tee -a install.log

run:
	docker-compose stop $(logs)
	docker-compose up -d --build $(logs)
	docker-compose rm -f composer $(logs)
	docker-compose exec php docker-php-ext-install mysqli pdo pdo_mysql $(logs)
	docker-compose restart php $(logs)
	make composer-install $(logs)

restart:
	docker-compose restart

bash:
	$(app_exec) web bash

composer-install:
	$(app_run) --rm composer install

composer-update:
	$(app_run) --rm composer update

composer:
	$(app_run) --rm composer $(cmd)

test:
	$(app_exec) php $(phpunit) --testdox --colors=auto tests

test-filter:
	$(app_exec) php $(phpunit) --testdox --colors=auto --filter $(class) tests
