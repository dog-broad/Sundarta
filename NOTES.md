# Setup Flow I followed

- Had to move the project into XAMPP htdocs folder to run the project on localhost
- Had to install composer to install the dependencies
    - Used the following settings:
        - Project name: `sundarta/beauty-wellness`
        - Project description: Beauty and wellness e-commerce site
        - Project type: `project`
        - Required dependencies: `monolog/monolog` (^3.8) and `vlucas/phpdotenv` (^5.6) (MonoLog for logging and PHP dotenv for environment variables, I downloaded monolog just for te sake of it, even I'm not sure what it is)
        - Project license: `MIT`
        - Minimum stability: `stable`
- Had to create a `.env` file to store the database credentials

- Filled in the database credentials in the `.env` file
- Created the `backend/config/db.php` file to connect to the database

- Wrote the `index.php` file to handle the routing

- filled in `frontend/partials/header.php` and `footer.php` files
- Created a home page `frontend/pages/home.php`, this page only loads the header and footer partials


