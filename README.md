# Shopify Embedded App (Laravel + React + Inertia)

This project is a Shopify Embedded App built with Laravel 11, Inertia.js, React, and Shopify Polaris. It fetches Products, Collections, and Orders from a Shopify store, syncs them to a local MySQL database, and displays them in a dashboard.

## Tech Stack

- **Backend:** Laravel 11 (didn't use Laravel 12 because of Inertia.Js using React 19 which causes lots of dependency issues)
- **Frontend:** React, Inertia.js, Shopify Polaris
- **Database:** MySQL
- **Shopify Integration:** kyon147/laravel-shopify, Shopify Admin API (GraphQL)

## Features

- **Authentication:** Shopify OAuth 2.0 implementation.
- **Data Sync:** 
  - Manual sync for Products and Collections via UI.
  - Webhooks for real-time Product updates (Create, Update, Delete).
- **Dashboard:** Summary cards for total products, collections, and last sync status.
- **Products Page:** Paginated list of local products with search and status filtering.

## Setup & Installation

### Prerequisites

- PHP 8.2+
- Composer
- Node.js & NPM
- MySQL
- Shopify Partner Account

### Installation Steps

1. **Clone the repository:**
   ```bash
   git clone <repository-url>
   cd <project-directory>
   ```

2. **Install PHP dependencies:**
   ```bash
   composer install
   ```

3. **Install Node dependencies:**
   ```bash
   npm install
   ```

4. **Configure Environment:**
   Copy `.env.example` to `.env` and configure the following:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

   Update your `.env` file with your database credentials and Shopify App keys:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=your_database_name
   DB_USERNAME=your_database_user
   DB_PASSWORD=your_database_password

   SHOPIFY_API_KEY=your_shopify_api_key
   SHOPIFY_API_SECRET=your_shopify_api_secret
   SHOPIFY_API_SCOPES=read_products,read_orders,read_inventory
   SHOPIFY_APP_URL=https://your-app-url.ngrok.io
   APP_URL=https://your-app-url.ngrok.io
   ```

5. **Run Migrations:**
   ```bash
   php artisan migrate
   ```

6. **Build Frontend Assets:**
   ```bash
   npm run build
   ```

### Running the App

1. **Start the Laravel & Vite server:**
   ```bash
   composer dev
   ```

2. **Expose local server (if needed):**
   Use ngrok to expose your local server to the internet for Shopify:
   ```bash
   ngrok http 8000
   ```
   Update `APP_URL` and `SHOPIFY_APP_URL` in `.env` with the ngrok URL.

## Architecture

- **Authentication:** Handled by a custom `EnsureShopifyInstalled` middleware that resolves the shop domain from the request/session/cookie, ensures the shop exists locally, triggers Shopify OAuth when needed, and logs the shop in as a `User`. The `User` model effectively represents the `Shop`.
- **Frontend:** Uses **Inertia.js** to glue Laravel backend and React frontend. The frontend uses **Shopify Polaris** components and **App Bridge** for embedded experience.
- **Data Sync:** 
  - **Manual:** Triggers `SyncProductsJob`, `SyncCollectionsJob`, or `SyncOrdersJob` which use `ShopifyGraphQLService` to fetch data via GraphQL cursor pagination and update the database.
  - **Webhooks (Products only):** `ProductsCreateJob`, `ProductsUpdateJob`, `ProductsDeleteJob` handle incoming product webhooks from Shopify to keep data in sync.

## Shopify API Scopes

- `read_products`: To fetch products and collections.
- `read_orders`: Included as per requirements (though not actively used in this demo).
- `read_inventory`: For inventory details.

## Commands

- **Run Migrations:** `php artisan migrate`
- **Run Backend:** `php artisan serve`
- **Run Frontend:** `npm run dev` (dev) or `npm run build` (prod)
- **Work Queue (for sync jobs):** `php artisan queue:work`
-**Run Backend, Frontend and work queue all at once** `composer dev`
