build:
	docker-compose up -d
	docker-compose exec app bash

up:
	docker-compose up

down:
	docker-compose down

stop:
	docker-compose stop

ps:
	docker ps

bash:
	docker-compose exec app bash
