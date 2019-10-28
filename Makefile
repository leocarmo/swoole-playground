# Variables
HOST := 0.0.0.0
PORT := 8080

run:
	@echo "---- Running application ----"
	clear
	php public/index.php

update:
	@echo "---- Update application [Dev] ----"
	@composer update -vvv --prefer-dist --profile

docker_restart:
	@echo "---- Running docker image ----"
	@docker-compose down
	@docker-compose up --build
