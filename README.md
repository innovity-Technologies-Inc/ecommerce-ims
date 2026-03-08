# smart-ecom

**smart-ecom** is a high-performance e-commerce platform built with Laravel 12. Follow the steps below to set up and
install the project on your local environment.

## Installation Guide

### 1. Prerequisites

Ensure you have the following installed:

- PHP 8.3 or higher
- Composer
- MySQL or PostgreSQL

### 2. Database Setup

Create a new database for the project (e.g., `smart_ecom`).

### 3. Environment Configuration

Copy the `.env.example` file to create your `.env` file:

```bash
cp .env.example .env
```

Open the `.env` file and update the following database connection details with your own username and password:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=smart_ecom
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### 4. Install Dependencies

Run the following commands to install and update the project's PHP dependencies:

```bash
composer install
composer update
```

### 5. Application Optimization

Clear the configuration cache and optimize the application:

```bash
php artisan config:clear
php artisan optimize
```

### 6. Database Migration
Run the database migrations to create the necessary tables:
```bash
php artisan migrate
```

### 7. Database Seeding (Optional)
If you want to populate the database with sample data (products, categories, settings, etc.):
```bash
php artisan db:seed
```

### 8. Default Credentials
Use the following credentials to access the admin panel:
- **Email:** admin@example.com
- **Password:** 12345678

### 8. Storage Symlink

If there is an existing `storage` folder inside the `public` directory, delete it first. Then, run the following command
to create the symbolic link:

```bash
php artisan storage:link
```

## Running the Application

Once the installation is complete, you can start the development server:

```bash
php artisan serve
```

The application will be accessible at `http://127.0.0.1:8000`.
