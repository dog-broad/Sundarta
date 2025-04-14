#!/bin/bash

# Get environment variables with defaults
DB_HOST=${DB_HOST:-localhost}
DB_USER=${DB_USER:-root}
DB_PASS=${DB_PASS:-}
DB_PORT=${DB_PORT:-3306}
DB_NAME=${DB_NAME:-sundarta_db}
APP_TIMEZONE=${APP_TIMEZONE:-UTC}
MAX_TRIES=${MAX_TRIES:-30}
SLEEP_TIME=${SLEEP_TIME:-5}

# Set PHP timezone dynamically (can't use env vars directly in php.ini)
if [ -n "$APP_TIMEZONE" ]; then
    echo "Setting PHP timezone to $APP_TIMEZONE"
    echo "date.timezone = $APP_TIMEZONE" > /usr/local/etc/php/conf.d/timezone.ini
fi

echo "🔄 Checking database connection..."
COUNTER=0

# Try to connect to MySQL with increasing timeouts
while [ $COUNTER -lt $MAX_TRIES ]; do
    COUNTER=$((COUNTER+1))
    
    echo "⏳ Attempt $COUNTER of $MAX_TRIES: Connecting to MySQL at $DB_HOST:$DB_PORT..."
    
    if mysqladmin ping -h "$DB_HOST" -P "$DB_PORT" -u "$DB_USER" ${DB_PASS:+-p"$DB_PASS"} --silent; then
        echo "✅ MySQL is available! Connecting..."
        break
    fi
    
    # If this is the last attempt, exit with error
    if [ $COUNTER -eq $MAX_TRIES ]; then
        echo "❌ Could not connect to MySQL after $MAX_TRIES attempts. Exiting."
        exit 1
    fi
    
    echo "⚠️ MySQL is not available yet. Waiting $SLEEP_TIME seconds..."
    sleep $SLEEP_TIME
done

# Log the project structure for debugging purposes, skipping vendor and .git folders
echo "🔍 Logging project structure for debugging..."
if command -v tree > /dev/null; then
    tree /var/www/html --prune -I 'vendor|.git'
else
    echo "⚠️ 'tree' command not found. Falling back to 'find' command."
    find /var/www/html -not -path '*/vendor/*' -not -path '*/.git/*' -print
fi

# Check for DB.sql in multiple locations
echo "🔍 Looking for DB.sql file..."
DB_SCRIPT=""
DB_LOCATIONS=(
    "/var/www/html/DB.sql"
    "/docker-entrypoint-initdb.d/DB.sql"
    "/var/www/DB.sql"
)

for loc in "${DB_LOCATIONS[@]}"; do
    if [ -f "$loc" ]; then
        DB_SCRIPT="$loc"
        echo "✅ Found DB.sql at $DB_SCRIPT"
        break
    fi
done

# If DB.sql not found in the usual locations, check if it might be in the current directory
if [ -z "$DB_SCRIPT" ] && [ -f "DB.sql" ]; then
    DB_SCRIPT="$(pwd)/DB.sql"
    echo "✅ Found DB.sql in current directory at $DB_SCRIPT"
fi

# Copy DB.sql to a known location if it's not already in the expected path
if [ -n "$DB_SCRIPT" ] && [ "$DB_SCRIPT" != "/var/www/html/DB.sql" ]; then
    echo "📋 Copying $DB_SCRIPT to /var/www/html/DB.sql for easier access"
    cp "$DB_SCRIPT" "/var/www/html/DB.sql"
    DB_SCRIPT="/var/www/html/DB.sql"
fi

# Check if the database exists and create it if needed
echo "🔄 Checking if database $DB_NAME exists..."
if ! mysql -h "$DB_HOST" -P "$DB_PORT" -u "$DB_USER" ${DB_PASS:+-p"$DB_PASS"} -e "USE $DB_NAME" 2>/dev/null; then
    echo "🔄 Database $DB_NAME does not exist, creating it..."
    mysql -h "$DB_HOST" -P "$DB_PORT" -u "$DB_USER" ${DB_PASS:+-p"$DB_PASS"} -e "CREATE DATABASE IF NOT EXISTS $DB_NAME"
fi

# Initialize database if DB.sql is found
if [ -n "$DB_SCRIPT" ]; then
    echo "🔄 Checking if database needs initialization..."
    
    # Check if users table exists as an indicator of whether DB is initialized
    TABLE_EXISTS=$(mysql -h "$DB_HOST" -P "$DB_PORT" -u "$DB_USER" ${DB_PASS:+-p"$DB_PASS"} -e "USE $DB_NAME; SHOW TABLES LIKE 'users';" 2>/dev/null | grep -c 'users')
    
    if [ "$TABLE_EXISTS" -eq 0 ]; then
        echo "🔄 Initializing database with DB.sql script..."
        mysql -h "$DB_HOST" -P "$DB_PORT" -u "$DB_USER" ${DB_PASS:+-p"$DB_PASS"} "$DB_NAME" < "$DB_SCRIPT"
        echo "✅ Database initialized successfully!"
    else
        echo "✅ Database already initialized!"
    fi
else
    echo "⚠️ DB.sql not found. Database initialization skipped."
    echo "⚠️ Please ensure DB.sql is available in the container or mounted as a volume."
fi

echo "🚀 Starting Apache..."
# Start Apache in foreground
exec apache2-foreground 