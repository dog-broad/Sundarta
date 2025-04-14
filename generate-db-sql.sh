#!/bin/bash
set -e

echo "===== Generating DB.sql file for deployment ====="

# Function to check if a command exists
command_exists() {
  command -v "$1" >/dev/null 2>&1
}

# Function to dump database from localhost
dump_database() {
  local db_user=${1:-root}
  local db_pass=${2:-root}
  local db_name=${3:-sundarta_db}
  local db_host=${4:-localhost}
  local db_port=${5:-3306}
  
  echo "Attempting to dump database $db_name from $db_host:$db_port..."
  mysqldump -h "$db_host" -P "$db_port" -u "$db_user" ${db_pass:+-p"$db_pass"} --add-drop-table --skip-comments "$db_name" > DB.sql
  
  if [ $? -eq 0 ] && [ -s DB.sql ]; then
    echo "✅ Database dump created successfully as DB.sql"
    echo "   Size: $(du -h DB.sql | cut -f1)"
    echo "   Lines: $(wc -l < DB.sql)"
    return 0
  else
    echo "❌ Failed to dump database or dump is empty"
    return 1
  fi
}

# Check if DB.sql already exists
if [ -f "DB.sql" ]; then
  echo "DB.sql already exists."
  echo "Size: $(du -h DB.sql | cut -f1)"
  echo "Lines: $(wc -l < DB.sql)"
  
  read -p "Do you want to overwrite it? (y/n): " overwrite
  if [ "$overwrite" != "y" ] && [ "$overwrite" != "Y" ]; then
    echo "Exiting without overwriting the existing file."
    exit 0
  fi
fi

# Check if MySQL client is installed
if ! command_exists mysql || ! command_exists mysqldump; then
  echo "⚠️ MySQL client or mysqldump not found. Cannot generate DB.sql directly."
  echo ""
else
  # Try to dump from local development database
  echo "Would you like to dump from your local development database?"
  read -p "Enter 'y' to continue or any other key to skip: " do_dump
  
  if [ "$do_dump" = "y" ] || [ "$do_dump" = "Y" ]; then
    # Get database credentials
    read -p "Database user (default: root): " db_user
    db_user=${db_user:-root}
    
    read -sp "Database password (default: root): " db_pass
    db_pass=${db_pass:-root}
    echo ""
    
    read -p "Database name (default: sundarta_db): " db_name
    db_name=${db_name:-sundarta_db}
    
    read -p "Database host (default: localhost): " db_host
    db_host=${db_host:-localhost}
    
    read -p "Database port (default: 3306): " db_port
    db_port=${db_port:-3306}
    
    if dump_database "$db_user" "$db_pass" "$db_name" "$db_host" "$db_port"; then
      echo "✅ DB.sql generated successfully from the database."
      exit 0
    fi
  fi
fi

# If we reach here, we need to find another source for the DB.sql file
echo ""
echo "Searching for alternative DB.sql sources..."

# Check for common locations of DB dump files
POSSIBLE_SOURCES=(
  "./database/DB.sql"
  "./sql/DB.sql"
  "./setup/DB.sql"
  "./data/DB.sql"
  "./database/dump.sql"
  "./database/schema.sql"
)

for source in "${POSSIBLE_SOURCES[@]}"; do
  if [ -f "$source" ] && [ -s "$source" ]; then
    echo "Found potential source: $source"
    read -p "Use this file? (y/n): " use_source
    
    if [ "$use_source" = "y" ] || [ "$use_source" = "Y" ]; then
      cp "$source" DB.sql
      echo "✅ Copied $source to DB.sql"
      echo "   Size: $(du -h DB.sql | cut -f1)"
      echo "   Lines: $(wc -l < DB.sql)"
      exit 0
    fi
  fi
done

echo ""
echo "⚠️ No suitable DB.sql sources found automatically."
echo ""
echo "Options:"
echo "1. Specify a path to an existing SQL dump file to copy"
echo "2. Extract from a Docker container (if running)"
echo "3. Create a minimal placeholder file"
echo ""
read -p "Select an option (1-3): " option

case $option in
  1)
    read -p "Enter the path to your SQL file: " sql_path
    if [ -f "$sql_path" ]; then
      cp "$sql_path" DB.sql
      echo "✅ Copied $sql_path to DB.sql"
      echo "   Size: $(du -h DB.sql | cut -f1)"
      echo "   Lines: $(wc -l < DB.sql)"
    else
      echo "❌ File not found: $sql_path"
      exit 1
    fi
    ;;
    
  2)
    if command_exists docker; then
      # Get running containers
      echo "Finding running MySQL/MariaDB containers..."
      containers=$(docker ps --format "{{.Names}}" | grep -E 'mysql|mariadb|db')
      
      if [ -z "$containers" ]; then
        echo "❌ No running MySQL/MariaDB containers found."
        exit 1
      fi
      
      echo "Found containers:"
      echo "$containers"
      read -p "Enter container name: " container_name
      
      read -p "Database name (default: sundarta_db): " db_name
      db_name=${db_name:-sundarta_db}
      
      read -p "Database user (default: root): " db_user
      db_user=${db_user:-root}
      
      read -sp "Database password (default: root): " db_pass
      db_pass=${db_pass:-root}
      echo ""
      
      echo "Dumping database from container $container_name..."
      docker exec -i "$container_name" mysqldump -u "$db_user" ${db_pass:+-p"$db_pass"} --add-drop-table --skip-comments "$db_name" > DB.sql
      
      if [ $? -eq 0 ] && [ -s DB.sql ]; then
        echo "✅ Database dumped successfully from container"
        echo "   Size: $(du -h DB.sql | cut -f1)"
        echo "   Lines: $(wc -l < DB.sql)"
      else
        echo "❌ Failed to dump database from container"
        exit 1
      fi
    else
      echo "❌ Docker not found. Cannot extract from containers."
      exit 1
    fi
    ;;
    
  3)
    echo "Creating minimal DB.sql placeholder file..."
    cat > DB.sql << EOL
-- Placeholder DB.sql file for Sundarta application
-- This is a minimal structure to allow the application to start

-- Create the database if it doesn't exist
CREATE DATABASE IF NOT EXISTS sundarta_db;
USE sundarta_db;

-- Minimal users table
CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(255) NOT NULL,
  email VARCHAR(255) UNIQUE NOT NULL,
  phone VARCHAR(15),
  password VARCHAR(255) NOT NULL,
  avatar VARCHAR(255) DEFAULT NULL,
  is_active BOOLEAN DEFAULT TRUE,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert at least one admin user
INSERT INTO users (username, email, password, is_active)
VALUES ('admin', 'admin@sundarta.com', '\$2y\$10\$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', TRUE);
-- Default password is 'password'

-- Minimal category table
CREATE TABLE IF NOT EXISTS category (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert basic categories
INSERT INTO category (name) VALUES 
  ('Skincare'), 
  ('Haircare'), 
  ('Makeup'),
  ('Wellness'),
  ('Fragrance');
EOL
    echo "✅ Created minimal DB.sql placeholder file"
    echo "   Size: $(du -h DB.sql | cut -f1)"
    echo "   Lines: $(wc -l < DB.sql)"
    ;;
    
  *)
    echo "Invalid option selected."
    exit 1
    ;;
esac

echo ""
echo "===== DB.sql Generation Complete ====="
echo "Your DB.sql file is now ready for deployment."
echo "You can copy it to your repository or upload it to a location"
echo "where your deployment process can access it." 