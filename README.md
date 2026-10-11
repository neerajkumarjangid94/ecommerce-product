# E-Commerce Product Management System (Web & REST API)

A full-featured E-Commerce Product & Variant Management system built with **Laravel 11**, supporting both a modern **Web Frontend (Storefront & Admin Panel)** and a structured **REST API (v1)**.

---

## 1. Project Setup Commands

Follow these steps in order to set up and run the project locally:

```bash
# 1. Clone the repository (if not already cloned)
git clone <repository-url>
cd product_site

# 2. Install PHP dependencies
composer install

# 3. Environment configuration
# Copy .env.example to .env
cp .env.example .env

# 4. Generate Application Encryption Key
php artisan key:generate

# 5. Configure Database
# Open .env and set your database credentials:
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=product_site
# DB_USERNAME=root
# DB_PASSWORD=

# 6. Run Database Migrations
php artisan migrate

# 7. Seed Database (Creates Default Admin & Sample Data)
php artisan db:seed

# Alternatively, seed only Admin:
php artisan db:seed --class=AdminSeeder

# 8. Create Storage Symlink (Crucial for product image uploads)
php artisan storage:link

# 9. Install & Build Frontend Assets (Vite/Node)
npm install
npm run build
# (Or 'npm run dev' during development)

# 10. Start Local Development Server
php artisan serve
```

The application will be accessible at: **`http://127.0.0.1:8000`**

---

## 2. Project Architecture: Web & API

The project provides dual access layers:

1. **Web Application (Blade + Vanilla JS)**:
    - Public Storefront (`/`, `/products`, `/products/{id}`)
    - Protected Admin Panel (`/admin/dashboard`, `/admin/products`, `/admin/settings`)
2. **REST API (`/api/v1/...`)**:
    - Public product browsing endpoints (for Mobile Apps & Frontends)
    - Sanctum Token-based protected endpoints for Admin management

---

## 3. Roles & Features

### 👤 Admin (Created via Seeder)

- **Default Credentials**:
    - **Email**: `admin@admin.com`
    - **Password**: `Admin@12345`
    - Created by: `database/seeders/AdminSeeder.php`
- **What Admin Can Do (Permissions & Flow)**:
    - **Authentication**: Login via Web (`/login`) or REST API (`POST /api/v1/login`) to receive Bearer token.
    - **Product Management**:
        - Create new products with multiple images, descriptions, and active/inactive status.
        - Multi-step product creation with dynamic color & size matrix generation.
        - Auto-generated SKUs (e.g. `PROD-RED-M`) that are non-editable to ensure inventory integrity.
        - Edit existing products, manage stock, and update variant-level pricing.
        - Soft delete products (`DELETE /api/v1/products/{id}`).
    - **Settings & Attributes Management**:
        - Add and delete Colors with Hex color codes (e.g., `#FF0000`).
        - Add and delete Sizes (e.g., `S`, `M`, `L`, `XL`).
    - **Admin Products View**:
        - Access all products (both active and inactive) via Web and API (`GET /api/v1/products`).

---

### 🛍️ User / Customer (Public Storefront & Public API)

- **No Login / Registration Required**: Users do not need an account to browse products.
- **What Users Can See & Do**:
    - **Product Catalog (`/products` & `GET /api/v1/products`)**:
        - Browse paginated catalog of active products.
        - Search products by title/name.
        - Filter by limit (`?limit=10`).
    - **Product Details Page (PDP) (`/products/{id}` & `GET /api/v1/products/{id}`)**:
        - View product images, titles, and descriptions.
        - **Dynamic Variant Switching**: When a user selects a color and size, the UI dynamically updates:
            - Real-time Price for that specific variant
            - Live Stock availability
            - Variant-specific image (if assigned)
            - SKU display
    - **Browse Available Attributes**:
        - Fetch available colors (`GET /api/v1/colors`)
        - Fetch available sizes (`GET /api/v1/sizes`)

---

## 4. REST API Reference (`/api/v1`)

### 🔓 Public Endpoints (No Auth Required)

| Method | Endpoint                 | Description                                     |
| :----- | :----------------------- | :---------------------------------------------- |
| `POST` | `/api/v1/login`          | Admin login (Returns Bearer token)              |
| `GET`  | `/api/v1/products`       | Public product listing (Active only, paginated) |
| `GET`  | `/api/v1/products/{id}`  | Single product details with relations           |
| `GET`  | `/api/v1/colors`         | List of all available colors                    |
| `GET`  | `/api/v1/colors/{color}` | Color details by ID                             |
| `GET`  | `/api/v1/sizes`          | List of all available sizes                     |
| `GET`  | `/api/v1/sizes/{size}`   | Size details by ID                              |

### 🔒 Protected Endpoints (`Authorization: Bearer <token>`)

| Method       | Endpoint                 | Description                                              |
| :----------- | :----------------------- | :------------------------------------------------------- |
| `POST`       | `/api/v1/logout`         | Revoke current Bearer token                              |
| `GET`        | `/api/v1/user`           | Authenticated user profile                               |
| `GET`        | `/api/v1/products`       | _(Admin)_ List all products (active + inactive)          |
| `POST`       | `/api/v1/products`       | _(Admin)_ Create new product with variants & images      |
| `POST / PUT` | `/api/v1/products/{id}`  | _(Admin)_ Update product & variants                      |
| `DELETE`     | `/api/v1/products/{id}`  | _(Admin)_ Soft delete product                            |
| `POST`       | `/api/v1/colors`         | _(Admin)_ Add new color (`color_name`, `color_hex_code`) |
| `DELETE`     | `/api/v1/colors/{color}` | _(Admin)_ Delete color                                   |
| `POST`       | `/api/v1/sizes`          | _(Admin)_ Add new size (`size_name`)                     |
| `DELETE`     | `/api/v1/sizes/{size}`   | _(Admin)_ Delete size                                    |

---

## 5. Technology Stack

- **Backend**: Laravel 11 (PHP 8.2+)
- **Authentication**: Laravel Sanctum (API Tokens) & Laravel Breeze (Web Session)
- **Database**: MySQL (Eloquent ORM with SoftDeletes)
- **Frontend**: Blade Templates, Vanilla JavaScript, CSS3
- **File Storage**: Local Public Disk (`storage/app/public`)
