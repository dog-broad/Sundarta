#!/bin/bash
set -e

echo "===== Checking DB.sql Availability for Deployment ====="

# Check if DB.sql exists in the root directory
if [ -f "DB.sql" ]; then
    echo "✅ DB.sql found in project root directory"
    # Check file size
    DB_SIZE=$(du -h DB.sql | cut -f1)
    echo "   - Size: $DB_SIZE"
    # Check line count
    DB_LINES=$(wc -l < DB.sql)
    echo "   - Lines: $DB_LINES"
    # Check if file appears valid
    if grep -q "CREATE DATABASE IF NOT EXISTS" DB.sql; then
        echo "   - Content: Valid database script detected"
    else
        echo "⚠️ Warning: DB.sql may not contain expected database creation commands"
    fi
else
    echo "❌ DB.sql not found in project root!"
    
    # Check for possible locations
    POSSIBLE_LOCATIONS=(
        "./database/DB.sql"
        "./sql/DB.sql"
        "./setup/DB.sql"
        "./data/DB.sql"
    )
    
    DB_PATH=""
    for loc in "${POSSIBLE_LOCATIONS[@]}"; do
        if [ -f "$loc" ]; then
            DB_PATH="$loc"
            echo "✅ Found DB.sql at: $DB_PATH"
            break
        fi
    done
    
    if [ -n "$DB_PATH" ]; then
        echo "📋 Copying $DB_PATH to project root for deployment..."
        cp "$DB_PATH" "./DB.sql"
        echo "✅ DB.sql copied to project root"
    else
        echo "❌ Could not find DB.sql in common locations. Please make sure it exists before deployment."
        echo "   Deployment might fail without proper database initialization."
        exit 1
    fi
fi

echo ""
echo "===== Database Initialization Check ====="
echo "During container startup, the application will:"
echo "1. Search for DB.sql in multiple locations"
echo "2. Create the database if it doesn't exist"
echo "3. Initialize the database if it's empty"
echo ""
echo "This ensures that even if the DB.sql placement changes, the application"
echo "will still be able to find and use it for database initialization."
echo ""
echo "===== Deployment Ready ====="
echo "Your application is ready for deployment with proper database initialization." 