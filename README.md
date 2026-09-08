# Chemical Stock OS

**Chemical Stock OS** is an enterprise-grade Chemical Inventory and Stock Movement Tracking Management System built on Laravel 12, MySQL/SQLite, Tailwind CSS v4, Alpine.js, and Vite.

## Features

- **Inventory Management**: Chemical registry, CAS numbers, batch/lot tracking, expiry tracking, hazard classification, printable QR codes.
- **Master Data**: Categories with color-coded badges, storage locations with temperature ranges, suppliers registry with contact directory.
- **Stock Movement & Ledger**: Real-time Stock In, Stock Out, and Stock Adjustment tracking with concurrency row locking (`lockForUpdate`).
- **QR Code Scanner**: In-browser camera scanning and manual code lookup with instant redirect to chemical records.
- **Reports & Analytics**: Inventory summary, stock movement logs, expiry forecasting, and stock adjustments with CSV export.
- **Security & Roles**: Multi-tier role authorization (`ADMIN`, `STOCK_MANAGER`, `AUDITOR`, `VIEWER`), brute-force login throttling, and full audit trail logging.
- **Responsive UI**: Mobile-first design with off-canvas navigation drawer, responsive data tables, and touch-friendly controls.

## Requirements

- PHP 8.2+
- Composer
- Node.js & NPM
- SQLite or MySQL 8.0+

## Quick Start

```bash
# 1. Install dependencies
composer install
npm install

# 2. Setup environment
cp .env.example .env
php artisan key:generate

# 3. Migrate and seed database
php artisan migrate --seed

# 4. Build frontend assets
npm run build

# 5. Start development server
php artisan serve
```

## Default Accounts

| Role | Email | Password |
|---|---|---|
| **Admin** | `admin@chemstock.com` | `Admin@12345` |
| **Stock Manager** | `manager@chemstock.com` | `Manager@12345` |
| **Auditor** | `auditor@chemstock.com` | `Auditor@12345` |
| **Viewer** | `viewer@chemstock.com` | `Viewer@12345` |

## Testing

```bash
php artisan test
```
