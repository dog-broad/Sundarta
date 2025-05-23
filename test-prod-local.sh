#!/bin/bash
set -e

echo "===== Testing Production Environment Locally ====="
echo "This script will start a production-like environment on your local machine"
echo "without requiring SSL certificates or a domain name."

# Create necessary directories
echo "Setting up directories..."
mkdir -p storage/logs storage/cache storage/sessions storage/uploads
mkdir -p public/assets/uploads
mkdir -p backups
chmod -R 775 storage public/assets/uploads

# Copy production environment
echo "Setting up production environment..."
if [ -f .env.production.local ]; then
    cp .env.production.local .env
    echo "Using .env.production.local configuration"
else
    echo "Warning: .env.production.local not found. Using .env.production.sample as a template"
    cp .env.production.sample .env
    echo "Please update .env with your actual database credentials"
fi

# Clean up any existing containers
echo "Stopping any existing containers..."
docker-compose down --remove-orphans
docker-compose -f docker-compose.yml -f docker-compose.prod.local.yml down --remove-orphans

# Build and start containers
echo "Building and starting production containers..."
docker-compose -f docker-compose.yml -f docker-compose.prod.local.yml build
docker-compose -f docker-compose.yml -f docker-compose.prod.local.yml up -d

# Show running containers
echo ""
echo "===== Production Test Environment Started ====="
echo "Your production-like environment is now running at:"
echo "• Web: http://localhost:8081"
echo "• Database port: 3308"
echo ""
echo "Notes on using an external database in production:"
echo "1. Update your .env file with your external database settings"
echo "2. Make sure DB_HOST, DB_PORT, DB_USER, DB_PASS, and DB_NAME are configured"
echo "3. Ensure your database server allows connections from your application server"
echo ""
echo "To view logs, run:"
echo "docker-compose -f docker-compose.yml -f docker-compose.prod.local.yml logs -f"
echo ""
echo "To stop the environment, run:"
echo "docker-compose -f docker-compose.yml -f docker-compose.prod.local.yml down"
echo "===== End of Setup =====" 