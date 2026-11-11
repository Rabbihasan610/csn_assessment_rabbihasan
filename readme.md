# Multi-Tenant Shared Catalog E-Commerce Management System

A multi-tenant e-commerce platform built with Laravel (REST API) and Vue.js 3 (Vite Frontend). The architecture features a shared global product catalog combined with store-level overrides for custom titles, descriptions, pricing, stock, and media thumbnails.

---

## Architecture & Core Features

- **Shared Global Catalog:** System Administrators and Shop Owners can create and publish items into a unified master catalog.
- **Tenant-Level Overrides:** Shop Owners can pick items from the master catalog and publish them to their store with custom titles, descriptions, prices, stock levels, and thumbnails.
- **Customer Store Isolation:** Public customers can only view products attached directly to a specific shop. Unassigned products or items from other stores return HTTP 404 Not Found.

---

## Tech Stack & Prerequisites

- **PHP:** `^8.2` or `^8.3`
- **Composer:** `^2.5`
- **Node.js:** `^18.x`, `^20.x` or `^22.x` & `npm`
- **Database:** MySQL `^8.0` / MariaDB `^10.4`
- **Backend:** Laravel 11
- **Frontend:** Vue.js 3, Vite, Vue Router 4, Axios

---

## Step-by-Step Setup Guide

### 1. Database Setup

Ensure MySQL is running on your machine and create an empty database:

```sql
CREATE DATABASE ecommerce_management CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

---

### 2. Backend Setup (Laravel API)

Navigate to the project root directory:

```bash
composer install
```

Configure your environment file:

```bash
cp .env.example .env
php artisan key:generate
```

Verify your `.env` database settings match your local MySQL configuration:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ecommerce_management
DB_USERNAME=root
DB_PASSWORD=
```

Run database migrations and load pre-populated seed data:

```bash
php artisan migrate:fresh --seed
```

Create storage link for uploaded media:

```bash
php artisan storage:link
```

Start the backend API server:

```bash
php artisan serve
```

The backend API will run at `http://127.0.0.1:8000`.

---

### 3. Frontend Setup (Vue.js 3 + Vite)

Open a new terminal window and navigate to the `frontend` folder:

```bash
cd frontend
npm install
```

Configure the frontend environment file:

```bash
cp .env.example .env
```

Ensure `frontend/.env` points to the Laravel API URL:

```env
VITE_API_BASE_URL=http://127.0.0.1:8000/api
```

Start the frontend development server:

```bash
npm run dev
```

The frontend application will run at `http://localhost:5173`.

---

## Seeded Test Accounts

The database seeder pre-populates the following accounts for immediate evaluation:

| Role | Email | Password | Assigned Scope / Shop |
| :--- | :--- | :--- | :--- |
| **System Admin** | `admin@example.com` | `password` | Global Management |
| **Shop Owner 1** | `owner1@example.com` | `password` | Apex Electronics |
| **Shop Owner 2** | `owner2@example.com` | `password` | Metro Fashion |
| **Public Customer** | No Login Required | N/A | Browse Public Shop URL |

---

## Evaluator Verification Workflow

### Test 1: Admin Privileges
1. Go to `http://localhost:5173/login` and click **System Admin** to log in.
2. Navigate to **Admin Portal**:
   - **Manage Shops:** Create new stores, edit store status, or delete stores.
   - **Manage Shop Owners:** Create owner accounts and assign them to existing shops.
   - **Master Catalog:** Create, edit, and delete products in the shared global catalog.

### Test 2: Shop Owner Catalog & Overrides
1. Log in with `owner1@example.com` / `password` (Apex Electronics).
2. Go to **Owner Portal**:
   - **Shared Global Catalog:** View master items created by Admin and other shop owners.
   - **Add to My Store:** Select a product, supply custom store title, price, description, thumbnail, and stock.
   - **Publish New Product:** Click **+ Create New Global Product** to publish a new item into the shared catalog.
3. Log in as `owner2@example.com` (Metro Fashion) and verify the newly published item is instantly visible in their Shared Catalog to adopt.

### Test 3: Customer Shop View & Product Isolation
1. Without logging in, navigate to `http://localhost:5173/shops`.
2. Select **Apex Electronics** (`/shops/apex-electronics`):
   - Verify only products assigned to Apex Electronics appear.
   - Verify store-specific prices and custom titles reflect override values.
3. Test isolation rule:
   - Click on an assigned product to view details (`/shops/apex-electronics/products/1`).
   - Use the built-in isolation verification tool on the storefront page or directly enter an unassigned product ID (e.g., `/shops/apex-electronics/products/99`); verify it returns HTTP 404 Not Found.

---

## Automated Testing

Run the automated test suite to validate all API endpoints, authorization checks, role policies, and customer product isolation:

```bash
php artisan test
```

---

## License

This project is licensed under the MIT License.

