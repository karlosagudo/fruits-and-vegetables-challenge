cnf1 ?= .env.local
include $(cnf1)
export $(shell sed 's/=.*//' $(cnf1))

CUR_DIR = $(notdir $(shell pwd))
DOCKER_IMAGE_NAME = ${CUR_DIR}-php
COLOR_RESET   = \033[0m
COLOR_SUCCESS = \033[32m
COLOR_ERROR   = \033[31m
COLOR_COMMENT = \033[33m

define log
	echo "[$(COLOR_COMMENT)$(shell date +"%T")$(COLOR_RESET)][$(COLOR_COMMENT)$(@)$(COLOR_RESET)] $(COLOR_COMMENT)$(1)$(COLOR_RESET)"
endef

define log_success
	echo "[$(COLOR_SUCCESS)$(shell date +"%T")$(COLOR_RESET)][$(COLOR_SUCCESS)$(@)$(COLOR_RESET)] $(COLOR_SUCCESS)$(1)$(COLOR_RESET)"
endef

define log_error
	echo "[$(COLOR_ERROR)$(shell date +"%T")$(COLOR_RESET)][$(COLOR_ERROR)$(@)$(COLOR_RESET)] $(COLOR_ERROR)$(1)$(COLOR_RESET)"
endef

define touch
	$(shell mkdir -p $(shell dirname $(1)))
	$(shell touch $(1))
endef


USER = $(shell whoami)
export USER_ID=1001
export GROUP_ID=1001
export USER_NAME=${USER}

CURRENT_USER := $(shell id -u)
CURRENT_GROUP := $(shell id -g)

TTY   := $(shell tty -s || echo '-T')
DOCKER_COMPOSE := FIXUID=$(CURRENT_USER) FIXGID=$(CURRENT_GROUP) docker-compose

.DEFAULT_GOAL := help
.PHONY: help
help: ## Show help
	@grep -E '^[a-zA-Z0-9 -]+:.*#'  Makefile | while read -r l; do printf "\033[1;32m$$(echo $$l | cut -f 1 -d':')\033[00m:$$(echo $$l | cut -f 2- -d'#')\n"; done

.PHONY: build
build: ## build docker container.
	@$(call log,Starting the docker stack ...)
	$(DOCKER_COMPOSE) --project-name $(CUR_DIR) -f docker-compose.yml up -d --build #--force-recreate

init: ## run composer install in the container
	@docker exec -ti $(DOCKER_IMAGE_NAME)-1 sh -c "composer install --no-interaction"

start: up ## serve app in defined port.
	@docker exec -u $(USER) -ti ${DOCKER_IMAGE_NAME}-1 sh -c "cd /srv/app; symfony serve -d --port=8000 --no-tls --listen-ip=0.0.0.0"

init-db: ## serve app in defined port.
	@docker exec -u $(USER) -ti ${DOCKER_IMAGE_NAME}-1 sh -c "/srv/app/src/init.sh"

stop: # stop app
	@docker exec -u $(USER) -ti ${DOCKER_IMAGE_NAME}-1 sh -c "symfony server:stop"

up: # start container
	$(DOCKER_COMPOSE) --project-name $(CUR_DIR) -f docker-compose.yml up -d

down: # stop container
	$(DOCKER_COMPOSE) --project-name $(CUR_DIR) -f docker-compose.yml down

prune: # remove unused containers
	@docker kill $(docker ps -q) ; docker system prune --force --all

logs: # docker logs
	@docker-compose logs

shell: up # gets into container shell
	@$(call log,Entering inside php container ...)
	@docker exec -u $(shell id -u $(USER)):$(shell id -g $(USER)) -ti ${DOCKER_IMAGE_NAME}-1 /bin/bash

shell-root: up # gets into container shell as root
	@$(call log,Entering inside php container as ROOT...)
	@docker exec -u root -ti ${DOCKER_IMAGE_NAME}-1 /bin/bash

fix: # php-cs-fixer
	@docker exec -u root -ti ${DOCKER_IMAGE_NAME}-1 sh -c 'vendor/bin/php-cs-fixer fix --diff --config .php-cs-fixer.dist.php src tests'

stan: # Run phpstan checks
	@docker exec -u root -ti ${DOCKER_IMAGE_NAME}-1 sh -c 'vendor/bin/phpstan analyse --memory-limit 1G'

generate: # Generate openapi-extractor files
	@docker exec -u root -ti ${DOCKER_IMAGE_NAME}-1 sh -c 'vendor/bin/openapi-extractor contracts/wound_monitoring.yml -o var/tmp'

xdebug-toggle: # Toggle Xdebug activation
	@docker exec -u root -ti ${DOCKER_IMAGE_NAME}-1 sh -c 'xdebug-toggle.sh && symfony server:stop && symfony server:start -d --port=8000'

test: up # Run all tests
	@docker exec -u root -ti ${DOCKER_IMAGE_NAME}-1 sh -c './vendor/bin/phpunit'
	@make down

test-coverage: up # Run all tests, show coverage in terminal and create a report at var/test_reports
	@docker exec -u root -ti ${DOCKER_IMAGE_NAME}-1 sh -c 'XDEBUG_MODE=coverage ./vendor/bin/phpunit --coverage-html=var/test_reports --coverage-text'
	@make down

test-mutant: up # Run all tests, show coverage in terminal and create a report at var/test_reports
	@docker exec -u root -ti ${DOCKER_IMAGE_NAME}-1 sh -c './vendor/bin/infection ' $(args)
	@make down

test-mutant-local: # Run all mutant tests locally
	@./vendor/bin/infection $(args)

release:
	@sh ./release.sh

install-hooks: # install hooks
	@docker exec -u root -ti ${DOCKER_IMAGE_NAME}-1 sh -c './vendor/bin/captainhook install --only-enabled ' $(args)
