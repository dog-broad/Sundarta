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
    if [ -f "$loc" ] && [ -s "$loc" ]; then  # Check if file exists and is not empty
        DB_SCRIPT="$loc"
        echo "✅ Found non-empty DB.sql at $DB_SCRIPT"
        break
    elif [ -f "$loc" ]; then
        echo "⚠️ Found DB.sql at $loc but it appears to be empty"
    fi
done

# If DB.sql not found in the usual locations, check if it might be in the current directory
if [ -z "$DB_SCRIPT" ] && [ -f "DB.sql" ] && [ -s "DB.sql" ]; then
    DB_SCRIPT="$(pwd)/DB.sql"
    echo "✅ Found DB.sql in current directory at $DB_SCRIPT"
fi

# Check if the database exists and create it if needed
echo "🔄 Checking if database $DB_NAME exists..."
if ! mysql -h "$DB_HOST" -P "$DB_PORT" -u "$DB_USER" ${DB_PASS:+-p"$DB_PASS"} -e "USE $DB_NAME" 2>/dev/null; then
    echo "🔄 Database $DB_NAME does not exist, creating it..."
    mysql -h "$DB_HOST" -P "$DB_PORT" -u "$DB_USER" ${DB_PASS:+-p"$DB_PASS"} -e "CREATE DATABASE IF NOT EXISTS $DB_NAME"
fi

# Initialize database if a valid DB.sql is found
if [ -n "$DB_SCRIPT" ]; then
    echo "🔄 Checking if database needs initialization..."
    
    # Check if users table exists as an indicator of whether DB is initialized
    TABLE_EXISTS=$(mysql -h "$DB_HOST" -P "$DB_PORT" -u "$DB_USER" ${DB_PASS:+-p"$DB_PASS"} -e "USE $DB_NAME; SHOW TABLES LIKE 'users';" 2>/dev/null | grep -c 'users')
    
    if [ "$TABLE_EXISTS" -eq 0 ]; then
        echo "🔄 Initializing database with DB.sql script..."
        
        # Check if the file has actual content
        SQL_SIZE=$(stat -c%s "$DB_SCRIPT")
        if [ "$SQL_SIZE" -lt 10 ]; then
            echo "⚠️ DB.sql appears to be too small (${SQL_SIZE} bytes). Database initialization might fail."
        fi
        
        # Try to initialize despite potential issues
        if mysql -h "$DB_HOST" -P "$DB_PORT" -u "$DB_USER" ${DB_PASS:+-p"$DB_PASS"} "$DB_NAME" < "$DB_SCRIPT"; then
            echo "✅ Database initialized successfully!"
        else
            echo "⚠️ Database initialization may have encountered issues, but the application will continue."
        fi
    else
        echo "✅ Database already initialized!"
    fi
else
    echo "⚠️ No valid DB.sql file found. Database initialization skipped."
    
    # Check if database is already initialized anyway
    TABLE_EXISTS=$(mysql -h "$DB_HOST" -P "$DB_PORT" -u "$DB_USER" ${DB_PASS:+-p"$DB_PASS"} -e "USE $DB_NAME; SHOW TABLES LIKE 'users';" 2>/dev/null | grep -c 'users')
    
    if [ "$TABLE_EXISTS" -eq 0 ]; then
        echo "⚠️ Database appears to be empty. The application may not function correctly."
        echo "⚠️ Please ensure you initialize the database manually or provide a valid DB.sql file."
        
        # Create minimal DB structure if needed (optional, uncomment to use)
        # echo "🔄 Creating minimal database structure..."
        # mysql -h "$DB_HOST" -P "$DB_PORT" -u "$DB_USER" ${DB_PASS:+-p"$DB_PASS"} "$DB_NAME" -e "
        #   CREATE TABLE IF NOT EXISTS users (
        #     id INT AUTO_INCREMENT PRIMARY KEY,
        #     username VARCHAR(255) NOT NULL,
        #     email VARCHAR(255) UNIQUE NOT NULL,
        #     password VARCHAR(255) NOT NULL,
        #     created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        #   );
        # "
        # echo "✅ Minimal database structure created."
    else
        echo "✅ Database already appears to be initialized with existing tables."
    fi
fi

echo "🚀 Starting Apache..."
# Start Apache in foreground
exec apache2-foreground 