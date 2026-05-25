# Blood Inventory System API

A comprehensive RESTful API for managing blood bank inventory, refrigerators, temperature monitoring, and expiry tracking. Built with Laravel 11.

## Key Features

- **Role-Based Access Control**: Secure API endpoints using Laravel Sanctum with `admin`, `blood_bank_staff`, and `monitoring_user` roles.
- **Inventory Management**: Full CRUD operations for Blood Banks, Refrigerators, and Blood Bags.
- **Temperature Monitoring**: API to log refrigerator temperatures. Real-time analysis with an observer pattern triggering critical alerts.
- **Expiry Tracking**: Automated tracking of blood bag expiry dates and "near-risk" (expiring within 24 hours) stock calculation.
- **Analytics Dashboard**: Aggregated and cached data for quick overview of inventory health, active refrigerators, and alerts.
- **Notifications**: Automated database notifications sent to blood bank users when a refrigerator enters a critical temperature state.

## Tech Stack

- PHP 8.2+
- Laravel 11
- Microsoft SQL Server (via Docker)
- Laravel Sanctum (Authentication)

## Installation & Setup

1. **Clone the Repository**
   ```bash
   git clone <repository-url>
   cd blood-inventory-system
   ```

2. **Install Dependencies**
   ```bash
   composer install
   ```

3. **Configure Environment**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   *Update your `.env` with the SQL Server credentials (see `database/blood_inventory.bak` for a backup restore).*
   ```env
   DB_CONNECTION=sqlsrv
   DB_HOST=127.0.0.1
   DB_PORT=1433
   DB_DATABASE=blood_inventory
   DB_USERNAME=sa
   DB_PASSWORD=YourPassword
   ```

4. **Run Migrations & Seeders**
   ```bash
   php artisan migrate --seed
   ```
   *The seeder populates the database with test users, a blood bank, refrigerators, blood bags, and temperature logs.*

5. **Start the Server**
   ```bash
   php artisan serve
   ```

## Test Credentials

The database seeder creates the following users (Password for all is `password`):

| Role | Email |
|------|-------|
| Admin | admin@example.com |
| Blood Bank Staff | staff@example.com |
| Monitoring User | monitor1@example.com |
| Monitoring User | monitor2@example.com |

## API Documentation

A Postman collection is provided in the project root: `Blood_Inventory_System.postman_collection.json`. Import this into Postman to test all available endpoints.

### Key Endpoints

- `POST /api/login` - Authenticate and get Bearer token.
- `GET /api/dashboard` - Get cached system overview (all authenticated users).
- `GET /api/blood-bags` - List inventory (all authenticated users).
- `POST /api/blood-bags` - Add inventory (Admin/Staff only).
- `POST /api/temperature-logs` - Log a temperature reading (Admin/Staff only).
- `GET /api/notifications` - Retrieve alerts/notifications.

## Testing

Run the feature test suite:
```bash
php artisan test
```
The test suite covers Authentication, Blood Bag CRUD (with role validation), and Dashboard analytics.
