#!/bin/bash
set -e

echo "===== Preparing Sundarta for Render Deployment ====="

# Check if render-cli is installed
if ! command -v render &> /dev/null; then
    echo "Render CLI is not installed. For easier deployment, consider installing it:"
    echo "npm install -g @renderinc/cli"
    echo ""
    echo "Continuing with manual preparation..."
fi

# Create necessary directories
echo "Setting up directories..."
mkdir -p storage/logs storage/cache storage/sessions storage/uploads
mkdir -p public/assets/uploads
chmod -R 775 storage public/assets/uploads 2>/dev/null || true

echo ""
echo "===== Render Deployment Instructions ====="
echo "To deploy to Render, you have two options:"

echo ""
echo "Option 1: Deploy using render.yaml (Blueprint)"
echo "  1. Push your code to a Git repository (GitHub, GitLab, etc.)"
echo "  2. In Render dashboard, click 'New' and select 'Blueprint'"
echo "  3. Connect your repository and Render will automatically detect the render.yaml file"
echo "  4. Follow the prompts to create the services defined in the blueprint"

echo ""
echo "Option 2: Manual deployment"
echo "  1. In Render dashboard, click 'New' and select 'Web Service'"
echo "  2. Connect your repository"
echo "  3. Use the following settings:"
echo "     - Environment: Docker"
echo "     - Docker Build Context: ."
echo "     - Dockerfile Path: docker/Dockerfile"
echo "     - Branch: main (or your preferred branch)"
echo "  4. Add the following environment variables:"
echo "     - APP_ENV=production"
echo "     - APP_DEBUG=false"
echo "     - DB_HOST=(your database host)"
echo "     - DB_USER=(your database user)"
echo "     - DB_PASS=(your database password)"
echo "     - DB_NAME=(your database name)"
echo "     - DB_PORT=3306 (or your database port)"
echo "     - APP_TIMEZONE=Asia/Kolkata (or your preferred timezone)"
echo "  5. Add a disk:"
echo "     - Mount Path: /var/www/html/storage"
echo "     - Size: 1GB (or as needed)"

echo ""
echo "For external database:"
echo "  - You can use Render's MySQL service or any external MySQL database"
echo "  - Ensure the database credentials in your environment variables are correct"
echo "  - The app will automatically connect to the external database"

echo ""
echo "===== Local Testing Before Deployment ====="
echo "To test your application locally with the same configuration as Render:"
echo "docker-compose -f docker-compose.yml -f docker-compose.render.yml build"
echo "docker-compose -f docker-compose.yml -f docker-compose.render.yml up -d"

echo ""
echo "===== Deployment Check Complete =====" 