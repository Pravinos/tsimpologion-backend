# Tsimpologion (MVP)

## Table of Contents
- [Description](#description)
- [Prerequisites](#prerequisites)
- [Installation](#installation)
- [Features](#features)
- [API Endpoints](#api-endpoints)
- [Database Schema](#database-schema)
- [Usage](#usage)
- [License](#license)
- [Contact](#contact)

## Description
Tsimpologion is a Laravel backend for a mobile app that helps users discover authentic Greek food spots. It provides a RESTful API for food spot data, user management, reviews, images, and more.

## Prerequisites
- PHP (>= 8.4)
- Composer
- MySQL or SQLite
- Git
- Code editor (e.g., VS Code)
- Postman or similar API tool

## Installation
1. Clone the repository and install dependencies:
   - `git clone https://github.com/Pravinos/tsimpologion-backend.git`
   - `cd tsimpologion-backend`
   - `composer install`
2. Copy `.env.example` to `.env` and configure your environment (DB, mail, etc.).
3. Generate application key: `php artisan key:generate`
4. Run migrations: `php artisan migrate`
5. Seed the database: `php artisan db:seed`
6. Start the server: `php artisan serve --host=0.0.0.0 --port=8000`

## Features
- **Authentication & Authorization**: Register, login, logout, email verification, and role-based access (admin, spot owner, foodie) using Laravel Sanctum.
- **User Management**: CRUD for users, user roles, and profile images.
- **Food Spot Management**: CRUD for food spots, including restore/force delete, owner assignment, and extra fields (phone, business hours, social links, price range).
- **Review System**: Users can leave one review per food spot, with rating, images, moderation (admin approval), soft deletes, and likes.
- **Favourites**: Users can favourite/unfavourite food spots and view their favourites.
- **Image Management**: Upload, view, and delete images for users, food spots, and reviews. Images are stored using Supabase Storage.
- **Nominatim Integration**: Search and create food spots using OpenStreetMap (Nominatim) data.
- **Policies**: Fine-grained permissions for users, food spots, reviews, and images.

## API Endpoints

### Authentication
- `POST /api/register` – Register a new user
- `POST /api/login` – Login and get token
- `POST /api/logout` – Logout (auth required)
- `GET /api/email/verify/{id}/{hash}` – Verify email
- `POST /api/email/verification-notification` – Resend verification email
- `GET /api/user` – Get current authenticated user

### Users
- `GET /api/users` – List users
- `GET /api/users/{id}` – User details
- `PUT/PATCH /api/users/{id}` – Update user
- `DELETE /api/users/{id}` – Delete user
- `GET /api/users/{user}/reviews` – User's reviews
- `GET /api/users/{user}/food-spots` – User's food spots

### Food Spots
- `GET /api/food-spots` – List all food spots
- `GET /api/food-spots/{food_spot}/rating` – Food spot average rating
- `POST /api/food-spots` – Create food spot
- `GET /api/food-spots/{id}` – Food spot details
- `PUT/PATCH /api/food-spots/{id}` – Update food spot
- `DELETE /api/food-spots/{id}` – Soft delete food spot
- `PUT /api/food-spots/{id}/restore` – Restore food spot
- `DELETE /api/food-spots/{id}/force` – Permanently delete food spot
- `POST /api/food-spots/from-nominatim` – Create from Nominatim

### Nominatim
- `GET|POST /api/nominatim/search` – Search food spots (OpenStreetMap)

### Reviews
- `GET /api/food-spots/{food_spot}/reviews` – List reviews for a food spot
- `POST /api/food-spots/{food_spot}/reviews` – Add review
- `PUT /api/food-spots/{food_spot}/reviews/{review}` – Update review
- `DELETE /api/food-spots/{food_spot}/reviews/{review}` – Delete review
- `PUT /api/food-spots/{food_spot}/reviews/{review}/moderate` – Moderate review (admin)
- `POST /api/reviews/{review}/like` – Like/unlike review
- `GET /api/reviews/{review}/like` – Check if liked
- `GET /api/reviews/{review}/likes/users` – Users who liked
- `POST /api/reviews/likes/bulk-check` – Bulk like check

### Favourites
- `GET /api/favourites` – List favourites
- `POST /api/food-spots/{foodSpotId}/favourite` – Add favourite
- `DELETE /api/food-spots/{foodSpotId}/favourite` – Remove favourite

### Images
- `POST /api/images/{modelType}/{id}` – Upload images
- `GET /api/images/{model_type}/{id}` – List images
- `GET /api/images/{model_type}/{id}/{image_id}` – Get image
- `DELETE /api/images/{model_type}/{id}/{image_id}` – Delete image

## Database Schema

### Food Spots
| Column         | Type         | Description                  |
| -------------- | ------------ | ---------------------------- |
| id             | BIGINT (PK)  | Primary key                  |
| name           | VARCHAR(255) | Name                         |
| category       | VARCHAR(255) | Category                     |
| city           | VARCHAR(255) | City                         |
| address        | VARCHAR(255) | Address                      |
| description    | TEXT         | Description                  |
| info_link      | VARCHAR(255) | Google Maps link             |
| rating         | FLOAT        | Average rating               |
| owner_id       | BIGINT       | Owner user id                |
| images         | JSON         | Images metadata              |
| phone          | VARCHAR(30)  | Phone number                 |
| business_hours | JSON         | Opening hours                |
| social_links   | JSON         | Social links                 |
| price_range    | VARCHAR(10)  | Price range                  |
| created_at     | TIMESTAMP    | Created at                   |
| updated_at     | TIMESTAMP    | Updated at                   |
| deleted_at     | TIMESTAMP    | Soft delete                  |

### Users
| Column             | Type         | Description                  |
| ------------------ | ------------ | ---------------------------- |
| id                 | BIGINT (PK)  | Primary key                  |
| name               | VARCHAR(255) | Name                         |
| email              | VARCHAR(255) | Email (unique)               |
| password           | VARCHAR(255) | Hashed password              |
| role               | VARCHAR(255) | User role                    |
| images             | JSON         | Profile images               |
| email_verified_at  | TIMESTAMP    | Email verified at            |
| created_at         | TIMESTAMP    | Created at                   |
| updated_at         | TIMESTAMP    | Updated at                   |

### Reviews
| Column       | Type         | Description                  |
| ------------ | ------------ | ---------------------------- |
| id           | BIGINT (PK)  | Primary key                  |
| food_spot_id | BIGINT (FK)  | Food spot id                 |
| user_id      | BIGINT (FK)  | User id                      |
| comment      | TEXT         | Review comment               |
| rating       | INTEGER      | Rating (1-5)                 |
| is_approved  | BOOLEAN      | Moderation status            |
| images       | JSON         | Review images                |
| created_at   | TIMESTAMP    | Created at                   |
| updated_at   | TIMESTAMP    | Updated at                   |
| deleted_at   | TIMESTAMP    | Soft delete                  |

### Review Likes
| Column    | Type         | Description                  |
| --------- | ------------ | ---------------------------- |
| id        | BIGINT (PK)  | Primary key                  |
| user_id   | BIGINT (FK)  | User who liked               |
| review_id | BIGINT (FK)  | Review id                    |
| created_at| TIMESTAMP    | Created at                   |
| updated_at| TIMESTAMP    | Updated at                   |

### Favourites
| Column        | Type         | Description                  |
| ------------- | ------------ | ---------------------------- |
| id            | BIGINT (PK)  | Primary key                  |
| user_id       | BIGINT (FK)  | User id                      |
| food_spot_id  | BIGINT (FK)  | Food spot id                 |
| created_at    | TIMESTAMP    | Created at                   |
| updated_at    | TIMESTAMP    | Updated at                   |

## Usage
- Start the server and use Postman or a browser to test endpoints (e.g., `http://localhost:8000/api/food-spots`).
- For email testing, use Mailtrap or set mail driver to 'log' in `.env`.
- To update food spot data, edit `FoodSpotSeeder.php` and run: `php artisan db:seed --class=FoodSpotSeeder`.

## License

This project is provided for **portfolio and demonstration purposes only**.

All rights are reserved.  
You may **view the source code**, but you are **not permitted** to copy, modify, reuse, or distribute any part of it without explicit permission from the author.

For licensing inquiries, contact [tpravinos99@gmail.com](mailto:tpravinos99@gmail.com).

## Contact
Built by Thomas Pravinos.  
Contact: tpravinos99@gmail.com or [GitHub](https://github.com/Pravinos).
