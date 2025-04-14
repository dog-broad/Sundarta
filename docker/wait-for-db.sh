#!/bin/bash

# Get environment variables with defaults
DB_HOST=${DB_HOST:-localhost}
DB_USER=${DB_USER:-root}
DB_PASS=${DB_PASS:-}
DB_PORT=${DB_PORT:-3306}
MAX_TRIES=${MAX_TRIES:-30}
SLEEP_TIME=${SLEEP_TIME:-5}

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

# Database script path
DB_SCRIPT="/var/www/html/DB.sql"

# Check if we need to initialize the database
if [ -f "$DB_SCRIPT" ] && [ "$APP_ENV" = "development" ]; then
    echo "🔄 Checking if database needs initialization..."
    
    # Check if users table exists as an indicator of whether DB is initialized
    TABLE_EXISTS=$(mysql -h "$DB_HOST" -P "$DB_PORT" -u "$DB_USER" ${DB_PASS:+-p"$DB_PASS"} -e "USE $DB_NAME; SHOW TABLES LIKE 'users';" 2>/dev/null | grep -c 'users')
    
    if [ "$TABLE_EXISTS" -eq 0 ]; then
        echo "🔄 Initializing database with DB.sql script..."
        mysql -h "$DB_HOST" -P "$DB_PORT" -u "$DB_USER" ${DB_PASS:+-p"$DB_PASS"} < "$DB_SCRIPT"
        echo "✅ Database initialized successfully!"
    else
        echo "✅ Database already initialized!"
    fi
fi

echo "🚀 Starting Apache..."
# Start Apache in foreground
exec apache2-foreground 