# 🌸 Sundarta - Beauty & Wellness E-Commerce Site 🌿

Welcome to Sundarta, an elegant and user-friendly platform where you can explore and purchase a wide range of beauty products and book rejuvenating wellness services. Indulge yourself in the art of self-care.

## Tech Stack

<div align="center">
  <img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/html5/html5-plain-wordmark.svg" width="50" height="50"/>
  <img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/css3/css3-plain-wordmark.svg" width="50" height="50"/>
  <img src="https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/tailwindcss/tailwindcss-original.svg" width="50" height="50"/>
  <img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/javascript/javascript-plain.svg" width="50" height="50"/>
  <img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/php/php-original.svg" width="50" height="50"/>
  <img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/mysql/mysql-plain-wordmark.svg" width="50" height="50"/>
</div>


## 🚀 Quick Start

### Local Development with Docker

1. Make sure you have Docker and Docker Compose installed
2. Clone the repository
3. Copy `.env.example` to `.env` and configure your environment variables
4. Run the following commands:

```bash
# Build and start the containers
docker-compose up --build

# Install dependencies
docker-compose exec app composer install
```

The application will be available at:
- Frontend: http://localhost:8080
- Database: localhost:3306 (MySQL)

### Environment Variables

Create a `.env` file in the root directory with the following variables:

```env
APP_ENV=development
APP_TIMEZONE=UTC
DB_HOST=db
DB_USER=root
DB_PASSWORD=root
DB_NAME=sundarta
```

## 📦 Deployment

### Shared Hosting (e.g., Hostinger)

1. Upload the following directories to your hosting:
   - `public/`
   - `backend/`
   - `frontend/`
   - `vendor/`
   - `.env`

2. Update the `.htaccess` file if deploying to a subdirectory:
   ```apache
   RewriteBase /your-subdirectory/
   ```

3. Import your database using phpMyAdmin

### Docker Platforms (e.g., Render, Railway)

1. Push your code to a Git repository
2. Create a new web service
3. Configure the following:
   - Build Command: `docker build -t sundarta .`
   - Start Command: `docker-compose up`
   - Port: 8080
4. Add a MySQL add-on or configure an external database
5. Set up environment variables

## 🛠️ Project Structure

```
Sundarta/
├── docker/                  # Docker configuration
│   ├── Dockerfile
│   └── apache.conf
├── public/                  # Web root
│   ├── index.php
│   ├── .htaccess
│   └── assets/
├── backend/                 # Backend logic
│   └── api/
├── frontend/               # Frontend templates
├── vendor/                 # Composer dependencies
├── .env                    # Environment variables
├── .env.example           # Example environment variables
├── composer.json          # PHP dependencies
└── docker-compose.yml     # Docker services
```

## 🔧 Development

### Running Tests

```bash
docker-compose exec app php vendor/bin/phpunit
```

### Database Migrations

```bash
docker-compose exec app php migrations/migrate.php
```

## 📝 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## Installation

Follow these steps to set up the application locally and test it.

### 1. Clone the repository

Clone the repository to your local machine using Git:

```bash
git clone https://github.com/yourusername/sundarta.git
```

### 2. Navigate to the project directory

Move into the project directory:

```bash
cd sundarta
```

### 3. Install Dependencies

