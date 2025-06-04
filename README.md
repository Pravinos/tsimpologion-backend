# Tsimpologion (MVP)
# Table of Contents

- [Description](#description)
- [Prerequisites](#prerequisites)
- [Installation](#installation)
- [Features](#features)
- [API Endpoints](#api-endpoints)
- [Database Schema](#database-schema)
- [Policies & Authorization](#policies--authorization)
- [Email Verification System](#email-verification-system)
- [Image Management System](#image-management-system)
- [API Endpoints Summary](#api-endpoints-summary)
- [Usage](#usage)
- [Future Enhancements](#future-enhancements)
- [License](#license)
- [Contact](#contact)

## Description
This is the backend for a mobile app designed to help users discover authentic Greek food spots, including taverns, brunch cafes, pizzerias, and more. Built with Laravel, it provides a simple RESTful API to serve food spot data such as names, addresses, descriptions, ratings, and Google Maps links. Initially using a static dataset, future enhancements include dynamic data sources and extended filtering capabilities.

## Prerequisites
To run this backend locally or deploy it, ensure you have the following installed:
- PHP (>= 8.4)
- Composer
- MySQL (or SQLite for simplicity)
- Git
- A code editor (e.g., Visual Studio Code)
- Postman (or a similar API testing tool)

## Installation

### Clone the Repository
- git clone https://github.com/Pravinos/tsimpologion-backend.git
- cd tsimpologion-backend

### Install Dependencies
- composer install

### Set Up Environment
- Copy the example file:
  - cp .env.example .env
- Edit the .env file with your database credentials:
  - DB_CONNECTION=sqlite
  - DB_DATABASE=tsimpologion_db
- Set up mail configuration for email verification:
  - MAIL_MAILER=smtp
  - MAIL_HOST=your_mail_host
  - MAIL_PORT=your_mail_port
  - MAIL_USERNAME=your_mail_username
  - MAIL_PASSWORD=your_mail_password
  - MAIL_FROM_ADDRESS=your_from_address
  - MAIL_FROM_NAME="${APP_NAME}"
- Generate an application key:
  - php artisan key:generate

### Run Migrations
- Ensure the database (e.g., tsimpologion_db) exists.
- Run migrations:
  - php artisan migrate

### Seed the Database
- Populate sample data:
  - php artisan db:seed

### Start the Server
- Start Laravel's built-in server:
  - php artisan serve --host=0.0.0.0 --port=8000
- The API will be available at http://localhost:8000.

## Features

### Authentication & Authorization

**User Authentication**: Users can register, log in, and log out using Laravel Sanctum, which supports both session-based and token-based authentication.

**Email Verification**: New users receive a verification email and must verify their email address before accessing the application.

**API Token Management**: Sanctum is configured for token expiration and prefixing to enhance security.

**User Policies**: User-related operations are secured via policies (see UserPolicy.php). For example, only administrators or the user themselves can update or delete a user record.

**Resource Policies**: Custom policies (e.g., FoodSpotPolicy in FoodSpotPolicy.php) control resource access, allowing administrators and food spot owners to manage food spots.

**Authorization Mapping**: The AuthServiceProvider maps models to corresponding policies for organized access control.

### Food Spot Model & API Enhancements
- **New Fields**: Food spots now support `phone`, `business_hours`, `social_links`, and `price_range` fields. These are available in the database, API, and seeders.
- **Restore & Force Delete**: API supports restoring and force-deleting food spots.
- **Nominatim Integration**: Endpoints for searching and creating food spots from the Nominatim API (OpenStreetMap geocoding).

### Review Model & API Enhancements
- **Review Images**: Reviews now support image attachments, managed via the `images` JSON field.
- **Review Moderation**: Admins can approve/disapprove reviews using the `is_approved` field and the `/moderate` endpoint.
- **Soft Deletes**: Reviews support soft deletion.
- **One Review Per User**: Enforced at the database level (unique constraint).

### User Model & API Enhancements
- **Role Field**: Users have a `role` field (`admin`, `spot_owner`, `foodie`).
- **User Reviews Endpoint**: You can now fetch all reviews by a user via `/api/users/{user}/reviews`.

### Image Management
- **HasImages Trait**: All main models (User, FoodSpot, Review) use a shared trait for image management.
- **Fine-grained Permissions**: Each model implements `userCanManageImages()` for image authorization.

### Policies & Authorization
- **ReviewPolicy**: Now includes `moderate` permission for admins.
- **FoodSpotPolicy**: Now includes `restore` and `forceDelete` permissions for admins.
- **UserPolicy**: Updated for new role logic.

### Seeder & Migration Updates
- **Seeders**: FoodSpotSeeder and ReviewSeeder now populate new fields.
- **Migrations**: Database tables for food spots and reviews include new fields and constraints.

### Role-Based Access Control
- **Administrators**: Have complete access to manage all users, food spots, and moderate reviews.
- **Spot Owners**: Can perform specific actions on the food spots they own, including managing images and updating their spots.
- **General Users**: Can view public listings, manage their own profiles, and leave one review per food spot.

### Review & Rating System
- **User Reviews**: Authenticated users can post, edit, and delete their reviews for food spots. Each user can only leave one review per food spot (enforced at the database level).
- **Rating System**: Users can rate food spots from 1-5 stars. Food spot ratings are automatically updated when reviews are created, updated, or deleted.
- **Moderation**: Admin users can moderate reviews by approving or disapproving them using the `is_approved` field and the `/moderate` endpoint. Only approved reviews are shown publicly.
- **Review Images**: Users can attach images to their reviews. Only review authors or admins can manage review images.
- **Average Ratings**: Food spots display an aggregated average rating from all approved reviews.

### Image Management
- **Image Upload**: Users can upload images for food spots and user profiles.
- **Multiple Image Support**: Each model can have multiple associated images.
- **Image Deletion**: Authorized users can delete images they've uploaded.
- **Public Image Access**: Anyone can view images associated with public resources.
- **Permission Management**: Images can only be managed by the resource owner or administrators.

## API Endpoints

### Authentication Endpoints
- **POST /api/register** – Register a new user and trigger verification email
- **POST /api/login** – Log in and retrieve an authentication token
- **POST /api/logout** – Invalidate the current token (requires authentication)
- **GET /api/email/verify/{id}/{hash}** – Verify user email with verification link
- **POST /api/email/verification-notification** – Resend verification email

### User Endpoints
- **GET /api/users** – Retrieve a list of users (admin sees all; non-admins see limited results)
- **GET /api/users/{id}** – Retrieve details of a specific user
- **PUT/PATCH /api/users/{id}** – Update a specific user (admin or self-update)
- **DELETE /api/users/{id}** – Delete a user (admin or self-delete)

### Food Spot Endpoints

#### Public
- **GET /api/food-spots** – Retrieve a list of all food spots
- **GET /api/food-spots/{food_spot}/rating** – Get the average rating for a food spot

#### Protected (requires authentication)
- **POST /api/food-spots** – Create a new food spot
- **GET /api/food-spots/{id}** – Retrieve detailed food spot information
- **PUT/PATCH /api/food-spots/{id}** – Update a food spot (admins and spot owners only)
- **DELETE /api/food-spots/{id}** – Soft delete a food spot
- **PUT /api/food-spots/{id}/restore** – Restore a soft-deleted food spot
- **DELETE /api/food-spots/{id}/force** – Permanently delete a food spot

### Review Endpoints
- **GET /api/food-spots/{food_spot}/reviews** – Get all reviews for a food spot
- **GET /api/food-spots/{food_spot}/reviews/{review}** – Get details of a specific review
- **POST /api/food-spots/{food_spot}/reviews** – Create a new review (auth required)
- **PUT /api/food-spots/{food_spot}/reviews/{review}** – Update a review (auth required, owner only)
- **DELETE /api/food-spots/{food_spot}/reviews/{review}** – Delete a review (auth required, owner or admin)
- **PUT /api/food-spots/{food_spot}/reviews/{review}/moderate** – Moderate a review (admin only)

### Image Management Endpoints
- **GET /api/images/{model_type}/{id}** – View all images for a specific resource
- **GET /api/images/{model_type}/{id}/{image_id}** – View a specific image
- **POST /api/images/{model_type}/{id}** – Upload images to a resource (auth required)
- **DELETE /api/images/{model_type}/{id}/{image_id}** – Delete an image (auth required, owner or admin)

## Database Schema

### Food Spots Table
| Column       | Type         | Description                    | Example Value                  |
|--------------|--------------|--------------------------------|-------------------------------|
| id           | BIGINT (PK)  | Auto-incrementing primary key  | 1                             |
| name         | VARCHAR(255) | Name of the food spot          | "Taverna To Koutouki"         |
| category     | VARCHAR(255) | Category of the food spot      | Taverna                       |
| city         | VARCHAR(255) | City of the spot               | Athens                        |
| address      | VARCHAR(255) | Physical address               | "Leof. Mesogeion 123, Athens" |
| description  | TEXT         | Short description of the spot  | "Cozy spot for grilled meats" |
| info_link    | VARCHAR(255) | Google Maps share link         | "https://maps.app.goo.gl/xyz" |
| rating       | FLOAT        | Calculated average rating      | 4.7                           |
| owner_id     | BIGINT       | Foreign key to user who owns the spot | 2                      |
| images       | JSON         | Stored images metadata         | JSON array of image objects   |
| phone        | VARCHAR(30)  | Contact phone number           | "+30 210 1234567"             |
| business_hours | JSON       | Opening hours                  | {"mon-fri":"12:00-23:00"}    |
| social_links | JSON         | Social media links             | {"facebook":"..."}           |
| price_range  | VARCHAR(10)  | Price range ($, $$, $$$, $$$$) | "$$"                          |
| created_at   | TIMESTAMP    | Record creation time           | 2025-03-25 10:00:00           |
| updated_at   | TIMESTAMP    | Record update time             | 2025-03-25 10:00:00           |
| deleted_at   | TIMESTAMP    | Soft delete timestamp          | NULL                          |

### Users Table
| Column       | Type         | Description                    | Example Value                  |
|--------------|--------------|--------------------------------|-------------------------------|
| id           | BIGINT (PK)  | Auto-incrementing primary key  | 1                             |
| name         | VARCHAR(255) | User's name                    | "Admin User"                  |
| email        | VARCHAR(255) | User's email (unique)          | "admin@example.com"           |
| password     | VARCHAR(255) | Hashed password                | "$2y$10$92IXUNpkjO0..."      |
| role         | VARCHAR(255) | User role                      | "admin"                       |
| images       | JSON         | Stored profile images          | JSON array of image objects   |
| email_verified_at | TIMESTAMP | Email verification timestamp | "2025-03-25 10:00:00"        |
| created_at   | TIMESTAMP    | Record creation time           | 2025-03-25 10:00:00           |
| updated_at   | TIMESTAMP    | Record update time             | 2025-03-25 10:00:00           |

### Reviews Table
| Column       | Type         | Description                    | Example Value                  |
|--------------|--------------|--------------------------------|-------------------------------|
| id           | BIGINT (PK)  | Auto-incrementing primary key  | 1                             |
| food_spot_id | BIGINT (FK)  | Foreign key to food spot       | 1                             |
| user_id      | BIGINT (FK)  | Foreign key to user            | 3                             |
| comment      | TEXT         | Review comment                 | "Great food and atmosphere!"  |
| rating       | INTEGER      | User rating (1-5)              | 5                             |
| is_approved  | BOOLEAN      | Moderation status              | true                          |
| images       | JSON         | Images attached to review      | JSON array of image objects   |
| created_at   | TIMESTAMP    | Record creation time           | 2025-03-25 10:00:00           |
| updated_at   | TIMESTAMP    | Record update time             | 2025-03-25 10:00:00           |
| deleted_at   | TIMESTAMP    | Soft delete timestamp          | NULL                          |

**Unique Constraint:** Each user can only leave one review per food spot.

## Policies & Authorization
- **UserPolicy**: Controls actions for viewing, creating, updating, and deleting user resources. Users can update or delete their own data or, if they are administrators, any user resource.
- **FoodSpotPolicy**: Governs access to food spot actions. Administrators and spot owners have extended permissions while public users can only view food spot information.
- **ReviewPolicy**: Controls who can create, update, delete, and moderate reviews. Users can only edit their own reviews, while admins can moderate and delete any review.
- **AuthServiceProvider**: Maps models to policies, ensuring every request is authorized according to defined rules.

## Email Verification System
- **Automatic Verification**: Upon registration, a verification email is automatically sent to new users.
- **Secure Tokens**: Uses secure, time-limited tokens for email verification.
- **Re-sending Option**: Users can request a new verification email if needed.
- **Required Verification**: Email verification is required before users can fully access the application.
- **Admin Override**: Admin accounts created via seeders are automatically verified.

## Image Management System
- **Multi-Model Support**: The image system works with Users, FoodSpots, and Reviews.
- **JSON Storage**: Images are stored as JSON metadata in the respective model tables.
- **Secure Access Control**: Each model implements userCanManageImages() to control permissions.
- **Efficient Storage**: Images are stored using unique identifiers to prevent collisions.
- **Public Access**: Public images can be viewed without authentication.

- **HasImages Trait**: All main models (User, FoodSpot, Review) use a shared trait for image management.
- **Fine-grained Permissions**: Each model implements `userCanManageImages()` for image authorization.

## API Endpoints Summary

### Authentication
| Method | Endpoint         | Description                   |
|--------|------------------|-------------------------------|
| POST   | /api/register    | Register a new user and trigger verification email |
| POST   | /api/login       | Log in and retrieve an auth token |
| POST   | /api/logout      | Log out (invalidate token)    |
| GET    | /api/email/verify/{id}/{hash} | Verify user email |
| POST   | /api/email/verification-notification | Resend verification email |

### User Resources
| Method | Endpoint         | Description                   |
|--------|------------------|-------------------------------|
| GET    | /api/users       | Retrieve a list of users (access controlled by roles) |
| GET    | /api/users/{id}  | Retrieve user details         |
| PUT    | /api/users/{id}  | Update user information (admin or self-update only) |
| DELETE | /api/users/{id}  | Delete a user record (admin or self-delete) |

### Food Spot Resources
| Method   | Endpoint                  | Description                   |
|----------|---------------------------|-------------------------------|
| GET      | /api/food-spots           | Public listing of all food spots |
| POST     | /api/food-spots           | Create a new food spot (protected, authentication required) |
| GET      | /api/food-spots/{id}      | Retrieve detailed food spot information |
| PUT/PATCH| /api/food-spots/{id}      | Update a food spot (admins and spot owners only) |
| DELETE   | /api/food-spots/{id}      | Soft delete a food spot (protected, authentication required) |
| PUT      | /api/food-spots/{id}/restore | Restore a soft-deleted food spot (protected, authentication required) |
| DELETE   | /api/food-spots/{id}/force | Permanently delete a food spot (protected, authentication required) |
| GET      | /api/food-spots/{id}/rating | Get average rating for a food spot |

### Review Resources
| Method | Endpoint                           | Description                   |
|--------|------------------------------------|-------------------------------|
| GET    | /api/food-spots/{id}/reviews       | List all reviews for a food spot |
| GET    | /api/food-spots/{id}/reviews/{review_id} | Get a specific review   |
| POST   | /api/food-spots/{id}/reviews       | Add a review to a food spot (authenticated) |
| PUT    | /api/food-spots/{id}/reviews/{review_id} | Update a review (owner only) |
| DELETE | /api/food-spots/{id}/reviews/{review_id} | Delete a review (owner or admin) |
| PUT    | /api/food-spots/{id}/reviews/{review_id}/moderate | Moderate a review (admin only) |

### Image Resources
| Method | Endpoint                           | Description                   |
|--------|------------------------------------|-------------------------------|
| GET    | /api/images/{model_type}/{id}      | Get all images for a resource |
| GET    | /api/images/{model_type}/{id}/{image_id} | Get a specific image   |
| POST   | /api/images/{model_type}/{id}      | Upload images (protected)     |
| DELETE | /api/images/{model_type}/{id}/{image_id} | Delete an image (protected) |

## Usage

### Local Testing
- After starting the server with php artisan serve, use Postman or a browser to test endpoints, e.g., http://localhost:8000/api/food-spots.

### Email Testing
- For testing email verification locally, use Mailtrap or configure the mail driver to 'log' in your .env file.

### Data Updates
- For this MVP, data is static. To update food spot data, edit FoodSpotSeeder.php and run:
  - php artisan db:seed --class=FoodSpotSeeder

## Future Enhancements
- Expand user authentication (e.g., adding OAuth support).
- Integrate dynamic data sources (e.g., Google Places API).
- Implement filters for food spots (e.g., by cuisine type or location).
- Enhance error handling and logging mechanisms.
- Add image resizing and optimization features.

## License
This project is for portfolio purposes and is not currently licensed for public distribution.

## Contact
Built by Thomas Pravinos.  
Reach out at tpravinos99@gmail.com or visit [GitHub](https://github.com/Pravinos).
