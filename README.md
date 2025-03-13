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

## License

This project is licensed under the MIT License. 