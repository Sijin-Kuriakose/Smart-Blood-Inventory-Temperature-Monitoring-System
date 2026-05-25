# System Architecture: Blood Inventory System

This document outlines the architectural decisions, design patterns, and system flows implemented in the Blood Inventory System.

## 1. High-Level Architecture

The system is built as a RESTful API using the **Laravel 11** framework. It adheres to the MVC (Model-View-Controller) pattern with thin controllers and thick service layers to decouple business logic from HTTP request handling.

### Core Components
- **Controllers (`app/Http/Controllers/Api`)**: Handle incoming HTTP requests, route them to appropriate services, and format JSON responses using a standardized `ApiResponseTrait`.
- **Services (`app/Services`)**: Encapsulate complex business logic (e.g., `BloodExpiryService`, `TemperatureAnalysisService`).
- **Models (`app/Models`)**: Represent database entities with defined relationships and mutators.
- **Observers & Events (`app/Observers`, `app/Events`)**: Handle asynchronous side-effects (e.g., analyzing temperature logs asynchronously).

## 2. Database Schema Design

The relational database is built on **SQL Server**.
Key tables and relationships:
- `users`: Core authentication. Has many `BloodBank`.
- `blood_banks`: Represents a facility. Belongs to many `User` (staff). Has many `Refrigerator`.
- `refrigerators`: Physical storage units. Has many `BloodBag` and `TemperatureLog`.
- `blood_bags`: Inventory items. Belongs to `Refrigerator`. Tracks `blood_group`, `status`, and `expiry_date`.
- `temperature_logs`: Time-series data of refrigerator temperatures.
- `alerts`: System-generated warnings.
- `notifications`: Laravel's built-in database notification table.

## 3. Advanced Laravel Concepts Utilized

### A. Observer Pattern & Asynchronous Jobs
When a new `TemperatureLog` is created, the `TemperatureLogObserver` automatically intercepts the creation event. It checks the temperature threshold using the `TemperatureAnalysisService`. If a critical condition is met (e.g., 10 consecutive readings above 8°C, representing 10 minutes continuously), it fires a `CriticalTemperatureDetected` event. A listener then dispatches a background Job (`ProcessTemperatureAlert`) to create an alert and notify users. This prevents slow API responses during data ingestion.

### B. Caching Strategy
To reduce database load on heavy read operations, caching is implemented in the `AnalyticsController`:
- **Dashboard Overview**: `Cache::remember('dashboard_stats', 300, ...)` caches the expensive aggregation queries (stock counts, near-risk percentages) for 5 minutes.
- **Refrigerator Analysis**: Cached per refrigerator for 2 minutes to prevent spamming the database with aggregate queries during rapid monitoring.

### C. Role-Based Access Control (RBAC) & Middleware
Authentication is handled via **Sanctum** tokens. Custom role-based middleware (`app/Http/Middleware/CheckRole.php`) restricts route access dynamically.
- `admin`: Global CUD permissions.
- `blood_bank_staff`: CUD permissions scoped to blood management and temperature logging.
- `monitoring_user`: Read-only access to analytics and inventory.

### D. Standardized API Responses & Exception Handling
A custom `ApiResponseTrait` guarantees a consistent JSON format `{"success": true|false, "message": "...", "data": ...}` across the entire application.
Validation errors are intercepted by overriding `failedValidation` in Custom Form Requests to return structured 422 JSON errors instead of redirects. Global exception handling (404 Model Not Found, Authentication) is configured in `bootstrap/app.php`.
