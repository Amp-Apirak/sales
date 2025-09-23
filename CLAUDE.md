# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is a PHP-based sales management system with role-based access control. The application uses MySQL database and follows a modular structure with shared components and page-specific modules.

## Development Setup

### Dependencies
- PHP 7.4+ with PDO MySQL extension
- MySQL/MariaDB database
- Composer for dependency management
- Web server (Apache/Nginx) with mod_rewrite

### Installation Commands
```bash
# Install PHP dependencies
composer install

# Import database schema
mysql -u root -p sales_db < config/sale_db.sql
```

### Database Configuration
Database connection settings are in `config/condb.php`:
- Default database: `sales_db`
- Default credentials: root/1234
- Uses PDO with UTF-8 charset

## Architecture

### File Structure
- `/config/` - Database configuration and SQL schema
- `/include/` - Shared components (Header, Footer, Navbar, session management)
- `/pages/` - Feature modules organized by functionality
- `/assets/` - Frontend assets (CSS, JS, images)
- `/AdminLTE/` - AdminLTE theme framework
- `/vendor/` - Composer dependencies
- `/uploads/` - User uploaded files
- `/logs/` - Application logs

### Core Components

#### Authentication & Authorization (`index.php`)
- Session-based authentication with role checking
- Four user roles: Executive, Sale Supervisor, Seller, Engineer
- Permission system controls data visibility:
  - Executive: Can view all data
  - Sale Supervisor: Can view team data
  - Seller/Engineer: Can view own data only
  - Engineer: No financial data access

#### Database Layer (`config/condb.php`)
- PDO-based database connection with prepared statements
- Includes encryption/decryption functions for user IDs
- Uses `BASE_URL` constant for path management

#### Module Structure (`pages/`)
Each functional area has its own directory:
- `account/` - User management
- `category/` - Product categories
- `claims/` - Claims management
- `customer/` - Customer management
- `inventory/` - Inventory tracking
- `project/` - Project management
- `service/` - Service management

### Frontend Framework
- AdminLTE 3.x for UI components
- Bootstrap 4 for responsive layout
- DataTables for data presentation
- Select2 for enhanced dropdowns
- SweetAlert for user notifications

### Security Features
- Password hashing with `password_verify()`
- User ID encryption with OpenSSL
- Session-based authentication
- Role-based access control
- Prepared statements for SQL injection prevention

## Common Tasks

### Adding New Pages
1. Create directory under `pages/[module]/`
2. Include session check and role validation
3. Use shared components from `include/`
4. Follow existing permission patterns

### Database Operations
- All database queries use PDO prepared statements
- Connection object available as `$condb`
- Include `config/condb.php` for database access

### Role-Based Features
Check user permissions using session variables:
```php
$role = $_SESSION['role'];
$team_id = $_SESSION['team_id'];
$user_id = $_SESSION['user_id'];
```

### Asset Management
- Static assets served from `/assets/`
- Use `BASE_URL` constant for path construction
- AdminLTE components available via `/AdminLTE/`