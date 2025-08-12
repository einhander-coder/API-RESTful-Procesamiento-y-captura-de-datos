## Repair Shop Admin Panel (PHP + HTML5)

Simple administrative panel for a technical repair shop with authentication (admin/technician), request tracking, and PDF/POS printing.

### Features
- Login for Admin and Technician
- Dashboard: technician name, owner name, requests, pending requests, device model, device status, problem, result status, observations
- Create and edit repair requests
- PDF export (via dompdf) and POS-style text receipt export

### Requirements
- PHP 8.0+
- MySQL/MariaDB
- Composer (for PDF support)

### Setup
1. Create database and import schema:
   ```sql
   CREATE DATABASE repair_shop CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   USE repair_shop;
   SOURCE /workspace/repair_shop/db.sql;  -- adjust path if needed
   ```

2. Configure DB credentials (via environment variables):
   - `DB_HOST` (default `127.0.0.1`)
   - `DB_PORT` (default `3306`)
   - `DB_NAME` (default `repair_shop`)
   - `DB_USER` (default `root`)
   - `DB_PASS` (default empty)

3. Seed default users:
   - Run in browser: `http://localhost/repair_shop/seed.php` (or CLI: `php /workspace/repair_shop/seed.php`)
   - Admin: `admin@example.com` / `admin123`
   - Technician: `tech@example.com` / `tech123`

4. Install PDF dependency (optional but recommended):
   ```bash
   cd /workspace/repair_shop
   composer install --no-interaction --prefer-dist
   ```

5. Serve app (examples):
   - Apache/Nginx: point document root to parent of `repair_shop/`
   - PHP built-in server:
     ```bash
     php -S 0.0.0.0:8080 -t /workspace
     # Visit: http://localhost:8080/repair_shop/login.php
     ```

### Notes
- PDF route: `/repair_shop/print_pdf.php?id=ID` (falls back to HTML if dompdf not installed; use browser Print to PDF)
- POS route: `/repair_shop/print_pos.php?id=ID` (downloads `.txt` formatted for 40-char POS width)
- For production, secure sessions and set proper permissions; add CSRF protection as needed.