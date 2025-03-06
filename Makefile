.PHONY: npm-install migrate test serve all

npm-install:
	@echo "Ejecutando npm install..."
	npm install

migrate:
	@echo "Ejecutando php artisan migrate:fresh..."
	php artisan migrate:fresh

test:
	@echo "Ejecutando php artisan test..."
	php artisan test

serve:
	@echo "Iniciando el servidor de Laravel..."
	php artisan serve

all: npm-install migrate test serve
