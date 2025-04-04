# Data Feed Task

This project is a Symfony based application for importing products from a CSV file into a database. 
It includes a command line interface (CLI) for running the import process.


## Features

- Import products from a CSV file.
- Validate CSV data before importing.
- Log import related messages to a dedicated log file (import.log).
- Handle errors gracefully and provide meaningful feedback.


## Requirements

- PHP 8.1 or higher
- Symfony 6.4.*
- Composer (for dependency management)
- MySQL database


## Installation

1. Extract the ZIP file:

   - Extract the contents of the ZIP file to your desired directory.

2. Install dependencies:

    composer install 

3. Set up the database:

 - Update the .env file with your database credentials:

    DATABASE_URL="mysql://db_user:db_password@127.0.0.1:3306/data_feed?serverVersion=8.4&charset=utf8mb4"

 - Run the migrations to create the database schema:

    php bin/console doctrine:migrations:migrate

4. Run the import command:

    php bin/console app:import-products /path/to/feed.csv

5. Project Structure

 - src/: Contains the application's PHP source code. 
   - Command/: Symfony commands.
   - Entity/: Doctrine entities.
   - Service/: Business logic and services.
   - Storage/: Storage implementations.
   - public/: include feed.csv files for test.
   - config/: Configuration files include (services.yaml, monolog.yaml).
   - migrations/: Database migration files.
   - tests/: Unit and functional tests.

6. Logging

 - Import-related logs are written to var/log/import.log in the dev environment.

7. Testing

 - To run the tests, use the following command:

    php bin/phpunit

8. Assumptions

 - The CSV file follows a specific format (columns: gtin, language, title, picture, description, price, stock).
