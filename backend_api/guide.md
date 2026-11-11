# Multi-Tenant Shared Catalog E-Commerce Management System

A scalable multi-tenant e-commerce management platform built with **Laravel (REST API)** and **Vue.js / React.js (Frontend)**. The architecture features a shared global product catalog coupled with store-specific overrides for pricing, stock, metadata, and media thumbnails.

---

## 📌 Architecture & Design Patterns

- **Shared Global Catalog:** Admin and Shop Owners can publish items into a shared master catalog.
- **Tenant-Level Overrides:** Shop Owners pick items from the master catalog and publish them to their shop with custom titles, descriptions, prices, and thumbnails.
- **Customer Isolation Rule:** Customers can only browse published items mapped directly to a specific store. Unassigned master catalog items return HTTP 404 or remain invisible.
- **Strict OOP & Clean Architecture:**
  - **Form Requests:** Strict schema input validation and authorization checks.
  - **Data Transfer Objects (DTOs):** Strongly typed, immutable data carriers between HTTP and Domain layers.
  - **Service-Repository Pattern:** Decoupled business logic wrapped inside database transactions (`DB::transaction`).
  - **API Resources:** Standardized JSON response formatting and schema transformation.
  - **RBAC & Security:** Laravel Sanctum token-based authentication paired with granular Policy authorizations.

---

## 🛠️ Tech Stack & Requirements

- **PHP:** `^8.2`
- **Composer:** `^2.5`
- **Node.js:** `^18.x` or `^20.x` & `npm`
- **Database:** MySQL `^8.0` / MariaDB `^10.4`
- **Backend Framework:** Laravel 10 / 11
- **Frontend Framework:** Vue 3 (Vite) / React (Vite)

---

## 🚀 Step-by-Step Local Setup Guide

Follow this sequential setup to clone, configure, migrate, and run the project from scratch.

### 1. Repository Setup



2. Backend Setup (Laravel API)
Step 2.1: Install Dependencies
Bash
composer install
Step 2.2: Environment Configuration
Copy the template environment file:

Bash
cp .env.example .env
Generate the encryption key:

Bash
php artisan key:generate
Step 2.3: Database Configuration
Update the .env file with your local MySQL credentials:

Code snippet
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ecommerce_management
DB_USERNAME=root
DB_PASSWORD=
(Ensure an empty database named ecommerce_management exists in your MySQL server).

Step 2.4: Migrations, Seeders & Symlink
Execute all database schema migrations and load demo seeders:

Bash
php artisan migrate:fresh --seed
Link local public file storage for product images and thumbnails:

Bash
php artisan storage:link
Step 2.5: Run Backend Server
Bash
php artisan serve
Backend API will be accessible at: http://127.0.0.1:8000

3. Frontend Setup (Vue.js / React)
(If your frontend is located inside a subdirectory such as frontend/ or client/, run cd frontend first).

Step 3.1: Install NPM Packages
Bash
npm install
Step 3.2: Configure Environment
Create a frontend .env file if required:

Bash
cp .env.example .env
Ensure the API base endpoint matches your Laravel backend:

