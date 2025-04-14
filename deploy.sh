#!/bin/bash
set -e

# Sundarta Production Deployment Script
# This script handles deployment of the Sundarta application to production
# Usage: ./deploy.sh [--skip-build]

# Parse arguments
SKIP_BUILD=false
while [ "$#" -gt 0 ]; do
  case "$1" in
    --skip-build) SKIP_BUILD=true; shift 1;;
    *) echo "Unknown parameter: $1"; exit 1;;
  esac
done

# Configuration
TIMESTAMP=$(date +%Y%m%d%H%M%S)
BACKUP_DIR="./backups"
ENV_FILE=".env.production"

echo "=== Sundarta Production Deployment ==="
echo "Starting deployment at $(date)"

# Check if running as root (which we shouldn't)
if [ "$(id -u)" = "0" ]; then
   echo "Error: This script should not be run as root" 1>&2
   exit 1
fi

# Ensure we're in the project root
if [ ! -f "docker-compose.yml" ]; then
    echo "Error: Must be run from project root" 1>&2
    exit 1
fi

# Create backup directory if it doesn't exist
mkdir -p $BACKUP_DIR

# Check for environment file
if [ ! -f "$ENV_FILE" ]; then
    echo "Error: $ENV_FILE not found. Please create it before deploying." 1>&2
    exit 1
fi

# Backup database
echo "=== Backing up database ==="
if [ -f ".env" ]; then
    source .env
    if docker-compose ps | grep -q db; then
        echo "Creating database backup..."
        docker-compose exec -T db mysqldump -u $DB_USER -p$DB_PASS $DB_NAME > "$BACKUP_DIR/sundarta_db_$TIMESTAMP.sql"
        echo "Database backup created at $BACKUP_DIR/sundarta_db_$TIMESTAMP.sql"
    else
        echo "Database container not running, skipping backup."
    fi
else
    echo "No .env file found, skipping database backup."
fi

# Copy production environment
echo "=== Setting up production environment ==="
cp "$ENV_FILE" .env
echo "Copied $ENV_FILE to .env"

# Pull latest changes if in git repository
if [ -d ".git" ]; then
    echo "=== Updating from git repository ==="
    git pull
    echo "Updated to latest version from git"
fi

# Build and start containers
if [ "$SKIP_BUILD" = false ]; then
    echo "=== Building production containers ==="
    docker-compose -f docker-compose.yml -f docker-compose.prod.yml build --no-cache
    echo "Build completed successfully"
fi

echo "=== Starting production environment ==="
docker-compose -f docker-compose.yml -f docker-compose.prod.yml up -d

# Verify deployment
echo "=== Verifying deployment ==="
sleep 10  # Give containers time to fully start
if curl -s http://localhost/api/health | grep -q '"status":"ok"'; then
    echo "Health check passed. Deployment successful!"
else
    echo "Warning: Health check did not return 'ok'. Please check logs."
    docker-compose logs app
fi

# Output information
echo "=== Deployment completed at $(date) ==="
echo "You can view logs with: docker-compose logs -f"
echo "To check container status: docker-compose ps"

# Make the script executable
chmod +x deploy.sh 