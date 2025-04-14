.PHONY: help setup up down restart logs build prod-build prod-up dev prod backup clean-volumes prepare

# Default target
help:
	@echo "Sundarta Beauty and Wellness Platform - Make Commands"
	@echo ""
	@echo "Usage:"
	@echo "  make setup        - Initial setup (copy environment file if not exists)"
	@echo "  make prepare      - Create necessary directories with proper permissions"
	@echo "  make up           - Start development environment"
	@echo "  make down         - Stop development environment"
	@echo "  make restart      - Restart development environment"
	@echo "  make logs         - Show logs from containers"
	@echo "  make build        - Rebuild development containers"
	@echo "  make prod-build   - Build production containers"
	@echo "  make prod-up      - Start production environment"
	@echo "  make dev          - Start development environment (alias for up)"
	@echo "  make prod         - Start production environment (alias for prod-up)"
	@echo "  make backup       - Create database backup"
	@echo "  make clean-volumes - Remove all volumes (WARNING: This will delete all data)"

# Setup environment
setup: prepare
	@if [ ! -f .env ]; then \
		echo "Creating .env file from .env.example..."; \
		cp .env.example .env; \
	else \
		echo ".env file already exists, skipping..."; \
	fi

# Prepare directories
prepare:
	@echo "Creating necessary directories..."
	@mkdir -p storage/logs storage/cache storage/sessions storage/uploads
	@mkdir -p public/assets/uploads
	@mkdir -p docker/nginx/conf.d docker/nginx/ssl
	@mkdir -p backups
	@chmod -R 775 storage public/assets/uploads
	@echo "Directories created and permissions set."

# Start development environment
up: prepare
	docker-compose down --remove-orphans
	docker-compose up -d

# Stop development environment
down:
	docker-compose down

# Restart development environment
restart:
	docker-compose down
	docker-compose up -d

# Show logs
logs:
	docker-compose logs -f

# Rebuild containers
build: prepare
	docker-compose down --volumes --remove-orphans
	docker-compose build --no-cache
	docker-compose up -d

# Build production containers
prod-build: prepare
	docker-compose -f docker-compose.yml -f docker-compose.prod.yml down --volumes --remove-orphans
	docker-compose -f docker-compose.yml -f docker-compose.prod.yml build --no-cache

# Start production environment
prod-up: prepare
	@if [ ! -f .env.production ]; then \
		echo "Error: .env.production file not found. Please create it first."; \
		exit 1; \
	fi
	cp .env.production .env
	docker-compose -f docker-compose.yml -f docker-compose.prod.yml up -d

# Development alias
dev: up

# Production alias
prod: prod-up

# Create database backup
backup:
	@echo "Creating database backup..."
	@mkdir -p backups
	@docker-compose exec -T db mysqldump -u root -proot sundarta_db > backups/sundarta_db_$(shell date +%Y%m%d%H%M%S).sql
	@echo "Backup created in backups/ directory"

# Clean volumes (WARNING: destroys data)
clean-volumes:
	@echo "WARNING: This will delete all data in volumes. Are you sure? [y/N] " && read ans && [ $${ans:-N} = y ]
	docker-compose down -v 