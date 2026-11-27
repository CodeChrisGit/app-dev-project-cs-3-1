# Pinoy HomeDream Finder – Backend

## Overview

The backend of **Pinoy HomeDream Finder** is a REST API built with Laravel. It manages property listings, locations, property types, property comparisons, admin authentication, and property price estimation.

The system also integrates a Python Flask machine learning service that uses a trained model to estimate property prices based on property characteristics.

## Technologies Used

- PHP
- Laravel
- MySQL / MariaDB
- Laravel Sanctum
- Python
- Flask
- Scikit-learn
- Pandas
- NumPy
- Joblib

## Project Structure

```text
backend/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   └── Resources/
│   ├── Models/
│   └── Providers/
├── bootstrap/
├── config/
├── database/
│   ├── migrations/
│   └── seeders/
├── routes/
│   └── api.php
├── storage/
├── .env.example
└── composer.json

ml-service/
├── models/
│   └── phil_price_model.pkl
├── app.py
├── requirements.txt
└── README.md
```

## Requirements

Before running the backend, install:

- PHP
- Composer
- MySQL or MariaDB (XAMPP)
- Python
- pip

## Installation and Setup

### 1. Clone the repository

```bash
git clone https://github.com/CodeChrisGit/app-dev-project-cs-3-1.git
cd app-dev-project-cs-3-1
```

### 2. Install Laravel dependencies

```bash
cd backend
composer install
```

### 3. Configure environment

Copy `.env.example` to `.env` and configure the database:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pinoy_dreamhome
DB_USERNAME=root
DB_PASSWORD=
```

Add the ML service URL:

```env
ML_SERVICE_URL=http://127.0.0.1:5000
```

Generate the application key:

```bash
php artisan key:generate
```

### 4. Create the database

Create a MySQL database named:

```text
pinoy_dreamhome
```

Make sure MySQL is running before proceeding.

### 5. Run migrations and seeders

```bash
php artisan migrate --seed
```

### 6. Create the storage link

```bash
php artisan storage:link
```

### 7. Start the Laravel API

```bash
php artisan serve
```

The backend API will be available at:

```text
http://127.0.0.1:8000
```

## Machine Learning Service Setup

The price estimator uses an existing trained machine learning model.

### 1. Open the ML service folder

From the project root:

```bash
cd ml-service
```

### 2. Create a virtual environment

```bash
python -m venv .venv
```

### 3. Activate the environment (Windows PowerShell)

```powershell
.\.venv\Scripts\Activate.ps1
```

### 4. Install dependencies

```bash
pip install -r requirements.txt
```

### 5. Start the Flask service

```bash
python app.py
```

The ML service will run at:

```text
http://127.0.0.1:5000
```

Health check:

```text
GET http://127.0.0.1:5000/health
```

**Important:** Keep both the Laravel API and Flask ML service running when using the property price estimator.

## API Endpoints

Base URL:

```text
http://127.0.0.1:8000/api
```

### Public Endpoints

| Method | Endpoint | Description |
|---|---|---|
| GET | `/properties` | Retrieve available properties |
| GET | `/properties/{id}` | Retrieve property details |
| GET | `/properties/compare?ids=42,44,45` | Compare properties |
| GET | `/locations` | Retrieve available locations |
| GET | `/property-types` | Retrieve property types |
| POST | `/estimate` | Estimate property price |

### Admin Endpoints

| Method | Endpoint | Description |
|---|---|---|
| POST | `/admin/login` | Admin login |
| GET | `/admin/me` | Retrieve authenticated admin |
| GET | `/admin/properties` | Retrieve admin property listings |
| POST | `/admin/properties` | Create a property |
| GET | `/admin/properties/{id}` | Retrieve a property |
| PUT | `/admin/properties/{id}` | Update a property |
| DELETE | `/admin/properties/{id}` | Delete a property |

Admin endpoints require a valid Laravel Sanctum Bearer token, except the login endpoint.

## Property Price Estimator

The estimator accepts four property inputs:

- Bedrooms
- Bathrooms
- Floor Area
- Land Area

Example request:

```http
POST /api/estimate
Content-Type: application/json
```

Request body:

```json
{
  "bedrooms": 3,
  "bathrooms": 2,
  "floor_area": 90,
  "land_area": 100
}
```

Example response:

```json
{
  "estimated_price": 6135000
}
```

The Laravel API sends the validated inputs to the Flask service. The Flask service uses the trained `phil_price_model.pkl` model to generate the estimated price.

**Note:** Price estimates are for guidance only and should not be treated as professional property appraisals.

## Admin Login

Seeded admin account for local development:

```text
Email: admin@dreamhome.test
Password: password
```

Login endpoint:

```text
POST /api/admin/login
```

Use the returned token as a Bearer token when accessing protected admin endpoints.

Do not use development credentials in production.

## Testing

The API can be tested using Postman.

Recommended tests:

- Retrieve property listings.
- Retrieve property details.
- Retrieve locations and property types.
- Compare multiple properties.
- Log in as admin.
- Test admin property CRUD operations.
- Submit property details to the price estimator.

Make sure MySQL, Laravel, and Flask are running before testing the complete system.

## Notes

- Do not commit `.env` files or other sensitive credentials.
- The trained model is stored in `ml-service/models/`.
- The Laravel backend communicates with the Flask service through `ML_SERVICE_URL`.
- Keep the Laravel API and Flask service running simultaneously for price estimation.
