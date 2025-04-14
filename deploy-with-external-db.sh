#!/bin/bash
set -e

echo "===== Deploying Sundarta with External Database ====="
echo "This script will help deploy the application to production"
echo "using an external MySQL database."

# Check if .env.production exists
if [ ! -f .env.production ]; then
    echo "Error: .env.production file not found!"
    echo "Please create this file with your production environment settings."
    echo "You can use .env.production.sample as a template."
    exit 1
fi

# Check the database connection using values from .env.production
source .env.production

# Test database connection
echo "Testing database connection to $DB_HOST:$DB_PORT..."
if ! mysqladmin ping -h "$DB_HOST" -P "$DB_PORT" -u "$DB_USER" -p"$DB_PASS" --silent; then
    echo "Error: Cannot connect to the external database."
    echo "Please check your database credentials and ensure the database is accessible."
    exit 1
fi

echo "Database connection successful!"

# Create necessary directories
echo "Setting up directories..."
mkdir -p storage/logs storage/cache storage/sessions storage/uploads
mkdir -p public/assets/uploads
mkdir -p backups
chmod -R 775 storage public/assets/uploads

# Copy the environment file
echo "Setting up production environment..."
cp .env.production .env

# Build and start the container
echo "Building and starting the production container..."
docker-compose -f docker-compose.yml -f docker-compose.prod.external-db.yml down --remove-orphans
docker-compose -f docker-compose.yml -f docker-compose.prod.external-db.yml build
docker-compose -f docker-compose.yml -f docker-compose.prod.external-db.yml up -d

echo ""
echo "===== Deployment Completed ====="
echo "Your application should now be running using the external database."
echo "To view logs, run:"
echo "docker-compose -f docker-compose.yml -f docker-compose.prod.external-db.yml logs -f"
echo ""
echo "To stop the application, run:"
echo "docker-compose -f docker-compose.yml -f docker-compose.prod.external-db.yml down"
echo "===== End of Deployment =====" 