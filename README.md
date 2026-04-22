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

## Social Authentication Setup

To enable "Login with Google" or "Login with Facebook", follow these configuration steps:

### 1. Facebook OAuth (Meta Developer Portal)
1.  Go to the [Meta for Developers](https://developers.facebook.com/) portal and log in.
2.  Click **"My Apps"** and then **"Create App"**.
3.  Select **"Authenticate and request data from users with Facebook Login"** (Consumer type).
4.  Once created, go to **"Use cases"** and click **"Edit"** on the **"Authentication and account creation"** card.
5.  Under **"Facebook Login" -> "Settings"**, find **"Valid OAuth Redirect URIs"** and add:
    `https://yourdomain.com/auth/facebook/callback`
    *(Note: For local development, use `http://localhost:8000/auth/facebook/callback`)*.
6.  Navigate to **"App settings" -> "Basic"** to find your **App ID** and **App Secret**.
7.  Update your `.env` file:
    ```env
    FACEBOOK_CLIENT_ID=your_app_id
    FACEBOOK_CLIENT_SECRET=your_app_secret
    FACEBOOK_REDIRECT_URL="https://yourdomain.com/auth/facebook/callback"
    ```
8.  Switch the app to **"Live"** mode in the top navigation bar when ready for production.

### 2. Google OAuth (Google Cloud Console)
1.  Go to the [Google Cloud Console](https://console.cloud.google.com/).
2.  Create a new project.
3.  Navigate to **"APIs & Services" -> "Credentials"**.
4.  Click **"Create Credentials"** and select **"OAuth client ID"**.
5.  Configure the Consent Screen if prompted.
6.  Set Application Type to **"Web application"**.
7.  Under **"Authorized redirect URIs"**, add:
    `https://yourdomain.com/auth/google/callback`
8.  Copy the **Client ID** and **Client Secret** and update your `.env` file:
    ```env
    GOOGLE_CLIENT_ID=your_client_id
    GOOGLE_CLIENT_SECRET=your_client_secret
    GOOGLE_REDIRECT_URL="https://yourdomain.com/auth/google/callback"
    ```

## Automation & Cron Jobs

For the system to function correctly, especially regarding stock notifications and promotions, the following tasks should be automated.

### 1. Standard Laravel Scheduler
The application uses the built-in Laravel Scheduler for background tasks. To enable this, add the following entry to your server's crontab:

```bash
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

### 2. Web-based Cron Jobs (Alternative)
If you are using a web-based cron service (like EasyCron), you can use the following URLs:

- **Flash Sale Expiry Check:** `/check-flash-sale-expiry`
  - *Purpose:* Deactivates expired flash sales and resets product discounts.
  - *Suggested Frequency:* Every minute.

- **Low Stock Notification Check:** `/admin/inventory/check-low-stock`
  - *Purpose:* Scans for products below minimum thresholds and sends email alerts.
  - *Suggested Frequency:* Once daily.
