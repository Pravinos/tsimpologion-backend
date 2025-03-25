# Tsimpologion (MVP)

## Description

This is the backend for a mobile app designed to help users discover authentic Greek food spots, including taverns, brunch cafes, pizza joints, and more. Built with Laravel, it provides a simple RESTful API to serve food spot data, including names, addresses, descriptions, ratings, and Google Maps links. This MVP focuses on a static dataset, manually curated for simplicity, with plans to expand functionality later.

## Prerequisites

To run this backend locally or deploy it, ensure you have the following installed:

- PHP (>= 8.2): Laravel requires a modern PHP version.
- Composer: Dependency manager for PHP (install from https://getcomposer.org/).
- MySQL: Database for storing food spot data (or use SQLite for simplicity).
- Git: For cloning the repository.
- A Code Editor: e.g., VS Code, PhpStorm.
- Optional: A tool like Postman for testing API endpoints.

## Installation

1. Clone the Repository
git clone https://github.com/yourusername/greek-food-trails-backend.git
cd greek-food-trails-backend
2. Install Dependencies
composer install
3. Set Up Environment
    - Copy the example .env file:
    cp .env.example .env
    - Edit .env with your database credentials:
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=tsimpologion_db
    DB_USERNAME=your_username
    DB_PASSWORD=your_password
    - Generate an app key:
    php artisan key:generate
4. Run Migrations
    - Create the database in MySQL (tsimpologion_db) if not already done.
    - Run migrations to set up the schema:
    php artisan migrate
5. Seed the Database
    - Populate with sample data:
    php artisan db:seed --class=FoodSpotSeeder
6. Start the Server
    - Run locally:
    php artisan serve
    - The API will be available at [http://localhost:8000](http://localhost:8000/).

## Database Schema

The backend uses a single table, food_spots, to store food spot data. Below is the schema:

| Column | Type | Description | Example Value |
| --- | --- | --- | --- |
| id | BIGINT (PK) | Auto-incrementing primary key | 1 |
| name | VARCHAR(255) | Name of the food spot | "Taverna To Koutouki" |
| address | VARCHAR(255) | Physical address | "Leof. Mesogeion 123, Athens" |
| description | TEXT | Short description of the spot | "Cozy spot for grilled meats" |
| info_link | VARCHAR(255) | Google Maps share link | "https://maps.app.goo.gl/xyz" |
| rating | FLOAT | Optional user rating (0-5) | 4.7 |
| created_at | TIMESTAMP | Record creation time | 2025-03-25 10:00:00 |
| updated_at | TIMESTAMP | Record update time | 2025-03-25 10:00:00 |
| category | VARCHAR(255) | Category of the food spot | Pizzeria |

### Migration File

Located at database/migrations/2025_03_25_000000_create_food_spots_table.php.

## API Endpoints

The API is prefixed with /api and returns JSON responses. Below are the available endpoints for the MVP:

### 1. Get All Food Spots

- Endpoint: GET /api/food-spots
- Description: Retrieves a list of all food spots.

### 2. Get a Single Food Spot

- Endpoint: GET /api/food-spots/{id}
- Description: Retrieves details of a specific food spot by ID.
- Example: GET /api/food-spots/1

## Usage

- Local Testing: After starting the server (php artisan serve), use Postman or a browser to hit http://localhost:8000/api/food-spots.
- Data Updates: For the MVP, data is static. Edit database/seeders/FoodSpotSeeder.php and re-run php artisan db:seed to update.

## Future Enhancements

- Add user authentication and reviews.
- Integrate dynamic data sources (e.g., Google Places API).
- Expand to include filters (e.g., by food type or location).

## License

This project is for portfolio purposes and not currently licensed for public distribution.

## Contact

Built by Thomas Pravinos. Reach me at [tpravinos99@gmail.com](mailto:tpravinos99@gmail.com) or [GitHub](https://github.com/Pravinos).