Make sure you have Composer installed. If not, you can install it by following the instructions on the [Composer website](https://getcomposer.org/download/).

Install all the PHP dependencies:

```bash
composer install
```

### 4. Set Up Environment File

Copy the `.env.example` file to `.env` (if not already present):

```bash
cp .env.example .env
```

Alternatively, create a new `.env` file in the root directory with the following content (replace `your_db_username` and `your_db_password` with your own database credentials):

```
DB_USER=your_db_username
DB_PASS=your_db_password
APP_ENV=development
APP_TIMEZONE=Asia/Kolkata
```

### 5. Import the Database Schema

Make sure MySQL is installed on your system. If you're on Windows, you can use a local MySQL server (like XAMPP or WAMP), and on Linux, you should have MySQL or MariaDB installed.

Create a new database in MySQL:

```bash
mysql -u root -p
CREATE DATABASE sundarta_db;
```

Then import the database schema:

```bash
mysql -u your_db_username -p sundarta_db < DB.sql
```

### 6. Start the PHP Built-in Server (Local Testing)

For testing the application locally, PHP provides a built-in server. Run the following command to start the server on `localhost:8000`:

```bash
php -S localhost:8000
```

You can now access the application in your browser by navigating to `http://localhost:8000`.

### 7. (Optional) Running with XAMPP or Another Local Server

If you prefer using XAMPP or a different local web server instead of PHP's built-in server, follow these steps:

1. **Copy your project folder (`sundarta`) to the `htdocs` folder of your XAMPP installation** (usually located in `C:\xampp\htdocs` for Windows or `/opt/lampp/htdocs` for Linux).
   
2. **Start XAMPP** (or your chosen local server).
   - On Windows, launch the XAMPP Control Panel and start Apache and MySQL.
   - On Linux, start Apache and MySQL via terminal or through the XAMPP manager (`sudo /opt/lampp/lampp start`).

3. **Set up the `.env` file** as described in Step 4.

4. **Create the database** as described in Step 5 (you can use phpMyAdmin on XAMPP to easily create the database if preferred).

5. **Access the application** by navigating to `http://localhost/sundarta` in your web browser.

### 8. Testing and Access

After completing the setup:

- If you're using the PHP built-in server: Open your browser and go to `http://localhost:8000`.
- If you're using XAMPP or another local server: Open your browser and go to `http://localhost/sundarta` (adjust the path based on where you placed your project folder).

---

### Additional Notes for Specific Environments:

#### Windows (without XAMPP)
- Ensure PHP is installed and available in your system's PATH.
- MySQL/MariaDB can be installed separately or as part of WAMP/XAMPP.
- For simplicity, you can install WAMP or XAMPP, which provides a pre-configured PHP + MySQL environment.

#### Linux
- PHP and MySQL/MariaDB can be installed using your package manager (e.g., `sudo apt install php mysql-server`).
- Make sure to install the necessary PHP extensions (such as `pdo_mysql`) if they're not installed by default.

## Production Deployment Guide

This guide explains how to deploy Sundarta to a production environment.

### Prerequisites

- A server with Docker and Docker Compose installed
- Domain name configured to point to your server
- SSL certificates for your domain (for HTTPS)

### Deployment Steps

1. **Clone the repository**

```bash
git clone https://github.com/yourusername/Sundarta.git
cd Sundarta
```

2. **Configure production environment**

Create a production environment file with secure credentials:

```bash
cp .env.example .env.production
```

Edit `.env.production` with your production settings:
- Set `APP_ENV=production`
- Set `APP_DEBUG=false`
- Configure database credentials
- Set `APP_URL` to your domain name
- Configure mail settings
- Set `COOKIE_SECURE=true` and `COOKIE_DOMAIN` to your domain

3. **Generate SSL certificates**

For production, you should use proper SSL certificates. You can use Let's Encrypt:

```bash
mkdir -p docker/nginx/ssl
# Using certbot for Let's Encrypt
sudo certbot certonly --webroot -w /path/to/Sundarta/public -d yourdomain.com -d www.yourdomain.com

# Copy certificates to the right location
sudo cp /etc/letsencrypt/live/yourdomain.com/fullchain.pem docker/nginx/ssl/sundarta.crt
sudo cp /etc/letsencrypt/live/yourdomain.com/privkey.pem docker/nginx/ssl/sundarta.key
sudo chown -R $USER:$USER docker/nginx/ssl
```

4. **Update Nginx configuration**

Edit `docker/nginx/conf.d/app.conf` to use your domain name.

5. **Build and deploy**

Use the deployment script to build and deploy the application:

```bash
./deploy.sh
```

The script will:
- Backup the database if there's an existing installation
- Copy production environment file
- Pull latest changes from git
- Build Docker containers
- Start the application
- Verify the deployment

6. **Verify the deployment**

Access your domain in a web browser to make sure everything is working.

You can also check the application logs:

```bash
docker-compose logs -f
```

### Production Maintenance

#### Database Backups

Create regular database backups using the Makefile command:

```bash
make backup
```

Backups are stored in the `backups/` directory.

#### Updating the Application

To update the application to the latest version:

```bash
git pull
./deploy.sh --skip-build  # Use if no dependencies have changed
```

For a full rebuild:

```bash
./deploy.sh
```

#### Security Considerations

1. Never expose the database ports to the internet
2. Keep your environment files secure
3. Regularly update Docker images and dependencies
4. Set up automated backups
5. Monitor your application with the health check endpoint

## Development Guide

For local development, use the following commands:

### Setup

```bash
# Clone the repository
git clone https://github.com/yourusername/Sundarta.git
cd Sundarta

# Setup environment
make setup

# Start development environment
make up
```

### Common Development Commands

```bash
# View container logs
make logs

# Restart containers
make restart

# Stop containers
make down

# Rebuild containers
make build
```

## Switching Between Development and Production

The Sundarta project is set up to easily switch between development and production environments using Docker Compose profiles.

### Development Mode

```bash
# Start development environment
make dev
# or
export APP_ENV=development && docker-compose up -d
```

### Production Mode

```bash
# Start production environment
make prod
# or
export APP_ENV=production && docker-compose --profile production up -d
```

## Troubleshooting

### Common Issues

1. **Database Connection Errors**
   - Check database credentials in `.env` file
   - Ensure the database container is running: `docker-compose ps`
   - Check database logs: `docker-compose logs db`

2. **Web Server Issues**
   - Check Nginx configuration in `docker/nginx/conf.d/app.conf`
   - Verify certificates are correct in `docker/nginx/ssl/`
   - Check webserver logs: `docker-compose logs webserver`

3. **Application Errors**
   - Check application logs: `docker-compose logs app`
   - Verify environment variables are set correctly
   - Check PHP error log: `docker-compose exec app cat /var/log/php_errors.log`

### Health Check

Access the health check endpoint to verify your application status:

```
https://yourdomain.com/api/health
```

This will return application health information in JSON format.

## License

[Your License Information] 