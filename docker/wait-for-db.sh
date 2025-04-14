#!/bin/bash
set -e

# Wait for the database to be ready
echo "Waiting for database connection..."
MAX_TRIES=30
TRIES=0

while [ $TRIES -lt $MAX_TRIES ]; do
    if mysql -h "$DB_HOST" -u "$DB_USER" -p"$DB_PASS" -e "SELECT 1" >/dev/null 2>&1; then
        echo "Database connection established"
        break
    fi
    TRIES=$((TRIES+1))
    echo "Waiting for database connection (${TRIES}/${MAX_TRIES})..."
    sleep 2
done

if [ $TRIES -eq $MAX_TRIES ]; then
    echo "Error: Could not connect to the database after ${MAX_TRIES} attempts"
    exit 1
fi

# Check if the database exists
if ! mysql -h "$DB_HOST" -u "$DB_USER" -p"$DB_PASS" -e "USE ${DB_NAME}" >/dev/null 2>&1; then
    echo "Database ${DB_NAME} does not exist, creating it..."
    mysql -h "$DB_HOST" -u "$DB_USER" -p"$DB_PASS" -e "CREATE DATABASE IF NOT EXISTS ${DB_NAME}"
    
    # Import database schema
    if [ -f /var/www/html/DB.sql ]; then
        echo "Importing database schema..."
        mysql -h "$DB_HOST" -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" < /var/www/html/DB.sql
        echo "Database schema imported successfully"
    else
        echo "Warning: DB.sql file not found, skipping schema import"
    fi
else
    echo "Database ${DB_NAME} already exists"
    
    # Check if the database has tables
    TABLES=$(mysql -h "$DB_HOST" -u "$DB_USER" -p"$DB_PASS" -e "USE ${DB_NAME}; SHOW TABLES;" | wc -l)
    
    if [ "$TABLES" -le 1 ]; then
        echo "Database exists but has no tables. Importing schema..."
        if [ -f /var/www/html/DB.sql ]; then
            mysql -h "$DB_HOST" -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" < /var/www/html/DB.sql
            echo "Database schema imported successfully"
        else
            echo "Warning: DB.sql file not found, skipping schema import"
        fi
    else
        echo "Database already has tables. Skipping import."
    fi
fi

# Start Apache
echo "Starting Apache..."
apache2-foreground 