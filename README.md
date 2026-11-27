# GBU Grievance Management System

A multi-tier web-based grievance redressal portal for **Gautam Buddha University (GBU)**, built with **PHP 8**, **MySQL (PDO)**, and **Bootstrap 5**.

## Features
- **Role-Based Access Control:** Dedicated dashboards and permissions for Students, Department Admins (HOD), College Admins (Dean), University Admins (Registrar), and Main Admin.
- **Grievance Lifecycle:** Students can submit complaints with category, department, official GBU hostel selection, and priority, and track real-time status (`Pending`, `In Progress`, `Resolved`, `Rejected`) along with admin remarks using tracking IDs (`GBU-YYYY-XXXX`).
- **Official GBU Hostel Directory:** Supports 18 official GBU hostels via `database/migrations/001_add_hostel_directory.sql` (with automatic fallback if the migration has not been applied).
- **Multi-Tier Escalation:** Supports manual escalation from the admin panel and automated SLA escalation (`cron/escalate.php`) from Department -> College -> University level with full escalation logging.
- **Notifications & Admin Tools:** In-app student notifications, user management (add/edit/toggle/delete), activity logs, and analytics/reporting tabs.
- **Optional Integrations:** Configurable n8n webhook for AI grievance summarization (see `N8N_SETUP.md`) and a standalone Node.js SHA-256 hash logger (`blockchain/server.js`).

## Project Structure
```text
├── blockchain/          # Optional Node.js SHA-256 hash service
├── config/              # Database & integration config (config.php, config.example.php)
├── cron/                # Scheduled SLA escalation script (escalate.php)
├── database/
│   ├── migrations/      # Incremental SQL migrations (001_add_hostel_directory.sql)
│   ├── schema.sql       # Main MySQL schema & default seed accounts
│   └── seed_demo_data.php # Demo data seeder (24 students, 120 grievances)
├── includes/            # Shared DB connection, auth helpers, header & footer
├── migrations/          # Legacy migration helper scripts
├── public/              # Web root (pages, tabs, CSS, images, and api/ endpoints)
├── tests/               # Automated PHP test suite (run_tests.php)
└── start.bat            # Windows/XAMPP one-click launcher
```

## Local Setup (XAMPP / PHP 8 + MySQL)

1. **Start MySQL** (e.g., via XAMPP Control Panel).
2. **Import the database:**
   Create and seed the `gbu_grievance` database by importing `database/schema.sql` via phpMyAdmin or CLI:
   ```bash
   mysql -u root -p < database/schema.sql
   ```
   *(Optional)* Import the hostel directory migration:
   ```bash
   mysql -u root -p gbu_grievance < database/migrations/001_add_hostel_directory.sql
   ```
3. **Configure database credentials:**
   Copy `config/config.example.php` to `config/config.php` and update `host`, `name`, `user`, and `pass` for your local MySQL server (default XAMPP `user` is `root` with an empty `pass`).
4. **(Optional) Seed demo data:**
   Populate the database with 24 student accounts, 120 realistic grievances, responses, notifications, and escalation logs:
   ```bash
   php database/seed_demo_data.php
   ```
5. **Start the application:**
   - **Windows (XAMPP):** Double-click `start.bat` (launches `http://localhost:8000` automatically).
   - **CLI:**
     ```bash
     php -S localhost:8000 -t public
     ```
     Then open `http://localhost:8000` in your browser.

## Running Tests
Run the built-in PHP test suite from the project root:
```bash
php tests/run_tests.php
```
*(If using XAMPP on Windows, run `"C:\xampp\php\php.exe" tests\run_tests.php`.)*

## Default Seed Accounts
All seed accounts in `database/schema.sql` and `database/seed_demo_data.php` use the password `password123`:

| Role | Email | Notes |
|---|---|---|
| **Student** | `aarav.sharma@gbu.ac.in` | Roll number `23ICS014` |
| **Department Admin (HOD)** | `hod.eng@gbu.ac.in` | Engineering department |
| **College Admin (Dean)** | `dean.eng@gbu.ac.in` | Engineering school |
| **University Admin (Registrar)** | `registrar@gbu.ac.in` | University level |
| **Main Admin** | `admin@gbu.ac.in` | Full system access |