Code snippet
VITE_API_BASE_URL=[http://127.0.0.1:8000/api](http://127.0.0.1:8000/api)
Step 3.3: Start Frontend Server
Bash
npm run dev
Frontend UI will run at: http://localhost:5173 (or http://localhost:3000).

👥 Seeded Test Credentials
The database seeders come pre-populated with accounts for evaluation:

Role	Email	Password	Assigned Shop / Scope
System Admin	admin@example.com	password	Global Management
Shop Owner 1	owner1@example.com	password	Apex Electronics
Shop Owner 2	owner2@example.com	password	Metro Fashion
Public Customer	No Login Required	N/A	Browse Public Shop URL
🧪 Evaluator Verification Workflow
Test 1: Admin Privileges
Log in with admin@example.com / password.

Manage Shops: View existing shops, update status, or create new shops.

Manage Shop Owners: Create shop owner profiles and associate them with stores.

Global Catalog Management: Create/update master products visible across all stores.

Test 2: Shop Owner Catalog & Overrides
Log in with owner1@example.com / password.

Open Shared Catalog: Browse global items created by Admin and other Shop Owners.

Add to My Shop:

Select a product and provide a shop-specific price, custom title, description, and thumbnail.

Create New Product:

Submit a new product from the owner dashboard.

Log in as owner2@example.com to confirm that this item is instantly visible in their Shared Catalog to adopt.

Test 3: Customer Shop View & Product Isolation
Without logging in, navigate to a public store: /shops/apex-electronics.

Verify that only products attached to Apex Electronics appear in the list.

Verify that the price and thumbnail displayed reflect the shop-specific override values.

Attempt to access an unassigned catalog product ID via the shop route; verify it returns a 404 Not Found response.

📡 REST API Reference
Authentication (/api/auth)
Method	Endpoint	Description	Headers / Access
POST	/api/auth/login	Authenticate user & return Sanctum Bearer token	Public
POST	/api/auth/logout	Revoke active token	Bearer Token
GET	/api/auth/me	Fetch authenticated profile and assigned shop	Bearer Token
Admin Endpoints (/api/admin/*)
Method	Endpoint	Description	Access
GET / POST	/api/admin/shops	List all shops / Register a new shop	Admin Only
PUT / DELETE	/api/admin/shops/{id}	Update or delete shop	Admin Only
GET / POST	/api/admin/shop-owners	List or register shop owner accounts	Admin Only
GET / POST	/api/admin/products	Manage shared master catalog products	Admin Only
Shop Owner Endpoints (/api/shop-owner/*)
Method	Endpoint	Description	Access
GET	/api/shop-owner/catalog	Browse global catalog items	Shop Owner
POST	/api/shop-owner/catalog	Create item directly into global catalog	Shop Owner
GET	/api/shop-owner/my-shop/products	List all products assigned to owner's shop	Shop Owner
POST	/api/shop-owner/my-shop/products/attach	Attach catalog item with custom price & media	Shop Owner
PUT	/api/shop-owner/my-shop/products/{id}	Update shop-specific price, stock, or overrides	Shop Owner
DELETE	/api/shop-owner/my-shop/products/{id}	Remove product from shop listing	Shop Owner
Customer Public Endpoints (/api/public/*)
Method	Endpoint	Description	Access
GET	/api/public/shops	List active shops	Public
GET	/api/public/shops/{slug}/products	List shop-specific products (with overrides applied)	Public
GET	/api/public/shops/{slug}/products/{id}	Single product detail for a specific shop	Public
📂 Project Directory Structure
Plaintext
├── app/
│   ├── DTOs/                   # Strongly typed Data Transfer Objects
│   │   ├── BaseDTO.php
│   │   ├── ProductDTO.php
│   │   └── ShopProductListingDTO.php
│   ├── Http/
│   │   ├── Controllers/Api/    # Thin Controllers (Admin, Owner, Customer)
│   │   ├── Requests/           # Strict Form Request Validations
│   │   └── Resources/          # API Transformation Resources
│   ├── Models/                 # Eloquent Models (User, Shop, Product, ShopProduct)
│   ├── Policies/               # Granular RBAC authorization checks
│   ├── Services/               # Business logic and Database transactions
│   │   ├── Contracts/          # Service Interfaces (OOP)
│   │   └── ProductService.php
│   └── Traits/
│       └── ApiResponseTrait.php # Standardized API JSON structures
├── database/
│   ├── migrations/             # Relational schema migrations
│   └── seeders/                # Demo users, shops, and products
├── routes/
│   └── api.php                 # Token and role-guarded routes
└── tests/
    └── Feature/                # Automated API endpoint integration tests
🧪 Automated Testing
Execute automated feature tests to validate API responses, permissions, and database constraints:

Bash
php artisan test
📄 License
This project is open-sourced under the MIT License.


